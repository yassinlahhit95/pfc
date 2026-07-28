import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/premium.dart';
import '../data/chat_repository.dart';
import 'chat_detail_screen.dart';
import 'new_chat_screen.dart';

class ConversationsScreen extends ConsumerStatefulWidget {
  const ConversationsScreen({super.key});

  @override
  ConsumerState<ConversationsScreen> createState() => _ConversationsScreenState();
}

class _ConversationsScreenState extends ConsumerState<ConversationsScreen> with WidgetsBindingObserver {
  Timer? _pollTimer;
  AppLifecycleState? _lastLifecycleState;
  // ponytail: adaptive backoff: starts at 30s when backgrounded, 8s when foregrounded
  int _pollIntervalSeconds = 8;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
    _startPolling();
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    _pollTimer?.cancel();
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    _lastLifecycleState = state;
    if (state == AppLifecycleState.paused || state == AppLifecycleState.detached) {
      // App backgrounded — stop polling to save battery
      _pollTimer?.cancel();
      _pollTimer = null;
    } else if (state == AppLifecycleState.resumed) {
      // App foregrounded — resume polling at normal rate
      _pollIntervalSeconds = 8;
      _startPolling();
      if (mounted) ref.invalidate(chatConversationsProvider);
    }
  }

  void _startPolling() {
    _pollTimer?.cancel();
    _pollTimer = Timer.periodic(Duration(seconds: _pollIntervalSeconds), (_) {
      if (mounted && _lastLifecycleState == AppLifecycleState.resumed) {
        ref.invalidate(chatConversationsProvider);
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    // Any incoming chat push (any conversation) refreshes the list instantly
    // instead of waiting for the 8s background timer.
    ref.listen(chatMessagePushProvider, (previous, convId) {
      if (convId != null) ref.invalidate(chatConversationsProvider);
    });

    final conversationsAsync = ref.watch(chatConversationsProvider);

    return Scaffold(
      appBar: AppBar(title: const Text('Chat')),
      floatingActionButton: FloatingActionButton(
        onPressed: () => Navigator.of(context).push(
          MaterialPageRoute(builder: (_) => const NewChatScreen()),
        ),
        elevation: 1,
        child: const Icon(Icons.edit_outlined),
      ),
      body: AsyncView<List<ChatConversation>>(
        value: conversationsAsync,
        onRetry: () => ref.invalidate(chatConversationsProvider),
        data: (context, items) {
          if (items.isEmpty) {
            return const EmptyState(
              icon: Icons.forum_outlined,
              title: 'Sin conversaciones',
              description: 'Toca el lápiz para empezar a chatear con alguien.',
            );
          }
          return RefreshIndicator(
            onRefresh: () async => ref.invalidate(chatConversationsProvider),
            child: ListView.separated(
              padding: const EdgeInsets.symmetric(vertical: Space.sm),
              itemCount: items.length,
              separatorBuilder: (_, __) => const SizedBox.shrink(),
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

    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: () => Navigator.of(context).push(
          MaterialPageRoute(
            builder: (_) => ChatDetailScreen(convId: conv.id, otherNombre: conv.otherNombre),
          ),
        ),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: Space.xl, vertical: Space.md),
          child: Row(
            children: [
              InitialsAvatar(name: conv.otherNombre, radius: 22),
              const SizedBox(width: Space.md),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      conv.otherNombre,
                      style: TextStyle(fontWeight: hasUnread ? FontWeight.w700 : FontWeight.w500),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      conv.lastPreview ?? '',
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: Theme.of(context).textTheme.bodySmall?.copyWith(
                            color: hasUnread ? scheme.onSurface : scheme.onSurfaceVariant,
                            fontWeight: hasUnread ? FontWeight.w600 : FontWeight.normal,
                          ),
                    ),
                  ],
                ),
              ),
              const SizedBox(width: Space.sm),
              Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  Text(
                    _formatTime(conv.lastMessageAt),
                    style: Theme.of(context).textTheme.bodySmall?.copyWith(
                          color: hasUnread ? scheme.primary : scheme.onSurfaceVariant,
                          fontWeight: hasUnread ? FontWeight.w600 : FontWeight.normal,
                        ),
                  ),
                  const SizedBox(height: 6),
                  if (hasUnread)
                    Container(
                      width: 18,
                      height: 18,
                      alignment: Alignment.center,
                      decoration: BoxDecoration(color: scheme.primary, shape: BoxShape.circle),
                      child: Text(
                        '${conv.unreadCount}',
                        style: TextStyle(color: scheme.onPrimary, fontSize: 10, fontWeight: FontWeight.w700),
                      ),
                    )
                  else
                    const SizedBox(height: 18),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }
}
