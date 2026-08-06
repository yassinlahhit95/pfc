import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';

import '../../features/announcements/presentation/announcements_screen.dart';
import '../../features/auth/presentation/login_screen.dart';
import '../../features/auth/presentation/onboarding_screen.dart';
import '../../features/events/presentation/events_screen.dart';
import '../../features/grades/presentation/grades_screen.dart';
import '../auth/auth_state.dart';
import 'home_shell.dart';

/// Bridges Riverpod's sessionControllerProvider and onboardingCompletedProvider
/// to go_router's refreshListenable so navigation reacts to auth and onboarding state changes.
class _RouterRefreshNotifier extends ChangeNotifier {
  _RouterRefreshNotifier(Ref ref) {
    ref.listen(sessionControllerProvider, (_, __) => notifyListeners());
    ref.listen(onboardingCompletedProvider, (_, __) => notifyListeners());
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
      final onboardingAsync = ref.read(onboardingCompletedProvider);

      if (sessionAsync.isLoading || onboardingAsync.isLoading) {
        return null; // stay on splash
      }

      final loggedIn = sessionAsync.value != null;
      final onboardingCompleted = onboardingAsync.value ?? false;

      final atLogin = state.matchedLocation == '/login';
      final atOnboarding = state.matchedLocation == '/onboarding';
      final atSplash = state.matchedLocation == '/';

      if (loggedIn) {
        if (atLogin || atOnboarding || atSplash) {
          return '/home';
        }
        return null;
      } else {
        // Not logged in
        if (!onboardingCompleted) {
          return atOnboarding ? null : '/onboarding';
        } else {
          return atLogin ? null : '/login';
        }
      }
    },
    routes: [
      GoRoute(
          path: '/',
          builder: (context, state) => const Scaffold(
                backgroundColor: Color(0xFF0A0E1A),
              )),
      GoRoute(
          path: '/onboarding',
          builder: (context, state) => const OnboardingScreen()),
      GoRoute(path: '/login', builder: (context, state) => const LoginScreen()),
      GoRoute(path: '/home', builder: (context, state) => const HomeShell()),
      // Not bottom-nav tabs (those stay Navigator.push from HomeScreen's own
      // cards) — these only exist so a push-notification tap for a type with
      // no dedicated tab (grades, events, announcements) has somewhere to
      // deep-link to. See NotificationsService._handleTap.
      GoRoute(
          path: '/grades', builder: (context, state) => const GradesScreen()),
      GoRoute(
          path: '/events', builder: (context, state) => const EventsScreen()),
      GoRoute(
          path: '/announcements',
          builder: (context, state) => const AnnouncementsScreen()),
    ],
  );
});
