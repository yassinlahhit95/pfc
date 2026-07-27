import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/auth/auth_state.dart';
import '../../../core/auth/session.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/filter_bar.dart';
import '../../../core/widgets/premium.dart';
import '../data/grades_repository.dart';

class GradesScreen extends ConsumerWidget {
  const GradesScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final gradesAsync = ref.watch(gradesProvider);
    final role = ref.watch(sessionControllerProvider).valueOrNull?.role;

    return Scaffold(
      appBar: AppBar(title: const Text('Notas')),
      body: RefreshIndicator(
        onRefresh: () async => ref.invalidate(gradesProvider),
        child: AsyncView<Map<String, dynamic>>(
          value: gradesAsync,
          onRetry: () => ref.invalidate(gradesProvider),
          data: (context, data) => switch (role) {
            UserRole.estudiante => _EstudianteGrades(data: data),
            UserRole.profesor => _ProfesorGrades(data: data),
            UserRole.tutor => _TutorGrades(data: data),
            _ => const EmptyState(
                icon: Icons.grade_outlined,
                title: 'No disponible para este rol',
              ),
          },
        ),
      ),
    );
  }
}

String _fmtNota(dynamic n) => n == null ? '—' : n.toString();

Color _notaColor(BuildContext context, dynamic raw) {
  final n = double.tryParse(raw?.toString() ?? '');
  if (n == null) return Theme.of(context).colorScheme.onSurfaceVariant;
  final dark = Theme.of(context).brightness == Brightness.dark;
  if (n >= 5) return dark ? AppColors.verdeDark : AppColors.verdeLight;
  return dark ? AppColors.rojoDark : AppColors.rojoLight;
}

class _ModuloCard extends StatelessWidget {
  const _ModuloCard({required this.m});
  final Map<String, dynamic> m;

  @override
  Widget build(BuildContext context) {
    return AppCard(
      margin: const EdgeInsets.only(bottom: Space.md),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(m['nombreModulo'] as String? ?? '', style: Theme.of(context).textTheme.titleSmall),
          const SizedBox(height: Space.md),
          Wrap(
            spacing: Space.sm,
            runSpacing: Space.sm,
            children: [
              _NotaChip('1ª ev.', m['nota_1ev']),
              _NotaChip('1ª final', m['nota_1final']),
              _NotaChip('2ª ev.', m['nota_2ev']),
              _NotaChip('2ª final', m['nota_2final']),
            ],
          ),
        ],
      ),
    );
  }
}

class _NotaChip extends StatelessWidget {
  const _NotaChip(this.label, this.rawValue);
  final String label;
  final dynamic rawValue;

  @override
  Widget build(BuildContext context) {
    final color = _notaColor(context, rawValue);
    final scheme = Theme.of(context).colorScheme;
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
      decoration: BoxDecoration(
        color: scheme.surfaceContainerHighest,
        borderRadius: BorderRadius.circular(Radii.sm),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Text('$label  ', style: Theme.of(context).textTheme.bodySmall),
          Text(_fmtNota(rawValue), style: TextStyle(color: color, fontWeight: FontWeight.w700)),
        ],
      ),
    );
  }
}

class _EstudianteGrades extends StatefulWidget {
  const _EstudianteGrades({required this.data});
  final Map<String, dynamic> data;

  @override
  State<_EstudianteGrades> createState() => _EstudianteGradesState();
}

class _EstudianteGradesState extends State<_EstudianteGrades> {
  String _searchQuery = '';

  @override
  Widget build(BuildContext context) {
    final modulos = (widget.data['modulos'] as List).cast<Map<String, dynamic>>();
    final retos = (widget.data['retos'] as List).cast<Map<String, dynamic>>();
    if (modulos.isEmpty && retos.isEmpty) {
      return const EmptyState(
        icon: Icons.grade_outlined,
        title: 'Todavía no hay notas registradas',
      );
    }

    final filteredModulos = modulos.where((m) {
      final name = (m['nombreModulo'] as String? ?? '').toLowerCase();
      return _searchQuery.isEmpty || name.contains(_searchQuery);
    }).toList();

    return Column(
      children: [
        if (modulos.length > 2)
          Padding(
            padding: const EdgeInsets.fromLTRB(Space.xl, Space.md, Space.xl, 0),
            child: TextField(
              decoration: const InputDecoration(
                labelText: 'Buscar asignatura / módulo...',
                prefixIcon: Icon(Icons.search_rounded),
                contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 8),
              ),
              onChanged: (val) {
                setState(() {
                  _searchQuery = val.trim().toLowerCase();
                });
              },
            ),
          ),
        Expanded(
          child: ListView(
            padding: const EdgeInsets.fromLTRB(Space.xl, Space.lg, Space.xl, Space.xxxl),
            children: [
              if (filteredModulos.isNotEmpty) ...[
                const SectionLabel('Módulos'),
                for (final m in filteredModulos) _ModuloCard(m: m),
                const SizedBox(height: Space.md),
              ] else if (modulos.isNotEmpty) ...[
                const EmptyState(icon: Icons.filter_alt_off_outlined, title: 'Sin resultados para estos filtros'),
              ],
              if (retos.isNotEmpty && _searchQuery.isEmpty) ...[
                const SectionLabel('Retos'),
                AppCard(
                  padding: EdgeInsets.zero,
                  child: Column(
                    children: [
                      for (var i = 0; i < retos.length; i++) ...[
                        Padding(
                          padding: const EdgeInsets.symmetric(horizontal: Space.lg, vertical: Space.md),
                          child: Row(
                            children: [
                              Expanded(child: Text(retos[i]['nombreReto'] as String? ?? '')),
                              Text(_fmtNota(retos[i]['nota']), style: const TextStyle(fontWeight: FontWeight.w700)),
                            ],
                          ),
                        ),
                        if (i != retos.length - 1)
                          Divider(height: 1, indent: Space.lg, color: Theme.of(context).colorScheme.outlineVariant),
                      ],
                    ],
                  ),
                ),
              ],
            ],
          ),
        ),
      ],
    );
  }
}

