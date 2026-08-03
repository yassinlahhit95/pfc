import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/error_modal.dart';
import '../../../core/widgets/photo_picker_sheet.dart';
import '../data/attendance_repository.dart';

/// Shared bottom-sheet chrome — rounded top, drag handle, consistent
/// padding — instead of each screen wiring up its own raw Padding+Column.
class JustifySheetChrome extends StatelessWidget {
  const JustifySheetChrome({super.key, required this.child});
  final Widget child;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Container(
      padding:
          const EdgeInsets.fromLTRB(Space.xl, Space.md, Space.xl, Space.xl),
      decoration: BoxDecoration(
        color: scheme.surface,
        borderRadius:
            const BorderRadius.vertical(top: Radius.circular(Radii.xl)),
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            width: 36,
            height: 4,
            margin: const EdgeInsets.only(bottom: Space.lg),
            decoration: BoxDecoration(
                color: scheme.outlineVariant,
                borderRadius: BorderRadius.circular(Radii.pill)),
          ),
          child,
        ],
      ),
    );
  }
}

/// Opens the "Justificar falta" bottom sheet (motivo + optional photo) and
/// submits it via [AttendanceRepository.justify]. Used by the student/tutor
/// attendance list and by [StaffJustifyScreen] (profesor/secretaría/director
/// justifying on a student's behalf, which the server auto-approves).
/// Returns true if the justification was sent successfully.
Future<bool> showJustifySheet(
  BuildContext context,
  WidgetRef ref, {
  required int idAsistencia,
  required String subtitulo,
}) async {
  final controller = TextEditingController();
  File? archivo;
  final sent = await showModalBottomSheet<bool>(
    context: context,
    isScrollControlled: true,
    backgroundColor: Colors.transparent,
    builder: (ctx) => StatefulBuilder(
      builder: (ctx, setSheetState) => JustifySheetChrome(
        child: Padding(
          padding:
              EdgeInsets.only(bottom: MediaQuery.of(ctx).viewInsets.bottom),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Text('Justificar falta',
                  style: Theme.of(ctx).textTheme.titleMedium),
              const SizedBox(height: 4),
              Text(subtitulo, style: Theme.of(ctx).textTheme.bodySmall),
              const SizedBox(height: Space.xl),
              TextField(
                controller: controller,
                minLines: 2,
                maxLines: 4,
                decoration: const InputDecoration(labelText: 'Motivo'),
              ),
              const SizedBox(height: Space.md),
              OutlinedButton.icon(
                onPressed: () async {
                  final picked = await pickPhoto(ctx);
                  if (picked != null) setSheetState(() => archivo = picked);
                },
                icon: Icon(archivo == null
                    ? Icons.add_a_photo_outlined
                    : Icons.check_circle_outline),
                label: Text(archivo == null
                    ? 'Adjuntar foto (opcional)'
                    : 'Foto adjuntada'),
              ),
              const SizedBox(height: Space.xl),
              FilledButton(
                onPressed: () async {
                  if (controller.text.trim().isEmpty) return;
                  try {
                    await ref.read(attendanceRepositoryProvider).justify(
                          idAsistencia: idAsistencia,
                          motivo: controller.text.trim(),
                          archivo: archivo,
                        );
                    if (ctx.mounted) Navigator.of(ctx).pop(true);
                  } catch (_) {
                    if (ctx.mounted) {
                      await showErrorAlert(
                          ctx, 'No se pudo enviar la justificación.');
                    }
                  }
                },
                child: const Text('Enviar'),
              ),
            ],
          ),
        ),
      ),
    ),
  );
  return sent == true;
}
