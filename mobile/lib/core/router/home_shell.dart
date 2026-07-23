import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../features/chat/presentation/conversations_screen.dart';
import '../../features/home/presentation/home_screen.dart';
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
/// Tutors don't use the mensajería (reclamaciones) system — chat only.
List<_Tab> _tabsFor(UserRole role) {
  final hasMessages = role != UserRole.tutor;
  return [
    const _Tab('Inicio', Icons.home_rounded, HomeScreen()),
    const _Tab('Chat', Icons.forum_rounded, ConversationsScreen()),
    if (hasMessages) const _Tab('Mensajes', Icons.mail_rounded, MessagesScreen()),
    const _Tab('Perfil', Icons.person_rounded, ProfileScreen()),
  ];
}

class HomeShell extends ConsumerStatefulWidget {
  const HomeShell({super.key});

  @override
  ConsumerState<HomeShell> createState() => _HomeShellState();
}

class _HomeShellState extends ConsumerState<HomeShell> {
  int _index = 0;

  @override
  Widget build(BuildContext context) {
    final role = ref.watch(sessionControllerProvider).valueOrNull?.role;
    if (role == null) return const SizedBox.shrink(); // router redirects away

    final tabs = _tabsFor(role);
    final index = _index.clamp(0, tabs.length - 1);

    return Scaffold(
      body: IndexedStack(
        index: index,
        children: [for (final t in tabs) t.screen],
      ),
      bottomNavigationBar: NavigationBar(
        selectedIndex: index,
        onDestinationSelected: (i) => setState(() => _index = i),
        destinations: [
          for (final t in tabs)
            NavigationDestination(icon: Icon(t.icon), label: t.label),
        ],
      ),
    );
  }
}
