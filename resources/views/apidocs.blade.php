@extends('layouts.app')
@section('title', 'เอกสาร API')
@section('crumb', 'เอกสาร API')

@section('content')
    <section class="hero">
        <h1>คู่มือ API สำหรับ Thunder Client</h1>
        <p>ทำตามลำดับ: Login รับ Token → เรียก API → Logout เมื่อใช้งานเสร็จ</p>
        
        <div class="toolbar">
            <code class="baseurl" id="api-base">{{ url('/api') }}</code>
            <button class="btn sm" onclick="copyText(document.getElementById('api-base').textContent, this)">คัดลอก Base URL</button>
            <span class="badge-free">🟢 ไม่ต้องมี token: POST /register, POST /login</span>
            <span class="badge-lock">🔒 เส้น API อื่นต้องมี token</span>
        </div>
        
        <div class="flow">
            <span>Login</span><i>→</i><span>เรียก API</span><i>→</i><span>Logout</span>
        </div>
        <div class="toolbar">
            <a class="btn ghost sm" href="#all-api">API ทั้งหมด</a>
            <a class="btn ghost sm" href="#photo-guide">วิธีเพิ่มรูปภาพ</a>
        </div>
    </section>

    @auth
        <section class="step">
            <span class="step-no">🔑</span>
            <h2>สร้าง Token สำหรับ Thunder Client</h2>
            <p>การ Login เข้าเว็บเป็น session ของเว็บ ซึ่งใช้แทน Bearer Token ใน Thunder ไม่ได้ กดปุ่มเพื่อสร้าง API Token สำหรับบัญชีที่ล็อกอินอยู่นี้</p>
            
            <div class="cond">
                <b>ข้อควรระวัง:</b> Token จะแสดงเฉพาะตอนสร้างเท่านั้น ให้คัดลอกเก็บใน Thunder Client และอย่าแชร์ให้ผู้อื่น Token หมดอายุใน 7 วัน
            </div>
            
            <form id="create-api-token" method="POST" action="{{ route('docs.token') }}">
                @csrf
                <button class="btn" type="submit">สร้าง Token สำหรับ Thunder</button>
            </form>
            
            <p id="token-status" class="meta" role="status" aria-live="polite"></p>
            
            <div id="token-result" hidden>
                <label for="api-token"><b>Bearer Token</b></label>
                <div class="toolbar">
                    <input id="api-token" type="text" readonly autocomplete="off" aria-label="Bearer API token">
                    <button class="btn sm" type="button" onclick="copyText(document.getElementById('api-token').value, this)">คัดลอก Token</button>
                </div>
                <p class="meta">ใน Thunder ตั้ง Header: <code>Authorization</code> = <code>Bearer &lt;token&gt;</code> และ <code>Accept</code> = <code>application/json</code></p>
            </div>
        </section>
    @else
        <div class="cond">เข้าสู่ระบบบนเว็บก่อน แล้วเปิดหน้านี้เพื่อสร้างและคัดลอก API Token สำหรับ Thunder Client</div>
    @endauth

    <section class="step">
        <span class="step-no">1</span>
        <h2>Step 1 — Login และ Token (ข้อ 1.2)</h2>
        
        <div class="table-wrap" role="region" aria-label="ตารางเอกสาร API" tabindex="0">
        <table class="tbl api-table">
            <tr>
                <th>Method</th>
                <th>Path</th>
                <th>เงื่อนไขและข้อมูล</th>
                <th>คัดลอก</th>
            </tr>
            <tr>
                <td><span class="m-POST">POST</span></td>
                <td>/register</td>
                <td>
                    <span class="badge-free">🟢 ไม่ต้องมี token</span><br>
                    สมัครสมาชิก: name, email, password (≥ 8 ตัว), password_confirmation; phone ไม่บังคับ
                </td>
                <td><button class="btn ghost sm" onclick="copyText('{{ url('/api/register') }}', this)">คัดลอก URL</button></td>
            </tr>
            <tr>
                <td><span class="m-POST">POST</span></td>
                <td>/login</td>
                <td>
                    <span class="badge-free">🟢 ไม่ต้องมี token</span><br>
                    ส่ง email และ password แล้วเก็บ token จาก response
                </td>
                <td><button class="btn ghost sm" onclick="copyText('{{ url('/api/login') }}', this)">คัดลอก URL</button></td>
            </tr>
            <tr>
                <td><span class="m-POST">POST</span></td>
                <td>/logout</td>
                <td>
                    <span class="badge-lock">🔒 ต้องมี token</span><br>
                    เพิกถอน token ที่ใช้ส่ง request นี้
                </td>
                <td><button class="btn ghost sm" onclick="copyText('{{ url('/api/logout') }}', this)">คัดลอก URL</button></td>
            </tr>
            <tr>
                <td><span class="m-GET">GET</span></td>
                <td>/me</td>
                <td>
                    <span class="badge-lock">🔒 ต้องมี token</span><br>
                    ดูข้อมูลบัญชีที่ Login อยู่
                </td>
                <td><button class="btn ghost sm" onclick="copyText('{{ url('/api/me') }}', this)">คัดลอก URL</button></td>
            </tr>
        </table>
        </div>
        
        <div class="cond"><b>เงื่อนไข Header:</b> ใส่ <code>Accept: application/json</code> ทุก request และ <code>Authorization: Bearer &lt;token&gt;</code> ทุกเส้น (ยกเว้น <code>/register</code> กับ <code>/login</code>)</div>
        <div class="cond"><b>หาก Login ได้ 401:</b> อย่าใช้ admin@example.com เว้นแต่มีใน db แนะนำให้ใช้ somchai@example.com / password123 สำหรับทดสอบ local</div>
    </section>

    <section class="step">
        <span class="step-no">2</span>
        <h2>Step 2 — ค้นหาข้อมูล GET (ข้อ 1.3)</h2>
        
        <div class="table-wrap" role="region" aria-label="ตารางเอกสาร API" tabindex="0">
        <table class="tbl api-table">
            <tr>
                <th>Method</th>
                <th>Path</th>
                <th>ผลลัพธ์</th>
                <th>Token</th>
                <th>คัดลอก</th>
            </tr>
            <tr>
                <td><span class="m-GET">GET</span></td>
                <td>/pet, /pets</td>
                <td>ข้อมูลสัตว์ทั้งหมด (แบ่งหน้า); กรองได้: q, species_id, breed_id, gender</td>
                <td><span class="badge-lock">🔒</span></td>
                <td><button class="btn ghost sm" onclick="copyText('{{ url('/api/pet') }}', this)">คัดลอก URL</button></td>
            </tr>
            <tr>
                <td><span class="m-GET">GET</span></td>
                <td>/pet/{id}</td>
                <td>ข้อมูลสัตว์ตาม ID</td>
                <td><span class="badge-lock">🔒</span></td>
                <td><button class="btn ghost sm" onclick="copyText('{{ url('/api/pet/1') }}', this)">คัดลอก URL</button></td>
            </tr>
            <tr>
                <td><span class="m-GET">GET</span></td>
                <td>/species</td>
                <td>ชนิดสัตว์ทั้งหมด</td>
                <td><span class="badge-lock">🔒</span></td>
                <td><button class="btn ghost sm" onclick="copyText('{{ url('/api/species') }}', this)">คัดลอก URL</button></td>
            </tr>
            <tr>
                <td><span class="m-GET">GET</span></td>
                <td>/breeds</td>
                <td>สายพันธุ์; กรองได้ด้วย species_id</td>
                <td><span class="badge-lock">🔒</span></td>
                <td><button class="btn ghost sm" onclick="copyText('{{ url('/api/breeds') }}', this)">คัดลอก URL</button></td>
            </tr>
            <tr>
                <td><span class="m-GET">GET</span></td>
                <td>/provinces</td>
                <td>จังหวัดทั้งหมด</td>
                <td><span class="badge-lock">🔒</span></td>
                <td><button class="btn ghost sm" onclick="copyText('{{ url('/api/provinces') }}', this)">คัดลอก URL</button></td>
            </tr>
            <tr>
                <td><span class="m-GET">GET</span></td>
                <td>/districts</td>
                <td>อำเภอ; กรองได้ด้วย province_id</td>
                <td><span class="badge-lock">🔒</span></td>
                <td><button class="btn ghost sm" onclick="copyText('{{ url('/api/districts') }}', this)">คัดลอก URL</button></td>
            </tr>
            <tr>
                <td><span class="m-GET">GET</span></td>
                <td>/subdistricts, /subdistricts/{id}</td>
                <td>ตำบล; กรองได้ด้วย district_id<br>ดูตาม ID เช่น <code>{{ url('/api/subdistricts/1') }}</code></td>
                <td><span class="badge-lock">🔒</span></td>
                <td><button class="btn ghost sm" onclick="copyText('{{ url('/api/subdistricts') }}', this)">คัดลอก URL</button></td>
            </tr>
        </table>
        </div>
    </section>

    <section class="step" id="photo-guide">
        <span class="step-no">3</span>
        <h2>Step 3 — จัดการข้อมูล POST, PUT, DELETE (ข้อ 1.4)</h2>
        
        <div class="table-wrap" role="region" aria-label="ตารางเอกสาร API" tabindex="0">
        <table class="tbl api-table">
            <tr>
                <th>Method</th>
                <th>Path</th>
                <th>ข้อมูล</th>
                <th>Token</th>
                <th>คัดลอก</th>
            </tr>
            <tr>
                <td><span class="m-POST">POST</span></td>
                <td>/pet</td>
                <td>species_id, name (จำเป็น); breed_id, color, gender, age, description (ไม่บังคับ)</td>
                <td><span class="badge-lock">🔒</span></td>
                <td><button class="btn ghost sm" onclick="copyText('{{ url('/api/pet') }}', this)">คัดลอก URL</button></td>
            </tr>
            <tr>
                <td><span class="m-PUT">PUT/PATCH</span></td>
                <td>/pet/{id}</td>
                <td>ส่งเฉพาะ field ที่ต้องการแก้ได้</td>
                <td><span class="badge-lock">🔒</span></td>
                <td><button class="btn ghost sm" onclick="copyText('{{ url('/api/pet/1') }}', this)">คัดลอก URL</button></td>
            </tr>
            <tr>
                <td><span class="m-DELETE">DELETE</span></td>
                <td>/pet/{id}</td>
                <td>ลบข้อมูลและรูปที่ผูกไว้</td>
                <td><span class="badge-lock">🔒</span></td>
                <td><button class="btn ghost sm" onclick="copyText('{{ url('/api/pet/1') }}', this)">คัดลอก URL</button></td>
            </tr>
        </table>
        </div>
        
        <div class="cond"><b>สิทธิ์:</b> ระบบกำหนด <code>user_id</code> เป็นผู้ยิง request ให้เอง ตอนแก้/ลบต้องเป็นเจ้าของหรือ admin เท่านั้น ถ้าแก้/ลบได้ 403 แปลว่าไม่มีสิทธิ์</div>
        <div class="cond"><b>ส่งรูป:</b> ใช้ <code>photos[]</code> (ไฟล์), <code>photo_urls[]</code> (URL รูป) และ <code>delete_photo_ids[]</code> (ลบรูปเดิม)<br>หากแก้ข้อมูลพร้อมแนบไฟล์ให้ใช้ <code>POST</code> พร้อม <code>_method=PUT</code> (multipart/form-data)</div>
        <div class="cond"><b>อัปโหลดใน Thunder Client:</b> เลือก Body → Form ใส่ข้อมูลสัตว์หรือประกาศ แล้วเพิ่ม <code>photos[]</code> ชนิด File รองรับ JPG, JPEG, PNG, WebP ไม่เกิน 5 MB ต่อรูป และไม่เกิน 10 ไฟล์ต่อคำขอ อย่าตั้ง Content-Type เอง เพื่อให้โปรแกรมสร้าง multipart boundary ให้<br>ลิงก์รูปใช้ <code>photo_urls[]</code> ไม่เกิน 10 URL ต่อคำขอ ส่วน <code>delete_photo_ids[]</code> ใช้ ID จาก photos ใน response และลบได้เฉพาะรูปของรายการนั้น การแสดงไฟล์อัปโหลดต้องตั้งค่า <code>php artisan storage:link</code> ครั้งแรก</div>
    </section>

    @php
        $apiHelp = [
            'AuthController@register' => 'สมัครสมาชิก: จำเป็น name, email, password (อย่างน้อย 8 ตัว), password_confirmation; phone ไม่บังคับ ได้ Token กลับมา',
            'AuthController@login' => 'เข้าสู่ระบบ: จำเป็น email, password ได้ Token สำหรับ Authorization: Bearer',
            'AuthController@logout' => 'ออกจากระบบ: ไม่ต้องส่ง Body เพิกถอน Token ที่ใช้ในคำขอนี้',
            'AuthController@me' => 'ดูข้อมูลบัญชีของ Token ปัจจุบัน',
            'PetController@index' => 'ดูสัตว์ทั้งหมดแบบแบ่งหน้า กรอง q, species_id, breed_id, gender=male/female/unknown; per_page=1–100 (ค่าเริ่มต้น 15), page',
            'PetController@show' => 'ดูสัตว์ตาม ID พร้อมชนิด สายพันธุ์ รูปภาพ และชื่อเจ้าของ',
            'PetController@store' => 'เพิ่มสัตว์: จำเป็น species_id, name; ไม่บังคับ breed_id, color, gender=male/female/unknown, age=0–50, description และรูปภาพ ระบบกำหนดเจ้าของจาก Token',
            'PetController@update' => 'แก้สัตว์: ส่งเฉพาะช่องที่ต้องการแก้จากรายการเพิ่มสัตว์ได้ รวมทั้งรูปภาพ เฉพาะเจ้าของหรือ admin; breed_id ต้องตรงกับ species_id',
            'PetController@destroy' => 'ลบสัตว์พร้อมรูปภาพ เฉพาะเจ้าของหรือ admin; ไม่ต้องส่ง Body',
            'ReportController@index' => 'ดูประกาศแบบแบ่งหน้า กรอง type=lost/found, status=open/closed, species_id, q; per_page=1–100 (ค่าเริ่มต้น 15), page',
            'ReportController@show' => 'ดูประกาศตาม ID พร้อมรูปภาพ สัตว์ที่อ้างอิง และข้อมูลพื้นที่',
            'ReportController@store' => 'เพิ่มประกาศ: จำเป็น species_id, type=lost/found, title, event_at (เช่น 2026-10-16 14:30:00), contact_phone; ไม่บังคับ pet_id, breed_id, color, description, subdistrict_id, place, latitude (-90 ถึง 90), longitude (-180 ถึง 180), status=open/closed และรูปภาพ; pet_id ต้องเป็นสัตว์ของตัวเอง ยกเว้น admin',
            'ReportController@update' => 'แก้ประกาศ: ส่งเฉพาะช่องที่ต้องการแก้จากรายการเพิ่มประกาศ รวมทั้งรูปภาพ เฉพาะเจ้าของหรือ admin; ใช้ status=closed เพื่อปิดประกาศ',
            'ReportController@destroy' => 'ลบประกาศพร้อมรูปภาพ เฉพาะเจ้าของหรือ admin; ไม่ต้องส่ง Body',
            'CatalogController@species' => 'ดูชนิดสัตว์ทั้งหมด',
            'CatalogController@speciesShow' => 'ดูชนิดสัตว์ตาม ID พร้อมสายพันธุ์ในชนิดนั้น',
            'CatalogController@breeds' => 'ดูสายพันธุ์ทั้งหมด กรอง species_id ได้',
            'CatalogController@breedShow' => 'ดูสายพันธุ์ตาม ID พร้อมชนิดสัตว์',
            'CatalogController@provinces' => 'ดูจังหวัดทั้งหมด',
            'CatalogController@provinceShow' => 'ดูจังหวัดตาม ID พร้อมอำเภอ',
            'CatalogController@districts' => 'ดูอำเภอ กรอง province_id ได้; ส่ง per_page=1–100 และ page เพื่อแบ่งหน้า ถ้าไม่ส่ง per_page จะคืนทั้งหมด',
            'CatalogController@districtShow' => 'ดูอำเภอตาม ID พร้อมจังหวัดและตำบล',
            'CatalogController@subdistricts' => 'ดูตำบล กรอง district_id ได้; ส่ง per_page=1–100 และ page เพื่อแบ่งหน้า ถ้าไม่ส่ง per_page จะคืนทั้งหมด',
            'CatalogController@subdistrictShow' => 'ดูตำบลตาม ID พร้อมอำเภอและจังหวัด',
            'CatalogController@storeSpecies' => 'เพิ่มชนิดสัตว์: จำเป็น name ความยาวไม่เกิน 100 ตัว และชื่อต้องไม่ซ้ำ',
            'CatalogController@updateSpecies' => 'แก้ชนิดสัตว์: จำเป็น name ความยาวไม่เกิน 100 ตัว และชื่อต้องไม่ซ้ำ',
            'CatalogController@destroySpecies' => 'ลบชนิดสัตว์: ไม่ต้องส่ง Body ลบไม่ได้ถ้ามีสัตว์หรือประกาศใช้งานอยู่',
            'CatalogController@storeBreed' => 'เพิ่มสายพันธุ์: จำเป็น species_id, name (ไม่เกิน 100 ตัว) ชื่อภายในชนิดเดียวกันต้องไม่ซ้ำ',
            'CatalogController@updateBreed' => 'แก้สายพันธุ์: ส่ง species_id และ/หรือ name (ไม่เกิน 100 ตัว) ชื่อภายในชนิดเดียวกันต้องไม่ซ้ำ',
            'CatalogController@destroyBreed' => 'ลบสายพันธุ์: ไม่ต้องส่ง Body ลบไม่ได้ถ้ามีสัตว์หรือประกาศใช้งานอยู่',
            'UserController@index' => 'ดูผู้ใช้แบบแบ่งหน้า กรอง q (ชื่อหรืออีเมล), role=user/admin; เลือก per_page และ page ได้',
            'UserController@show' => 'ดูผู้ใช้ตาม ID พร้อมจำนวนสัตว์ของผู้ใช้',
            'UserController@update' => 'แก้ผู้ใช้: ส่ง name, phone และ/หรือ role=user/admin; ห้ามส่ง role เมื่อแก้บัญชีตัวเอง API นี้ไม่รองรับการเปลี่ยนอีเมลหรือรหัสผ่าน',
            'UserController@destroy' => 'ลบผู้ใช้พร้อมสัตว์ ประกาศ และรูปภาพที่เป็นเจ้าของ ห้ามลบบัญชีตัวเอง; ไม่ต้องส่ง Body',
        ];
        $apiRoutes = collect(\Illuminate\Support\Facades\Route::getRoutes()->getRoutes())
            ->filter(fn ($route) => str_starts_with($route->uri(), 'api/'));
        $apiGroups = $apiRoutes->groupBy(function ($route) {
            if (in_array('admin', $route->gatherMiddleware(), true)) return 'จัดการข้อมูลสำหรับแอดมิน';
            $controller = class_basename($route->getControllerClass());
            return [
                'AuthController' => 'บัญชีและ Token',
                'PetController' => 'สัตว์เลี้ยง',
                'ReportController' => 'ประกาศสัตว์หาย / พบสัตว์',
                'CatalogController' => 'ชนิดสัตว์ สายพันธุ์ และข้อมูลพื้นที่',
            ][$controller] ?? 'API อื่น ๆ';
        });
    @endphp
    <section class="step" id="all-api">
        <span class="step-no">API</span>
        <h2>รายการ API ทั้งหมดที่เปิดใช้งาน ({{ $apiRoutes->count() }} เส้นทาง)</h2>
        <p class="meta">ทุก Path ด้านล่างต่อจาก Base URL ด้านบน เปลี่ยนค่าภายในวงเล็บปีกกาเป็น ID จริง ปุ่มคัดลอกใช้ ID 1 เป็นตัวอย่าง PUT และ PATCH ใช้ URL เดียวกัน</p>
        <div class="cond"><b>รูปแบบคำขอ:</b> ส่ง <code>Accept: application/json</code> ทุกครั้ง สำหรับ JSON ตั้ง <code>Content-Type: application/json</code> ด้วย ส่วนคำขอแนบไฟล์ใช้ Form ตามวิธีเพิ่มรูปภาพด้านบน<br><b>ชื่อเส้นทางสัตว์:</b> <code>/pet</code> และ <code>/pets</code> ใช้งานแทนกันได้ครบทุก method ส่วนประกาศใช้ <code>/reports</code></div>
        @foreach ($apiGroups as $groupName => $groupRoutes)
            <h3>{{ $groupName }}</h3>
            <div class="table-wrap" role="region" aria-label="{{ $groupName }}" tabindex="0">
                <table class="tbl api-table">
                    <thead><tr><th>Method</th><th>Path</th><th>ข้อมูลและเงื่อนไข</th><th>สิทธิ์</th><th>คัดลอก</th></tr></thead>
                    <tbody>
                        @foreach ($groupRoutes as $apiRoute)
                            @php
                                $methods = array_values(array_diff($apiRoute->methods(), ['HEAD']));
                                $path = '/' . $apiRoute->uri();
                                $examplePath = preg_replace('/\{[^}]+\}/', '1', $path);
                                $helpKey = class_basename($apiRoute->getControllerClass()) . '@' . $apiRoute->getActionMethod();
                                $middleware = $apiRoute->gatherMiddleware();
                                $isAdminApi = in_array('admin', $middleware, true);
                                $isPrivateApi = in_array('auth:sanctum', $middleware, true);
                            @endphp
                            <tr data-api-route="{{ $apiRoute->uri() }}">
                                <td><span class="m-{{ $methods[0] }}">{{ implode('/', $methods) }}</span></td>
                                <td><code>{{ '/' . preg_replace('#^api/#', '', $apiRoute->uri()) }}</code></td>
                                <td>{{ $apiHelp[$helpKey] ?? 'ดูรายละเอียดจาก API response' }}</td>
                                <td><span class="{{ $isPrivateApi ? 'badge-lock' : 'badge-free' }}">{{ $isAdminApi ? 'Token ของ admin' : ($isPrivateApi ? 'ต้องมี token' : 'ไม่ต้องมี token') }}</span></td>
                                <td><button class="btn ghost sm" onclick="copyText('{{ url($examplePath) }}', this)">คัดลอก URL</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </section>

    <section class="step">
        <span class="step-no">4</span>
        <h2>Step 4 — ตัวอย่างการเรียกใช้งาน (cURL)</h2>
        
        <div class="code-title">
            <strong>1. Login (เก็บ Token)</strong>
            <button class="btn ghost sm" onclick="copyJson('curl-login', this)">คัดลอก</button>
        </div>
        <pre class="json" id="curl-login">
