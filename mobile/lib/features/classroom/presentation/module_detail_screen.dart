import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../core/auth/auth_state.dart';
import '../../../core/auth/session.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/error_modal.dart';
import '../data/classroom_repository.dart';

class ModuleDetailScreen extends ConsumerWidget {
  const ModuleDetailScreen({super.key, required this.module});
  final ClassroomModule module;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final role = ref.watch(sessionControllerProvider).value?.role;

    // "Sesiones vivas" removed for every role — was the only reason this
    // screen needed a TabBar at all, so it's back to a single body instead
    // of a tab bar with one lonely tab.
    return Scaffold(
      appBar: AppBar(title: Text(module.nombre)),
      body: _FilesTab(
          idModulo: module.id, canFavorite: role == UserRole.estudiante),
    );
  }
}

// ── Archivos ────────────────────────────────────────────────────────────

class _FilesTab extends ConsumerStatefulWidget {
  const _FilesTab({required this.idModulo, required this.canFavorite});
  final int idModulo;
  final bool canFavorite;

  @override
  ConsumerState<_FilesTab> createState() => _FilesTabState();
}

class _FilesTabState extends ConsumerState<_FilesTab> {
  int? _openFolderId;
  String? _openFolderName;

  @override
  Widget build(BuildContext context) {
    if (_openFolderId != null) {
      return _FileList(
        idModulo: widget.idModulo,
        idCarpeta: _openFolderId,
        canFavorite: widget.canFavorite,
        header: ListTile(
          leading: const Icon(Icons.arrow_back),
          title: Text(_openFolderName ?? ''),
          onTap: () => setState(() {
            _openFolderId = null;
            _openFolderName = null;
          }),
        ),
      );
    }

    final foldersAsync = ref.watch(_foldersProvider(widget.idModulo));
    final scheme = Theme.of(context).colorScheme;
    return AsyncView<List<ClassroomFolder>>(
      value: foldersAsync,
      onRetry: () => ref.invalidate(_foldersProvider(widget.idModulo)),
      data: (context, folders) {
        // Expanded only works inside a Flex (Column/Row) — folders render as
        // a small, non-scrolling header block, the file list below owns the
        // actual scrolling via its own ListView (not nested inside one).
        return Column(
          children: [
            if (folders.isNotEmpty)
              ...folders.map((f) => Material(
                    color: Colors.transparent,
                    child: InkWell(
                      onTap: () => setState(() {
                        _openFolderId = f.id;
                        _openFolderName = f.nombre;
                      }),
                      child: Padding(
                        padding: const EdgeInsets.symmetric(
                            horizontal: Space.xl, vertical: Space.md),
                        child: Row(
                          children: [
                            Icon(Icons.folder_outlined,
                                size: 21, color: scheme.onSurfaceVariant),
                            const SizedBox(width: Space.md),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(f.nombre,
                                      style: const TextStyle(
                                          fontWeight: FontWeight.w500)),
                                  Text('${f.totalArchivos} archivo(s)',
                                      style: Theme.of(context)
                                          .textTheme
                                          .bodySmall),
                                ],
                              ),
                            ),
                            Icon(Icons.chevron_right_rounded,
                                size: 20,
                                color: scheme.onSurfaceVariant
                                    .withValues(alpha: 0.6)),
                          ],
                        ),
                      ),
                    ),
                  )),
            if (folders.isNotEmpty)
              Divider(
                  height: 1, indent: Space.xl, color: scheme.outlineVariant),
            Expanded(
                child: _FileList(
                    idModulo: widget.idModulo,
                    canFavorite: widget.canFavorite)),
          ],
        );
      },
    );
  }
}

final _foldersProvider =
    FutureProvider.autoDispose.family<List<ClassroomFolder>, int>(
  (ref, idModulo) =>
      ref.read(classroomRepositoryProvider).fetchFolders(idModulo),
);

