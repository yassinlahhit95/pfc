import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_riverpod/legacy.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/profile_detail_sheet.dart';
import '../../../core/widgets/password_confirmation_dialog.dart';
import '../../../core/utils/debounce.dart';
import '../../../core/auth/auth_state.dart';
import '../../../core/auth/session.dart';
import '../data/students_repository.dart';
import 'add_student_screen.dart';
import 'family_screen.dart';
import '../../attendance/presentation/center_attendance_screen.dart'
    show lookupsProvider;

class StudentsFilters {
  const StudentsFilters({
    this.nivel,
    this.ciclo,
    this.anio,
    this.grupo,
    this.estado,
    this.q,
  });

  final int? nivel;
  final int? ciclo;
  final String? anio;
  final int? grupo;
  final String? estado;
  final String? q;

  StudentsFilters copyWith({
    int? nivel,
    int? ciclo,
    String? anio,
    int? grupo,
    String? estado,
    String? q,
  }) {
    return StudentsFilters(
      nivel: nivel ?? this.nivel,
      ciclo: ciclo ?? this.ciclo,
      anio: anio ?? this.anio,
      grupo: grupo ?? this.grupo,
      estado: estado ?? this.estado,
      q: q ?? this.q,
    );
  }
}

final studentsFiltersProvider =
    StateProvider<StudentsFilters>((ref) => const StudentsFilters());

class StudentsScreen extends ConsumerStatefulWidget {
  const StudentsScreen({super.key});

  @override
  ConsumerState<StudentsScreen> createState() => _StudentsScreenState();
}

class _StudentsScreenState extends ConsumerState<StudentsScreen> {
  late ScrollController _scrollController;
  final Debounce _debounce = Debounce();

  @override
  void initState() {
    super.initState();
    _scrollController = ScrollController();
    _scrollController.addListener(_onScroll);
  }

  void _onScroll() {
    if (_scrollController.position.pixels >=
        _scrollController.position.maxScrollExtent - 200) {
      final filters = ref.read(studentsFiltersProvider);
      _debounce(const Duration(milliseconds: 300), () {
        ref
            .read(studentsProvider(
              (
                cicloId: filters.ciclo,
                nivelId: filters.nivel,
                status: filters.estado,
                query: filters.q,
              ),
            ).notifier)
            .loadMore();
      });
    }
  }

  @override
  void dispose() {
    _scrollController.dispose();
    _debounce.cancel();
    super.dispose();
  }

