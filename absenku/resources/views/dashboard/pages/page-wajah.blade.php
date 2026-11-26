<div class="fa-page" id="fa-page-wajah">
 
    <div class="fa-page-header">
        <h2>{{ auth()->user()->face_registered ? 'Wajah Saya' : 'Daftarkan Wajah' }}</h2>
        <p>{{ auth()->user()->face_registered ? 'Kelola data wajah Anda untuk keperluan absensi' : 'Daftarkan wajah Anda agar bisa melakukan absensi' }}</p>
    </div>
 
    @if(auth()->user()->face_registered)
        {{-- ── Sudah terdaftar ── --}}
        <div class="fa-reg-layout">
 
            {{-- Status card --}}
            <div class="fa-reg-status-card">
                <div class="fa-reg-status-icon">
                    <svg viewBox="0 0 24 24" fill="var(--green-600)">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17z"/>
                    </svg>
                </div>
                <div class="fa-reg-status-text">
                    <div class="fa-reg-status-title">Wajah Sudah Terdaftar</div>
                    <div class="fa-reg-status-sub">Anda dapat melakukan absensi menggunakan wajah</div>
                </div>
            </div>
 
            {{-- Info card --}}
            <div class="fa-card" style="margin-bottom:14px">
                <div class="fa-card-header">
                    <div class="fa-card-title">Informasi Pendaftaran</div>
                </div>
                <div class="fa-time-row">
                    <span class="fa-time-label">Status</span>
                    <span class="fa-time-val" style="color:var(--green-600)">Aktif</span>
                </div>
                <div class="fa-time-row">
                    <span class="fa-time-label">Jumlah Sample</span>
                    <span class="fa-time-val">10 foto</span>
                </div>
                <div class="fa-time-row">
                    <span class="fa-time-label">Model</span>
                    <span class="fa-time-val">FaceNet + MTCNN</span>
                </div>
            </div>
 
            {{-- Update button --}}
            <div class="fa-card">
                <div class="fa-card-header">
                    <div class="fa-card-title">Perbarui Data Wajah</div>
                </div>
                <p style="font-size:13px;color:var(--gray-600);margin-bottom:16px">
                    Perbarui data wajah jika absensi sering gagal atau penampilan Anda berubah signifikan.
                    Data wajah lama akan digantikan sepenuhnya.
                </p>
                <button class="fa-btn fa-btn-outline" style="max-width:200px" onclick="faShowRegCamera()">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 15.2c1.77 0 3.2-1.43 3.2-3.2S13.77 8.8 12 8.8 8.8 10.23 8.8 12s1.43 3.2 3.2 3.2zm9-8.2h-2.83l-1.42-1.6H8.25L6.83 7H4C2.9 7 2 7.9 2 9v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2z"/>
                    </svg>
                    Perbarui Wajah
                </button>
            </div>
 
        </div>
 
        {{-- Camera section (hidden by default, show on update) --}}
        <div id="fa-reg-camera-section" style="display:none;margin-top:14px">
            @include('dashboard.partials.reg-camera')
        </div>
 
    @else
        {{-- ── Belum terdaftar ── --}}
        <div class="fa-reg-intro">
            <div class="fa-reg-intro-card">
 
                {{-- Steps guide --}}
                <div class="fa-reg-steps">
                    <div class="fa-card-title" style="margin-bottom:16px">Cara Pendaftaran Wajah</div>
                    <div class="fa-reg-step">
                        <div class="fa-reg-step-num">1</div>
                        <div class="fa-reg-step-text">
                            <div class="fa-reg-step-title">Aktifkan Kamera</div>
                            <div class="fa-reg-step-desc">Tekan tombol mulai untuk mengaktifkan kamera</div>
                        </div>
                    </div>
                    <div class="fa-reg-step">
                        <div class="fa-reg-step-num">2</div>
                        <div class="fa-reg-step-text">
                            <div class="fa-reg-step-title">Posisikan Wajah</div>
                            <div class="fa-reg-step-desc">Masukkan wajah ke dalam kotak panduan hingga berwarna hijau</div>
                        </div>
                    </div>
                    <div class="fa-reg-step">
                        <div class="fa-reg-step-num">3</div>
                        <div class="fa-reg-step-text">
                            <div class="fa-reg-step-title">Auto Capture</div>
                            <div class="fa-reg-step-desc">Sistem akan mengambil 10 foto secara otomatis dengan panduan gerakan</div>
                        </div>
                    </div>
                    <div class="fa-reg-step">
                        <div class="fa-reg-step-num">4</div>
                        <div class="fa-reg-step-text">
                            <div class="fa-reg-step-title">Selesai</div>
                            <div class="fa-reg-step-desc">Data wajah tersimpan dan siap digunakan untuk absensi</div>
                        </div>
                    </div>
                </div>
 
                <button class="fa-btn fa-btn-primary" style="margin-top:20px;max-width:220px" onclick="faShowRegCamera()">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 15.2c1.77 0 3.2-1.43 3.2-3.2S13.77 8.8 12 8.8 8.8 10.23 8.8 12s1.43 3.2 3.2 3.2zm9-8.2h-2.83l-1.42-1.6H8.25L6.83 7H4C2.9 7 2 7.9 2 9v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V9c0-1.1-.9-2-2-2z"/>
                    </svg>
                    Mulai Pendaftaran
                </button>
            </div>
        </div>
 
        {{-- Camera section (hidden by default) --}}
        <div id="fa-reg-camera-section" style="display:none;margin-top:14px">
            @include('dashboard.partials.reg-camera')
        </div>
 
    @endif
 
</div>