import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/filter_bar.dart';
import '../../../core/widgets/premium.dart';
import '../data/payments_repository.dart';

class PaymentsScreen extends StatelessWidget {
  const PaymentsScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return DefaultTabController(
      length: 2,
      child: Scaffold(
        appBar: AppBar(
          title: const Text('Pagos'),
          bottom: const TabBar(tabs: [
            Tab(text: 'Todos'),
            Tab(text: 'Pendientes'),
          ]),
        ),
        body: const TabBarView(children: [_AllPaymentsTab(), _PendingPaymentsTab()]),
      ),
    );
  }
}

Color _estadoColor(BuildContext context, String estado) {
  final dark = Theme.of(context).brightness == Brightness.dark;
  return switch (estado) {
    'aprobado' => dark ? AppColors.verdeDark : AppColors.verdeLight,
    'verificando' => dark ? AppColors.naranjaDark : AppColors.naranjaLight,
    'rechazado' => dark ? AppColors.rojoDark : AppColors.rojoLight,
    _ => Theme.of(context).colorScheme.onSurfaceVariant,
  };
}

class _AllPaymentsTab extends ConsumerStatefulWidget {
  const _AllPaymentsTab();

  @override
  ConsumerState<_AllPaymentsTab> createState() => _AllPaymentsTabState();
}

class _AllPaymentsTabState extends ConsumerState<_AllPaymentsTab> {
  String? _ciclo;
  String? _nivel;
  String? _estado;

  @override
  Widget build(BuildContext context) {
    final paymentsAsync = ref.watch(paymentsProvider);
    final currency = NumberFormat.currency(locale: 'es_ES', symbol: '€');

    return AsyncView<List<Payment>>(
      value: paymentsAsync,
      onRetry: () => ref.invalidate(paymentsProvider),
      data: (context, allItems) {
        if (allItems.isEmpty) {
          return const EmptyState(icon: Icons.receipt_long_outlined, title: 'Sin pagos registrados');
        }

        final ciclos = allItems.map((p) => p.nombreCiclo).where((c) => c.isNotEmpty).toSet().toList()..sort();
        final niveles = allItems.map((p) => p.nivel).where((n) => n.isNotEmpty).toSet().toList()..sort();
        final estados = allItems.map((p) => p.estadoComprobante).where((e) => e.isNotEmpty).toSet().toList()..sort();

        final items = allItems.where((p) {
          if (_ciclo != null && p.nombreCiclo != _ciclo) return false;
          if (_nivel != null && p.nivel != _nivel) return false;
          if (_estado != null && p.estadoComprobante != _estado) return false;
          return true;
        }).toList();

        return Column(
          children: [
            const SizedBox(height: Space.md),
            FilterBar(children: [
              FilterPill<String>(
                label: 'Ciclo',
                value: _ciclo,
                options: [for (final c in ciclos) (c, c)],
                onChanged: (v) => setState(() => _ciclo = v),
              ),
              FilterPill<String>(
                label: 'Nivel',
                value: _nivel,
                options: [for (final n in niveles) (n, n)],
                onChanged: (v) => setState(() => _nivel = v),
              ),
              FilterPill<String>(
                label: 'Estado',
                value: _estado,
                options: [for (final e in estados) (e, e)],
                onChanged: (v) => setState(() => _estado = v),
              ),
            ]),
            const SizedBox(height: Space.sm),
            Expanded(
              child: items.isEmpty
                  ? const EmptyState(icon: Icons.filter_alt_off_outlined, title: 'Sin resultados para estos filtros')
                  : RefreshIndicator(
                      onRefresh: () async => ref.invalidate(paymentsProvider),
                      child: ListView.builder(
                        padding: const EdgeInsets.fromLTRB(Space.xl, Space.sm, Space.xl, Space.xxxl),
                        itemCount: items.length,
                        itemBuilder: (context, i) {
                          final p = items[i];
                          final color = _estadoColor(context, p.estadoComprobante);
                          final monto = double.tryParse(p.monto) ?? 0;
                          return AppCard(
                            margin: const EdgeInsets.only(bottom: Space.md),
                            child: Row(
                              children: [
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(p.nombreEstudiante, style: const TextStyle(fontWeight: FontWeight.w600)),
                                      const SizedBox(height: 2),
                                      Text('${p.nombreCiclo} · ${p.tipoPago} · ${p.fechaPago}',
                                          style: Theme.of(context).textTheme.bodySmall),
                                    ],
                                  ),
                                ),
                                Column(
                                  crossAxisAlignment: CrossAxisAlignment.end,
                                  children: [
                                    Text(currency.format(monto), style: const TextStyle(fontWeight: FontWeight.w700)),
                                    const SizedBox(height: 6),
                                    StatusPill(label: p.estadoComprobante, color: color),
                                  ],
                                ),
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

class _PendingPaymentsTab extends ConsumerWidget {
  const _PendingPaymentsTab();

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final pendingAsync = ref.watch(pendingPaymentsProvider);
    final currency = NumberFormat.currency(locale: 'es_ES', symbol: '€');

    return AsyncView<List<PendingPayment>>(
      value: pendingAsync,
      onRetry: () => ref.invalidate(pendingPaymentsProvider),
      data: (context, items) {
        if (items.isEmpty) {
          return const EmptyState(icon: Icons.check_circle_outline, title: 'Sin pagos pendientes');
        }
        final color = Theme.of(context).brightness == Brightness.dark ? AppColors.rojoDark : AppColors.rojoLight;
        return RefreshIndicator(
          onRefresh: () async => ref.invalidate(pendingPaymentsProvider),
          child: ListView.builder(
            padding: const EdgeInsets.fromLTRB(Space.xl, Space.lg, Space.xl, Space.xxxl),
            itemCount: items.length,
            itemBuilder: (context, i) {
              final p = items[i];
              final deuda = double.tryParse(p.deuda) ?? 0;
              return AppCard(
                margin: const EdgeInsets.only(bottom: Space.md),
                child: Row(
                  children: [
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(p.nombreEstudiante, style: const TextStyle(fontWeight: FontWeight.w600)),
                          Text(p.nombreCiclo, style: Theme.of(context).textTheme.bodySmall),
                        ],
                      ),
                    ),
                    Text(currency.format(deuda), style: TextStyle(fontWeight: FontWeight.w700, color: color)),
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
