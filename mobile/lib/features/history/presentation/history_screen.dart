import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/premium.dart';
import '../data/history_repository.dart';

class HistoryScreen extends ConsumerStatefulWidget {
  const HistoryScreen({super.key});

  @override
  ConsumerState<HistoryScreen> createState() => _HistoryScreenState();
}

class _HistoryScreenState extends ConsumerState<HistoryScreen> {
  late DateTime _selectedDate;

  @override
  void initState() {
    super.initState();
    final now = DateTime.now();
    _selectedDate = DateTime(now.year, now.month, now.day);
  }

  String _formatDateToApi(DateTime dt) {
    return "${dt.year}-${dt.month.toString().padLeft(2, '0')}-${dt.day.toString().padLeft(2, '0')}";
  }

  String _dateLabel(DateTime dt) {
    final now = DateTime.now();
    final today = DateTime(now.year, now.month, now.day);
    final yesterday = today.subtract(const Duration(days: 1));
    final check = DateTime(dt.year, dt.month, dt.day);

    if (check == today) return "Hoy";
    if (check == yesterday) return "Ayer";

    const weekdayShort = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
    final dayName = weekdayShort[dt.weekday - 1];
    return "$dayName ${dt.day}";
  }

  List<DateTime> _getDaysList() {
    final list = <DateTime>[];
    final now = DateTime.now();
    final today = DateTime(now.year, now.month, now.day);
    for (int i = 0; i < 7; i++) {
      list.add(today.subtract(Duration(days: i)));
    }

    final selNormalized =
        DateTime(_selectedDate.year, _selectedDate.month, _selectedDate.day);
    bool found = false;
    for (final d in list) {
      if (DateTime(d.year, d.month, d.day) == selNormalized) {
        found = true;
        break;
      }
    }

    if (!found) {
      // Put selected date first if not in the recent 7 days
      list.insert(0, selNormalized);
    }
    return list;
  }

  IconData _getIcon(String tabla) {
    switch (tabla) {
      case 'gastos':
        return Icons.shopping_bag_outlined;
      case 'estudiantes':
        return Icons.people_outlined;
      case 'ciclos':
      case 'ofertaCiclos':
        return Icons.school_outlined;
      case 'horario':
        return Icons.schedule_rounded;
      case 'anuncios':
        return Icons.campaign_outlined;
      case 'eventos':
        return Icons.event_outlined;
      case 'pagos':
        return Icons.receipt_long_outlined;
      default:
        return Icons.info_outline_rounded;
    }
  }

  Color _getColor(String tabla) {
    switch (tabla) {
      case 'gastos':
        return const Color(0xFFE11D48); // Red/Rose
      case 'estudiantes':
        return const Color(0xFF2563EB); // Blue
      case 'ciclos':
      case 'ofertaCiclos':
        return const Color(0xFF7C3AED); // Violet
      case 'horario':
        return const Color(0xFF0D9488); // Teal
      case 'anuncios':
        return const Color(0xFFD97706); // Amber
      case 'eventos':
        return const Color(0xFF4F46E5); // Indigo
      case 'pagos':
        return const Color(0xFF059669); // Emerald
      default:
        return Colors.blueGrey;
    }
  }

  String _formatTitle(HistoryItem item) {
    final who = item.userName;
    if (who.toLowerCase() == 'sistema') {
      return 'Sistema';
    }
    final role = item.role == 'admin' ? 'director' : 'secretaría';
    return '$who ($role)';
  }

