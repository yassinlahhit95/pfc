import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/auth/auth_state.dart';
import '../../../core/auth/session.dart';
import '../../../core/widgets/async_view.dart';
import '../data/grades_repository.dart';

class GradesScreen extends ConsumerWidget {
  const GradesScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final gradesAsync = ref.watch(gradesProvider);
    final role = ref.watch(sessionControllerProvider).valueOrNull?.role;

    return Scaffold(
      appBar: AppBar(title: const Text('Notas')),
      body: AsyncView<Map<String, dynamic>>(
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
    );
  }
}

String _fmtNota(dynamic n) => n == null ? '—' : n.toString();

// Matches the web app's .texto-estado convention: verde >=5, rojo <5, gris sin nota.
Color _notaColor(dynamic raw) {
  final n = double.tryParse(raw?.toString() ?? '');
  if (n == null) return const Color(0xFF9AA6BC);
  return n >= 5 ? const Color(0xFF10B981) : const Color(0xFFEF4444);
}

class _ModuloCard extends StatelessWidget {
  const _ModuloCard({required this.m});
  final Map<String, dynamic> m;

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
      child: Padding(
        padding: const EdgeInsets.all(14),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(m['nombreModulo'] as String? ?? '',
                style: Theme.of(context).textTheme.titleSmall?.copyWith(fontWeight: FontWeight.bold)),
            const SizedBox(height: 10),
            Wrap(
              spacing: 10,
              runSpacing: 8,
              children: [
                _NotaChip('1ª ev.', m['nota_1ev']),
                _NotaChip('1ª final', m['nota_1final']),
                _NotaChip('2ª ev.', m['nota_2ev']),
                _NotaChip('2ª final', m['nota_2final']),
              ],
            ),
          ],
        ),
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
    final color = _notaColor(rawValue);
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.12),
        borderRadius: BorderRadius.circular(10),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Text('$label ', style: Theme.of(context).textTheme.bodySmall),
          Text(
            _fmtNota(rawValue),
            style: TextStyle(color: color, fontWeight: FontWeight.bold),
          ),
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
      padding: const EdgeInsets.symmetric(vertical: 8),
      children: [
        if (modulos.isNotEmpty) ...[
          const _SectionHeader('Módulos'),
          for (final m in modulos) _ModuloCard(m: m),
        ],
        if (retos.isNotEmpty) ...[
          const _SectionHeader('Retos'),
          for (final r in retos)
            Card(
              margin: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
              child: ListTile(
                title: Text(r['nombreReto'] as String? ?? ''),
                trailing: Text(_fmtNota(r['nota'])),
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
      padding: const EdgeInsets.symmetric(vertical: 8),
      children: [
        for (final m in modulos) ...[
          _SectionHeader(m['nombreModulo'] as String? ?? ''),
          for (final e
              in (m['estudiantes'] as List).cast<Map<String, dynamic>>())
            Card(
              margin: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
              child: ExpansionTile(
                title: Text(e['nombreEstudiante'] as String? ?? ''),
                children: [
                  Padding(
                    padding: const EdgeInsets.fromLTRB(16, 0, 16, 12),
                    child: Wrap(
                      spacing: 10,
                      runSpacing: 8,
                      children: [
                        _NotaChip('1ª ev.', e['nota_1ev']),
                        _NotaChip('1ª final', e['nota_1final']),
                        _NotaChip('2ª ev.', e['nota_2ev']),
                        _NotaChip('2ª final', e['nota_2final']),
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
      padding: const EdgeInsets.symmetric(vertical: 8),
      children: [
        for (final s in students) ...[
          _SectionHeader('${s['nombreEstudiante']} (${s['parentesco'] ?? ''})'),
          for (final m in (s['modulos'] as List).cast<Map<String, dynamic>>())
            _ModuloCard(m: m),
        ],
      ],
    );
  }
}

class _SectionHeader extends StatelessWidget {
  const _SectionHeader(this.title);
  final String title;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 4),
      child: Text(title, style: Theme.of(context).textTheme.titleMedium),
    );
  }
}
