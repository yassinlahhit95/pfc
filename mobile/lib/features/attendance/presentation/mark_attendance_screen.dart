import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../classroom/data/classroom_repository.dart';
import '../data/attendance_repository.dart';

class MarkAttendanceScreen extends ConsumerStatefulWidget {
  const MarkAttendanceScreen({super.key});

  @override
  ConsumerState<MarkAttendanceScreen> createState() => _MarkAttendanceScreenState();
}

class _MarkAttendanceScreenState extends ConsumerState<MarkAttendanceScreen> {
  ClassroomModule? _selectedModule;
  DateTime _selectedDate = DateTime.now();
  final Map<int, String> _estados = {};
  bool _loading = false;
  bool _saving = false;

  String get _fechaStr => DateFormat('yyyy-MM-dd').format(_selectedDate);

  Future<void> _loadRoster() async {
    if (_selectedModule == null) return;
    setState(() => _loading = true);
    try {
      final result =
          await ref.read(attendanceRepositoryProvider).fetchForModule(_selectedModule!.id, fecha: _fechaStr);
      final byStudent = {for (final a in result.attendance) a.idEstudiante: a.estado};
      setState(() {
        _estados
          ..clear()
          ..addAll({for (final r in result.roster) r.id: byStudent[r.id] ?? 'presente'});
        _roster = result.roster;
        _loading = false;
      });
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error al cargar el registro: $e')),
        );
        setState(() => _loading = false);
      }
    }
  }

  List<RosterStudent> _roster = [];

  Future<void> _submit() async {
    if (_selectedModule == null) return;
    setState(() => _saving = true);
    try {
      await ref.read(attendanceRepositoryProvider).submitAttendance(
            idModulo: _selectedModule!.id,
            fecha: _fechaStr,
            registros: [
              for (final entry in _estados.entries)
                {'idEstudiante': entry.key, 'estado': entry.value},
            ],
          );
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Asistencia guardada.')));
        ref.invalidate(classroomModulesProvider);
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('No se pudo guardar: $e')));
      }
    } finally {
      if (mounted) setState(() => _saving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final modulesAsync = ref.watch(classroomModulesProvider);

    return AsyncView<List<ClassroomModule>>(
      value: modulesAsync,
      onRetry: () => ref.invalidate(classroomModulesProvider),
      data: (context, modules) {
        return Padding(
          padding: const EdgeInsets.all(Space.xl),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Row(
                children: [
                  Expanded(
                    child: DropdownButtonFormField<ClassroomModule>(
                      isExpanded: true,
                      initialValue: _selectedModule,
                      decoration: const InputDecoration(labelText: 'Módulo'),
                      items: [
                        for (final m in modules)
                          DropdownMenuItem(
                            value: m,
                            child: Text(m.nombre, overflow: TextOverflow.ellipsis, maxLines: 1),
                          ),
                      ],
                      onChanged: (m) {
                        setState(() => _selectedModule = m);
                        _loadRoster();
                      },
                    ),
                  ),
                  const SizedBox(width: Space.sm),
                  OutlinedButton.icon(
                    onPressed: () async {
                      final picked = await showDatePicker(
                        context: context,
                        initialDate: _selectedDate,
                        firstDate: DateTime.now().subtract(const Duration(days: 365)),
                        lastDate: DateTime.now(),
                      );
                      if (picked != null) {
                        setState(() => _selectedDate = picked);
                        _loadRoster();
                      }
                    },
                    icon: const Icon(Icons.calendar_today_outlined, size: 20),
                    label: Text(DateFormat('d/MM').format(_selectedDate)),
                  ),
                ],
              ),
              const SizedBox(height: Space.lg),
              Expanded(
                child: _loading
                    ? const Center(child: CircularProgressIndicator(strokeWidth: 2.4))
                    : _selectedModule == null
                        ? const EmptyState(icon: Icons.checklist_rounded, title: 'Elige un módulo y una fecha')
                        : _roster.isEmpty
                            ? const EmptyState(icon: Icons.people_outline, title: 'Sin alumnos en este módulo')
                            : ListView.separated(
                                itemCount: _roster.length,
                                separatorBuilder: (_, __) =>
                                    Divider(height: 1, color: Theme.of(context).colorScheme.outlineVariant),
                                itemBuilder: (context, i) {
                                  final s = _roster[i];
                                  return _StudentRow(
                                    student: s,
                                    estado: _estados[s.id] ?? 'presente',
                                    onChanged: (v) => setState(() => _estados[s.id] = v),
                                  );
                                },
                              ),
              ),
              if (_selectedModule != null && _roster.isNotEmpty) ...[
                const SizedBox(height: Space.lg),
                FilledButton(
                  onPressed: _saving ? null : _submit,
                  child: _saving
                      ? SizedBox(
                          height: 18,
                          width: 18,
                          child: CircularProgressIndicator(
                            strokeWidth: 2,
                            valueColor: AlwaysStoppedAnimation<Color>(Theme.of(context).colorScheme.onPrimary),
                          ),
                        )
                      : const Text('Guardar asistencia'),
                ),
              ],
            ],
          ),
        );
      },
    );
  }
}

class _StudentRow extends StatelessWidget {
  const _StudentRow({required this.student, required this.estado, required this.onChanged});
  final RosterStudent student;
  final String estado;
  final ValueChanged<String> onChanged;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: Space.sm),
      child: Row(
        children: [
          Expanded(child: Text(student.nombre, overflow: TextOverflow.ellipsis, style: const TextStyle(fontWeight: FontWeight.w500))),
          SegmentedButton<String>(
            segments: const [
              ButtonSegment(value: 'presente', label: Text('P')),
              ButtonSegment(value: 'retraso', label: Text('R')),
              ButtonSegment(value: 'ausente', label: Text('A')),
            ],
            selected: {estado},
            onSelectionChanged: (s) => onChanged(s.first),
            showSelectedIcon: false,
            style: const ButtonStyle(visualDensity: VisualDensity.compact),
          ),
        ],
      ),
    );
  }
}
