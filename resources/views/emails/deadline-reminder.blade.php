<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reminder Deadline</title>
  <style>
    body { font-family: 'Segoe UI', sans-serif; background: #f8fafc; margin: 0; padding: 20px; color: #1e293b; }
    .container { max-width: 560px; margin: 0 auto; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.08); }
    .header { background: linear-gradient(135deg, #3b82f6, #8b5cf6); padding: 32px 32px 24px; text-align: center; }
    .header h1 { color: #fff; font-size: 22px; margin: 0 0 6px; }
    .header p  { color: rgba(255,255,255,.8); font-size: 14px; margin: 0; }
    .logo { font-size: 40px; margin-bottom: 12px; }
    .body { padding: 28px 32px; }
    .greeting { font-size: 15px; color: #475569; margin-bottom: 20px; }
    .greeting strong { color: #1e293b; }
    .alert-box { background: #fef3c7; border: 1.5px solid #fcd34d; border-radius: 10px; padding: 14px 16px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
    .alert-box span { font-size: 13.5px; color: #92400e; font-weight: 600; }
    .tugas-list { list-style: none; padding: 0; margin: 0 0 24px; }
    .tugas-item { display: flex; align-items: flex-start; gap: 12px; padding: 14px 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; margin-bottom: 10px; }
    .tugas-item:last-child { margin-bottom: 0; }
    .tugas-icon { font-size: 20px; flex-shrink: 0; margin-top: 2px; }
    .tugas-nama { font-size: 14px; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
    .tugas-meta { font-size: 12px; color: #64748b; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 700; margin-left: 6px; }
    .badge-sekolah { background: #dbeafe; color: #1d4ed8; }
    .badge-s2      { background: #d1fae5; color: #065f46; }
    .badge-pribadi { background: #f3e8ff; color: #6b21a8; }
    .deadline-tag { background: #fee2e2; color: #dc2626; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .cta { text-align: center; margin: 24px 0 8px; }
    .cta a { background: #2563eb; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 10px; font-size: 14px; font-weight: 700; display: inline-block; }
    .footer { background: #f1f5f9; padding: 18px 32px; text-align: center; font-size: 12px; color: #94a3b8; }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <div class="logo">📚</div>
      <h1>Jurnalku — Reminder Deadline</h1>
      <p>Jangan sampai terlewat!</p>
    </div>

    <div class="body">
      <p class="greeting">Halo, <strong>{{ $userName }}</strong> 👋</p>

      <div class="alert-box">
        ⚠️ <span>Kamu punya {{ count($tugas) }} tugas yang deadline-nya <strong>besok</strong>. Segera selesaikan!</span>
      </div>

      <ul class="tugas-list">
        @foreach($tugas as $t)
        @php
          $badgeClass = match($t['kategori']) {
            'Sekolah' => 'badge-sekolah',
            'S2'      => 'badge-s2',
            default   => 'badge-pribadi',
          };
        @endphp
        <li class="tugas-item">
          <div class="tugas-icon">
            {{ $t['status'] === 'Sedang Dikerjakan' ? '🔄' : '📋' }}
          </div>
          <div>
            <div class="tugas-nama">
              {{ $t['nama'] }}
              <span class="badge {{ $badgeClass }}">{{ $t['kategori'] }}</span>
            </div>
            <div class="tugas-meta">
              <span class="deadline-tag">⏰ Deadline: {{ $t['deadline'] }}</span>
              &nbsp; Status: {{ $t['status'] }}
            </div>
          </div>
        </li>
        @endforeach
      </ul>

      <div class="cta">
        <a href="{{ config('app.url') }}/tugas">Lihat Semua Tugas →</a>
      </div>
    </div>

    <div class="footer">
      Email ini dikirim otomatis oleh <strong>Jurnalku</strong>.<br>
      {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
    </div>
  </div>
</body>
</html>
