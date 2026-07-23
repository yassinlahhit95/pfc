/// Typed wrapper around the API's standard error envelope:
/// `{ "ok": false, "error": "...", "code": "short_code" }`
class ApiException implements Exception {
  const ApiException({
    required this.message,
    required this.code,
    required this.statusCode,
  });

  final String message;
  final String code;
  final int statusCode;

  bool get isUnauthenticated =>
      code == 'unauthenticated' || code == 'token_expired';
  bool get isRateLimited => code == 'rate_limited';
  bool get isForbidden => code == 'forbidden';
  bool get isValidation => code == 'validation';

  @override
  String toString() => 'ApiException($code, $statusCode): $message';
}

/// Raised when the device has no usable connectivity to the API host.
class ApiConnectionException implements Exception {
  const ApiConnectionException(this.message);
  final String message;

  @override
  String toString() => 'ApiConnectionException: $message';
}
