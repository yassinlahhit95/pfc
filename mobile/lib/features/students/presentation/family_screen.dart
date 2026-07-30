import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/password_confirmation_dialog.dart';
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
                  trailing: PopupMenuButton<String>(
                    icon: const Icon(Icons.more_vert),
                    onSelected: (value) async {
                      if (value == 'edit') {
                        final result = await TutorFormSheet.show(
                          context,
                          idEstudiante,
                          tutor: tutor,
                        );
                        if (result == true) {
                          ref.invalidate(studentFamilyProvider(idEstudiante));
                        }
                      } else if (value == 'password') {
                        final newPassword = await PasswordConfirmationDialog.show(
                          context,
                          title: 'Cambiar Contraseña',
                          message: 'Introduce la nueva contraseña para ${tutor.nombre}.',
                        );
                        if (newPassword != null) {
                          try {
                            await ref.read(familyRepositoryProvider).changeFamilyPassword(tutor.id, newPassword);
                            if (context.mounted) {
                              ScaffoldMessenger.of(context).showSnackBar(
                                const SnackBar(content: Text('Contraseña actualizada correctamente'), backgroundColor: Colors.green),
                              );
                            }
                          } catch (e) {
                            if (context.mounted) {
                              ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Error: $e'), backgroundColor: scheme.error));
                            }
                          }
                        }
                      } else if (value == 'delete') {
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
                              ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Error: $e'), backgroundColor: scheme.error));
                            }
                          }
                        }
                      }
                    },
                    itemBuilder: (context) => [
                      const PopupMenuItem(value: 'edit', child: Text('Editar')),
                      const PopupMenuItem(value: 'password', child: Text('Cambiar Contraseña')),
                      const PopupMenuItem(value: 'delete', child: Text('Desvincular', style: TextStyle(color: Colors.red))),
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
