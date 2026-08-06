import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../core/auth/auth_state.dart';
import '../../../core/auth/session.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/error_modal.dart';
import '../../../core/widgets/filter_bar.dart';
import '../../../core/widgets/premium.dart';
import '../data/attendance_repository.dart';
import 'justify_sheet.dart';
import 'mark_attendance_screen.dart';

class AttendanceScreen extends StatelessWidget {
  const AttendanceScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return _AttendanceScreenWrapper();
  }
}

class _AttendanceScreenWrapper extends ConsumerStatefulWidget {
  const _AttendanceScreenWrapper();

  @override
  ConsumerState<_AttendanceScreenWrapper> createState() =>
      _AttendanceScreenWrapperState();
}

class _AttendanceScreenWrapperState
    extends ConsumerState<_AttendanceScreenWrapper>
    with TickerProviderStateMixin {
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
    return _AttendanceScreenContent(tabController: _tabController);
  }
}

class _AttendanceScreenContent extends ConsumerWidget {
  const _AttendanceScreenContent({required this.tabController});
  final TabController tabController;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final sessionAsync = ref.watch(sessionControllerProvider);
    final role = sessionAsync.value?.role;

    if (role == null) {
      return Scaffold(
        appBar: AppBar(title: const Text('Asistencias')),
        body: const Center(child: CircularProgressIndicator()),
      );
    }

    if (role == UserRole.profesor) {
      return Scaffold(
        appBar: AppBar(
          title: const Text('Asistencias'),
          elevation: 0,
          backgroundColor: Colors.transparent,
          bottom: PreferredSize(
            preferredSize: const Size.fromHeight(48),
            child: TabBar(
              controller: tabController,
              indicatorSize: TabBarIndicatorSize.label,
              indicator: BoxDecoration(
                borderRadius: BorderRadius.circular(8),
                color: Theme.of(context)
                    .colorScheme
                    .primary
                    .withValues(alpha: 0.15),
              ),
              tabs: const [
                Tab(text: '📋 Pasar lista'),
                Tab(text: '✓ Justificaciones'),
              ],
            ),
          ),
        ),
        body: TabBarView(
          controller: tabController,
          children: const [MarkAttendanceScreen(), _PendingJustificationsTab()],
        ),
      );
    }

    return const _MyAttendanceList();
  }
}

Future<void> _openJustificante(BuildContext context, String url) async {
  final ok =
      await launchUrl(Uri.parse(url), mode: LaunchMode.externalApplication);
  if (!ok && context.mounted) {
    await showErrorAlert(context, 'No se pudo abrir el justificante.');
  }
}

const _estadoColors = {
  'presente': AppColors.verdeLight,
  'ausente': AppColors.rojoLight,
  'retraso': AppColors.naranjaLight,
  'justificado': AppColors.azulLight,
};
const _estadoLabels = {
  'presente': 'Presente',
  'ausente': 'Ausente',
  'retraso': 'Retraso',
  'justificado': 'Justificado',
};

const _months = [
  'Enero',
  'Febrero',
  'Marzo',
  'Abril',
  'Mayo',
  'Junio',
  'Julio',
  'Agosto',
  'Septiembre',
  'Octubre',
  'Noviembre',
  'Diciembre'
];
const _weekdays = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];

class _MyAttendanceList extends ConsumerStatefulWidget {
  const _MyAttendanceList();

  @override
  ConsumerState<_MyAttendanceList> createState() => _MyAttendanceListState();
}

class _MyAttendanceListState extends ConsumerState<_MyAttendanceList> {
  late DateTime _focusedMonth;
  late DateTime _selectedDay;
  String? _selectedStudent;

  @override
  void initState() {
    super.initState();
    final now = DateTime.now();
    _focusedMonth = DateTime(now.year, now.month, 1);
    _selectedDay = DateTime(now.year, now.month, now.day);
  }

  String _normalizeDate(DateTime dt) {
    return "${dt.year}-${dt.month.toString().padLeft(2, '0')}-${dt.day.toString().padLeft(2, '0')}";
  }

  bool _isWeekend(DateTime date) => date.weekday > 5;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final attendanceAsync = ref.watch(attendanceMineProvider);

