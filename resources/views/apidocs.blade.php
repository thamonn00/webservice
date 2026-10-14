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
        
        <div class="cond"><b>เงื่อนไข Header:</b> ใส่ <code>Accept: application/json</code> ทุก request และ <code>Authorization: Bearer &lt;token&gt;</code> ทุกเส้น (ยกเว้น <code>/register</code> กับ <code>/login</code>)</div>
        <div class="cond"><b>หาก Login ได้ 401:</b> อย่าใช้ admin@example.com เว้นแต่มีใน db แนะนำให้ใช้ somchai@example.com / password123 สำหรับทดสอบ local</div>
    </section>

    <section class="step">
        <span class="step-no">2</span>
        <h2>Step 2 — ค้นหาข้อมูล GET (ข้อ 1.3)</h2>
        
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
    </section>

    <section class="step">
        <span class="step-no">3</span>
        <h2>Step 3 — จัดการข้อมูล POST, PUT, DELETE (ข้อ 1.4)</h2>
        
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
        
        <div class="cond"><b>สิทธิ์:</b> ระบบกำหนด <code>user_id</code> เป็นผู้ยิง request ให้เอง ตอนแก้/ลบต้องเป็นเจ้าของหรือ admin เท่านั้น ถ้าแก้/ลบได้ 403 แปลว่าไม่มีสิทธิ์</div>
        <div class="cond"><b>ส่งรูป:</b> ใช้ <code>photos[]</code> (ไฟล์), <code>photo_urls[]</code> (URL รูป) และ <code>delete_photo_ids[]</code> (ลบรูปเดิม)<br>หากแก้ข้อมูลพร้อมแนบไฟล์ให้ใช้ <code>POST</code> พร้อม <code>_method=PUT</code> (multipart/form-data)</div>
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
        <table class="tbl api-table">
            <tr><th>รหัส</th><th>ความหมาย</th></tr>
            <tr><td><b>200</b></td><td>สำเร็จ (Login, อ่าน, แก้ไข, Logout)</td></tr>
            <tr><td><b>201</b></td><td>สร้างข้อมูลสำเร็จ (เพิ่มสัตว์, สมัครสมาชิก)</td></tr>
            <tr><td><b>401</b></td><td>ไม่มี Token, Token หมดอายุ หรือ Login ไม่ผ่าน</td></tr>
            <tr><td><b>403</b></td><td>ไม่มีสิทธิ์ (เช่น พยายามแก้ข้อมูลของคนอื่น)</td></tr>
            <tr><td><b>404</b></td><td>ไม่พบข้อมูล</td></tr>
            <tr><td><b>422</b></td><td>ข้อมูลส่งมาผิดพลาดหรือไม่ครบถ้วน</td></tr>
        </table>

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