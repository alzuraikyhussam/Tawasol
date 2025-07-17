<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponseTrait;

    /**
     * عرض كل المستخدمين
     */
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->search, function ($query) use ($request) {
                $query->where('username', 'like', "%{$request->search}%")
                    ->orWhere('full_name', 'like', "%{$request->search}%");
            })
            ->with('department')
            ->paginate(20);

        return $this->success($users, 'قائمة المستخدمين');
    }

    /**
     * إضافة مستخدم جديد
     */
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $validated = $request->validate([
                'username' => 'required|string|max:50|unique:users,username',
                'full_name' => 'required|string|max:100',
                'password' => 'required|string|min:6|confirmed',
                'department_id' => 'nullable|exists:departments,id',
                'is_active' => 'sometimes|boolean',
            ]);

            $user = User::create([
                'username' => $validated['username'],
                'full_name' => $validated['full_name'],
                'password_hash' => Hash::make($validated['password']),
                'department_id' => $validated['department_id'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
            ]);

            return $this->success($user, 'تم إضافة المستخدم بنجاح', 201);
        });
    }

    /**
     * تفاصيل مستخدم واحد
     */
    public function show($id)
    {
        $user = User::with('department')->find($id);

        if (!$user) {
            return $this->notFound('المستخدم غير موجود');
        }

        return $this->success($user, 'تفاصيل المستخدم');
    }

    /**
     * تعديل مستخدم
     */
    public function update(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $user = User::find($id);

            if (!$user) {
                return $this->notFound('المستخدم غير موجود');
            }

            $validated = $request->validate([
                'full_name' => 'sometimes|string|max:100',
                'password' => 'sometimes|string|min:6|confirmed',
                'department_id' => 'sometimes|nullable|exists:departments,id',
                'profile_image_url' => 'sometimes|nullable|string',
                'is_active' => 'sometimes|boolean',
            ]);

            if (isset($validated['password'])) {
                $validated['password_hash'] = Hash::make($validated['password']);
                unset($validated['password'], $validated['password_confirmation']);
            }

            $user->update($validated);

            return $this->success($user, 'تم تحديث المستخدم بنجاح');
        });
    }

    /**
     * حذف مستخدم
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->notFound('المستخدم غير موجود');
        }

        $user->delete();

        return $this->success(null, 'تم حذف المستخدم بنجاح');
    }

    /**
     * تغيير حالة التفعيل
     */
    public function toggleStatus($id)
    {
        $user = User::find($id);

        if (!$user) {
            return $this->notFound('المستخدم غير موجود');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return $this->success($user, 'تم تغيير حالة المستخدم');
    }

    /**
     * تغيير كلمة المرور من لوحة التحكم
     */
    public function changePassword(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $user = User::find($id);

            if (!$user) {
                return $this->notFound('المستخدم غير موجود');
            }

            $validated = $request->validate([
                'password' => 'required|string|min:6|confirmed',
            ]);

            $user->update([
                'password_hash' => Hash::make($validated['password']),
            ]);

            return $this->success(null, 'تم تغيير كلمة المرور بنجاح');
        });
    }
}