    return Scaffold(
      appBar: AppBar(
        title: const Text('Asistencias'),
        elevation: 0,
        backgroundColor: Colors.transparent,
      ),
      body: AsyncView<List<AttendanceRecord>>(
        value: attendanceAsync,
        onRetry: () => ref.invalidate(attendanceMineProvider),
        data: (context, allRecords) {
          final estudiantes = allRecords
              .map((r) => r.nombreEstudiante)
              .toSet()
              .toList()
            ..sort();

          if (_selectedStudent == null && estudiantes.isNotEmpty) {
            _selectedStudent = estudiantes.first;
          }

          final studentRecords = _selectedStudent != null
              ? allRecords
                  .where((r) => r.nombreEstudiante == _selectedStudent)
                  .toList()
              : allRecords;

          final totalDays =
              DateTime(_focusedMonth.year, _focusedMonth.month + 1, 0).day;
          final offset =
              DateTime(_focusedMonth.year, _focusedMonth.month, 1).weekday - 1;
          final totalGridItems = offset + totalDays;

          final monthPrefix =
              "${_focusedMonth.year}-${_focusedMonth.month.toString().padLeft(2, '0')}";
          final monthRecords = studentRecords
              .where((r) => r.fecha.startsWith(monthPrefix))
              .toList();
          final totalAusencias =
              monthRecords.where((r) => r.estado == 'ausente').length;
          final totalRetrasos =
              monthRecords.where((r) => r.estado == 'retraso').length;
          final totalJustificados =
              monthRecords.where((r) => r.estado == 'justificado').length;

          final dayStr = _normalizeDate(_selectedDay);
          final dayRecords =
              studentRecords.where((r) => r.fecha == dayStr).toList();

          return ListView(
            children: [
              if (estudiantes.length > 1) ...[
                const SizedBox(height: Space.md),
                FilterBar(children: [
                  FilterPill<String>(
                    label: 'Estudiante',
                    value: _selectedStudent,
                    options: [for (final s in estudiantes) (s, s)],
                    onChanged: (v) {
                      setState(() => _selectedStudent = v);
                    },
                  ),
                ]),
              ],

              const SizedBox(height: Space.lg),

              // Modern Minimalist Calendar Card
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: Space.md),
                child: Container(
                  decoration: BoxDecoration(
                    color: scheme.surfaceContainerHigh,
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(
                      color: scheme.outlineVariant.withValues(alpha: 0.3),
                      width: 1,
                    ),
                  ),
                  padding: const EdgeInsets.all(Space.md),
                  child: Column(
                    children: [
                      // Month Navigation
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          IconButton(
                            icon: const Icon(Icons.chevron_left_rounded),
                            onPressed: () => setState(() {
                              _focusedMonth = DateTime(_focusedMonth.year,
                                  _focusedMonth.month - 1, 1);
                            }),
                            color: scheme.onSurfaceVariant,
                          ),
                          Column(
                            children: [
                              Text(
                                _months[_focusedMonth.month - 1],
                                style: Theme.of(context)
                                    .textTheme
                                    .titleMedium
                                    ?.copyWith(
                                      fontWeight: FontWeight.bold,
                                    ),
                              ),
                              Text(
                                _focusedMonth.year.toString(),
                                style: Theme.of(context)
                                    .textTheme
                                    .bodySmall
                                    ?.copyWith(
                                      color: scheme.onSurfaceVariant,
                                    ),
                              ),
                            ],
                          ),
                          IconButton(
                            icon: const Icon(Icons.chevron_right_rounded),
                            onPressed: () => setState(() {
                              _focusedMonth = DateTime(_focusedMonth.year,
                                  _focusedMonth.month + 1, 1);
                            }),
                            color: scheme.onSurfaceVariant,
                          ),
                        ],
                      ),

                      const SizedBox(height: Space.md),

                      // Weekday Headers
                      Row(
                        children: [
                          for (int i = 0; i < 7; i++)
                            Expanded(
                              child: Center(
                                child: Text(
                                  _weekdays[i],
                                  style: Theme.of(context)
                                      .textTheme
                                      .labelSmall
                                      ?.copyWith(
                                        color: i >= 5
                                            ? scheme.error.withValues(alpha: 0.7)
                                            : scheme.onSurfaceVariant,
                                        fontWeight: FontWeight.bold,
                                      ),
                                ),
                              ),
                            ),
                        ],
                      ),

                      const SizedBox(height: Space.md),

                      // Calendar Grid
                      GridView.builder(
                        shrinkWrap: true,
                        physics: const NeverScrollableScrollPhysics(),
                        itemCount: totalGridItems,
                        gridDelegate:
                            const SliverGridDelegateWithFixedCrossAxisCount(
                          crossAxisCount: 7,
                          childAspectRatio: 1,
                          mainAxisSpacing: 6,
                          crossAxisSpacing: 6,
                        ),
                        itemBuilder: (context, i) {
                          if (i < offset) return const SizedBox.shrink();

                          final dayNum = i - offset + 1;
                          final dayDate = DateTime(
                              _focusedMonth.year, _focusedMonth.month, dayNum);
                          final active = DateTime(
                                  dayDate.year, dayDate.month, dayDate.day) ==
                              DateTime(_selectedDay.year, _selectedDay.month,
                                  _selectedDay.day);
                          final isWeekend = _isWeekend(dayDate);

                          final itemDayStr = _normalizeDate(dayDate);
                          final itemRecords = studentRecords
                              .where((r) => r.fecha == itemDayStr)
                              .toList();

                          final hasIssue =
                              itemRecords.any((r) => r.estado != 'presente');
                          final issueRecord = hasIssue
                              ? itemRecords.firstWhere(
                                  (r) => r.estado != 'presente',
                                  orElse: () => itemRecords.first)
                              : itemRecords.isNotEmpty
                                  ? itemRecords.first
                                  : null;

                          final stateColor = issueRecord != null
                              ? (_estadoColors[issueRecord.estado] ??
                                  scheme.error)
                              : scheme.outline;

                          return GestureDetector(
                            onTap: isWeekend
                                ? null
                                : () => setState(() => _selectedDay = dayDate),
                            child: Center(
                              child: Container(
                                width: 38,
                                height: 38,
                                decoration: BoxDecoration(
                                  shape: BoxShape.circle,
                                  color: active
                                      ? scheme.primary
                                      : null,
                                ),
                                alignment: Alignment.center,
                                child: Column(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    Text(
                                      dayNum.toString(),
                                      style: TextStyle(
                                        fontWeight: active
                                            ? FontWeight.bold
                                            : FontWeight.w600,
                                        color: active
                                            ? scheme.onPrimary
                                            : isWeekend
                                                ? scheme.onSurfaceVariant.withValues(alpha: 0.35)
                                                : scheme.onSurface,
                                        fontSize: 13,
                                      ),
                                    ),
                                    if (itemRecords.isNotEmpty) ...[
                                      const SizedBox(height: 2),
                                      Container(
                                        width: 4,
                                        height: 4,
                                        decoration: BoxDecoration(
                                          color: active
                                              ? scheme.onPrimary
                                              : stateColor,
                                          shape: BoxShape.circle,
                                        ),
                                      ),
                                    ],
                                  ],
                                ),
                              ),
                            ),
                          );
                        },
                      ),
                    ],
                  ),
                ),
              ),

