import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';

import '../../../core/auth/auth_state.dart';
import '../../../core/auth/session.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/error_modal.dart';
import '../data/chat_repository.dart';

class ChatDetailScreen extends ConsumerStatefulWidget {
  const ChatDetailScreen({super.key, required this.convId, required this.otherNombre});

  final int convId;
  final String otherNombre;

  @override
  ConsumerState<ChatDetailScreen> createState() => _ChatDetailScreenState();
}

class _ChatDetailScreenState extends ConsumerState<ChatDetailScreen> with WidgetsBindingObserver {
  final _messages = <ChatMessage>[];
  final _scrollController = ScrollController();
  final _inputController = TextEditingController();
  Timer? _pollTimer;
  bool _loading = true;
  bool _sending = false;
  bool _polling = false;
  Object? _error;
  AppLifecycleState _lastLifecycleState = AppLifecycleState.resumed;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
    _loadInitial();
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    _pollTimer?.cancel();
    _scrollController.dispose();
    _inputController.dispose();
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    _lastLifecycleState = state;
    if (state == AppLifecycleState.paused || state == AppLifecycleState.detached) {
      _pollTimer?.cancel();
      _pollTimer = null;
    } else if (state == AppLifecycleState.resumed) {
      _startPolling();
      if (mounted) _poll();
    }
  }

  Future<void> _loadInitial() async {
    try {
      final repo = ref.read(chatRepositoryProvider);
      final messages = await repo.fetchMessages(widget.convId);
      if (!mounted) return;
      setState(() {
        _messages.addAll(messages);
        _loading = false;
      });
      _scrollToBottom();
      _startPolling();
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _error = e;
        _loading = false;
      });
    }
  }

  void _startPolling() {
    if (_lastLifecycleState != AppLifecycleState.resumed) return;
    // ponytail: adaptive polling: 8s normal, stops when backgrounded
    _pollTimer = Timer.periodic(const Duration(seconds: 8), (_) => _poll());
  }

  Future<void> _poll() async {
    if (_polling || _lastLifecycleState != AppLifecycleState.resumed) return;
    _polling = true;
    try {
      final repo = ref.read(chatRepositoryProvider);
      final lastId = _messages.isNotEmpty ? _messages.last.id : null;
      final newOnes = await repo.fetchMessages(widget.convId, after: lastId);
      if (!mounted) {
        _polling = false;
        return;
      }

      // Server-side cursor (after: lastId) already deduplicates — no need for client-side Set
      // Mark any pending messages as sent (server confirmed delivery)
      setState(() {
        // Remove pending messages since they're being replaced with real ones from server
        _messages.removeWhere((m) => m.pending);
        // Add the confirmed real messages
        _messages.addAll(newOnes);
      });

      if (newOnes.isNotEmpty) {
        _scrollToBottom();
      }
    } catch (_) {
      // Silent — this is a background refresh, don't interrupt the user.
    } finally {
      _polling = false;
    }
  }

  void _scrollToBottom() {
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (!_scrollController.hasClients) return;
      _scrollController.animateTo(
        _scrollController.position.maxScrollExtent,
        duration: const Duration(milliseconds: 250),
        curve: Curves.easeOut,
      );
    });
  }

  Future<void> _send() async {
    final text = _inputController.text.trim();
    if (text.isEmpty || _sending) return;

    final session = ref.read(sessionControllerProvider).valueOrNull;
    final myId = session?.userId;
    final myRol = session?.role == UserRole.director ? 'admin' : session?.role.name;

    // Optimistic insert — add pending message immediately
    if (myId != null && myRol != null) {
      setState(() {
        _messages.add(ChatMessage(
          id: DateTime.now().millisecondsSinceEpoch, // Temporary ID
          emisorRol: myRol,
          emisorId: myId,
          emisorNombre: 'Tú', // Display as "You" for pending messages
          contenido: text,
          fecha: DateTime.now().toString(),
          leido: true,
          pending: true,
        ));
      });
      _scrollToBottom();
    }

    _inputController.clear();
    setState(() { _sending = true; });
    try {
      final repo = ref.read(chatRepositoryProvider);
      await repo.sendMessage(convId: widget.convId, contenido: text);
      // Poll to get the real ID and confirm delivery
      await _poll();
    } catch (e) {
      if (mounted) {
        await showErrorAlert(context, 'No se pudo enviar el mensaje.');
        // Remove optimistic message on error
        setState(() {
          _messages.removeWhere((m) => m.pending && m.contenido == text);
        });
      }
    } finally {
      if (mounted) {
        setState(() { _sending = false; });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    // A push for THIS conversation arrived while it's open — poll right now
    // instead of waiting for the next 4s tick, so it doesn't look frozen.
    ref.listen(chatMessagePushProvider, (previous, convId) {
      if (convId == widget.convId) _poll();
    });

    final session = ref.watch(sessionControllerProvider).valueOrNull;
    final myId = session?.userId;
    // Chat's role strings use 'admin' for director (see api/v1/chat.php).
    final myRol = session?.role == UserRole.director ? 'admin' : session?.role.name;

    return Scaffold(
      appBar: AppBar(title: Text(widget.otherNombre)),
      body: Column(
        children: [
          Expanded(
            child: _loading
                ? const Center(child: CircularProgressIndicator())
                : _error != null
                    ? Center(
                        child: Column(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            const Text('No se pudo cargar la conversación.'),
                            TextButton(onPressed: _loadInitial, child: const Text('Reintentar')),
                          ],
                        ),
                      )
                    : ListView.builder(
                        controller: _scrollController,
                        padding: const EdgeInsets.all(12),
                        itemCount: _messages.length,
                        itemBuilder: (context, i) {
                          final m = _messages[i];
                          final isMine = m.emisorId == myId && m.emisorRol == myRol;
                          return _Bubble(message: m, isMine: isMine);
                        },
                      ),
          ),
          _Composer(controller: _inputController, sending: _sending, onSend: _send),
        ],
      ),
    );
  }
}