  void _showFiltersSheet(BuildContext context) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      builder: (context) => const _StudentsFiltersSheet(),
    );
  }

  @override
  Widget build(BuildContext context) {
    final session = ref.watch(sessionControllerProvider).value;
    final canManage = session?.role == UserRole.director ||
        session?.role == UserRole.secretaria;
    final filters = ref.watch(studentsFiltersProvider);
    final studentsAsync = ref.watch(studentsProvider(
      (
        cicloId: filters.ciclo,
        nivelId: filters.nivel,
        status: filters.estado,
        query: filters.q,
      ),
    ));

    return Scaffold(
      appBar: AppBar(
        title: const Text('Alumnos'),
        actions: [
          IconButton(
            icon: const Icon(Icons.filter_list),
            onPressed: () => _showFiltersSheet(context),
          ),
        ],
      ),
      floatingActionButton: canManage
          ? FloatingActionButton(
              onPressed: () async {
                final result = await Navigator.push(
                  context,
                  MaterialPageRoute(builder: (_) => const AddStudentScreen()),
                );
                if (result == true) {
                  ref.invalidate(studentsProvider);
                }
              },
              child: const Icon(Icons.add),
            )
          : null,
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(Space.md),
            child: TextField(
              onChanged: (value) {
                _debounce(const Duration(milliseconds: 500), () {
                  ref
                      .read(studentsFiltersProvider.notifier)
                      .update((s) => s.copyWith(q: value));
                });
              },
              decoration: InputDecoration(
                hintText: 'Buscar por nombre...',
                prefixIcon: const Icon(Icons.search),
                contentPadding: const EdgeInsets.symmetric(
                    horizontal: Space.md, vertical: Space.sm),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(Radii.md),
                  borderSide: BorderSide.none,
                ),
                filled: true,
                fillColor:
                    Theme.of(context).colorScheme.surfaceContainerHighest,
              ),
            ),
          ),
          Expanded(
            child: AsyncView<({List<Student> students, int total})>(
              value: studentsAsync,
              onRetry: () => ref.invalidate(studentsProvider),
              data: (context, data) {
                if (data.students.isEmpty) {
                  return const EmptyState(
                      icon: Icons.people_outlined,
                      title: 'Sin alumnos registrados');
                }
                return Column(
                  children: [
                    Expanded(
                      child: ListView.separated(
                        controller: _scrollController,
                        itemCount: data.students.length,
                        separatorBuilder: (_, __) =>
                            const SizedBox(height: Space.sm),
                        itemBuilder: (context, index) {
                          final student = data.students[index];
                          return _StudentCard(student: student);
                        },
                      ),
                    ),
                  ],
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}

class _StudentsFiltersSheet extends ConsumerStatefulWidget {
  const _StudentsFiltersSheet();

  @override
  ConsumerState<_StudentsFiltersSheet> createState() =>
      _StudentsFiltersSheetState();
}

class _StudentsFiltersSheetState extends ConsumerState<_StudentsFiltersSheet> {
  int? _nivel;
  int? _ciclo;
  String? _anio;
  int? _grupo;
  String? _estado;

  @override
  void initState() {
    super.initState();
    final filters = ref.read(studentsFiltersProvider);
    _nivel = filters.nivel;
    _ciclo = filters.ciclo;
    _anio = filters.anio;
    _grupo = filters.grupo;
    _estado = filters.estado;
  }

  @override
  Widget build(BuildContext context) {
    final lookupsAsync = ref.watch(lookupsProvider);

    return Padding(
      padding: EdgeInsets.only(
        bottom: MediaQuery.of(context).viewInsets.bottom,
        left: 16,
        right: 16,
        top: 16,
      ),
      child: lookupsAsync.when(
        loading: () => const SizedBox(
            height: 200, child: Center(child: CircularProgressIndicator())),
        error: (e, st) => const SizedBox(
            height: 200, child: Center(child: Text('Error cargando filtros'))),
        data: (lookups) {
          final nivelesData = lookups['niveles'] as List? ?? [];
          final ciclos = lookups['ciclos'] as List? ?? [];
          final activeNivelIds = ciclos.map((c) => c['idNivel'] as int).toSet();
          final niveles = nivelesData
              .where((n) => activeNivelIds.contains(n['idNivel'] as int))
              .toList();

          final textTheme = Theme.of(context).textTheme;
          return Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Text('Filtros', style: textTheme.titleLarge),
              const SizedBox(height: 16),
              DropdownButtonFormField<int?>(
                initialValue: _nivel,
                isExpanded: true,
                decoration: const InputDecoration(labelText: 'Nivel'),
                items: [
                  const DropdownMenuItem(
                      value: null, child: Text('Todos los niveles')),
                  ...niveles.map((n) => DropdownMenuItem(
                      value: n['idNivel'] as int,
                      child: Text(n['nombreNivel'],
                          overflow: TextOverflow.ellipsis, maxLines: 1))),
                ],
                onChanged: (val) => setState(() => _nivel = val),
              ),
              const SizedBox(height: 16),
              DropdownButtonFormField<int?>(
                initialValue: _ciclo,
                isExpanded: true,
                decoration: const InputDecoration(labelText: 'Ciclo'),
                items: [
                  const DropdownMenuItem(
                      value: null, child: Text('Todos los ciclos')),
                  ...ciclos.map((c) => DropdownMenuItem(
                      value: c['idCiclo'] as int,
                      child: Text(c['nombreCiclo'],
                          overflow: TextOverflow.ellipsis, maxLines: 1))),
                ],
                onChanged: (val) => setState(() => _ciclo = val),
              ),
              const SizedBox(height: 24),
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton(
                      onPressed: () {
                        ref.read(studentsFiltersProvider.notifier).state =
                            const StudentsFilters();
                        Navigator.pop(context);
                      },
                      child: const Text('Limpiar'),
                    ),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: FilledButton(
                      onPressed: () {
                        final currentFilters =
                            ref.read(studentsFiltersProvider);
                        ref.read(studentsFiltersProvider.notifier).state =
                            currentFilters.copyWith(
                          nivel: _nivel,
                          ciclo: _ciclo,
                          anio: _anio,
                          grupo: _grupo,
                          estado: _estado,
                        );
                        Navigator.pop(context);
                      },
                      child: const Text('Aplicar'),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 24),
            ],
          );
        },
      ),
    );
  }
}

class _StudentCard extends ConsumerWidget {
  const _StudentCard({required this.student});
  final Student student;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final scheme = Theme.of(context).colorScheme;
    final textTheme = Theme.of(context).textTheme;

    final session = ref.watch(sessionControllerProvider).value;
    final canManage = session?.role == UserRole.director ||
        session?.role == UserRole.secretaria;

    return InkWell(
      onTap: () {
        ProfileDetailSheet.show(
          context,
          uid: student.id,
          rol: 'estudiante',
          nombre: student.nombre,
          email: student.email,
          telefono: student.telefono,
          subtitle:
              '${student.course} - ${student.year ?? ''} (${student.abreviaturaCiclo})',
        );
      },
      borderRadius: BorderRadius.circular(Radii.md),
      child: Container(
        margin: const EdgeInsets.symmetric(horizontal: Space.md),
        padding: const EdgeInsets.all(Space.md),
        decoration: BoxDecoration(
          color: scheme.surfaceContainerHighest,
          borderRadius: BorderRadius.circular(Radii.md),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Container(
                  width: 48,
                  height: 48,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    color: scheme.primary.withValues(alpha: 0.15),
                  ),
                  alignment: Alignment.center,
                  child: Text(
                    student.nombre.isNotEmpty
                        ? student.nombre[0].toUpperCase()
                        : '?',
                    style: textTheme.headlineSmall
                        ?.copyWith(color: scheme.primary),
                  ),
                ),
                const SizedBox(width: Space.md),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(student.nombre, style: textTheme.titleSmall),
                      Text(
                        student.abreviaturaCiclo,
                        style: textTheme.bodySmall
                            ?.copyWith(color: scheme.onSurfaceVariant),
                      ),
                    ],
                  ),
                ),
                if (canManage)
                  PopupMenuButton<String>(
                    icon: const Icon(Icons.more_vert),
                    onSelected: (value) async {
                      if (value == 'family') {
                        Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) =>
                                FamilyScreen(idEstudiante: student.id),
                          ),
                        );
                      } else if (value == 'edit') {
                        final result = await Navigator.push(
                          context,
                          MaterialPageRoute(
                            builder: (_) => AddStudentScreen(student: student),
                          ),
                        );
                        if (result == true) {
                          ref.invalidate(studentsProvider);
                        }
                      } else if (value == 'password') {
                        _showChangePasswordDialog(context, ref, student);
                      } else if (value == 'delete') {
                        final password = await PasswordConfirmationDialog.show(
                          context,
                          title: 'Eliminar Alumno',
                          message:
                              'Introduce tu contraseña para confirmar la eliminación de ${student.nombre}.',
                        );
                        if (password != null) {
                          try {
                            await ref
                                .read(studentsRepositoryProvider)
                                .deleteStudent(student.id, password);
                            ref.invalidate(studentsProvider);
                          } catch (e) {
                            if (context.mounted) {
                              ScaffoldMessenger.of(context).showSnackBar(
                                SnackBar(
                                    content: Text('Error: $e'),
                                    backgroundColor:
                                        Theme.of(context).colorScheme.error),
                              );
                            }
                          }
                        }
                      }
                    },
                    itemBuilder: (context) => [
                      const PopupMenuItem(
                          value: 'family', child: Text('Familiares (Tutores)')),
                      const PopupMenuItem(value: 'edit', child: Text('Editar')),
                      const PopupMenuItem(
                          value: 'password', child: Text('Cambiar Contraseña')),
                      const PopupMenuItem(
                          value: 'delete',
                          child: Text('Eliminar',
                              style: TextStyle(color: Colors.red))),
                    ],
                  ),
              ],
            ),
            if (student.course.isNotEmpty) ...[
              const SizedBox(height: Space.sm),
              Text(
                '${student.course} - ${student.year ?? ''}',
                style: textTheme.bodySmall
                    ?.copyWith(color: scheme.onSurfaceVariant),
              ),
            ],
          ],
        ),
      ),
    );
  }
}

