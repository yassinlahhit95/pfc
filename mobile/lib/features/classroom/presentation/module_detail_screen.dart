import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../core/auth/auth_state.dart';
import '../../../core/auth/session.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/app_bottom_sheet.dart';
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
        idModulo: module.id,
        canFavorite: role == UserRole.estudiante,
        canManage: role == UserRole.profesor,
      ),
    );
  }
}

// ── Archivos ────────────────────────────────────────────────────────────

class _FilesTab extends ConsumerStatefulWidget {
  const _FilesTab(
      {required this.idModulo,
      required this.canFavorite,
      required this.canManage});
  final int idModulo;
  final bool canFavorite;
  final bool canManage;

  @override
  ConsumerState<_FilesTab> createState() => _FilesTabState();
}

class _FilesTabState extends ConsumerState<_FilesTab> {
  int? _openFolderId;
  String? _openFolderName;

  Future<void> _openAddMenu() async {
    final choice = await showModalBottomSheet<String>(
      context: context,
      builder: (_) => SafeArea(
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            ListTile(
              leading: const Icon(Icons.cloud_upload_outlined),
              title: const Text('Subir archivo'),
              onTap: () => Navigator.of(context).pop('upload'),
            ),
            ListTile(
              leading: const Icon(Icons.create_new_folder_outlined),
              title: const Text('Nueva carpeta'),
              onTap: () => Navigator.of(context).pop('folder'),
            ),
          ],
        ),
      ),
    );
    if (!mounted || choice == null) return;

    final created = await showModalBottomSheet<bool>(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => choice == 'upload'
          ? _UploadFileSheet(idModulo: widget.idModulo, idCarpeta: _openFolderId)
          : _CreateFolderSheet(idModulo: widget.idModulo, idPadre: _openFolderId),
    );
    if (created == true) {
      ref.invalidate(_foldersProvider(widget.idModulo));
      ref.invalidate(_filesProvider((widget.idModulo, _openFolderId)));
      if (mounted) {
        showSuccessSnack(
            context, choice == 'upload' ? 'Archivo subido.' : 'Carpeta creada.');
      }
    }
  }

  Future<void> _deleteFolder(ClassroomFolder folder) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (_) => AlertDialog(
        title: const Text('Eliminar carpeta'),
        content: Text(
            '¿Eliminar «${folder.nombre}» y todo su contenido? Esta acción no se puede deshacer.'),
        actions: [
          TextButton(
              onPressed: () => Navigator.of(context).pop(false),
              child: const Text('Cancelar')),
          FilledButton.tonal(
              onPressed: () => Navigator.of(context).pop(true),
              child: const Text('Eliminar')),
        ],
      ),
    );
    if (confirm != true) return;
    try {
      await ref.read(classroomRepositoryProvider).deleteFolder(folder.id);
      ref.invalidate(_foldersProvider(widget.idModulo));
      if (mounted) showSuccessSnack(context, 'Carpeta eliminada.');
    } catch (_) {
      if (mounted) {
        await showErrorAlert(context, 'No se pudo eliminar la carpeta.');
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final body = _buildBody(context);
    if (!widget.canManage) return body;
    return Stack(
      children: [
        Positioned.fill(child: body),
        Positioned(
          right: Space.lg,
          bottom: Space.lg,
          child: FloatingActionButton(
            onPressed: _openAddMenu,
            child: const Icon(Icons.add),
          ),
        ),
      ],
    );
  }

  Widget _buildBody(BuildContext context) {
    if (_openFolderId != null) {
      return _FileList(
        idModulo: widget.idModulo,
        idCarpeta: _openFolderId,
        canFavorite: widget.canFavorite,
        canManage: widget.canManage,
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
                            if (widget.canManage)
                              IconButton(
                                icon: Icon(Icons.delete_outline_rounded,
                                    size: 20, color: scheme.onSurfaceVariant),
                                onPressed: () => _deleteFolder(f),
                              )
                            else
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
                    canFavorite: widget.canFavorite,
                    canManage: widget.canManage)),
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
      required this.canFavorite,
      required this.canManage});
  final int idModulo;
  final int? idCarpeta;
  final Widget? header;
  final bool canFavorite;
  final bool canManage;

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
              _FileTile(
                  file: f,
                  filesKey: key,
                  canFavorite: canFavorite,
                  canManage: canManage),
          ],
        );
      },
    );
  }
}

