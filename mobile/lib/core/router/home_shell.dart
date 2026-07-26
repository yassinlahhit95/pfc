import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../features/chat/data/chat_repository.dart';
import '../../features/chat/presentation/conversations_screen.dart';
import '../../features/home/presentation/home_screen.dart';
import '../../features/messages/data/messages_repository.dart';
import '../../features/messages/presentation/messages_screen.dart';
import '../../features/profile/presentation/profile_screen.dart';
import '../auth/auth_state.dart';
import '../auth/session.dart';

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
List<_Tab> _tabsFor(UserRole role) {
  final hasMessages = role != UserRole.tutor;
  final hasChat = role != UserRole.tutor;
  return [
    const _Tab('Inicio', Icons.home_rounded, HomeScreen()),
    if (hasChat) const _Tab('Chat', Icons.forum_rounded, ConversationsScreen()),
    if (hasMessages) const _Tab('Mensajes', Icons.mail_rounded, MessagesScreen()),
    const _Tab('Perfil', Icons.person_rounded, ProfileScreen()),
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

/// Maps a push notification's `data.type` to a tab index, mirroring
/// [_tabsFor]'s own conditional list so the index always matches what's
/// actually rendered for this role. Null means "stay on whatever tab is
/// already showing" (most types are dashboard cards, not dedicated tabs).
int? homeTabIndexForType(String type, UserRole role) {
  final tabs = _tabsFor(role).map((t) => t.label).toList();
  final target = switch (type) {
    'chat_message' => 'Chat',
    'message' => 'Mensajes',
    _ => null,
  };
  if (target == null) return null;
  final i = tabs.indexOf(target);
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

    final tabs = _tabsFor(role);
    final index = ref.watch(homeTabIndexProvider).clamp(0, tabs.length - 1);
    final hasChat = tabs.any((t) => t.label == 'Chat');
    final hasMessages = tabs.any((t) => t.label == 'Mensajes');

    // Only watched (and therefore only polled — see chatUnreadCountProvider/
    // messagesUnreadCountProvider) when this role actually has that tab, so a
    // tutor session never fires either poll.
    final chatUnread = hasChat ? ref.watch(chatUnreadCountProvider).valueOrNull ?? 0 : 0;
    final messagesUnread = hasMessages ? ref.watch(messagesUnreadCountProvider).valueOrNull ?? 0 : 0;

    return Scaffold(
      body: IndexedStack(
        index: index,
        children: [for (final t in tabs) t.screen],
      ),
      bottomNavigationBar: NavigationBar(
        selectedIndex: index,
        onDestinationSelected: (i) => ref.read(homeTabIndexProvider.notifier).state = i,
        destinations: [
          for (final t in tabs)
            NavigationDestination(
              icon: _badgedIcon(t.icon, switch (t.label) {
                'Chat' => chatUnread,
                'Mensajes' => messagesUnread,
                _ => 0,
              }),
              label: t.label,
            ),
        ],
      ),
    );
  }
}

/// Wraps a nav icon with an unread-count badge — mirrors the web app's
/// nav-badge (estilo.css) / notif-dot pattern: only shown when count > 0.
Widget _badgedIcon(IconData icon, int count) {
  final base = Icon(icon);
  if (count <= 0) return base;
  return Badge(
    label: Text(count > 99 ? '99+' : '$count'),
    child: base,
  );
}
