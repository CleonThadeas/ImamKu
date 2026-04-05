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
        $maxImams = config('imamku.max_imams', 5);
        return view('admin.imams.index', compact('imams', 'maxImams'));
    }

    public function create()
    {
        $currentCount = User::where('role', 'imam')->count();
        $maxImams = config('imamku.max_imams', 5);

        if ($currentCount >= $maxImams) {
            return redirect()->route('admin.imams.index')
                ->with('error', "Maksimal {$maxImams} imam telah tercapai.");
        }

        return view('admin.imams.create');
    }

    public function store(Request $request)
    {
        $currentCount = User::where('role', 'imam')->count();
        $maxImams = config('imamku.max_imams', 5);

        if ($currentCount >= $maxImams) {
            return redirect()->route('admin.imams.index')
                ->with('error', "Maksimal {$maxImams} imam telah tercapai.");
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
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
            'email' => 'required|email|unique:users,email,' . $imam->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ];

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
