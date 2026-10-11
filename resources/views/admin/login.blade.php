@extends('admin.layout')

@section('title', 'เข้าสู่ระบบแอดมิน')

@section('content')
<main class="login-wrap">
    <section class="login-card">
        <div class="eyebrow">ADMINISTRATOR</div>
        <h1>เข้าสู่ระบบผู้ดูแล</h1>
        <p class="muted">สำหรับผู้ดูแลระบบเท่านั้น ผู้ใช้ทั่วไปสามารถเรียก API ไปออกแบบแอปของตนเองได้</p>

        @if ($errors->any())
            <div class="alert error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login.store') }}">
            @csrf
            <div class="field" style="margin:16px 0">
                <label for="email">อีเมล</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            </div>
            <div class="field">
                <label for="password">รหัสผ่าน</label>
                <input id="password" name="password" type="password" required autocomplete="current-password">
            </div>
            <button class="button primary" type="submit">เข้าสู่ระบบ</button>
        </form>
    </section>
</main>
@endsection
