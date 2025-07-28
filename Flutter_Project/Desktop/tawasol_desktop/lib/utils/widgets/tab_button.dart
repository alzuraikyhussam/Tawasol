import 'package:flutter/material.dart';
import 'package:tawasol_desktop/utils/theme/app_colors.dart';

class TabButton extends StatelessWidget {
  final String text;
  final bool selected;
  const TabButton({required this.text, this.selected = false, super.key});

  @override
  Widget build(BuildContext context) {
    return Expanded(
      child: Container(
        margin: const EdgeInsets.symmetric(horizontal: 2),
        height: 36,
        decoration: BoxDecoration(
          gradient: LinearGradient(
            colors: selected
                ? [
                    AppColors.primary,
                    AppColors.secondary,
                  ]
                : [
                    AppColors.white,
                    AppColors.white,
                  ],
            begin: AlignmentDirectional.topStart,
            end: AlignmentDirectional.bottomEnd,
          ),
          // color: selected ? AppColors.primary : AppColors.white,
          borderRadius: BorderRadius.circular(8),
        ),
        child: Center(
          child: Text(
            text,
            style: TextStyle(
              color: selected ? AppColors.white : AppColors.black,
              fontWeight: FontWeight.bold,
            ),
          ),
        ),
      ),
    );
  }
}