class _ProfesorGrades extends StatefulWidget {
  const _ProfesorGrades({required this.data});
  final Map<String, dynamic> data;

  @override
  State<_ProfesorGrades> createState() => _ProfesorGradesState();
}

class _ProfesorGradesState extends State<_ProfesorGrades> {
  String _searchQuery = '';

  @override
  Widget build(BuildContext context) {
    final modulos = (widget.data['modulos'] as List).cast<Map<String, dynamic>>();
    if (modulos.isEmpty) {
      return const EmptyState(
        icon: Icons.grade_outlined,
        title: 'No impartes ningún módulo con alumnado',
      );
    }
    return Column(
      children: [
        Padding(
          padding: const EdgeInsets.fromLTRB(Space.xl, Space.md, Space.xl, 0),
          child: TextField(
            decoration: const InputDecoration(
              labelText: 'Buscar alumno...',
              prefixIcon: Icon(Icons.search_rounded),
              contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            ),
            onChanged: (val) {
              setState(() {
                _searchQuery = val.trim().toLowerCase();
              });
            },
          ),
        ),
        Expanded(
          child: ListView(
            padding: const EdgeInsets.fromLTRB(Space.xl, Space.lg, Space.xl, Space.xxxl),
            children: [
              for (final m in modulos) ...[
                final allStudents = (m['estudiantes'] as List).cast<Map<String, dynamic>>();
                final filteredStudents = allStudents.where((e) {
                  final name = (e['nombreEstudiante'] as String? ?? '').toLowerCase();
                  return _searchQuery.isEmpty || name.contains(_searchQuery);
                }).toList();

                if (filteredStudents.isNotEmpty) ...[
                  SectionLabel(m['nombreModulo'] as String? ?? ''),
                  AppCard(
                    padding: EdgeInsets.zero,
                    margin: const EdgeInsets.only(bottom: Space.xxl),
                    child: Column(
                      children: [
                        for (final e in filteredStudents)
                          Theme(
                            data: Theme.of(context).copyWith(dividerColor: Colors.transparent),
                            child: ExpansionTile(
                              title: Text(e['nombreEstudiante'] as String? ?? '', style: const TextStyle(fontWeight: FontWeight.w500)),
                              childrenPadding: const EdgeInsets.fromLTRB(Space.lg, 0, Space.lg, Space.lg),
                              children: [
                                Wrap(
                                  spacing: Space.sm,
                                  runSpacing: Space.sm,
                                  children: [
                                    _NotaChip('1ª ev.', e['nota_1ev']),
                                    _NotaChip('1ª final', e['nota_1final']),
                                    _NotaChip('2ª ev.', e['nota_2ev']),
                                    _NotaChip('2ª final', e['nota_2final']),
                                  ],
                                ),
                              ],
                            ),
                          ),
                      ],
                    ),
                  ),
                ],
              ],
            ],
          ),
        ),
      ],
    );
  }
}

class _TutorGrades extends StatefulWidget {
  const _TutorGrades({required this.data});
  final Map<String, dynamic> data;

  @override
  State<_TutorGrades> createState() => _TutorGradesState();
}

class _TutorGradesState extends State<_TutorGrades> {
  String? _selectedStudent;

  @override
  Widget build(BuildContext context) {
    final students = (widget.data['students'] as List).cast<Map<String, dynamic>>();
    if (students.isEmpty) {
      return const EmptyState(
        icon: Icons.grade_outlined,
        title: 'No tienes estudiantes tutorizados',
      );
    }

    final filteredStudents = students.where((s) {
      return _selectedStudent == null || s['nombreEstudiante'] == _selectedStudent;
    }).toList();

    return Column(
      children: [
        if (students.length > 1) ...[
          const SizedBox(height: Space.md),
          FilterBar(children: [
            FilterPill<String>(
              label: 'Hijo/a',
              value: _selectedStudent,
              options: [for (final s in students) (s['nombreEstudiante'] as String, s['nombreEstudiante'] as String)],
              onChanged: (v) => setState(() => _selectedStudent = v),
            ),
          ]),
        ],
        Expanded(
          child: ListView(
            padding: const EdgeInsets.fromLTRB(Space.xl, Space.lg, Space.xl, Space.xxxl),
            children: [
              for (final s in filteredStudents) ...[
                SectionLabel('${s['nombreEstudiante']} · ${s['parentesco'] ?? ''}'),
                for (final m in (s['modulos'] as List).cast<Map<String, dynamic>>()) _ModuloCard(m: m),
                const SizedBox(height: Space.md),
              ],
            ],
          ),
        ),
      ],
    );
  }
}
