import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';

import '../../../core/auth/auth_state.dart';
import '../../../core/auth/session.dart';
import '../../../core/widgets/async_view.dart';
import '../data/attendance_repository.dart';
import 'mark_attendance_screen.dart';

const _estadoColors = {
  'presente': Color(0xFF10B981),
  'ausente': Color(0xFFEF4444),
  'retraso': Color(0xFFF59E0B),
  'justificado': Color(0xFF3B82F6),
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
              Tab(text: 'Pasar lista', icon: Icon(Icons.checklist_rounded)),
              Tab(text: 'Justificaciones', icon: Icon(Icons.fact_check_outlined)),
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

class _MyAttendanceList extends ConsumerWidget {
  const _MyAttendanceList();

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final attendanceAsync = ref.watch(attendanceMineProvider);
    return Scaffold(
      appBar: AppBar(title: const Text('Asistencias')),
      body: AsyncView<List<AttendanceRecord>>(
        value: attendanceAsync,
        onRetry: () => ref.invalidate(attendanceMineProvider),
        data: (context, records) {
          if (records.isEmpty) {
            return const EmptyState(icon: Icons.event_available_outlined, title: 'Sin registros de asistencia');
          }
          return RefreshIndicator(
            onRefresh: () async => ref.invalidate(attendanceMineProvider),
            child: ListView.builder(
              padding: const EdgeInsets.all(16),
              itemCount: records.length,
              itemBuilder: (context, i) => _AttendanceCard(record: records[i]),
            ),
          );
        },
      ),
    );
  }
}

class _AttendanceCard extends ConsumerWidget {
  const _AttendanceCard({required this.record});
  final AttendanceRecord record;

  Future<void> _openJustifySheet(BuildContext context, WidgetRef ref) async {
    final controller = TextEditingController();
    final sent = await showModalBottomSheet<bool>(
      context: context,
      isScrollControlled: true,
      builder: (ctx) => Padding(
        padding: EdgeInsets.only(
          left: 16,
          right: 16,
          top: 16,
          bottom: MediaQuery.of(ctx).viewInsets.bottom + 16,
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Text('Justificar falta', style: Theme.of(ctx).textTheme.titleMedium),
            const SizedBox(height: 4),
            Text('${record.nombreModulo} · ${record.fecha}', style: Theme.of(ctx).textTheme.bodySmall),
            const SizedBox(height: 16),
            TextField(
              controller: controller,
              minLines: 2,
              maxLines: 4,
              decoration: const InputDecoration(labelText: 'Motivo', border: OutlineInputBorder()),
            ),
            const SizedBox(height: 16),
            FilledButton(
              onPressed: () async {
                if (controller.text.trim().isEmpty) return;
                try {
                  await ref.read(attendanceRepositoryProvider).justify(
                        idAsistencia: record.id,
                        motivo: controller.text.trim(),
                      );
                  if (ctx.mounted) Navigator.of(ctx).pop(true);
                } catch (_) {
                  if (ctx.mounted) {
                    ScaffoldMessenger.of(ctx)
                        .showSnackBar(const SnackBar(content: Text('No se pudo enviar la justificación.')));
                  }
                }
              },
              child: const Text('Enviar'),
            ),
          ],
        ),
      ),
    );
    if (sent == true) {
      ref.invalidate(attendanceMineProvider);
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Justificación enviada.')));
      }
    }
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final scheme = Theme.of(context).colorScheme;
    final color = _estadoColors[record.estado] ?? scheme.outline;
    final date = DateTime.tryParse(record.fecha);

    return Container(
      margin: const EdgeInsets.only(bottom: 10),
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: scheme.surface,
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: scheme.outlineVariant),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(width: 4, height: 40, decoration: BoxDecoration(color: color, borderRadius: BorderRadius.circular(4))),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(record.nombreModulo, style: const TextStyle(fontWeight: FontWeight.bold)),
                const SizedBox(height: 2),
                Text(
                  date != null ? DateFormat('d MMM yyyy').format(date) : record.fecha,
                  style: Theme.of(context).textTheme.bodySmall,
                ),
                if (record.justificacion != null) ...[
                  const SizedBox(height: 6),
                  Text(
                    switch (record.justificacion!.estado) {
                      'pendiente' => '⏳ Justificación en revisión',
                      'aprobada' => '✅ Justificación aprobada',
                      'rechazada' => '❌ Rechazada: ${record.justificacion!.motivoRechazo ?? ''}',
                      _ => '',
                    },
                    style: Theme.of(context).textTheme.bodySmall,
                  ),
                ],
                if (record.canJustify) ...[
                  const SizedBox(height: 8),
                  OutlinedButton(
                    onPressed: () => _openJustifySheet(context, ref),
                    child: const Text('Justificar'),
                  ),
                ],
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
            decoration: BoxDecoration(color: color.withValues(alpha: 0.15), borderRadius: BorderRadius.circular(20)),
            child: Text(
              _estadoLabels[record.estado] ?? record.estado,
              style: TextStyle(color: color, fontWeight: FontWeight.bold, fontSize: 12),
            ),
          ),
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
            padding: const EdgeInsets.all(16),
            itemCount: items.length,
            itemBuilder: (context, i) {
              final pj = items[i];
              return Card(
                margin: const EdgeInsets.only(bottom: 10),
                child: Padding(
                  padding: const EdgeInsets.all(14),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('${pj.nombreEstudiante} · ${pj.nombreModulo}',
                          style: const TextStyle(fontWeight: FontWeight.bold)),
                      const SizedBox(height: 4),
                      Text(pj.fecha, style: Theme.of(context).textTheme.bodySmall),
                      const SizedBox(height: 8),
                      Text(pj.motivo),
                      const SizedBox(height: 10),
                      Row(
                        children: [
                          Expanded(
                            child: OutlinedButton(
                              onPressed: () => _resolve(context, ref, pj, false),
                              style: OutlinedButton.styleFrom(foregroundColor: const Color(0xFFEF4444)),
                              child: const Text('Rechazar'),
                            ),
                          ),
                          const SizedBox(width: 8),
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
                ),
              );
            },
          ),
        );
      },
    );
  }
}
