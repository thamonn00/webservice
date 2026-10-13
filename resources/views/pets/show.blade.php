@extends('layouts.app')
@section('title', $pet->name)
@section('content')
    @php $photos = $pet->photos; $cover = $photos->first()->url ?? asset('images/sample/dog.svg'); @endphp
    <div class="crumb"><a href="{{ route('pets.index') }}">สัตว์เลี้ยง</a> / {{ $pet->name }}</div>

    <div class="detail">
        <div class="gallery">
            <div class="main"><img id="cover" src="{{ $cover }}" alt="{{ $pet->name }}"></div>
            @if ($photos->count() > 1)
                <div class="thumbs">
                    @foreach ($photos as $ph)
                        <img src="{{ $ph->url }}" alt="" class="{{ $loop->first ? 'on' : '' }}"
                            onclick="document.getElementById('cover').src=this.src;document.querySelectorAll('.thumbs img').forEach(i=>i.classList.remove('on'));this.classList.add('on')">
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <div class="title-row">
                <h1>{{ $pet->name }}</h1>
                <span class="chip pri">{{ $pet->species->name ?? '-' }}</span>
                @if ($pet->breed)
                    <span class="chip">{{ $pet->breed->name }}</span>
                @endif
            </div>
            <div class="meta">รหัส #{{ $pet->id }} · เพิ่มเมื่อ {{ $pet->created_at?->format('d/m/Y') }}</div>

            <dl class="kv">
                <dt>สี</dt><dd>{{ $pet->color ?? '-' }}</dd>
                <dt>เพศ</dt><dd>{{ $pet->gender_label }}</dd>
                <dt>อายุ</dt><dd>{{ $pet->age !== null ? $pet->age . ' ปี' : '-' }}</dd>
                <dt>รายละเอียด</dt><dd>{{ $pet->description ?? '-' }}</dd>
                <dt>เจ้าของ</dt><dd>{{ $pet->user->name ?? '-' }}</dd>
            </dl>

            <div class="toolbar" style="margin-top:24px">
                <a class="btn ghost" href="{{ route('pets.index') }}">← กลับ</a>
                <button class="btn ghost" onclick="copyText('{{ url('/api/pet/' . $pet->id) }}',this)">คัดลอก URL ของ API</button>
            </div>
        </div>
    </div>

    <details class="api">
        <summary>ดูข้อมูลดิบ (JSON) ที่ API ส่งกลับ</summary>
        <div class="toolbar">
            <span class="m GET">GET</span><code>{{ url('/api/pet/' . $pet->id) }}</code>
            <button class="btn ghost sm" onclick="copyEl('petjson',this)">คัดลอก JSON</button>
        </div>
        <pre class="code" id="petjson">{{ $json }}</pre>
    </details>
@endsection
