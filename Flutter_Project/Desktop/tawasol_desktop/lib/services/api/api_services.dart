import 'dart:developer';
import 'package:dio/dio.dart';

class ApiServices {
  static final ApiServices _instance = ApiServices._internal();
  factory ApiServices() => _instance;

  late Dio _dio;
  String? _token;

  ApiServices._internal() {
    BaseOptions options = BaseOptions(
      connectTimeout: const Duration(seconds: 15),
      receiveTimeout: const Duration(seconds: 15),
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    );
    _dio = Dio(options);
  }

  /// 🔐 إعداد التوكن
  void setToken(String token) {
    _token = token;
    _dio.options.headers["Authorization"] = "Bearer $_token";
  }

  /// ❌ إزالة التوكن
  void clearToken() {
    _token = null;
    _dio.options.headers.remove("Authorization");
  }

  // /// 📥 GET
  // Future<List<dynamic>?> getList(String endpoint,
  //     {Map<String, dynamic>? params}) async {
  //   try {
  //     Response response = await _dio.get(endpoint, queryParameters: params);
  //     return response.data is List ? response.data : response.data['data'];
  //   } catch (e) {
  //     _handleError(e);
  //     return null;
  //   }
  // }

  /// 🧍‍♂️ GET
  Future<dynamic> get(String endpoint, {Map<String, dynamic>? params}) async {
    try {
      Response response = await _dio.get(endpoint, queryParameters: params);
      return response.data;
    } catch (e) {
      _handleError(e);
      return null;
    }
  }

  /// ➕ POST
  Future<dynamic> post(String endpoint, {dynamic data}) async {
    try {
      Response response = await _dio.post(endpoint, data: data);
      return response.data;
    } catch (e) {
      _handleError(e);
      return null;
    }
  }

  /// 🔄 PUT
  Future<dynamic> put(String endpoint, {dynamic data}) async {
    try {
      Response response = await _dio.put(endpoint, data: data);
      return response.data;
    } catch (e) {
      _handleError(e);
      return null;
    }
  }

  /// 🗑️ DELETE
  Future<dynamic> delete(String endpoint, {dynamic data}) async {
    try {
      Response response = await _dio.delete(endpoint, data: data);
      return response.data;
    } catch (e) {
      _handleError(e);
      return null;
    }
  }

  /// ⬆️ رفع ملف
  Future<dynamic> uploadFile(String endpoint, String filePath,
      {Map<String, dynamic>? data}) async {
    try {
      FormData formData = FormData.fromMap({
        ...?data,
        'file': await MultipartFile.fromFile(filePath),
      });

      Response response = await _dio.post(endpoint, data: formData);
      return response.data;
    } catch (e) {
      _handleError(e);
      return null;
    }
  }

  /// ⚠️ إدارة الأخطاء
  void _handleError(dynamic error) {
    if (error is DioException) {
      log('API Error: ${error.response?.statusCode} -> ${error.response?.data}');
    } else {
      log('Unexpected error: $error');
    }
  }
}
