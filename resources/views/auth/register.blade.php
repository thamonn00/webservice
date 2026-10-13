@extends('layouts.app')
@section('title', 'สมัครสมาชิก')
@section('content')
    <div class="auth">
        <div class="card">
            <div class="bd">
                <div class="logo">🐾</div>
                <h1>สมัครสมาชิก</h1>
                <p class="sub">สร้างบัญชีเพื่อดูข้อมูลและรับ API Token</p>
                @if ($errors->any())
                    <div class="alert err">{{ implode("\n", $errors->all()) }}</div>
                @endif
                <form class="form" method="POST" action="{{ route('register') }}">@csrf
                    <label class="field">ชื่อ-นามสกุล<input type="text" name="name" value="{{ old('name') }}" required autofocus></label>
                    <label class="field">อีเมล<input type="email" name="email" value="{{ old('email') }}" required></label>
                    <label class="field">เบอร์โทร (ไม่บังคับ)<input type="text" name="phone" value="{{ old('phone') }}"></label>
                    <label class="field">รหัสผ่าน (อย่างน้อย 8 ตัว)<input type="password" name="password" required></label>
                    <label class="field">ยืนยันรหัสผ่าน<input type="password" name="password_confirmation" required></label>
                    <button class="btn block">สมัครสมาชิก</button>
                </form>
                <p class="alt">มีบัญชีแล้ว? <a href="{{ route('login') }}">เข้าสู่ระบบ</a></p>
            </div>
        </div>
    </div>
@endsection
