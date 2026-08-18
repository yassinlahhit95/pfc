import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/i18n/translations.dart';
import '../data/messages_repository.dart';
import 'new_thread_screen.dart';
import 'thread_detail_screen.dart';

class MessagesScreen extends ConsumerWidget {
  const MessagesScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final threadsAsync = ref.watch(messageThreadsProvider);
    final scheme = Theme.of(context).colorScheme;
    final t = ref.watch(translationsProvider);

    return Scaffold(
      appBar: AppBar(title: Text(t['nav_mensajeria'] ?? 'Mensajería')),
      body: AsyncView<List<MessageThread>>(
        value: threadsAsync,
        onRetry: () => ref.invalidate(messageThreadsProvider),
        data: (context, items) {
          if (items.isEmpty) {
            return const EmptyState(
              icon: Icons.mail_outline_rounded,
              title: 'Sin mensajes',
            );
          }
          return RefreshIndicator(
            onRefresh: () async => ref.invalidate(messageThreadsProvider),
            child: ListView.builder(
              padding: const EdgeInsets.symmetric(vertical: Space.sm),
              itemCount: items.length,
              itemBuilder: (context, i) {
                final t = items[i];
                final other =
                    t.nombreEstudiante ?? t.nombreProfesor ?? 'Dirección';
                return Material(
                  color: Colors.transparent,
                  child: InkWell(
                    onTap: () => Navigator.of(context).push(
                      MaterialPageRoute(
                          builder: (_) => ThreadDetailScreen(
                              threadId: t.id, asunto: t.asunto)),
                    ),
                    child: Padding(
                      padding: const EdgeInsets.symmetric(
                          horizontal: Space.xl, vertical: Space.md),
                      child: Row(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Padding(
                            padding: const EdgeInsets.only(top: 6),
                            child: Container(
                              width: 7,
                              height: 7,
                              decoration: BoxDecoration(
                                color: t.leido
                                    ? Colors.transparent
                                    : scheme.primary,
                                shape: BoxShape.circle,
                              ),
                            ),
                          ),
                          const SizedBox(width: Space.md),
                          Expanded(
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  children: [
                                    Expanded(
                                      child: Text(
                                        t.asunto,
                                        style: TextStyle(
                                            fontWeight: t.leido
                                                ? FontWeight.w500
                                                : FontWeight.w700),
                                      ),
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 2),
                                Text(
                                  '$other · ${t.descripcion}',
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                  style: Theme.of(context).textTheme.bodySmall,
                                ),
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
        },
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () => Navigator.of(context).push(
          MaterialPageRoute(builder: (_) => const NewThreadScreen()),
        ),
        child: const Icon(Icons.edit_note_rounded),
      ),
    );
  }
}
