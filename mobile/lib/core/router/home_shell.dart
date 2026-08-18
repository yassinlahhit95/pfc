import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_riverpod/legacy.dart';

import '../../features/announcements/presentation/announcements_screen.dart';
import '../../features/chat/data/chat_repository.dart';
import '../../features/chat/presentation/conversations_screen.dart';
import '../../features/classroom/presentation/modules_screen.dart';
import '../../features/home/presentation/home_screen.dart';
import '../../features/messages/data/messages_repository.dart';
import '../../features/messages/presentation/messages_screen.dart';
import '../../features/payments/presentation/my_payments_screen.dart';
import '../../features/planificacion/presentation/planificacion_screen.dart';
import '../../features/profile/presentation/profile_screen.dart';
import '../../features/tareas/presentation/tareas_screen.dart';
import '../auth/auth_state.dart';
import '../auth/session.dart';
import '../i18n/translations.dart';

/// Which unread-count provider (if any) badges this tab.
enum _Badge { none, messages, chat }

class _Tab {
  const _Tab(this.label, this.icon, this.screen, {this.badge = _Badge.none});
  final String label;
  final IconData icon;
  final Widget screen;
  final _Badge badge;
}

/// Bottom nav is deliberately kept to ~4-5 items and differs by role group:
/// - director/secretaria: back-office roles, no course content of their own —
///   Materiales/Tareas make no sense for them, replaced by Planificación (a
///   shared checklist/notebook for what the center still has to do).
/// - estudiante/profesor: unchanged — Materiales/Tareas are their actual work.
/// - tutor: Recibos/Avisos are what a parent actually checks day to day.
///   Mensajería here is backed by the Chat system (not the reclamaciones-style
///   Mensajería used by the other roles) — reclamaciones never supported
///   tutores (api/v1/messages.php returns 403 for them), while Chat already
///   supports all 5 roles, so it's the only backend that can actually serve
///   a working "message the school" tab for a parent.
List<_Tab> _tabsFor(UserRole role, Map<String, String> t) {
  final perfil = _Tab(
      t['nav_perfil'] ?? 'Perfil', Icons.person_rounded, const ProfileScreen());
  final inicio =
      _Tab(t['nav_inicio'] ?? 'Inicio', Icons.home_rounded, const HomeScreen());

  if (role == UserRole.director || role == UserRole.secretaria) {
    return [
      inicio,
      _Tab(t['nav_planificacion'] ?? 'Planificación', Icons.checklist_rounded,
          const PlanificacionScreen()),
      _Tab(t['nav_mensajeria'] ?? 'Mensajería', Icons.mail_rounded,
          const MessagesScreen(), badge: _Badge.messages),
      perfil,
    ];
  }

  if (role == UserRole.tutor) {
    return [
      inicio,
      _Tab(t['metric_recibos'] ?? 'Recibos', Icons.receipt_rounded,
          const MyPaymentsScreen()),
      _Tab(t['nav_mensajeria'] ?? 'Mensajería', Icons.mail_rounded,
          const ConversationsScreen(), badge: _Badge.chat),
      _Tab(t['nav_anuncios'] ?? 'Avisos', Icons.campaign_outlined,
          AnnouncementsScreen()),
      perfil,
    ];
  }

  // estudiante / profesor
  return [
    inicio,
    _Tab(t['nav_mensajeria'] ?? 'Mensajería', Icons.mail_rounded,
        const MessagesScreen(), badge: _Badge.messages),
    _Tab('Materiales', Icons.auto_stories_outlined, const ModulesScreen()),
    _Tab('Tareas', Icons.assignment_outlined, const TareasScreen()),
    perfil,
  ];
}

class HomeShell extends ConsumerStatefulWidget {
  const HomeShell({super.key});

  @override
  ConsumerState<HomeShell> createState() => _HomeShellState();
}

/// Single source of truth for the selected tab — settable from outside the
/// widget tree (NotificationsService jumps here on a notification tap).
final homeTabIndexProvider = StateProvider<int>((ref) => 0);

