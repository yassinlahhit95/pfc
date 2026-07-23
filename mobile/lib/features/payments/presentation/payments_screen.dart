import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';

import '../../../core/widgets/async_view.dart';
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

const _estadoColors = {
  'aprobado': Color(0xFF10B981),
  'verificando': Color(0xFFF59E0B),
  'rechazado': Color(0xFFEF4444),
  'ninguno': Color(0xFF9AA6BC),
};

class _AllPaymentsTab extends ConsumerWidget {
  const _AllPaymentsTab();

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final paymentsAsync = ref.watch(paymentsProvider);
    final currency = NumberFormat.currency(locale: 'es_ES', symbol: '€');

    return AsyncView<List<Payment>>(
      value: paymentsAsync,
      onRetry: () => ref.invalidate(paymentsProvider),
      data: (context, items) {
        if (items.isEmpty) {
          return const EmptyState(icon: Icons.payments_outlined, title: 'Sin pagos registrados');
        }
        return RefreshIndicator(
          onRefresh: () async => ref.invalidate(paymentsProvider),
          child: ListView.builder(
            padding: const EdgeInsets.all(16),
            itemCount: items.length,
            itemBuilder: (context, i) {
              final p = items[i];
              final color = _estadoColors[p.estadoComprobante] ?? Colors.grey;
              final monto = double.tryParse(p.monto) ?? 0;
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
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(p.nombreEstudiante, style: const TextStyle(fontWeight: FontWeight.bold)),
                          const SizedBox(height: 2),
                          Text('${p.nombreCiclo} · ${p.tipoPago} · ${p.fechaPago}',
                              style: Theme.of(context).textTheme.bodySmall),
                        ],
                      ),
                    ),
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.end,
                      children: [
                        Text(currency.format(monto), style: const TextStyle(fontWeight: FontWeight.bold)),
                        const SizedBox(height: 4),
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
                          decoration: BoxDecoration(color: color.withValues(alpha: 0.15), borderRadius: BorderRadius.circular(10)),
                          child: Text(p.estadoComprobante,
                              style: TextStyle(color: color, fontSize: 11, fontWeight: FontWeight.bold)),
                        ),
                      ],
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
        return RefreshIndicator(
          onRefresh: () async => ref.invalidate(pendingPaymentsProvider),
          child: ListView.builder(
            padding: const EdgeInsets.all(16),
            itemCount: items.length,
            itemBuilder: (context, i) {
              final p = items[i];
              final deuda = double.tryParse(p.deuda) ?? 0;
              return ListTile(
                leading: const Icon(Icons.warning_amber_rounded, color: Color(0xFFEF4444)),
                title: Text(p.nombreEstudiante),
                subtitle: Text(p.nombreCiclo),
                trailing: Text(
                  currency.format(deuda),
                  style: const TextStyle(fontWeight: FontWeight.bold, color: Color(0xFFEF4444)),
                ),
              );
            },
          ),
        );
      },
    );
  }
}