              const SizedBox(height: Space.lg),

              // Stats Row - Luxury Style
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: Space.md),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                  children: [
                    _LuxuryStat(
                      count: totalAusencias,
                      label: 'Ausencias',
                      color: AppColors.rojoLight,
                      icon: Icons.close_rounded,
                    ),
                    _LuxuryStat(
                      count: totalRetrasos,
                      label: 'Retrasos',
                      color: AppColors.naranjaLight,
                      icon: Icons.schedule_rounded,
                    ),
                    _LuxuryStat(
                      count: totalJustificados,
                      label: 'Justificados',
                      color: AppColors.azulLight,
                      icon: Icons.check_circle_rounded,
                    ),
                  ],
                ),
              ),

              const SizedBox(height: Space.xl),

              // Day Details Section
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: Space.md),
                child: Row(
                  children: [
                    Container(
                      width: 4,
                      height: 24,
                      decoration: BoxDecoration(
                        color: scheme.primary,
                        borderRadius: BorderRadius.circular(2),
                      ),
                    ),
                    const SizedBox(width: Space.md),
                    Text(
                      DateFormat('EEEE, d MMMM yyyy', 'es')
                          .format(_selectedDay),
                      style: Theme.of(context).textTheme.titleMedium?.copyWith(
                            fontWeight: FontWeight.w800,
                            letterSpacing: 0.3,
                          ),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: Space.md),

              // Daily Records
              if (dayRecords.isEmpty)
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: Space.md),
                  child: const EmptyState(
                    icon: Icons.check_circle_outline_rounded,
                    title: 'Día sin incidencias',
                    description:
                        'No hay registros de faltas o retrasos en esta fecha.',
                  ),
                )
              else
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: Space.md),
                  child: ListView.builder(
                    shrinkWrap: true,
                    physics: const NeverScrollableScrollPhysics(),
                    itemCount: dayRecords.length,
                    itemBuilder: (context, i) =>
                        _AttendanceCard(record: dayRecords[i]),
                  ),
                ),

              const SizedBox(height: Space.xxxl),
            ],
          );
        },
      ),
    );
  }
}

