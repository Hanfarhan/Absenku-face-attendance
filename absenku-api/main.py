import os
import io
import json
import base64
import numpy as np
from PIL import Image
from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import List, Optional
from mtcnn import MTCNN
from keras_facenet import FaceNet
from liveness import check_liveness

# ============================================================
# INIT APP
# ============================================================
app = FastAPI(title="FaceAttend API", version="2.1.0")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["http://localhost:8000", "http://127.0.0.1:8000"],
    allow_methods=["*"],
    allow_headers=["*"],
)

# ============================================================
# CONFIG — baca/tulis threshold dari config.json
# ============================================================
CONFIG_PATH = os.path.join(os.path.dirname(__file__), "config.json")

def _read_config() -> dict:
    """Baca config.json, kembalikan dict."""
    with open(CONFIG_PATH, "r") as f:
        return json.load(f)

def _write_config(data: dict) -> None:
    """Tulis ulang config.json secara atomik."""
    with open(CONFIG_PATH, "w") as f:
        json.dump(data, f, indent=4)

# ============================================================
# LOAD MODELS (sekali saat startup)
# ============================================================
print("Loading MTCNN...")
detector = MTCNN()

print("Loading FaceNet...")
embedder = FaceNet()

print("All models loaded successfully!")

# ============================================================
# SCHEMAS
# ============================================================
class RegisterRequest(BaseModel):
    captures: List[str]  # list base64 image strings

class RecognizeRequest(BaseModel):
    image:           str                  # base64 image string
    embeddings:      List[List[float]]    # embedding dari database
    threshold:       float  = 0.75        # threshold FaceNet dari admin
    evaluation_mode: str    = "full_pipeline"  # "full_pipeline" | "facenet_only"

class ThresholdUpdateRequest(BaseModel):
    facenet_threshold:  Optional[float] = None   # 0.0 – 1.0
    liveness_threshold: Optional[float] = None   # 0.0 – 1.0

class HealthResponse(BaseModel):
    status:  str
    message: str

class ConfigResponse(BaseModel):
    facenet_threshold:  float
    liveness_threshold: float
    evaluation_mode:    str

# ============================================================
# HELPERS
# ============================================================
def base64_to_image(base64_str: str) -> np.ndarray:
    """Konversi base64 string ke numpy array RGB."""
    if "," in base64_str:
        base64_str = base64_str.split(",")[1]
    img_bytes = base64.b64decode(base64_str)
    img       = Image.open(io.BytesIO(img_bytes)).convert("RGB")
    return np.array(img)

def detect_and_align(image: np.ndarray):
    """
    Deteksi wajah dengan MTCNN, crop dan align.
    Return: (face_array, box, error_reason)
      - (None, None, 'no_face')        → tidak ada wajah terdeteksi
      - (None, None, 'multiple_faces') → lebih dari 1 wajah terdeteksi
      - (face, box, None)              → berhasil, tepat 1 wajah
    """
    results = detector.detect_faces(image)

    # ── Tidak ada wajah ─────────────────────────────────────────
    if not results:
        return None, None, 'no_face'

    # ── Lebih dari 1 wajah → TOLAK ──────────────────────────────
    # Hanya 1 wajah yang diperbolehkan dalam frame (keamanan & akurasi)
    if len(results) > 1:
        print(f"[SECURITY] Multiple faces detected: {len(results)} faces in frame")
        return None, None, 'multiple_faces'

    # ── Tepat 1 wajah ────────────────────────────────────────────
    best       = results[0]
    x, y, w, h = best['box']

    x = max(0, x)
    y = max(0, y)

    # Padding 10% untuk crop FaceNet
    pad_x = int(w * 0.10)
    pad_y = int(h * 0.10)
    x1    = max(0, x - pad_x)
    y1    = max(0, y - pad_y)
    x2    = min(image.shape[1], x + w + pad_x)
    y2    = min(image.shape[0], y + h + pad_y)

    face         = image[y1:y2, x1:x2]
    face_resized = np.array(Image.fromarray(face).resize((160, 160)))

    # Kembalikan bounding box asli (tanpa padding) untuk liveness check
    return face_resized, (x, y, w, h), None

