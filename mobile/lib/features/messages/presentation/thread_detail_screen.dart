import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';

import '../../../core/auth/auth_state.dart';
import '../../../core/auth/session.dart';
import '../../../core/theme/app_theme.dart';
import '../data/messages_repository.dart';

class ThreadDetailScreen extends ConsumerStatefulWidget {
  const ThreadDetailScreen({super.key, required this.threadId, required this.asunto});

  final int threadId;
  final String asunto;

  @override
  ConsumerState<ThreadDetailScreen> createState() => _ThreadDetailScreenState();
}

class _ThreadDetailScreenState extends ConsumerState<ThreadDetailScreen> {
  final _replyController = TextEditingController();
  List<MessageThread>? _messages;
  bool _loading = true;
  bool _sending = false;

  @override
  void initState() {
    super.initState();
    _load();
  }

  @override
  void dispose() {
    _replyController.dispose();
    super.dispose();
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final repo = ref.read(messagesRepositoryProvider);
      final thread = await repo.fetchThread(widget.threadId);
      await repo.markRead(widget.threadId);
      if (!mounted) return;
      setState(() {
        _messages = thread;
        _loading = false;
      });
    } catch (_) {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _send() async {
    final text = _replyController.text.trim();
    if (text.isEmpty || _sending) return;
    setState(() => _sending = true);
    try {
      await ref.read(messagesRepositoryProvider).reply(idParent: widget.threadId, contenido: text);
      _replyController.clear();
      await _load();
      ref.invalidate(messageThreadsProvider);
    } catch (_) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('No se pudo enviar la respuesta.')));
      }
    } finally {
      if (mounted) setState(() => _sending = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final role = ref.watch(sessionControllerProvider).valueOrNull?.role;
    final myEmisorRol = (role == UserRole.director || role == UserRole.secretaria)
        ? 'admin'
        : role?.name;

    return Scaffold(
      appBar: AppBar(title: Text(widget.asunto)),
      body: Column(
        children: [
          Expanded(
            child: _loading
                ? const Center(child: CircularProgressIndicator(strokeWidth: 2.4))
                : (_messages == null || _messages!.isEmpty)
                    ? const Center(child: Text('No se pudo cargar el hilo.'))
                    : ListView.builder(
                        padding: const EdgeInsets.all(Space.lg),
                        itemCount: _messages!.length,
                        itemBuilder: (context, i) {
                          final m = _messages![i];
                          return _MessageCard(message: m, isMine: m.emisorRol == myEmisorRol);
                        },
                      ),
          ),
          SafeArea(
            child: Container(
              decoration: BoxDecoration(
                color: scheme.surface,
                border: Border(top: BorderSide(color: scheme.outlineVariant)),
              ),
              padding: const EdgeInsets.fromLTRB(Space.md, Space.sm, Space.md, Space.sm),
              child: Row(
                children: [
                  Expanded(
                    child: TextField(
                      controller: _replyController,
                      minLines: 1,
                      maxLines: 4,
                      decoration: InputDecoration(
                        hintText: 'Responder',
                        contentPadding: const EdgeInsets.symmetric(horizontal: Space.lg, vertical: Space.md),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(Radii.pill),
                          borderSide: BorderSide.none,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: Space.sm),
                  IconButton.filled(
                    onPressed: _sending ? null : _send,
                    icon: _sending
                        ? const SizedBox(width: 18, height: 18, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                        : const Icon(Icons.arrow_upward_rounded),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _MessageCard extends StatelessWidget {
  const _MessageCard({required this.message, required this.isMine});
  final MessageThread message;
  final bool isMine;

  String _roleLabel(String rol) => switch (rol) {
        'admin' => 'Dirección',
        'profesor' => 'Profesor',
        'estudiante' => 'Estudiante',
        _ => rol,
      };

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final time = DateTime.tryParse(message.fecha.replaceFirst(' ', 'T'));

    return Align(
      alignment: isMine ? Alignment.centerRight : Alignment.centerLeft,
      child: Container(
        margin: const EdgeInsets.symmetric(vertical: Space.xs),
        padding: const EdgeInsets.all(Space.md),
        constraints: BoxConstraints(maxWidth: MediaQuery.of(context).size.width * 0.8),
        decoration: BoxDecoration(
          color: isMine ? scheme.primary.withValues(alpha: 0.1) : scheme.surfaceContainerHighest,
          borderRadius: BorderRadius.circular(Radii.md),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(_roleLabel(message.emisorRol),
                style: Theme.of(context).textTheme.labelSmall?.copyWith(color: scheme.primary)),
            const SizedBox(height: 4),
            Text(message.descripcion),
            const SizedBox(height: 4),
            Text(
              time != null ? DateFormat('d MMM, HH:mm').format(time) : '',
              style: Theme.of(context).textTheme.bodySmall,
            ),
          ],
        ),
      ),
    );
  }
}
