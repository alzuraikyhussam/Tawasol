import 'package:flutter/material.dart';
import 'package:get/get.dart';

class AppTextStyles {
  static final bool isDark = Get.isDarkMode;

  static final TextStyle font24BlackSemibold = TextStyle(
    fontSize: 24,
    fontWeight: FontWeight.w600,
    color: isDark ? Colors.white : Colors.black,
  );

  // static final TextStyle headlineMedium = TextStyle(
  //   fontSize: 20,
  //   fontWeight: FontWeight.w600,
  //   color: isDark ? Colors.white70 : Colors.black87,
  // );

  // static final TextStyle bodyLarge = TextStyle(
  //   fontSize: 16,
  //   fontWeight: FontWeight.w500,
  //   color: isDark ? Colors.white70 : Colors.black87,
  // );

  // static final TextStyle bodyMedium = TextStyle(
  //   fontSize: 14,
  //   fontWeight: FontWeight.normal,
  //   color: isDark ? Colors.white60 : Colors.black54,
  // );

  // static final TextStyle bodySmall = TextStyle(
  //   fontSize: 12,
  //   fontWeight: FontWeight.w300,
  //   color: isDark ? Colors.white54 : Colors.black45,
  // );

  // static final TextStyle caption = TextStyle(
  //   fontSize: 12,
  //   fontStyle: FontStyle.italic,
  //   color: isDark ? Colors.white38 : Colors.black38,
  // );

  // static final TextStyle labelSmall = TextStyle(
  //   fontSize: 10,
  //   fontWeight: FontWeight.bold,
  //   color: isDark ? Colors.grey[400] : Colors.grey[800],
  // );
}