def cosine_similarity(a: np.ndarray, b: np.ndarray) -> float:
    """Hitung cosine similarity antara dua embedding vector."""
    a = a / (np.linalg.norm(a) + 1e-10)
    b = b / (np.linalg.norm(b) + 1e-10)
    return float(np.dot(a, b))

# ============================================================
# ROUTES — Health & Config
# ============================================================

@app.get("/health", response_model=HealthResponse)
def health_check():
    return {
        "status":  "ok",
        "message": "FaceAttend API v2.1 is running (dynamic threshold support)"
    }


@app.get("/config", response_model=ConfigResponse)
def get_config():
    """
    Kembalikan konfigurasi threshold aktif saat ini.
    Dipanggil oleh Filament dashboard saat halaman threshold dibuka.
    """
    cfg = _read_config()
    return {
        "facenet_threshold":  cfg.get("facenet_threshold", 0.75),
        "liveness_threshold": cfg.get("liveness_threshold", 0.5),
        "evaluation_mode":    cfg.get("evaluation_mode", "full_pipeline"),
    }


@app.put("/config/thresholds")
def update_thresholds(request: ThresholdUpdateRequest):
    """
    Update facenet_threshold dan/atau liveness_threshold di config.json.
    Perubahan langsung berlaku pada request berikutnya — tanpa restart server.

    Dipanggil oleh Filament dashboard saat admin klik "Simpan".
    """
    cfg = _read_config()

    updated_fields = []

    if request.facenet_threshold is not None:
        if not (0.0 <= request.facenet_threshold <= 1.0):
            raise HTTPException(status_code=422, detail="facenet_threshold harus antara 0.0 dan 1.0")
        cfg["facenet_threshold"] = round(request.facenet_threshold, 4)
        updated_fields.append(f"facenet_threshold={cfg['facenet_threshold']}")

    if request.liveness_threshold is not None:
        if not (0.0 <= request.liveness_threshold <= 1.0):
            raise HTTPException(status_code=422, detail="liveness_threshold harus antara 0.0 dan 1.0")
        cfg["liveness_threshold"] = round(request.liveness_threshold, 4)
        updated_fields.append(f"liveness_threshold={cfg['liveness_threshold']}")

    if not updated_fields:
        raise HTTPException(status_code=422, detail="Tidak ada field yang diupdate")

    _write_config(cfg)

    print(f"Config updated: {', '.join(updated_fields)}")

    return {
        "success":            True,
        "message":            f"Threshold diperbarui: {', '.join(updated_fields)}",
        "facenet_threshold":  cfg["facenet_threshold"],
        "liveness_threshold": cfg["liveness_threshold"],
    }


# ============================================================
# ROUTES — Face Recognition
# ============================================================

@app.post("/register")
async def register_face(request: RegisterRequest):
    """
    Terima 10 foto base64, proses dengan MTCNN + FaceNet,
    kembalikan rata-rata embedding.
    (Tidak perlu liveness check saat registrasi —
     diasumsikan registrasi dilakukan langsung oleh pemilik akun)
    """
    if not request.captures:
        raise HTTPException(status_code=400, detail="Tidak ada foto yang dikirim")

    embeddings = []
    failed     = 0

    for i, capture in enumerate(request.captures):
        try:
            image              = base64_to_image(capture)
            face, _, err_reason = detect_and_align(image)

            if face is None:
                failed += 1
                if err_reason == 'multiple_faces':
                    # Langsung tolak seluruh registrasi jika ada frame dengan banyak wajah
                    raise HTTPException(
                        status_code=400,
                        detail="Terdeteksi lebih dari 1 wajah dalam frame. "
                               "Pastikan hanya ada 1 wajah saat registrasi."
                    )
                print(f"Foto {i+1}: wajah tidak terdeteksi")
                continue

            face_expanded = np.expand_dims(face, axis=0)
            embedding     = embedder.embeddings(face_expanded)[0]
            embeddings.append(embedding.tolist())

        except HTTPException:
            raise  # re-raise security errors
        except Exception as e:
            failed += 1
            print(f"Foto {i+1} error: {str(e)}")
            continue

    if len(embeddings) == 0:
        raise HTTPException(
            status_code=422,
            detail="Tidak ada wajah yang berhasil diproses. Pastikan pencahayaan cukup."
        )

    mean_embedding = np.mean(embeddings, axis=0).tolist()

    return {
        "success":        True,
        "embedding":      mean_embedding,
        "total_photos":   len(request.captures),
        "success_photos": len(embeddings),
        "failed_photos":  failed,
    }


