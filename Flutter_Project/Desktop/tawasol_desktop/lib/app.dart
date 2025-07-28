import 'package:flutter/material.dart';
import 'package:get/get_navigation/src/root/get_material_app.dart';
import 'package:tawasol_desktop/utils/bindings/app_bindings.dart';
import 'package:tawasol_desktop/utils/constants/app_constants.dart';
import 'package:tawasol_desktop/utils/constants/app_pages.dart';
import 'package:tawasol_desktop/utils/constants/app_routes.dart';
import 'package:tawasol_desktop/utils/theme/app_themes.dart';
import 'package:flutter_localizations/flutter_localizations.dart';

class MyApp extends StatelessWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context) {
    return GetMaterialApp(
      title: AppConstants.appName,
      debugShowCheckedModeBanner: false,
      initialBinding: AppBindings(),
      initialRoute: AppRoutes.splash,
      getPages: AppPages.routes,
      theme: AppThemes.lightTheme,
      darkTheme: AppThemes.darkTheme,
      themeMode: ThemeMode.light,
      locale: const Locale('ar'), // ← تعيين اللغة للعربية
      supportedLocales: const [
        Locale('ar'), // دعم اللغة العربية
        Locale('en'), // يمكنك دعم لغات أخرى
      ],
      localizationsDelegates: const [
        GlobalMaterialLocalizations.delegate,
        GlobalWidgetsLocalizations.delegate,
        GlobalCupertinoLocalizations.delegate,
      ],
    );
  }
}
