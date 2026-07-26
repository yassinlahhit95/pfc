import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../core/auth/auth_state.dart';
import '../../../core/auth/session.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/premium.dart';
import '../data/classroom_repository.dart';

class ModuleDetailScreen extends ConsumerWidget {
  const ModuleDetailScreen({super.key, required this.module});
  final ClassroomModule module;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final role = ref.watch(sessionControllerProvider).valueOrNull?.role;
    final isProfesor = role == UserRole.profesor;

    return DefaultTabController(
      length: 2,
      child: Scaffold(
        appBar: AppBar(
          title: Text(module.nombre),
          bottom: const TabBar(tabs: [
            Tab(text: 'Archivos'),
            Tab(text: 'Tareas'),
          ]),
        ),
        body: TabBarView(
          children: [
            _FilesTab(idModulo: module.id, canFavorite: role == UserRole.estudiante),
            _TasksTab(idModulo: module.id, isProfesor: isProfesor),
          ],
        ),
      ),
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
                        padding: const EdgeInsets.symmetric(horizontal: Space.xl, vertical: Space.md),
                        child: Row(
                          children: [
                            Icon(Icons.folder_outlined, size: 21, color: scheme.onSurfaceVariant),
                            const SizedBox(width: Space.md),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(f.nombre, style: const TextStyle(fontWeight: FontWeight.w500)),
                                  Text('${f.totalArchivos} archivo(s)', style: Theme.of(context).textTheme.bodySmall),
                                ],
                              ),
                            ),
                            Icon(Icons.chevron_right_rounded, size: 20, color: scheme.onSurfaceVariant.withValues(alpha: 0.6)),
                          ],
                        ),
                      ),
                    ),
                  )),
            if (folders.isNotEmpty) Divider(height: 1, indent: Space.xl, color: scheme.outlineVariant),
            Expanded(child: _FileList(idModulo: widget.idModulo, canFavorite: widget.canFavorite)),
          ],
        );
      },
    );
  }
}

final _foldersProvider = FutureProvider.autoDispose.family<List<ClassroomFolder>, int>(
  (ref, idModulo) => ref.read(classroomRepositoryProvider).fetchFolders(idModulo),
);

final _filesProvider = FutureProvider.autoDispose.family<List<ClassroomFile>, (int, int?)>(
  (ref, key) => ref.read(classroomRepositoryProvider).fetchFiles(key.$1, idCarpeta: key.$2),
);

class _FileList extends ConsumerWidget {
  const _FileList({required this.idModulo, this.idCarpeta, this.header, required this.canFavorite});
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
          return const EmptyState(icon: Icons.insert_drive_file_outlined, title: 'Sin archivos');
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
            for (final f in files) _FileTile(file: f, filesKey: key, canFavorite: canFavorite),
          ],
        );
      },
    );
  }
}

class _FileTile extends ConsumerWidget {
  const _FileTile({required this.file, required this.filesKey, required this.canFavorite});
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
      contentPadding: const EdgeInsets.symmetric(horizontal: Space.xl, vertical: 2),
      leading: Icon(_icon, size: 21, color: scheme.onSurfaceVariant),
      title: Text(file.nombreOriginal, style: const TextStyle(fontWeight: FontWeight.w500)),
      subtitle: Text('${file.humanSize} · ${file.nombreProfesor}'),
      trailing: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          if (canFavorite)
            IconButton(
              icon: Icon(
                file.esFavorito ? Icons.star_rounded : Icons.star_outline_rounded,
                size: 20,
                color: file.esFavorito ? Colors.amber.shade700 : scheme.onSurfaceVariant,
              ),
              onPressed: () async {
                try {
                  await ref.read(classroomRepositoryProvider).toggleFavorite(file.id);
                  ref.invalidate(_filesProvider(filesKey));
                } catch (_) {
                  if (context.mounted) {
                    ScaffoldMessenger.of(context)
                        .showSnackBar(const SnackBar(content: Text('No se pudo actualizar el favorito.')));
                  }
                }
              },
            ),
          Icon(Icons.file_download_outlined, size: 20, color: scheme.onSurfaceVariant),
        ],
      ),
      onTap: () async {
        final url = ref.read(classroomRepositoryProvider).downloadUrl(file.id);
        final uri = Uri.parse(url);
        final ok = await launchUrl(uri, mode: LaunchMode.externalApplication);
        if (!ok && context.mounted) {
          ScaffoldMessenger.of(context)
              .showSnackBar(const SnackBar(content: Text('No se pudo abrir el archivo.')));
        }
      },
    );
  }
}

