import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';

import '../../../core/auth/auth_state.dart';
import '../../../core/auth/session.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/filter_bar.dart';
import '../../../core/widgets/premium.dart';
import '../data/announcements_repository.dart';

class AnnouncementsScreen extends ConsumerStatefulWidget {
  const AnnouncementsScreen({super.key});

  @override
  ConsumerState<AnnouncementsScreen> createState() => _AnnouncementsScreenState();
}

class _AnnouncementsScreenState extends ConsumerState<AnnouncementsScreen> {
  String _searchQuery = '';
  String? _selectedDestinatario;

  @override
  Widget build(BuildContext context) {
    final announcementsAsync = ref.watch(announcementsProvider);
    final role = ref.watch(sessionControllerProvider).valueOrNull?.role;
    final canCreate = role == UserRole.director || role == UserRole.secretaria;

    return Scaffold(
      appBar: AppBar(title: const Text('Anuncios')),
      floatingActionButton: canCreate
          ? FloatingActionButton.extended(
              onPressed: () async {
                final created = await showModalBottomSheet<bool>(
                  context: context,
                  isScrollControlled: true,
                  backgroundColor: Colors.transparent,
                  builder: (_) => const _CreateAnnouncementSheet(),
                );
                if (created == true) ref.invalidate(announcementsProvider);
              },
              icon: const Icon(Icons.add_rounded),
              label: const Text('Nuevo anuncio'),
            )
          : null,
      body: AsyncView<List<Announcement>>(
        value: announcementsAsync,
        onRetry: () => ref.invalidate(announcementsProvider),
        data: (context, allItems) {
          if (allItems.isEmpty) {
            return const EmptyState(
              icon: Icons.campaign_outlined,
              title: 'No hay anuncios',
            );
          }

          final filteredItems = allItems.where((item) {
            final matchesSearch = _searchQuery.isEmpty ||
                item.titulo.toLowerCase().contains(_searchQuery) ||
                item.mensaje.toLowerCase().contains(_searchQuery);
            final matchesDest = _selectedDestinatario == null || item.dirigidoA == _selectedDestinatario;
            return matchesSearch && matchesDest;
          }).toList();

          return Column(
            children: [
              Padding(
                padding: const EdgeInsets.fromLTRB(Space.xl, Space.md, Space.xl, 0),
                child: TextField(
                  decoration: const InputDecoration(
                    labelText: 'Buscar anuncios...',
                    prefixIcon: Icon(Icons.search_rounded),
                    contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                  ),
                  onChanged: (val) {
                    setState(() {
                      _searchQuery = val.trim().toLowerCase();
                    });
                  },
                ),
              ),
              const SizedBox(height: Space.md),
              FilterBar(children: [
                FilterPill<String>(
                  label: 'Destinatarios',
                  value: _selectedDestinatario,
                  options: const [
                    ('todos', 'Todos'),
                    ('estudiantes', 'Estudiantes'),
                    ('profesores', 'Profesores'),
                    ('tutores', 'Tutores'),
                  ],
                  onChanged: (v) => setState(() => _selectedDestinatario = v),
                ),
              ]),
              const SizedBox(height: Space.sm),
              Expanded(
                child: filteredItems.isEmpty
                    ? const EmptyState(icon: Icons.filter_alt_off_outlined, title: 'Sin resultados para estos filtros')
                    : RefreshIndicator(
                        onRefresh: () async => ref.invalidate(announcementsProvider),
                        child: ListView.separated(
                          padding: const EdgeInsets.fromLTRB(Space.xl, Space.sm, Space.xl, Space.xxxl),
                          itemCount: filteredItems.length,
                          separatorBuilder: (_, __) => const SizedBox(height: Space.md),
                          itemBuilder: (context, i) => _AnnouncementCard(item: filteredItems[i]),
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

class _AnnouncementCard extends StatelessWidget {
  const _AnnouncementCard({required this.item});
  final Announcement item;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final date = DateTime.tryParse(item.fecha.replaceFirst(' ', 'T'));

    return AppCard(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Expanded(
                child: Text(item.titulo, style: const TextStyle(fontWeight: FontWeight.w600)),
              ),
              if (date != null)
                Text(
                  DateFormat('d MMM').format(date),
                  style: Theme.of(context).textTheme.bodySmall?.copyWith(color: scheme.onSurfaceVariant),
                ),
            ],
          ),
          const SizedBox(height: Space.sm),
          Text(item.mensaje, style: Theme.of(context).textTheme.bodyMedium),
        ],
      ),
    );
  }
}

const _dirigidoAOptions = [
  ('todos', 'Todos'),
  ('estudiantes', 'Estudiantes'),
  ('profesores', 'Profesores'),
  ('tutores', 'Tutores'),
];

class _CreateAnnouncementSheet extends ConsumerStatefulWidget {
  const _CreateAnnouncementSheet();

  @override
  ConsumerState<_CreateAnnouncementSheet> createState() => _CreateAnnouncementSheetState();
}

class _CreateAnnouncementSheetState extends ConsumerState<_CreateAnnouncementSheet> {
  final _tituloController = TextEditingController();
  final _mensajeController = TextEditingController();
  String _dirigidoA = 'todos';
  bool _sending = false;
  String? _error;

  @override
  void dispose() {
    _tituloController.dispose();
    _mensajeController.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    final titulo = _tituloController.text.trim();
    final mensaje = _mensajeController.text.trim();
    if (titulo.isEmpty || mensaje.isEmpty) {
      setState(() => _error = 'El título y el contenido son obligatorios.');
      return;
    }
    setState(() {
      _sending = true;
      _error = null;
    });
    try {
      await ref.read(announcementsRepositoryProvider).create(
            titulo: titulo,
            mensaje: mensaje,
            dirigidoA: _dirigidoA,
          );
      if (mounted) Navigator.of(context).pop(true);
    } catch (_) {
      setState(() {
        _sending = false;
        _error = 'No se pudo publicar el anuncio.';
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Container(
      padding: EdgeInsets.fromLTRB(Space.xl, Space.md, Space.xl, Space.xl + MediaQuery.of(context).viewInsets.bottom),
      decoration: BoxDecoration(
        color: scheme.surface,
        borderRadius: const BorderRadius.vertical(top: Radius.circular(Radii.xl)),
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Container(
            width: 36,
            height: 4,
            margin: const EdgeInsets.only(bottom: Space.lg),
            alignment: Alignment.center,
            decoration: BoxDecoration(color: scheme.outlineVariant, borderRadius: BorderRadius.circular(Radii.pill)),
          ),
          Text('Nuevo anuncio', style: Theme.of(context).textTheme.titleMedium),
          const SizedBox(height: Space.xl),
          TextField(
            controller: _tituloController,
            decoration: const InputDecoration(labelText: 'Título'),
          ),
          const SizedBox(height: Space.md),
          TextField(
            controller: _mensajeController,
            minLines: 3,
            maxLines: 6,
            decoration: const InputDecoration(labelText: 'Contenido'),
          ),
          const SizedBox(height: Space.md),
          Text('Dirigido a', style: Theme.of(context).textTheme.bodySmall),
          const SizedBox(height: Space.xs),
          Wrap(
            spacing: Space.sm,
            children: [
              for (final (value, label) in _dirigidoAOptions)
                ChoiceChip(
                  label: Text(label),
                  selected: _dirigidoA == value,
                  onSelected: (_) => setState(() => _dirigidoA = value),
                ),
            ],
          ),
          if (_error != null) ...[
            const SizedBox(height: Space.md),
            Text(_error!, style: TextStyle(color: scheme.error)),
          ],
          const SizedBox(height: Space.xl),
          FilledButton(
            onPressed: _sending ? null : _submit,
            child: _sending
                ? const SizedBox(height: 18, width: 18, child: CircularProgressIndicator(strokeWidth: 2))
                : const Text('Publicar'),
          ),
        ],
      ),
    );
  }
}
