import 'dart:async';

/// Simple debounce utility for text inputs and async operations.
/// Delays execution of [callback] until [duration] has passed without new calls.
class Debounce {
  Timer? _timer;

  void call(Duration duration, Function() callback) {
    _timer?.cancel();
    _timer = Timer(duration, callback);
  }

  void cancel() => _timer?.cancel();
}