/// Maps a push notification's `data.type` to a tab index, using stable
/// identifiers rather than translated labels. Null means "stay on whatever
/// tab is already showing" (most types are dashboard cards, not dedicated tabs).
/// Mirrors _tabsFor's per-role-group id order exactly.
int? homeTabIndexForType(String type, UserRole role) {
  final tabIds = <String>['inicio'];
  if (role == UserRole.director || role == UserRole.secretaria) {
    tabIds.addAll(['planificacion', 'messages', 'perfil']);
  } else if (role == UserRole.tutor) {
    tabIds.addAll(['recibos', 'messages', 'avisos', 'perfil']);
  } else {
    tabIds.addAll(['messages', 'materiales', 'tareas', 'perfil']);
  }

  final targetId = switch (type) {
    'message' || 'chat_message' => 'messages',
    _ => null,
  };
  if (targetId == null) return null;
  final i = tabIds.indexOf(targetId);
  return i == -1 ? null : i;
}

/// Counterpart to [homeTabIndexForType] for notification types whose content
/// lives as an Inicio dashboard card rather than a bottom-nav tab (grades,
/// events, announcements) — those need an actual route push, not a tab index.
String? homeRouteForType(String type) {
  return switch (type) {
    'nota_publicada' || 'grade_tfg' => '/grades',
    'evento_nuevo' => '/events',
    'announcement' => '/announcements',
    _ => null,
  };
}

class _HomeShellState extends ConsumerState<HomeShell> {
  // One controller per tab slot (5 = the largest role group's tab count) so
  // switching tabs never disturbs another tab's scroll position. Each
  // screen's own top-level ListView has no explicit controller of its own,
  // so it automatically binds to whichever PrimaryScrollController wraps it
  // (see IndexedStack below) — no changes needed in any individual screen.
  final List<ScrollController> _scrollControllers =
      List.generate(5, (_) => ScrollController());

  @override
  void dispose() {
    for (final c in _scrollControllers) {
      c.dispose();
    }
    super.dispose();
  }

  void _onDestinationSelected(int tapped, int current) {
    if (tapped == current) {
      final controller = _scrollControllers[tapped];
      if (controller.hasClients) {
        controller.animateTo(0,
            duration: const Duration(milliseconds: 300),
            curve: Curves.easeOutCubic);
      }
      return;
    }
    ref.read(homeTabIndexProvider.notifier).state = tapped;
  }

  @override
  Widget build(BuildContext context) {
    final role = ref.watch(sessionControllerProvider).value?.role;
    if (role == null) return const SizedBox.shrink(); // router redirects away

    final t = ref.watch(translationsProvider);
    final tabs = _tabsFor(role, t);
    final index = ref.watch(homeTabIndexProvider).clamp(0, tabs.length - 1);

    // Only watched (and therefore only polled) when this role's tab set
    // actually uses that badge kind, so no session fires a poll it doesn't need.
    final hasMessagesBadge = tabs.any((tab) => tab.badge == _Badge.messages);
    final hasChatBadge = tabs.any((tab) => tab.badge == _Badge.chat);
    final messagesUnread =
        hasMessagesBadge ? ref.watch(messagesUnreadCountProvider).value ?? 0 : 0;
    final chatUnread =
        hasChatBadge ? ref.watch(chatUnreadCountProvider).value ?? 0 : 0;

    return Scaffold(
      body: IndexedStack(
        index: index,
        children: [
          for (var i = 0; i < tabs.length; i++)
            PrimaryScrollController(
              controller: _scrollControllers[i],
              child: tabs[i].screen,
            ),
        ],
      ),
      bottomNavigationBar: NavigationBar(
        selectedIndex: index,
        onDestinationSelected: (i) => _onDestinationSelected(i, index),
        destinations: tabs.map((tab) {
          final count = switch (tab.badge) {
            _Badge.messages => messagesUnread,
            _Badge.chat => chatUnread,
            _Badge.none => 0,
          };
          return NavigationDestination(
            icon: _badgedIcon(tab.icon, count, false),
            selectedIcon: _badgedIcon(tab.icon, count, true),
            label: tab.label,
          );
        }).toList(),
      ),
    );
  }
}

/// Wraps a nav icon with an unread-count badge.
Widget _badgedIcon(IconData icon, int count, bool isSelected) {
  final base = Icon(icon);
  if (count <= 0) return base;
  return Badge(
    label: Text(count > 99 ? '99+' : '$count'),
    child: base,
  );
}
