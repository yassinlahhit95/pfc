import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

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
      final contacts = await ref.read(chatRepositoryProvider).fetchContacts(query: query);
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
          builder: (_) => ChatDetailScreen(convId: convId, otherNombre: contact.nombre),
        ),
      );
    } catch (_) {
      if (mounted) {
        ScaffoldMessenger.of(context)
            .showSnackBar(const SnackBar(content: Text('No se pudo iniciar la conversación.')));
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
    return Scaffold(
      appBar: AppBar(
        title: TextField(
          controller: _searchController,
          autofocus: true,
          onChanged: _onQueryChanged,
          decoration: const InputDecoration(
            hintText: 'Buscar contacto…',
            border: InputBorder.none,
          ),
        ),
      ),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : _contacts.isEmpty
              ? const Center(child: Text('Sin resultados'))
              : ListView.builder(
                  itemCount: _contacts.length,
                  itemBuilder: (context, i) {
                    final c = _contacts[i];
                    return ListTile(
                      leading: CircleAvatar(
                        child: Text(c.nombre.isNotEmpty ? c.nombre[0].toUpperCase() : '?'),
                      ),
                      title: Text(c.nombre),
                      subtitle: Text(_roleLabel(c.rol)),
                      onTap: () => _startChat(c),
                    );
                  },
                ),
    );
  }
}