final _filesProvider =
    FutureProvider.autoDispose.family<List<ClassroomFile>, (int, int?)>(
  (ref, key) => ref
      .read(classroomRepositoryProvider)
      .fetchFiles(key.$1, idCarpeta: key.$2),
);

class _FileList extends ConsumerWidget {
  const _FileList(
      {required this.idModulo,
      this.idCarpeta,
      this.header,
      required this.canFavorite});
  final int idModulo;
  final int? idCarpeta;
  final Widget? header;
  final bool canFavorite;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final key = (idModulo, idCarpeta);
    final filesAsync = ref.watch(_filesProvider(key));
    return AsyncView<List<ClassroomFile>>(
      value: filesAsync,
      onRetry: () => ref.invalidate(_filesProvider(key)),
      data: (context, files) {
        if (files.isEmpty && header == null) {
          return const EmptyState(
              icon: Icons.insert_drive_file_outlined, title: 'Sin archivos');
        }
        return ListView(
          padding: const EdgeInsets.symmetric(vertical: 8),
          children: [
            if (header != null) header!,
            if (files.isEmpty)
              const Padding(
                padding: EdgeInsets.all(24),
                child: Center(child: Text('Sin archivos en esta carpeta')),
              ),
            for (final f in files)
              _FileTile(file: f, filesKey: key, canFavorite: canFavorite),
          ],
        );
      },
    );
  }
}

class _FileTile extends ConsumerWidget {
  const _FileTile(
      {required this.file, required this.filesKey, required this.canFavorite});
  final ClassroomFile file;
  final (int, int?) filesKey;
  final bool canFavorite;

  IconData get _icon => switch (file.extension.toLowerCase()) {
        'pdf' => Icons.picture_as_pdf_outlined,
        'doc' || 'docx' => Icons.description_outlined,
        'xls' || 'xlsx' || 'csv' => Icons.table_chart_outlined,
        'ppt' || 'pptx' => Icons.slideshow_outlined,
        'jpg' || 'jpeg' || 'png' || 'gif' || 'webp' => Icons.image_outlined,
        'zip' || 'rar' => Icons.folder_zip_outlined,
        _ => Icons.insert_drive_file_outlined,
      };

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final scheme = Theme.of(context).colorScheme;
    return ListTile(
      contentPadding:
          const EdgeInsets.symmetric(horizontal: Space.xl, vertical: 2),
      leading: Icon(_icon, size: 21, color: scheme.onSurfaceVariant),
      title: Text(file.nombreOriginal,
          style: const TextStyle(fontWeight: FontWeight.w500)),
      subtitle: Text('${file.humanSize} · ${file.nombreProfesor}'),
      trailing: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          if (canFavorite)
            IconButton(
              icon: Icon(
                file.esFavorito
                    ? Icons.star_rounded
                    : Icons.star_outline_rounded,
                size: 20,
                color: file.esFavorito
                    ? Colors.amber.shade700
                    : scheme.onSurfaceVariant,
              ),
              onPressed: () async {
                try {
                  await ref
                      .read(classroomRepositoryProvider)
                      .toggleFavorite(file.id);
                  ref.invalidate(_filesProvider(filesKey));
                } catch (_) {
                  if (context.mounted) {
                    await showErrorAlert(
                        context, 'No se pudo actualizar el favorito.');
                  }
                }
              },
            ),
          Icon(Icons.file_download_outlined,
              size: 20, color: scheme.onSurfaceVariant),
        ],
      ),
      onTap: () async {
        final url = ref.read(classroomRepositoryProvider).downloadUrl(file.id);
        final uri = Uri.parse(url);
        final ok = await launchUrl(uri, mode: LaunchMode.externalApplication);
        if (!ok && context.mounted) {
          await showErrorAlert(context, 'No se pudo abrir el archivo.');
        }
      },
    );
  }
}

// ── Helper Tareas Global ──────────────────────────────────────────────────

