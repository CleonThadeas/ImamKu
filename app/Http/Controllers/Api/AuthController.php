<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\Api\UserResource;
use App\Http\Traits\ApiResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * POST /api/login
     * Login dan dapatkan token Sanctum.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        // Validasi kredensial
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return $this->error('Email atau password salah.', 401);
        }

        // Hanya imam yang boleh login via API mobile
        if ($user->role !== 'imam') {
            return $this->error('Akun ini tidak memiliki akses ke aplikasi mobile.', 403);
        }

        // Cek status aktif
        if (! $user->is_active) {
            return $this->error('Akun Anda telah dinonaktifkan oleh admin.', 403);
        }

        // Buat token
        $deviceName = $request->device_name ?? 'mobile-app';
        $token = $user->createToken($deviceName)->plainTextToken;

        return $this->success([
            'user'  => new UserResource($user),
            'token' => $token,
        ], 'Login berhasil');
    }

    /**
     * POST /api/logout
     * Revoke token saat ini.
     */
    public function logout(Request $request): JsonResponse
    {
        // Hapus token yang digunakan saat ini
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logout berhasil');
    }

    /**
     * GET /api/user
     * Ambil data profil user yang sedang login.
     */
    public function user(Request $request): JsonResponse
    {
        return $this->success(
            new UserResource($request->user()),
            'Data user berhasil diambil'
        );
    }
}
