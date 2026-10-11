@extends('admin.layout')

@section('title', 'แดชบอร์ด')

@section('content')
<main class="container">
    <div class="eyebrow">ภาพรวมระบบ</div>
    <h1>แดชบอร์ดผู้ดูแล</h1>
    <p class="muted">จัดการข้อมูลผ่านหน้าแอดมิน ส่วนผู้ใช้ทั่วไปสามารถนำ API สาธารณะไปออกแบบแอปได้</p>

    <nav class="nav" aria-label="เมนูจัดการข้อมูล">
        <a href="{{ route('admin.resources.index', 'pets') }}">จัดการสัตว์เลี้ยง</a>
        <a href="{{ route('admin.resources.index', 'reports') }}">จัดการรายงานสัตว์หาย</a>
        <a href="{{ route('admin.resources.index', 'sightings') }}">จัดการการแจ้งพบ</a>
    </nav>

    <section class="stats">
        <article class="stat"><span class="muted">สัตว์เลี้ยงทั้งหมด</span><strong>{{ number_format($petCount) }}</strong></article>
        <article class="stat"><span class="muted">รายงานที่กำลังดำเนินการ</span><strong>{{ number_format($activeReportCount) }}</strong></article>
        <article class="stat"><span class="muted">รายการแจ้งพบทั้งหมด</span><strong>{{ number_format($sightingCount) }}</strong></article>
    </section>

    <section class="panel">
        <h2>รายงานล่าสุด</h2>
        @forelse ($recentReports as $report)
            <div class="record">
                <div class="record-main">
                    <div class="record-title">{{ $report->title }}</div>
                    <div class="record-meta">{{ $report->pet?->name ?? 'ไม่ระบุสัตว์' }} · {{ $report->province }} / {{ $report->district }}</div>
                </div>
                <span class="pill">{{ $report->type === 'lost' ? 'สัตว์หาย' : 'พบสัตว์' }}</span>
            </div>
        @empty
            <div class="empty">ยังไม่มีรายงาน</div>
        @endforelse
    </section>
</main>
@endsection
