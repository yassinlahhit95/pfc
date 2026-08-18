import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/filter_bar.dart';
import '../../../core/widgets/premium.dart';
import '../../../core/i18n/translations.dart';
import '../data/schedule_repository.dart';

// Canonical order — the API returns whatever order the DB happens to give,
// which isn't reliably Monday-first.
const _dayOrder = [
  'Lunes',
  'Martes',
  'Miércoles',
  'Jueves',
  'Viernes',
  'Sábado',
  'Domingo'
];
const _dayShort = {
  'Lunes': 'Lun',
  'Martes': 'Mar',
  'Miércoles': 'Mié',
  'Jueves': 'Jue',
  'Viernes': 'Vie',
  'Sábado': 'Sáb',
  'Domingo': 'Dom',
};

class ScheduleScreen extends ConsumerStatefulWidget {
  const ScheduleScreen({super.key});

  @override
  ConsumerState<ScheduleScreen> createState() => _ScheduleScreenState();
}

class _ScheduleScreenState extends ConsumerState<ScheduleScreen> {
  String? _selectedCiclo;

  @override
  Widget build(BuildContext context) {
    final scheduleAsync = ref.watch(scheduleProvider);
    final t = ref.watch(translationsProvider);

    return Scaffold(
      appBar: AppBar(title: Text(t['nav_horario'] ?? 'Horario')),
      body: RefreshIndicator(
        onRefresh: () async => ref.invalidate(scheduleProvider),
        child: AsyncView<List<ScheduleSlot>>(
          value: scheduleAsync,
          onRetry: () => ref.invalidate(scheduleProvider),
          data: (context, allSlots) {
            if (allSlots.isEmpty) {
              return const EmptyState(
                icon: Icons.calendar_month_outlined,
                title: 'Sin horario disponible',
              );
            }

            // Only populated for a tutor's merged multi-child schedule — a
            // single-cycle role (estudiante/profesor) has nombreCiclo null
            // or all-equal, so ciclos.length never exceeds 1 for them.
            final ciclos = allSlots
                .map((s) => s.nombreCiclo)
                .whereType<String>()
                .toSet()
                .toList()
              ..sort();

            final slots = (ciclos.length > 1 && _selectedCiclo != null)
                ? allSlots.where((s) => s.nombreCiclo == _selectedCiclo).toList()
                : allSlots;

            final byDay = <String, List<ScheduleSlot>>{};
            for (final s in slots) {
              byDay.putIfAbsent(s.diaSemana, () => []).add(s);
            }
            final days = _dayOrder.where(byDay.containsKey).toList();
            for (final list in byDay.values) {
              list.sort((a, b) => a.horaInicio.compareTo(b.horaInicio));
            }

            return Column(
              children: [
                if (ciclos.length > 1) ...[
                  const SizedBox(height: Space.md),
                  FilterBar(children: [
                    FilterPill<String>(
                      label: 'Hijo/a',
                      value: _selectedCiclo,
                      options: [for (final c in ciclos) (c, c)],
                      onChanged: (v) => setState(() => _selectedCiclo = v),
                    ),
                  ]),
                ],
                Expanded(child: _DaySchedule(byDay: byDay, days: days)),
              ],
            );
          },
        ),
      ),
    );
  }
}

/// One day visible at a time via a compact day selector — showing all 5
/// working days stacked (up to 6 classes each) was unreadable, this is the
/// simple fix: pick a day, see just that day.
class _DaySchedule extends StatefulWidget {
  const _DaySchedule({required this.byDay, required this.days});
  final Map<String, List<ScheduleSlot>> byDay;
  final List<String> days;

  @override
  State<_DaySchedule> createState() => _DayScheduleState();
}

class _DayScheduleState extends State<_DaySchedule> {
  late String _selected = _todayOrFirst();

  String _todayOrFirst() {
    const weekdayNames = [
      'Lunes',
      'Martes',
      'Miércoles',
      'Jueves',
      'Viernes',
      'Sábado',
      'Domingo'
    ];
    final today = weekdayNames[DateTime.now().weekday - 1];
    return widget.days.contains(today) ? today : widget.days.first;
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final slots = widget.byDay[_selected] ?? [];

    return Column(
      children: [
        const SizedBox(height: Space.md),
        SizedBox(
          height: 40,
          child: ListView.separated(
            scrollDirection: Axis.horizontal,
            padding: const EdgeInsets.symmetric(horizontal: Space.xl),
            itemCount: widget.days.length,
            separatorBuilder: (_, __) => const SizedBox(width: Space.sm),
            itemBuilder: (context, i) {
              final day = widget.days[i];
              final active = day == _selected;
              return Material(
                color: active ? scheme.primary : scheme.surfaceContainerHighest,
                borderRadius: BorderRadius.circular(Radii.pill),
                child: InkWell(
                  borderRadius: BorderRadius.circular(Radii.pill),
                  onTap: () => setState(() => _selected = day),
                  child: Container(
                    alignment: Alignment.center,
                    padding: const EdgeInsets.symmetric(horizontal: Space.lg),
                    child: Text(
                      _dayShort[day] ?? day,
                      style: TextStyle(
                        fontWeight: FontWeight.w600,
                        fontSize: 13,
                        color:
                            active ? scheme.onPrimary : scheme.onSurfaceVariant,
                      ),
                    ),
                  ),
                ),
              );
            },
          ),
        ),
        const SizedBox(height: Space.lg),
        Expanded(
          child: slots.isEmpty
              ? const EmptyState(
                  icon: Icons.free_breakfast_outlined,
                  title: 'Sin clases este día')
              : ListView(
                  padding: const EdgeInsets.fromLTRB(
                      Space.xl, 0, Space.xl, Space.xxxl),
                  children: [
                    AppCard(
                      padding: EdgeInsets.zero,
                      child: Column(
                        children: [
                          for (var i = 0; i < slots.length; i++) ...[
                            _SlotRow(slot: slots[i]),
                            if (i != slots.length - 1)
                              Divider(
                                  height: 1,
                                  indent: Space.lg,
                                  color: scheme.outlineVariant),
                          ],
                        ],
                      ),
                    ),
                  ],
                ),
        ),
      ],
    );
  }
}

class _SlotRow extends StatelessWidget {
  const _SlotRow({required this.slot});
  final ScheduleSlot slot;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Padding(
      padding:
          const EdgeInsets.symmetric(horizontal: Space.lg, vertical: Space.md),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          SizedBox(
            width: 52,
            child: Text(
              slot.horaInicio.substring(0, 5),
              style: Theme.of(context)
                  .textTheme
                  .labelLarge
                  ?.copyWith(color: scheme.onSurfaceVariant, fontSize: 13),
            ),
          ),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(slot.nombreModulo,
                    style: const TextStyle(fontWeight: FontWeight.w600)),
                const SizedBox(height: 2),
                Text(
                  slot.nombreProfesor,
                  style: Theme.of(context).textTheme.bodySmall,
                ),
              ],
            ),
          ),
          if (slot.codigoAula.isNotEmpty || slot.nombreAula.isNotEmpty)
            Text(
              slot.codigoAula.isNotEmpty ? slot.codigoAula : slot.nombreAula,
              style: Theme.of(context)
                  .textTheme
                  .bodySmall
                  ?.copyWith(fontWeight: FontWeight.w600),
            ),
        ],
      ),
    );
  }
}
