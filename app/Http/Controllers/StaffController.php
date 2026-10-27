<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ])['search'] ?? '');
        $staff = Staff::when($search !== '', function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        })->orderBy('name')->get();

        return view('staff.index', compact('staff', 'search'));
    }

    public function create()
    {
        return view('staff.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:staff,name'],
        ]);

        Staff::create($validated);

        return redirect()->route('staff.index')->with('success', 'Staf berhasil ditambahkan.');
    }

    public function edit(Staff $staff)
    {
        return view('staff.edit', compact('staff'));
    }

    public function update(Request $request, Staff $staff)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('staff', 'name')->ignore($staff->id)],
            'is_active' => ['required', 'boolean'],
        ]);

        $staff->update($validated);

        return redirect()->route('staff.index')->with('success', 'Data staf berhasil diperbarui.');
    }

    public function destroy(Staff $staff)
    {
        $staff->delete();

        return redirect()->route('staff.index')->with('success', 'Staf berhasil dihapus. Riwayat transaksi tetap tersimpan.');
    }
}
