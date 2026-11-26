<aside class="fa-sidebar">
 
    {{-- Logo --}}
    <div class="fa-sidebar-logo">
        <div class="fa-logo-icon">
            <svg viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
            </svg>
        </div>
        <div>
            <div class="fa-logo-text">FaceAttend</div>
            <div class="fa-logo-sub">Sistem Absensi</div>
        </div>
    </div>
 
    {{-- Navigation --}}
    <nav class="fa-sidebar-nav">
        <div class="fa-nav-label">Menu</div>
 
        <button class="fa-nav-item active" onclick="faShowPage('dashboard', this)">
            <svg class="fa-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
            </svg>
            Dashboard
        </button>
 
        <button class="fa-nav-item" onclick="faShowPage('absensi', this)">
            <svg class="fa-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/>
            </svg>
            Absensi
            @if(!auth()->user()->face_registered)
                <span class="fa-nav-badge" style="background:var(--amber-600)">!</span>
            @endif
        </button>
 
        <button class="fa-nav-item" onclick="faShowPage('wajah', this)">
            <svg class="fa-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                <path d="M9 11.75c-.69 0-1.25.56-1.25 1.25s.56 1.25 1.25 1.25 1.25-.56 1.25-1.25-.56-1.25-1.25-1.25zm6 0c-.69 0-1.25.56-1.25 1.25s.56 1.25 1.25 1.25 1.25-.56 1.25-1.25-.56-1.25-1.25-1.25zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8 0-.29.02-.58.05-.86 2.36-1.05 4.23-2.98 5.21-5.37C11.07 8.33 14.05 10 17.42 10c.78 0 1.53-.09 2.25-.26.21.71.33 1.47.33 2.26 0 4.41-3.59 8-8 8z"/>
            </svg>
            @if(auth()->user()->face_registered)
                Wajah Saya
                <span class="fa-nav-badge" style="background:var(--green-600);margin-left:auto">✓</span>
            @else
                Daftarkan Wajah
                <span class="fa-nav-badge" style="background:var(--amber-600);margin-left:auto">Baru</span>
            @endif
        </button>
 
        <button class="fa-nav-item" onclick="faShowPage('riwayat', this)">
            <svg class="fa-nav-icon" viewBox="0 0 24 24" fill="currentColor">
                <path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/>
            </svg>
            Riwayat
        </button>
 
    </nav>
 
    {{-- User footer --}}
    <div class="fa-sidebar-footer">
        <div class="fa-sidebar-user">
            <div class="fa-user-av">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div style="flex:1;min-width:0">
                <div class="fa-user-name">{{ auth()->user()->name }}</div>
                <div class="fa-user-role">
                    @if(auth()->user()->face_registered)
                        <span style="color:var(--green-600)">● Wajah Terdaftar</span>
                    @else
                        <span style="color:var(--amber-600)">● Belum Daftar Wajah</span>
                    @endif
                </div>
            </div>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="#888780">
                <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"/>
            </svg>
        </div>
    </div>
 
</aside>