import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/premium.dart';
import '../../chat/data/chat_repository.dart';
import '../data/messages_repository.dart';

class NewThreadScreen extends ConsumerStatefulWidget {
  const NewThreadScreen({super.key});

  @override
  ConsumerState<NewThreadScreen> createState() => _NewThreadScreenState();
}

class _NewThreadScreenState extends ConsumerState<NewThreadScreen> {
  final _searchController = TextEditingController();
  List<ChatContact> _contacts = [];
  bool _loading = true;
  Timer? _debounce;
  ChatContact? _selectedContact;

  final _asuntoController = TextEditingController();
  final _descController = TextEditingController();
  bool _sending = false;

  @override
  void initState() {
    super.initState();
    _search('');
  }

  @override
  void dispose() {
    _debounce?.cancel();
    _searchController.dispose();
    _asuntoController.dispose();
    _descController.dispose();
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

  Future<void> _send() async {
    final contact = _selectedContact;
    if (contact == null) return;
    final asunto = _asuntoController.text.trim();
    final desc = _descController.text.trim();

    if (asunto.isEmpty || desc.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('Por favor, completa el asunto y el mensaje.')),
      );
      return;
    }

    setState(() => _sending = true);
    try {
      await ref.read(messagesRepositoryProvider).createThread(
            asunto: asunto,
            descripcion: desc,
            idProfesor: contact.rol == 'profesor' ? contact.uid : null,
            idEstudiante: contact.rol == 'estudiante' ? contact.uid : null,
          );
      ref.invalidate(messageThreadsProvider);
      if (!mounted) return;
      Navigator.of(context).pop();
    } catch (_) {
      setState(() => _sending = false);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('No se pudo enviar el mensaje.')),
        );
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

    if (_selectedContact != null) {
      final c = _selectedContact!;
      return Scaffold(
        appBar: AppBar(title: const Text('Nuevo mensaje')),
        body: SingleChildScrollView(
          padding: const EdgeInsets.all(Space.xl),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              // Contact Card
              AppCard(
                child: Row(
                  children: [
                    InitialsAvatar(name: c.nombre, radius: 20),
                    const SizedBox(width: Space.md),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(c.nombre, style: const TextStyle(fontWeight: FontWeight.w600)),
                          Text('Destinatario: ${_roleLabel(c.rol)}', style: Theme.of(context).textTheme.bodySmall),
                        ],
                      ),
                    ),
                    IconButton(
                      icon: const Icon(Icons.close_rounded),
                      onPressed: () => setState(() => _selectedContact = null),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: Space.xl),
              TextField(
                controller: _asuntoController,
                decoration: const InputDecoration(
                  labelText: 'Asunto',
                  hintText: 'Ej: Duda sobre entrega / Reclamación examen',
                ),
              ),
              const SizedBox(height: Space.lg),
              TextField(
                controller: _descController,
                minLines: 4,
                maxLines: 8,
                decoration: const InputDecoration(
                  labelText: 'Mensaje',
                  hintText: 'Escribe tu mensaje aquí...',
                ),
              ),
              const SizedBox(height: Space.xl + Space.md),
              FilledButton(
                onPressed: _sending ? null : _send,
                child: _sending
                    ? SizedBox(
                        height: 18,
                        width: 18,
                        child: CircularProgressIndicator(
                          strokeWidth: 2,
                          valueColor: AlwaysStoppedAnimation<Color>(Theme.of(context).colorScheme.onPrimary),
                        ),
                      )
                    : const Text('Enviar mensaje'),
              ),
            ],
          ),
        ),
      );
    }

    return Scaffold(
      appBar: AppBar(
        title: TextField(
          controller: _searchController,
          autofocus: true,
          onChanged: _onQueryChanged,
          style: Theme.of(context).textTheme.bodyLarge,
          decoration: InputDecoration(
            hintText: 'Buscar destinatario',
            filled: false,
            border: InputBorder.none,
            hintStyle: TextStyle(color: scheme.onSurfaceVariant, fontWeight: FontWeight.normal),
            contentPadding: EdgeInsets.zero,
          ),
        ),
      ),
      body: _loading
          ? const Center(child: CircularProgressIndicator(strokeWidth: 2.4))
          : _contacts.isEmpty
              ? const EmptyState(icon: Icons.person_search_outlined, title: 'Sin resultados')
              : ListView.builder(
                  padding: const EdgeInsets.symmetric(vertical: Space.sm),
                  itemCount: _contacts.length,
                  itemBuilder: (context, i) {
                    final c = _contacts[i];
                    return Material(
                      color: Colors.transparent,
                      child: InkWell(
                        onTap: () => setState(() => _selectedContact = c),
                        child: Padding(
                          padding: const EdgeInsets.symmetric(horizontal: Space.xl, vertical: Space.md),
                          child: Row(
                            children: [
                              InitialsAvatar(name: c.nombre, radius: 20),
                              const SizedBox(width: Space.md),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(c.nombre, style: const TextStyle(fontWeight: FontWeight.w500)),
                                    Text(_roleLabel(c.rol), style: Theme.of(context).textTheme.bodySmall),
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