class _FileTile extends ConsumerWidget {
  const _FileTile(
      {required this.file,
      required this.filesKey,
      required this.canFavorite,
      required this.canManage});
  final ClassroomFile file;
  final (int, int?) filesKey;
  final bool canFavorite;
  final bool canManage;

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
          if (canManage)
            IconButton(
              icon: Icon(Icons.delete_outline_rounded,
                  size: 20, color: scheme.onSurfaceVariant),
              onPressed: () async {
                final confirm = await showDialog<bool>(
                  context: context,
                  builder: (_) => AlertDialog(
                    title: const Text('Eliminar archivo'),
                    content: Text(
                        '¿Eliminar «${file.nombreOriginal}»? Esta acción no se puede deshacer.'),
                    actions: [
                      TextButton(
                          onPressed: () => Navigator.of(context).pop(false),
                          child: const Text('Cancelar')),
                      FilledButton.tonal(
                          onPressed: () => Navigator.of(context).pop(true),
                          child: const Text('Eliminar')),
                    ],
                  ),
                );
                if (confirm != true) return;
                try {
                  await ref
                      .read(classroomRepositoryProvider)
                      .deleteFile(file.id);
                  ref.invalidate(_filesProvider(filesKey));
                  ref.invalidate(_foldersProvider(filesKey.$1));
                  if (context.mounted) {
                    showSuccessSnack(context, 'Archivo eliminado.');
                  }
                } catch (_) {
                  if (context.mounted) {
                    await showErrorAlert(
                        context, 'No se pudo eliminar el archivo.');
                  }
                }
              },
            ),
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

class _UploadFileSheet extends ConsumerStatefulWidget {
  const _UploadFileSheet({required this.idModulo, this.idCarpeta});
  final int idModulo;
  final int? idCarpeta;

  @override
  ConsumerState<_UploadFileSheet> createState() => _UploadFileSheetState();
}

class _UploadFileSheetState extends ConsumerState<_UploadFileSheet> {
  final _tituloController = TextEditingController();
  PlatformFile? _picked;
  bool _uploading = false;

  Future<void> _pickFile() async {
    final result = await FilePicker.platform.pickFiles(
      type: FileType.custom,
      allowedExtensions: const [
        'pdf', 'doc', 'docx', 'txt', 'rtf', 'odt', 'xls', 'xlsx', 'ods',
        'csv', 'ppt', 'pptx', 'odp', 'jpg', 'jpeg', 'png', 'gif', 'webp',
        'zip', 'rar',
      ],
    );
    if (result != null && result.files.isNotEmpty) {
      setState(() => _picked = result.files.single);
    }
  }

