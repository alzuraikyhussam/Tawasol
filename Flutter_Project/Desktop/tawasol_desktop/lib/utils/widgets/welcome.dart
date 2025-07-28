import 'package:flutter/material.dart';
import 'package:tawasol_desktop/utils/theme/app_colors.dart';

class WelcomePage extends StatelessWidget {
  const WelcomePage({super.key});

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Image.asset(
            'assets/images/tawasol_logo_high.png',
            width: 500,
            // height: 500,
            fit: BoxFit.cover,
          ),
          // Container(
          //   width: 200,
          //   height: 200,
          //   decoration: const BoxDecoration(
          //     shape: BoxShape.circle,
          //     gradient: LinearGradient(
          //       colors: [
          //         AppColors.primary,
          //         AppColors.secondary,
          //       ],
          //     ),
          //   ),
          //   child: Center(
          //     child: Image.asset(
          //       'assets/images/imag2.png',
          //       width: 199,
          //       height: 193,
          //       fit: BoxFit.fill,
          //     ),
          //   ),
          // ),

          const SizedBox(height: 32),
          const Text(
            'خليك متصل!',
            style: TextStyle(
              fontSize: 28,
              fontWeight: FontWeight.w600,
              color: AppColors.primary,
            ),
          ),
          const SizedBox(height: 16),
          const Text(
            'ابقى على اتصال لمزامنة الرسائل. بدون انترنت!!!.',
            textAlign: TextAlign.center,
            style: TextStyle(
              fontSize: 20,
              color: AppColors.gray,
            ),
          ),
        ],
      ),
    );
  }
}
