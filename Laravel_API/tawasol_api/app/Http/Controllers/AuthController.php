<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Traits\ApiResponseTrait;

class AuthController extends Controller
{
    use ApiResponseTrait;

    /**
     * تسجيل مستخدم جديد
     */
    public function register(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $validated = $request->validate([
                'username'      => 'required|string|max:50|unique:users,username',
                'full_name'     => 'required|string|max:100',
                'password'      => 'required|string|min:6|confirmed',
                'department_id' => 'nullable|exists:departments,id',
            ]);

            $user = User::create([
                'username'      => $validated['username'],
                'full_name'     => $validated['full_name'],
                'password_hash' => Hash::make($validated['password']),
                'department_id' => $validated['department_id'] ?? null,
                'is_active'     => true,
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->success([
                'user'  => $user,
                'token' => $token,
            ], 'تم التسجيل بنجاح', 201);
        });
    }

    /**
     * تسجيل الدخول
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $validated['username'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password_hash)) {
            return $this->error('بيانات الاعتماد غير صحيحة', 401);
        }

        if (!$user->is_active) {
            return $this->error('الحساب غير مفعل', 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $user->update([
            'is_online' => true,
            'last_seen' => now(),
        ]);

        return $this->success([
            'user'  => $user,
            'token' => $token,
        ], 'تم تسجيل الدخول بنجاح');
    }

    /**
     * تسجيل الخروج
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        $request->user()->update([
            'is_online' => false,
            'last_seen' => now(),
        ]);

        return $this->success([], 'تم تسجيل الخروج بنجاح');
    }

    /**
     * تحديث الملف الشخصي
     */
    public function updateProfile(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $user = $request->user();

            $validated = $request->validate([
                'full_name'         => 'sometimes|string|max:100',
                'password'          => 'sometimes|string|min:6|confirmed',
                'department_id'     => 'sometimes|nullable|exists:departments,id',
                'profile_image_url' => 'sometimes|nullable|string',
                'is_active'         => 'sometimes|boolean',
            ]);

            if (isset($validated['password'])) {
                $validated['password_hash'] = Hash::make($validated['password']);
                unset($validated['password'], $validated['password_confirmation']);
            }

            $user->update($validated);

            return $this->success($user, 'تم تحديث الملف الشخصي بنجاح');
        });
    }
}