  String _formatActionText(HistoryItem item) {
    final desc = item.descripcion;

    if (item.tabla == 'ofertaCiclos' || item.tabla == 'ciclos') {
      if (item.accion == 'actualizar') {
        return 'Actualizó el ciclo formativo:\n"$desc"';
      }
      if (item.accion == 'insertar') {
        return 'Creó el ciclo formativo:\n"$desc"';
      }
      if (item.accion == 'borrar') {
        return 'Eliminó el ciclo formativo:\n"$desc"';
      }
    }

    if (item.tabla == 'gastos') {
      if (item.accion == 'insertar') {
        return 'Registró un gasto:\n$desc';
      }
      if (item.accion == 'actualizar') {
        return 'Modificó un gasto:\n$desc';
      }
      if (item.accion == 'borrar') {
        return 'Eliminó un gasto:\n$desc';
      }
    }

    if (item.tabla == 'estudiantes') {
      if (item.accion == 'eliminar' || item.accion == 'borrar') {
        return 'Eliminó al estudiante:\n$desc';
      }
      if (item.accion == 'insertar') {
        return 'Registró al estudiante:\n$desc';
      }
    }

    if (item.tabla == 'eventos') {
      if (item.accion == 'insertar') {
        return 'Creó el evento:\n"$desc"';
      }
      if (item.accion == 'actualizar') {
        return 'Actualizó el evento:\n"$desc"';
      }
      if (item.accion == 'borrar') {
        return 'Eliminó el evento:\n"$desc"';
      }
    }

    if (item.tabla == 'horario') {
      if (item.accion == 'guardar') {
        return 'Guardó celda de horario:\n$desc';
      }
      if (item.accion == 'borrar') {
        return 'Borró celda de horario:\n$desc';
      }
      if (item.accion == 'remove_franja') {
        return 'Eliminó franja horaria:\n$desc';
      }
    }

    if (item.tabla == 'pagos') {
      if (item.accion == 'rechazar_comprobante') {
        return 'Rechazó comprobante de pago:\n$desc';
      }
      if (item.accion == 'resolver' || item.accion == 'cobrar') {
        return 'Registró/Aprobó pago:\n$desc';
      }
    }

    final verb = switch (item.accion) {
      'insertar' => 'creó/insertó',
      'actualizar' => 'actualizó',
      'borrar' || 'eliminar' => 'eliminó',
      _ => item.accion,
    };
    return 'Realizó la acción "$verb" en "${item.tabla}":\n$desc';
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final dateStr = _formatDateToApi(_selectedDate);
    final historyAsync = ref.watch(historyProvider(dateStr));
    final daysList = _getDaysList();

    return Scaffold(
      backgroundColor: const Color(0xFFFAFAFA),
      appBar: AppBar(
        title: const Text('Historial de Actividad'),
      ),
      body: Column(
        children: [
          const SizedBox(height: Space.md),
          // Day selector bar
          SizedBox(
            height: 48,
            child: Row(
              children: [
                const SizedBox(width: Space.lg),
                Expanded(
                  child: ListView.separated(
                    scrollDirection: Axis.horizontal,
                    reverse: true,
                    padding: const EdgeInsets.only(left: Space.xl),
                    itemCount: daysList.length,
                    separatorBuilder: (_, __) =>
                        const SizedBox(width: Space.sm),
                    itemBuilder: (context, i) {
                      final day = daysList[i];
                      final active = DateTime(day.year, day.month, day.day) ==
                          DateTime(_selectedDate.year, _selectedDate.month,
                              _selectedDate.day);
                      return Material(
                        color: active
                            ? scheme.primary
                            : scheme.surfaceContainerHighest,
                        borderRadius: BorderRadius.circular(Radii.pill),
                        child: InkWell(
                          borderRadius: BorderRadius.circular(Radii.pill),
                          onTap: () => setState(() => _selectedDate = day),
                          child: Container(
                            alignment: Alignment.center,
                            padding: const EdgeInsets.symmetric(
                                horizontal: Space.lg),
                            child: Text(
                              _dateLabel(day),
                              style: TextStyle(
                                fontWeight: FontWeight.w600,
                                fontSize: 13,
                                color: active
                                    ? scheme.onPrimary
                                    : scheme.onSurfaceVariant,
                              ),
                            ),
                          ),
                        ),
                      );
                    },
                  ),
                ),
                const SizedBox(width: Space.xs),
                // Calendar date picker button
                IconButton(
                  icon: Icon(Icons.calendar_month_outlined,
                      color: scheme.primary),
                  onPressed: () async {
                    final picked = await showDatePicker(
                      context: context,
                      initialDate: _selectedDate,
                      firstDate: DateTime(2020),
                      lastDate: DateTime.now(),
                    );
                    if (picked != null) {
                      setState(() {
                        _selectedDate = picked;
                      });
                    }
                  },
                ),
                const SizedBox(width: Space.lg),
              ],
            ),
          ),
          const SizedBox(height: Space.lg),
          // Actions list
          Expanded(
            child: RefreshIndicator(
              onRefresh: () async => ref.invalidate(historyProvider(dateStr)),
              child: AsyncView<List<HistoryItem>>(
                value: historyAsync,
                onRetry: () => ref.invalidate(historyProvider(dateStr)),
                data: (context, items) {
                  if (items.isEmpty) {
                    return const EmptyState(
                      icon: Icons.history_rounded,
                      title: 'Sin actividad este día',
                      description:
                          'No se encontraron registros de auditoría para la fecha seleccionada.',
                    );
                  }

                  return ListView.separated(
                    padding: const EdgeInsets.fromLTRB(
                        Space.xl, 0, Space.xl, Space.xxxl),
                    itemCount: items.length,
                    separatorBuilder: (_, __) =>
                        const SizedBox(height: Space.md),
                    itemBuilder: (context, i) {
                      final item = items[i];
                      final color = _getColor(item.tabla);
                      final icon = _getIcon(item.tabla);
                      final title = _formatTitle(item);
                      final actionText = _formatActionText(item);
                      final timeStr =
                          "${item.fecha.hour.toString().padLeft(2, '0')}:${item.fecha.minute.toString().padLeft(2, '0')}";

                      return IntrinsicHeight(
                        child: Row(
                          crossAxisAlignment: CrossAxisAlignment.stretch,
                          children: [
                            // Timeline connector column
                            SizedBox(
                              width: 32,
                              child: Column(
                                children: [
                                  Container(
                                    width: 1.5,
                                    height: 12,
                                    color: i == 0
                                        ? Colors.transparent
                                        : scheme.outlineVariant
                                            .withValues(alpha: 0.8),
                                  ),
                                  Container(
                                    width: 32,
                                    height: 32,
                                    decoration: BoxDecoration(
                                      color: color.withValues(alpha: 0.12),
                                      shape: BoxShape.circle,
                                      border: Border.all(
                                          color: color.withValues(alpha: 0.3),
                                          width: 1.5),
                                    ),
                                    child: Icon(icon, color: color, size: 16),
                                  ),
                                  Expanded(
                                    child: Container(
                                      width: 1.5,
                                      color: i == items.length - 1
                                          ? Colors.transparent
                                          : scheme.outlineVariant
                                              .withValues(alpha: 0.8),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                            const SizedBox(width: Space.md),
                            // Details card
                            Expanded(
                              child: AppCard(
                                padding: const EdgeInsets.all(Space.md),
                                child: Row(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Expanded(
                                      child: Column(
                                        crossAxisAlignment:
                                            CrossAxisAlignment.start,
                                        children: [
                                          Text(
                                            title,
                                            style: const TextStyle(
                                              fontWeight: FontWeight.bold,
                                              fontSize: 14,
                                            ),
                                          ),
                                          const SizedBox(height: 4),
                                          Text(
                                            actionText,
                                            style: TextStyle(
                                              color: scheme.onSurfaceVariant,
                                              fontSize: 13,
                                              height: 1.3,
                                            ),
                                          ),
                                        ],
                                      ),
                                    ),
                                    const SizedBox(width: Space.sm),
                                    Text(
                                      timeStr,
                                      style: TextStyle(
                                        color: scheme.onSurfaceVariant
                                            .withValues(alpha: 0.7),
                                        fontSize: 12,
                                        fontWeight: FontWeight.w500,
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ],
                        ),
                      );
                    },
                  );
                },
              ),
            ),
          ),
        ],
      ),
    );
  }
}
