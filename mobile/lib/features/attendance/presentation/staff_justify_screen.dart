import 'dart:async';

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';

import '../../../core/auth/auth_state.dart';
import '../../../core/auth/session.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/error_modal.dart';
import '../../../core/widgets/premium.dart';
import '../../../core/i18n/translations.dart';
import '../../chat/data/chat_repository.dart';
import '../../classroom/data/classroom_repository.dart';
import '../data/attendance_repository.dart';
import 'justify_sheet.dart';

/// Lets profesor/secretaría/director photograph an absence-justification
/// note on a student's behalf (e.g. a parent hands it in physically) —
/// server-side this auto-approves immediately since staff already validated
/// the proof in person. Profesor is scoped to their own módulos; secretaría/
/// director can search any student (matches their web-wide access).
class StaffJustifyScreen extends ConsumerWidget {
  const StaffJustifyScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final role = ref.watch(sessionControllerProvider).value?.role;
    final t = ref.watch(translationsProvider);
    return Scaffold(
      appBar: AppBar(
          title: Text(t['title_justificar_falta'] ?? 'Justificar falta')),
      body: role == UserRole.profesor
          ? const _ProfesorPicker()
          : const _StudentSearchPicker(),
    );
  }
}

class _JustifiableCard extends StatelessWidget {
  const _JustifiableCard({required this.record, required this.onTap});
  final AttendanceRecord record;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    final date = DateTime.tryParse(record.fecha);
    final scheme = Theme.of(context).colorScheme;
    return AppCard(
      margin: const EdgeInsets.only(bottom: Space.sm),
      onTap: onTap,
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(record.nombreEstudiante,
                    style: const TextStyle(fontWeight: FontWeight.w600)),
                const SizedBox(height: 2),
                Text(
                  '${record.nombreModulo} · ${date != null ? DateFormat('d MMM yyyy').format(date) : record.fecha}',
                  style: Theme.of(context).textTheme.bodySmall,
                ),
              ],
            ),
          ),
          StatusPill(
            label: record.estado == 'ausente' ? 'Ausente' : 'Retraso',
            color: record.estado == 'ausente'
                ? AppColors.rojoLight
                : AppColors.naranjaLight,
          ),
          const SizedBox(width: Space.sm),
          Icon(Icons.chevron_right_rounded,
              color: scheme.onSurfaceVariant.withValues(alpha: 0.5)),
        ],
      ),
    );
  }
}

class _ProfesorPicker extends ConsumerStatefulWidget {
  const _ProfesorPicker();

  @override
  ConsumerState<_ProfesorPicker> createState() => _ProfesorPickerState();
}

class _ProfesorPickerState extends ConsumerState<_ProfesorPicker> {
  ClassroomModule? _selectedModule;
  List<AttendanceRecord>? _records;
  bool _loading = false;

  Future<void> _loadRecords(ClassroomModule modulo) async {
    setState(() {
      _selectedModule = modulo;
      _loading = true;
    });
    try {
      final result = await ref
          .read(attendanceRepositoryProvider)
          .fetchForModule(modulo.id);
      if (mounted) {
        setState(() =>
            _records = result.attendance.where((r) => r.canJustify).toList());
      }
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _justify(AttendanceRecord record) async {
    final sent = await showJustifySheet(
      context,
      ref,
      idAsistencia: record.id,
      subtitulo:
          '${record.nombreEstudiante} · ${record.nombreModulo} · ${record.fecha}',
    );
    if (sent && _selectedModule != null) {
      if (mounted) {
        await showErrorAlert(context, 'Justificación registrada y aprobada.',
            title: 'Éxito');
      }
      _loadRecords(_selectedModule!);
    }
  }

  @override
  Widget build(BuildContext context) {
    final modulesAsync = ref.watch(classroomModulesProvider);
    return AsyncView<List<ClassroomModule>>(
      value: modulesAsync,
      onRetry: () => ref.invalidate(classroomModulesProvider),
      data: (context, modules) => Padding(
        padding: const EdgeInsets.all(Space.xl),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            DropdownButtonFormField<ClassroomModule>(
              isExpanded: true,
              initialValue: _selectedModule,
              decoration: const InputDecoration(labelText: 'Módulo'),
              items: [
                for (final m in modules)
                  DropdownMenuItem(
                      value: m,
                      child: Text(m.nombre, overflow: TextOverflow.ellipsis)),
              ],
              onChanged: (m) {
                if (m != null) _loadRecords(m);
              },
            ),
            const SizedBox(height: Space.lg),
            if (_loading)
              const Padding(
                  padding: EdgeInsets.only(top: Space.xxl),
                  child: CircularProgressIndicator()),
            if (!_loading && _records != null)
              Expanded(
                child: _records!.isEmpty
                    ? const EmptyState(
                        icon: Icons.check_circle_outline,
                        title: 'Sin faltas por justificar en este módulo')
                    : ListView.builder(
                        itemCount: _records!.length,
                        itemBuilder: (context, i) => _JustifiableCard(
                            record: _records![i],
                            onTap: () => _justify(_records![i])),
                      ),
              ),
          ],
        ),
      ),
    );
  }
}

