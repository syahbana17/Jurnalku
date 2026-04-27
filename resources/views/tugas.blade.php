@extends('layouts.app')
@section('title', 'Manajemen Tugas')
@section('page-title', 'Manajemen Tugas')
@section('page-subtitle', 'Kelola tugas sekolah & S2')

@section('content')

{{-- Form Tambah --}}
<div class="card page-section">
  <div class="card-title">➕ Tambah Tugas Baru</div>
  <form method="POST" action="{{ route('tugas.store') }}">
    @csrf
    <div class="row-3">
      <div class="form-group">
        <label>Nama Tugas <span class="req">*</span></label>
        <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Contoh: Buat RPP Bab 3" required class="{{ $errors->has('nama') ? 'err' : '' }}">
        @error('nama')<span class="field-error">{{ $message }}</span>@enderror
      </div>
      <div class="form-group">
        <label>Kategori <span class="req">*</span></label>
        <select name="kategori" required class="{{ $errors->has('kategori') ? 'err' : '' }}">
          <option value="">-- Pilih --</option>
          @foreach(['Sekolah','S2','Pribadi'] as $k)
            <option {{ old('kategori') === $k ? 'selected' : '' }}>{{ $k }}</option>
          @endforeach
        </select>
        @error('kategori')<span class="field-error">{{ $message }}</span>@enderror
      </div>
      <div class="form-group">
        <label>Deadline <span class="req">*</span></label>
        <input type="date" name="deadline" value="{{ old('deadline', date('Y-m-d')) }}" required class="{{ $errors->has('deadline') ? 'err' : '' }}">
        @error('deadline')<span class="field-error">{{ $message }}</span>@enderror
      </div>
    </div>

    {{-- Jam Mulai & Selesai --}}
    <div class="row-3">
      <div class="form-group">
        <label>🕐 Jam Mulai</label>
        <input type="time" name="jam_mulai" value="{{ old('jam_mulai') }}" class="{{ $errors->has('jam_mulai') ? 'err' : '' }}">
        @error('jam_mulai')<span class="field-error">{{ $message }}</span>@enderror
      </div>
      <div class="form-group">
        <label>🕐 Jam Selesai</label>
        <input type="time" name="jam_selesai" value="{{ old('jam_selesai') }}" class="{{ $errors->has('jam_selesai') ? 'err' : '' }}">
        @error('jam_selesai')<span class="field-error">{{ $message }}</span>@enderror
      </div>
      <div class="form-group">
        <label>Status</label>
        <select name="status">
          @foreach(['Belum','Sedang Dikerjakan','Selesai'] as $s)
            <option {{ old('status', 'Belum') === $s ? 'selected' : '' }}>{{ $s }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div style="display:flex;justify-content:flex-end">
      <button type="submit" class="btn btn-primary">➕ Tambah Tugas</button>
    </div>
  </form>
</div>

{{-- Search & Filter --}}
<div class="card page-section">
  <form method="GET" action="{{ route('tugas.index') }}" class="filter-row">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 Cari tugas..." class="search-input">
    <select name="status" class="filter-select">
      <option value="">Semua Status</option>
      @foreach(['Belum','Sedang Dikerjakan','Selesai'] as $s)
        <option {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
      @endforeach
    </select>
    <select name="kategori" class="filter-select">
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
            <span class="badge {{ $t->kategori === 'S2' ? 'badge-blue' : ($t->kategori === 'Pribadi' ? 'badge-purple' : 'badge-gray') }}">{{ $t->kategori }}</span>
          </td>
          <td>{{ $t->deadline->translatedFormat('d M Y') }}</td>
          <td>
            @if($t->jam_mulai || $t->jam_selesai)
              <div class="jam-badge">
                @if($t->jam_mulai)
                  <span class="jam-start">🕐 {{ \Carbon\Carbon::parse($t->jam_mulai)->format('H:i') }}</span>
                @endif
                @if($t->jam_mulai && $t->jam_selesai)
                  <span class="jam-arrow">→</span>
                @endif
                @if($t->jam_selesai)
                  <span class="jam-end">{{ \Carbon\Carbon::parse($t->jam_selesai)->format('H:i') }}</span>
                @endif
              </div>
            @else
              <span style="color:var(--g4);font-size:12px">—</span>
            @endif
          </td>
          <td>
            <span class="badge {{ $t->status === 'Selesai' ? 'badge-green' : ($t->status === 'Sedang Dikerjakan' ? 'badge-yellow' : 'badge-red') }}">{{ $t->status }}</span>
          </td>
          <td style="display:flex;gap:6px">
            <form method="POST" action="{{ route('tugas.status', $t) }}">
              @csrf @method('PATCH')
              <button class="btn btn-outline btn-xs">Update</button>
            </form>
            <button class="btn btn-danger btn-xs" onclick="confirmDelete('{{ route('tugas.destroy', $t) }}', 'Hapus tugas ini?')">Hapus</button>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6">
            <div class="empty"><div class="empty-icon">📋</div><p>Belum ada tugas</p></div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="margin-top:16px">{{ $tugas->links() }}</div>
</div>

@include('partials.confirm-modal')
@endsection