// ── Tareas ──────────────────────────────────────────────────────────────

final _tasksProvider = FutureProvider.autoDispose.family<List<ClassroomTask>, int>(
  (ref, idModulo) => ref.read(classroomRepositoryProvider).fetchTasks(idModulo),
);

class _TasksTab extends ConsumerWidget {
  const _TasksTab({required this.idModulo, required this.isProfesor});
  final int idModulo;
  final bool isProfesor;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final tasksAsync = ref.watch(_tasksProvider(idModulo));
    return AsyncView<List<ClassroomTask>>(
      value: tasksAsync,
      onRetry: () => ref.invalidate(_tasksProvider(idModulo)),
      data: (context, tasks) {
        if (tasks.isEmpty) {
          return EmptyState(
            icon: Icons.assignment_outlined,
            title: isProfesor ? 'Sin tareas creadas' : 'Sin tareas publicadas',
          );
        }
        return RefreshIndicator(
          onRefresh: () async => ref.invalidate(_tasksProvider(idModulo)),
          child: ListView.builder(
            padding: const EdgeInsets.fromLTRB(Space.xl, Space.lg, Space.xl, Space.xxxl),
            itemCount: tasks.length,
            itemBuilder: (context, i) => _TaskCard(
              task: tasks[i],
              isProfesor: isProfesor,
              onChanged: () => ref.invalidate(_tasksProvider(idModulo)),
            ),
          ),
        );
      },
    );
  }
}

class _TaskCard extends ConsumerWidget {
  const _TaskCard({required this.task, required this.isProfesor, required this.onChanged});
  final ClassroomTask task;
  final bool isProfesor;
  final VoidCallback onChanged;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final scheme = Theme.of(context).colorScheme;
    final graded = task.estado == 'corregida';
    final gradeColor = scheme.brightness == Brightness.dark ? AppColors.verdeDark : AppColors.verdeLight;
    final date = DateTime.tryParse(task.fechaCreacion.replaceFirst(' ', 'T'));

