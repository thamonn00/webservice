<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ระบบจัดการ') | แจ้งสัตว์หาย</title>
    <style>
        :root{font-family:Tahoma,"Noto Sans Thai",sans-serif;color:#1d2939;background:#f4f7f5;font-synthesis:none}
        *{box-sizing:border-box}body{margin:0}a{color:inherit;text-decoration:none}
        .topbar{background:#123b32;color:#fff;padding:16px 0}.topbar-inner,.container{width:min(1160px,calc(100% - 36px));margin:auto}
        .topbar-inner{display:flex;align-items:center;justify-content:space-between;gap:18px}.brand{font-size:18px;font-weight:700}
        .top-actions{display:flex;align-items:center;gap:16px}.top-actions form{margin:0}
        .container{padding:30px 0 56px}.eyebrow{color:#198754;font-size:13px;font-weight:700;letter-spacing:.04em}
        h1{font-size:28px;margin:8px 0 6px}h2{font-size:19px;margin:0 0 16px}p{line-height:1.65}
        .muted{color:#667085}.panel,.stat,.login-card{background:#fff;border:1px solid #e5eae7;border-radius:16px;box-shadow:0 8px 26px #163b3210}
        .panel{padding:22px;margin:20px 0}.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin:24px 0}
        .stat{padding:22px}.stat strong{display:block;font-size:30px;margin-top:8px;color:#123b32}
        .nav{display:flex;gap:8px;flex-wrap:wrap;margin:22px 0}.nav a,.button{display:inline-flex;align-items:center;justify-content:center;gap:7px;border:1px solid #d0d9d4;border-radius:9px;background:#fff;padding:9px 14px;font:inherit;cursor:pointer}
        .nav a.active,.button.primary{background:#13795b;color:#fff;border-color:#13795b}.button.danger{color:#b42318;border-color:#f2b8b5}
        .button.small{padding:6px 10px;font-size:13px}.button.link{background:transparent;border-color:transparent;color:#126c50}
        .grid{display:grid;grid-template-columns:1fr 1.25fr;gap:20px;align-items:start}
        .form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:13px}
        .field{display:flex;flex-direction:column;gap:6px}.field.full{grid-column:1/-1}label{font-size:13px;font-weight:700}
        input,select,textarea{width:100%;border:1px solid #d0d5dd;border-radius:8px;padding:10px 11px;font:inherit;background:#fff;color:#1d2939}
        textarea{min-height:86px;resize:vertical}input:focus,select:focus,textarea:focus{outline:3px solid #13795b20;border-color:#13795b}
        .help{font-size:12px;color:#667085}.alert{padding:12px 14px;border-radius:9px;margin:12px 0}
        .alert.success{background:#e8f7ef;color:#146c43}.alert.error{background:#fff0ef;color:#a1261b}
        .record{display:flex;justify-content:space-between;gap:14px;border-top:1px solid #eaeeeb;padding:16px 0}
        .record:first-of-type{border-top:0}.record-main{min-width:0}.record-title{font-weight:700}.record-meta{font-size:13px;color:#667085;margin-top:5px}
        .record-actions{display:flex;align-items:center;gap:6px;flex-shrink:0}.record-actions form{margin:0}
        .pill{display:inline-block;border-radius:999px;background:#eaf4ef;color:#146c43;padding:4px 9px;font-size:12px}
        .photos{display:flex;gap:8px;flex-wrap:wrap;margin-top:10px}.photo{width:64px;height:64px;object-fit:cover;border-radius:9px;background:#eef2ef}
        .check-photo{display:inline-flex;align-items:center;gap:4px;font-size:11px}.check-photo input{width:auto}
        .pagination{margin-top:16px}.pagination nav{display:flex;justify-content:space-between;gap:12px}
        .login-wrap{min-height:calc(100vh - 58px);display:grid;place-items:center;padding:28px}
        .login-card{width:min(440px,100%);padding:30px}.login-card .button{width:100%;margin-top:14px}
        .empty{padding:28px;text-align:center;color:#667085}.footer{color:#98a2b3;text-align:center;font-size:12px;margin-top:30px}
        @media(max-width:850px){.grid{grid-template-columns:1fr}.stats{grid-template-columns:1fr 1fr}}
        @media(max-width:560px){.container,.topbar-inner{width:min(100% - 24px,1160px)}.stats{grid-template-columns:1fr 1fr}.panel{padding:16px}.form-grid{grid-template-columns:1fr}.field.full{grid-column:auto}.record{flex-direction:column}.record-actions{justify-content:flex-start}.top-actions{gap:8px}.brand{font-size:15px}}
    </style>
</head>
<body>
    <header class="topbar">
        <div class="topbar-inner">
            <a class="brand" href="{{ auth()->check() ? route('admin.dashboard') : route('home') }}">🐾 แจ้งสัตว์หาย <span style="font-weight:400;opacity:.75">/ ระบบแอดมิน</span></a>
            @auth
                <div class="top-actions">
                    <span>{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button class="button small" type="submit">ออกจากระบบ</button>
                    </form>
                </div>
            @endauth
        </div>
    </header>
    @yield('content')
</body>
</html>
