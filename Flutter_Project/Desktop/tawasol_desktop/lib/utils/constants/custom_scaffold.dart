import 'package:bitsdojo_window/bitsdojo_window.dart';
import 'package:flutter/material.dart';
import 'package:tawasol_desktop/utils/theme/app_colors.dart';
import 'package:tawasol_desktop/utils/widgets/custom_window_title_bar.dart';

class CustomScaffold extends StatelessWidget {
  final Widget child;
  // final String title;

  const CustomScaffold({required this.child, super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: WindowBorder(
        color: Colors.transparent,
        child: Column(
          children: [
            CustomWindowTitleBar(),
            // محتوى الصفحة (يختلف حسب الواجهة)
            Expanded(
              child: child,
            ),
          ],
        ),
      ),
    );
  }
}
