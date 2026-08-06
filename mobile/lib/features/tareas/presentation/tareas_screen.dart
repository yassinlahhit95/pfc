import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/error_modal.dart';
import '../../../core/auth/auth_state.dart';
import '../../../core/auth/session.dart';
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

    return Scaffold(
      appBar: AppBar(title: const Text('Mis Tareas')),
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
            padding: const EdgeInsets.all(Space.md),
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
    this.onSubmitted,
  });

  final ClassroomTask task;
  final bool isProfesor;
  final VoidCallback? onPublishToggle;
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
                    Switch(
                      value: task.publicado,
                      onChanged: onPublishToggle != null
                          ? (_) => onPublishToggle!()
                          : null,
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
