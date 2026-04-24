@extends('layouts.app')
@section('title','Dashboard')
@section('page-title','Dashboard')
@section('page-subtitle'){{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}@endsection

@section('content')

@php
  $uid        = auth()->id();
  $tugasAll   = \App\Models\Tugas::where('user_id',$uid)->get();
  $selesai    = $tugasAll->where('status','Selesai')->count();
  $berjalan   = $tugasAll->where('status','Sedang Dikerjakan')->count();
  $belum      = $tugasAll->where('status','Belum')->count();
  $totalTugas = $tugasAll->count();
  $pctSelesai = $totalTugas > 0 ? round($selesai/$totalTugas*100) : 0;
@endphp

{{-- STAT CARDS --}}
<div class="stat-grid page-section">
  <div class="stat-card">
    <div class="stat-icon-wrap si-blue">📝</div>
    <div class="stat-info"><p>Total Jurnal</p><h3>{{ $stats['jurnal'] }}</h3><span class="stat-sub">📖 Jurnal hari ini</span></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon-wrap si-green">✅</div>
    <div class="stat-info"><p>Total Tugas</p><h3>{{ $stats['tugas'] }}</h3>
      <span class="stat-sub"><span style="color:var(--green)">● {{ $selesai }} selesai</span> &nbsp;● {{ $berjalan }} berjalan</span>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon-wrap si-purple">🔬</div>
    <div class="stat-info"><p>Arsip Materi</p><h3>{{ $stats['materi'] }}</h3><span class="stat-sub">📚 Tersimpan rapi</span></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon-wrap si-orange">🎓</div>
    <div class="stat-info"><p>Semester S2</p><h3>{{ $semesters->count() }}</h3><span class="stat-sub">→ {{ $semesterView?->nama ?? '-' }} Aktif</span></div>
  </div>
</div>

{{-- ROW 2: Jadwal + Progress --}}
<div class="grid-2 page-section">

  {{-- Jadwal --}}
  <div class="card">
    <div class="card-title-row">
      <span class="card-title">📅 Jadwal Hari Ini</span>
      <div style="display:flex;gap:8px;align-items:center">
        <button class="btn-ghost-sm" onclick="toggleModal('modal-kalender')">📅 Lihat Kalender</button>
        <button class="btn btn-primary btn-xs" onclick="toggleModal('modal-jadwal')">+ Tambah</button>
      </div>
    </div>
    @if($jadwals->isEmpty())
      <div class="empty"><div class="empty-icon">📅</div><p>Belum ada jadwal</p></div>
    @else
      <div class="jadwal-timeline">
        @foreach($jadwals as $j)
        @php
          $dotCls   = match($j->kategori){ 'Sekolah'=>'dot-blue','S2'=>'dot-green',default=>'dot-yellow' };
          $badgeCls = match($j->kategori){ 'Sekolah'=>'badge-blue','S2'=>'badge-green','Mandiri'=>'badge-gray',default=>'badge-gray' };
        @endphp
        <div class="timeline-item">
          <div class="timeline-time">{{ $j->jam }}{{ $j->jam_selesai ? ' – '.$j->jam_selesai : '' }}</div>
          <div class="timeline-dot-wrap">
            <div class="timeline-dot {{ $dotCls }}"></div>
            <div class="timeline-line"></div>
          </div>
          <div class="timeline-body">
            <div class="timeline-title">{{ $j->judul }} <span class="badge {{ $badgeCls }}">{{ $j->kategori }}</span></div>
            @if($j->keterangan)<div class="timeline-sub">{{ $j->keterangan }}</div>@endif
          </div>
          <button class="btn-icon-del" onclick="confirmDelete('{{ route('jadwal.destroy',$j) }}','Hapus jadwal?')">✕</button>
        </div>
        @endforeach
      </div>
    @endif
  </div>

  {{-- Progress S2 --}}
  <div class="card">
    <div class="card-title-row">
      <span class="card-title">🎓 Progress S2</span>
      <div style="display:flex;gap:6px;align-items:center">
        {{-- Semester Dropdown --}}
        <div class="semester-dropdown" id="sem-dropdown">
          <button class="btn-ghost-sm sem-trigger" onclick="toggleSemDropdown()">
            {{ $semesterView?->nama ?? 'Pilih Semester' }} ▾
          </button>
          <div class="sem-menu" id="sem-menu">
            @foreach($semesters as $sem)
            <div class="sem-menu-item {{ $sem->id == $semesterView?->id ? 'active' : '' }}">
              <a href="{{ route('dashboard', ['semester' => $sem->id]) }}" class="sem-name">
                {{ $sem->nama }}
                @if($sem->aktif)<span class="sem-aktif-dot">●</span>@endif
              </a>
              <div style="display:flex;gap:4px">
                @if(!$sem->aktif)
                <form method="POST" action="{{ route('semester.activate', $sem) }}">
                  @csrf @method('PATCH')
                  <button class="sem-btn-aktif" title="Jadikan aktif">✓</button>
                </form>
                @endif
                @if($semesters->count() > 1)
                <form method="POST" action="{{ route('semester.destroy', $sem) }}" onsubmit="return confirm('Hapus semester ini?')">
                  @csrf @method('DELETE')
                  <button class="sem-btn-del" title="Hapus">✕</button>
                </form>
                @endif
              </div>
            </div>
            @endforeach
            <div class="sem-menu-divider"></div>
            <button class="sem-btn-add" onclick="toggleSemDropdown();toggleModal('modal-semester')">+ Tambah Semester</button>
          </div>
        </div>
        <button class="btn btn-primary btn-xs" onclick="toggleModal('modal-progres')">+ Tambah</button>
      </div>
    </div>

    {{-- Donut --}}
    <div class="progress-donut-wrap">
      <div class="donut-chart">
        <svg viewBox="0 0 120 120" width="120" height="120">
          <circle cx="60" cy="60" r="50" fill="none" stroke="var(--g2)" stroke-width="12"/>
          <circle cx="60" cy="60" r="50" fill="none" stroke="var(--blue)" stroke-width="12"
            stroke-dasharray="{{ round($pctSelesai * 3.14159) }} 314"
            stroke-dashoffset="78.5" stroke-linecap="round"
            style="transition:stroke-dasharray .8s ease"/>
        </svg>
        <div class="donut-label">
          <span class="donut-pct">{{ $pctSelesai }}%</span>
          <span class="donut-sub">Selesai</span>
        </div>
      </div>
      <div class="donut-info">
        <p class="donut-msg"><strong>{{ $selesai }} dari {{ $totalTugas }} tugas selesai</strong></p>
        <p class="donut-cheer">Terus semangat! 💪</p>
        <div class="donut-bar-wrap"><div class="donut-bar" style="width:{{ $pctSelesai }}%"></div></div>
        <div class="donut-stats">
          <div><span class="ds-num" style="color:var(--green)">{{ $selesai }}</span><span class="ds-lbl">● Selesai</span></div>
          <div><span class="ds-num" style="color:var(--yellow)">{{ $berjalan }}</span><span class="ds-lbl">● Berjalan</span></div>
          <div><span class="ds-num">{{ $belum }}</span><span class="ds-lbl">Belum Mulai</span></div>
        </div>
      </div>
    </div>

    {{-- Progress bars --}}
    @if($progres->isNotEmpty())
    <div style="margin-top:16px;border-top:1px solid var(--g2);padding-top:14px">
      @foreach($progres as $p)
      <div class="prog-item">
        <div class="prog-label">
          <span>{{ $p->label }}</span>
          <div style="display:flex;align-items:center;gap:6px">
            <span>{{ $p->persen }}%</span>
            <button class="btn-icon-edit" onclick="openEditProgres({{ $p->id }},'{{ addslashes($p->label) }}',{{ $p->persen }},'{{ $p->warna }}')">✎</button>
            <button class="btn-icon-del" onclick="confirmDelete('{{ route('progres.destroy',$p) }}','Hapus progress?')">✕</button>
          </div>
        </div>
        <div class="prog-track"><div class="prog-fill {{ $p->warna !== 'blue' ? $p->warna[0] : '' }}" style="width:{{ $p->persen }}%"></div></div>
      </div>
      @endforeach
    </div>
    @else
    <div class="empty" style="padding:20px"><p>Belum ada progress untuk {{ $semesterView?->nama }}</p></div>
    @endif
  </div>
</div>

{{-- ROW 3: Tugas + Catatan --}}
<div class="grid-2 page-section">
  <div class="card">
    <div class="card-title">⏰ Tugas Terdekat</div>
    @forelse($tugasTerdekat as $t)
    <div class="tugas-item">
      <div>
        <p class="tugas-nama">{{ $t->nama }}</p>
        <span class="tugas-meta">{{ $t->kategori }} • Deadline: {{ $t->deadline->translatedFormat('d F Y') }}</span>
      </div>
      <span class="badge {{ $t->status==='Selesai'?'badge-green':($t->status==='Sedang Dikerjakan'?'badge-yellow':'badge-red') }}">{{ $t->status }}</span>
    </div>
    @empty
    <div class="empty"><div class="empty-icon">✅</div><p>Tidak ada tugas aktif</p></div>
    @endforelse
    <a href="{{ route('tugas.index') }}" class="btn-link-arrow">Lihat Semua Tugas →</a>
  </div>

  <div class="card" style="display:flex;flex-direction:column">
    <div class="card-title">💡 Catatan Cepat</div>
    <textarea id="quick-note" class="quick-note-ta" placeholder="Tulis catatan cepat di sini...">{{ $quickNote?->content }}</textarea>
    <div class="note-footer">
      <span id="note-status">✅ Tersimpan otomatis</span>
      <span id="note-count">0/500</span>
    </div>
  </div>
</div>

{{-- ===== MODALS ===== --}}

{{-- Modal Kalender --}}
<div class="modal-backdrop" id="modal-kalender" onclick="closeModalOutside(event,'modal-kalender')">
  <div class="modal" style="max-width:860px;width:100%">
    <div class="modal-header">
      <h3>📅 Kalender Jadwal & Tugas</h3>
      <button class="modal-close" onclick="toggleModal('modal-kalender')">✕</button>
    </div>
    <div id="kalender-container"></div>
  </div>
</div>

{{-- Modal Tambah Semester --}}
<div class="modal-backdrop" id="modal-semester" onclick="closeModalOutside(event,'modal-semester')">
  <div class="modal" style="max-width:380px">
    <div class="modal-header">
      <h3>🎓 Tambah Semester</h3>
      <button class="modal-close" onclick="toggleModal('modal-semester')">✕</button>
    </div>
    <form method="POST" action="{{ route('semester.store') }}">
      @csrf
      <div class="form-group">
        <label>Nama Semester <span class="req">*</span></label>
        <input type="text" name="nama" placeholder="Contoh: Semester 3" required>
        <span style="font-size:11.5px;color:var(--g5);margin-top:6px;display:block">Contoh: Semester 3, Semester 4, dst.</span>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end">
        <button type="button" class="btn btn-outline" onclick="toggleModal('modal-semester')">Batal</button>
        <button type="submit" class="btn btn-primary">Tambah</button>
      </div>
    </form>
  </div>
</div>

{{-- Modal Jadwal --}}
<div class="modal-backdrop" id="modal-jadwal" onclick="closeModalOutside(event,'modal-jadwal')">
  <div class="modal modal-jadwal-wrap">
    <div class="modal-header">
      <h3>📅 Tambah Jadwal</h3>
      <button class="modal-close" onclick="toggleModal('modal-jadwal')">✕</button>
    </div>
    <form method="POST" action="{{ route('jadwal.store') }}">
      @csrf
      <div class="jadwal-layout">
        <div class="jadwal-clock-col">
          <div class="time-range-row">
            <div class="time-range-box active" id="box-mulai" onclick="switchClock('mulai')">
              <span class="time-range-label">Mulai</span>
              <span class="time-range-val" id="display-mulai">--:--</span>
            </div>
            <div class="time-range-arrow">→</div>
            <div class="time-range-box" id="box-selesai" onclick="switchClock('selesai')">
              <span class="time-range-label">Selesai</span>
              <span class="time-range-val" id="display-selesai">--:--</span>
            </div>
          </div>
          <input type="hidden" name="jam" id="inp-mulai">
          <input type="hidden" name="jam_selesai" id="inp-selesai">
          <div class="clock-mode-label" id="clock-mode-label">Pilih Jam Mulai</div>
          <div class="clock-wrap">
            <svg class="clock-svg" viewBox="0 0 220 220" id="clock-svg">
              <circle cx="110" cy="110" r="105" fill="var(--g1)" stroke="var(--g3)" stroke-width="1.5"/>
              <line id="clock-hand" x1="110" y1="110" x2="110" y2="30" stroke="var(--blue)" stroke-width="2.5" stroke-linecap="round"/>
              <circle cx="110" cy="110" r="5" fill="var(--blue)"/>
              <g id="clock-numbers"></g>
            </svg>
          </div>
          <div class="minute-row">
            <span class="minute-label">Menit:</span>
            @foreach(['00','15','30','45'] as $min)
              <button type="button" class="minute-btn" data-min="{{ $min }}">:{{ $min }}</button>
            @endforeach
          </div>
        </div>
        <div class="jadwal-form-col">
          <div class="form-group">
            <label>Kategori <span class="req">*</span></label>
            <select name="kategori" required>
              <option value="Sekolah">🏫 Sekolah</option>
              <option value="S2">🎓 S2</option>
              <option value="Mandiri">📖 Mandiri</option>
              <option value="Lainnya">📌 Lainnya</option>
            </select>
          </div>
          <div class="form-group">
            <label>Judul <span class="req">*</span></label>
            <input type="text" name="judul" placeholder="Contoh: Mengajar IPA Kelas 7A" required>
          </div>
          <div class="form-group">
            <label>Keterangan</label>
            <input type="text" name="keterangan" placeholder="Contoh: Sel dan Jaringan">
          </div>
          <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:auto;padding-top:8px">
            <button type="button" class="btn btn-outline btn-sm" onclick="toggleModal('modal-jadwal')">Batal</button>
            <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

{{-- Modal Progress --}}
<div class="modal-backdrop" id="modal-progres" onclick="closeModalOutside(event,'modal-progres')">
  <div class="modal">
    <div class="modal-header"><h3>🎓 Tambah Progress — {{ $semesterView?->nama }}</h3><button class="modal-close" onclick="toggleModal('modal-progres')">✕</button></div>
    <form method="POST" action="{{ route('progres.store') }}">
      @csrf
      <input type="hidden" name="semester_id" value="{{ $semesterView?->id }}">
      <div class="form-group"><label>Label <span class="req">*</span></label><input type="text" name="label" placeholder="Contoh: Metodologi Penelitian" required></div>
      <div class="row-2">
        <div class="form-group"><label>Persentase (%)</label><input type="number" name="persen" min="0" max="100" placeholder="70" required></div>
        <div class="form-group"><label>Warna</label>
          <select name="warna"><option value="blue">🔵 Biru</option><option value="green">🟢 Hijau</option><option value="yellow">🟡 Kuning</option><option value="purple">🟣 Ungu</option></select>
        </div>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end">
        <button type="button" class="btn btn-outline" onclick="toggleModal('modal-progres')">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>

{{-- Modal Edit Progress --}}
<div class="modal-backdrop" id="modal-edit-progres" onclick="closeModalOutside(event,'modal-edit-progres')">
  <div class="modal">
    <div class="modal-header"><h3>✎ Edit Progress</h3><button class="modal-close" onclick="toggleModal('modal-edit-progres')">✕</button></div>
    <form method="POST" id="form-edit-progres" action="">
      @csrf @method('PATCH')
      <div class="form-group"><label>Label</label><input type="text" name="label" id="edit-label" required></div>
      <div class="row-2">
        <div class="form-group"><label>Persentase (%)</label><input type="number" name="persen" id="edit-persen" min="0" max="100" required></div>
        <div class="form-group"><label>Warna</label>
          <select name="warna" id="edit-warna"><option value="blue">🔵 Biru</option><option value="green">🟢 Hijau</option><option value="yellow">🟡 Kuning</option><option value="purple">🟣 Ungu</option></select>
        </div>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end">
        <button type="button" class="btn btn-outline" onclick="toggleModal('modal-edit-progres')">Batal</button>
        <button type="submit" class="btn btn-primary">Update</button>
      </div>
    </form>
  </div>
</div>

@include('partials.confirm-modal')

{{-- FullCalendar --}}
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<script>
// Quick note
(function(){
  const ta = document.getElementById('quick-note');
  const st = document.getElementById('note-status');
  const ct = document.getElementById('note-count');
  if(!ta) return;
  function updateCount(){ if(ct) ct.textContent = ta.value.length+'/500'; }
  updateCount();
  let timer;
  ta.addEventListener('input', () => {
    updateCount(); st.textContent = '...menyimpan';
    clearTimeout(timer);
    timer = setTimeout(() => {
      fetch('{{ route('note.save') }}', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
        body: JSON.stringify({content: ta.value})
      }).then(()=>{ st.textContent='✅ Tersimpan otomatis'; });
    }, 800);
  });
})();