    return AppCard(
      margin: const EdgeInsets.only(bottom: Space.md),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Expanded(
                child: Text(task.titulo, style: Theme.of(context).textTheme.titleSmall),
              ),
              if (!isProfesor && task.nota != null) StatusPill(label: task.nota!, color: graded ? gradeColor : scheme.onSurfaceVariant),
              if (isProfesor && !task.publicado) StatusPill(label: 'Borrador', color: scheme.onSurfaceVariant),
            ],
          ),
          const SizedBox(height: Space.sm),
          Text(task.descripcion, style: Theme.of(context).textTheme.bodyMedium),
          const SizedBox(height: Space.sm),
          Text(
            '${task.nombreProfesor}${date != null ? ' · ${DateFormat('d MMM yyyy').format(date)}' : ''}',
            style: Theme.of(context).textTheme.bodySmall,
          ),
          if (task.archivoAdjunto != null) ...[
            const SizedBox(height: Space.sm),
            InkWell(
              borderRadius: BorderRadius.circular(Radii.sm),
              onTap: () async {
                final url = ref.read(classroomRepositoryProvider).taskAttachmentUrl(task.id);
                final ok = await launchUrl(Uri.parse(url), mode: LaunchMode.externalApplication);
                if (!ok && context.mounted) {
                  ScaffoldMessenger.of(context)
                      .showSnackBar(const SnackBar(content: Text('No se pudo abrir el archivo.')));
                }
              },
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: Space.md, vertical: Space.sm),
                decoration: BoxDecoration(
                  color: scheme.surfaceContainerHighest,
                  borderRadius: BorderRadius.circular(Radii.sm),
                ),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Icon(Icons.attach_file_rounded, size: 16, color: scheme.onSurfaceVariant),
                    const SizedBox(width: Space.xs),
                    Text('Ver archivo adjunto', style: Theme.of(context).textTheme.bodySmall),
                  ],
                ),
              ),
            ),
          ],
          if (!isProfesor && task.comentario != null && task.comentario!.isNotEmpty) ...[
            const SizedBox(height: Space.sm),
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(Space.md),
              decoration: BoxDecoration(
                color: scheme.surfaceContainerHighest,
                borderRadius: BorderRadius.circular(Radii.sm),
              ),
              child: Text(task.comentario!, style: Theme.of(context).textTheme.bodySmall),
            ),
          ],
          const SizedBox(height: Space.md),
          if (isProfesor) ...[
            Row(
              children: [
                Expanded(
                  child: Text(
                    '${task.totalCorregidas}/${task.totalEntregas} corregidas',
                    style: Theme.of(context).textTheme.bodySmall,
                  ),
                ),
                Switch(
                  value: task.publicado,
                  onChanged: (_) async {
                    try {
                      await ref.read(classroomRepositoryProvider).togglePublish(task.id);
                      onChanged();
                    } catch (_) {
                      if (context.mounted) {
                        ScaffoldMessenger.of(context)
                            .showSnackBar(const SnackBar(content: Text('No se pudo cambiar el estado.')));
                      }
                    }
                  },
                ),
              ],
            ),
            OutlinedButton(
              onPressed: () => Navigator.of(context).push(
                MaterialPageRoute(builder: (_) => _SubmissionsScreen(task: task)),
              ),
              child: const Text('Ver entregas'),
            ),
          ] else
            OutlinedButton(
              onPressed: task.publicado
                  ? () async {
                      final bool? sent;
                      if (task.estado != null) {
                        // Ya hay una entrega: mostrarla primero. Antes esto
                        // abría siempre el formulario de envío en blanco, sin
                        // cargar nunca lo ya enviado — el estudiante no tenía
                        // forma de ver su propia entrega, solo de
                        // sobrescribirla a ciegas.
                        sent = await showModalBottomSheet<bool>(
                          context: context,
                          isScrollControlled: true,
                          backgroundColor: Colors.transparent,
                          builder: (_) => _ViewSubmissionSheet(task: task),
                        );
                      } else {
                        sent = await showModalBottomSheet<bool>(
                          context: context,
                          isScrollControlled: true,
                          backgroundColor: Colors.transparent,
                          builder: (_) => _SubmitSheet(task: task),
                        );
                      }
                      if (sent == true) onChanged();
                    }
                  : null,
              child: Text(task.estado != null ? 'Ver / actualizar entrega' : 'Enviar entrega'),
            ),
        ],
      ),
    );
  }
}

/// Shared bottom-sheet chrome — rounded top, drag handle.
class _Sheet extends StatelessWidget {
  const _Sheet({required this.child});
  final Widget child;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Container(
      padding: const EdgeInsets.fromLTRB(Space.xl, Space.md, Space.xl, Space.xl),
      decoration: BoxDecoration(
        color: scheme.surface,
        borderRadius: const BorderRadius.vertical(top: Radius.circular(Radii.xl)),
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            width: 36,
            height: 4,
            margin: const EdgeInsets.only(bottom: Space.lg),
            decoration: BoxDecoration(color: scheme.outlineVariant, borderRadius: BorderRadius.circular(Radii.pill)),
          ),
          child,
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
  late final _controller = TextEditingController(text: widget.initialRespuesta ?? '');
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
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('No se pudo enviar la entrega.')));
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return _Sheet(
      child: Padding(
        padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Text(widget.initialRespuesta != null ? 'Actualizar entrega' : 'Enviar entrega',
                style: Theme.of(context).textTheme.titleMedium),
            const SizedBox(height: 4),
            Text(widget.task.titulo, style: Theme.of(context).textTheme.bodySmall),
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
              decoration: const InputDecoration(labelText: 'Respuesta (opcional si adjuntas un archivo)'),
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
                  ? const SizedBox(height: 18, width: 18, child: CircularProgressIndicator(strokeWidth: 2))
                  : Text(widget.initialRespuesta != null ? 'Actualizar' : 'Enviar'),
            ),
          ],
        ),
      ),
    );
  }
}