  Future<void> _upload() async {
    final picked = _picked;
    if (picked?.path == null) return;
    setState(() => _uploading = true);
    try {
      await ref.read(classroomRepositoryProvider).uploadFile(
            idModulo: widget.idModulo,
            idCarpeta: widget.idCarpeta,
            titulo: _tituloController.text.trim(),
            filePath: picked!.path!,
            fileName: picked.name,
          );
      if (mounted) Navigator.of(context).pop(true);
    } catch (_) {
      setState(() => _uploading = false);
      if (mounted) {
        await showErrorAlert(context, 'No se pudo subir el archivo.');
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return AppBottomSheet(
      child: Padding(
        padding:
            EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Text('Subir archivo', style: Theme.of(context).textTheme.titleMedium),
            const SizedBox(height: Space.xl),
            OutlinedButton.icon(
              onPressed: _pickFile,
              icon: const Icon(Icons.attach_file_rounded),
              label: Text(_picked?.name ?? 'Elegir archivo (máx. 20 MB)'),
            ),
            const SizedBox(height: Space.md),
            TextField(
              controller: _tituloController,
              decoration: const InputDecoration(
                  labelText: 'Título (opcional)',
                  helperText: 'Si lo dejas vacío, se conserva el nombre del archivo'),
            ),
            const SizedBox(height: Space.xl),
            FilledButton(
              onPressed: (_picked == null || _uploading) ? null : _upload,
              child: _uploading
                  ? const SizedBox(
                      height: 18,
                      width: 18,
                      child: CircularProgressIndicator(strokeWidth: 2))
                  : const Text('Subir'),
            ),
          ],
        ),
      ),
    );
  }
}

class _CreateFolderSheet extends ConsumerStatefulWidget {
  const _CreateFolderSheet({required this.idModulo, this.idPadre});
  final int idModulo;
  final int? idPadre;

  @override
  ConsumerState<_CreateFolderSheet> createState() => _CreateFolderSheetState();
}

class _CreateFolderSheetState extends ConsumerState<_CreateFolderSheet> {
  final _nombreController = TextEditingController();
  bool _saving = false;

  Future<void> _create() async {
    final nombre = _nombreController.text.trim();
    if (nombre.isEmpty) return;
    setState(() => _saving = true);
    try {
      await ref.read(classroomRepositoryProvider).createFolder(
            idModulo: widget.idModulo,
            nombre: nombre,
            idPadre: widget.idPadre,
          );
      if (mounted) Navigator.of(context).pop(true);
    } catch (_) {
      setState(() => _saving = false);
      if (mounted) {
        await showErrorAlert(context, 'No se pudo crear la carpeta.');
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return AppBottomSheet(
      child: Padding(
        padding:
            EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Text('Nueva carpeta', style: Theme.of(context).textTheme.titleMedium),
            const SizedBox(height: Space.xl),
            TextField(
              controller: _nombreController,
              autofocus: true,
              decoration: const InputDecoration(labelText: 'Nombre *'),
              onSubmitted: (_) => _create(),
            ),
            const SizedBox(height: Space.xl),
            FilledButton(
              onPressed: _saving ? null : _create,
              child: _saving
                  ? const SizedBox(
                      height: 18,
                      width: 18,
                      child: CircularProgressIndicator(strokeWidth: 2))
                  : const Text('Crear'),
            ),
          ],
        ),
      ),
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
    return showModalBottomSheet<bool>(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => _SubmissionsSheet(task: task, onGraded: onSubmitted),
    );
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
    return AppBottomSheet(
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

    return AppBottomSheet(
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
                    if (sent == true && context.mounted) {
                      Navigator.of(context).pop(true);
                    }
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

final _submissionsProvider =
    FutureProvider.autoDispose.family<List<ClassroomSubmission>, int>(
  (ref, idTarea) =>
      ref.read(classroomRepositoryProvider).fetchSubmissions(idTarea),
);

/// Full roster for a task — every student in the ciclo, with or without an
/// entrega — with a "Calificar" action per row. Reached from
/// [showTaskDetailSheet] when a profesor taps a task; previously that just
/// showed an error telling the profesor to use the web, even though the
/// roster fetch (fetchSubmissions) and the grading dialog (_GradeDialog)
/// were already fully built and simply never wired to any UI.
class _SubmissionsSheet extends ConsumerWidget {
  const _SubmissionsSheet({required this.task, this.onGraded});
  final ClassroomTask task;
  final VoidCallback? onGraded;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final submissionsAsync = ref.watch(_submissionsProvider(task.id));
    final scheme = Theme.of(context).colorScheme;

    return AppBottomSheet(
      child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          Text(task.titulo, style: Theme.of(context).textTheme.titleMedium),
          const SizedBox(height: 4),
          Text('${task.totalEntregas} entregas · ${task.totalCorregidas} corregidas',
              style: Theme.of(context).textTheme.bodySmall),
          const SizedBox(height: Space.lg),
          submissionsAsync.when(
            loading: () => const Padding(
              padding: EdgeInsets.symmetric(vertical: Space.xxxl),
              child: Center(child: CircularProgressIndicator(strokeWidth: 2.4)),
            ),
            error: (_, __) => Padding(
              padding: const EdgeInsets.symmetric(vertical: Space.xl),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  const Text('No se pudieron cargar las entregas.'),
                  const SizedBox(height: Space.md),
                  OutlinedButton(
                    onPressed: () =>
                        ref.invalidate(_submissionsProvider(task.id)),
                    child: const Text('Reintentar'),
                  ),
                ],
              ),
            ),
            data: (submissions) {
              if (submissions.isEmpty) {
                return const Padding(
                  padding: EdgeInsets.symmetric(vertical: Space.xl),
                  child: Text('No hay estudiantes matriculados en este ciclo.'),
                );
              }
              return Column(
                children: [
                  for (final s in submissions)
                    Padding(
                      padding: const EdgeInsets.symmetric(vertical: Space.xs),
                      child: Material(
                        color: scheme.surfaceContainerHighest,
                        borderRadius: BorderRadius.circular(Radii.md),
                        child: ListTile(
                          shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(Radii.md)),
                          title: Text(s.nombreEstudiante ?? 'Estudiante'),
                          subtitle: Text(!s.hasSubmitted
                              ? 'Sin entregar'
                              : s.estado == 'corregida'
                                  ? 'Corregida · ${s.nota ?? '—'}/10'
                                  : 'Entregada, pendiente de corregir'),
                          trailing: s.hasSubmitted
                              ? FilledButton.tonal(
                                  onPressed: () async {
                                    final saved = await showDialog<bool>(
                                      context: context,
                                      builder: (_) => _GradeDialog(
                                          idTarea: task.id, submission: s),
                                    );
                                    if (saved == true) {
                                      ref.invalidate(
                                          _submissionsProvider(task.id));
                                      onGraded?.call();
                                      if (context.mounted) {
                                        showSuccessSnack(context,
                                            'Calificación guardada.');
                                      }
                                    }
                                  },
                                  child: Text(s.estado == 'corregida'
                                      ? 'Editar nota'
                                      : 'Calificar'),
                                )
                              : null,
                        ),
                      ),
                    ),
                ],
              );
            },
          ),
        ],
      ),
    );
  }
}

