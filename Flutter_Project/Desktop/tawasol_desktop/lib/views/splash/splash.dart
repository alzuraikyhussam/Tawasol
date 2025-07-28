import 'package:flutter/material.dart';
import 'package:get/get_core/get_core.dart';
import 'package:get/get_navigation/get_navigation.dart';
import 'package:loading_indicator/loading_indicator.dart';
import 'package:tawasol_desktop/utils/constants/app_pages.dart';
import 'package:tawasol_desktop/utils/constants/app_routes.dart';
import 'package:tawasol_desktop/utils/theme/app_colors.dart';
import 'package:tawasol_desktop/views/home/home.dart';

class SplashPage extends StatefulWidget {
  const SplashPage({super.key});

  @override
  State<SplashPage> createState() => _SplashPageState();
}

class _SplashPageState extends State<SplashPage>
    with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double> _animation;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1200),
    );
    _animation = CurvedAnimation(parent: _controller, curve: Curves.easeInOut);

    _controller.forward();

    Future.delayed(const Duration(seconds: 4), () {
      // Navigator.of(context).pushReplacement(
      //   MaterialPageRoute(builder: (_) => const HomePage()),
      // );

      Get.offNamed(AppRoutes.home);
    });
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            colors: [
              AppColors.white,
              AppColors.whiteSmoke,
              // Color.fromARGB(255, 188, 166, 228),
              // Color(0xFF9575cd),
            ],
            begin: Alignment.bottomCenter,
            end: Alignment.topCenter,
          ),
        ),
        child: Center(
          child: FadeTransition(
            opacity: _animation,
            child: ScaleTransition(
              scale: _animation,
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  // شعارك
                  Container(
                    // decoration: BoxDecoration(
                    //   color: Colors.white,
                    //   shape: BoxShape.circle,
                    //   boxShadow: [
                    //     BoxShadow(
                    //       color: Colors.black12,
                    //       blurRadius: 24,
                    //       spreadRadius: 2,
                    //     ),
                    //   ],
                    // ),
                    padding: const EdgeInsets.all(32),
                    child: Image.asset(
                      'assets/images/tawasol_logo_high.png',
                      width: 500,
                      height: 200,
                      fit: BoxFit.contain,
                    ),
                  ),
                  const SizedBox(height: 20),
                  const SizedBox(
                    height: 50,
                    width: 50,
                    child: LoadingIndicator(
                      indicatorType: Indicator.lineScale,

                      /// Required, The loading type of the widget
                      colors: [
                        AppColors.secondary,
                        AppColors.primary,
                      ],

                      /// Optional, The color collections
                      strokeWidth: 2,

                      /// Optional, The stroke of the line, only applicable to widget which contains line
                      // backgroundColor: Colors.black,

                      /// Optional, Background of the widget
                      // pathBackgroundColor: Colors.black,

                      /// Optional, the stroke backgroundColor
                    ),
                  ),

                  // const CircularProgressIndicator(
                  //   valueColor:
                  //       AlwaysStoppedAnimation<Color>(AppColors.primary),
                  // ),
                  // const SizedBox(height: 24),
                  // // نص ترحيبي أو اسم التطبيق
                  // const Text(
                  //   "Tawasol",
                  //   style: TextStyle(
                  //     fontSize: 28,
                  //     fontWeight: FontWeight.bold,
                  //     color: AppColors.primary,
                  //     letterSpacing: 2,
                  //   ),
                  // ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
