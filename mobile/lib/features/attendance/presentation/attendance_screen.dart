import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../core/auth/auth_state.dart';
import '../../../core/auth/session.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/filter_bar.dart';
import '../../../core/widgets/premium.dart';
import '../data/attendance_repository.dart';
import 'mark_attendance_screen.dart';

Future<void> _openJustificante(BuildContext context, String url) async {
  final ok = await launchUrl(Uri.parse(url), mode: LaunchMode.externalApplication);
  if (!ok && context.mounted) {
    ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('No se pudo abrir el justificante.')));
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

class AttendanceScreen extends ConsumerWidget {
  const AttendanceScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final role = ref.watch(sessionControllerProvider).valueOrNull?.role;

    if (role == UserRole.profesor) {
      return DefaultTabController(
        length: 2,
        child: Scaffold(
          appBar: AppBar(
            title: const Text('Asistencias'),
            bottom: const TabBar(tabs: [
              Tab(text: 'Pasar lista'),
              Tab(text: 'Justificaciones'),
            ]),
          ),
          body: const TabBarView(
            children: [MarkAttendanceScreen(), _PendingJustificationsTab()],
          ),
        ),
      );
    }

    return const _MyAttendanceList();
  }
}

class _MyAttendanceList extends ConsumerStatefulWidget {
  const _MyAttendanceList();

  @override
  ConsumerState<_MyAttendanceList> createState() => _MyAttendanceListState();
}

class _MyAttendanceListState extends ConsumerState<_MyAttendanceList> {
  String? _estado;
  String? _modulo;
  String? _estudiante;

  @override
  Widget build(BuildContext context) {
    final attendanceAsync = ref.watch(attendanceMineProvider);
    return Scaffold(
      appBar: AppBar(title: const Text('Asistencias')),
      body: AsyncView<List<AttendanceRecord>>(
        value: attendanceAsync,
        onRetry: () => ref.invalidate(attendanceMineProvider),
        data: (context, allRecords) {
          if (allRecords.isEmpty) {
            return const EmptyState(icon: Icons.event_available_outlined, title: 'Sin registros de asistencia');
          }

          final modulos = allRecords.map((r) => r.nombreModulo).toSet().toList()..sort();
          final estudiantes = allRecords.map((r) => r.nombreEstudiante).toSet().toList()..sort();
          const estados = ['presente', 'ausente', 'retraso', 'justificado'];

          final records = allRecords.where((r) {
            if (_estado != null && r.estado != _estado) return false;
            if (_modulo != null && r.nombreModulo != _modulo) return false;
            if (_estudiante != null && r.nombreEstudiante != _estudiante) return false;
            return true;
          }).toList();

          return Column(
            children: [
              const SizedBox(height: Space.md),
              FilterBar(children: [
                FilterPill<String>(
                  label: 'Estado',
                  value: _estado,
                  options: [for (final e in estados) (e, _estadoLabels[e] ?? e)],
                  onChanged: (v) => setState(() => _estado = v),
                ),
                FilterPill<String>(
                  label: 'Módulo',
                  value: _modulo,
                  options: [for (final m in modulos) (m, m)],
                  onChanged: (v) => setState(() => _modulo = v),
                ),
                if (estudiantes.length > 1)
                  FilterPill<String>(
                    label: 'Estudiante',
                    value: _estudiante,
                    options: [for (final s in estudiantes) (s, s)],
                    onChanged: (v) => setState(() => _estudiante = v),
                  ),
              ]),
              const SizedBox(height: Space.sm),
              Expanded(
                child: records.isEmpty
                    ? const EmptyState(icon: Icons.filter_alt_off_outlined, title: 'Sin resultados para estos filtros')
                    : RefreshIndicator(
                        onRefresh: () async => ref.invalidate(attendanceMineProvider),
                        child: ListView.builder(
                          padding: const EdgeInsets.fromLTRB(Space.xl, Space.sm, Space.xl, Space.xxxl),
                          itemCount: records.length,
                          itemBuilder: (context, i) => _AttendanceCard(record: records[i]),
                        ),
                      ),
              ),
            ],
          );
        },
      ),
    );
  }
}

