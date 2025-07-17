<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactsController extends Controller
{
    use ApiResponseTrait;

    /**
     * عرض كل جهات الاتصال للمستخدم الحالي
     */
    public function index(Request $request)
    {
        $contacts = $request->user()->contacts()->with('contactUser')->get();

        return $this->success($contacts, 'قائمة جهات الاتصال');
    }

    /**
     * إضافة جهة اتصال جديدة
     */
    public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $validated = $request->validate([
                'contact_user_id' => 'required|exists:users,id|different:owner_id',
            ]);

            // التأكد من عدم التكرار
            $exists = Contact::where('owner_id', $request->user()->id)
                ->where('contact_user_id', $validated['contact_user_id'])
                ->exists();

            if ($exists) {
                return $this->error('جهة الاتصال موجودة بالفعل', 409);
            }

            $contact = Contact::create([
                'owner_id' => $request->user()->id,
                'contact_user_id' => $validated['contact_user_id'],
            ]);

            return $this->success($contact, 'تمت إضافة جهة الاتصال', 201);
        });
    }

    /**
     * عرض تفاصيل جهة اتصال
     */
    public function show(Request $request, $id)
    {
        $contact = $request->user()->contacts()->with('contactUser')->find($id);

        if (!$contact) {
            return $this->notFound('جهة الاتصال غير موجودة');
        }

        return $this->success($contact, 'تفاصيل جهة الاتصال');
    }

    /**
     * حذف جهة اتصال
     */
    public function destroy(Request $request, $id)
    {
        $contact = $request->user()->contacts()->find($id);

        if (!$contact) {
            return $this->notFound('جهة الاتصال غير موجودة');
        }

        $contact->delete();

        return $this->success(null, 'تم حذف جهة الاتصال');
    }

    /**
     * التحقق هل جهة الاتصال موجودة
     */
    public function checkContact(Request $request)
    {
        $validated = $request->validate([
            'contact_user_id' => 'required|exists:users,id',
        ]);

        $exists = Contact::where('owner_id', $request->user()->id)
            ->where('contact_user_id', $validated['contact_user_id'])
            ->exists();

        return $this->success(['exists' => $exists], 'نتيجة التحقق');
    }

    /**
     * جهات الاتصال المشتركة بين مستخدمين
     */
    public function mutualContacts(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id|different:' . $request->user()->id,
        ]);

        $ownerContacts = $request->user()->contacts()->pluck('contact_user_id')->toArray();
        $otherContacts = User::find($validated['user_id'])->contacts()->pluck('contact_user_id')->toArray();

        $mutual = array_intersect($ownerContacts, $otherContacts);

        $users = User::whereIn('id', $mutual)->get();

        return $this->success($users, 'الجهات المشتركة');
    }
}
