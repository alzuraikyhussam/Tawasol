import 'package:flutter/material.dart';
import 'package:tawasol_desktop/utils/theme/app_colors.dart';

class ProfileDialog extends StatelessWidget {
  final String name;
  final String avatar;
  final String about;
  const ProfileDialog(
      {super.key,
      required this.name,
      required this.avatar,
      required this.about});

  @override
  Widget build(BuildContext context) {
    return Dialog(
      backgroundColor: AppColors.black,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
      child: Container(
        width: 350,
        padding: EdgeInsets.symmetric(vertical: 24, horizontal: 16),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Align(
              alignment: Alignment.topRight,
              child: IconButton(
                icon: Icon(Icons.close, color: AppColors.white),
                onPressed: () => Navigator.of(context).pop(),
              ),
            ),
            CircleAvatar(
              backgroundImage: AssetImage(avatar),
              radius: 54,
            ),
            SizedBox(height: 16),
            Text(
              name,
              style: TextStyle(
                  color: AppColors.white,
                  fontSize: 24,
                  fontWeight: FontWeight.bold),
              textAlign: TextAlign.center,
            ),
            SizedBox(height: 8),
            // لا تعرض رقم الهاتف
            SizedBox(height: 16),
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(Icons.info_outline, color: AppColors.lightGray, size: 20),
                SizedBox(width: 8),
                Flexible(
                  child: Text(
                    about,
                    style: TextStyle(color: AppColors.lightGray, fontSize: 15),
                    textAlign: TextAlign.center,
                  ),
                ),
              ],
            ),
            SizedBox(height: 24),
            // يمكنك إضافة خيارات أخرى هنا لاحقاً
          ],
        ),
      ),
    );
  }
}
