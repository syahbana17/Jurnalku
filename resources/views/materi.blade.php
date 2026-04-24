@extends('layouts.app')
@section('title', 'Materi IPA')
@section('page-title', 'Arsip Materi IPA')
@section('page-subtitle', 'Biologi · Fisika · Kimia')

@section('content')

<div class="card page-section">
  <div class="card-title">➕ Tambah Materi Baru</div>
  <form method="POST" action="{{ route('materi.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="row-2">
      <div class="form-group">
        <label>Judul Materi <span class="req">*</span></label>
        <input type="text" name="judul" value="{{ old('judul') }}" placeholder="Contoh: Sistem Pencernaan Manusia" required class="{{ $errors->has('judul') ? 'err' : '' }}">
        @error('judul')<span class="field-error">{{ $message }}</span>@enderror
      </div>
      <div class="form-group">
        <label>Topik <span class="req">*</span></label>
        <select name="topik" required class="{{ $errors->has('topik') ? 'err' : '' }}">
          <option value="">-- Pilih Topik --</option>
          @foreach(['Biologi'=>'🌿 Biologi','Fisika'=>'⚡ Fisika','Kimia'=>'🧪 Kimia'] as $val => $label)
            <option value="{{ $val }}" {{ old('topik') === $val ? 'selected' : '' }}>{{ $label }}</option>
          @endforeach
        </select>
        @error('topik')<span class="field-error">{{ $message }}</span>@enderror
      </div>
    </div>

    <div class="form-group">
      <label>Kelas</label>
      <input type="text" name="kelas" value="{{ old('kelas') }}" placeholder="Contoh: 7A, 8B, 9C">
    </div>

    <div class="form-group">
      <label>Catatan / Ringkasan</label>
      <textarea name="catatan" placeholder="Tuliskan ringkasan atau catatan penting tentang materi ini...">{{ old('catatan') }}</textarea>
    </div>

    {{-- Media Section --}}
    <div class="media-section">
      <div class="media-section-title">🔗 Sumber Materi (opsional)</div>
      <div class="media-tabs" id="media-tabs">
        <button type="button" class="media-tab active" data-tab="link">🌐 Link URL</button>
        <button type="button" class="media-tab" data-tab="pdf">📄 Upload PDF</button>
      </div>

      <div id="tab-link" class="media-tab-content">
        <div class="form-group" style="margin-bottom:0">
          <label>Link YouTube / Canva / Google Drive / URL lainnya</label>
          <input type="url" name="link_url" value="{{ old('link_url') }}"
            placeholder="https://youtube.com/watch?v=... atau https://www.canva.com/..."
            id="link-url-input" class="{{ $errors->has('link_url') ? 'err' : '' }}">
          @error('link_url')<span class="field-error">{{ $message }}</span>@enderror
          <div class="link-preview" id="link-preview"></div>
        </div>
      </div>

      <div id="tab-pdf" class="media-tab-content" style="display:none">
        <div class="form-group" style="margin-bottom:0">
          <label>Upload File PDF <span style="color:var(--g5);font-weight:400">(maks. 10MB)</span></label>
          <div class="file-drop" id="file-drop">
            <input type="file" name="file_pdf" id="file-pdf" accept=".pdf" class="file-input">
            <div class="file-drop-inner">
              <span class="file-drop-icon">📄</span>
              <p>Klik atau drag & drop file PDF di sini</p>
              <span id="file-name" class="file-name"></span>
            </div>
          </div>
          @error('file_pdf')<span class="field-error">{{ $message }}</span>@enderror
        </div>
      </div>
    </div>

    <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px">
      <a href="{{ route('materi.index') }}" class="btn btn-outline">Reset</a>
      <button type="submit" class="btn btn-primary">💾 Simpan Materi</button>
    </div>
  </form>
</div>

