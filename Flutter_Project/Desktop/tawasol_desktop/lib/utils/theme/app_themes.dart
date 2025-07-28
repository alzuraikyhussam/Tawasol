import 'package:flutter/material.dart';
import 'package:tawasol_desktop/utils/constants/app_constants.dart';
import 'app_colors.dart';

class AppThemes {
  static final lightTheme = ThemeData(
    brightness: Brightness.light,
    primaryColor: AppColors.primary,
    scaffoldBackgroundColor: AppColors.white,
    fontFamily: AppConstants.appFont,
    appBarTheme: const AppBarTheme(
      backgroundColor: AppColors.primary,
    ),
  );

  static final darkTheme = ThemeData(
    brightness: Brightness.dark,
    primaryColor: AppColors.primary,
    scaffoldBackgroundColor: AppColors.black,
    fontFamily: AppConstants.appFont,
    appBarTheme: const AppBarTheme(
      backgroundColor: AppColors.black,
    ),
  );
}
