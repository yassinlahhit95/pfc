import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../auth/auth_state.dart';
import 'api_exception.dart';

/// Base URL for api/v1/*.php. Override per environment, e.g.:
///   flutter run --dart-define=API_BASE_URL=http://10.0.2.2
/// (Android emulator reaches the host machine's Laragon via 10.0.2.2, not
/// pfc.test/localhost.) Defaults to the production domain.
const _defaultBaseUrl = 'https://aulapro.yassin.agency';
const String apiBaseUrl =
    String.fromEnvironment('API_BASE_URL', defaultValue: _defaultBaseUrl);

/// Thin wrapper around a single Dio instance shared by every feature
/// repository. Endpoints are hit at their literal filenames under
/// /api/v1/ — there is no server-side router to match against.
class ApiClient {
  ApiClient(this._ref)
      : dio = Dio(BaseOptions(
          baseUrl: '$apiBaseUrl/api/v1',
          connectTimeout: const Duration(seconds: 15),
          receiveTimeout: const Duration(seconds: 20),
          headers: {'Content-Type': 'application/json'},
        )) {
    dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        final session = _ref.read(sessionControllerProvider).valueOrNull;
        if (session != null) {
          options.headers['Authorization'] = 'Bearer ${session.token}';
        }
        handler.next(options);
      },
      onError: (error, handler) async {
        final code = error.response?.data is Map
            ? (error.response!.data as Map)['code'] as String?
            : null;
        if (code == 'unauthenticated' || code == 'token_expired') {
          await _ref.read(sessionControllerProvider.notifier).clear();
        }
        handler.next(error);
      },
    ));
  }

  final Ref _ref;
  final Dio dio;

  /// Runs [request] and translates Dio/HTTP failures into [ApiException] /
  /// [ApiConnectionException] so callers only ever handle two error types.
  /// Automatically retries transient network failures up to 3 times with
  /// exponential backoff (1s, 2s, 4s).
  Future<Map<String, dynamic>> call(
    Future<Response<dynamic>> Function() request,
  ) async {
    int retries = 0;
    const maxRetries = 3;

    while (true) {
      try {
        final response = await request();
        final data = response.data;
        if (data is Map<String, dynamic>) return data;
        throw const ApiConnectionException('Unexpected response shape.');
      } on DioException catch (e) {
        final data = e.response?.data;

        // Always throw API errors (4xx/5xx with error response) — don't retry
        if (data is Map<String, dynamic> && data['ok'] == false) {
          throw ApiException(
            message: (data['error'] as String?) ?? 'Unknown error.',
            code: (data['code'] as String?) ?? 'error',
            statusCode: e.response?.statusCode ?? 0,
          );
        }

        // Retry transient network errors (connection timeout, receive timeout)
        final isTransient = e.type == DioExceptionType.connectionTimeout ||
            e.type == DioExceptionType.receiveTimeout;

        if (isTransient && retries < maxRetries) {
          retries++;
          // Exponential backoff: 1s, 2s, 4s
          await Future.delayed(Duration(seconds: 1 << (retries - 1)));
          continue;
        }

        // Non-transient error or max retries reached
        throw ApiConnectionException(e.message ?? 'Network error.');
      }
    }
  }

  Future<Map<String, dynamic>> get(String path,
          {Map<String, dynamic>? query}) =>
      call(() => dio.get(path, queryParameters: query));

  Future<Map<String, dynamic>> post(String path, {Object? data, Map<String, dynamic>? query}) =>
      call(() => dio.post(path, data: data, queryParameters: query));

  Future<Map<String, dynamic>> delete(String path) =>
      call(() => dio.delete(path));
}

final apiClientProvider = Provider<ApiClient>((ref) => ApiClient(ref));
