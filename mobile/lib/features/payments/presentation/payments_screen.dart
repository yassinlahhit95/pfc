import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/filter_bar.dart';
import '../../../core/widgets/premium.dart';
import '../data/payments_repository.dart';
import 'cobrar_pago_sheet.dart';

Future<void> _openComprobante(BuildContext context, String url) async {
  final ok = await launchUrl(Uri.parse(url), mode: LaunchMode.externalApplication);
  if (!ok && context.mounted) {
    ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('No se pudo abrir el comprobante.')));
  }
}

class PaymentsScreen extends StatefulWidget {
  const PaymentsScreen({super.key});

  @override
  State<PaymentsScreen> createState() => _PaymentsScreenState();
}

class _PaymentsScreenState extends State<PaymentsScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Pagos'),
        bottom: TabBar(
          controller: _tabController,
          tabs: const [
            Tab(text: 'Todos'),
            Tab(text: 'Pendientes'),
          ],
        ),
      ),
      body: TabBarView(
        controller: _tabController,
        children: const [_AllPaymentsTab(), _PendingPaymentsTab()],
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

    return AsyncView<List<Payment>>(
      value: paymentsAsync,
      onRetry: () => ref.invalidate(paymentsProvider),
      data: (context, allItems) {
        if (allItems.isEmpty) {
          return const EmptyState(icon: Icons.receipt_long_outlined, title: 'Sin pagos registrados');
        }

        // ponytail: build lookup maps to filter dinamically by nivel
        final ciclosByNivel = <String, Set<String>>{};
        final niveles = <String>{};
        final estados = <String>{};

        for (final p in allItems) {
          if (p.nivel.isNotEmpty) niveles.add(p.nivel);
          if (p.estadoComprobante.isNotEmpty) estados.add(p.estadoComprobante);
          if (p.nombreCiclo.isNotEmpty) {
            ciclosByNivel.putIfAbsent(p.nivel, () => {}).add(p.nombreCiclo);
          }
        }

        // if nivel is selected, show only ciclos for that nivel; else show all ciclos
        final cicloOptions = _nivel != null
            ? (ciclosByNivel[_nivel] ?? <String>{}).toList()..sort()
            : ciclosByNivel.values.expand((c) => c).toSet().toList()..sort();

        // reset ciclo if it's no longer valid for the selected nivel
        if (_nivel != null && !cicloOptions.contains(_ciclo)) {
          _ciclo = null;
        }

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
                label: 'Nivel',
                value: _nivel,
                options: [for (final n in niveles) (n, n)],
                onChanged: (v) => setState(() {
                  _nivel = v;
                  _ciclo = null; // reset ciclo when nivel changes
                }),
              ),
              FilterPill<String>(
                label: 'Ciclo',
                value: _ciclo,
                options: [for (final c in cicloOptions) (c, c)],
                onChanged: (v) => setState(() => _ciclo = v),
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
                        itemExtent: 100, // ponytail: optimize list rendering
                        itemBuilder: (context, i) => _PaymentReviewCard(
                          payment: items[i],
                          onResolved: () => ref.invalidate(paymentsProvider),
                        ),
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
            itemExtent: 90, // ponytail: optimize list rendering
            itemBuilder: (context, i) {
              final p = items[i];
              final deuda = double.tryParse(p.deuda) ?? 0;
              return AppCard(
                margin: const EdgeInsets.only(bottom: Space.md),
                child: InkWell(
                  onTap: () async {
                    final updated = await showCobrarPagoSheet(
                      context,
                      ref,
                      idEstudiante: p.idEstudiante,
                      nombreEstudiante: p.nombreEstudiante,
                      deudaActual: deuda,
                    );
                    if (updated) {
                      ref.invalidate(pendingPaymentsProvider);
                      ref.invalidate(paymentsProvider);
                    }
                  },
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
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          Text(currency.format(deuda), style: TextStyle(fontWeight: FontWeight.w700, color: color)),
                          const SizedBox(height: 4),
                          Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Text('Cobrar', style: TextStyle(fontSize: 12, color: Theme.of(context).colorScheme.primary, fontWeight: FontWeight.w600)),
                              Icon(Icons.chevron_right_rounded, size: 14, color: Theme.of(context).colorScheme.primary),
                            ],
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              );
            },
          ),
        );
      },
    );
  }
}

class _PaymentReviewCard extends ConsumerStatefulWidget {
  const _PaymentReviewCard({required this.payment, required this.onResolved});
  final Payment payment;
  final VoidCallback onResolved;

  @override
  ConsumerState<_PaymentReviewCard> createState() => _PaymentReviewCardState();
}

class _PaymentReviewCardState extends ConsumerState<_PaymentReviewCard> {
  bool _resolving = false;

  Future<void> _resolve(bool aprobar) async {
    String? motivoRechazo;
    if (!aprobar) {
      final controller = TextEditingController();
      final confirmed = await showDialog<bool>(
        context: context,
        builder: (ctx) => AlertDialog(
          title: const Text('Rechazar comprobante'),
          content: TextField(
            controller: controller,
            decoration: const InputDecoration(labelText: 'Motivo del rechazo'),
          ),
          actions: [
            TextButton(onPressed: () => Navigator.of(ctx).pop(false), child: const Text('Cancelar')),
            FilledButton(onPressed: () => Navigator.of(ctx).pop(true), child: const Text('Rechazar')),
          ],
        ),
      );
      if (confirmed != true || controller.text.trim().isEmpty) return;
      motivoRechazo = controller.text.trim();
    }

    setState(() => _resolving = true);
    try {
      await ref.read(paymentsRepositoryProvider).resolveComprobante(
            idPago: widget.payment.id,
            aprobar: aprobar,
            motivoRechazo: motivoRechazo,
          );
      widget.onResolved();
      if (mounted) {
        ScaffoldMessenger.of(context)
            .showSnackBar(SnackBar(content: Text(aprobar ? 'Comprobante aprobado.' : 'Comprobante rechazado.')));
      }
    } catch (_) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('No se pudo resolver el comprobante.')));
      }
    } finally {
      if (mounted) setState(() => _resolving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final p = widget.payment;
    final currency = NumberFormat.currency(locale: 'es_ES', symbol: '€');
    final color = _estadoColor(context, p.estadoComprobante);
    final monto = double.tryParse(p.monto) ?? 0;

    return AppCard(
      margin: const EdgeInsets.only(bottom: Space.md),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
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
          if (p.comprobanteUrl != null) ...[
            const SizedBox(height: Space.sm),
            InkWell(
              onTap: () => _openComprobante(context, p.comprobanteUrl!),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(Icons.attach_file_rounded, size: 16, color: Theme.of(context).colorScheme.primary),
                  const SizedBox(width: 4),
                  Text(
                    'Ver comprobante',
                    style: TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: Theme.of(context).colorScheme.primary),
                  ),
                ],
              ),
            ),
          ],
          if (p.estadoComprobante == 'verificando') ...[
            const SizedBox(height: Space.md),
            Row(
              children: [
                Expanded(
                  child: OutlinedButton(
                    onPressed: _resolving ? null : () => _resolve(false),
                    style: OutlinedButton.styleFrom(foregroundColor: AppColors.rojoLight),
                    child: const Text('Rechazar'),
                  ),
                ),
                const SizedBox(width: Space.sm),
                Expanded(
                  child: FilledButton(
                    onPressed: _resolving ? null : () => _resolve(true),
                    child: const Text('Aprobar'),
                  ),
                ),
              ],
            ),
          ],
        ],
      ),
    );
  }
}