// Edit progres
function openEditProgres(id, label, persen, warna){
  document.getElementById('form-edit-progres').action = '/progres/' + id;
  document.getElementById('edit-label').value  = label;
  document.getElementById('edit-persen').value = persen;
  document.getElementById('edit-warna').value  = warna;
  toggleModal('modal-edit-progres');
}

// Semester dropdown
function toggleSemDropdown(){
  document.getElementById('sem-menu').classList.toggle('show');
}
document.addEventListener('click', function(e){
  if(!e.target.closest('#sem-dropdown')) {
    document.getElementById('sem-menu')?.classList.remove('show');
  }
});

// Kalender
let calendarInit = false;
const origToggle = window.toggleModal;
window.toggleModal = function(id){
  origToggle(id);
  if(id === 'modal-kalender' && document.getElementById(id).classList.contains('show') && !calendarInit){
    calendarInit = true;
    const events = @json($kalenderEvents);
    const cal = new FullCalendar.Calendar(document.getElementById('kalender-container'), {
      initialView: 'dayGridMonth',
      locale: 'id',
      headerToolbar: { left:'prev,next today', center:'title', right:'dayGridMonth,timeGridWeek,listWeek' },
      height: 520,
      events: events,
      eventClick: function(info){
        const p = info.event.extendedProps;
        alert(info.event.title + '\n' + (p.kategori||'') + (p.keterangan ? '\n'+p.keterangan : '') + (p.status ? '\nStatus: '+p.status : ''));
      }
    });
    cal.render();
  }
};
</script>
@endsection
