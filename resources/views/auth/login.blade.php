@extends('layouts.app')
@section('title', 'เข้าสู่ระบบ')
@section('content')
    <div class="auth">
        <div class="card">
            <div class="bd">
                <div class="logo">🐾</div>
                <h1>เข้าสู่ระบบ</h1>
                <p class="sub">ระบบแจ้งสัตว์หาย / พบสัตว์</p>
                @if ($errors->any())
                    <div class="alert err">{{ implode("\n", $errors->all()) }}</div>
                @endif
                <form class="form" method="POST" action="{{ route('login') }}">@csrf
                    <label class="field">อีเมล<input type="email" name="email" value="{{ old('email') }}" required autofocus></label>
                    <label class="field">รหัสผ่าน<input type="password" name="password" required></label>
                    <label class="check"><input type="checkbox" name="remember" value="1"> จดจำฉันไว้ในเครื่องนี้</label>
                    <button class="btn block">เข้าสู่ระบบ</button>
                </form>
                <p class="alt">ยังไม่มีบัญชี? <a href="{{ route('register') }}">สมัครสมาชิก</a></p>
            </div>
        </div>
    </div>
@endsection
