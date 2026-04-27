@extends('layouts.app')
@section('title', 'Manajemen Tugas')
@section('page-title', 'Manajemen Tugas')
@section('page-subtitle', 'Kelola tugas sekolah & S2')

@section('content')

{{-- Form Tambah --}}
<div class="card page-section">
  <div class="card-title">➕ Tambah Tugas Baru</div>
  <form method="POST" action="{{ route('tugas.store') }}" id="form-tugas">
    @csrf

    {{-- Row 1: Nama, Kategori, Deadline --}}
    <div class="row-3">
      <div class="form-group">
        <label>Nama Tugas <span class="req">*</span></label>
        <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Contoh: Buat RPP Bab 3" required class="{{ $errors->has('nama') ? 'err' : '' }}">
        @error('nama')<span class="field-error">{{ $message }}</span>@enderror
      </div>
      <div class="form-group">
        <label>Kategori <span class="req">*</span></label>
        <select name="kategori" required>
          <option value="">-- Pilih --</option>
          @foreach(['Sekolah','S2','Pribadi'] as $k)
            <option {{ old('kategori') === $k ? 'selected' : '' }}>{{ $k }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label>Deadline <span class="req">*</span></label>
        <input type="date" name="deadline" value="{{ old('deadline', date('Y-m-d')) }}" required>
      </div>
    </div>

    {{-- Clock Picker Jam --}}
    <div class="tugas-clock-section">
      <div class="tugas-clock-wrap">

        {{-- Time Range Display --}}
        <div class="time-range-row">
          <div class="time-range-box active" id="t-box-mulai" onclick="tSwitchClock('mulai')">
            <span class="time-range-label">Mulai</span>
            <span class="time-range-val" id="t-display-mulai">--:--</span>
          </div>
          <div class="time-range-arrow">→</div>
          <div class="time-range-box" id="t-box-selesai" onclick="tSwitchClock('selesai')">
            <span class="time-range-label">Selesai</span>
            <span class="time-range-val" id="t-display-selesai">--:--</span>
          </div>
        </div>

        <input type="hidden" name="jam_mulai"   id="t-inp-mulai">
        <input type="hidden" name="jam_selesai" id="t-inp-selesai">

        <div class="clock-mode-label" id="t-clock-label">Pilih Jam Mulai</div>

        <div class="clock-wrap">
          <svg class="clock-svg" viewBox="0 0 220 220" id="t-clock-svg">
            <circle cx="110" cy="110" r="105" fill="var(--g1)" stroke="var(--g3)" stroke-width="1.5"/>
            <line id="t-clock-hand" x1="110" y1="110" x2="110" y2="30"
              stroke="var(--blue)" stroke-width="2.5" stroke-linecap="round"/>
            <circle cx="110" cy="110" r="5" fill="var(--blue)"/>
            <g id="t-clock-numbers"></g>
          </svg>
        </div>

        <div class="minute-row">
          <span class="minute-label">Menit:</span>
          @foreach(['00','15','30','45'] as $min)
            <button type="button" class="minute-btn t-min-btn" data-min="{{ $min }}">:{{ $min }}</button>
          @endforeach
        </div>

      </div>

      {{-- Form kanan --}}
      <div class="tugas-form-right">
        <div class="form-group">
          <label>Status</label>
          <select name="status">
            @foreach(['Belum','Sedang Dikerjakan','Selesai'] as $s)
              <option {{ old('status','Belum') === $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
        </div>
        <div style="display:flex;justify-content:flex-end;margin-top:auto;padding-top:8px">
          <button type="submit" class="btn btn-primary">➕ Tambah Tugas</button>
        </div>
      </div>
    </div>

  </form>
</div>

{{-- Search & Filter --}}
<div class="card page-section" style="padding:14px 20px">
  <form method="GET" action="{{ route('tugas.index') }}" class="filter-row-compact">
    <div class="filter-search-wrap">
      <span style="color:var(--g5);font-size:14px">🔍</span>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari tugas..." class="filter-search-inline">
    </div>
    <select name="status" class="filter-select-sm">
      <option value="">Semua Status</option>
      @foreach(['Belum','Sedang Dikerjakan','Selesai'] as $s)
        <option {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
      @endforeach
    </select>
    <select name="kategori" class="filter-select-sm">
      <option value="">Semua Kategori</option>
      @foreach(['Sekolah','S2','Pribadi'] as $k)
        <option {{ request('kategori') === $k ? 'selected' : '' }}>{{ $k }}</option>
      @endforeach
    </select>
    <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    <a href="{{ route('tugas.index') }}" class="btn btn-outline btn-sm">Reset</a>
  </form>
</div>

{{-- Tabel --}}
<div class="card">
  <div class="card-title">📋 Daftar Tugas <span style="font-weight:500;color:var(--g5);font-size:12px">({{ $tugas->total() }} total)</span></div>
  <div class="tbl-wrap">
    <table>
      <thead>
        <tr>
          <th>Nama Tugas</th>
          <th>Kategori</th>
          <th>Deadline</th>
          <th>Jam</th>
          <th>Status</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($tugas as $t)
        @php
          $isLate = $t->status !== 'Selesai' && $t->deadline->isPast();
          $isNear = $t->status !== 'Selesai' && !$isLate && $t->deadline->diffInDays(now()) <= 3;
        @endphp
        <tr class="{{ $isLate ? 'row-late' : ($isNear ? 'row-near' : '') }}">
          <td>
            <span style="font-weight:600">{{ $t->nama }}</span>
            @if($isLate)<span class="badge badge-red" style="margin-left:6px;font-size:10px">Terlambat</span>@endif
            @if($isNear)<span class="badge badge-yellow" style="margin-left:6px;font-size:10px">Segera</span>@endif
          </td>
          <td>
            @php
              $katClass = match($t->kategori) {
                'Sekolah' => 'badge-kat-sekolah',
                'S2'      => 'badge-kat-s2',
                'Pribadi' => 'badge-kat-pribadi',
                default   => 'badge-gray',
              };
            @endphp
            <span class="badge {{ $katClass }}">{{ $t->kategori }}</span>
          </td>
          <td>{{ $t->deadline->translatedFormat('d M Y') }}</td>
          <td>
            @if($t->jam_mulai || $t->jam_selesai)
              <div class="jam-badge">
                @if($t->jam_mulai)
                  <div class="jam-item">
                    <span class="jam-keterangan">Mulai</span>
                    <span class="jam-start">🕐 {{ \Carbon\Carbon::parse($t->jam_mulai)->format('H:i') }}</span>
                  </div>
                @endif
                @if($t->jam_mulai && $t->jam_selesai)
                  <span class="jam-arrow">→</span>
                @endif
                @if($t->jam_selesai)
                  <div class="jam-item">
                    <span class="jam-keterangan">Selesai</span>
                    <span class="jam-end">🔴 {{ \Carbon\Carbon::parse($t->jam_selesai)->format('H:i') }}</span>
                  </div>
                @endif
              </div>
            @else
              <span style="color:var(--g4);font-size:12px">—</span>
            @endif
          </td>
          <td><span class="badge {{ $t->status==='Selesai'?'badge-green':($t->status==='Sedang Dikerjakan'?'badge-yellow':'badge-red') }}">{{ $t->status }}</span></td>
          <td style="display:flex;gap:6px">
            <form method="POST" action="{{ route('tugas.status', $t) }}">
              @csrf @method('PATCH')
              <button class="btn btn-outline btn-xs">Update</button>
            </form>
            <button class="btn btn-danger btn-xs" onclick="confirmDelete('{{ route('tugas.destroy', $t) }}','Hapus tugas ini?')">Hapus</button>
          </td>
        </tr>
        @empty
        <tr><td colspan="6"><div class="empty"><div class="empty-icon">📋</div><p>Belum ada tugas</p></div></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="margin-top:16px">{{ $tugas->links() }}</div>
</div>

@include('partials.confirm-modal')

<script>
/* ── TUGAS CLOCK PICKER (independent dari modal jadwal) ── */
(function(){
  const CX=110, CY=110;
  const hours=[12,1,2,3,4,5,6,7,8,9,10,11,0,13,14,15,16,17,18,19,20,21,22,23];
  let mode='mulai';
  let selHour={mulai:null,selesai:null};
  let selMin={mulai:'00',selesai:'00'};

  function degToRad(d){ return d*Math.PI/180; }

  function build(){
    const g=document.getElementById('t-clock-numbers');
    if(!g) return;
    g.innerHTML='';
    hours.forEach(h=>{
      const inner=h===0||h>=13;
      const r=inner?55:82;
      const angle=degToRad((h%12)/12*360-90);
      const x=CX+r*Math.cos(angle), y=CY+r*Math.sin(angle);
      const active=selHour[mode]===h;
      const c=document.createElementNS('http://www.w3.org/2000/svg','circle');
      c.setAttribute('cx',x);c.setAttribute('cy',y);c.setAttribute('r',14);
      c.setAttribute('class','clock-num-bg'+(active?' active-bg':''));
      c.dataset.tugasHour=h; g.appendChild(c);
      const t=document.createElementNS('http://www.w3.org/2000/svg','text');
      t.setAttribute('x',x);t.setAttribute('y',y+4.5);
      t.setAttribute('text-anchor','middle');
      t.setAttribute('class','clock-num'+(active?' active-num':''));
      t.dataset.tugasHour=h;
      t.textContent=h===0?'00':h; g.appendChild(t);
    });
  }

  function updateHand(h){
    const hand=document.getElementById('t-clock-hand');
    if(!hand) return;
    const inner=h===0||h>=13;
    const r=inner?55:82;
    const angle=degToRad((h%12)/12*360-90);
    hand.setAttribute('x2',CX+r*Math.cos(angle));
    hand.setAttribute('y2',CY+r*Math.sin(angle));
  }

  function fmt(h,m){ return h===null?'--:--':String(h).padStart(2,'0')+':'+m; }

  function updateDisplays(){
    const dm=document.getElementById('t-display-mulai');
    const ds=document.getElementById('t-display-selesai');
    const im=document.getElementById('t-inp-mulai');
    const is=document.getElementById('t-inp-selesai');
    if(dm) dm.textContent=fmt(selHour.mulai,selMin.mulai);
    if(ds) ds.textContent=fmt(selHour.selesai,selMin.selesai);
    if(im&&selHour.mulai!==null) im.value=fmt(selHour.mulai,selMin.mulai);
    if(is&&selHour.selesai!==null) is.value=fmt(selHour.selesai,selMin.selesai);
  }

  window.tSwitchClock=function(m){
    mode=m;
    document.getElementById('t-box-mulai')?.classList.toggle('active',m==='mulai');
    document.getElementById('t-box-selesai')?.classList.toggle('active',m==='selesai');
    const lbl=document.getElementById('t-clock-label');
    if(lbl) lbl.textContent=m==='mulai'?'Pilih Jam Mulai':'Pilih Jam Selesai';
    if(selHour[m]!==null) updateHand(selHour[m]);
    build();
    document.querySelectorAll('.t-min-btn').forEach(b=>
      b.classList.toggle('active',b.dataset.min===selMin[m]));
  };

  document.addEventListener('click',function(e){
    const el=e.target.closest('[data-tugas-hour]');
    if(!el) return;
    const h=parseInt(el.dataset.tugasHour);
    selHour[mode]=h;
    updateHand(h); build(); updateDisplays();
    if(mode==='mulai') setTimeout(()=>tSwitchClock('selesai'),300);
  });

  document.addEventListener('click',function(e){
    if(!e.target.classList.contains('t-min-btn')) return;
    const min=e.target.dataset.min;
    selMin[mode]=min;
    document.querySelectorAll('.t-min-btn').forEach(b=>
      b.classList.toggle('active',b.dataset.min===min));
    updateDisplays();
  });

  document.addEventListener('DOMContentLoaded', build);
})();
</script>
@endsection