final _submissionProvider = FutureProvider.autoDispose.family<ClassroomSubmission?, int>(
  (ref, idTarea) => ref.read(classroomRepositoryProvider).fetchSubmission(idTarea),
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
              ? DateTime.tryParse(submission.fechaEntrega!.replaceFirst(' ', 'T'))
              : null;

          return Padding(
            padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                Text('Tu entrega', style: Theme.of(context).textTheme.titleMedium),
                const SizedBox(height: 4),
                Text(
                  date != null ? 'Enviada el ${DateFormat('d MMM yyyy, HH:mm').format(date)}' : task.titulo,
                  style: Theme.of(context).textTheme.bodySmall,
                ),
                const SizedBox(height: Space.xl),
                if (submission.respuesta != null && submission.respuesta!.isNotEmpty) ...[
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
                      final url = ref.read(classroomRepositoryProvider).submissionFileUrl(task.id, kind: 'entrega');
                      await launchUrl(Uri.parse(url), mode: LaunchMode.externalApplication);
                    },
                    icon: const Icon(Icons.attach_file_rounded),
                    label: const Text('Abrir mi archivo adjunto'),
                  ),
                if (graded) ...[
                  const SizedBox(height: Space.lg),
                  Row(
                    children: [
                      Icon(Icons.grade_rounded, size: 20, color: scheme.onSurfaceVariant),
                      const SizedBox(width: Space.sm),
                      Text('Calificación: ${submission.nota ?? '—'}/10', style: Theme.of(context).textTheme.titleSmall),
                    ],
                  ),
                  if (submission.comentarioCalificacion != null && submission.comentarioCalificacion!.isNotEmpty) ...[
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
                        final url = ref.read(classroomRepositoryProvider).submissionFileUrl(task.id, kind: 'correccion');
                        await launchUrl(Uri.parse(url), mode: LaunchMode.externalApplication);
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
                      builder: (_) => _SubmitSheet(task: task, initialRespuesta: submission.respuesta ?? ''),
                    );
                    if (sent == true && context.mounted) Navigator.of(context).pop(true);
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

final _submissionsProvider = FutureProvider.autoDispose.family<List<ClassroomSubmission>, int>(
  (ref, idTarea) => ref.read(classroomRepositoryProvider).fetchSubmissions(idTarea),
);

class _SubmissionsScreen extends ConsumerWidget {
  const _SubmissionsScreen({required this.task});
  final ClassroomTask task;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final submissionsAsync = ref.watch(_submissionsProvider(task.id));
    return Scaffold(
      appBar: AppBar(title: Text(task.titulo)),
      body: AsyncView<List<ClassroomSubmission>>(
        value: submissionsAsync,
        onRetry: () => ref.invalidate(_submissionsProvider(task.id)),
        data: (context, submissions) {
          if (submissions.isEmpty) {
            return const EmptyState(icon: Icons.people_outline_rounded, title: 'Sin estudiantes en este ciclo');
          }
          return RefreshIndicator(
            onRefresh: () async => ref.invalidate(_submissionsProvider(task.id)),
            child: ListView.builder(
              padding: const EdgeInsets.fromLTRB(Space.xl, Space.lg, Space.xl, Space.xxxl),
              itemCount: submissions.length,
              itemBuilder: (context, i) {
                final s = submissions[i];
                final scheme = Theme.of(context).colorScheme;
                final gradeColor = scheme.brightness == Brightness.dark ? AppColors.verdeDark : AppColors.verdeLight;
                return AppCard(
                  margin: const EdgeInsets.only(bottom: Space.md),
                  child: Row(
                    children: [
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(s.nombreEstudiante ?? '', style: const TextStyle(fontWeight: FontWeight.w600)),
                            const SizedBox(height: 2),
                            Text(
                              s.hasSubmitted ? 'Entregado${s.fechaEntrega != null ? ' · ${s.fechaEntrega}' : ''}' : 'Sin entregar',
                              style: Theme.of(context).textTheme.bodySmall,
                            ),
                          ],
                        ),
                      ),
                      if (s.nota != null) StatusPill(label: s.nota!, color: gradeColor),
                      if (s.hasSubmitted) ...[
                        const SizedBox(width: Space.sm),
                        IconButton(
                          icon: const Icon(Icons.grade_outlined),
                          onPressed: () async {
                            final graded = await showDialog<bool>(
                              context: context,
                              builder: (_) => _GradeDialog(submission: s),
                            );
                            if (graded == true) ref.invalidate(_submissionsProvider(task.id));
                          },
                        ),
                      ],
                    ],
                  ),
                );
              },
            ),
          );
        },
      ),
    );
  }
}

