import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../features/announcements/presentation/announcements_screen.dart';
import '../../features/auth/presentation/login_screen.dart';
import '../../features/events/presentation/events_screen.dart';
import '../../features/grades/presentation/grades_screen.dart';
import '../auth/auth_state.dart';
import '../theme/app_theme.dart';
import 'home_shell.dart';

/// Bridges Riverpod's sessionControllerProvider to go_router's
/// refreshListenable so navigation reacts to login/logout without manual
/// context.go() calls scattered across the app.
class _RouterRefreshNotifier extends ChangeNotifier {
  _RouterRefreshNotifier(Ref ref) {
    ref.listen(sessionControllerProvider, (_, __) => notifyListeners());
  }
}

final routerProvider = Provider<GoRouter>((ref) {
  final notifier = _RouterRefreshNotifier(ref);
  ref.onDispose(notifier.dispose);

  return GoRouter(
    initialLocation: '/',
    refreshListenable: notifier,
    redirect: (context, state) {
      final sessionAsync = ref.read(sessionControllerProvider);
      if (sessionAsync.isLoading) return null; // stay on splash

      final loggedIn = sessionAsync.valueOrNull != null;
      final atLogin = state.matchedLocation == '/login';
      final atSplash = state.matchedLocation == '/';

      if (!loggedIn) return atLogin ? null : '/login';
      if (loggedIn && (atLogin || atSplash)) return '/home';
      return null;
    },
    routes: [
      GoRoute(path: '/', builder: (context, state) => const _SplashScreen()),
      GoRoute(path: '/login', builder: (context, state) => const LoginScreen()),
      GoRoute(path: '/home', builder: (context, state) => const HomeShell()),
      // Not bottom-nav tabs (those stay Navigator.push from HomeScreen's own
      // cards) — these only exist so a push-notification tap for a type with
      // no dedicated tab (grades, events, announcements) has somewhere to
      // deep-link to. See NotificationsService._handleTap.
      GoRoute(path: '/grades', builder: (context, state) => const GradesScreen()),
      GoRoute(path: '/events', builder: (context, state) => const EventsScreen()),
      GoRoute(path: '/announcements', builder: (context, state) => const AnnouncementsScreen()),
    ],
  );
});

/// Cold-start / session-restore screen. Used to be a bare centered spinner —
/// no branding, so every launch felt like a stalled blank screen for however
/// long the session restore took. Now it shows the app mark first (fades +
/// scales in once) with a small secondary spinner underneath, so there's
/// always something to look at immediately instead of nothing but a circle.
class _SplashScreen extends StatelessWidget {
  const _SplashScreen();

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Scaffold(
      backgroundColor: scheme.surface,
      body: Center(
        child: TweenAnimationBuilder<double>(
          tween: Tween(begin: 0, end: 1),
          duration: const Duration(milliseconds: 650),
          curve: Curves.easeOutCubic,
          builder: (context, t, child) => Opacity(
            opacity: t,
            child: Transform.scale(scale: 0.92 + 0.08 * t, child: child),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                width: 84,
                height: 84,
                decoration: BoxDecoration(
                  gradient: const LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: [Color(0xFF6366F1), Color(0xFF4F46E5)],
                  ),
                  borderRadius: BorderRadius.circular(Radii.xl),
                  boxShadow: cardShadow(Theme.of(context).brightness),
                ),
                alignment: Alignment.center,
                child: const Icon(
                  Icons.auto_stories_rounded,
                  color: Colors.white,
                  size: 38,
                ),
              ),
              const SizedBox(height: Space.lg),
              Text('AulaPro', style: Theme.of(context).textTheme.titleLarge),
              const SizedBox(height: Space.xxxl),
              SizedBox(
                width: 26,
                height: 26,
                child: CircularProgressIndicator(
                  strokeWidth: 2.4,
                  color: scheme.primary.withValues(alpha: 0.5),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
