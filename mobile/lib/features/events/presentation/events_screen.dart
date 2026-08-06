import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';

import '../../../core/auth/auth_state.dart';
import '../../../core/auth/session.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/utils/debounce.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/premium.dart';
import '../data/events_repository.dart';
import 'add_event_screen.dart';

class EventsScreen extends ConsumerStatefulWidget {
  const EventsScreen({super.key});

  @override
  ConsumerState<EventsScreen> createState() => _EventsScreenState();
}

class _EventsScreenState extends ConsumerState<EventsScreen> {
  String _searchQuery = '';
  final _debounce = Debounce();

  @override
  void dispose() {
    _debounce.cancel();
    super.dispose();
  }

  void _deleteEvent(SchoolEvent event) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (c) => AlertDialog(
        title: const Text('Eliminar evento'),
        content:
            const Text('¿Estás seguro de que quieres eliminar este evento?'),
        actions: [
          TextButton(
              onPressed: () => Navigator.pop(c, false),
              child: const Text('Cancelar')),
          TextButton(
              onPressed: () => Navigator.pop(c, true),
              child: const Text('Eliminar')),
        ],
      ),
    );
    if (confirm != true) return;

    try {
      await ref.read(eventsRepositoryProvider).deleteEvent(event.id);
      ref.invalidate(eventsProvider);
    } catch (e) {
      if (mounted)
        ScaffoldMessenger.of(context)
            .showSnackBar(SnackBar(content: Text('Error: $e')));
    }
  }

  @override
  Widget build(BuildContext context) {
    final eventsAsync = ref.watch(eventsProvider);
    final session = ref.watch(sessionControllerProvider).value;
    final role = session?.role;
    final isBackOffice =
        role == UserRole.director || role == UserRole.secretaria;

    return Scaffold(
      appBar: AppBar(title: const Text('Eventos')),
      floatingActionButton: isBackOffice
          ? FloatingActionButton(
              onPressed: () {
                Navigator.push(
                  context,
                  MaterialPageRoute(builder: (_) => const AddEventScreen()),
                );
              },
              child: const Icon(Icons.add),
            )
          : null,
      body: AsyncView<List<SchoolEvent>>(
        value: eventsAsync,
        onRetry: () => ref.invalidate(eventsProvider),
        data: (context, allItems) {
          if (allItems.isEmpty) {
            return const EmptyState(
              icon: Icons.event_outlined,
              title: 'No hay eventos próximos',
            );
          }

          final items = allItems.where((e) {
            final matchesSearch = _searchQuery.isEmpty ||
                e.titulo.toLowerCase().contains(_searchQuery) ||
                (e.descripcion != null &&
                    e.descripcion!.toLowerCase().contains(_searchQuery)) ||
                (e.ubicacion != null &&
                    e.ubicacion!.toLowerCase().contains(_searchQuery));
            return matchesSearch;
          }).toList();

          return Column(
            children: [
              Padding(
                padding:
                    const EdgeInsets.fromLTRB(Space.xl, Space.md, Space.xl, 0),
                child: TextField(
                  decoration: const InputDecoration(
                    labelText: 'Buscar eventos...',
                    prefixIcon: Icon(Icons.search_rounded),
                    contentPadding:
                        EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                  ),
                  onChanged: (val) {
                    _debounce(const Duration(milliseconds: 300), () {
                      setState(() {
                        _searchQuery = val.trim().toLowerCase();
                      });
                    });
                  },
                ),
              ),
              const SizedBox(height: Space.sm),
              Expanded(
                child: items.isEmpty
                    ? const EmptyState(
                        icon: Icons.filter_alt_off_outlined,
                        title: 'Sin resultados para estos filtros')
                    : RefreshIndicator(
                        onRefresh: () async => ref.invalidate(eventsProvider),
                        child: ListView.separated(
                          padding: const EdgeInsets.fromLTRB(
                              Space.xl, Space.sm, Space.xl, Space.xxxl),
                          itemCount: items.length,
                          separatorBuilder: (_, __) =>
                              const SizedBox(height: Space.md),
                          itemBuilder: (context, i) => _EventCard(
                            item: items[i],
                            isEditable: isBackOffice,
                            onEdit: () {
                              Navigator.push(
                                context,
                                MaterialPageRoute(
                                  builder: (_) =>
                                      AddEventScreen(eventToEdit: items[i]),
                                ),
                              );
                            },
                            onDelete: () => _deleteEvent(items[i]),
                          ),
                        ),
                      ),
              ),
            ],
          );
        },
      ),
    );
  }
}

class _EventCard extends StatelessWidget {
  const _EventCard({
    required this.item,
    required this.isEditable,
    required this.onEdit,
    required this.onDelete,
  });

  final SchoolEvent item;
  final bool isEditable;
  final VoidCallback onEdit;
  final VoidCallback onDelete;

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
                style:
                    Theme.of(context).textTheme.titleLarge?.copyWith(height: 1),
              ),
              Text(
                date != null
                    ? DateFormat('MMM').format(date).toUpperCase()
                    : '',
                style: Theme.of(context)
                    .textTheme
                    .labelSmall
                    ?.copyWith(color: scheme.onSurfaceVariant),
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
                Text(item.titulo,
                    style: const TextStyle(fontWeight: FontWeight.w600)),
                if (item.descripcion?.isNotEmpty == true) ...[
                  const SizedBox(height: 4),
                  Text(item.descripcion!,
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                      style: Theme.of(context).textTheme.bodySmall),
                ],
                if (item.ubicacion?.isNotEmpty == true) ...[
                  const SizedBox(height: Space.sm),
                  Row(
                    children: [
                      Icon(Icons.place_outlined,
                          size: 14, color: scheme.onSurfaceVariant),
                      const SizedBox(width: 4),
                      Text(item.ubicacion!,
                          style: Theme.of(context).textTheme.bodySmall),
                    ],
                  ),
                ],
              ],
            ),
          ),
          if (isEditable)
            PopupMenuButton<String>(
              icon: const Icon(Icons.more_vert),
              onSelected: (val) {
                if (val == 'edit') onEdit();
                if (val == 'delete') onDelete();
              },
              itemBuilder: (context) => const [
                PopupMenuItem(value: 'edit', child: Text('Editar')),
                PopupMenuItem(value: 'delete', child: Text('Eliminar')),
              ],
            ),
        ],
      ),
    );
  }
}
