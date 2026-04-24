<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login — Jurnalku</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <style>
    body {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
    }
    .login-wrap {
      width: 100%;
      max-width: 420px;
      padding: 20px;
    }
    .login-card {
      background: var(--g0);
      border-radius: 24px;
      padding: 48px 40px;
      box-shadow: 0 32px 64px rgba(0,0,0,.3);
      text-align: center;
    }
    .login-logo {
      width: 64px; height: 64px;
      background: linear-gradient(135deg, #3b82f6, #8b5cf6);
      border-radius: 18px;
      display: flex; align-items: center; justify-content: center;
      font-size: 30px;
      margin: 0 auto 20px;
    }
    .login-title {
      font-size: 26px; font-weight: 800;
      color: var(--g9); letter-spacing: -.03em;
      margin-bottom: 6px;
    }
    .login-sub {
      font-size: 14px; color: var(--g5);
      margin-bottom: 36px;
    }
    .btn-google {
      display: flex; align-items: center; justify-content: center; gap: 12px;
      width: 100%; padding: 14px 20px;
      background: #fff; border: 2px solid #e2e8f0;
      border-radius: 12px; cursor: pointer;
      font-size: 15px; font-weight: 700;
      color: #1e293b; text-decoration: none;
      transition: all .2s; box-shadow: 0 2px 8px rgba(0,0,0,.06);
    }
    .btn-google:hover {
      border-color: #3b82f6;
      box-shadow: 0 4px 16px rgba(59,130,246,.2);
      transform: translateY(-1px);
    }
    .btn-google svg { flex-shrink: 0; }
    .login-divider {
      margin: 28px 0;
      display: flex; align-items: center; gap: 12px;
      font-size: 12px; color: var(--g5); font-weight: 600;
    }
    .login-divider::before, .login-divider::after {
      content: ''; flex: 1; height: 1px; background: var(--g3);
    }
    .login-note {
      font-size: 12px; color: var(--g5); line-height: 1.6;
    }
    .alert-error {
      background: #fef2f2; color: #991b1b;
      border: 1px solid #fca5a5;
      padding: 12px 16px; border-radius: 10px;
      font-size: 13px; font-weight: 600;
      margin-bottom: 20px;
    }
  </style>
</head>
<body>
  <div class="login-wrap">
    <div class="login-card">
      <div class="login-logo">📚</div>
      <h1 class="login-title">Jurnalku</h1>
      <p class="login-sub">Catat, kelola, dan refleksikan perjalananmu</p>

      @if(session('error'))
        <div class="alert-error">{{ session('error') }}</div>
      @endif

      <a href="{{ route('auth.google') }}" class="btn-google">
        <svg width="20" height="20" viewBox="0 0 48 48">
          <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
          <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
          <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
          <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
          <path fill="none" d="M0 0h48v48H0z"/>
        </svg>
        Masuk dengan Google
      </a>

      <div class="login-divider">atau</div>

      <p class="login-note">
        Dengan masuk, kamu menyetujui penggunaan data Google kamu untuk autentikasi di aplikasi ini.
      </p>
    </div>
  </div>
</body>
</html>
