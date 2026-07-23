import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';

import '../../../core/widgets/async_view.dart';
import '../data/chat_repository.dart';
import 'chat_detail_screen.dart';
import 'new_chat_screen.dart';

class ConversationsScreen extends ConsumerStatefulWidget {
  const ConversationsScreen({super.key});

  @override
  ConsumerState<ConversationsScreen> createState() => _ConversationsScreenState();
}

class _ConversationsScreenState extends ConsumerState<ConversationsScreen> {
  Timer? _pollTimer;

  @override
  void initState() {
    super.initState();
    // This screen stays mounted in the background (it's a bottom-nav tab
    // inside an IndexedStack), so this timer keeps the conversation list
    // and unread badges fresh even while another tab is showing — the
    // closest approximation to "live" we have until push notifications
    // (Phase 2 backend exists, not wired client-side yet) land.
    _pollTimer = Timer.periodic(const Duration(seconds: 8), (_) {
      if (mounted) ref.invalidate(chatConversationsProvider);
    });
  }

  @override
  void dispose() {
    _pollTimer?.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final conversationsAsync = ref.watch(chatConversationsProvider);

    return Scaffold(
      appBar: AppBar(title: const Text('Chat')),
      floatingActionButton: FloatingActionButton(
        onPressed: () => Navigator.of(context).push(
          MaterialPageRoute(builder: (_) => const NewChatScreen()),
        ),
        child: const Icon(Icons.add_comment_outlined),
      ),
      body: AsyncView<List<ChatConversation>>(
        value: conversationsAsync,
        onRetry: () => ref.invalidate(chatConversationsProvider),
        data: (context, items) {
          if (items.isEmpty) {
            return const EmptyState(
              icon: Icons.forum_outlined,
              title: 'Sin conversaciones',
              description: 'Toca + para empezar a chatear con alguien.',
            );
          }
          return RefreshIndicator(
            onRefresh: () async => ref.invalidate(chatConversationsProvider),
            child: ListView.separated(
              padding: const EdgeInsets.symmetric(vertical: 8),
              itemCount: items.length,
              separatorBuilder: (_, __) => const SizedBox(height: 2),
              itemBuilder: (context, i) => _ConversationTile(conv: items[i]),
            ),
          );
        },
      ),
    );
  }
}

class _ConversationTile extends StatelessWidget {
  const _ConversationTile({required this.conv});
  final ChatConversation conv;

  String _formatTime(String? raw) {
    if (raw == null) return '';
    final dt = DateTime.tryParse(raw.replaceFirst(' ', 'T'));
    if (dt == null) return '';
    final now = DateTime.now();
    if (dt.year == now.year && dt.month == now.month && dt.day == now.day) {
      return DateFormat.Hm().format(dt);
    }
    return DateFormat('d MMM').format(dt);
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final hasUnread = conv.unreadCount > 0;

    return ListTile(
      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
      leading: CircleAvatar(
        radius: 24,
        backgroundColor: scheme.primary.withValues(alpha: 0.12),
        child: Text(
          conv.otherNombre.isNotEmpty ? conv.otherNombre[0].toUpperCase() : '?',
          style: TextStyle(color: scheme.primary, fontWeight: FontWeight.bold),
        ),
      ),
      title: Text(
        conv.otherNombre,
        style: TextStyle(fontWeight: hasUnread ? FontWeight.bold : FontWeight.w500),
      ),
      subtitle: Text(
        conv.lastPreview ?? '',
        maxLines: 1,
        overflow: TextOverflow.ellipsis,
        style: TextStyle(
          color: hasUnread ? scheme.onSurface : scheme.onSurfaceVariant,
          fontWeight: hasUnread ? FontWeight.w600 : FontWeight.normal,
        ),
      ),
      trailing: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.end,
        children: [
          Text(_formatTime(conv.lastMessageAt), style: Theme.of(context).textTheme.bodySmall),
          const SizedBox(height: 6),
          if (hasUnread)
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 7, vertical: 2),
              decoration: BoxDecoration(color: scheme.primary, borderRadius: BorderRadius.circular(10)),
              child: Text(
                '${conv.unreadCount}',
                style: TextStyle(color: scheme.onPrimary, fontSize: 11, fontWeight: FontWeight.bold),
              ),
            ),
        ],
      ),
      onTap: () => Navigator.of(context).push(
        MaterialPageRoute(
          builder: (_) => ChatDetailScreen(
            convId: conv.id,
            otherNombre: conv.otherNombre,
          ),
        ),
      ),
    );
  }
}