$base = '{{ url('/api') }}'
curl.exe -X POST "$base/login" -H "Accept: application/json" -H "Content-Type: application/json" -d '{"email":"your-email@example.com","password":"your-password"}'
        </pre>

        <div class="code-title">
            <strong>2. Get All Pets</strong>
            <button class="btn ghost sm" onclick="copyJson('curl-all', this)">คัดลอก</button>
        </div>
        <pre class="json" id="curl-all">
$token = 'วาง token ที่ได้จาก Login'
curl.exe "$base/pet" -H "Accept: application/json" -H "Authorization: Bearer $token"
        </pre>

        <div class="code-title">
            <strong>3. Insert Pet</strong>
            <button class="btn ghost sm" onclick="copyJson('curl-insert', this)">คัดลอก</button>
        </div>
        <pre class="json" id="curl-insert">
curl.exe -X POST "$base/pet" -H "Accept: application/json" -H "Authorization: Bearer $token" -F "species_id=1" -F "name=มะลิ" -F "color=ขาว"
        </pre>

        <div class="code-title">
            <strong>4. Update Pet (ใช้ _method=PUT)</strong>
            <button class="btn ghost sm" onclick="copyJson('curl-update', this)">คัดลอก</button>
        </div>
        <pre class="json" id="curl-update">