Future<void> _showChangePasswordDialog(
    BuildContext context, WidgetRef ref, Student student) async {
  final controller = TextEditingController();
  final formKey = GlobalKey<FormState>();

  final result = await showDialog<bool>(
    context: context,
    builder: (ctx) => AlertDialog(
      title: const Text('Cambiar Contraseña'),
      content: Form(
        key: formKey,
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text('Nueva contraseña para ${student.nombre}:'),
            const SizedBox(height: Space.md),
            TextFormField(
              controller: controller,
              decoration: const InputDecoration(
                  labelText: 'Nueva Contraseña', border: OutlineInputBorder()),
              obscureText: true,
              validator: (v) =>
                  v == null || v.length < 6 ? 'Mínimo 6 caracteres' : null,
            ),
          ],
        ),
      ),
      actions: [
        TextButton(
          onPressed: () => Navigator.pop(ctx, false),
          child: const Text('Cancelar'),
        ),
        FilledButton(
          onPressed: () {
            if (formKey.currentState!.validate()) {
              Navigator.pop(ctx, true);
            }
          },
          child: const Text('Guardar'),
        ),
      ],
    ),
  );

  if (result == true && context.mounted) {
    try {
      await ref
          .read(studentsRepositoryProvider)
          .changeStudentPassword(student.id, controller.text);
      if (context.mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(
            content: Text('Contraseña actualizada correctamente')));
      }
    } catch (e) {
      if (context.mounted) {
        ScaffoldMessenger.of(context)
            .showSnackBar(SnackBar(content: Text('Error: $e')));
      }
    }
  }
}
