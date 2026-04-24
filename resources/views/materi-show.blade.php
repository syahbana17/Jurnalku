@extends('layouts.app')
@section('title', $materi->judul)
@section('page-title', $materi->judul)
@section('page-subtitle', $materi->topik . ($materi->kelas ? ' · Kelas ' . $materi->kelas : ''))

@section('content')

<div style="margin-bottom:16px">
  <a href="{{ route('materi.index') }}" class="btn btn-outline btn-sm">← Kembali ke Materi</a>
</div>

<div class="grid-2" style="align-items:start">

  {{-- Info Card --}}
  <div class="card">
    @php $cls = ['Biologi'=>'mt-bio','Fisika'=>'mt-fis','Kimia'=>'mt-kim'][$materi->topik] ?? ''; @endphp
    <div class="m-topic {{ $cls }}" style="margin-bottom:16px">{{ $materi->topik }}</div>
    <h2 style="font-size:20px;font-weight:800;color:var(--g9);margin-bottom:8px">{{ $materi->judul }}</h2>
    <p style="font-size:13px;color:var(--g5);margin-bottom:20px">
      Kelas: {{ $materi->kelas ?: '-' }} &nbsp;·&nbsp; {{ $materi->created_at->translatedFormat('d F Y') }}
    </p>

    @if($materi->catatan)
    <div class="catatan-box">
      <div class="catatan-label">📝 Catatan</div>
      <p>{{ $materi->catatan }}</p>
    </div>
    @endif

    {{-- Media Info --}}
    @if($materi->link_url || $materi->file_pdf)
    <div style="margin-top:20px">
      <div class="catatan-label">🔗 Sumber Materi</div>
      <div style="margin-top:10px;display:flex;flex-direction:column;gap:8px">

        @if($materi->link_url)
        <a href="{{ $materi->link_url }}" target="_blank" rel="noopener" class="media-link-btn {{ $materi->mediaBadgeClass() }}">
          <span style="font-size:20px">{{ $materi->mediaIcon() }}</span>
          <div>
            <p>Buka di {{ $materi->mediaLabel() }}</p>
            <span>{{ Str::limit($materi->link_url, 50) }}</span>
          </div>
          <span style="margin-left:auto;font-size:18px">↗</span>
        </a>
        @endif

        @if($materi->file_pdf)
        <a href="{{ asset('storage/' . $materi->file_pdf) }}" target="_blank" class="media-link-btn media-pdf">
          <span style="font-size:20px">📄</span>
          <div>
            <p>Buka File PDF</p>
            <span>{{ basename($materi->file_pdf) }}</span>
          </div>
          <span style="margin-left:auto;font-size:18px">↗</span>
        </a>
        @endif

      </div>
    </div>
    @endif
  </div>

  {{-- Preview Panel --}}
  <div class="card" style="padding:0;overflow:hidden">
    @if($materi->file_pdf)
      {{-- PDF Embed --}}
      <div class="preview-header">📄 Preview PDF</div>
      <iframe src="{{ asset('storage/' . $materi->file_pdf) }}"
        style="width:100%;height:600px;border:none;display:block"></iframe>

    @elseif($materi->link_type === 'youtube' && $materi->link_url)
      {{-- YouTube Embed --}}
      @php
        preg_match('/(?:v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $materi->link_url, $m);
        $ytId = $m[1] ?? null;
      @endphp
      @if($ytId)
        <div class="preview-header">▶️ Preview YouTube</div>
        <div style="position:relative;padding-bottom:56.25%;height:0">
          <iframe src="https://www.youtube.com/embed/{{ $ytId }}"
            style="position:absolute;top:0;left:0;width:100%;height:100%;border:none"
            allowfullscreen></iframe>
        </div>
      @endif

    @elseif($materi->link_type === 'canva' && $materi->link_url)
      {{-- Canva Embed --}}
      <div class="preview-header">🎨 Preview Canva</div>
      <div style="position:relative;padding-bottom:56.25%;height:0">
        <iframe src="{{ str_replace('/view', '/embed', $materi->link_url) }}"
          style="position:absolute;top:0;left:0;width:100%;height:100%;border:none"
          allowfullscreen></iframe>
      </div>

    @elseif($materi->link_type === 'drive' && $materi->link_url)
      {{-- Google Drive Embed --}}
      @php
        preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $materi->link_url, $m);
        $driveId = $m[1] ?? null;
      @endphp
      @if($driveId)
        <div class="preview-header">📁 Preview Google Drive</div>
        <iframe src="https://drive.google.com/file/d/{{ $driveId }}/preview"
          style="width:100%;height:600px;border:none;display:block"
          allowfullscreen></iframe>
      @endif

    @else
      <div class="preview-empty">
        <span style="font-size:48px">🔗</span>
        <p>Klik tombol di samping untuk membuka materi</p>
        @if($materi->link_url)
          <a href="{{ $materi->link_url }}" target="_blank" class="btn btn-primary" style="margin-top:16px">Buka Link →</a>
        @endif
      </div>
    @endif
  </div>

</div>
@endsection
