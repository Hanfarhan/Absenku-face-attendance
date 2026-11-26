"""
liveness.py
============
Modul deteksi liveness (anti-spoofing) menggunakan model MiniFASNetV2-SE
dari facenox/face-antispoof-onnx (akurasi ~98.20%).

Perbedaan dengan MiniFASNet V2 sebelumnya:
    - Input: RGB 128x128 (bukan BGR 80x80)
    - Output: 2 kelas [Real, Spoof] (bukan 3 kelas)
    - Normalisasi: pixel / 255.0

Sumber: github.com/facenox/face-antispoof-onnx
Lisensi: MIT
"""

import json
import os
import numpy as np
import cv2
import onnxruntime as ort
from PIL import Image

# Path ke model ONNX baru
MODEL_PATH = "models/best_model.onnx"

# Path ke config file (threshold dinamis)
CONFIG_PATH = os.path.join(os.path.dirname(__file__), "config.json")

# Label output model: index 0 = Real, index 1 = Spoof
LABELS = ["real", "spoof"]


def _load_liveness_threshold() -> float:
    """
    Baca liveness threshold dari config.json.
    Dipanggil setiap kali check_liveness() dijalankan,
    sehingga perubahan threshold langsung berlaku tanpa restart server.
    """
    try:
        with open(CONFIG_PATH, "r") as f:
            cfg = json.load(f)
        return float(cfg.get("liveness_threshold", 0.5))
    except Exception:
        return 0.5  # fallback default


# Load model sekali saat modul diimport
print("Loading Anti-Spoofing model (facenox/MiniFASNetV2-SE)...")
_session     = ort.InferenceSession(MODEL_PATH, providers=["CPUExecutionProvider"])
_input_name  = _session.get_inputs()[0].name
_output_name = _session.get_outputs()[0].name
print(f"Anti-Spoofing model loaded — input: {_session.get_inputs()[0].shape}")


def _crop_with_margin(image: np.ndarray, box: tuple, scale: float = 2.7, size: int = 128) -> np.ndarray:
    """
    Crop wajah dengan margin dari bounding box MTCNN,
    lalu resize ke ukuran target (128x128 untuk model ini).

    image : numpy array RGB
    box   : (x, y, w, h) bounding box dari MTCNN
    """
    x, y, w, h = box
    cx = x + w / 2
    cy = y + h / 2

    new_w = w * scale
    new_h = h * scale

    x1 = max(0, int(cx - new_w / 2))
    y1 = max(0, int(cy - new_h / 2))
    x2 = min(image.shape[1], int(cx + new_w / 2))
    y2 = min(image.shape[0], int(cy + new_h / 2))

    cropped = image[y1:y2, x1:x2]
    resized  = np.array(Image.fromarray(cropped).resize((size, size)))

    return resized


def check_liveness(image: np.ndarray, face_box: tuple) -> dict:
    """
    Cek apakah wajah dalam gambar adalah wajah ASLI atau SPOOF.

    Parameters
    ----------
    image    : numpy array RGB (frame asli sebelum di-crop MTCNN)
    face_box : (x, y, w, h) bounding box wajah dari hasil deteksi MTCNN

    Returns
    -------
    dict berisi:
        is_real        : bool  -> True jika wajah asli
        label          : str   -> 'real' / 'spoof'
        spoof_type     : str   -> 'print_photo' / 'digital_screen' / None
                                  (diisi None karena model hanya 2 kelas)
        liveness_score : float -> probabilitas wajah asli (0.0 - 1.0)
        raw_probs      : dict  -> probabilitas tiap kelas
        threshold_used : float -> threshold yang dipakai saat check ini
    """
    # Baca threshold terkini dari config.json (dinamis, tanpa restart)
    liveness_threshold = _load_liveness_threshold()

    # 1. Crop wajah dengan margin (input model: 128×128 RGB)
    face_crop = _crop_with_margin(image, face_box, scale=2.7, size=128)

    # 2. Normalisasi pixel ke range [0, 1] — model menggunakan RGB
    face_normalized = face_crop.astype(np.float32) / 255.0

    # 3. Reshape HWC -> NCHW (format input ONNX)
    input_tensor = np.transpose(face_normalized, (2, 0, 1))  # HWC -> CHW
    input_tensor = np.expand_dims(input_tensor, axis=0)       # tambah dimensi batch

    # 4. Jalankan inference
    outputs = _session.run([_output_name], {_input_name: input_tensor})
    logits  = outputs[0][0]

    # 5. Softmax untuk mendapatkan probabilitas
    exp_logits = np.exp(logits - np.max(logits))
    probs      = exp_logits / np.sum(exp_logits)

    prob_real  = float(probs[0])  # index 0 = Real
    prob_spoof = float(probs[1])  # index 1 = Spoof

    # 6. Liveness score = probabilitas Real, bandingkan dengan threshold dinamis
    liveness_score = prob_real
    is_real        = liveness_score >= liveness_threshold
    label          = "real" if is_real else "spoof"

    print(f"Liveness check: {label} "
          f"(score: {round(liveness_score, 4)}, "
          f"threshold: {liveness_threshold}, "
          f"probs: real={round(prob_real, 4)}, spoof={round(prob_spoof, 4)})")

    return {
        "is_real":          is_real,
        "label":            label,
        "spoof_type":       "spoof" if not is_real else None,
        "liveness_score":   round(liveness_score, 4),
        "threshold_used":   liveness_threshold,
        "raw_probs": {
            "live":           round(prob_real, 4),
            "print_photo":    round(prob_spoof / 2, 4),
            "digital_screen": round(prob_spoof / 2, 4),
        },
    }