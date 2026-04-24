@extends('layouts.app')
@section('title', 'Refleksi Mingguan')
@section('page-title', 'Refleksi Mingguan')
@section('page-subtitle', 'Evaluasi diri setiap minggu')

@section('content')

<div class="grid-2">

  <div class="card">
    <div class="card-title">🌟 Tulis Refleksi Minggu Ini</div>
    <form method="POST" action="{{ route('refleksi.store') }}">
      @csrf

      <div class="form-group">
        <label>✅ Yang Berhasil Minggu Ini <span class="req">*</span></label>
        <textarea name="berhasil" placeholder="Pencapaian atau hal positif minggu ini..." required style="min-height:100px">{{ old('berhasil') }}</textarea>
      </div>

      <div class="form-group">
        <label>❌ Yang Belum Berhasil <span class="req">*</span></label>
        <textarea name="gagal" placeholder="Apa yang tidak berjalan sesuai rencana?" required style="min-height:100px">{{ old('gagal') }}</textarea>
      </div>

      <div class="form-group">
        <label>🔧 Rencana Perbaikan <span class="req">*</span></label>
        <textarea name="perbaikan" placeholder="Langkah konkret untuk memperbaiki..." required style="min-height:100px">{{ old('perbaikan') }}</textarea>
      </div>

      <div class="form-group">
        <label>🎯 Target Minggu Depan <span class="req">*</span></label>
        <textarea name="target" placeholder="Target spesifik minggu depan..." required style="min-height:100px">{{ old('target') }}</textarea>
      </div>

      <div style="display:flex;gap:10px;justify-content:flex-end">
        <a href="{{ route('refleksi.index') }}" class="btn btn-outline">Reset</a>
        <button type="submit" class="btn btn-primary">🌟 Simpan Refleksi</button>
      </div>
    </form>
  </div>

  <div>
    <div class="card-title" style="margin-bottom:14px">📖 Riwayat Refleksi</div>
    @forelse($riwayat as $r)
    <div class="ref-card">
      <div class="ref-date">📅 {{ $r->tanggal->translatedFormat('d F Y') }}</div>
      <div class="ref-grid">
        <div class="ref-block rb-green"><p>✅ Berhasil</p><span>{{ $r->berhasil }}</span></div>
        <div class="ref-block rb-red"><p>❌ Gagal</p><span>{{ $r->gagal }}</span></div>
        <div class="ref-block rb-yellow"><p>🔧 Perbaikan</p><span>{{ $r->perbaikan }}</span></div>
        <div class="ref-block rb-blue"><p>🎯 Target</p><span>{{ $r->target }}</span></div>
      </div>
      <div style="margin-top:12px;text-align:right">
        <button class="btn btn-danger btn-xs" onclick="confirmDelete('{{ route('refleksi.destroy', $r) }}', 'Hapus refleksi ini?')">Hapus</button>
      </div>
    </div>
    @empty
    <div class="empty"><div class="empty-icon">📖</div><p>Belum ada refleksi tersimpan</p></div>
    @endforelse
  </div>

</div>
@include('partials.confirm-modal')
@endsection
