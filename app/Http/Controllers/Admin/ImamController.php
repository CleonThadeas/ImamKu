<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ImamController extends Controller
{
    public function index()
    {
        $imams = User::where('role', 'imam')->latest()->get();
        return view('admin.imams.index', compact('imams'));
    }

    public function create()
    {
        return view('admin.imams.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'unique:users,email', 'regex:/^[a-zA-Z0-9._%+-]+@(gmail\.com|yahoo\.com)$/i'],
            'phone' => ['nullable', 'regex:/^[0-9]+$/', 'min:10', 'max:15'],
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.regex' => 'Email harus menggunakan domain @gmail.com atau @yahoo.com.',
            'phone.regex' => 'Nomor telepon harus berupa angka saja (tanpa spasi, huruf, atau karakter lain).',
            'phone.min' => 'Nomor telepon minimal 10 digit.',
            'phone.max' => 'Nomor telepon maksimal 15 digit.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => bcrypt($validated['password']),
            'role' => 'imam',
            'is_active' => true,
        ]);

        return redirect()->route('admin.imams.index')
            ->with('success', 'Imam berhasil ditambahkan.');
    }

    public function edit(User $imam)
    {
        return view('admin.imams.edit', compact('imam'));
    }

    public function update(Request $request, User $imam)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'unique:users,email,' . $imam->id, 'regex:/^[a-zA-Z0-9._%+-]+@(gmail\.com|yahoo\.com)$/i'],
            'phone' => ['nullable', 'regex:/^[0-9]+$/', 'min:10', 'max:15'],
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'boolean',
        ], [
            'email.regex' => 'Email harus menggunakan domain @gmail.com atau @yahoo.com.',
            'phone.regex' => 'Nomor telepon harus berupa angka saja (tanpa spasi, huruf, atau karakter lain).',
            'phone.min' => 'Nomor telepon minimal 10 digit.',
            'phone.max' => 'Nomor telepon maksimal 15 digit.',
        ]);

        $newIsActive = $request->boolean('is_active', true);
        
        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => $newIsActive,
        ];

        // Jika sebelumnya nonaktif dan sekarang diaktifkan kembali
        if (!$imam->is_active && $newIsActive) {
            $data['penalty_points'] = 0;
            $data['is_restricted'] = false;

            if ($imam->penalty_points < 0) {
                \App\Models\PenaltyLog::create([
                    'user_id' => $imam->id,
                    'event_type' => 'manual_reset',
                    'points' => abs($imam->penalty_points),
                    'description' => 'Poin direset ke 0 oleh Admin karena akun diaktifkan kembali.',
                ]);
            }
        }

        if (!empty($validated['password'])) {
            $data['password'] = bcrypt($validated['password']);
        }

        $imam->update($data);

        return redirect()->route('admin.imams.index')
            ->with('success', 'Data imam berhasil diperbarui.');
    }

    public function destroy(User $imam)
    {
        $imam->delete();
        return redirect()->route('admin.imams.index')
            ->with('success', 'Imam berhasil dihapus.');
    }
}
