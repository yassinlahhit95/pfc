import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../data/planificacion_repository.dart';

// 'YYYY-MM-DD HH:MM:SS' (formato MySQL) -> 'DD/MM/YYYY', igual que la web.
String _formatFecha(String mysqlDatetime) {
  final datePart = mysqlDatetime.split(' ').first;
  final parts = datePart.split('-');
  if (parts.length != 3) return datePart;
  return '${parts[2]}/${parts[1]}/${parts[0]}';
}

class PlanificacionScreen extends ConsumerWidget {
  const PlanificacionScreen({super.key});

  Future<void> _addItem(BuildContext context, WidgetRef ref) async {
    final controller = TextEditingController();
    final texto = await showModalBottomSheet<String>(
      context: context,
      isScrollControlled: true,
      builder: (context) => Padding(
        padding: EdgeInsets.only(
          left: Space.lg,
          right: Space.lg,
          top: Space.lg,
          bottom: MediaQuery.of(context).viewInsets.bottom + Space.lg,
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Text('Nueva tarea', style: Theme.of(context).textTheme.titleMedium),
            const SizedBox(height: Space.md),
            TextField(
              controller: controller,
              autofocus: true,
              maxLength: 500,
              maxLines: 3,
              minLines: 1,
              decoration: const InputDecoration(hintText: 'Qué hay que hacer…'),
              onSubmitted: (v) => Navigator.of(context).pop(v.trim()),
            ),
            const SizedBox(height: Space.sm),
            ElevatedButton(
              onPressed: () =>
                  Navigator.of(context).pop(controller.text.trim()),
              child: const Text('Añadir'),
            ),
          ],
        ),
      ),
    );
    if (texto != null && texto.isNotEmpty) {
      await ref.read(planificacionRepositoryProvider).crear(texto);
      ref.invalidate(planificacionListProvider);
    }
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final tareasAsync = ref.watch(planificacionListProvider);

    return Scaffold(
      appBar: AppBar(title: const Text('Planificación')),
      floatingActionButton: FloatingActionButton(
        onPressed: () => _addItem(context, ref),
        child: const Icon(Icons.add),
      ),
      body: AsyncView<List<PlanTarea>>(
        value: tareasAsync,
        onRetry: () => ref.invalidate(planificacionListProvider),
        data: (context, tareas) {
          if (tareas.isEmpty) {
            return const EmptyState(
              icon: Icons.checklist_rounded,
              title: 'Sin tareas planificadas',
              description: 'Añade lo que el centro tiene pendiente hacer.',
            );
          }
          return ListView.builder(
            padding: const EdgeInsets.all(Space.md).copyWith(bottom: 100),
            itemCount: tareas.length,
            itemBuilder: (context, index) {
              final tarea = tareas[index];
              return Dismissible(
                key: ValueKey(tarea.id),
                direction: DismissDirection.endToStart,
                background: Container(
                  alignment: Alignment.centerRight,
                  padding: const EdgeInsets.symmetric(horizontal: Space.lg),
                  color: Theme.of(context).colorScheme.errorContainer,
                  child: Icon(Icons.delete_outline,
                      color: Theme.of(context).colorScheme.onErrorContainer),
                ),
                onDismissed: (_) async {
                  await ref.read(planificacionRepositoryProvider).borrar(tarea.id);
                  ref.invalidate(planificacionListProvider);
                },
                child: CheckboxListTile(
                  value: tarea.completada,
                  onChanged: (value) async {
                    await ref
                        .read(planificacionRepositoryProvider)
                        .toggle(tarea.id, value ?? false);
                    ref.invalidate(planificacionListProvider);
                  },
                  title: Text(
                    tarea.texto,
                    style: tarea.completada
                        ? const TextStyle(decoration: TextDecoration.lineThrough)
                        : null,
                  ),
                  // Same "quién y cuándo" the web history shows — a task
                  // completed from here should read the same on both.
                  subtitle: (tarea.completada && tarea.completadaPorNombre != null)
                      ? Text(
                          'Completada'
                          '${tarea.fechaCompletada != null ? ' el ${_formatFecha(tarea.fechaCompletada!)}' : ''}'
                          ' por ${tarea.completadaPorNombre}',
                          style: Theme.of(context).textTheme.bodySmall,
                        )
                      : null,
                  controlAffinity: ListTileControlAffinity.leading,
                ),
              );
            },
          );
        },
      ),
    );
  }
}
