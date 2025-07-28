import 'package:bitsdojo_window/bitsdojo_window.dart';
import 'package:flutter/material.dart';
import 'package:tawasol_desktop/utils/theme/app_colors.dart';

class CustomWindowTitleBar extends StatelessWidget {
  // final String title;
  // final bool isMaximized;
  // final VoidCallback onToggleMaxRestore;

  const CustomWindowTitleBar({
    super.key,
    // required this.title,
    // required this.isMaximized,
    // required this.onToggleMaxRestore,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: EdgeInsetsDirectional.all(10),
      // decoration: BoxDecoration(
      //   gradient: LinearGradient(
      //     colors: [
      //       AppColors.primary,
      //       AppColors.secondary,
      //     ],
      //     begin: AlignmentDirectional.bottomEnd,
      //     end: AlignmentDirectional.topStart,
      //   ),
      // ),
      color: AppColors.whiteSmoke,
      height: 65,
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.center,
        mainAxisAlignment: MainAxisAlignment.start,
        children: [
          Expanded(
            child: GestureDetector(
              behavior: HitTestBehavior.translucent,
              // onPanStart: (_) => appWindow.startDragging(),
              child: Container(
                  alignment: AlignmentDirectional.centerStart,
                  child: Image.asset(
                    'assets/images/tawasol_logo_high.png',
                    fit: BoxFit.cover,
                  )),
            ),
          ),
          Container(
            height: 30,
            width: 30,
            alignment: AlignmentDirectional.center,
            decoration: BoxDecoration(
                boxShadow: [
                  BoxShadow(
                    // blurRadius: 1,
                    spreadRadius: 5,
                    color: AppColors.white,
                  ),
                ],
                color: Colors.orangeAccent,
                borderRadius: BorderRadiusDirectional.all(Radius.circular(50))),
            child: IconButton(
              icon: Icon(Icons.minimize, color: Colors.white),
              tooltip: 'تصغير',
              iconSize: 18,
              onPressed: () => appWindow.minimize(),
              padding: EdgeInsets.zero,
            ),
          ),
          SizedBox(
            width: 12,
          ),
          Container(
            height: 30,
            width: 30,
            alignment: AlignmentDirectional.center,
            decoration: BoxDecoration(
                boxShadow: [
                  BoxShadow(
                    // blurRadius: 1,
                    spreadRadius: 5,
                    color: AppColors.white,
                  ),
                ],
                color: Colors.red,
                borderRadius: BorderRadiusDirectional.all(Radius.circular(50))),
            child: IconButton(
              icon: Icon(Icons.close, color: AppColors.white),
              tooltip: 'إغلاق',
              iconSize: 18,
              onPressed: () => appWindow.minimize(),
              padding: EdgeInsets.zero,
            ),
          ),
          SizedBox(
            width: 20,
          )
          // IconButton(
          //   icon: Icon(
          //     isMaximized ? Icons.crop_square : Icons.check_box_outline_blank,
          //     color: Colors.white,
          //   ),
          //   tooltip: isMaximized ? 'استعادة' : 'تكبير',
          //   onPressed: onToggleMaxRestore,
          //   padding: EdgeInsets.zero,
          // ),
          // IconButton(
          //   icon: Icon(Icons.close, color: Colors.white),
          //   tooltip: 'إغلاق',
          //   onPressed: () => appWindow.close(),
          //   padding: EdgeInsets.zero,
          // ),
        ],
      ),
    );
  }
}