class _StudentSearchPicker extends ConsumerStatefulWidget {
  const _StudentSearchPicker();

  @override
  ConsumerState<_StudentSearchPicker> createState() =>
      _StudentSearchPickerState();
}

class _StudentSearchPickerState extends ConsumerState<_StudentSearchPicker> {
  final _searchController = TextEditingController();
  Timer? _debounce;
  List<ChatContact> _results = [];
  bool _searching = false;

  ChatContact? _selectedStudent;
  List<AttendanceRecord>? _records;
  bool _loadingRecords = false;

  @override
  void dispose() {
    _debounce?.cancel();
    _searchController.dispose();
    super.dispose();
  }

  void _onQueryChanged(String value) {
    _debounce?.cancel();
    _debounce = Timer(const Duration(milliseconds: 300), () => _search(value));
  }

  Future<void> _search(String query) async {
    setState(() => _searching = true);
    try {
      final contacts =
          await ref.read(chatRepositoryProvider).fetchContacts(query: query);
      if (mounted) {
        setState(() =>
            _results = contacts.where((c) => c.rol == 'estudiante').toList());
      }
    } catch (_) {
      // keep previous results on error
    } finally {
      if (mounted) setState(() => _searching = false);
    }
  }

  Future<void> _selectStudent(ChatContact student) async {
    setState(() {
      _selectedStudent = student;
      _loadingRecords = true;
    });
    try {
      final records = await ref
          .read(attendanceRepositoryProvider)
          .fetchForStudent(student.uid);
      if (mounted) {
        setState(() => _records = records.where((r) => r.canJustify).toList());
      }
    } finally {
      if (mounted) setState(() => _loadingRecords = false);
    }
  }

  Future<void> _justify(AttendanceRecord record) async {
    final sent = await showJustifySheet(
      context,
      ref,
      idAsistencia: record.id,
      subtitulo: '${record.nombreModulo} · ${record.fecha}',
    );
    if (sent && _selectedStudent != null) {
      if (mounted) {
        await showErrorAlert(context, 'Justificación registrada y aprobada.',
            title: 'Éxito');
      }
      _selectStudent(_selectedStudent!);
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_selectedStudent != null) {
      return Padding(
        padding: const EdgeInsets.all(Space.xl),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Row(
              children: [
                IconButton(
                  icon: const Icon(Icons.arrow_back),
                  onPressed: () => setState(() {
                    _selectedStudent = null;
                    _records = null;
                  }),
                ),
                Expanded(
                  child: Text(_selectedStudent!.nombre,
                      style: Theme.of(context).textTheme.titleMedium),
                ),
              ],
            ),
            const SizedBox(height: Space.md),
            if (_loadingRecords)
              const Padding(
                  padding: EdgeInsets.only(top: Space.xxl),
                  child: CircularProgressIndicator()),
            if (!_loadingRecords && _records != null)
              Expanded(
                child: _records!.isEmpty
                    ? const EmptyState(
                        icon: Icons.check_circle_outline,
                        title: 'Sin faltas por justificar')
                    : ListView.builder(
                        itemCount: _records!.length,
                        itemBuilder: (context, i) => _JustifiableCard(
                            record: _records![i],
                            onTap: () => _justify(_records![i])),
                      ),
              ),
          ],
        ),
      );
    }

    return Padding(
      padding: const EdgeInsets.all(Space.xl),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          TextField(
            controller: _searchController,
            autocorrect: false,
            decoration: const InputDecoration(
              labelText: 'Buscar estudiante',
              prefixIcon: Icon(Icons.search),
            ),
            onChanged: _onQueryChanged,
          ),
          const SizedBox(height: Space.lg),
          if (_searching) const LinearProgressIndicator(),
          Expanded(
            child: _results.isEmpty
                ? const EmptyState(
                    icon: Icons.person_search_outlined,
                    title: 'Busca un estudiante por nombre')
                : ListView.builder(
                    itemCount: _results.length,
                    itemBuilder: (context, i) {
                      final student = _results[i];
                      return AppCard(
                        margin: const EdgeInsets.only(bottom: Space.sm),
                        onTap: () => _selectStudent(student),
                        child: Row(
                          children: [
                            Expanded(
                                child: Text(student.nombre,
                                    style: const TextStyle(
                                        fontWeight: FontWeight.w600))),
                            const Icon(Icons.chevron_right_rounded),
                          ],
                        ),
                      );
                    },
                  ),
          ),
        ],
      ),
    );
  }
}