class _LuxuryStat extends StatelessWidget {
  const _LuxuryStat({
    required this.count,
    required this.label,
    required this.color,
    required this.icon,
  });

  final int count;
  final String label;
  final Color color;
  final IconData icon;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;

    return Expanded(
      child: Container(
        margin: const EdgeInsets.symmetric(horizontal: 4),
        padding: const EdgeInsets.symmetric(vertical: Space.md, horizontal: Space.sm),
        decoration: BoxDecoration(
          color: scheme.surfaceContainerLow,
          borderRadius: BorderRadius.circular(12),
          border: Border.all(
            color: scheme.outlineVariant.withValues(alpha: 0.3),
            width: 1,
          ),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Icon(icon, size: 20, color: color),
            const SizedBox(height: Space.xs),
            Text(
              count.toString(),
              style: TextStyle(
                color: color,
                fontWeight: FontWeight.bold,
                fontSize: 16,
              ),
            ),
            const SizedBox(height: 2),
            Text(
              label,
              style: TextStyle(
                color: scheme.onSurfaceVariant,
                fontWeight: FontWeight.w500,
                fontSize: 11,
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _AttendanceCard extends ConsumerWidget {
  const _AttendanceCard({required this.record});
  final AttendanceRecord record;

  IconData _getStatusIcon(String estado) => switch (estado) {
        'presente' => Icons.check_circle_rounded,
        'ausente' => Icons.cancel_rounded,
        'retraso' => Icons.schedule_rounded,
        'justificado' => Icons.verified_rounded,
        _ => Icons.help_rounded,
      };

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final color =
        _estadoColors[record.estado] ?? Theme.of(context).colorScheme.outline;
    final date = DateTime.tryParse(record.fecha);
    final role = ref.watch(sessionControllerProvider).value?.role;
    final scheme = Theme.of(context).colorScheme;

    return Container(
      margin: const EdgeInsets.only(bottom: Space.md),
      decoration: BoxDecoration(
        color: scheme.surfaceContainerLow,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: scheme.outlineVariant.withValues(alpha: 0.3),
          width: 1,
        ),
      ),
      child: Padding(
        padding: const EdgeInsets.all(Space.md),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Container(
                  padding: const EdgeInsets.all(Space.sm),
                  decoration: BoxDecoration(
                    color: color.withValues(alpha: 0.15),
                    borderRadius: BorderRadius.circular(Radii.lg),
                  ),
                  child: Icon(_getStatusIcon(record.estado),
                      color: color, size: 20),
                ),
                const SizedBox(width: Space.md),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        record.nombreModulo,
                        style: Theme.of(context).textTheme.titleSmall?.copyWith(
                              fontWeight: FontWeight.w700,
                            ),
                        overflow: TextOverflow.ellipsis,
                      ),
                      const SizedBox(height: 3),
                      Text(
                        [
                          if (date != null) DateFormat('d MMM').format(date),
                          if (record.nombreProfesor.isNotEmpty)
                            '• ${record.nombreProfesor}',
                        ].join(' '),
                        style: Theme.of(context).textTheme.labelSmall?.copyWith(
                              color: scheme.onSurfaceVariant,
                            ),
                        overflow: TextOverflow.ellipsis,
                      ),
                    ],
                  ),
                ),
                const SizedBox(width: Space.md),
                Container(
                  padding: const EdgeInsets.symmetric(
                      horizontal: Space.md, vertical: Space.sm),
                  decoration: BoxDecoration(
                    color: color.withValues(alpha: 0.15),
                    borderRadius: BorderRadius.circular(Radii.pill),
                  ),
                  child: Text(
                    _estadoLabels[record.estado] ?? record.estado,
                    style: TextStyle(
                      color: color,
                      fontWeight: FontWeight.w700,
                      fontSize: 11,
                    ),
                  ),
                ),
              ],
            ),
            if (record.justificacion != null) ...[
              const SizedBox(height: Space.md),
              _JustificationBox(justificacion: record.justificacion!),
            ],
            if ((role == UserRole.tutor ||
                    role == UserRole.director ||
                    role == UserRole.secretaria) &&
                record.canJustify) ...[
              const SizedBox(height: Space.md),
              SizedBox(
                width: double.infinity,
                child: FilledButton.icon(
                  onPressed: () async {
                    final sent = await showJustifySheet(
                      context,
                      ref,
                      idAsistencia: record.id,
                      subtitulo:
                          '${record.nombreEstudiante} · ${record.nombreModulo} · ${record.fecha}',
                    );
                    if (sent) {
                      ref.invalidate(attendanceMineProvider);
                    }
                  },
                  icon: const Icon(Icons.add_a_photo_outlined, size: 16),
                  label: const Text('Justificar'),
                ),
              ),
            ],
          ],
        ),
      ),
    );
  }
}

