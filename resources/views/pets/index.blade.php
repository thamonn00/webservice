@extends('layouts.app')
@section('title', 'สัตว์เลี้ยง')
@section('content')
    <div class="page-head">
        <div>
            <h1>สัตว์เลี้ยง</h1>
            <p>ข้อมูลสัตว์ทั้งหมด {{ $pets->count() }} ตัว · หน้านี้ใช้ดูข้อมูล ส่วนเพิ่ม/แก้ไข/ลบทำผ่าน Thunder Client</p>
        </div>
        <a class="btn" href="{{ route('docs') }}">เรียนรู้ CRUD ใน Thunder Client</a>
    </div>

    <form class="filters" method="GET">
        <input class="search" type="search" name="q" placeholder="ค้นหาชื่อหรือสี…" value="{{ request('q') }}">
        <select name="species_id" onchange="this.form.submit()">
            <option value="">ทุกชนิด</option>
            @foreach ($species as $s)
                <option value="{{ $s->id }}" @selected(request('species_id') == $s->id)>{{ $s->name }}</option>
            @endforeach
        </select>
        <label class="check"><input type="checkbox" name="mine" value="1" @checked(request('mine'))
                onchange="this.form.submit()"> ของฉัน</label>
        <button class="btn ghost">ค้นหา</button>
        @if (request()->hasAny(['q', 'species_id', 'mine']))
            <a class="btn ghost" href="{{ route('pets.index') }}">ล้าง</a>
        @endif
    </form>

    @if ($pets->isEmpty())
        <div class="card empty">
            <div class="big">🔍</div>
            ไม่พบข้อมูลที่ตรงกับเงื่อนไข
        </div>
    @else
        <div class="pet-grid">
            @foreach ($pets as $p)
                <a class="card pet-card" href="{{ route('pets.show', $p) }}">
                    <div class="pic"><img loading="lazy" src="{{ $p->photos->first()->url ?? asset('images/sample/other.svg') }}"
                            alt="{{ $p->name }}"></div>
                    <div class="info">
                        <h3>{{ $p->name }} <small>#{{ $p->id }}</small></h3>
                        <div>
                            <span class="chip pri">{{ $p->species->name ?? '-' }}</span>
                            @if ($p->breed)
                                <span class="chip">{{ $p->breed->name }}</span>
                            @endif
                        </div>
                        <div class="line">{{ $p->color ?? 'ไม่ระบุสี' }} · {{ $p->gender_label }}@if ($p->age !== null) · {{ $p->age }} ปี @endif</div>
                        <div class="foot"><span class="avatar">{{ mb_substr($p->user->name ?? '?', 0, 1) }}</span>{{ $p->user->name ?? '-' }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endsection
