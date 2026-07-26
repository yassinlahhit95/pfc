import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';

import '../../../core/auth/auth_state.dart';
import '../../../core/auth/session.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/filter_bar.dart';
import '../../../core/widgets/premium.dart';
import '../data/payments_repository.dart';

Color _estadoColor(BuildContext context, String estado) {
  final dark = Theme.of(context).brightness == Brightness.dark;
  return switch (estado) {
    'aprobado' => dark ? AppColors.verdeDark : AppColors.verdeLight,
    'verificando' => dark ? AppColors.naranjaDark : AppColors.naranjaLight,
    'rechazado' => dark ? AppColors.rojoDark : AppColors.rojoLight,
    _ => Theme.of(context).colorScheme.onSurfaceVariant,
  };
}

class MyPaymentsScreen extends ConsumerWidget {
  const MyPaymentsScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final role = ref.watch(sessionControllerProvider).valueOrNull?.role;

    return Scaffold(
      appBar: AppBar(title: const Text('Mis pagos')),
      body: role == UserRole.tutor ? const _TutorView() : const _StudentView(),
    );
  }
}

class _StudentView extends ConsumerWidget {
  const _StudentView();

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final asyncData = ref.watch(myPaymentsProvider);
    return AsyncView<({List<Payment> payments, FinancialStatus estado})>(
      value: asyncData,
      onRetry: () => ref.invalidate(myPaymentsProvider),
      data: (context, data) => RefreshIndicator(
        onRefresh: () async => ref.invalidate(myPaymentsProvider),
        child: ListView(
          padding: const EdgeInsets.fromLTRB(Space.xl, Space.lg, Space.xl, Space.xxxl),
          children: [
            _BalanceCard(estado: data.estado),
            const SizedBox(height: Space.xxl),
            if (data.payments.isEmpty)
              const EmptyState(icon: Icons.receipt_long_outlined, title: 'Sin pagos registrados')
            else ...[
              const SectionLabel('Historial'),
              for (final p in data.payments) _PaymentTile(payment: p),
            ],
          ],
        ),
      ),
    );
  }
}

class _TutorView extends ConsumerStatefulWidget {
  const _TutorView();

  @override
  ConsumerState<_TutorView> createState() => _TutorViewState();
}

class _TutorViewState extends ConsumerState<_TutorView> {
  int? _hijo;

  @override
  Widget build(BuildContext context) {
    final asyncData = ref.watch(tutorPaymentsProvider);
    return AsyncView<List<StudentPaymentsGroup>>(
      value: asyncData,
      onRetry: () => ref.invalidate(tutorPaymentsProvider),
      data: (context, allGroups) {
        if (allGroups.isEmpty) {
          return const EmptyState(icon: Icons.receipt_long_outlined, title: 'Sin estudiantes vinculados');
        }
        final groups = _hijo == null ? allGroups : allGroups.where((g) => g.idEstudiante == _hijo).toList();

        return Column(
          children: [
            if (allGroups.length > 1) ...[
              const SizedBox(height: Space.md),
              FilterBar(children: [
                FilterPill<int>(
                  label: 'Hijo',
                  value: _hijo,
                  allLabel: 'Todos los hijos',
                  options: [for (final g in allGroups) (g.idEstudiante, g.nombreEstudiante)],
                  onChanged: (v) => setState(() => _hijo = v),
                ),
              ]),
            ],
            const SizedBox(height: Space.sm),
            Expanded(
              child: RefreshIndicator(
                onRefresh: () async => ref.invalidate(tutorPaymentsProvider),
                child: ListView(
                  padding: const EdgeInsets.fromLTRB(Space.xl, Space.sm, Space.xl, Space.xxxl),
                  children: [
                    for (final g in groups) ...[
                      if (allGroups.length > 1) SectionLabel(g.nombreEstudiante),
                      _BalanceCard(estado: g.estado),
                      const SizedBox(height: Space.md),
                      for (final p in g.payments) _PaymentTile(payment: p),
                      const SizedBox(height: Space.xxl),
                    ],
                  ],
                ),
              ),
            ),
          ],
        );
      },
    );
  }
}

class _BalanceCard extends StatelessWidget {
  const _BalanceCard({required this.estado});
  final FinancialStatus estado;

  @override
  Widget build(BuildContext context) {
    final currency = NumberFormat.currency(locale: 'es_ES', symbol: '€');
    final scheme = Theme.of(context).colorScheme;
    final ok = estado.restante <= 0;
    final restanteColor = ok
        ? (scheme.brightness == Brightness.dark ? AppColors.verdeDark : AppColors.verdeLight)
        : (scheme.brightness == Brightness.dark ? AppColors.rojoDark : AppColors.rojoLight);

    return AppCard(
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Pagado', style: Theme.of(context).textTheme.bodySmall),
                const SizedBox(height: 2),
                Text(currency.format(estado.totalPagado), style: Theme.of(context).textTheme.titleMedium),
              ],
            ),
          ),
          Container(width: 1, height: 32, color: scheme.outlineVariant),
          const SizedBox(width: Space.lg),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Restante', style: Theme.of(context).textTheme.bodySmall),
                const SizedBox(height: 2),
                Text(
                  currency.format(estado.restante),
                  style: Theme.of(context).textTheme.titleMedium?.copyWith(color: restanteColor),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _PaymentTile extends StatelessWidget {
  const _PaymentTile({required this.payment});
  final Payment payment;

  @override
  Widget build(BuildContext context) {
    final color = _estadoColor(context, payment.estadoComprobante);
    final currency = NumberFormat.currency(locale: 'es_ES', symbol: '€');
    final monto = double.tryParse(payment.monto) ?? 0;

    return AppCard(
      margin: const EdgeInsets.only(bottom: Space.sm),
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(payment.tipoPago, style: const TextStyle(fontWeight: FontWeight.w600)),
                Text(payment.fechaPago, style: Theme.of(context).textTheme.bodySmall),
              ],
            ),
          ),
          Text(currency.format(monto), style: const TextStyle(fontWeight: FontWeight.w700)),
          const SizedBox(width: Space.sm),
          Container(width: 6, height: 6, decoration: BoxDecoration(color: color, shape: BoxShape.circle)),
        ],
      ),
    );
  }
}
