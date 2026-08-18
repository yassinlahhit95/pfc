import 'package:file_picker/file_picker.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/app_bottom_sheet.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/error_modal.dart';
import '../../../core/auth/auth_state.dart';
import '../../../core/auth/session.dart';
import '../../../core/i18n/translations.dart';
import '../../classroom/data/classroom_repository.dart';
import '../../classroom/presentation/module_detail_screen.dart'; // Para reutilizar _TaskTile o la lógica
import '../data/tareas_repository.dart';

class TareasScreen extends ConsumerStatefulWidget {
  const TareasScreen({super.key});

  @override
  ConsumerState<TareasScreen> createState() => _TareasScreenState();
}

class _TareasScreenState extends ConsumerState<TareasScreen> {
  @override
  Widget build(BuildContext context) {
    final tasksAsync = ref.watch(allTasksProvider);
    final userRole = ref.watch(sessionControllerProvider).value?.role;
    final isProfesor = userRole == UserRole.profesor;
    final t = ref.watch(translationsProvider);

    return Scaffold(
      appBar: AppBar(title: Text(t['title_mis_tareas'] ?? 'Mis Tareas')),
      floatingActionButton: isProfesor
          ? FloatingActionButton(
              onPressed: () async {
                final created = await showModalBottomSheet<bool>(
                  context: context,
                  isScrollControlled: true,
                  backgroundColor: Colors.transparent,
                  builder: (_) => const _TaskFormSheet(),
                );
                if (created == true) {
                  ref.invalidate(allTasksProvider);
                  if (context.mounted) {
                    showSuccessSnack(context, 'Tarea creada correctamente.');
                  }
                }
              },
              child: const Icon(Icons.add),
            )
          : null,
      body: AsyncView<List<ClassroomTask>>(
        value: tasksAsync,
        onRetry: () => ref.invalidate(allTasksProvider),
        data: (context, tasks) {
          if (tasks.isEmpty) {
            return EmptyState(
              icon: Icons.assignment_turned_in_outlined,
              title:
                  isProfesor ? 'No has creado tareas' : 'Sin tareas pendientes',
            );
          }
          return ListView.separated(
            padding: const EdgeInsets.all(Space.md).copyWith(bottom: 88),
            itemCount: tasks.length,
            separatorBuilder: (_, __) => const SizedBox(height: Space.md),
            itemBuilder: (context, index) {
              final task = tasks[index];
              return _GlobalTaskTile(
                task: task,
                isProfesor: isProfesor,
                onPublishToggle: isProfesor
                    ? () async {
                        try {
                          await ref
                              .read(classroomRepositoryProvider)
                              .togglePublish(task.id);
                          ref.invalidate(allTasksProvider);
                        } catch (e) {
                          if (context.mounted) {
                            await showErrorAlert(
                                context, 'Error al cambiar el estado: $e');
                          }
                        }
                      }
                    : null,
                onEdit: isProfesor
                    ? () async {
                        final saved = await showModalBottomSheet<bool>(
                          context: context,
                          isScrollControlled: true,
                          backgroundColor: Colors.transparent,
                          builder: (_) => _TaskFormSheet(task: task),
                        );
                        if (saved == true) {
                          ref.invalidate(allTasksProvider);
                          if (context.mounted) {
                            showSuccessSnack(
                                context, 'Tarea actualizada correctamente.');
                          }
                        }
                      }
                    : null,
                onDelete: isProfesor
                    ? () async {
                        final confirm = await showDialog<bool>(
                          context: context,
                          builder: (_) => AlertDialog(
                            title: const Text('Eliminar tarea'),
                            content: Text(
                                '¿Eliminar «${task.titulo}»? Se eliminarán también todas las entregas. Esta acción no se puede deshacer.'),
                            actions: [
                              TextButton(
                                  onPressed: () =>
                                      Navigator.of(context).pop(false),
                                  child: const Text('Cancelar')),
                              FilledButton.tonal(
                                  onPressed: () =>
                                      Navigator.of(context).pop(true),
                                  child: const Text('Eliminar')),
                            ],
                          ),
                        );
                        if (confirm != true) return;
                        try {
                          await ref
                              .read(classroomRepositoryProvider)
                              .deleteTask(task.id);
                          ref.invalidate(allTasksProvider);
                          if (context.mounted) {
                            showSuccessSnack(context, 'Tarea eliminada.');
                          }
                        } catch (e) {
                          if (context.mounted) {
                            await showErrorAlert(
                                context, 'No se pudo eliminar la tarea.');
                          }
                        }
                      }
                    : null,
                onSubmitted: () => ref.invalidate(allTasksProvider),
              );
            },
          );
        },
      ),
    );
  }
}