{{-- Filter --}}
<div class="card page-section" style="padding:16px 24px">
  <div class="filter-bar" style="margin-bottom:0">
    @foreach(['semua'=>'Semua','Biologi'=>'🌿 Biologi','Fisika'=>'⚡ Fisika','Kimia'=>'🧪 Kimia'] as $val => $lbl)
      <a href="{{ route('materi.index', $val !== 'semua' ? ['topik'=>$val] : []) }}"
         class="f-btn {{ $val === 'Biologi' ? 'bio' : ($val === 'Kimia' ? 'kim' : '') }} {{ $topik === $val ? 'active' : '' }}">{{ $lbl }}</a>
    @endforeach
  </div>
</div>

{{-- Grid Materi --}}
@if($materis->isEmpty())
  <div class="card"><div class="empty"><div class="empty-icon">🔬</div><p>Belum ada materi tersimpan</p></div></div>
@else
  <div class="materi-grid">
    @foreach($materis as $m)
    @php $cls = ['Biologi'=>'mt-bio','Fisika'=>'mt-fis','Kimia'=>'mt-kim'][$m->topik] ?? ''; @endphp
    <div class="m-card">
      <div class="m-card-top">
        <div class="m-topic {{ $cls }}">{{ $m->topik }}</div>
        @if($m->link_url || $m->file_pdf)
          <span class="media-badge {{ $m->mediaBadgeClass() }}">{{ $m->mediaIcon() }} {{ $m->mediaLabel() }}</span>
        @endif
      </div>
      <h3>{{ $m->judul }}</h3>
      <p>{{ $m->catatan ?: 'Tidak ada catatan' }}</p>
      <div class="m-footer">
        <span>Kelas: {{ $m->kelas ?: '-' }} · {{ $m->created_at->translatedFormat('d M Y') }}</span>
        <div style="display:flex;gap:6px;align-items:center">
          @if($m->link_url || $m->file_pdf)
            <a href="{{ route('materi.show', $m) }}" class="btn btn-primary btn-xs">👁 Lihat</a>
          @endif
          <button class="btn btn-danger btn-xs" onclick="confirmDelete('{{ route('materi.destroy', $m) }}', 'Hapus materi ini?')">Hapus</button>
        </div>
      </div>
    </div>
    @endforeach
  </div>
@endif

@include('partials.confirm-modal')

<script>
// Tab switcher
document.querySelectorAll('.media-tab').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.media-tab').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.media-tab-content').forEach(c => c.style.display = 'none');
    btn.classList.add('active');
    document.getElementById('tab-' + btn.dataset.tab).style.display = 'block';
  });
});

// Link preview
const linkInput = document.getElementById('link-url-input');
const preview   = document.getElementById('link-preview');
linkInput.addEventListener('input', () => {
  const val = linkInput.value.trim();
  if (!val) { preview.innerHTML = ''; return; }
  let icon = '🔗', label = 'Link', cls = 'media-other';
  if (val.includes('youtube.com') || val.includes('youtu.be')) { icon='▶️'; label='YouTube'; cls='media-yt'; }
  else if (val.includes('canva.com'))  { icon='🎨'; label='Canva'; cls='media-canva'; }
  else if (val.includes('drive.google.com') || val.includes('docs.google.com')) { icon='📁'; label='Google Drive'; cls='media-drive'; }
  preview.innerHTML = `<span class="media-badge ${cls}" style="margin-top:8px;display:inline-flex">${icon} ${label} terdeteksi</span>`;
});

// File drop
const fileDrop = document.getElementById('file-drop');
const fileInput = document.getElementById('file-pdf');
const fileName  = document.getElementById('file-name');
fileInput.addEventListener('change', () => {
  fileName.textContent = fileInput.files[0]?.name || '';
  if (fileInput.files[0]) fileDrop.classList.add('has-file');
});
fileDrop.addEventListener('dragover', e => { e.preventDefault(); fileDrop.classList.add('drag-over'); });
fileDrop.addEventListener('dragleave', () => fileDrop.classList.remove('drag-over'));
fileDrop.addEventListener('drop', e => {
  e.preventDefault(); fileDrop.classList.remove('drag-over');
  const file = e.dataTransfer.files[0];
  if (file && file.type === 'application/pdf') {
    const dt = new DataTransfer(); dt.items.add(file);
    fileInput.files = dt.files;
    fileName.textContent = file.name;
    fileDrop.classList.add('has-file');
  }
});
</script>
@endsection
