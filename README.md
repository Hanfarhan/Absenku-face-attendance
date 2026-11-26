# 📸 Absenku — Intelligent Face Attendance System with Anti-Spoofing

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![FastAPI](https://img.shields.io/badge/FastAPI-0.100+-009688?style=for-the-badge&logo=fastapi&logoColor=white)](https://fastapi.tiangolo.com)
[![Python](https://img.shields.io/badge/Python-3.10+-3776AB?style=for-the-badge&logo=python&logoColor=white)](https://python.org)
[![ONNX Runtime](https://img.shields.io/badge/ONNX_Runtime-Inference-005CED?style=for-the-badge&logo=onnx&logoColor=white)](https://onnxruntime.ai/)
[![OpenCV](https://img.shields.io/badge/OpenCV-Computer_Vision-5C3EE8?style=for-the-badge&logo=opencv&logoColor=white)](https://opencv.org/)

Sistem absensi cerdas berbasis web yang mengintegrasikan aplikasi manajemen absensi modern dengan **Computer Vision AI Microservice**. Sistem ini dirancang untuk mengatasi kecurangan absensi (*attendance fraud*) dengan menerapkan verifikasi identitas biometrik dan **deteksi keaslian wajah (Liveness Detection / Anti-Spoofing)** secara *real-time*.

---

## 🌟 Highlight & Fitur Utama

- **Presentation Attack Detection (Anti-Spoofing)**: Mampu mendeteksi dan menolak upaya pemalsuan absensi menggunakan foto cetak (*print attack*), replay video di layar HP/tablet (*screen replay attack*), maupun foto digital.
- **Accurate Facial Verification**: Ekstraksi fitur wajah 512 dimensi menggunakan FaceNet dan kalkulasi *Cosine Similarity* terhadap database embedding karyawan.
- ** Decoupled Microservice Architecture**: Memisahkan backend web (Laravel) dengan komputasi AI intensif (FastAPI/Python) untuk skalabilitas dan efisiensi resource.
- **Real-time Attendance & Dashboard**: Presensi berbasis kamera browser, pencatatan otomatis waktu masuk/pulang, riwayat kehadiran, serta rekap data karyawan.
- **Dynamic AI Threshold Control**: Konfigurasi nilai *liveness threshold* dan *facenet threshold* yang fleksibel untuk menyeimbangkan FAR (*False Acceptance Rate*) dan FRR (*False Rejection Rate*).

---

## Arsitektur Sistem & Pipeline AI

```text
[ Browser / Client Camera ]
           │ (Base64 Frame Capture)
           ▼
[ Laravel Web Application ] ──────────┐
  - Autentikasi Pengguna              │ (HTTP POST Request)
  - Penyimpanan Riwayat & Profil      ▼
                                [ FastAPI AI Service ]
                                  │
                                  ├─► 1. Preprocessing & Alignment (MTCNN)
                                  │
                                  ├─► 2. Liveness Detection (MiniFASNet ONNX)
                                  │      └── Spoof? ──► [ REJECT: Fake / Screen ]
                                  │
                                  └─► 3. Feature Extraction (FaceNet Embedder)
                                         └── Cosine Similarity vs Enrolled Embeddings
                                                ├── Match >= Threshold ──► [ ACCEPTED ]
                                                └── Match < Threshold  ──► [ REJECT: Low Conf ]
```

---

## 📂 Struktur Monorepo

```text
Project-Absenku/
│
├── absenku/                 # Web Application & Dashboard (Laravel)
│   ├── app/                 # Controllers, Models, Services
│   ├── resources/           # Blade Views, JavaScript (Camera handler), Tailwind CSS
│   ├── routes/              # Web & API Routes
│   └── database/            # Migrations & Seeders
│
├── absenku-api/             # AI Inference Microservice (Python)
│   ├── models/              # Pretrained Weights (MiniFASNet ONNX, Best Models)
│   ├── main.py              # FastAPI Endpoints, CORS, Pipeline Orchestration
│   ├── liveness.py          # Anti-spoofing inference & Fourier/Texture analyzer
│   ├── config.json          # Dynamic AI threshold configurations
│   └── requirements.txt     # Python Dependencies
│
└── README.md                # Dokumentasi utama project
```

---

## 🛠️ Tech Stack & Library

| Domain | Teknologi / Library | Kegunaan |
|---|---|---|
| **Web Backend** | **Laravel 11 (PHP 8.2+)** | REST API, Database Management, Session & Auth |
| **Database** | **MySQL** | Relational Database (User, Logs, Embeddings) |
| **Frontend** | **Blade, Tailwind CSS, JavaScript** | Responsive UI, WebRTC MediaStream Camera |
| **AI Backend** | **FastAPI (ASGI)** | High-performance asynchronous AI Inference Service |
| **Face Anti-Spoofing** | **MiniFASNet (ONNX Runtime)** | Liveness Detection inference berkecepatan tinggi |
| **Face Recognition** | **MTCNN + Keras-FaceNet** | Face Detection, Face Alignment, 512-d Embedding |
| **Image Processing** | **OpenCV & Pillow** | Normalisasi citra, crop ROI, array formatting |

---

## 🚀 Panduan Menjalankan Sistem di Lokal

Pastikan telah menginstall **PHP 8.2+**, **Composer**, **Node.js**, **Python 3.10+**, dan **MySQL**.

### 1. Menjalankan AI Microservice (Python)

1. Masuk ke direktori service AI:
   ```bash
   cd absenku-api
   ```
2. Buat dan aktifkan Python Virtual Environment:
   ```bash
   # Windows
   python -m venv venv
   .\venv\Scripts\activate

   # Linux / macOS
   python3 -m venv venv
   source venv/bin/activate
   ```
3. Install dependensi:
   ```bash
   pip install -r requirements.txt
   ```
4. Jalankan FastAPI server:
   ```bash
   python main.py
   # atau menggunakan uvicorn:
   # uvicorn main:app --host 127.0.0.1 --port 8000 --reload
   ```
   *Service AI akan aktif dan siap menerima request inferensi.*

---

### 2. Menjalankan Web Application (Laravel)

1. Buka terminal baru dan masuk ke direktori web:
   ```bash
   cd absenku
   ```
2. Install dependensi PHP dan Node.js:
   ```bash
   composer install
   npm install
   ```
3. Salin file environment dan generate application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. Sesuaikan konfigurasi database di file `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=absenku
   DB_USERNAME=root
   DB_PASSWORD=
   ```
5. Jalankan database migration:
   ```bash
   php artisan migrate
   ```
6. Jalankan build assets dan development server:
   ```bash
   # Jalankan Vite untuk frontend
   npm run dev

   # Jalankan Laravel Server di terminal terpisah
   php artisan serve
   ```
7. Buka browser dan akses aplikasi melalui `http://127.0.0.1:8000`.

---

## 🌟 Credits & Acknowledgements

- **Face Anti-Spoofing Model**: Menggunakan bobot model dan arsitektur *MiniFASNet* dari riset open-source [MiniVision Silent-Face-Anti-Spoofing](https://github.com/minivision-ai/Silent-Face-Anti-Spoofing).
- **FaceNet**: Didasarkan pada arsitektur Google FaceNet untuk komputasi representasi embedding wajah berdimensi tinggi.
---