import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import 'core/auth/auth_state.dart';
import 'core/notifications/notifications_service.dart';
import 'core/router/app_router.dart';
import 'core/theme/app_theme.dart';

void main() {
  runApp(const ProviderScope(child: AulaProApp()));
}

class AulaProApp extends ConsumerWidget {
  const AulaProApp({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final router = ref.watch(routerProvider);
    // Fires on login and on cold-start session restore alike — a single
    // hook point instead of duplicating this in login_screen.dart.
    ref.listen(sessionControllerProvider, (previous, next) {
      if (next.valueOrNull != null) {
        ref.read(notificationsServiceProvider).init();
      }
    });
    return MaterialApp.router(
      title: 'AulaPro',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.light(),
      darkTheme: AppTheme.dark(),
      routerConfig: router,
    );
  }
}
