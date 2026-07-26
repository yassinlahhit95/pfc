import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/auth/auth_state.dart';
import '../../../core/auth/session.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
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

class _EstudianteGrades extends StatelessWidget {
  const _EstudianteGrades({required this.data});
  final Map<String, dynamic> data;

  @override
  Widget build(BuildContext context) {
    final modulos = (data['modulos'] as List).cast<Map<String, dynamic>>();
    final retos = (data['retos'] as List).cast<Map<String, dynamic>>();
    if (modulos.isEmpty && retos.isEmpty) {
      return const EmptyState(
        icon: Icons.grade_outlined,
        title: 'Todavía no hay notas registradas',
      );
    }
    return ListView(
      padding: const EdgeInsets.fromLTRB(Space.xl, Space.lg, Space.xl, Space.xxxl),
      children: [
        if (modulos.isNotEmpty) ...[
          const SectionLabel('Módulos'),
          for (final m in modulos) _ModuloCard(m: m),
          const SizedBox(height: Space.md),
        ],
        if (retos.isNotEmpty) ...[
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
    );
  }
}

class _ProfesorGrades extends StatelessWidget {
  const _ProfesorGrades({required this.data});
  final Map<String, dynamic> data;

  @override
  Widget build(BuildContext context) {
    final modulos = (data['modulos'] as List).cast<Map<String, dynamic>>();
    if (modulos.isEmpty) {
      return const EmptyState(
        icon: Icons.grade_outlined,
        title: 'No impartes ningún módulo con alumnado',
      );
    }
    return ListView(
      padding: const EdgeInsets.fromLTRB(Space.xl, Space.lg, Space.xl, Space.xxxl),
      children: [
        for (final m in modulos) ...[
          SectionLabel(m['nombreModulo'] as String? ?? ''),
          AppCard(
            padding: EdgeInsets.zero,
            margin: const EdgeInsets.only(bottom: Space.xxl),
            child: Column(
              children: [
                for (final e in (m['estudiantes'] as List).cast<Map<String, dynamic>>())
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
    );
  }
}

class _TutorGrades extends StatelessWidget {
  const _TutorGrades({required this.data});
  final Map<String, dynamic> data;

  @override
  Widget build(BuildContext context) {
    final students = (data['students'] as List).cast<Map<String, dynamic>>();
    if (students.isEmpty) {
      return const EmptyState(
        icon: Icons.grade_outlined,
        title: 'No tienes estudiantes tutorizados',
      );
    }
    return ListView(
      padding: const EdgeInsets.fromLTRB(Space.xl, Space.lg, Space.xl, Space.xxxl),
      children: [
        for (final s in students) ...[
          SectionLabel('${s['nombreEstudiante']} · ${s['parentesco'] ?? ''}'),
          for (final m in (s['modulos'] as List).cast<Map<String, dynamic>>()) _ModuloCard(m: m),
          const SizedBox(height: Space.md),
        ],
      ],
    );
  }
}
