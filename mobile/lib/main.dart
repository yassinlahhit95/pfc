import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import 'core/auth/auth_state.dart';
import 'core/theme/theme_mode_provider.dart';
import 'core/notifications/notifications_service.dart';
import 'core/router/app_router.dart';
import 'core/theme/app_theme.dart';

import 'package:flutter_native_splash/flutter_native_splash.dart';
import 'package:intl/date_symbol_data_local.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';

void main() async {
  WidgetsBinding widgetsBinding = WidgetsFlutterBinding.ensureInitialized();
  FlutterNativeSplash.preserve(widgetsBinding: widgetsBinding);
  await dotenv.load(fileName: ".env");
  await initializeDateFormatting('es', null);
  await initializeDateFormatting('es_ES', null);
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
      if (next.value != null) {
        ref.read(notificationsServiceProvider).init();
      }
    });

    final sessionAsync = ref.watch(sessionControllerProvider);
    final onboardingAsync = ref.watch(onboardingCompletedControllerProvider);

    if (!sessionAsync.isLoading && !onboardingAsync.isLoading) {
      FlutterNativeSplash.remove();
    }

    final themeMode = ref.watch(themeModeProvider);

    return MaterialApp.router(
      title: dotenv.env['APP_NAME'] ?? 'Plataforma Académica',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.light(),
      darkTheme: AppTheme.dark(),
      themeMode: themeMode,
      routerConfig: router,
    );
  }
}