const _justEstadoLabels = {
  'pendiente': 'Justificación pendiente',
  'aprobada': 'Justificación aprobada',
  'rechazada': 'Justificación rechazada',
};
const _justEstadoColors = {
  'pendiente': AppColors.naranjaLight,
  'aprobada': AppColors.verdeLight,
  'rechazada': AppColors.rojoLight,
};

class _JustificationBox extends StatelessWidget {
  const _JustificationBox({required this.justificacion});
  final Justification justificacion;

  IconData _getJustIcon(String estado) => switch (estado) {
        'aprobada' => Icons.verified_rounded,
        'rechazada' => Icons.cancel_rounded,
        _ => Icons.pending_actions_rounded,
      };

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final color = _justEstadoColors[justificacion.estado] ?? scheme.outline;

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(Space.md),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            color.withValues(alpha: 0.12),
            color.withValues(alpha: 0.04),
          ],
        ),
        borderRadius: BorderRadius.circular(Radii.lg),
        border: Border.all(color: color.withValues(alpha: 0.2)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Icon(_getJustIcon(justificacion.estado), size: 16, color: color),
              const SizedBox(width: Space.sm),
              Expanded(
                child: Text(
                  _justEstadoLabels[justificacion.estado] ??
                      justificacion.estado,
                  style: TextStyle(
                      fontWeight: FontWeight.w700, fontSize: 13, color: color),
                ),
              ),
              if (justificacion.archivoUrl != null)
                GestureDetector(
                  onTap: () =>
                      _openJustificante(context, justificacion.archivoUrl!),
                  child: Container(
                    padding: const EdgeInsets.symmetric(
                        horizontal: Space.sm, vertical: 2),
                    decoration: BoxDecoration(
                      color: scheme.primary.withValues(alpha: 0.1),
                      borderRadius: BorderRadius.circular(Radii.sm),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(Icons.attach_file_rounded,
                            size: 14, color: scheme.primary),
                        const SizedBox(width: 3),
                        Text(
                          'Ver',
                          style: TextStyle(
                              fontSize: 11,
                              fontWeight: FontWeight.w600,
                              color: scheme.primary),
                        ),
                      ],
                    ),
                  ),
                ),
            ],
          ),
          const SizedBox(height: Space.sm),
          Text(
            justificacion.motivo,
            style: Theme.of(context).textTheme.bodySmall?.copyWith(
                  color: scheme.onSurface.withValues(alpha: 0.8),
                ),
          ),
          if (justificacion.estado == 'rechazada' &&
              (justificacion.motivoRechazo?.isNotEmpty ?? false)) ...[
            const SizedBox(height: Space.sm),
            Container(
              padding: const EdgeInsets.all(Space.sm),
              decoration: BoxDecoration(
                color: AppColors.rojoLight.withValues(alpha: 0.08),
                borderRadius: BorderRadius.circular(Radii.md),
              ),
              child: Text(
                'Rechazo: ${justificacion.motivoRechazo}',
                style: Theme.of(context).textTheme.labelSmall?.copyWith(
                      color: AppColors.rojoLight.withValues(alpha: 0.9),
                      fontWeight: FontWeight.w600,
                    ),
              ),
            ),
          ],
        ],
      ),
    );
  }
}

