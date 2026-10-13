@extends('layouts.app')
@section('title', 'จัดการผู้ใช้')
@section('content')
    <div class="page-head">
        <div>
            <h1>ผู้ใช้</h1>
            <p>สมาชิกทั้งหมด {{ $users->total() }} คน · เฉพาะแอดมิน</p>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert err">{{ implode("\n", $errors->all()) }}</div>
    @endif

    <form class="filters" method="GET">
        <input class="search" type="search" name="q" placeholder="ค้นหาชื่อหรืออีเมล…" value="{{ request('q') }}">
        <select name="role" onchange="this.form.submit()">
            <option value="">ทุกสิทธิ์</option>
            <option value="user" @selected(request('role') === 'user')>user</option>
            <option value="admin" @selected(request('role') === 'admin')>admin</option>
        </select>
        <button class="btn ghost">ค้นหา</button>
        @if (request()->hasAny(['q', 'role']))
            <a class="btn ghost" href="{{ route('admin.users.index') }}">ล้าง</a>
        @endif
    </form>

    <div class="card">
        <div class="table-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>ผู้ใช้</th>
                        <th>เบอร์โทร</th>
                        <th>สิทธิ์</th>
                        <th style="text-align:right">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $u)
                        <tr>
                            <td>
                                <div class="user">
                                    <span class="avatar">{{ mb_substr($u->name, 0, 1) }}</span>
                                    <div><b>{{ $u->name }}</b><span class="meta">{{ $u->email }} · #{{ $u->id }}</span></div>
                                </div>
                            </td>
                            <td>{{ $u->phone ?? '-' }}</td>
                            <td><span class="chip {{ $u->role === 'admin' ? 'admin' : '' }}">{{ $u->role }}</span></td>
                            <td>
                                <div class="acts">
                                    <a class="btn ghost sm" href="{{ route('admin.users.edit', $u) }}">แก้ไข</a>
                                    @if ((int) $u->id === (int) auth()->id())
                                        <span class="meta" style="align-self:center">บัญชีของคุณ</span>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                            onsubmit="return confirm('ลบ {{ $u->email }} ?\nสัตว์และประกาศของผู้ใช้นี้จะถูกลบด้วย')">
                                            @csrf @method('DELETE')
                                            <button class="btn danger sm">ลบ</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="empty">ไม่พบผู้ใช้</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $users->links() }}
    </div>
@endsection