$petId = '1'
curl.exe -X POST "$base/pet/$petId" -H "Accept: application/json" -H "Authorization: Bearer $token" -F "_method=PUT" -F "name=มะลิใหม่"
        </pre>

        <div class="code-title">
            <strong>5. Delete Pet</strong>
            <button class="btn ghost sm" onclick="copyJson('curl-delete', this)">คัดลอก</button>
        </div>
        <pre class="json" id="curl-delete">
curl.exe -X DELETE "$base/pet/$petId" -H "Accept: application/json" -H "Authorization: Bearer $token"
        </pre>

        <h3>รหัสตอบกลับ (HTTP Status Codes)</h3>
        <div class="table-wrap" role="region" aria-label="ตารางเอกสาร API" tabindex="0">
        <table class="tbl api-table">
            <tr><th>รหัส</th><th>ความหมาย</th></tr>
            <tr><td><b>200</b></td><td>สำเร็จ (Login, อ่าน, แก้ไข, Logout)</td></tr>
            <tr><td><b>201</b></td><td>สร้างข้อมูลสำเร็จ (เพิ่มสัตว์, สมัครสมาชิก)</td></tr>
            <tr><td><b>401</b></td><td>ไม่มี Token, Token หมดอายุ หรือ Login ไม่ผ่าน</td></tr>
            <tr><td><b>403</b></td><td>ไม่มีสิทธิ์ (เช่น พยายามแก้ข้อมูลของคนอื่น)</td></tr>
            <tr><td><b>404</b></td><td>ไม่พบข้อมูล</td></tr>
            <tr><td><b>422</b></td><td>ข้อมูลส่งมาผิดพลาดหรือไม่ครบถ้วน</td></tr>
        </table>
        </div>

        <h3>JSON ตัวอย่างจากข้อมูลสัตว์ในฐานข้อมูล</h3>
        <pre class="json">{{ $petJson }}</pre>
    </section>

    @auth
        <script>
            document.getElementById('create-api-token').addEventListener('submit', async function (event) {
                event.preventDefault();
                const form = event.currentTarget;
                const button = form.querySelector('button[type="submit"]');
                const status = document.getElementById('token-status');
                
                button.disabled = true;
                status.textContent = 'กำลังสร้าง Token...';

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
                        },
                        credentials: 'same-origin'
                    });
                    const result = await response.json();

                    if (!response.ok) throw new Error(result.message || 'สร้าง Token ไม่สำเร็จ');

                    document.getElementById('api-token').value = result.token;
                    document.getElementById('token-result').hidden = false;
                    status.textContent = result.message;
                } catch (error) {
                    status.textContent = error.message || 'เชื่อมต่อไม่ได้ กรุณาลองใหม่';
                } finally {
                    button.disabled = false;
                }
            });
        </script>
    @endauth
@endsection