Future<bool?> showTaskDetailSheet(
  BuildContext context, {
  required ClassroomTask task,
  required bool isProfesor,
  VoidCallback? onSubmitted,
}) async {
  if (isProfesor) {
    if (context.mounted) {
      await showErrorAlert(context,
          'Las entregas y calificaciones se gestionan desde la versión web.');
    }
    return null;
  } else {
    if (task.estado != null) {
      return showModalBottomSheet<bool>(
        context: context,
        isScrollControlled: true,
        backgroundColor: Colors.transparent,
        builder: (_) => _ViewSubmissionSheet(task: task),
      );
    } else {
      return showModalBottomSheet<bool>(
        context: context,
        isScrollControlled: true,
        backgroundColor: Colors.transparent,
        builder: (_) => _SubmitSheet(task: task),
      );
    }
  }
}

/// Shared bottom-sheet chrome — rounded top, drag handle, scrollable body.
/// The scroll view matters once a task's full description (previously
/// nowhere in this sheet at all, see _SubmitSheet/_ViewSubmissionSheet) is
/// long enough to push the submit button off-screen on a small device.
class _Sheet extends StatelessWidget {
  const _Sheet({required this.child});
  final Widget child;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Container(
      padding:
          const EdgeInsets.fromLTRB(Space.xl, Space.md, Space.xl, Space.xl),
      constraints: BoxConstraints(
        maxHeight: MediaQuery.of(context).size.height * 0.88,
      ),
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
          Flexible(child: SingleChildScrollView(child: child)),
        ],
      ),
    );
  }
}

class _SubmitSheet extends ConsumerStatefulWidget {
  const _SubmitSheet({required this.task, this.initialRespuesta});
  final ClassroomTask task;
  final String? initialRespuesta;

  @override
  ConsumerState<_SubmitSheet> createState() => _SubmitSheetState();
}

class _SubmitSheetState extends ConsumerState<_SubmitSheet> {
  late final _controller =
      TextEditingController(text: widget.initialRespuesta ?? '');
  PlatformFile? _picked;
  bool _sending = false;

  Future<void> _pickFile() async {
    final result = await FilePicker.platform.pickFiles(
      type: FileType.custom,
      allowedExtensions: ['pdf', 'docx', 'txt'],
    );
    if (result != null && result.files.isNotEmpty) {
      setState(() => _picked = result.files.single);
    }
  }

