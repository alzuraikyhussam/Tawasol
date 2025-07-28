class Validators {
  static String? validateUsername(String? val) {
    if (val == null || val.trim().isEmpty) return 'الرجاء إدخال اسم المستخدم';
    if (val.length < 3) return 'اسم المستخدم قصير جداً';
    return null;
  }

  static String? validatePassword(String? val) {
    if (val == null || val.trim().isEmpty) return 'الرجاء إدخال كلمة المرور';
    if (val.length < 4) return 'كلمة المرور قصيرة جداً';
    return null;
  }
}