class _AttendanceCard extends ConsumerWidget {
  const _AttendanceCard({required this.record});
  final AttendanceRecord record;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final color = _estadoColors[record.estado] ?? Theme.of(context).colorScheme.outline;
    final date = DateTime.tryParse(record.fecha);

    return AppCard(
      margin: const EdgeInsets.only(bottom: Space.md),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(record.nombreModulo, style: const TextStyle(fontWeight: FontWeight.w600)),
                const SizedBox(height: 2),
                Text(
                  [
                    if (date != null) DateFormat('d MMM yyyy').format(date) else record.fecha,
                    if (record.nombreProfesor.isNotEmpty) record.nombreProfesor,
                  ].join(' · '),
                  style: Theme.of(context).textTheme.bodySmall,
                ),
                if (record.justificacion != null) ...[
                  const SizedBox(height: Space.md),
                  _JustificationBox(justificacion: record.justificacion!),
                ],
              ],
            ),
          ),
          const SizedBox(width: Space.sm),
          StatusPill(label: _estadoLabels[record.estado] ?? record.estado, color: color),
        ],
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

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final color = _justEstadoColors[justificacion.estado] ?? scheme.outline;

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(Space.md),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.08),
        borderRadius: BorderRadius.circular(Radii.md),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Expanded(
                child: Text(
                  _justEstadoLabels[justificacion.estado] ?? justificacion.estado,
                  style: TextStyle(fontWeight: FontWeight.w600, fontSize: 13, color: color),
                ),
              ),
              if (justificacion.archivoUrl != null)
                InkWell(
                  onTap: () => _openJustificante(context, justificacion.archivoUrl!),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(Icons.attach_file_rounded, size: 16, color: scheme.primary),
                      const SizedBox(width: 4),
                      Text(
                        'Ver justificante',
                        style: TextStyle(fontSize: 13, fontWeight: FontWeight.w600, color: scheme.primary),
                      ),
                    ],
                  ),
                ),
            ],
          ),
          const SizedBox(height: 4),
          Text(justificacion.motivo, style: Theme.of(context).textTheme.bodySmall),
          if (justificacion.estado == 'rechazada' && (justificacion.motivoRechazo?.isNotEmpty ?? false)) ...[
            const SizedBox(height: 4),
            Text(
              'Motivo del rechazo: ${justificacion.motivoRechazo}',
              style: Theme.of(context).textTheme.bodySmall?.copyWith(color: scheme.onSurfaceVariant),
            ),
          ],
        ],
      ),
    );
  }
}

class _PendingJustificationsTab extends ConsumerWidget {
  const _PendingJustificationsTab();

  Future<void> _resolve(BuildContext context, WidgetRef ref, PendingJustification pj, bool aprobar) async {
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
            TextButton(onPressed: () => Navigator.of(ctx).pop(false), child: const Text('Cancelar')),
            FilledButton(onPressed: () => Navigator.of(ctx).pop(true), child: const Text('Rechazar')),
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
        ScaffoldMessenger.of(context)
            .showSnackBar(SnackBar(content: Text(aprobar ? 'Aprobada.' : 'Rechazada.')));
      }
    } catch (_) {
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('No se pudo resolver.')));
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
          return const EmptyState(icon: Icons.fact_check_outlined, title: 'Sin justificaciones pendientes');
        }
        return RefreshIndicator(
          onRefresh: () async => ref.invalidate(pendingJustificationsProvider),
          child: ListView.builder(
            padding: const EdgeInsets.fromLTRB(Space.xl, Space.lg, Space.xl, Space.xxxl),
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
                    Text(pj.fecha, style: Theme.of(context).textTheme.bodySmall),
                    const SizedBox(height: Space.sm),
                    Text(pj.motivo),
                    if (pj.archivoUrl != null) ...[
                      const SizedBox(height: Space.sm),
                      InkWell(
                        onTap: () => _openJustificante(context, pj.archivoUrl!),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(Icons.attach_file_rounded, size: 16, color: Theme.of(context).colorScheme.primary),
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
                            style: OutlinedButton.styleFrom(foregroundColor: AppColors.rojoLight),
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