class _Bubble extends StatelessWidget {
  const _Bubble({required this.message, required this.isMine});
  final ChatMessage message;
  final bool isMine;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final time = DateTime.tryParse(message.fecha.replaceFirst(' ', 'T'));

    return Align(
      alignment: isMine ? Alignment.centerRight : Alignment.centerLeft,
      child: Container(
        margin: const EdgeInsets.symmetric(vertical: 3),
        padding: const EdgeInsets.symmetric(horizontal: Space.lg, vertical: Space.sm),
        constraints: BoxConstraints(maxWidth: MediaQuery.of(context).size.width * 0.75),
        decoration: BoxDecoration(
          color: isMine ? scheme.primary : scheme.surfaceContainerHighest,
          borderRadius: BorderRadius.only(
            topLeft: const Radius.circular(Radii.lg),
            topRight: const Radius.circular(Radii.lg),
            bottomLeft: Radius.circular(isMine ? Radii.lg : 4),
            bottomRight: Radius.circular(isMine ? 4 : Radii.lg),
          ),
          border: message.pending ? Border.all(color: scheme.outlineVariant, width: 1) : null,
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.end,
          mainAxisSize: MainAxisSize.min,
          children: [
            Opacity(
              opacity: message.pending ? 0.7 : 1.0,
              child: Text(
                message.contenido,
                style: TextStyle(color: isMine ? scheme.onPrimary : scheme.onSurface),
              ),
            ),
            const SizedBox(height: 3),
            Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                Text(
                  time != null ? DateFormat.Hm().format(time) : '',
                  style: TextStyle(
                    fontSize: 10,
                    color: (isMine ? scheme.onPrimary : scheme.onSurface).withValues(alpha: 0.65),
                  ),
                ),
                if (isMine) ...[
                  const SizedBox(width: 4),
                  if (message.pending)
                    SizedBox(
                      width: 10,
                      height: 10,
                      child: CircularProgressIndicator(
                        strokeWidth: 1.5,
                        valueColor: AlwaysStoppedAnimation<Color>(scheme.onPrimary),
                      ),
                    )
                  else if (message.readAt != null)
                    // Double checkmark: read by recipient
                    Opacity(
                      opacity: 0.8,
                      child: Text(
                        '✓✓',
                        style: TextStyle(
                          fontSize: 10,
                          color: scheme.primary,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    )
                  else
                    // Single checkmark: sent to server
                    Opacity(
                      opacity: 0.6,
                      child: Text(
                        '✓',
                        style: TextStyle(
                          fontSize: 10,
                          color: isMine ? scheme.onPrimary : scheme.onSurface,
                        ),
                      ),
                    ),
                ],
              ],
            ),
          ],
        ),
      ),
    );
  }
}

class _Composer extends StatelessWidget {
  const _Composer({required this.controller, required this.sending, required this.onSend});
  final TextEditingController controller;
  final bool sending;
  final VoidCallback onSend;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return SafeArea(
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
                controller: controller,
                minLines: 1,
                maxLines: 4,
                textInputAction: TextInputAction.send,
                onSubmitted: (_) => onSend(),
                decoration: InputDecoration(
                  hintText: 'Mensaje',
                  contentPadding: const EdgeInsets.symmetric(horizontal: Space.lg, vertical: Space.md),
                  border: OutlineInputBorder(borderRadius: BorderRadius.circular(Radii.pill), borderSide: BorderSide.none),
                ),
              ),
            ),
            const SizedBox(width: Space.sm),
            IconButton.filled(
              onPressed: sending ? null : onSend,
              icon: sending
                  ? SizedBox(
                      width: 18,
                      height: 18,
                      child: CircularProgressIndicator(
                        strokeWidth: 2,
                        valueColor: AlwaysStoppedAnimation<Color>(Theme.of(context).colorScheme.onPrimary),
                      ),
                    )
                  : const Icon(Icons.arrow_upward_rounded),
            ),
          ],
        ),
      ),
    );
  }
}
