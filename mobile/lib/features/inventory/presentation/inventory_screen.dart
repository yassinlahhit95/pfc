import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/filter_bar.dart';
import '../../../core/widgets/premium.dart';
import '../../chat/data/chat_repository.dart';
import '../data/inventory_repository.dart';

class InventoryScreen extends StatelessWidget {
  const InventoryScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
      length: 2,
      child: Scaffold(
        appBar: AppBar(
          title: const Text('Inventario'),
          bottom: const TabBar(tabs: [
            Tab(text: 'Dispositivos'),
            Tab(text: 'Préstamos'),
          ]),
        ),
        body: const TabBarView(children: [_DevicesTab(), _LoansTab()]),
      ),
    );
  }
}

Color _deviceColor(BuildContext context, String estado) {
  final dark = Theme.of(context).brightness == Brightness.dark;
  return switch (estado) {
    'disponible' => dark ? AppColors.verdeDark : AppColors.verdeLight,
    'prestado' => dark ? AppColors.naranjaDark : AppColors.naranjaLight,
    'baja' => dark ? AppColors.rojoDark : AppColors.rojoLight,
    _ => Theme.of(context).colorScheme.onSurfaceVariant,
  };
}

class _DevicesTab extends ConsumerStatefulWidget {
  const _DevicesTab();

  @override
  ConsumerState<_DevicesTab> createState() => _DevicesTabState();
}

class _DevicesTabState extends ConsumerState<_DevicesTab> {
  String _searchQuery = '';
  String? _status;

  Future<void> _openPrestarDialog(BuildContext context, Device device) async {
    final result = await showModalBottomSheet<bool>(
      context: context,
      isScrollControlled: true,
      builder: (ctx) => _StudentPickerSheet(device: device),
    );
    if (result == true) {
      ref.invalidate(devicesProvider);
      ref.invalidate(loansProvider);
    }
  }

  @override
  Widget build(BuildContext context) {
    final devicesAsync = ref.watch(devicesProvider);

    return AsyncView<List<Device>>(
      value: devicesAsync,
      onRetry: () => ref.invalidate(devicesProvider),
      data: (context, allItems) {
        if (allItems.isEmpty) {
          return const EmptyState(icon: Icons.devices_other_outlined, title: 'Sin dispositivos');
        }

        final items = allItems.where((d) {
          final matchesSearch = _searchQuery.isEmpty ||
              d.nombre.toLowerCase().contains(_searchQuery) ||
              d.numeroSerie.toLowerCase().contains(_searchQuery);
          final matchesStatus = _status == null || d.estado == _status;
          return matchesSearch && matchesStatus;
        }).toList();

        return Column(
          children: [
            Padding(
              padding: const EdgeInsets.fromLTRB(Space.xl, Space.md, Space.xl, 0),
              child: TextField(
                decoration: const InputDecoration(
                  labelText: 'Buscar dispositivo...',
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
                label: 'Estado',
                value: _status,
                options: const [
                  ('disponible', 'Disponible'),
                  ('prestado', 'Prestado'),
                  ('baja', 'De baja'),
                ],
                onChanged: (v) => setState(() => _status = v),
              ),
            ]),
            const SizedBox(height: Space.sm),
            Expanded(
              child: items.isEmpty
                  ? const EmptyState(icon: Icons.filter_alt_off_outlined, title: 'Sin resultados para estos filtros')
                  : RefreshIndicator(
                      onRefresh: () async => ref.invalidate(devicesProvider),
                      child: ListView.builder(
                        padding: const EdgeInsets.fromLTRB(Space.xl, Space.sm, Space.xl, Space.xxxl),
                        itemCount: items.length,
                        itemBuilder: (context, i) {
                          final d = items[i];
                          final color = _deviceColor(context, d.estado);
                          final available = d.estado == 'disponible';
                          return AppCard(
                            margin: const EdgeInsets.only(bottom: Space.md),
                            child: Row(
                              children: [
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(d.nombre, style: const TextStyle(fontWeight: FontWeight.w600)),
                                      Text(d.numeroSerie, style: Theme.of(context).textTheme.bodySmall),
                                    ],
                                  ),
                                ),
                                if (available)
                                  OutlinedButton(
                                    onPressed: () => _openPrestarDialog(context, d),
                                    child: const Text('Prestar'),
                                  )
                                else
                                  StatusPill(label: d.estado, color: color),
                              ],
                            ),
                          );
                        },
                      ),
                    ),
            ),
          ],
        );
      },
    );
  }
}

class _StudentPickerSheet extends ConsumerStatefulWidget {
  const _StudentPickerSheet({required this.device});
  final Device device;

  @override
  ConsumerState<_StudentPickerSheet> createState() => _StudentPickerSheetState();
}

class _StudentPickerSheetState extends ConsumerState<_StudentPickerSheet> {
  List<ChatContact> _results = [];
  bool _loading = true;
  Timer? _debounce;

  @override
  void initState() {
    super.initState();
    _search('');
  }

  @override
  void dispose() {
    _debounce?.cancel();
    super.dispose();
  }

