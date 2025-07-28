import 'package:get/get.dart';
import 'package:tawasol_desktop/utils/bindings/home_binding.dart';
import 'package:tawasol_desktop/utils/bindings/splash_binding.dart';
import 'package:tawasol_desktop/utils/constants/app_routes.dart';
import 'package:tawasol_desktop/views/home/home.dart';
import 'package:tawasol_desktop/views/splash/splash.dart';

class AppPages {
  static final routes = [
    GetPage(
      name: AppRoutes.splash,
      page: () => const SplashPage(),
      binding: SplashBinding(),
      transitionDuration: const Duration(milliseconds: 500),
    ),
    GetPage(
      name: AppRoutes.home,
      page: () => const HomePage(),
      binding: HomeBinding(),
      transition: Transition.rightToLeftWithFade, // أو rightToLeft أو zoom
      transitionDuration: const Duration(milliseconds: 1200),
    ),
  ];
}
