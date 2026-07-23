import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';

import '../../../core/widgets/async_view.dart';
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

const _deviceStatusColors = {
  'disponible': Color(0xFF10B981),
  'prestado': Color(0xFFF59E0B),
  'baja': Color(0xFFEF4444),
};

class _DevicesTab extends ConsumerWidget {
  const _DevicesTab();

  Future<void> _openPrestarDialog(BuildContext context, WidgetRef ref, Device device) async {
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
  Widget build(BuildContext context, WidgetRef ref) {
    final devicesAsync = ref.watch(devicesProvider);

    return AsyncView<List<Device>>(
      value: devicesAsync,
      onRetry: () => ref.invalidate(devicesProvider),
      data: (context, items) {
        if (items.isEmpty) {
          return const EmptyState(icon: Icons.devices_other_outlined, title: 'Sin dispositivos');
        }
        return RefreshIndicator(
          onRefresh: () async => ref.invalidate(devicesProvider),
          child: ListView.builder(
            padding: const EdgeInsets.all(16),
            itemCount: items.length,
            itemBuilder: (context, i) {
              final d = items[i];
              final color = _deviceStatusColors[d.estado] ?? Colors.grey;
              final available = d.estado == 'disponible';
              return Container(
                margin: const EdgeInsets.only(bottom: 10),
                padding: const EdgeInsets.all(14),
                decoration: BoxDecoration(
                  color: Theme.of(context).colorScheme.surface,
                  borderRadius: BorderRadius.circular(14),
                  border: Border.all(color: Theme.of(context).colorScheme.outlineVariant),
                ),
                child: Row(
                  children: [
                    Icon(Icons.laptop_mac_outlined, color: color),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(d.nombre, style: const TextStyle(fontWeight: FontWeight.bold)),
                          Text(d.numeroSerie, style: Theme.of(context).textTheme.bodySmall),
                        ],
                      ),
                    ),
                    if (available)
                      OutlinedButton(
                        onPressed: () => _openPrestarDialog(context, ref, d),
                        child: const Text('Prestar'),
                      )
                    else
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
                        decoration: BoxDecoration(color: color.withValues(alpha: 0.15), borderRadius: BorderRadius.circular(20)),
                        child: Text(d.estado, style: TextStyle(color: color, fontWeight: FontWeight.bold, fontSize: 12)),
                      ),
                  ],
                ),
              );
            },
          ),
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
    return DraggableScrollableSheet(
      initialChildSize: 0.7,
      expand: false,
      builder: (context, scrollController) => Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Text('Prestar ${widget.device.nombre}', style: Theme.of(context).textTheme.titleMedium),
            const SizedBox(height: 12),
            TextField(
              autofocus: true,
              onChanged: (v) {
                _debounce?.cancel();
                _debounce = Timer(const Duration(milliseconds: 300), () => _search(v));
              },
              decoration: const InputDecoration(hintText: 'Buscar estudiante…', prefixIcon: Icon(Icons.search)),
            ),
            const SizedBox(height: 8),
            Expanded(
              child: _loading
                  ? const Center(child: CircularProgressIndicator())
                  : ListView.builder(
                      controller: scrollController,
                      itemCount: _results.length,
                      itemBuilder: (context, i) {
                        final s = _results[i];
                        return ListTile(
                          leading: CircleAvatar(child: Text(s.nombre.isNotEmpty ? s.nombre[0].toUpperCase() : '?')),
                          title: Text(s.nombre),
                          onTap: () => _prestar(s),
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

class _LoansTab extends ConsumerWidget {
  const _LoansTab();

  Future<void> _devolver(BuildContext context, WidgetRef ref, Loan loan) async {
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
  Widget build(BuildContext context, WidgetRef ref) {
    final loansAsync = ref.watch(loansProvider);

    return AsyncView<List<Loan>>(
      value: loansAsync,
      onRetry: () => ref.invalidate(loansProvider),
      data: (context, items) {
        if (items.isEmpty) {
          return const EmptyState(icon: Icons.assignment_return_outlined, title: 'Sin préstamos');
        }
        return RefreshIndicator(
          onRefresh: () async => ref.invalidate(loansProvider),
          child: ListView.builder(
            padding: const EdgeInsets.all(16),
            itemCount: items.length,
            itemBuilder: (context, i) {
              final l = items[i];
              final enCurso = l.estadoPrestamo == 'en curso';
              final date = DateTime.tryParse(l.fechaPrestamo);
              return ListTile(
                leading: Icon(Icons.laptop_mac_outlined, color: enCurso ? const Color(0xFFF59E0B) : Colors.grey),
                title: Text('${l.nombreEstudiante} · ${l.nombreArticulo}'),
                subtitle: Text(date != null ? DateFormat('d MMM yyyy').format(date) : l.fechaPrestamo),
                trailing: enCurso
                    ? OutlinedButton(onPressed: () => _devolver(context, ref, l), child: const Text('Devolver'))
                    : const Text('Devuelto', style: TextStyle(color: Colors.grey)),
              );
            },
          ),
        );
      },
    );
  }
}
