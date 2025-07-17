<?php

namespace App\Http\Controllers;

use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ApiResponseTrait;

class UserSettingsController extends Controller
{
    use ApiResponseTrait;

    /**
     * قائمة إعدادات المستخدم الحالي
     */
    public function index(Request $request)
    {
        $settings = UserSetting::where('user_id', $request->user()->id)->get();
        return $this->success($settings, 'قائمة الإعدادات');
    }

    /**
     * تفاصيل إعداد محدد
     */
    public function show(Request $request, $id)
    {
        $setting = UserSetting::where('user_id', $request->user()->id)
                              ->find($id);

        if (!$setting) {
            return $this->notFound('الإعداد غير موجود');
        }

        return $this->success($setting, 'تفاصيل الإعداد');
    }

    /**
     * إضافة إعداد جديد
     */
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $validated = $request->validate([
                'setting_key'   => 'required|string|max:100',
                'setting_value' => 'nullable|string|max:255',
            ]);

            $setting = UserSetting::create([
                'user_id'       => $request->user()->id,
                'setting_key'   => $validated['setting_key'],
                'setting_value' => $validated['setting_value'],
            ]);

            return $this->success($setting, 'تم إضافة الإعداد', 201);
        });
    }

    /**
     * تحديث إعداد موجود
     */
    public function update(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $setting = UserSetting::where('user_id', $request->user()->id)
                                  ->find($id);

            if (!$setting) {
                return $this->notFound('الإعداد غير موجود');
            }

            $validated = $request->validate([
                'setting_value' => 'nullable|string|max:255',
            ]);

            $setting->update($validated);

            return $this->success($setting, 'تم تحديث الإعداد');
        });
    }

    /**
     * حذف إعداد
     */
    public function destroy(Request $request, $id)
    {
        $setting = UserSetting::where('user_id', $request->user()->id)
                              ->find($id);

        if (!$setting) {
            return $this->notFound('الإعداد غير موجود');
        }

        $setting->delete();

        return $this->success(null, 'تم حذف الإعداد');
    }

    /**
     * إضافة أو تحديث إعداد (Upsert)
     */
    public function upsertSetting(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $validated = $request->validate([
                'setting_key'   => 'required|string|max:100',
                'setting_value' => 'nullable|string|max:255',
            ]);

            $setting = UserSetting::updateOrCreate(
                [
                    'user_id'     => $request->user()->id,
                    'setting_key' => $validated['setting_key'],
                ],
                [
                    'setting_value' => $validated['setting_value'],
                ]
            );

            return $this->success($setting, 'تم إضافة أو تحديث الإعداد');
        });
    }
}
