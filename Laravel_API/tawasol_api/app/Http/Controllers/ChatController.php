<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatMember;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatsController extends Controller
{
    use ApiResponseTrait;

    /**
     * جميع محادثاتي
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        $chats = Chat::whereHas('members', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->with(['members.user', 'messages' => function ($q) {
            $q->latest()->limit(1);
        }])->get();

        return $this->success($chats, 'قائمة محادثات المستخدم');
    }

    /**
     * إنشاء محادثة جديدة
     */
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $validated = $request->validate([
                'is_group' => 'required|boolean',
                'group_name' => 'required_if:is_group,1|string|max:100',
                'group_description' => 'nullable|string',
                'member_ids' => 'required|array|min:1',
                'member_ids.*' => 'exists:users,id',
            ]);

            $chat = Chat::create([
                'is_group' => $validated['is_group'],
                'group_name' => $validated['group_name'] ?? null,
                'group_description' => $validated['group_description'] ?? null,
                'created_by' => $request->user()->id,
                'department_id' => $request->user()->department_id ?? null,
            ]);

            // إضافة المنشئ
            ChatMember::create([
                'chat_id' => $chat->id,
                'user_id' => $request->user()->id,
                'is_admin' => true,
            ]);

            // إضافة الأعضاء الآخرين
            foreach ($validated['member_ids'] as $memberId) {
                ChatMember::create([
                    'chat_id' => $chat->id,
                    'user_id' => $memberId,
                ]);
            }

            return $this->success($chat->load('members.user'), 'تم إنشاء المحادثة', 201);
        });
    }

    /**
     * تفاصيل محادثة
     */
    public function show(Request $request, $id)
    {
        $chat = Chat::with(['members.user', 'messages' => function ($q) {
            $q->latest()->limit(20);
        }])->find($id);

        if (!$chat) {
            return $this->notFound('المحادثة غير موجودة');
        }

        return $this->success($chat, 'تفاصيل المحادثة');
    }

    /**
     * تعديل محادثة (للجروب فقط)
     */
    public function update(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $chat = Chat::find($id);

            if (!$chat) {
                return $this->notFound('المحادثة غير موجودة');
            }

            if (!$chat->is_group) {
                return $this->error('غير مسموح بتعديل شات فردي', 403);
            }

            $validated = $request->validate([
                'group_name' => 'sometimes|required|string|max:100',
                'group_description' => 'sometimes|nullable|string',
            ]);

            $chat->update($validated);

            return $this->success($chat, 'تم تعديل بيانات المجموعة');
        });
    }

    /**
     * حذف محادثة
     */
    public function destroy($id)
    {
        $chat = Chat::find($id);

        if (!$chat) {
            return $this->notFound('المحادثة غير موجودة');
        }

        $chat->delete();

        return $this->success(null, 'تم حذف المحادثة');
    }

    /**
     * إضافة عضو
     */
    public function addMember(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $chat = Chat::find($id);

            if (!$chat || !$chat->is_group) {
                return $this->error('المحادثة غير صالحة', 400);
            }

            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);

            $exists = ChatMember::where('chat_id', $chat->id)
                ->where('user_id', $validated['user_id'])
                ->exists();

            if ($exists) {
                return $this->error('العضو موجود بالفعل', 409);
            }

            ChatMember::create([
                'chat_id' => $chat->id,
                'user_id' => $validated['user_id'],
            ]);

            return $this->success(null, 'تم إضافة العضو بنجاح');
        });
    }

    /**
     * إزالة عضو
     */
    public function removeMember(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $chat = Chat::find($id);

            if (!$chat || !$chat->is_group) {
                return $this->error('المحادثة غير صالحة', 400);
            }

            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
            ]);

            $member = ChatMember::where('chat_id', $chat->id)
                ->where('user_id', $validated['user_id'])
                ->first();

            if (!$member) {
                return $this->notFound('العضو غير موجود');
            }

            $member->delete();

            return $this->success(null, 'تم إزالة العضو');
        });
    }

    /**
     * مغادرة المجموعة
     */
    public function leaveGroup(Request $request, $id)
    {
        $chat = Chat::find($id);

        if (!$chat || !$chat->is_group) {
            return $this->error('المحادثة غير صالحة', 400);
        }

        $member = ChatMember::where('chat_id', $chat->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$member) {
            return $this->notFound('أنت لست عضوًا في هذه المجموعة');
        }

        $member->delete();

        return $this->success(null, 'تمت مغادرة المجموعة');
    }

    /**
     * جلب الأعضاء
     */
    public function members($id)
    {
        $chat = Chat::with('members.user')->find($id);

        if (!$chat) {
            return $this->notFound('المحادثة غير موجودة');
        }

        return $this->success($chat->members, 'أعضاء المحادثة');
    }

    /**
     * جميع المجموعات التي أنا عضو فيها
     */
    public function myGroups(Request $request)
    {
        $groups = Chat::where('is_group', true)
            ->whereHas('members', function ($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            })->with('members.user')
            ->get();

        return $this->success($groups, 'قائمة المجموعات الخاصة بك');
    }
}