  Future<void> _submit() async {
    if (_controller.text.trim().isEmpty && _picked == null) return;
    setState(() => _sending = true);
    try {
      await ref.read(classroomRepositoryProvider).submit(
            idTarea: widget.task.id,
            respuesta: _controller.text.trim(),
            filePath: _picked?.path,
            fileName: _picked?.name,
          );
      if (mounted) Navigator.of(context).pop(true);
    } catch (_) {
      setState(() => _sending = false);
      if (mounted) {
        await showErrorAlert(context, 'No se pudo enviar la entrega.');
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return _Sheet(
      child: Padding(
        padding:
            EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Text(
                widget.initialRespuesta != null
                    ? 'Actualizar entrega'
                    : 'Enviar entrega',
                style: Theme.of(context).textTheme.titleMedium),
            const SizedBox(height: 4),
            Text(widget.task.titulo,
                style: Theme.of(context).textTheme.bodySmall),
            // Enunciado completo del profesor — antes no aparecía en ningún
            // sitio de este sheet (solo un resumen de 2 líneas en la tarjeta
            // de la lista), así que si la tarea no llevaba archivo adjunto el
            // estudiante no tenía forma de leer las instrucciones completas.
            if (widget.task.descripcion.isNotEmpty) ...[
              const SizedBox(height: Space.md),
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(Space.md),
                decoration: BoxDecoration(
                  color: Theme.of(context).colorScheme.surfaceContainerHighest,
                  borderRadius: BorderRadius.circular(Radii.md),
                ),
                child: Text(widget.task.descripcion,
                    style: Theme.of(context).textTheme.bodyMedium),
              ),
            ],
            if (widget.task.archivoAdjunto != null) ...[
              const SizedBox(height: Space.sm),
              Align(
                alignment: Alignment.centerLeft,
                child: OutlinedButton.icon(
                  onPressed: () async {
                    final url = ref
                        .read(classroomRepositoryProvider)
                        .taskAttachmentUrl(widget.task.id);
                    await launchUrl(Uri.parse(url),
                        mode: LaunchMode.externalApplication);
                  },
                  icon: const Icon(Icons.download_rounded, size: 18),
                  label: const Text('Descargar adjunto del profesor'),
                  style: OutlinedButton.styleFrom(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                  ),
                ),
              ),
            ],
            if (widget.initialRespuesta != null) ...[
              const SizedBox(height: Space.sm),
              Text(
                'Si adjuntas un nuevo archivo, sustituirá al anterior. La versión previa queda guardada en el historial.',
                style: Theme.of(context).textTheme.bodySmall,
              ),
            ],
            const SizedBox(height: Space.xl),
            TextField(
              controller: _controller,
              minLines: 3,
              maxLines: 6,
              decoration: const InputDecoration(
                  labelText: 'Respuesta (opcional si adjuntas un archivo)'),
            ),
            const SizedBox(height: Space.md),
            OutlinedButton.icon(
              onPressed: _pickFile,
              icon: const Icon(Icons.attach_file_rounded),
              label: Text(_picked?.name ?? 'Adjuntar archivo (PDF, DOCX, TXT)'),
            ),
            const SizedBox(height: Space.xl),
            FilledButton(
              onPressed: _sending ? null : _submit,
              child: _sending
                  ? const SizedBox(
                      height: 18,
                      width: 18,
                      child: CircularProgressIndicator(strokeWidth: 2))
                  : Text(widget.initialRespuesta != null
                      ? 'Actualizar'
                      : 'Enviar'),
            ),
          ],
        ),
      ),
    );
  }
}

final _submissionProvider =
    FutureProvider.autoDispose.family<ClassroomSubmission?, int>(
  (ref, idTarea) =>
      ref.read(classroomRepositoryProvider).fetchSubmission(idTarea),
);

/// Shows the estudiante's own already-sent entrega — response text, attached
/// file, and grade/feedback once corrected — with an "Actualizar entrega"
/// action that reopens [_SubmitSheet] pre-filled. Reached from the same
/// button that used to jump straight to a blank _SubmitSheet even when a
/// submission already existed, so there was no way to actually see it.
class _ViewSubmissionSheet extends ConsumerWidget {
  const _ViewSubmissionSheet({required this.task});
  final ClassroomTask task;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final submissionAsync = ref.watch(_submissionProvider(task.id));
    final scheme = Theme.of(context).colorScheme;

