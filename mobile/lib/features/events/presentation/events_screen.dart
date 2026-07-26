import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/premium.dart';
import '../data/events_repository.dart';

class EventsScreen extends ConsumerWidget {
  const EventsScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final eventsAsync = ref.watch(eventsProvider);

    return Scaffold(
      appBar: AppBar(title: const Text('Eventos')),
      body: AsyncView<List<SchoolEvent>>(
        value: eventsAsync,
        onRetry: () => ref.invalidate(eventsProvider),
        data: (context, items) {
          if (items.isEmpty) {
            return const EmptyState(
              icon: Icons.event_outlined,
              title: 'No hay eventos próximos',
            );
          }
          return RefreshIndicator(
            onRefresh: () async => ref.invalidate(eventsProvider),
            child: ListView.separated(
              padding: const EdgeInsets.fromLTRB(Space.xl, Space.lg, Space.xl, Space.xxxl),
              itemCount: items.length,
              separatorBuilder: (_, __) => const SizedBox(height: Space.md),
              itemBuilder: (context, i) => _EventCard(item: items[i]),
            ),
          );
        },
      ),
    );
  }
}

class _EventCard extends StatelessWidget {
  const _EventCard({required this.item});
  final SchoolEvent item;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final date = DateTime.tryParse(item.fecha);

    return AppCard(
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Column(
            children: [
              Text(
                date != null ? DateFormat('d').format(date) : '–',
                style: Theme.of(context).textTheme.titleLarge?.copyWith(height: 1),
              ),
              Text(
                date != null ? DateFormat('MMM').format(date).toUpperCase() : '',
                style: Theme.of(context).textTheme.labelSmall?.copyWith(color: scheme.onSurfaceVariant),
              ),
            ],
          ),
          const SizedBox(width: Space.lg),
          Container(width: 1, height: 34, color: scheme.outlineVariant),
          const SizedBox(width: Space.lg),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(item.titulo, style: const TextStyle(fontWeight: FontWeight.w600)),
                if (item.descripcion?.isNotEmpty == true) ...[
                  const SizedBox(height: 4),
                  Text(item.descripcion!, maxLines: 2, overflow: TextOverflow.ellipsis,
                      style: Theme.of(context).textTheme.bodySmall),
                ],
                if (item.ubicacion?.isNotEmpty == true) ...[
                  const SizedBox(height: Space.sm),
                  Row(
                    children: [
                      Icon(Icons.place_outlined, size: 14, color: scheme.onSurfaceVariant),
                      const SizedBox(width: 4),
                      Text(item.ubicacion!, style: Theme.of(context).textTheme.bodySmall),
                    ],
                  ),
                ],
              ],
            ),
          ),
        ],
      ),
    );
  }
}
