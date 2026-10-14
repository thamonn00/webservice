<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Breed;
use App\Models\Pet;
use App\Models\Report;
use App\Models\Species;
use Illuminate\Http\Request;

class PetWebController extends Controller
{
    public function index(Request $request)
    {
        $q = Pet::with(['species', 'breed', 'photos', 'user:id,name'])
            ->when($request->filled('q'), function ($x) use ($request) {
                $t = '%' . addcslashes($request->q, '%_\\') . '%';
                $x->where(fn($i) => $i->where('name', 'like', $t)->orWhere('color', 'like', $t));
            })
            ->when($request->filled('species_id'), fn($x) => $x->where('species_id', $request->species_id))
            ->when($request->filled('breed_id'), fn($x) => $x->where('breed_id', $request->breed_id))
            ->when($request->boolean('mine'), fn($x) => $x->where('user_id', auth()->id()))
            ->latest('id')->get();

        return view('pets.index', [
            'pets' => $q,
            'species' => Species::orderBy('id')->get(),
            'breeds' => Breed::orderBy('id')->get(),
        ]);
    }








    public function show(Pet $pet)
    {
        $pet->load(['species', 'breed', 'photos', 'user:id,name']);
        $json = $pet->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return view('pets.show', compact('pet', 'json'));
    }

    // ---------- หน้าเว็บประกาศหาย/พบ: ใช้ controller ไฟล์เดิม ไม่สร้าง controller ใหม่ ----------
    public function reportsIndex(Request $request)
    {
        $request->validate([
            'type' => ['sometimes', 'in:lost,found'],
            'status' => ['sometimes', 'in:open,closed'],
        ]);

        $reports = Report::with(['species', 'breed', 'photos', 'user:id,name', 'pet:id,name', 'subdistrict.district.province'])
            ->when($request->filled('q'), function ($x) use ($request) {
                $t = '%' . addcslashes($request->q, '%_\\') . '%';
                $x->where(fn($i) => $i->where('title', 'like', $t)->orWhere('color', 'like', $t));
            })
            ->when($request->filled('type'), fn($x) => $x->where('type', $request->type))
            ->when($request->filled('status'), fn($x) => $x->where('status', $request->status))
            ->when($request->filled('species_id'), fn($x) => $x->where('species_id', $request->species_id))
            ->latest('id')->get();

        return view('reports.index', [
            'reports' => $reports,
            'species' => Species::orderBy('id')->get(),
        ]);
    }

    public function reportsShow(Report $report)
    {
        $report->load(['species', 'breed', 'photos', 'user:id,name', 'pet:id,name', 'subdistrict.district.province']);
        $json = $report->toJson(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return view('reports.show', compact('report', 'json'));
    }

    // ---------- หน้าเว็บแอดมิน: ใช้ไฟล์เดิม ไม่สร้าง controller ใหม่ ----------
    public function adminUsers(Request $request)
    {
        $users = \App\Models\User::query()
            ->when($request->filled('q'), function ($x) use ($request) {
                $t = '%' . addcslashes($request->q, '%_\\') . '%';
                $x->where(fn($i) => $i->where('name', 'like', $t)->orWhere('email', 'like', $t));
            })
            ->when($request->filled('role'), fn($x) => $x->where('role', $request->role))
            ->latest('id')->paginate(15);
        $users->withQueryString();

        return view('admin.users', compact('users'));
    }

    public function adminUsersEdit(\App\Models\User $user)
    {
        return view('admin.users-edit', compact('user'));
    }



    public function adminUsersUpdate(Request $request, \App\Models\User $user)
    {
        $isSelf = (int) $user->id === (int) auth()->id();

        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'role'  => ['required', 'in:user,admin'],
        ]);

        if ($isSelf && $data['role'] !== $user->role) {
            return back()->withErrors(['role' => 'ไม่สามารถเปลี่ยน role ของตัวเองได้'])->withInput();
        }

        $user->update($data);

        return redirect()->route('admin.users.edit', $user)->with('ok', 'บันทึกข้อมูล ' . $user->email . ' แล้ว');
    }

    public function adminUsersDestroy(\App\Models\User $user)
    {
        if ((int) $user->id === (int) auth()->id()) {
            return back()->withErrors(['role' => 'ไม่สามารถลบบัญชีของตัวเองได้']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('ok', 'ลบผู้ใช้แล้ว');
    }
}