class _GradeDialog extends ConsumerStatefulWidget {
  const _GradeDialog({required this.submission});
  final ClassroomSubmission submission;

  @override
  ConsumerState<_GradeDialog> createState() => _GradeDialogState();
}

class _GradeDialogState extends ConsumerState<_GradeDialog> {
  late final _notaController = TextEditingController(text: widget.submission.nota ?? '');
  late final _comentarioController = TextEditingController(text: widget.submission.comentarioCalificacion ?? '');
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
            decoration: const InputDecoration(labelText: 'Comentario (opcional)'),
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
        TextButton(onPressed: () => Navigator.of(context).pop(false), child: const Text('Cancelar')),
        FilledButton(
          onPressed: _saving
              ? null
              : () async {
                  final nota = double.tryParse(_notaController.text.replaceAll(',', '.'));
                  if (nota == null || nota < 0 || nota > 10 || widget.submission.idEntrega == null) return;
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
                      ScaffoldMessenger.of(context)
                          .showSnackBar(const SnackBar(content: Text('No se pudo guardar la calificación.')));
                    }
                  }
                },
          child: _saving
              ? const SizedBox(height: 16, width: 16, child: CircularProgressIndicator(strokeWidth: 2))
              : const Text('Guardar'),
        ),
      ],
    );
  }
}

// ── Sesiones vivas ─────────────────────────────────────────────────────

final _sessionsProvider = FutureProvider.autoDispose.family<List<ClassroomSession>, int>(
  (ref, idModulo) => ref.read(classroomRepositoryProvider).fetchSessions(idModulo),
);

class _SessionsTab extends ConsumerWidget {
  const _SessionsTab({required this.idModulo, required this.moduleName, required this.isProfesor});
  final int idModulo;
  final String moduleName;
  final bool isProfesor;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final sessionsAsync = ref.watch(_sessionsProvider(idModulo));
    return Scaffold(
      body: AsyncView<List<ClassroomSession>>(
        value: sessionsAsync,
        onRetry: () => ref.invalidate(_sessionsProvider(idModulo)),
        data: (context, sessions) {
          if (sessions.isEmpty) {
            return const EmptyState(icon: Icons.video_camera_front_outlined, title: 'Sin sesiones vivas');
          }
          return RefreshIndicator(
            onRefresh: () async => ref.invalidate(_sessionsProvider(idModulo)),
            child: ListView.builder(
              padding: const EdgeInsets.fromLTRB(Space.xl, Space.lg, Space.xl, Space.xxxl),
              itemCount: sessions.length,
              itemBuilder: (context, i) => _SessionCard(session: sessions[i]),
            ),
          );
        },
      ),
      floatingActionButton: isProfesor
          ? FloatingActionButton(
              onPressed: () async {
                final created = await showModalBottomSheet<bool>(
                  context: context,
                  isScrollControlled: true,
                  backgroundColor: Colors.transparent,
                  builder: (_) => _CreateSessionSheet(idModulo: idModulo, moduleName: moduleName),
                );
                if (created == true) ref.invalidate(_sessionsProvider(idModulo));
              },
              child: const Icon(Icons.add_rounded),
            )
          : null,
    );
  }
}

class _SessionCard extends StatelessWidget {
  const _SessionCard({required this.session});
  final ClassroomSession session;

  @override
  Widget build(BuildContext context) {
    final date = DateTime.tryParse(session.fechaSesion);
    return AppCard(
      margin: const EdgeInsets.only(bottom: Space.md),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(session.titulo, style: Theme.of(context).textTheme.titleSmall),
          const SizedBox(height: Space.sm),
          if (session.descripcion != null && session.descripcion!.isNotEmpty)
            Text(session.descripcion!, style: Theme.of(context).textTheme.bodyMedium),
          const SizedBox(height: Space.sm),
          Text(
            '${date != null ? DateFormat('d MMM yyyy').format(date) : session.fechaSesion} · ${session.horaSesion.substring(0, 5)}'
            '${session.plataforma != null && session.plataforma!.isNotEmpty ? ' · ${session.plataforma}' : ''}',
            style: Theme.of(context).textTheme.bodySmall,
          ),
          if (session.enlaceReunion != null && session.enlaceReunion!.isNotEmpty) ...[
            const SizedBox(height: Space.md),
            OutlinedButton.icon(
              icon: const Icon(Icons.videocam_outlined),
              label: const Text('Unirse'),
              onPressed: () async {
                await launchUrl(Uri.parse(session.enlaceReunion!), mode: LaunchMode.externalApplication);
              },
            ),
          ],
        ],
      ),
    );
  }
}

