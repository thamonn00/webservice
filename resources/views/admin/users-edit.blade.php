@extends('layouts.app')
@section('title', 'แก้ไขผู้ใช้ #' . $user->id)
@section('content')
    @php $self = (int) $user->id === (int) auth()->id(); @endphp
    <div class="crumb"><a href="{{ route('admin.users.index') }}">ผู้ใช้</a> / แก้ไข #{{ $user->id }}</div>
    <div class="page-head">
        <div class="title-row" style="margin:0">
            <span class="avatar" style="width:48px;height:48px;font-size:1.2rem">{{ mb_substr($user->name, 0, 1) }}</span>
            <div>
                <h1 style="font-size:1.5rem">{{ $user->name }}</h1>
                <div class="meta">{{ $user->email }}</div>
            </div>
        </div>
    </div>

    <div class="card" style="max-width:560px">
        <div class="bd">
            @if ($errors->any())
                <div class="alert err">{{ implode("\n", $errors->all()) }}</div>
            @endif
            <form class="form" method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf @method('PATCH')
                <label class="field">ชื่อ-นามสกุล<input type="text" name="name" value="{{ old('name', $user->name) }}" required></label>
                <label class="field">อีเมล<input type="email" name="email" value="{{ old('email', $user->email) }}" required></label>
                <label class="field">เบอร์โทร<input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="-"></label>
                <label class="field">สิทธิ์
                    <select name="role" @disabled($self)>
                        <option value="user" @selected(old('role', $user->role) === 'user')>user</option>
                        <option value="admin" @selected(old('role', $user->role) === 'admin')>admin</option>
                    </select>
                    @if ($self)
                        <input type="hidden" name="role" value="{{ $user->role }}">
                        <span class="meta" style="font-weight:400">บัญชีของคุณเอง: เปลี่ยนสิทธิ์ตัวเองไม่ได้</span>
                    @endif
                </label>
                <div class="toolbar" style="margin:8px 0 0">
                    <button class="btn">บันทึก</button>
                    <a class="btn ghost" href="{{ route('admin.users.index') }}">ยกเลิก</a>
                </div>
            </form>
        </div>
    </div>
@endsection
