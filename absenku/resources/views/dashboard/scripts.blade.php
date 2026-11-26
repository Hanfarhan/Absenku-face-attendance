<script>
// ============================================================
// CONSTANTS
// ============================================================
const FA_DAYS   = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];
const FA_MONTHS = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
const FA_FULL_M = ['Januari','Februari','Maret','April','Mei','Juni','Juli',
                   'Agustus','September','Oktober','November','Desember'];
const FA_TITLES = { dashboard: 'Dasbor', absensi: 'Absensi', riwayat: 'Riwayat Kehadiran', wajah : 'Daftarkan Wajah' };

// Data aktivitas minggu ini dari controller (real data)
const faWeekActivity = @json($weekActivity);

// ============================================================
// DATE & GREETING
// ============================================================
function faGetGreeting() {
    const h = new Date().getHours();
    if (h < 5)  return 'Selamat Tengah Malam';
    if (h < 11) return 'Selamat Pagi';
    if (h < 15) return 'Selamat Siang';
    if (h < 18) return 'Selamat Sore';
    return 'Selamat Malam';
}

function faInitDate() {
    const now = new Date();
    const d = now.getDate(), m = now.getMonth(), y = now.getFullYear(), wd = now.getDay();

    document.getElementById('fa-today-tag').textContent     = `${FA_DAYS[wd]}, ${d} ${FA_MONTHS[m]} ${y}`;
    document.getElementById('fa-greeting-text').textContent = `${faGetGreeting()}, {{ auth()->user()->name }}!`;
    document.getElementById('fa-topbar-date').textContent   = `${d} ${FA_FULL_M[m]} ${y}`;

    const attDate = document.getElementById('fa-att-date');
    if (attDate) attDate.textContent = `${FA_DAYS[wd]}, ${d} ${FA_FULL_M[m]} ${y}`;
}

// ============================================================
// WEEK BARS (data real dari controller)
// ============================================================
function faBuildWeekBars() {
    let bHtml = '', lHtml = '';
    const today = new Date().toISOString().split('T')[0];

    faWeekActivity.forEach((day) => {
        const isToday = day.date === today;
        const color   = isToday ? 'var(--blue-600)' : day.present ? 'var(--blue-200)' : '#e2e8f0';
        const height  = day.present ? Math.max(day.score, 20) : 4;

        bHtml += `<div class="fa-week-bar-col">
                    <div class="fa-week-bar" style="height:${height}px;background:${color};min-height:4px"></div>
                  </div>`;
        lHtml += `<span style="font-size:10px;color:${isToday ? 'var(--blue-600)' : 'var(--gray-400)'};font-weight:${isToday ? 700 : 400}">
                    ${day.label}
                  </span>`;
    });

    document.getElementById('fa-week-bars').innerHTML   = bHtml;
    document.getElementById('fa-week-labels').innerHTML = lHtml;
}

// ============================================================
// PAGE NAVIGATION
// ============================================================
function faShowPage(name, el) {
    document.querySelectorAll('.fa-page').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.fa-nav-item').forEach(n => n.classList.remove('active'));

    document.getElementById('fa-page-' + name).classList.add('active');

    if (el) {
        el.classList.add('active');
    } else {
        document.querySelectorAll('.fa-nav-item').forEach(n => {
            const t = n.textContent.trim().toLowerCase();
            if (
                (name === 'riwayat'   && t.includes('riwayat'))  ||
                (name === 'absensi'   && t.includes('absensi'))   ||
                (name === 'dashboard' && t.includes('dasbor'))
            ) n.classList.add('active');
        });
    }

    document.getElementById('fa-page-title').textContent = FA_TITLES[name];
}

// ============================================================
// CAMERA
// ============================================================
let faCamStream = null;
let faCamActive = false;

// ============================================================
// FILTER RIWAYAT
// ============================================================
function faApplyFilter() {
    const status = document.getElementById('fa-filter-status').value;
    const date   = document.getElementById('fa-filter-date').value;
    let url      = new URL(window.location.href);

    if (status) url.searchParams.set('status', status);
    else url.searchParams.delete('status');

    if (date) url.searchParams.set('date', date);
    else url.searchParams.delete('date');

    url.searchParams.set('page', 1);
    url.hash = 'riwayat';   // ← tandai agar setelah reload langsung ke riwayat
    window.location.href = url.toString();
}

function faResetFilter() {
    let url = new URL(window.location.href);
    url.searchParams.delete('status');
    url.searchParams.delete('date');
    url.searchParams.delete('page');
    url.hash = 'riwayat';   // ← tetap di riwayat setelah reset
    window.location.href = url.toString();
}

// ============================================================
// INIT
// ============================================================
faInitDate();
faBuildWeekBars();

// Setelah reload karna filter/pagination, kembali ke halaman riwayat
(function faRestorePage() {
    const params = new URLSearchParams(window.location.search);
    const hash   = window.location.hash;
    const hasFilter = params.has('status') || params.has('date') || params.has('page');
    if (hash === '#riwayat' || hasFilter) {
        faShowPage('riwayat', null);
    }
})();
</script>