class _CreateSessionSheet extends ConsumerStatefulWidget {
  const _CreateSessionSheet({required this.idModulo, required this.moduleName});
  final int idModulo;
  final String moduleName;

  @override
  ConsumerState<_CreateSessionSheet> createState() => _CreateSessionSheetState();
}

class _CreateSessionSheetState extends ConsumerState<_CreateSessionSheet> {
  final _tituloController = TextEditingController();
  final _descripcionController = TextEditingController();
  final _enlaceController = TextEditingController();
  final _plataformaController = TextEditingController();
  DateTime? _fecha;
  TimeOfDay? _hora;
  bool _saving = false;

  Future<void> _create() async {
    if (_tituloController.text.trim().isEmpty || _fecha == null || _hora == null) return;
    setState(() => _saving = true);
    final fechaStr = DateFormat('yyyy-MM-dd').format(_fecha!);
    final horaStr =
        '${_hora!.hour.toString().padLeft(2, '0')}:${_hora!.minute.toString().padLeft(2, '0')}:00';
    try {
      await ref.read(classroomRepositoryProvider).createSession(
            idModulo: widget.idModulo,
            titulo: _tituloController.text.trim(),
            descripcion: _descripcionController.text.trim(),
            fechaSesion: fechaStr,
            horaSesion: horaStr,
            enlaceReunion: _enlaceController.text.trim(),
            plataforma: _plataformaController.text.trim(),
          );
      if (mounted) Navigator.of(context).pop(true);
    } catch (e) {
      setState(() => _saving = false);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('No se pudo crear la sesión.')));
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return _Sheet(
      child: Padding(
        padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Text('Nueva sesión viva', style: Theme.of(context).textTheme.titleMedium),
            const SizedBox(height: 4),
            Text(widget.moduleName, style: Theme.of(context).textTheme.bodySmall),
            const SizedBox(height: Space.xl),
            TextField(controller: _tituloController, decoration: const InputDecoration(labelText: 'Título')),
            const SizedBox(height: Space.md),
            TextField(
              controller: _descripcionController,
              minLines: 2,
              maxLines: 4,
              decoration: const InputDecoration(labelText: 'Descripción (opcional)'),
            ),
            const SizedBox(height: Space.md),
            Row(
              children: [
                Expanded(
                  child: OutlinedButton(
                    onPressed: () async {
                      final picked = await showDatePicker(
                        context: context,
                        initialDate: DateTime.now(),
                        firstDate: DateTime.now(),
                        lastDate: DateTime.now().add(const Duration(days: 365)),
                      );
                      if (picked != null) setState(() => _fecha = picked);
                    },
                    child: Text(_fecha != null ? DateFormat('d MMM yyyy').format(_fecha!) : 'Fecha'),
                  ),
                ),
                const SizedBox(width: Space.md),
                Expanded(
                  child: OutlinedButton(
                    onPressed: () async {
                      final picked = await showTimePicker(context: context, initialTime: TimeOfDay.now());
                      if (picked != null) setState(() => _hora = picked);
                    },
                    child: Text(_hora != null ? _hora!.format(context) : 'Hora'),
                  ),
                ),
              ],
            ),
            const SizedBox(height: Space.md),
            TextField(
              controller: _enlaceController,
              decoration: const InputDecoration(labelText: 'Enlace de la reunión (opcional)'),
            ),
            const SizedBox(height: Space.md),
            TextField(
              controller: _plataformaController,
              decoration: const InputDecoration(labelText: 'Plataforma (opcional)'),
            ),
            const SizedBox(height: Space.xl),
            FilledButton(
              onPressed: _saving ? null : _create,
              child: _saving
                  ? const SizedBox(height: 18, width: 18, child: CircularProgressIndicator(strokeWidth: 2))
                  : const Text('Crear sesión'),
            ),
          ],
        ),
      ),
    );
  }
}
