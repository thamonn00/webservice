<!doctype html>
<html lang="th">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Lost Pet') - ระบบแจ้งสัตว์หาย/พบสัตว์</title>
    <style>
        :root {
            --bg: #eef2f7;
            --card: #fff;
            --ink: #0f172a;
            --mute: #64748b;
            --line: #e2e8f0;
            --pri: #1d4ed8;
            --pri2: #1e40af;
            --ok: #16a34a;
            --bad: #dc2626
        }

        * {
            box-sizing: border-box
        }

        body {
            margin: 0;
            font-family: "Sarabun", "Noto Sans Thai", system-ui, sans-serif;
            background: var(--bg);
            color: var(--ink);
            line-height: 1.55
        }

        .topbar {
            background: #0f172a;
            color: #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 20
        }

        .topbar .in {
            max-width: 1140px;
            margin: auto;
            padding: 10px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: #fff;
            font-weight: 800;
            font-size: 1.1rem
        }

        .brand .mark {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            display: grid;
            place-items: center;
            font-size: 1.2rem
        }

        .brand small {
            display: block;
            font-weight: 400;
            font-size: .72rem;
            color: #94a3b8
        }

        .nav {
            display: flex;
            gap: 2px;
            flex-wrap: wrap;
            flex: 1
        }

        .nav a {
            color: #cbd5e1;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: .94rem
        }

        .nav a:hover {
            background: #1e293b;
            color: #fff
        }

        .nav a.on {
            background: #1d4ed8;
            color: #fff;
            font-weight: 700
        }

        .userbox {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: .88rem;
            color: #cbd5e1
        }

        .userbox .pill {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 99px;
            padding: 3px 12px
        }

        .userbox button {
            background: #dc2626;
            border: 0;
            color: #fff;
            border-radius: 8px;
            padding: 7px 14px;
            cursor: pointer;
            font: inherit
        }

        .wrap {
            max-width: 1140px;
            margin: auto;
            padding: 20px 18px 60px
        }

        .crumb {
            font-size: .85rem;
            color: var(--mute);
            margin: 2px 0 12px
        }

        .crumb a {
            color: var(--pri);
            text-decoration: none
        }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 1px 3px #0f172a14
        }

        .card .hd {
            padding: 14px 18px;
            border-bottom: 1px solid var(--line);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap
        }

        .card .hd h2 {
            margin: 0;
            font-size: 1.08rem
        }

        .card .bd {
            padding: 18px
        }

        .btn {
            background: var(--pri);
            color: #fff;
            border: 0;
            border-radius: 8px;
            padding: 9px 18px;
            font: inherit;
            cursor: pointer;
            text-decoration: none;
            display: inline-block
        }

        .btn:hover {
            background: var(--pri2)
        }

        .btn.ghost {
            background: #fff;
            color: var(--ink);
            border: 1px solid var(--line)
        }

        .btn.danger {
            background: #fff;
            color: var(--bad);
            border: 1px solid #fecaca
        }

        .btn.ok {
            background: var(--ok)
        }

        .btn.sm {
            padding: 5px 12px;
            font-size: .86rem
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 14px
        }

        .pcard {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column
        }

        .pcard img {
            width: 100%;
            height: 170px;
            object-fit: cover;
            background: #f1f5f9
        }

        .pcard .b {
            padding: 12px;
            flex: 1
        }

        .pcard h3 {
            margin: 0 0 4px;
            font-size: 1rem
        }

        .meta {
            color: var(--mute);
            font-size: .86rem
        }

        .tag {
            display: inline-block;
            font-size: .75rem;
            padding: 1px 9px;
            border-radius: 99px;
            color: #fff;
            margin-right: 4px
        }

        .tag.lost {
            background: var(--bad)
        }

        .tag.found {
            background: var(--ok)
        }

        .tag.closed {
            background: #94a3b8
        }

        .tag.open {
            background: #0284c7
        }

        .acts {
            display: flex;
            gap: 6px;
            padding: 0 12px 12px;
            flex-wrap: wrap
        }

        .tools {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 14px
        }

        input,
        select,
        textarea {
            font: inherit;
            padding: 9px 11px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #fff;
            width: 100%
        }

        .tools input,
        .tools select {
            width: auto;
            flex: 1;
            min-width: 140px
        }

        table.tbl {
            border-collapse: collapse;
            width: 100%;
            background: #fff;
            font-size: .9rem
        }

        table.tbl td,
        table.tbl th {
            border: 1px solid var(--line);
            padding: 6px 10px;
            text-align: left;
            vertical-align: top
        }

        table.tbl th {
            background: #eff6ff
        }

        img.thumb {
            width: 52px;
            height: 52px;
            object-fit: cover;
            border-radius: 8px;
            background: #f1f5f9;
            display: block
        }

        pre.json {
            background: #0f172a;
            color: #e2e8f0;
            border-radius: 10px;
            padding: 14px;
            overflow: auto;
            max-height: 420px;
            font-size: .82rem
        }

        .toolbar {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin: 10px 0
        }

        .err {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: .9rem;
            white-space: pre-line;
            margin-bottom: 10px
        }

        .okmsg {
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: .9rem;
            margin-bottom: 10px
        }

        .detailimg {
            width: 100%;
            max-height: 320px;
            object-fit: cover;
            border-radius: 10px;
            background: #f1f5f9
        }

        .kv {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 4px 12px;
            font-size: .94rem
        }

        .kv dt {
            color: var(--mute)
        }

        .kv dd {
            margin: 0
        }

        .authwrap {
            max-width: 440px;
            margin: 40px auto
        }

        .footer {
            text-align: center;
            color: var(--mute);
            font-size: .82rem;
            padding: 24px
        }

        .pager {
            display: flex;
            gap: 10px;
            justify-content: center;
            align-items: center;
            margin: 18px 0;
            flex-wrap: wrap
        }

        .tabs {
            display: flex;
            gap: 6px;
            margin: 12px 0
        }

        .tabs button {
            border: 1px solid var(--line);
            background: #fff;
            border-radius: 8px 8px 0 0;
            padding: 7px 16px;
            cursor: pointer;
            font: inherit
        }

        .tabs button.on {
            background: #0f172a;
            color: #fff;
            border-color: #0f172a
        }

        .hero {
            background: linear-gradient(135deg, #eff6ff, #fff);
            border: 1px solid #bfdbfe;
            border-radius: 16px;
            padding: 22px;
            margin-bottom: 16px
        }

        .hero h1 {
            margin: 0 0 8px;
            font-size: 1.5rem
        }

        .hero p {
            margin: 0 0 12px;
            color: var(--mute)
        }

        .baseurl {
            display: inline-block;
            background: #0f172a;
            color: #e2e8f0;
            border-radius: 8px;
            padding: 8px 12px;
            word-break: break-all
        }

        .step {
            position: relative;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 18px 18px 18px 72px;
            margin: 16px 0;
            box-shadow: 0 1px 3px #0f172a14
        }

        .step-no {
            position: absolute;
            left: 18px;
            top: 18px;
            width: 38px;
            height: 38px;
            border-radius: 12px;
            background: var(--pri);
            color: #fff;
            display: grid;
            place-items: center;
            font-weight: 800;
            font-size: 1.1rem
        }

        .step h2 {
            margin: 0 0 12px;
            font-size: 1.15rem
        }

        .step h3 {
            margin: 16px 0 8px;
            font-size: 1rem
        }

        .cond {
            background: #fffbeb;
            border: 1px solid #facc15;
            border-left: 5px solid #eab308;
            border-radius: 9px;
            padding: 11px 14px;
            margin: 12px 0;
            color: #713f12
        }

        .badge-lock,
        .badge-free {
            display: inline-block;
            padding: 2px 9px;
            border-radius: 99px;
            font-size: .78rem;
            font-weight: 700;
            white-space: nowrap
        }

        .badge-lock {
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #fecdd3
        }

        .badge-free {
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #bbf7d0
        }

        .m-GET,
        .m-POST,
        .m-PUT,
        .m-DELETE {
            display: inline-block;
            min-width: 62px;
            text-align: center;
            padding: 2px 8px;
            border-radius: 6px;
            color: #fff;
            font-size: .76rem;
            font-weight: 800
        }

        .m-GET {
            background: #0284c7
        }

        .m-POST {
            background: #16a34a
        }

        .m-PUT {
            background: #d97706
        }

        .m-DELETE {
            background: #dc2626
        }

        .flow {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
            background: #f8fafc;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 12px;
            margin: 12px 0
        }

        .flow span {
            font-weight: 700
        }

        .flow i {
            font-style: normal;
            color: var(--pri);
            font-weight: 900
        }

        .api-table {
            margin: 10px 0 14px
        }

        .api-table td:first-child {
            white-space: nowrap
        }

        .code-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            margin: 14px 0 4px;
            flex-wrap: wrap
        }

        .code-title strong {
            font-size: .92rem
        }

        .step pre.json {
            margin: 6px 0 12px
        }

        .readonly-note {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e3a8a;
            border-radius: 9px;
            padding: 10px 13px;
            margin: 0 0 14px
        }

        .page-head,
        .title-row,
        .toolbar,
        .foot,
        .user,
        .acts {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap
        }

        .page-head {
            justify-content: space-between;
            margin: 4px 0 18px
        }

        .page-head h1,
        .title-row h1 {
            margin: 0;
            font-size: 1.55rem
        }

        .page-head p,
        .sub {
            margin: 4px 0 0;
            color: var(--mute)
        }

        .filters {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin: 0 0 18px
        }

        .filters .search {
            flex: 1;
            min-width: 200px
        }

        .filters select {
            width: auto;
            min-width: 140px
        }

        .check {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            white-space: nowrap
        }

        .check input {
            width: auto;
            margin: 0
        }

        .pet-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 16px
        }

        .pet-card {
            color: inherit;
            text-decoration: none;
            transition: transform .15s, box-shadow .15s
        }

        .pet-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px #0f172a18
        }

        .pet-card .pic,
        .gallery .main {
            background: #f1f5f9;
            overflow: hidden
        }

        .pet-card .pic {
            height: 190px
        }

        .pet-card .pic img,
        .gallery .main img {
            width: 100%;
            height: 100%;
            object-fit: cover
        }

        .pet-card .info {
            padding: 14px
        }

        .pet-card h3 {
            margin: 0 0 8px;
            font-size: 1.08rem
        }

        .pet-card h3 small {
            color: var(--mute);
            font-weight: 400
        }

        .chip {
            display: inline-block;
            padding: 2px 9px;
            border: 1px solid var(--line);
            border-radius: 99px;
            color: #475569;
            font-size: .78rem
        }

        .chip.pri {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1d4ed8
        }

        .chip.admin {
            background: #fff7ed;
            border-color: #fed7aa;
            color: #c2410c
        }

        .line {
            margin: 9px 0;
            color: var(--mute);
            font-size: .88rem
        }

        .foot,
        .user {
            color: var(--mute);
            font-size: .86rem
        }

        .avatar {
            display: inline-grid;
            place-items: center;
            flex: 0 0 32px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #dbeafe;
            color: #1d4ed8;
            font-weight: 700
        }

        .detail {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(0, 1fr);
            gap: 28px;
            align-items: start
        }

        .gallery .main {
            height: 360px;
            border-radius: 14px
        }

        .thumbs {
            display: flex;
            gap: 8px;
            margin-top: 10px;
            overflow-x: auto
        }

        .thumbs img {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border: 2px solid transparent;
            border-radius: 8px;
            cursor: pointer
        }

        .thumbs img.on {
            border-color: var(--pri)
        }

        .title-row {
            margin: 0 0 8px
        }

        .kv {
            margin: 20px 0 0
        }

        .api {
            margin-top: 24px;
            padding: 16px;
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 14px
        }

        .api summary {
            cursor: pointer;
            font-weight: 700
        }

        .api code {
            overflow-wrap: anywhere
        }

        pre.code {
            background: #0f172a;
            color: #e2e8f0;
            border-radius: 10px;
            padding: 14px;
            overflow: auto;
            max-height: 420px;
            font-size: .82rem
        }

        .m.GET {
            display: inline-block;
            min-width: 62px;
            text-align: center;
            padding: 2px 8px;
            border-radius: 6px;
            background: #0284c7;
            color: #fff;
            font-size: .76rem;
            font-weight: 800
        }

        .alert {
            border-radius: 8px;
            padding: 8px 12px;
            margin-bottom: 12px;
            white-space: pre-line
        }

        .alert.err {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca
        }

        .empty {
            padding: 24px;
            text-align: center;
            color: var(--mute)
        }

        .empty .big {
            font-size: 2rem
        }

        .form {
            display: grid;
            gap: 14px
        }

        .field {
            display: grid;
            gap: 6px;
            font-weight: 600
        }

        .field input,
        .field select {
            font-weight: 400
        }

        .form .toolbar {
            margin-top: 8px
        }

        .btn.block {
            width: 100%
        }

        .auth {
            max-width: 440px;
            margin: 32px auto
        }

        .auth .bd {
            padding: 26px
        }

        .auth .logo {
            font-size: 2rem;
            text-align: center
        }

        .auth h1 {
            margin: 4px 0;
            text-align: center
        }

        .auth .sub,
        .auth .alt {
            text-align: center;
            margin: 8px 0 20px;
            color: var(--mute)
        }

        .auth .alt {
            margin: 18px 0 0
        }

        .auth .alt a {
            color: var(--pri)
        }

        .table-wrap {
            overflow-x: auto
        }

        .acts {
            justify-content: flex-end
        }

        .acts form {
            margin: 0
        }

        .acts .btn.danger {
            background: #fff;
            color: var(--bad);
            border: 1px solid #fecaca
        }

        .pagination {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 6px;
            padding: 14px;
            list-style: none
        }

        .pagination a,
        .pagination span {
            display: block;
            padding: 5px 10px;
            border: 1px solid var(--line);
            border-radius: 7px;
            background: #fff;
            color: var(--ink);
            text-decoration: none
        }

        .pagination .active span {
            background: var(--pri);
            border-color: var(--pri);
            color: #fff
        }

        @media(max-width:640px) {
            .detail {
                grid-template-columns: 1fr
            }

            .gallery .main {
                height: 280px
            }

            .filters .search {
                min-width: 100%
            }

            .step {
                padding: 64px 14px 14px
            }

            .step-no {
                left: 14px;
                top: 14px
            }

            .hero {
                padding: 16px
            }

            .flow {
                justify-content: flex-start
            }
        }
    </style>
</head>

<body>
    <header class="topbar">
        <div class="in">
            <a class="brand" href="{{ auth()->check() ? route('pets.index') : route('login') }}"><span
                    class="mark">🐾</span><span>Lost Pet<small>ระบบแจ้งสัตว์หาย - พบสัตว์</small></span></a>
            @auth
                <nav class="nav">
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
            @endauth
        </div>
    </header>
    <div class="wrap">
        @if (!request()->routeIs('login', 'register'))
            @hasSection('crumb')
                <div class="crumb">@yield('crumb')</div>
            @endif
        @endif
        @if (session('ok'))
            <div class="okmsg">{{ session('ok') }}</div>
        @endif
        @yield('content')
    </div>
    <div class="footer">Lost Pet Web Service - Laravel 9 + Sanctum - Base URL <b>{{ url('/api') }}</b></div>
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
