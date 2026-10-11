<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>แจ้งสัตว์หายและพบสัตว์</title>
    <style>
        *{box-sizing:border-box}body{margin:0;background:#f3f7f4;color:#17372e;font-family:Tahoma,"Noto Sans Thai",sans-serif}
        a{color:inherit;text-decoration:none}.nav{height:72px;background:#fff;border-bottom:1px solid #e4ebe6;display:flex;align-items:center}
        .nav-inner,.wrap{width:min(1100px,calc(100% - 40px));margin:auto}.nav-inner{display:flex;justify-content:space-between;align-items:center}
        .brand{font-weight:700;font-size:18px}.admin-link,.button{display:inline-flex;padding:10px 16px;border-radius:9px;background:#14775a;color:#fff;font-weight:700}
        .hero{padding:80px 0 54px;background:radial-gradient(ellipse at 82% 20%,#d8efe0 0,transparent 42%),#f3f7f4}
        .tag{display:inline-block;background:#e2f1e8;color:#146c43;border-radius:99px;padding:7px 12px;font-size:13px;font-weight:700}
        h1{font-size:clamp(34px,6vw,58px);line-height:1.16;max-width:760px;margin:20px 0}
        .lead{max-width:680px;color:#53695f;font-size:18px;line-height:1.8}.actions{display:flex;gap:12px;flex-wrap:wrap;margin-top:26px}
        .button.light{background:#fff;border:1px solid #d7e3dc;color:#17372e}
        .section{padding:24px 0 66px}.section h2{font-size:25px}.cards{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
        .card{background:#fff;border:1px solid #e2e9e4;border-radius:16px;padding:22px}
        .card .icon{font-size:28px}.card h3{margin:13px 0 8px}.card p{color:#66776e;line-height:1.7;margin:0}
        code{background:#eaf0ec;padding:3px 7px;border-radius:5px}
        footer{padding:22px 0;color:#708078;border-top:1px solid #e2e9e4;font-size:13px}
        @media(max-width:700px){.hero{padding:55px 0 36px}.cards{grid-template-columns:1fr}.lead{font-size:16px}}
    </style>
</head>
<body>
    <header class="nav">
        <div class="nav-inner">
            <a class="brand" href="/">🐾 แจ้งสัตว์หายและพบสัตว์</a>
            <a class="admin-link" href="{{ route('admin.login') }}">เข้าสู่ระบบแอดมิน</a>
        </div>
    </header>
    <main>
        <section class="hero">
            <div class="wrap">
                <span class="tag">พื้นที่กลางสำหรับช่วยเหลือสัตว์เลี้ยง</span>
                <h1>ช่วยกันตามหา<br>และพาสัตว์กลับบ้าน</h1>
                <p class="lead">บริการ API สำหรับดูข้อมูลสัตว์เลี้ยง รายงานสัตว์หาย และแจ้งพบสัตว์
                    นักพัฒนาสามารถนำข้อมูล JSON ไปออกแบบเว็บไซต์หรือแอปพลิเคชันของตนเองได้</p>
                <div class="actions">
                    <a class="button" href="/api/reports">ดูรายงานสัตว์หาย</a>
                    <a class="button light" href="/api/pets">ดูข้อมูลสัตว์เลี้ยง</a>
                    <a class="button light" href="/api/provinces">ดูข้อมูลจังหวัด</a>
                </div>
            </div>
        </section>
        <section class="section">
            <div class="wrap">
                <h2>เริ่มใช้งาน API</h2>
                <div class="cards">
                    <article class="card"><div class="icon">📋</div><h3>เรียกดูข้อมูล</h3><p>เริ่มจาก <code>GET /api/reports</code>, <code>/api/pets</code> หรือข้อมูลจังหวัด เพื่อรับผลลัพธ์ JSON</p></article>
                    <article class="card"><div class="icon">🔐</div><h3>บัญชีผู้ใช้และโทเคน</h3><p>สมัครและเข้าสู่ระบบผ่าน API เพื่อรับโทเคนสำหรับยืนยันตัวตน</p></article>
                    <article class="card"><div class="icon">🛠️</div><h3>ระบบจัดการแอดมิน</h3><p>เฉพาะผู้ดูแลระบบเท่านั้นที่เพิ่ม แก้ไข หรือลบข้อมูลผ่านหน้าแอดมินหรือ API ได้</p></article>
                </div>
            </div>
        </section>
    </main>
    <footer><div class="wrap">ข้อมูลสำหรับช่วยประสานการตามหาสัตว์เลี้ยงและพากลับคืนสู่เจ้าของ</div></footer>
</body>
</html>
