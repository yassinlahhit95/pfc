import 'dart:async';
import 'package:flutter_riverpod/flutter_riverpod.dart';

extension CacheExtension on AutoDisposeRef {
  /// Keeps the provider alive for [duration] after the last listener is removed.
  /// If a new listener is added within [duration], the timer is cancelled and
  /// the state is retained. Otherwise, the provider is disposed.
  void cacheFor(Duration duration) {
    final link = keepAlive();
    Timer? timer;

    onDispose(() {
      timer?.cancel();
    });

    onCancel(() {
      timer = Timer(duration, () {
        link.close();
      });
    });

    onResume(() {
      timer?.cancel();
    });
  }
}
