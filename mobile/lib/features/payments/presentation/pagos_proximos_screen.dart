import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../data/payments_repository.dart';

class PagosProximosScreen extends StatefulWidget {
  const PagosProximosScreen({super.key});

  @override
  State<PagosProximosScreen> createState() => _PagosProximosScreenState();
}

class _PagosProximosScreenState extends State<PagosProximosScreen> {
  late ScrollController _scrollController;
  String? _selectedStatus;
  int _currentOffset = 0;
  static const int _pageSize = 20;

  @override
  void initState() {
    super.initState();
    _scrollController = ScrollController();
  }

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Pagos Próximos')),
      body: Consumer(
        builder: (context, ref, _) {
          final pagosAsync = ref.watch(
            pagosProximosProvider(
              (
                limit: _pageSize,
                offset: _currentOffset,
                status: _selectedStatus,
                cicloId: null,
              ),
            ),
          );

          return AsyncView<({List<PagoProximo> pagos, int total})>(
            value: pagosAsync,
            onRetry: () => ref.invalidate(
              pagosProximosProvider(
                (
                  limit: _pageSize,
                  offset: _currentOffset,
                  status: _selectedStatus,
                  cicloId: null,
                ),
              ),
            ),
            data: (context, data) {
              if (data.pagos.isEmpty) {
                return const EmptyState(icon: Icons.receipt_outlined, title: 'Sin pagos próximos');
              }

              return Column(
                children: [
                  Padding(
                    padding: const EdgeInsets.all(Space.md),
                    child: SizedBox(
                      height: 36,
                      child: ListView(
                        scrollDirection: Axis.horizontal,
                        children: [
                          _FilterChip(
                            label: 'Todos',
                            isSelected: _selectedStatus == null,
                            onPressed: () => setState(() {
                              _selectedStatus = null;
                              _currentOffset = 0;
                            }),
                          ),
                          const SizedBox(width: Space.sm),
                          _FilterChip(
                            label: 'Pendiente',
                            isSelected: _selectedStatus == 'pendiente',
                            onPressed: () => setState(() {
                              _selectedStatus = 'pendiente';
                              _currentOffset = 0;
                            }),
                          ),
                          const SizedBox(width: Space.sm),
                          _FilterChip(
                            label: 'Vencido',
                            isSelected: _selectedStatus == 'vencido',
                            onPressed: () => setState(() {
                              _selectedStatus = 'vencido';
                              _currentOffset = 0;
                            }),
                          ),
                          const SizedBox(width: Space.sm),
                          _FilterChip(
                            label: 'Pagado',
                            isSelected: _selectedStatus == 'pagado',
                            onPressed: () => setState(() {
                              _selectedStatus = 'pagado';
                              _currentOffset = 0;
                            }),
                          ),
                        ],
                      ),
                    ),
                  ),
                  Expanded(
                    child: ListView.separated(
                      controller: _scrollController,
                      itemCount: data.pagos.length,
                      separatorBuilder: (_, __) => const SizedBox(height: Space.sm),
                      itemBuilder: (context, index) {
                        final pago = data.pagos[index];
                        return _PagoCard(pago: pago);
                      },
                    ),
                  ),
                  if (data.total > _currentOffset + _pageSize)
                    Padding(
                      padding: const EdgeInsets.all(Space.md),
                      child: ElevatedButton.icon(
                        onPressed: () => setState(() => _currentOffset += _pageSize),
                        icon: const Icon(Icons.arrow_downward),
                        label: const Text('Cargar más'),
                      ),
                    ),
                ],
              );
            },
          );
        },
      ),
    );
  }
}

class _FilterChip extends StatelessWidget {
  const _FilterChip({required this.label, required this.isSelected, required this.onPressed});
  final String label;
  final bool isSelected;
  final VoidCallback onPressed;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return FilterChip(
      label: Text(label),
      selected: isSelected,
      onSelected: (_) => onPressed(),
      backgroundColor: scheme.surfaceContainerHighest,
      selectedColor: scheme.primary,
      labelStyle: TextStyle(
        color: isSelected ? scheme.onPrimary : scheme.onSurfaceVariant,
      ),
    );
  }
}

class _PagoCard extends StatelessWidget {
  const _PagoCard({required this.pago});
  final PagoProximo pago;

  Color _getStatusColor(BuildContext context) {
    final dark = Theme.of(context).brightness == Brightness.dark;
    return switch (pago.estado) {
      'pagado' => dark ? AppColors.verdeDark : AppColors.verdeLight,
      'vencido' => dark ? AppColors.rojoDark : AppColors.rojoLight,
      _ => dark ? AppColors.naranjaDark : AppColors.naranjaLight,
    };
  }

  String _getStatusLabel() => switch (pago.estado) {
        'pagado' => 'Pagado',
        'vencido' => 'Vencido',
        _ => 'Pendiente',
      };

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final textTheme = Theme.of(context).textTheme;
    final fecha = DateTime.tryParse(pago.fechaProximoPago);
    final formattedDate =
        fecha != null ? DateFormat('d MMM yyyy', 'es_ES').format(fecha) : pago.fechaProximoPago;

    return Container(
      margin: const EdgeInsets.symmetric(horizontal: Space.md),
      padding: const EdgeInsets.all(Space.md),
      decoration: BoxDecoration(
        color: scheme.surfaceContainerHighest,
        borderRadius: BorderRadius.circular(Radii.md),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(pago.nombreEstudiante, style: textTheme.titleSmall),
                    Text(
                      pago.abreviaturaCiclo,
                      style: textTheme.bodySmall?.copyWith(color: scheme.onSurfaceVariant),
                    ),
                  ],
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(
                  color: _getStatusColor(context).withValues(alpha: 0.15),
                  borderRadius: BorderRadius.circular(Radii.pill),
                ),
                child: Text(
                  _getStatusLabel(),
                  style: textTheme.labelSmall?.copyWith(color: _getStatusColor(context)),
                ),
              ),
            ],
          ),
          const SizedBox(height: Space.md),
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Monto',
                    style: textTheme.bodySmall?.copyWith(color: scheme.onSurfaceVariant),
                  ),
                  Text(
                    '\$${pago.monto}',
                    style: textTheme.titleSmall?.copyWith(fontWeight: FontWeight.bold),
                  ),
                ],
              ),
              Column(
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  Text(
                    'Próximo Pago',
                    style: textTheme.bodySmall?.copyWith(color: scheme.onSurfaceVariant),
                  ),
                  Text(
                    formattedDate,
                    style: textTheme.titleSmall?.copyWith(fontWeight: FontWeight.bold),
                  ),
                ],
              ),
            ],
          ),
          if (pago.tipoPago.isNotEmpty) ...[
            const SizedBox(height: Space.sm),
            Text(
              'Frecuencia: ${_capitalizeTipoPago(pago.tipoPago)}',
              style: textTheme.bodySmall?.copyWith(color: scheme.onSurfaceVariant),
            ),
          ],
        ],
      ),
    );
  }

  String _capitalizeTipoPago(String tipo) => switch (tipo) {
        'mensual' => 'Mensual',
        'trimestral' => 'Trimestral',
        'semestral' => 'Semestral',
        'unico' => 'Único',
        _ => tipo,
      };
}
