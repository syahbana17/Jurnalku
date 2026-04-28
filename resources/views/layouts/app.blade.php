<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>@yield('title', 'Dashboard') — Jurnalku</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <style>
    /* Fallback jika asset tidak load */
    body { font-family: 'Plus Jakarta Sans', sans-serif; }
  </style>
</head>
<body>

@php
  $deadlineWarning = \App\Models\Tugas::where('user_id', auth()->id())
    ->where('status', '!=', 'Selesai')
    ->whereDate('deadline', '<=', now()->addDays(3))
    ->count();
@endphp

<aside class="sidebar" id="sidebar">
  <div class="sb-brand">
    <div class="sb-logo-icon">📚</div>
    <div>
      <h2>Jurnalku</h2>
      <p>Kelola jurnal & tugasmu</p>
    </div>
  </div>
  <nav class="sb-nav">
    <a href="{{ route('dashboard') }}" class="sb-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <span class="sb-icon">🏠</span> Dashboard
    </a>
    <div class="sb-section-label">MENU</div>
    <a href="{{ route('jurnal.index') }}" class="sb-item {{ request()->routeIs('jurnal.*') ? 'active' : '' }}">
      <span class="sb-icon">📝</span> Jurnal Harian
    </a>
    <a href="{{ route('tugas.index') }}" class="sb-item {{ request()->routeIs('tugas.*') ? 'active' : '' }}">
      <span class="sb-icon">✅</span> Manajemen Tugas
      @if($deadlineWarning > 0)<span class="sb-badge">{{ $deadlineWarning }}</span>@endif
    </a>
    <a href="{{ route('materi.index') }}" class="sb-item {{ request()->routeIs('materi.*') ? 'active' : '' }}">
      <span class="sb-icon">🔬</span> Materi IPA
    </a>
    <a href="{{ route('refleksi.index') }}" class="sb-item {{ request()->routeIs('refleksi.*') ? 'active' : '' }}">
      <span class="sb-icon">🌟</span> Refleksi Mingguan
    </a>
  </nav>
  <div class="sb-footer">
    <div class="sb-user-info-bar">
      @if(auth()->user()->avatar)
        <img src="{{ auth()->user()->avatar }}" class="sb-avatar-img" alt="">
      @else
        <div class="sb-avatar">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
      @endif
      <div class="sb-user-text">
        <p>{{ auth()->user()->name }}</p>
        <span>{{ auth()->user()->email }}</span>
      </div>
    </div>
    <button class="theme-toggle" id="theme-toggle">
      <span class="theme-icon">🌙</span>
      <span class="theme-label">Mode Gelap</span>
      <span class="theme-switch" id="theme-switch"></span>
    </button>

    {{-- Nomor WhatsApp untuk reminder --}}
    <form method="POST" action="{{ route('profil.whatsapp') }}" class="wa-form">
      @csrf
      <div class="wa-input-wrap">
        <span class="wa-icon">📱</span>
        <input type="text" name="whatsapp"
          value="{{ auth()->user()->whatsapp }}"
          placeholder="Nomor WA (cth: 628xxx)"
          class="wa-input">
        <button type="submit" class="wa-save-btn" title="Simpan">✓</button>
      </div>
      @if(auth()->user()->whatsapp)
        <span class="wa-status">✅ Reminder aktif</span>
      @else
        <span class="wa-status wa-status-off">⚠️ Isi untuk aktifkan reminder</span>
      @endif
    </form>

    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="btn-logout">🚪 Keluar</button>
    </form>
  </div>
</aside>

<div class="overlay" id="overlay"></div>

<div class="main-wrap">
  <div class="topbar">
    <div class="topbar-left">
      <button class="hamburger" id="hamburger">☰</button>
      <div class="topbar-title">
        <h1>@yield('page-title', 'Dashboard')</h1>
        <p>@yield('page-subtitle', '')</p>
      </div>
    </div>
    <div class="topbar-right">
      <span id="topbar-clock" class="topbar-clock"></span>
      <button class="theme-toggle-top" id="theme-toggle-top" title="Toggle tema">🌙</button>
      @if(auth()->user()->avatar)
        <img src="{{ auth()->user()->avatar }}" class="topbar-avatar" alt="">
      @else
        <div class="topbar-avatar-text">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
      @endif
    </div>
  </div>

  <div class="content">
    @if(session('success'))
      <div class="alert alert-success" id="flash-alert">✅ {{ session('success') }}</div>
    @endif
    @if($errors->any())
      <div class="alert alert-error">
        <ul style="margin:0;padding-left:16px">
          @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
      </div>
    @endif
    @yield('content')
  </div>
</div>

<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