@app.post("/recognize")
async def recognize_face(request: RecognizeRequest):
    """
    Pipeline absensi dengan mode yang dapat dikonfigurasi:

    Mode 'full_pipeline' (Sistem B — default produksi):
        Tahap 1 — Liveness Detection (MiniFASNetV2-SE)
        → Cek apakah wajah ASLI atau SPOOF
        → Jika spoof terdeteksi → REJECTED langsung

        Tahap 2 — Identity Verification (MTCNN + FaceNet)
        → Hanya dijalankan jika Tahap 1 lolos (wajah asli)

    Mode 'facenet_only' (Sistem A — untuk eksperimen penelitian):
        → Skip liveness detection sepenuhnya
        → Langsung ke Identity Verification (FaceNet)
        → Untuk mengukur FAR/FRR tanpa pengaruh liveness
    """
    if not request.embeddings:
        raise HTTPException(status_code=400, detail="Tidak ada embedding yang dikirim")

    # Konversi base64 ke image
    try:
        image = base64_to_image(request.image)
    except Exception:
        raise HTTPException(status_code=400, detail="Format gambar tidak valid")

    # ── Deteksi wajah (selalu dijalankan di kedua mode) ──────
    face, box, err_reason = detect_and_align(image)

    if face is None:
        # Beda pesan untuk "tidak ada wajah" vs "lebih dari 1 wajah"
        rejection_reason = "multiple_faces_detected" if err_reason == 'multiple_faces' else "no_face_detected"
        return {
            "success":           False,
            "result":            "rejected",
            "rejection_reason":  rejection_reason,
            "confidence_score":  0.0,
            "liveness_score":    0.0,
            "spoof_type":        None,
            "threshold_used":    request.threshold,
            "evaluation_mode":   request.evaluation_mode,
        }

    # ── Inisialisasi liveness result (default untuk facenet_only mode) ──
    liveness_result = {
        "is_real":        True,
        "label":          "bypassed",
        "liveness_score": None,
        "spoof_type":     None,
        "threshold_used": None,
    }

    # ── TAHAP 1: Liveness Check — HANYA jika mode full_pipeline ──────
    if request.evaluation_mode == "full_pipeline":
        liveness_result = check_liveness(image, box)

        print(f"Liveness check: {liveness_result['label']} "
              f"(score: {liveness_result['liveness_score']}, "
              f"probs: {liveness_result['raw_probs']})")

        if not liveness_result["is_real"]:
            # SPOOF TERDETEKSI — tolak langsung tanpa cek identitas
            return {
                "success":          True,
                "result":           "rejected",
                "rejection_reason": "spoof_detected",
                "confidence_score": 0.0,
                "liveness_score":   liveness_result["liveness_score"],
                "spoof_type":       liveness_result["label"],
                "threshold_used":   request.threshold,
                "evaluation_mode":  request.evaluation_mode,
            }
    else:
        print(f"Mode facenet_only: liveness detection dilewati (bypass)")

    # ── TAHAP 2: Identity Verification (FaceNet) ──────────────
    face_expanded   = np.expand_dims(face, axis=0)
    input_embedding = embedder.embeddings(face_expanded)[0]

    similarities = [
        cosine_similarity(input_embedding, np.array(db_emb))
        for db_emb in request.embeddings
    ]
    best_score = max(similarities)

    if best_score >= request.threshold:
        result = "accepted"
        reason = None
    else:
        result = "rejected"
        reason = "low_confidence"

    return {
        "success":          True,
        "result":           result,
        "rejection_reason": reason,
        "confidence_score": round(best_score, 4),
        "liveness_score":   liveness_result["liveness_score"],
        "spoof_type":       None,  # None karena lolos liveness (wajah asli)
        "threshold_used":   request.threshold,
        "evaluation_mode":  request.evaluation_mode,
    }