    return _Sheet(
      child: submissionAsync.when(
        loading: () => const Padding(
          padding: EdgeInsets.symmetric(vertical: Space.xxxl),
          child: Center(child: CircularProgressIndicator(strokeWidth: 2.4)),
        ),
        error: (_, __) => Padding(
          padding: const EdgeInsets.symmetric(vertical: Space.xl),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const Text('No se pudo cargar tu entrega.'),
              const SizedBox(height: Space.md),
              OutlinedButton(
                onPressed: () => ref.invalidate(_submissionProvider(task.id)),
                child: const Text('Reintentar'),
              ),
            ],
          ),
        ),
        data: (submission) {
          if (submission == null) {
            return const Padding(
              padding: EdgeInsets.symmetric(vertical: Space.xl),
              child: Text('No se encontró tu entrega.'),
            );
          }
          final graded = submission.estado == 'corregida';
          final date = submission.fechaEntrega != null
              ? DateTime.tryParse(
                  submission.fechaEntrega!.replaceFirst(' ', 'T'))
              : null;

          return Padding(
            padding: EdgeInsets.only(
                bottom: MediaQuery.of(context).viewInsets.bottom),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                Text('Tu entrega',
                    style: Theme.of(context).textTheme.titleMedium),
                const SizedBox(height: 4),
                Text(
                  date != null
                      ? 'Enviada el ${DateFormat('d MMM yyyy, HH:mm').format(date)}'
                      : task.titulo,
                  style: Theme.of(context).textTheme.bodySmall,
                ),
                if (task.descripcion.isNotEmpty) ...[
                  const SizedBox(height: Space.md),
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(Space.md),
                    decoration: BoxDecoration(
                      color: Theme.of(context)
                          .colorScheme
                          .surfaceContainerHighest,
                      borderRadius: BorderRadius.circular(Radii.md),
                    ),
                    child: Text(task.descripcion,
                        style: Theme.of(context).textTheme.bodyMedium),
                  ),
                ],
                const SizedBox(height: Space.xl),
                if (task.archivoAdjunto != null) ...[
                  Align(
                    alignment: Alignment.centerLeft,
                    child: OutlinedButton.icon(
                      onPressed: () async {
                        final url = ref
                            .read(classroomRepositoryProvider)
                            .taskAttachmentUrl(task.id);
                        await launchUrl(Uri.parse(url),
                            mode: LaunchMode.externalApplication);
                      },
                      icon: const Icon(Icons.download_rounded, size: 18),
                      label: const Text('Descargar adjunto del profesor'),
                      style: OutlinedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                      ),
                    ),
                  ),
                  const SizedBox(height: Space.md),
                ],
                if (submission.respuesta != null &&
                    submission.respuesta!.isNotEmpty) ...[
                  Container(
                    width: double.infinity,
                    padding: const EdgeInsets.all(Space.md),
                    decoration: BoxDecoration(
                      color: scheme.surfaceContainerHighest,
                      borderRadius: BorderRadius.circular(Radii.sm),
                    ),
                    child: Text(submission.respuesta!),
                  ),
                  const SizedBox(height: Space.md),
                ],
                if (submission.archivoEntrega != null)
                  OutlinedButton.icon(
                    onPressed: () async {
                      final url = ref
                          .read(classroomRepositoryProvider)
                          .submissionFileUrl(task.id, kind: 'entrega');
                      await launchUrl(Uri.parse(url),
                          mode: LaunchMode.externalApplication);
                    },
                    icon: const Icon(Icons.attach_file_rounded),
                    label: const Text('Abrir mi archivo adjunto'),
                  ),
                if (graded) ...[
                  const SizedBox(height: Space.lg),
                  Row(
                    children: [
                      Icon(Icons.grade_rounded,
                          size: 20, color: scheme.onSurfaceVariant),
                      const SizedBox(width: Space.sm),
                      Text('Calificación: ${submission.nota ?? '—'}/10',
                          style: Theme.of(context).textTheme.titleSmall),
                    ],
                  ),
                  if (submission.comentarioCalificacion != null &&
                      submission.comentarioCalificacion!.isNotEmpty) ...[
                    const SizedBox(height: Space.sm),
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.all(Space.md),
                      decoration: BoxDecoration(
                        color: scheme.surfaceContainerHighest,
                        borderRadius: BorderRadius.circular(Radii.sm),
                      ),
                      child: Text(submission.comentarioCalificacion!),
                    ),
                  ],
                  if (submission.archivoCorreccion != null) ...[
                    const SizedBox(height: Space.sm),
                    OutlinedButton.icon(
                      onPressed: () async {
                        final url = ref
                            .read(classroomRepositoryProvider)
                            .submissionFileUrl(task.id, kind: 'correccion');
                        await launchUrl(Uri.parse(url),
                            mode: LaunchMode.externalApplication);
                      },
                      icon: const Icon(Icons.file_present_rounded),
                      label: const Text('Ver corrección del profesor'),
                    ),
                  ],
                ],
                const SizedBox(height: Space.xl),
                FilledButton(
                  onPressed: () async {
                    final sent = await showModalBottomSheet<bool>(
                      context: context,
                      isScrollControlled: true,
                      backgroundColor: Colors.transparent,
                      builder: (_) => _SubmitSheet(
                          task: task,
                          initialRespuesta: submission.respuesta ?? ''),
                    );
                    if (sent == true && context.mounted)
                      Navigator.of(context).pop(true);
                  },
                  child: const Text('Actualizar entrega'),
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}

