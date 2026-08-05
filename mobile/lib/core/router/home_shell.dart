import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../features/classroom/presentation/modules_screen.dart';
import '../../features/home/presentation/home_screen.dart';
import '../../features/messages/data/messages_repository.dart';
import '../../features/messages/presentation/messages_screen.dart';
import '../../features/profile/presentation/profile_screen.dart';
import '../../features/tareas/presentation/tareas_screen.dart';
import '../auth/auth_state.dart';
import '../auth/session.dart';
import '../i18n/translations.dart';

class _Tab {
  const _Tab(this.label, this.icon, this.screen);
  final String label;
  final IconData icon;
  final Widget screen;
}

/// Bottom nav is deliberately kept to ~4-5 items — Horario/Notas/Anuncios/
/// Eventos/Aula live as quick-access cards on the Inicio dashboard instead
/// of separate tabs, since that scales much better as more features land
/// (asistencias, admin back-office, ...) than an ever-growing nav bar.
/// Tutors get neither Mensajería (reclamaciones never supported tutores,
/// on web or mobile) nor Chat (mobile-only restriction — tutores keep chat
/// on the web app; on mobile they're intentionally scoped to viewing their
/// children's data, not open-ended messaging).
List<_Tab> _tabsFor(UserRole role, Map<String, String> t) {
  final hasMessages = role != UserRole.tutor;
  return [
    _Tab(t['nav_inicio'] ?? 'Inicio', Icons.home_rounded, const HomeScreen()),
    if (hasMessages)
      _Tab(t['nav_mensajeria'] ?? 'Mensajería', Icons.mail_rounded,
          const MessagesScreen()),
    _Tab('Materiales', Icons.auto_stories_outlined, const ModulesScreen()),
    _Tab('Tareas', Icons.assignment_outlined, const TareasScreen()),
    _Tab(t['nav_perfil'] ?? 'Perfil', Icons.person_rounded,
        const ProfileScreen()),
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
int? homeTabIndexForType(String type, UserRole role) {
  final tabIds = <String>[];
  tabIds.add('inicio');
  if (role != UserRole.tutor) tabIds.add('messages');
  tabIds.add('materiales');
  tabIds.add('tareas');
  tabIds.add('perfil');

  final targetId = switch (type) {
    'message' => 'messages',
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
  @override
  Widget build(BuildContext context) {
    final role = ref.watch(sessionControllerProvider).valueOrNull?.role;
    if (role == null) return const SizedBox.shrink(); // router redirects away

    final t = ref.watch(translationsProvider);
    final tabs = _tabsFor(role, t);
    final index = ref.watch(homeTabIndexProvider).clamp(0, tabs.length - 1);
    final hasMessages = tabs.any(
        (tab) => tab.label == t['nav_mensajeria'] || tab.label == 'Mensajería');

    // Only watched (and therefore only polled — see messagesUnreadCountProvider)
    // when this role actually has that tab, so a tutor session never fires the poll.
    final messagesUnread = hasMessages
        ? ref.watch(messagesUnreadCountProvider).valueOrNull ?? 0
        : 0;

    return Scaffold(
      body: IndexedStack(
        index: index,
        children: [for (final tab in tabs) tab.screen],
      ),
      bottomNavigationBar: NavigationBar(
        selectedIndex: index,
        onDestinationSelected: (i) =>
            ref.read(homeTabIndexProvider.notifier).state = i,
        destinations: tabs.map((tab) {
          final count = (tab.label == t['nav_mensajeria'] || tab.label == 'Mensajería') 
              ? messagesUnread 
              : 0;
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
