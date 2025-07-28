import 'package:get/get.dart';
import 'package:tawasol_desktop/controllers/splash_controller.dart';

class AppBindings implements Bindings {
  @override
  void dependencies() {
    // Get.lazyPut<SplashController>(() => SplashController());
    Get.put(() => SplashController());
  }
}
