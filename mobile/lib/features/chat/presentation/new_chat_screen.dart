import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/error_modal.dart';
import '../../../core/widgets/premium.dart';
import '../data/chat_repository.dart';
import 'chat_detail_screen.dart';

class NewChatScreen extends ConsumerStatefulWidget {
  const NewChatScreen({super.key});

  @override
  ConsumerState<NewChatScreen> createState() => _NewChatScreenState();
}

class _NewChatScreenState extends ConsumerState<NewChatScreen> {
  final _searchController = TextEditingController();
  List<ChatContact> _contacts = [];
  bool _loading = true;
  Timer? _debounce;

  @override
  void initState() {
    super.initState();
    _search('');
  }

  @override
  void dispose() {
    _debounce?.cancel();
    _searchController.dispose();
    super.dispose();
  }

  void _onQueryChanged(String value) {
    _debounce?.cancel();
    _debounce = Timer(const Duration(milliseconds: 300), () => _search(value));
  }

  Future<void> _search(String query) async {
    setState(() => _loading = true);
    try {
      final contacts =
          await ref.read(chatRepositoryProvider).fetchContacts(query: query);
      if (!mounted) return;
      setState(() {
        _contacts = contacts;
        _loading = false;
      });
    } catch (_) {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _startChat(ChatContact contact) async {
    try {
      final convId = await ref
          .read(chatRepositoryProvider)
          .startConversation(targetRol: contact.rol, targetId: contact.uid);
      ref.invalidate(chatConversationsProvider);
      if (!mounted) return;
      Navigator.of(context).pushReplacement(
        MaterialPageRoute(
          builder: (_) =>
              ChatDetailScreen(convId: convId, otherNombre: contact.nombre),
        ),
      );
    } catch (_) {
      if (mounted) {
        await showErrorAlert(context, 'No se pudo iniciar la conversación.');
      }
    }
  }

  String _roleLabel(String rol) => switch (rol) {
        'admin' => 'Dirección',
        'profesor' => 'Profesor',
        'estudiante' => 'Estudiante',
        'secretaria' => 'Secretaría',
        'tutor' => 'Tutor',
        _ => rol,
      };

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Scaffold(
      appBar: AppBar(
        title: TextField(
          controller: _searchController,
          autofocus: true,
          onChanged: _onQueryChanged,
          style: Theme.of(context).textTheme.bodyLarge,
          decoration: InputDecoration(
            hintText: 'Buscar contacto',
            filled: false,
            border: InputBorder.none,
            hintStyle: TextStyle(
                color: scheme.onSurfaceVariant, fontWeight: FontWeight.normal),
            contentPadding: EdgeInsets.zero,
          ),
        ),
      ),
      body: _loading
          ? const Center(child: CircularProgressIndicator(strokeWidth: 2.4))
          : _contacts.isEmpty
              ? const EmptyState(
                  icon: Icons.person_search_outlined, title: 'Sin resultados')
              : ListView.builder(
                  padding: const EdgeInsets.symmetric(vertical: Space.sm),
                  itemCount: _contacts.length,
                  itemBuilder: (context, i) {
                    final c = _contacts[i];
                    return Material(
                      color: Colors.transparent,
                      child: InkWell(
                        onTap: () => _startChat(c),
                        child: Padding(
                          padding: const EdgeInsets.symmetric(
                              horizontal: Space.xl, vertical: Space.md),
                          child: Row(
                            children: [
                              InitialsAvatar(name: c.nombre, radius: 20),
                              const SizedBox(width: Space.md),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(c.nombre,
                                        style: const TextStyle(
                                            fontWeight: FontWeight.w500)),
                                    Text(_roleLabel(c.rol),
                                        style: Theme.of(context)
                                            .textTheme
                                            .bodySmall),
                                  ],
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    );
                  },
                ),
    );
  }
}