class _GradeDialog extends ConsumerStatefulWidget {
  const _GradeDialog({required this.idTarea, required this.submission});
  final int idTarea;
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
      content: SingleChildScrollView(
        child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          // La respuesta y el archivo entregados por el estudiante — antes el
          // profesor tenía que abrir la web para leerlos antes de calificar,
          // aunque los datos ya llegaban en la propia entrega.
          if (widget.submission.respuesta != null &&
              widget.submission.respuesta!.isNotEmpty) ...[
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(Space.md),
              decoration: BoxDecoration(
                color: Theme.of(context).colorScheme.surfaceContainerHighest,
                borderRadius: BorderRadius.circular(Radii.sm),
              ),
              child: Text(widget.submission.respuesta!),
            ),
            const SizedBox(height: Space.md),
          ],
          if (widget.submission.archivoEntrega != null) ...[
            Align(
              alignment: Alignment.centerLeft,
              child: OutlinedButton.icon(
                onPressed: () async {
                  final url = ref.read(classroomRepositoryProvider).submissionFileUrl(
                      widget.idTarea,
                      kind: 'entrega',
                      idEstudiante: widget.submission.idEstudiante);
                  await launchUrl(Uri.parse(url),
                      mode: LaunchMode.externalApplication);
                },
                icon: const Icon(Icons.attach_file_rounded, size: 18),
                label: const Text('Abrir archivo entregado'),
              ),
            ),
            const SizedBox(height: Space.md),
          ],
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
                      widget.submission.idEntrega == null) {
                    return;
                  }
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