class _GlobalTaskTile extends ConsumerWidget {
  const _GlobalTaskTile({
    required this.task,
    required this.isProfesor,
    this.onPublishToggle,
    this.onEdit,
    this.onDelete,
    this.onSubmitted,
  });

  final ClassroomTask task;
  final bool isProfesor;
  final VoidCallback? onPublishToggle;
  final VoidCallback? onEdit;
  final VoidCallback? onDelete;
  final VoidCallback? onSubmitted;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final scheme = Theme.of(context).colorScheme;
    final textTheme = Theme.of(context).textTheme;

    final isEntregado =
        task.estado == 'entregado' || task.estado == 'calificado';
    final estadoColor = task.estado == 'calificado'
        ? AppColors.azulLight
        : (isEntregado
            ? AppColors.verdeLight
            : (task.estado == 'borrador'
                ? AppColors.naranjaLight
                : AppColors.rojoLight));

    return Card(
      elevation: 0,
      color: scheme.surfaceContainerHighest,
      shape:
          RoundedRectangleBorder(borderRadius: BorderRadius.circular(Radii.md)),
      child: InkWell(
        onTap: () {
          // Utilizar la misma hoja de entregas o detalle que el aula digital
          showTaskDetailSheet(
            context,
            task: task,
            isProfesor: isProfesor,
            onSubmitted: onSubmitted,
          );
        },
        borderRadius: BorderRadius.circular(Radii.md),
        child: Padding(
          padding: const EdgeInsets.all(Space.lg),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(task.titulo, style: textTheme.titleMedium),
                        const SizedBox(height: Space.xs),
                        Text(
                          '${task.nombreModulo} • ${task.nombreProfesor}',
                          style: textTheme.bodySmall
                              ?.copyWith(color: scheme.onSurfaceVariant),
                        ),
                      ],
                    ),
                  ),
                  if (isProfesor)
                    Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Switch(
                          value: task.publicado,
                          onChanged: onPublishToggle != null
                              ? (_) => onPublishToggle!()
                              : null,
                        ),
                        PopupMenuButton<String>(
                          icon: Icon(Icons.more_vert_rounded,
                              color: scheme.onSurfaceVariant),
                          onSelected: (value) {
                            if (value == 'edit') onEdit?.call();
                            if (value == 'delete') onDelete?.call();
                          },
                          itemBuilder: (_) => const [
                            PopupMenuItem(
                                value: 'edit',
                                child: Text('Editar')),
                            PopupMenuItem(
                                value: 'delete',
                                child: Text('Eliminar')),
                          ],
                        ),
                      ],
                    )
                  else
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: estadoColor.withValues(alpha: 0.1),
                        borderRadius: BorderRadius.circular(Radii.pill),
                      ),
                      child: Text(
                        task.estado?.toUpperCase() ?? 'PENDIENTE',
                        style:
                            textTheme.labelSmall?.copyWith(color: estadoColor),
                      ),
                    ),
                ],
              ),
              if (task.descripcion.isNotEmpty) ...[
                const SizedBox(height: Space.md),
                Text(
                  task.descripcion,
                  style: textTheme.bodyMedium,
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                ),
              ],
              if (task.archivoAdjunto != null) ...[
                const SizedBox(height: Space.sm),
                InkWell(
                  borderRadius: BorderRadius.circular(Radii.sm),
                  onTap: () async {
                    final url = ref
                        .read(classroomRepositoryProvider)
                        .taskAttachmentUrl(task.id);
                    final ok = await launchUrl(Uri.parse(url),
                        mode: LaunchMode.externalApplication);
                    if (!ok && context.mounted) {
                      await showErrorAlert(context, 'No se pudo abrir el archivo.');
                    }
                  },
                  child: Container(
                    padding: const EdgeInsets.symmetric(
                        horizontal: Space.md, vertical: Space.sm),
                    decoration: BoxDecoration(
                      color: scheme.surfaceContainerHighest,
                      borderRadius: BorderRadius.circular(Radii.sm),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(Icons.attach_file_rounded,
                            size: 16, color: scheme.onSurfaceVariant),
                        const SizedBox(width: Space.xs),
                        Text('Ver archivo adjunto',
                            style: Theme.of(context).textTheme.bodySmall),
                      ],
                    ),
                  ),
                ),
              ],
              if (!isProfesor && task.archivoEntrega != null) ...[
                const SizedBox(height: Space.sm),
                InkWell(
                  borderRadius: BorderRadius.circular(Radii.sm),
                  onTap: () async {
                    final url = ref
                        .read(classroomRepositoryProvider)
                        .submissionFileUrl(task.id, kind: 'entrega');
                    final ok = await launchUrl(Uri.parse(url),
                        mode: LaunchMode.externalApplication);
                    if (!ok && context.mounted) {
                      await showErrorAlert(context, 'No se pudo abrir el archivo.');
                    }
                  },
                  child: Container(
                    padding: const EdgeInsets.symmetric(
                        horizontal: Space.md, vertical: Space.sm),
                    decoration: BoxDecoration(
                      color: scheme.primaryContainer.withValues(alpha: 0.15),
                      borderRadius: BorderRadius.circular(Radii.sm),
                      border: Border.all(
                        color: scheme.primary.withValues(alpha: 0.25),
                        width: 1,
                      ),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(Icons.download_rounded,
                            size: 16, color: scheme.primary),
                        const SizedBox(width: Space.xs),
                        Text('Descargar mi tarea entregada',
                            style: Theme.of(context).textTheme.bodySmall?.copyWith(
                                  color: scheme.primary,
                                  fontWeight: FontWeight.w700,
                                )),
                      ],
                    ),
                  ),
                ),
              ],
              if (isProfesor) ...[
                const SizedBox(height: Space.md),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      '${task.totalEntregas} entregas',
                      style:
                          textTheme.bodySmall?.copyWith(color: scheme.primary),
                    ),
                    Text(
                      '${task.totalCorregidas} corregidas',
                      style: textTheme.bodySmall
                          ?.copyWith(color: AppColors.verdeLight),
                    ),
                  ],
                ),
              ] else if (task.nota != null) ...[
                const SizedBox(height: Space.md),
                Container(
                  padding: const EdgeInsets.symmetric(
                      horizontal: Space.md, vertical: Space.sm),
                  decoration: BoxDecoration(
                    color: scheme.surface,
                    borderRadius: BorderRadius.circular(Radii.sm),
                    border: Border.all(color: scheme.outlineVariant),
                  ),
                  child: Row(
                    children: [
                      Icon(Icons.stars_rounded,
                          size: 16, color: AppColors.naranjaLight),
                      const SizedBox(width: Space.sm),
                      Text('Nota: ${task.nota}',
                          style: textTheme.bodySmall
                              ?.copyWith(fontWeight: FontWeight.bold)),
                    ],
                  ),
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }
}

