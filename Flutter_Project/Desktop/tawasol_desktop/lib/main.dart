import 'package:bitsdojo_window/bitsdojo_window.dart';
import 'package:flutter/material.dart';
import 'package:tawasol_desktop/app.dart';

void main() {
  runApp(const MyApp());

  doWhenWindowReady(() {
    appWindow.maximize();
    appWindow.show();
  });
}
