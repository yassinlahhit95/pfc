import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../data/family_repository.dart';
import 'tutor_form_sheet.dart';

class FamilyScreen extends ConsumerWidget {
  const FamilyScreen({super.key, required this.idEstudiante});
  final int idEstudiante;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final familyAsync = ref.watch(studentFamilyProvider(idEstudiante));
    final scheme = Theme.of(context).colorScheme;

    return Scaffold(
      appBar: AppBar(
        title: const Text('Familiares'),
      ),
      floatingActionButton: FloatingActionButton(
        onPressed: () async {
          final result = await TutorFormSheet.show(context, idEstudiante);
          if (result == true) {
            ref.invalidate(studentFamilyProvider(idEstudiante));
          }
        },
        child: const Icon(Icons.add),
      ),
      body: AsyncView<List<Tutor>>(
        value: familyAsync,
        onRetry: () => ref.invalidate(studentFamilyProvider(idEstudiante)),
        data: (context, tutores) {
          if (tutores.isEmpty) {
            return const Center(
              child: Text('No hay familiares registrados para este alumno.'),
            );
          }

          return ListView.separated(
            padding: const EdgeInsets.all(Space.md),
            itemCount: tutores.length,
            separatorBuilder: (_, __) => const SizedBox(height: Space.sm),
            itemBuilder: (context, index) {
              final tutor = tutores[index];
              return Card(
                elevation: 0,
                color: scheme.surfaceContainerHighest,
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(Radii.md),
                ),
                child: ListTile(
                  leading: CircleAvatar(
                    backgroundColor: scheme.primary.withValues(alpha: 0.15),
                    child: Text(
                      tutor.nombre.isNotEmpty ? tutor.nombre[0].toUpperCase() : '?',
                      style: TextStyle(color: scheme.primary),
                    ),
                  ),
                  title: Text(tutor.nombre, style: const TextStyle(fontWeight: FontWeight.w600)),
                  subtitle: Text('${tutor.parentesco}\n${tutor.email}\n${tutor.telefono}'),
                  isThreeLine: true,
                  trailing: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      IconButton(
                        icon: const Icon(Icons.edit),
                        color: scheme.primary,
                        onPressed: () async {
                          final result = await TutorFormSheet.show(
                            context,
                            idEstudiante,
                            tutor: tutor,
                          );
                          if (result == true) {
                            ref.invalidate(studentFamilyProvider(idEstudiante));
                          }
                        },
                      ),
                      IconButton(
                        icon: const Icon(Icons.delete_outline),
                        color: scheme.error,
                        onPressed: () async {
                          final confirm = await showDialog<bool>(
                            context: context,
                            builder: (ctx) => AlertDialog(
                              title: const Text('Desvincular Familiar'),
                              content: Text('¿Está seguro de que desea desvincular a ${tutor.nombre}?'),
                              actions: [
                                TextButton(
                                  onPressed: () => Navigator.pop(ctx, false),
                                  child: const Text('Cancelar'),
                                ),
                                FilledButton(
                                  onPressed: () => Navigator.pop(ctx, true),
                                  child: const Text('Desvincular'),
                                ),
                              ],
                            ),
                          );
                          if (confirm == true) {
                            try {
                              await ref.read(familyRepositoryProvider).removeTutorFromStudent(idEstudiante, tutor.id);
                              ref.invalidate(studentFamilyProvider(idEstudiante));
                            } catch (e) {
                              if (context.mounted) {
                                ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Error: $e')));
                              }
                            }
                          }
                        },
                      ),
                    ],
                  ),
                ),
              );
            },
          );
        },
      ),
    );
  }
}