/// Create/edit sheet for a profesor's own task. The module picker only
/// appears when creating — a task's module can't change once it exists
/// (mirrors the web: guardarTarea.php never lets idModulo change on edit).
class _TaskFormSheet extends ConsumerStatefulWidget {
  const _TaskFormSheet({this.task});
  final ClassroomTask? task;

  @override
  ConsumerState<_TaskFormSheet> createState() => _TaskFormSheetState();
}

class _TaskFormSheetState extends ConsumerState<_TaskFormSheet> {
  late final _tituloController =
      TextEditingController(text: widget.task?.titulo ?? '');
  late final _descripcionController =
      TextEditingController(text: widget.task?.descripcion ?? '');
  late bool _publicado = widget.task?.publicado ?? true;
  int? _idModulo;
  PlatformFile? _picked;
  bool _saving = false;

  bool get _isEdit => widget.task != null;

  Future<void> _pickFile() async {
    final result = await FilePicker.platform.pickFiles(
      type: FileType.custom,
      allowedExtensions: ['pdf', 'docx', 'txt', 'zip', 'png', 'jpg', 'jpeg'],
    );
    if (result != null && result.files.isNotEmpty) {
      setState(() => _picked = result.files.single);
    }
  }

  Future<void> _save() async {
    final titulo = _tituloController.text.trim();
    if (titulo.isEmpty || (!_isEdit && _idModulo == null)) return;
    setState(() => _saving = true);
    try {
      final repo = ref.read(classroomRepositoryProvider);
      if (_isEdit) {
        await repo.updateTask(
          idTarea: widget.task!.id,
          titulo: titulo,
          descripcion: _descripcionController.text.trim(),
          publicado: _publicado,
          filePath: _picked?.path,
          fileName: _picked?.name,
        );
      } else {
        await repo.createTask(
          idModulo: _idModulo!,
          titulo: titulo,
          descripcion: _descripcionController.text.trim(),
          publicado: _publicado,
          filePath: _picked?.path,
          fileName: _picked?.name,
        );
      }
      if (mounted) Navigator.of(context).pop(true);
    } catch (e) {
      setState(() => _saving = false);
      if (mounted) {
        await showErrorAlert(
            context,
            _isEdit
                ? 'No se pudo actualizar la tarea.'
                : 'No se pudo crear la tarea.');
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final modulesAsync = ref.watch(classroomModulesProvider);
    return AppBottomSheet(
      child: Padding(
        padding:
            EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Text(_isEdit ? 'Editar tarea' : 'Nueva tarea',
                style: Theme.of(context).textTheme.titleMedium),
            const SizedBox(height: Space.xl),
            if (!_isEdit) ...[
              modulesAsync.when(
                loading: () => const Padding(
                  padding: EdgeInsets.symmetric(vertical: Space.md),
                  child: Center(
                      child:
                          CircularProgressIndicator(strokeWidth: 2)),
                ),
                error: (_, __) =>
                    const Text('No se pudieron cargar los módulos.'),
                data: (modules) => DropdownButtonFormField<int>(
                  isExpanded: true,
                  initialValue: _idModulo,
                  decoration: const InputDecoration(labelText: 'Módulo *'),
                  items: [
                    for (final m in modules)
                      DropdownMenuItem(value: m.id, child: Text(m.nombre)),
                  ],
                  onChanged: (v) => setState(() => _idModulo = v),
                ),
              ),
              const SizedBox(height: Space.md),
            ],
            TextField(
              controller: _tituloController,
              maxLength: 150,
              decoration: const InputDecoration(labelText: 'Título *'),
            ),
            TextField(
              controller: _descripcionController,
              minLines: 3,
              maxLines: 6,
              decoration:
                  const InputDecoration(labelText: 'Descripción / enunciado'),
            ),
            const SizedBox(height: Space.sm),
            OutlinedButton.icon(
              onPressed: _pickFile,
              icon: const Icon(Icons.attach_file_rounded),
              label: Text(_picked?.name ??
                  (widget.task?.archivoAdjunto != null
                      ? 'Sustituir adjunto'
                      : 'Adjuntar archivo (opcional)')),
            ),
            const SizedBox(height: Space.md),
            SwitchListTile(
              contentPadding: EdgeInsets.zero,
              title: const Text('Publicada'),
              subtitle: const Text('Visible para los estudiantes'),
              value: _publicado,
              onChanged: (v) => setState(() => _publicado = v),
            ),
            const SizedBox(height: Space.xl),
            FilledButton(
              onPressed: _saving ? null : _save,
              child: _saving
                  ? const SizedBox(
                      height: 18,
                      width: 18,
                      child: CircularProgressIndicator(strokeWidth: 2))
                  : Text(_isEdit ? 'Guardar cambios' : 'Crear tarea'),
            ),
          ],
        ),
      ),
    );
  }
}