  Future<void> _search(String q) async {
    setState(() => _loading = true);
    try {
      final all = await ref.read(chatRepositoryProvider).fetchContacts(query: q);
      if (!mounted) return;
      setState(() {
        _results = all.where((c) => c.rol == 'estudiante').toList();
        _loading = false;
      });
    } catch (_) {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _prestar(ChatContact student) async {
    try {
      await ref.read(inventoryRepositoryProvider).prestar(idArticulo: widget.device.id, idEstudiante: student.uid);
      if (mounted) Navigator.of(context).pop(true);
    } catch (_) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('No se pudo registrar el préstamo.')));
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return DraggableScrollableSheet(
      initialChildSize: 0.7,
      expand: false,
      builder: (context, scrollController) => Container(
        decoration: BoxDecoration(
          color: scheme.surface,
          borderRadius: const BorderRadius.vertical(top: Radius.circular(Radii.xl)),
        ),
        padding: const EdgeInsets.fromLTRB(Space.xl, Space.md, Space.xl, Space.xl),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Container(
              width: 36,
              height: 4,
              margin: const EdgeInsets.only(bottom: Space.lg),
              alignment: Alignment.center,
              decoration: BoxDecoration(color: scheme.outlineVariant, borderRadius: BorderRadius.circular(Radii.pill)),
            ),
            Text('Prestar ${widget.device.nombre}', style: Theme.of(context).textTheme.titleMedium),
            const SizedBox(height: Space.md),
            TextField(
              autofocus: true,
              onChanged: (v) {
                _debounce?.cancel();
                _debounce = Timer(const Duration(milliseconds: 300), () => _search(v));
              },
              decoration: const InputDecoration(hintText: 'Buscar estudiante', prefixIcon: Icon(Icons.search_rounded)),
            ),
            const SizedBox(height: Space.sm),
            Expanded(
              child: _loading
                  ? const Center(child: CircularProgressIndicator(strokeWidth: 2.4))
                  : ListView.builder(
                      controller: scrollController,
                      itemCount: _results.length,
                      itemBuilder: (context, i) {
                        final s = _results[i];
                        return Padding(
                          padding: const EdgeInsets.symmetric(vertical: Space.xs),
                          child: Material(
                            color: Colors.transparent,
                            child: InkWell(
                              borderRadius: BorderRadius.circular(Radii.md),
                              onTap: () => _prestar(s),
                              child: Padding(
                                padding: const EdgeInsets.symmetric(vertical: Space.sm),
                                child: Row(
                                  children: [
                                    InitialsAvatar(name: s.nombre, radius: 18),
                                    const SizedBox(width: Space.md),
                                    Text(s.nombre, style: const TextStyle(fontWeight: FontWeight.w500)),
                                  ],
                                ),
                              ),
                            ),
                          ),
                        );
                      },
                    ),
            ),
          ],
        ),
      ),
    );
  }
}

class _LoansTab extends ConsumerStatefulWidget {
  const _LoansTab();

  @override
  ConsumerState<_LoansTab> createState() => _LoansTabState();
}

class _LoansTabState extends ConsumerState<_LoansTab> {
  String _searchQuery = '';
  String? _status;

  Future<void> _devolver(BuildContext context, Loan loan) async {
    try {
      await ref.read(inventoryRepositoryProvider).devolver(loan.id);
      ref.invalidate(loansProvider);
      ref.invalidate(devicesProvider);
    } catch (_) {
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('No se pudo registrar la devolución.')));
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final loansAsync = ref.watch(loansProvider);

    return AsyncView<List<Loan>>(
      value: loansAsync,
      onRetry: () => ref.invalidate(loansProvider),
      data: (context, allItems) {
        if (allItems.isEmpty) {
          return const EmptyState(icon: Icons.assignment_return_outlined, title: 'Sin préstamos');
        }

        final items = allItems.where((l) {
          final matchesSearch = _searchQuery.isEmpty ||
              l.nombreEstudiante.toLowerCase().contains(_searchQuery) ||
              l.nombreArticulo.toLowerCase().contains(_searchQuery);
          final matchesStatus = _status == null || l.estadoPrestamo == _status;
          return matchesSearch && matchesStatus;
        }).toList();

        return Column(
          children: [
            Padding(
              padding: const EdgeInsets.fromLTRB(Space.xl, Space.md, Space.xl, 0),
              child: TextField(
                decoration: const InputDecoration(
                  labelText: 'Buscar préstamo...',
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
                label: 'Estado',
                value: _status,
                options: const [
                  ('en curso', 'En curso'),
                  ('devuelto', 'Devuelto'),
                ],
                onChanged: (v) => setState(() => _status = v),
              ),
            ]),
            const SizedBox(height: Space.sm),
            Expanded(
              child: items.isEmpty
                  ? const EmptyState(icon: Icons.filter_alt_off_outlined, title: 'Sin resultados para estos filtros')
                  : RefreshIndicator(
                      onRefresh: () async => ref.invalidate(loansProvider),
                      child: ListView.builder(
                        padding: const EdgeInsets.fromLTRB(Space.xl, Space.sm, Space.xl, Space.xxxl),
                        itemCount: items.length,
                        itemBuilder: (context, i) {
                          final l = items[i];
                          final enCurso = l.estadoPrestamo == 'en curso';
                          final date = DateTime.tryParse(l.fechaPrestamo);
                          return AppCard(
                            margin: const EdgeInsets.only(bottom: Space.md),
                            child: Row(
                              children: [
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text('${l.nombreEstudiante} · ${l.nombreArticulo}',
                                          style: const TextStyle(fontWeight: FontWeight.w600)),
                                      Text(
                                        date != null ? DateFormat('d MMM yyyy').format(date) : l.fechaPrestamo,
                                        style: Theme.of(context).textTheme.bodySmall,
                                      ),
                                    ],
                                  ),
                                ),
                                if (enCurso)
                                  OutlinedButton(onPressed: () => _devolver(context, l), child: const Text('Devolver'))
                                else
                                  Text('Devuelto', style: Theme.of(context).textTheme.bodySmall),
                              ],
                            ),
                          );
                        },
                      ),
                    ),
            ),
          ],
        );
      },
    );
  }
}
