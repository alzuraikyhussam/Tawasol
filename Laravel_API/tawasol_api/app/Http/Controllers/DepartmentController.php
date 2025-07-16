<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    use ApiResponseTrait;

    /**
     * جميع الإدارات مع عدد المستخدمين
     */
    public function index()
    {
        $departments = Department::withCount('users')->get();

        return $this->success($departments, 'قائمة الإدارات مع عدد المستخدمين');
    }

    /**
     * إضافة إدارة جديدة
     */
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $validated = $request->validate([
                'name' => 'required|string|max:100|unique:departments,name',
            ]);

            $department = Department::create($validated);

            return $this->success($department, 'تمت إضافة الإدارة بنجاح', 201);
        });
    }

    /**
     * تفاصيل إدارة
     */
    public function show($id)
    {
        $department = Department::with('users')->find($id);

        if (!$department) {
            return $this->notFound('الإدارة غير موجودة');
        }

        return $this->success($department, 'تفاصيل الإدارة مع المستخدمين');
    }

    /**
     * تعديل إدارة
     */
    public function update(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $department = Department::find($id);

            if (!$department) {
                return $this->notFound('الإدارة غير موجودة');
            }

            $validated = $request->validate([
                'name' => 'required|string|max:100|unique:departments,name,' . $department->id,
            ]);

            $department->update($validated);

            return $this->success($department, 'تم تحديث الإدارة بنجاح');
        });
    }

    /**
     * حذف إدارة
     */
    public function destroy($id)
    {
        $department = Department::find($id);

        if (!$department) {
            return $this->notFound('الإدارة غير موجودة');
        }

        $department->delete();

        return $this->success(null, 'تم حذف الإدارة بنجاح');
    }

    /**
     * المستخدمين ضمن إدارة معينة
     */
    public function users($id)
    {
        $department = Department::find($id);

        if (!$department) {
            return $this->notFound('الإدارة غير موجودة');
        }

        $users = $department->users()->with('department')->get();

        return $this->success($users, 'قائمة المستخدمين ضمن الإدارة');
    }

    /**
     * الإدارات التي لديها مستخدمين نشطين فقط
     */
    public function activeDepartments()
    {
        $departments = Department::whereHas('users', function ($q) {
            $q->where('is_active', true);
        })->withCount('users')->get();

        return $this->success($departments, 'الإدارات مع مستخدمين نشطين فقط');
    }
}