class _PendingJustificationsTab extends ConsumerWidget {
  const _PendingJustificationsTab();

  Future<void> _resolve(BuildContext context, WidgetRef ref,
      PendingJustification pj, bool aprobar) async {
    String? motivoRechazo;
    if (!aprobar) {
      final controller = TextEditingController();
      final confirmed = await showDialog<bool>(
        context: context,
        builder: (ctx) => AlertDialog(
          title: const Text('Rechazar justificación'),
          content: TextField(
            controller: controller,
            decoration: const InputDecoration(labelText: 'Motivo del rechazo'),
          ),
          actions: [
            TextButton(
                onPressed: () => Navigator.of(ctx).pop(false),
                child: const Text('Cancelar')),
            FilledButton(
                onPressed: () => Navigator.of(ctx).pop(true),
                child: const Text('Rechazar')),
          ],
        ),
      );
      if (confirmed != true || controller.text.trim().isEmpty) return;
      motivoRechazo = controller.text.trim();
    }

    try {
      await ref.read(attendanceRepositoryProvider).resolve(
            idJustificacion: pj.idJustificacion,
            aprobar: aprobar,
            motivoRechazo: motivoRechazo,
          );
      ref.invalidate(pendingJustificationsProvider);
      if (context.mounted) {
        await showErrorAlert(context, aprobar ? 'Aprobada.' : 'Rechazada.',
            title: 'Éxito');
      }
    } catch (_) {
      if (context.mounted) {
        await showErrorAlert(context, 'No se pudo resolver.');
      }
    }
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final pendingAsync = ref.watch(pendingJustificationsProvider);
    return AsyncView<List<PendingJustification>>(
      value: pendingAsync,
      onRetry: () => ref.invalidate(pendingJustificationsProvider),
      data: (context, items) {
        if (items.isEmpty) {
          return const EmptyState(
              icon: Icons.fact_check_outlined,
              title: 'Sin justificaciones pendientes');
        }
        return RefreshIndicator(
          onRefresh: () async => ref.invalidate(pendingJustificationsProvider),
          child: ListView.builder(
            padding: const EdgeInsets.fromLTRB(
                Space.xl, Space.lg, Space.xl, Space.xxxl),
            itemCount: items.length,
            itemBuilder: (context, i) {
              final pj = items[i];
              return AppCard(
                margin: const EdgeInsets.only(bottom: Space.md),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text('${pj.nombreEstudiante} · ${pj.nombreModulo}',
                        style: const TextStyle(fontWeight: FontWeight.w600)),
                    const SizedBox(height: 4),
                    Text(pj.fecha,
                        style: Theme.of(context).textTheme.bodySmall),
                    const SizedBox(height: Space.sm),
                    Text(pj.motivo),
                    if (pj.archivoUrl != null) ...[
                      const SizedBox(height: Space.sm),
                      InkWell(
                        onTap: () => _openJustificante(context, pj.archivoUrl!),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(Icons.attach_file_rounded,
                                size: 16,
                                color: Theme.of(context).colorScheme.primary),
                            const SizedBox(width: 4),
                            Text(
                              'Ver justificante',
                              style: TextStyle(
                                fontSize: 13,
                                fontWeight: FontWeight.w600,
                                color: Theme.of(context).colorScheme.primary,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                    const SizedBox(height: Space.md),
                    Row(
                      children: [
                        Expanded(
                          child: OutlinedButton(
                            onPressed: () => _resolve(context, ref, pj, false),
                            style: OutlinedButton.styleFrom(
                                foregroundColor: AppColors.rojoLight),
                            child: const Text('Rechazar'),
                          ),
                        ),
                        const SizedBox(width: Space.sm),
                        Expanded(
                          child: FilledButton(
                            onPressed: () => _resolve(context, ref, pj, true),
                            child: const Text('Aprobar'),
                          ),
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
