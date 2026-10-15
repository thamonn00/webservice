<!doctype html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Lost Pet') - ระบบแจ้งสัตว์หาย/พบสัตว์</title>
    <style>
        :root {
            --bg: #f5f7f3;
            --card: #ffffff;
            --ink: #193c34;
            --mute: #62766e;
            --line: #dce6df;
            --pri: #246650;
            --pri2: #174b3b;
            --soft: #eaf2ec;
            --ok: #246650;
            --bad: #a43f44;
            --shadow: 0 8px 30px rgba(25, 60, 52, .05);
        }
        * { box-sizing: border-box; }
        html { color-scheme: light; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: "Sarabun", "Noto Sans Thai", Tahoma, system-ui, sans-serif;
            background: radial-gradient(ellipse at 0 0, #e7f0e8 0, transparent 48%), var(--bg);
            color: var(--ink);
            line-height: 1.7;
            -webkit-font-smoothing: antialiased;
        }
        a { color: var(--pri); text-underline-offset: 4px; }
        button, input, select, textarea { font: inherit; }
        button, a, input, select, textarea, summary { -webkit-tap-highlight-color: transparent; }
        :focus-visible { outline: 3px solid #88b69b; outline-offset: 4px; }
        ::selection { background: #c9e2d1; color: #173d2f; }
        h1, h2, h3 { color: var(--ink); line-height: 1.4; overflow-wrap: anywhere; }
        h1 { letter-spacing: -.035em; }
        .topbar {
            background: rgba(255, 255, 255, .95);
            border-bottom: 1px solid var(--line);
            position: sticky;
            top: 0;
            z-index: 20;
            backdrop-filter: blur(14px);
        }
        .topbar .in {
            max-width: 1200px;
            min-height: 86px;
            margin: auto;
            padding: 16px 28px;
            display: flex;
            align-items: center;
            gap: 28px;
            flex-wrap: wrap;
        }
        .brand { display: flex; align-items: center; gap: 12px; color: var(--ink); text-decoration: none; font-weight: 800; font-size: 1.2rem; line-height: 1.35; }
        .brand .mark { width: 46px; height: 46px; border-radius: 15px; background: var(--soft); display: grid; place-items: center; font-size: 1.45rem; }
        .brand small { display: block; margin-top: 5px; font-weight: 400; font-size: .72rem; color: var(--mute); }
        .nav { display: flex; align-items: center; gap: 5px; flex: 1; flex-wrap: wrap; }
        .nav a { color: var(--mute); text-decoration: none; padding: 9px 16px; border-radius: 10px; font-size: .9rem; transition: background .18s, color .18s; }
        .nav a:hover { background: var(--soft); color: var(--pri); }
        .nav a.on { background: var(--pri); color: white; font-weight: 600; }
        .userbox { display: flex; align-items: center; gap: 12px; font-size: .8rem; min-width: 0; }
        .userbox .pill { max-width: 270px; overflow-wrap: anywhere; color: var(--mute); }
        .userbox form { margin: 0; flex-shrink: 0; }
        .userbox button { background: var(--soft); border: 1px solid var(--line); color: var(--pri); border-radius: 9px; padding: 8px 12px; cursor: pointer; }
        .userbox button:hover { background: #dbeade; }
        .guest-nav { flex: 1; justify-content: flex-end; }
        .wrap { width: 100%; max-width: 1200px; margin: 0 auto; padding: 36px 28px 64px; flex: 1; min-width: 0; }
        .crumb { font-size: .82rem; color: var(--mute); margin: 0 0 20px; }
        .crumb a { text-decoration: none; }
        .card, .pcard { background: var(--card); border: 1px solid var(--line); border-radius: 20px; overflow: hidden; box-shadow: var(--shadow); }
        .card .hd { padding: 20px 24px; border-bottom: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; }
        .card .hd h2 { margin: 0; font-size: 1.1rem; }
        .card .bd { padding: 28px; }
        .btn { background: var(--pri); color: white; border: 1px solid transparent; border-radius: 10px; padding: 10px 19px; cursor: pointer; text-decoration: none; display: inline-flex; justify-content: center; align-items: center; gap: 8px; font-size: .9rem; font-weight: 600; line-height: 1.6; transition: background .18s, border-color .18s, box-shadow .18s; }
        .btn:hover { background: var(--pri2); box-shadow: 0 4px 12px #193c3414; }
        .btn.ghost { background: var(--card); color: var(--pri); border-color: var(--line); }
        .btn.ghost:hover { background: var(--soft); border-color: #aac9b5; }
        .btn.danger, .acts .btn.danger { background: #fff5f4; color: var(--bad); border-color: #ecd2d1; }
        .btn.danger:hover { background: #f7e7e5; }
        .btn.ok { background: var(--pri); }
        .btn.sm { padding: 6px 12px; font-size: .78rem; }
        .btn.block { width: 100%; min-height: 48px; }
        .page-head, .title-row, .toolbar, .foot, .user, .acts { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .page-head { justify-content: space-between; margin: 0 0 26px; gap: 20px; }
        .page-head h1, .title-row h1 { margin: 0; font-size: clamp(1.65rem, 3vw, 2.15rem); font-weight: 800; }
        .page-head p, .sub { margin: 8px 0 0; color: var(--mute); font-size: .9rem; }
        .meta { color: var(--mute); font-size: .82rem; overflow-wrap: anywhere; }
        .user .meta { display: block; }
        .filters, .tools { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; margin: 0 0 28px; padding: 18px; background: var(--card); border: 1px solid var(--line); border-radius: 16px; }
        input, select, textarea { padding: 11px 14px; border: 1px solid var(--line); border-radius: 10px; background: #fbfcfa; color: var(--ink); width: 100%; min-width: 0; transition: border-color .18s, box-shadow .18s; }
        input::placeholder, textarea::placeholder { color: #7e8d84; }
        input:focus, select:focus, textarea:focus { outline: none; border-color: #7aa78c; box-shadow: 0 0 0 3px #24665012; background: white; }
        input:disabled, select:disabled { opacity: .7; cursor: not-allowed; }
        input[type=checkbox], input[type=radio] { accent-color: var(--pri); width: 17px; height: 17px; flex-shrink: 0; }
        .filters .search { flex: 1; min-width: 200px; }
        .filters select { width: auto; min-width: 140px; }
        .tools input, .tools select { flex: 1; width: auto; min-width: 140px; }
        .check { display: inline-flex; align-items: center; gap: 8px; white-space: nowrap; font-size: .87rem; color: var(--mute); }
        .check input { width: 17px; margin: 0; }
        .grid, .pet-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 24px; }
        .pet-card { color: inherit; text-decoration: none; transition: transform .2s, box-shadow .2s, border-color .2s; }
        .pet-card:hover { transform: translateY(-4px); box-shadow: 0 14px 30px #193c3412; border-color: #afcbb7; }
        .pet-card .pic, .gallery .main { background: var(--soft); overflow: hidden; }
        .pet-card .pic { height: 220px; }
        .pet-card .pic img, .gallery .main img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .pet-card .info { padding: 20px; }
        .pet-card h3 { margin: 0 0 12px; font-size: 1.16rem; }
        .pet-card h3 small { float: right; font-size: .76rem; color: var(--mute); font-weight: 400; padding-top: 5px; }
        .chip { display: inline-block; padding: 3px 11px; border: 1px solid var(--line); border-radius: 99px; color: var(--mute); font-size: .74rem; background: #f8faf7; }
        .chip.pri, .chip.admin { background: var(--soft); border-color: #c9ddcf; color: var(--pri); }
        .chip.admin { font-weight: 700; }
        .line { margin: 14px 0; color: var(--mute); font-size: .85rem; }
        .foot { padding-top: 14px; border-top: 1px solid var(--line); }
        .foot, .user { color: var(--mute); font-size: .84rem; }
        .avatar { display: inline-grid; place-items: center; flex-shrink: 0; width: 34px; height: 34px; border-radius: 12px; background: var(--soft); color: var(--pri); font-weight: 700; }
        .pcard { display: flex; flex-direction: column; }
        .pcard img { width: 100%; height: 190px; object-fit: cover; background: var(--soft); }
        .pcard .b { padding: 18px; flex: 1; }
        .pcard h3 { margin: 0 0 8px; font-size: 1.05rem; }
        .acts { justify-content: flex-end; gap: 8px; }
        .acts form { margin: 0; }
        .pcard .acts { padding: 0 18px 18px; }
        .detail { display: grid; grid-template-columns: minmax(0, 1.1fr) minmax(0, 1fr); gap: 36px; align-items: start; padding: 28px; background: var(--card); border: 1px solid var(--line); border-radius: 24px; box-shadow: var(--shadow); }
        .detail > * { min-width: 0; }
        .gallery .main { height: 390px; border-radius: 16px; }
        .detailimg { width: 100%; max-height: 390px; object-fit: cover; border-radius: 16px; }
        .thumbs { display: flex; gap: 10px; margin-top: 12px; overflow-x: auto; padding: 3px; }
        .thumbs img { width: 68px; height: 68px; object-fit: cover; border: 3px solid transparent; border-radius: 12px; cursor: pointer; }
        .thumbs img.on { border-color: var(--pri); }
        .title-row { margin: 0 0 12px; }
        .kv { display: grid; grid-template-columns: 100px minmax(0, 1fr); gap: 14px 18px; margin: 24px 0 0; padding-top: 22px; border-top: 1px solid var(--line); font-size: .92rem; }
        .kv dt { color: var(--mute); }
        .kv dd { margin: 0; overflow-wrap: anywhere; }
        .toolbar { margin: 14px 0; }
        .api { margin-top: 24px; padding: 22px; background: var(--card); border: 1px solid var(--line); border-radius: 18px; min-width: 0; }
        .api summary { cursor: pointer; font-weight: 700; color: var(--pri); }
        .api code { overflow-wrap: anywhere; }
        .hero { background: #eaf2e9; border: 1px solid #cedfcf; border-radius: 22px; padding: 32px; margin-bottom: 28px; }
        .hero h1 { margin: 0 0 12px; font-size: clamp(1.6rem, 3vw, 2rem); }
        .hero p { margin: 0 0 18px; color: var(--mute); }
        .baseurl { display: inline-block; background: #193c34; color: #e3f0e7; border-radius: 9px; padding: 9px 14px; overflow-wrap: anywhere; max-width: 100%; }
        .flow { display: flex; align-items: center; justify-content: center; gap: 12px; flex-wrap: wrap; background: #ffffffa6; border: 1px solid #cedfcf; border-radius: 12px; padding: 16px; margin: 20px 0 0; font-size: .85rem; }
        .flow span { font-weight: 600; }
        .flow i { font-style: normal; color: var(--pri); }
        .step { position: relative; min-width: 0; background: var(--card); border: 1px solid var(--line); border-radius: 20px; padding: 28px 28px 28px 88px; margin: 22px 0; box-shadow: var(--shadow); }
        .step-no { position: absolute; left: 24px; top: 26px; width: 42px; height: 42px; border-radius: 13px; background: var(--soft); color: var(--pri); display: grid; place-items: center; font-weight: 800; font-size: 1.15rem; border: 1px solid #d1e2d6; }
        .step h2 { margin: 0 0 16px; font-size: 1.2rem; }
        .step h3 { margin: 24px 0 12px; font-size: 1rem; }
        .step p, .cond { overflow-wrap: anywhere; }
        .cond, .readonly-note { background: #f0f5ef; border: 1px solid #d9e5d9; border-left: 3px solid #79a189; border-radius: 10px; padding: 14px 17px; margin: 16px 0; color: #42634e; font-size: .87rem; }
        .badge-lock, .badge-free { display: inline-block; padding: 3px 10px; border-radius: 99px; font-size: .72rem; font-weight: 600; white-space: normal; background: var(--soft); color: var(--pri); border: 1px solid #ccdfd2; }
        .badge-lock { background: #f2f4ed; color: #5a6950; border-color: #dce2d2; }
        .m-GET, .m-POST, .m-PUT, .m-DELETE, .m.GET { display: inline-block; min-width: 66px; text-align: center; padding: 4px 9px; border-radius: 7px; color: var(--pri); background: #e5f0e8; border: 1px solid #cbded1; font-family: Consolas, monospace; font-size: .73rem; font-weight: 700; }
        .m-POST { color: white; background: #34775c; border-color: #34775c; }
        .m-PUT { color: #566540; background: #eff1e4; border-color: #dde1ca; }
        .m-DELETE { color: #99484b; background: #faeeee; border-color: #ead5d4; }
        .table-wrap { width: 100%; overflow-x: auto; border-radius: 12px; }
        table.tbl { border-collapse: collapse; width: 100%; background: var(--card); font-size: .86rem; }
        table.tbl td, table.tbl th { border: 0; border-bottom: 1px solid var(--line); padding: 16px; text-align: left; vertical-align: middle; }
        table.tbl th { background: #edf3ed; color: #486450; font-weight: 600; white-space: nowrap; font-size: .78rem; }
        table.tbl tbody tr:last-child td { border-bottom: 0; }
        table.tbl tbody tr:hover { background: #f8faf6; }
        .api-table { min-width: 640px; margin: 0; }
        .api-table td { overflow-wrap: anywhere; }
        .api-table td:first-child { white-space: nowrap; }
        .step .table-wrap { margin: 16px 0 20px; border: 1px solid var(--line); }
        img.thumb { width: 52px; height: 52px; object-fit: cover; border-radius: 10px; background: var(--soft); display: block; }
        pre.json, pre.code { background: #19372f; color: #deeee1; border: 1px solid #274a3e; border-radius: 13px; padding: 20px; overflow: auto; max-height: 420px; max-width: 100%; font-family: Consolas, "Courier New", monospace; font-size: .8rem; line-height: 1.8; tab-size: 4; }
        code { font-family: Consolas, "Courier New", monospace; font-size: .88em; }
        .code-title { display: flex; justify-content: space-between; align-items: center; gap: 10px; margin: 20px 0 8px; flex-wrap: wrap; }
        .code-title strong { font-size: .88rem; }
        .step pre.json { margin: 8px 0 16px; }
        .form { display: grid; gap: 19px; }
        .field { display: grid; gap: 8px; font-weight: 600; font-size: .88rem; }
        .field input, .field select { font-weight: 400; }
        .form .toolbar { margin-top: 10px; }
        .auth, .authwrap { max-width: 460px; margin: 22px auto 36px; }
        .auth .card { border-radius: 24px; border-top: 4px solid var(--pri); box-shadow: 0 18px 55px #193c340c; }
        .auth .bd { padding: 36px; }
        .auth .logo { width: 64px; height: 64px; display: grid; place-items: center; border-radius: 20px; background: var(--soft); font-size: 1.8rem; margin: 0 auto 20px; }
        .auth h1 { margin: 0; text-align: center; font-size: 1.8rem; }
        .auth .sub, .auth .alt { text-align: center; margin: 10px 0 28px; color: var(--mute); font-size: .86rem; }
        .auth .alt { margin: 26px 0 0; padding-top: 22px; border-top: 1px solid var(--line); }
        .auth .alt a { font-weight: 600; }
        .alert, .err, .okmsg { border-radius: 12px; padding: 14px 17px; margin-bottom: 20px; font-size: .87rem; white-space: pre-line; }
        .alert.err, .err { background: #fcf0ee; color: #963f44; border: 1px solid #eed5d2; }
        .okmsg { background: var(--soft); color: var(--pri); border: 1px solid #ccdfd2; }
        .empty { padding: 56px 24px; text-align: center; color: var(--mute); }
        .empty .big { font-size: 2.5rem; margin-bottom: 12px; }
        .tag { display: inline-block; font-size: .74rem; padding: 3px 10px; border-radius: 99px; margin-right: 4px; background: var(--soft); color: var(--pri); }
        .tag.lost { background: #fcf0ee; color: var(--bad); }
        .tag.closed { background: #ecf0ea; color: var(--mute); }
        .pager, .pagination { display: flex; gap: 8px; justify-content: center; align-items: center; flex-wrap: wrap; padding: 20px; list-style: none; }
        .pagination a, .pagination span { display: block; padding: 6px 12px; border: 1px solid var(--line); border-radius: 8px; background: white; color: var(--ink); text-decoration: none; }
        .pagination .active span { background: var(--pri); border-color: var(--pri); color: white; }
        nav[role="navigation"] { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 16px; padding: 20px; font-size: .83rem; border-top: 1px solid var(--line); }
        nav[role="navigation"] > div:first-child { display: none; }
        nav[role="navigation"] > div:last-child { width: 100%; display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; }
        nav[role="navigation"] p { margin: 0; color: var(--mute); }
        nav[role="navigation"] svg { width: 18px; height: 18px; vertical-align: middle; }
        nav[role="navigation"] a, nav[role="navigation"] span[aria-current] > span, nav[role="navigation"] span[aria-disabled] > span { display: inline-flex; align-items: center; justify-content: center; min-width: 34px; min-height: 36px; padding: 5px 10px; margin: 2px; border: 1px solid var(--line); border-radius: 8px; text-decoration: none; }
        nav[role="navigation"] span[aria-current] > span { background: var(--pri); color: white; border-color: var(--pri); }
        nav[role="navigation"] span[aria-disabled] > span { color: #8c9a8f; }
        .tabs { display: flex; gap: 8px; margin: 14px 0; }
        .tabs button { border: 1px solid var(--line); background: white; color: var(--pri); border-radius: 10px; padding: 9px 18px; cursor: pointer; }
        .tabs button.on { background: var(--pri); color: white; }
        .footer { text-align: center; color: var(--mute); font-size: .75rem; padding: 24px; border-top: 1px solid var(--line); overflow-wrap: anywhere; background: #eef3ed; }
        @media (max-width: 1000px) {
            .topbar .in { gap: 16px; }
            .userbox { width: 100%; justify-content: flex-end; border-top: 1px solid var(--line); padding-top: 12px; }
            .userbox .pill { max-width: none; }
            .detail { gap: 24px; }
        }
        @media (max-width: 700px) {
            .topbar .in { padding: 14px 18px; }
            .brand { font-size: 1.05rem; }
            .brand .mark { width: 40px; height: 40px; border-radius: 12px; }
            .nav { gap: 4px; }
            .nav a { padding: 8px 10px; font-size: .8rem; }
            .topbar .in > .nav:not(.guest-nav) { flex-basis: 100%; }
            .userbox { justify-content: space-between; font-size: .74rem; }
            .wrap { padding: 26px 16px 40px; }
            .page-head { margin-bottom: 22px; }
            .filters { padding: 14px; gap: 10px; }
            .filters .search { min-width: 100%; }
            .filters select { flex: 1; min-width: 120px; }
            .pet-grid, .grid { gap: 16px; grid-template-columns: repeat(auto-fill, minmax(225px, 1fr)); }
            .detail { grid-template-columns: 1fr; padding: 18px; border-radius: 18px; }
            .gallery .main { height: 310px; }
            .hero { padding: 24px 20px; }
            .flow { justify-content: flex-start; font-size: .78rem; gap: 8px; }
            .step { padding: 80px 18px 22px; }
            .step-no { left: 18px; top: 20px; }
            .auth { margin-top: 8px; }
            .auth .bd { padding: 28px 22px; }
            .card .bd { padding: 22px; }
            .auth .card .bd { padding: 28px 22px; }
            pre.json, pre.code { padding: 16px; font-size: .75rem; }
            .table-wrap > .tbl:not(.api-table) { min-width: 580px; }
            .footer { padding: 20px 16px; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { transition: none !important; animation: none !important; }
            .pet-card:hover { transform: none; }
        }
    </style>
</head>

<body>
    <header class="topbar">
        <div class="in">
            <a class="brand" href="{{ auth()->check() ? route('pets.index') : route('login') }}"><span
                    class="mark">🐾</span><span>Lost Pet<small>ระบบแจ้งสัตว์หาย - พบสัตว์</small></span></a>
            @auth
                <nav class="nav" aria-label="เมนูหลัก">
                    <a href="{{ route('pets.index') }}"
                        class="{{ request()->routeIs('pets.*') ? 'on' : '' }}">สัตว์เลี้ยง</a>
                    <a href="{{ route('docs') }}" class="{{ request()->routeIs('docs') ? 'on' : '' }}">API</a>
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.users.index') }}"
                            class="{{ request()->routeIs('admin.*') ? 'on' : '' }}">จัดการผู้ใช้</a>
                    @endif
                </nav>
                <div class="userbox"><span class="pill">{{ auth()->user()->name }} ({{ auth()->user()->email }})</span>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button>ออกจากระบบ</button></form>
                </div>
            @else
                <nav class="nav guest-nav" aria-label="เมนูหลัก">
                    <a href="{{ route('docs') }}" class="{{ request()->routeIs('docs') ? 'on' : '' }}">เอกสาร API</a>
                    <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'on' : '' }}">เข้าสู่ระบบ</a>
                    <a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? 'on' : '' }}">สมัครสมาชิก</a>
                </nav>
            @endauth
        </div>
    </header>
    <main class="wrap" id="main-content">
        @if (!request()->routeIs('login', 'register'))
            @hasSection('crumb')
                <div class="crumb">@yield('crumb')</div>
            @endif
        @endif
        @if (session('ok'))
            <div class="okmsg">{{ session('ok') }}</div>
        @endif
        @yield('content')
    </main>
    <footer class="footer">🐾 Lost Pet · พาน้องกลับบ้าน ด้วยความห่วงใย</footer>
    <script>
        async function copyText(t, btn) {
            try {
                await navigator.clipboard.writeText(t);
                const o = btn.textContent;
                btn.textContent = 'คัดลอกแล้ว';
                setTimeout(() => btn.textContent = o, 1200);
            } catch (e) {
                alert('คัดลอกไม่ได้: ' + e.message);
            }
        }

        function copyJson(id, btn) {
            copyText(document.getElementById(id).textContent, btn);
        }

        function copyEl(id, btn) {
            copyText(document.getElementById(id).textContent, btn);
        }
    </script>
</body>

</html>
