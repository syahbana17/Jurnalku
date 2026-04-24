@extends('layouts.app')
@section('title', 'Jurnal Harian')
@section('page-title', 'Jurnal Harian')
@section('page-subtitle', 'Catat aktivitas mengajar & kuliah S2')

@section('content')

<div class="card page-section">
  <div class="card-title">📝 Input Jurnal Baru</div>
  <form method="POST" action="{{ route('jurnal.store') }}">
    @csrf
    <div class="form-divider blue">🏫 Aktivitas Mengajar</div>
    <div class="row-2">
      <div class="form-group">
        <label>Tanggal <span class="req">*</span></label>
        <input type="date" name="tanggal" id="j-tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required class="{{ $errors->has('tanggal') ? 'err' : '' }}">
        @error('tanggal')<span class="field-error">{{ $message }}</span>@enderror
      </div>
      <div class="form-group">
        <label>Hari</label>
        <input type="text" name="hari" id="j-hari" value="{{ old('hari') }}" readonly>
      </div>
    </div>
    <div class="row-2">
      <div class="form-group">
        <label>Materi yang Diajarkan <span class="req">*</span></label>
        <input type="text" name="materi" value="{{ old('materi') }}" placeholder="Contoh: Sistem Pencernaan Manusia" required class="{{ $errors->has('materi') ? 'err' : '' }}">
        @error('materi')<span class="field-error">{{ $message }}</span>@enderror
      </div>
      <div class="form-group">
        <label>Kelas <span class="req">*</span></label>
        <input type="text" name="kelas" value="{{ old('kelas') }}" placeholder="Contoh: 7A, 8B" required class="{{ $errors->has('kelas') ? 'err' : '' }}">
        @error('kelas')<span class="field-error">{{ $message }}</span>@enderror
      </div>
    </div>
    <div class="form-group">
      <label>Metode Pembelajaran</label>
      <select name="metode">
        <option value="">-- Pilih Metode --</option>
        @foreach(['Ceramah','Diskusi','Praktikum','Problem Based Learning','Project Based Learning','Demonstrasi','Tanya Jawab','Kooperatif'] as $m)
          <option {{ old('metode') === $m ? 'selected' : '' }}>{{ $m }}</option>
        @endforeach
      </select>
    </div>
    <div class="row-2">
      <div class="form-group">
        <label>Kendala yang Dihadapi</label>
        <textarea name="kendala" placeholder="Tuliskan kendala saat mengajar...">{{ old('kendala') }}</textarea>
      </div>
      <div class="form-group">
        <label>Evaluasi & Refleksi Mengajar</label>
        <textarea name="evaluasi" placeholder="Apa yang bisa diperbaiki?">{{ old('evaluasi') }}</textarea>
      </div>
    </div>
    <div class="form-divider green">🎓 Aktivitas Kuliah S2</div>
    <div class="row-2">
      <div class="form-group">
        <label>Mata Kuliah S2</label>
        <input type="text" name="matkul_s2" value="{{ old('matkul_s2') }}" placeholder="Contoh: Metodologi Penelitian">
      </div>
      <div class="form-group">
        <label>Tugas S2</label>
        <input type="text" name="tugas_s2" value="{{ old('tugas_s2') }}" placeholder="Tugas yang diberikan hari ini">
      </div>
    </div>
    <div class="form-group">
      <label>Insight / Hal Menarik Hari Ini</label>
      <textarea name="insight" placeholder="Tuliskan insight atau hal menarik yang dipelajari hari ini...">{{ old('insight') }}</textarea>
    </div>
    <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:4px">
      <a href="{{ route('jurnal.index') }}" class="btn btn-outline">Reset</a>
      <button type="submit" class="btn btn-primary">💾 Simpan Jurnal</button>
    </div>
  </form>
</div>

{{-- Search --}}
<div class="card page-section">
  <form method="GET" action="{{ route('jurnal.index') }}" class="filter-row">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Cari jurnal..." class="search-input">
    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
    <a href="{{ route('jurnal.index') }}" class="btn btn-outline btn-sm">Reset</a>
  </form>
</div>

@if($jurnals->count())
<div class="card">
  <div class="card-title">📋 Riwayat Jurnal <span style="font-weight:500;color:var(--g5);font-size:12px">({{ $jurnals->total() }} total)</span></div>
  <div class="tbl-wrap">
    <table>
      <thead>
        <tr><th>Tanggal</th><th>Materi</th><th>Kelas</th><th>Metode</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        @foreach($jurnals as $j)
        <tr>
          <td>{{ $j->tanggal->translatedFormat('d M Y') }}</td>
          <td style="font-weight:600">{{ $j->materi }}</td>
          <td>{{ $j->kelas }}</td>
          <td>{{ $j->metode ?: '-' }}</td>
          <td>
            <button class="btn btn-danger btn-xs" onclick="confirmDelete('{{ route('jurnal.destroy', $j) }}', 'Hapus jurnal ini?')">Hapus</button>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div style="margin-top:16px">{{ $jurnals->links() }}</div>
</div>
@endif

<script>
(function(){
  const tgl = document.getElementById('j-tanggal');
  const hari = document.getElementById('j-hari');
  const days = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
  function update(){ if(tgl.value) hari.value = days[new Date(tgl.value+'T00:00:00').getDay()]; }
  tgl.addEventListener('change', update); update();
})();
</script>

@include('partials.confirm-modal')
@endsection
