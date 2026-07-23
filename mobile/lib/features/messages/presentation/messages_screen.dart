import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/widgets/async_view.dart';
import '../data/messages_repository.dart';
import 'thread_detail_screen.dart';

class MessagesScreen extends ConsumerWidget {
  const MessagesScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final threadsAsync = ref.watch(messageThreadsProvider);

    return Scaffold(
      appBar: AppBar(title: const Text('Mensajes')),
      body: AsyncView<List<MessageThread>>(
        value: threadsAsync,
        onRetry: () => ref.invalidate(messageThreadsProvider),
        data: (context, items) {
          if (items.isEmpty) {
            return const EmptyState(
              icon: Icons.mail_outline,
              title: 'Sin mensajes',
            );
          }
          return RefreshIndicator(
            onRefresh: () async => ref.invalidate(messageThreadsProvider),
            child: ListView.separated(
              padding: const EdgeInsets.symmetric(vertical: 8),
              itemCount: items.length,
              separatorBuilder: (_, __) => const Divider(height: 1),
              itemBuilder: (context, i) {
                final t = items[i];
                final other = t.nombreEstudiante ?? t.nombreProfesor ?? 'Dirección';
                return ListTile(
                  leading: CircleAvatar(
                    backgroundColor: t.leido
                        ? Theme.of(context).colorScheme.surfaceContainerHighest
                        : Theme.of(context).colorScheme.primary.withValues(alpha: 0.15),
                    child: Icon(
                      Icons.mail_outline,
                      color: t.leido ? null : Theme.of(context).colorScheme.primary,
                    ),
                  ),
                  title: Text(
                    t.asunto,
                    style: TextStyle(fontWeight: t.leido ? FontWeight.normal : FontWeight.bold),
                  ),
                  subtitle: Text('$other · ${t.descripcion}', maxLines: 1, overflow: TextOverflow.ellipsis),
                  onTap: () => Navigator.of(context).push(
                    MaterialPageRoute(builder: (_) => ThreadDetailScreen(threadId: t.id, asunto: t.asunto)),
                  ),
                );
              },
            ),
          );
        },
      ),
    );
  }
}