// ── Entregas (profesor) ────────────────────────────────────────────────

class _GradeDialog extends ConsumerStatefulWidget {
  const _GradeDialog({required this.submission});
  final ClassroomSubmission submission;

  @override
  ConsumerState<_GradeDialog> createState() => _GradeDialogState();
}

class _GradeDialogState extends ConsumerState<_GradeDialog> {
  late final _notaController =
      TextEditingController(text: widget.submission.nota ?? '');
  late final _comentarioController = TextEditingController(
      text: widget.submission.comentarioCalificacion ?? '');
  PlatformFile? _picked;
  bool _saving = false;

  Future<void> _pickFile() async {
    final result = await FilePicker.platform.pickFiles(
      type: FileType.custom,
      allowedExtensions: ['pdf', 'docx', 'txt'],
    );
    if (result != null && result.files.isNotEmpty) {
      setState(() => _picked = result.files.single);
    }
  }

  @override
  Widget build(BuildContext context) {
    final hasExistingCorrection = widget.submission.archivoCorreccion != null;
    return AlertDialog(
      title: Text('Calificar a ${widget.submission.nombreEstudiante ?? ''}'),
      content: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          TextField(
            controller: _notaController,
            keyboardType: const TextInputType.numberWithOptions(decimal: true),
            decoration: const InputDecoration(labelText: 'Nota (0-10)'),
          ),
          const SizedBox(height: Space.md),
          TextField(
            controller: _comentarioController,
            minLines: 2,
            maxLines: 4,
            decoration:
                const InputDecoration(labelText: 'Comentario (opcional)'),
          ),
          const SizedBox(height: Space.md),
          Align(
            alignment: Alignment.centerLeft,
            child: OutlinedButton.icon(
              onPressed: _pickFile,
              icon: const Icon(Icons.attach_file_rounded, size: 18),
              label: Text(
                _picked?.name ??
                    (hasExistingCorrection
                        ? 'Sustituir archivo de corrección'
                        : 'Adjuntar corrección (opcional)'),
              ),
            ),
          ),
        ],
      ),
      actions: [
        TextButton(
            onPressed: () => Navigator.of(context).pop(false),
            child: const Text('Cancelar')),
        FilledButton(
          onPressed: _saving
              ? null
              : () async {
                  final nota = double.tryParse(
                      _notaController.text.replaceAll(',', '.'));
                  if (nota == null ||
                      nota < 0 ||
                      nota > 10 ||
                      widget.submission.idEntrega == null) return;
                  setState(() => _saving = true);
                  try {
                    await ref.read(classroomRepositoryProvider).grade(
                          idEntrega: widget.submission.idEntrega!,
                          nota: nota,
                          comentario: _comentarioController.text.trim(),
                          correctionFilePath: _picked?.path,
                          correctionFileName: _picked?.name,
                        );
                    if (context.mounted) Navigator.of(context).pop(true);
                  } catch (_) {
                    setState(() => _saving = false);
                    if (context.mounted) {
                      await showErrorAlert(
                          context, 'No se pudo guardar la calificación.');
                    }
                  }
                },
          child: _saving
              ? const SizedBox(
                  height: 16,
                  width: 16,
                  child: CircularProgressIndicator(strokeWidth: 2))
              : const Text('Guardar'),
        ),
      ],
    );
  }
}
