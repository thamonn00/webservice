@extends('admin.layout')

@php
    $titles = ['pets' => 'สัตว์เลี้ยง', 'reports' => 'รายงานสัตว์หาย', 'sightings' => 'การแจ้งพบสัตว์'];
    $title = $titles[$resource];
    $editing = $editing ?? null;
@endphp

@section('title', 'จัดการ'.$title)

@section('content')
<main class="container">
    <div class="eyebrow">จัดการข้อมูล</div>
    <h1>{{ $editing ? 'แก้ไข' : 'จัดการ' }}{{ $title }}</h1>
    <p class="muted">หน้านี้สำหรับผู้ดูแลระบบเท่านั้น การเพิ่ม แก้ไข หรือลบข้อมูลจะมีผลกับ API</p>

    <nav class="nav" aria-label="เมนูจัดการข้อมูล">
        <a class="{{ $resource === 'pets' ? 'active' : '' }}" href="{{ route('admin.resources.index', 'pets') }}">สัตว์เลี้ยง</a>
        <a class="{{ $resource === 'reports' ? 'active' : '' }}" href="{{ route('admin.resources.index', 'reports') }}">รายงานสัตว์หาย</a>
        <a class="{{ $resource === 'sightings' ? 'active' : '' }}" href="{{ route('admin.resources.index', 'sightings') }}">การแจ้งพบ</a>
    </nav>

    @if (session('status'))
        <div class="alert success">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert error">
            <strong>กรุณาตรวจสอบข้อมูล</strong>
            <ul style="margin-bottom:0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="grid">
        <section class="panel">
            <h2>{{ $editing ? 'แก้ไขข้อมูล' : 'เพิ่มข้อมูลใหม่' }}</h2>
            <form method="POST"
                  action="{{ $editing ? route('admin.resources.update', [$resource, $editing->id]) : route('admin.resources.store', $resource) }}"
                  enctype="multipart/form-data">
                @csrf
                @if ($editing) @method('PUT') @endif
                <div class="form-grid">
                    @if ($resource === 'pets')
                        <div class="field">
                            <label for="species_id">ชนิดสัตว์</label>
                            <select id="species_id" name="species_id" required>
                                <option value="">เลือกชนิดสัตว์</option>
                                @foreach ($species as $item)
                                    <option value="{{ $item->id }}" @selected((string) old('species_id', $editing?->species_id) === (string) $item->id)>{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field">
                            <label for="breed_id">สายพันธุ์</label>
                            <select id="breed_id" name="breed_id">
                                <option value="">ไม่ระบุ</option>
                                @foreach ($species as $item)
                                    <optgroup label="{{ $item->name }}">
                                        @foreach ($item->breeds as $breed)
                                            <option value="{{ $breed->id }}" data-species="{{ $item->id }}" @selected((string) old('breed_id', $editing?->breed_id) === (string) $breed->id)>{{ $breed->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="field"><label for="name">ชื่อสัตว์</label><input id="name" name="name" value="{{ old('name', $editing?->name) }}" required></div>
                        <div class="field"><label for="color">สี</label><input id="color" name="color" value="{{ old('color', $editing?->color) }}"></div>
                        <div class="field">
                            <label for="gender">เพศ</label>
                            <select id="gender" name="gender">
                                @foreach (['unknown' => 'ไม่ทราบ', 'male' => 'ผู้', 'female' => 'เมีย'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('gender', $editing?->gender ?? 'unknown') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="field"><label for="age">อายุ (ปี)</label><input id="age" name="age" type="number" min="0" value="{{ old('age', $editing?->age) }}"></div>
                        <div class="field full"><label for="description">รายละเอียด</label><textarea id="description" name="description">{{ old('description', $editing?->description) }}</textarea></div>
                    @elseif ($resource === 'reports')
                        <div class="field full">
                            <label for="pet_id">สัตว์เลี้ยง</label>
                            <select id="pet_id" name="pet_id" required><option value="">เลือกสัตว์เลี้ยง</option>
                                @foreach ($pets as $pet)<option value="{{ $pet->id }}" @selected((string) old('pet_id', $editing?->pet_id) === (string) $pet->id)>{{ $pet->name }} · {{ $pet->species?->name }}</option>@endforeach
                            </select>
                        </div>
                        <div class="field"><label for="type">ประเภท</label><select id="type" name="type" required><option value="lost" @selected(old('type', $editing?->type) === 'lost')>สัตว์หาย</option><option value="found" @selected(old('type', $editing?->type) === 'found')>พบสัตว์</option></select></div>
                        <div class="field"><label for="status">สถานะ</label><select id="status" name="status">@foreach (['active' => 'กำลังดำเนินการ', 'found' => 'พบเจ้าของแล้ว', 'closed' => 'ปิดรายงาน', 'cancelled' => 'ยกเลิก'] as $value => $label)<option value="{{ $value }}" @selected(old('status', $editing?->status ?? 'active') === $value)>{{ $label }}</option>@endforeach</select></div>
                        <div class="field full"><label for="title">หัวข้อ</label><input id="title" name="title" value="{{ old('title', $editing?->title) }}" required></div>
                        <div class="field full"><label for="description">รายละเอียด</label><textarea id="description" name="description" required>{{ old('description', $editing?->description) }}</textarea></div>
                        <div class="field"><label for="province">จังหวัด</label><input id="province" name="province" value="{{ old('province', $editing?->province) }}" required></div>
                        <div class="field"><label for="district">อำเภอ/เขต</label><input id="district" name="district" value="{{ old('district', $editing?->district) }}" required></div>
                        <div class="field"><label for="subdistrict">ตำบล/แขวง</label><input id="subdistrict" name="subdistrict" value="{{ old('subdistrict', $editing?->subdistrict) }}" required></div>
                        <div class="field"><label for="place">สถานที่</label><input id="place" name="place" value="{{ old('place', $editing?->place) }}" required></div>
                        <div class="field"><label for="seen_at">วันที่พบเห็น</label><input id="seen_at" name="seen_at" type="datetime-local" value="{{ old('seen_at', $editing?->seen_at?->format('Y-m-d\TH:i')) }}" required></div>
                        <div class="field"><label for="contact_phone">เบอร์ติดต่อ</label><input id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $editing?->contact_phone) }}" required></div>
                        <div class="field"><label for="latitude">ละติจูด</label><input id="latitude" name="latitude" type="number" step="any" value="{{ old('latitude', $editing?->latitude) }}"></div>
                        <div class="field"><label for="longitude">ลองจิจูด</label><input id="longitude" name="longitude" type="number" step="any" value="{{ old('longitude', $editing?->longitude) }}"></div>
                    @else
                        <div class="field full">
                            <label for="report_id">รายงานที่เกี่ยวข้อง</label>
                            <select id="report_id" name="report_id" required><option value="">เลือกรายงาน</option>
                                @foreach ($reports as $report)<option value="{{ $report->id }}" @selected((string) old('report_id', $editing?->report_id) === (string) $report->id)>#{{ $report->id }} · {{ $report->title }}</option>@endforeach
                            </select>
                        </div>
                        <div class="field full"><label for="message">รายละเอียดการพบ</label><textarea id="message" name="message" required>{{ old('message', $editing?->message) }}</textarea></div>
                        <div class="field"><label for="province">จังหวัด</label><input id="province" name="province" value="{{ old('province', $editing?->province) }}" required></div>
                        <div class="field"><label for="district">อำเภอ/เขต</label><input id="district" name="district" value="{{ old('district', $editing?->district) }}" required></div>
                        <div class="field"><label for="subdistrict">ตำบล/แขวง</label><input id="subdistrict" name="subdistrict" value="{{ old('subdistrict', $editing?->subdistrict) }}" required></div>
                        <div class="field"><label for="seen_place">สถานที่พบ</label><input id="seen_place" name="seen_place" value="{{ old('seen_place', $editing?->seen_place) }}" required></div>
                        <div class="field"><label for="seen_at">วันที่พบเห็น</label><input id="seen_at" name="seen_at" type="datetime-local" value="{{ old('seen_at', $editing?->seen_at?->format('Y-m-d\TH:i')) }}" required></div>
                        <div class="field"><label for="contact">ช่องทางติดต่อ</label><input id="contact" name="contact" value="{{ old('contact', $editing?->contact) }}"></div>
                        <div class="field"><label for="latitude">ละติจูด</label><input id="latitude" name="latitude" type="number" step="any" value="{{ old('latitude', $editing?->latitude) }}"></div>
                        <div class="field"><label for="longitude">ลองจิจูด</label><input id="longitude" name="longitude" type="number" step="any" value="{{ old('longitude', $editing?->longitude) }}"></div>
                    @endif

                    <div class="field full">
                        <label for="photos">อัปโหลดรูปภาพ</label>
                        <input id="photos" name="photos[]" type="file" accept="image/jpeg,image/png,image/webp" multiple>
                        <span class="help">รองรับ JPG, PNG, WEBP ขนาดไม่เกิน 5 MB ต่อรูป</span>
                    </div>
                    <div class="field full">
                        <label for="photo_urls">ลิงก์รูปภาพ</label>
                        <input id="photo_url" name="photo_url" type="url" placeholder="https://example.com/photo.jpg">
                        <span class="help">เพิ่มลิงก์รูปได้หนึ่งลิงก์ต่อครั้ง</span>
                    </div>
                    @if ($editing && $editing->photos->isNotEmpty())
                        <div class="field full">
                            <label>รูปที่มีอยู่ (เลือกเพื่อลบ)</label>
                            <div class="photos">
                                @foreach ($editing->photos as $photo)
                                    <label class="check-photo"><img class="photo" src="{{ $photo->photo_url }}" alt="รูปแนบ"><input type="checkbox" name="delete_photo_ids[]" value="{{ $photo->id }}">ลบ</label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                <div style="display:flex;gap:8px;margin-top:18px">
                    <button class="button primary" type="submit">{{ $editing ? 'บันทึกการแก้ไข' : 'เพิ่มข้อมูล' }}</button>
                    @if ($editing)<a class="button" href="{{ route('admin.resources.index', $resource) }}">ยกเลิก</a>@endif
                </div>
            </form>
        </section>

        <section class="panel">
            <h2>รายการ{{ $title }} <span class="muted" style="font-size:14px;font-weight:400">({{ $records->total() }} รายการ)</span></h2>
            @forelse ($records as $record)
                <article class="record">
                    <div class="record-main">
                        <div class="record-title">
                            @if ($resource === 'pets')
                                {{ $record->name }} <span class="pill">{{ $record->species?->name }}</span>
                            @elseif ($resource === 'reports')
                                {{ $record->title }} <span class="pill">{{ $record->type === 'lost' ? 'สัตว์หาย' : 'พบสัตว์' }}</span>
                            @else
                                {{ \Illuminate\Support\Str::limit($record->message, 80) }}
                            @endif
                        </div>
                        <div class="record-meta">
                            @if ($resource === 'pets')
                                เจ้าของ: {{ $record->user?->name ?? '-' }} · {{ $record->breed?->name ?? 'ไม่ระบุสายพันธุ์' }} · รูป {{ $record->photos->count() }} รูป
                            @elseif ($resource === 'reports')
                                {{ $record->pet?->name ?? 'ไม่ระบุสัตว์' }} · {{ $record->province }} / {{ $record->district }} · {{ $record->status }}
                            @else
                                {{ $record->province }} / {{ $record->district }} · ผู้แจ้ง: {{ $record->user?->name ?? '-' }}
                            @endif
                        </div>
                        @if ($record->photos->isNotEmpty())
                            <div class="photos">@foreach ($record->photos->take(4) as $photo)<img class="photo" src="{{ $photo->photo_url }}" alt="รูปประกอบ">@endforeach</div>
                        @endif
                    </div>
                    <div class="record-actions">
                        <a class="button small" href="{{ route('admin.resources.index', ['resource' => $resource, 'edit' => $record->id]) }}">แก้ไข</a>
                        <form method="POST" action="{{ route('admin.resources.destroy', [$resource, $record->id]) }}" onsubmit="return confirm('ยืนยันการลบข้อมูลนี้?')">
                            @csrf @method('DELETE')
                            <button class="button small danger" type="submit">ลบ</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="empty">ยังไม่มีข้อมูล{{ $title }}</div>
            @endforelse
            <div class="pagination">{{ $records->links() }}</div>
        </section>
    </div>
</main>
<script>
    const speciesSelect = document.getElementById('species_id');
    const breedSelect = document.getElementById('breed_id');
    if (speciesSelect && breedSelect) {
        const filterBreeds = () => {
            [...breedSelect.options].forEach(option => {
                if (option.dataset.species) {
                    option.hidden = option.dataset.species !== speciesSelect.value;
                }
            });
            const selected = breedSelect.selectedOptions[0];
            if (selected?.dataset.species && selected.dataset.species !== speciesSelect.value) {
                breedSelect.value = '';
            }
        };
        speciesSelect.addEventListener('change', filterBreeds);
        filterBreeds();
    }
</script>
@endsection
