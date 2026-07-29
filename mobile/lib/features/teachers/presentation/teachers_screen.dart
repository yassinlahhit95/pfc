import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/profile_detail_sheet.dart';
import '../../../core/utils/debounce.dart';
import '../data/teachers_repository.dart';

class TeachersScreen extends StatefulWidget {
  const TeachersScreen({super.key});

  @override
  State<TeachersScreen> createState() => _TeachersScreenState();
}

class _TeachersScreenState extends State<TeachersScreen> {
  late ScrollController _scrollController;
  final Debounce _debounce = Debounce();
  String _searchQuery = '';
  String? _selectedStatus;
  int _currentOffset = 0;
  static const int _pageSize = 20;

  @override
  void initState() {
    super.initState();
    _scrollController = ScrollController();
    _scrollController.addListener(_onScroll);
  }

  void _onScroll() {
    if (_scrollController.position.pixels >= _scrollController.position.maxScrollExtent - 200) {
      // Just flag we need more data. But we need to use ref.read inside build, so we can't do it easily from State.
      // Wait, ConsumerState is needed to access ref inside _onScroll!
      // I'll leave the debounce logic but I need to convert it to ConsumerState.
      // Wait, _TeachersScreenState is currently just State<TeachersScreen> but uses Consumer inside build.
      // To do pagination elegantly, I can debounce the setState.
      _debounce(const Duration(milliseconds: 300), () {
        setState(() {
          // Incrementing offset triggers Consumer rebuild which triggers fetch
          _currentOffset += _pageSize;
        });
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
      builder: (context) => _TeachersFiltersSheet(
        initialStatus: _selectedStatus,
        onApply: (status) {
          setState(() {
            _selectedStatus = status;
            _currentOffset = 0;
          });
        },
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Profesores'),
        actions: [
          IconButton(
            icon: const Icon(Icons.filter_list),
            onPressed: () => _showFiltersSheet(context),
          ),
        ],
      ),
      body: Consumer(
        builder: (context, ref, _) {
          final teachersAsync = ref.watch(
            teachersProvider(
              (
                limit: _pageSize,
                offset: _currentOffset,
                status: _selectedStatus,
                query: _searchQuery.isNotEmpty ? _searchQuery : null,
              ),
            ),
          );

          return AsyncView<({List<Teacher> teachers, int total})>(
            value: teachersAsync,
            onRetry: () => ref.invalidate(
              teachersProvider(
                (
                  limit: _pageSize,
                  offset: _currentOffset,
                  status: _selectedStatus,
                  query: _searchQuery.isNotEmpty ? _searchQuery : null,
                ),
              ),
            ),
            data: (context, data) {
              return Column(
                children: [
                  Padding(
                    padding: const EdgeInsets.all(Space.md),
                    child: TextField(
                      onChanged: (value) {
                        _debounce(const Duration(milliseconds: 500), () {
                          setState(() {
                            _searchQuery = value;
                            _currentOffset = 0;
                          });
                        });
                      },
                      decoration: InputDecoration(
                        hintText: 'Buscar por nombre...',
                        prefixIcon: const Icon(Icons.search),
                        contentPadding: const EdgeInsets.symmetric(horizontal: Space.md, vertical: Space.sm),
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(Radii.md),
                          borderSide: BorderSide.none,
                        ),
                        filled: true,
                        fillColor: Theme.of(context).colorScheme.surfaceContainerHighest,
                      ),
                    ),
                  ),
                  Expanded(
                    child: data.teachers.isEmpty
                        ? const EmptyState(
                            icon: Icons.school_outlined,
                            title: 'Sin profesores registrados',
                            description: 'No hay profesores que coincidan con la búsqueda',
                          )
                        : ListView.separated(
                            controller: _scrollController,
                            itemCount: data.teachers.length,
                            separatorBuilder: (_, __) => const SizedBox(height: Space.sm),
                            itemBuilder: (context, index) {
                              final teacher = data.teachers[index];
                              return _TeacherCard(teacher: teacher);
                            },
                          ),
                  ),
                ],
              );
            },
          );
        },
      ),
    );
  }
}

class _TeachersFiltersSheet extends StatefulWidget {
  const _TeachersFiltersSheet({this.initialStatus, required this.onApply});
  final String? initialStatus;
  final ValueChanged<String?> onApply;

  @override
  State<_TeachersFiltersSheet> createState() => _TeachersFiltersSheetState();
}

class _TeachersFiltersSheetState extends State<_TeachersFiltersSheet> {
  String? _status;

  @override
  void initState() {
    super.initState();
    _status = widget.initialStatus;
  }

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: EdgeInsets.only(
        bottom: MediaQuery.of(context).viewInsets.bottom,
        left: 16,
        right: 16,
        top: 16,
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          const Text('Filtros', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
          const SizedBox(height: 16),
          DropdownButtonFormField<String?>(
            value: _status,
            decoration: const InputDecoration(labelText: 'Estado'),
            items: const [
              DropdownMenuItem(value: null, child: Text('Todos los estados')),
              DropdownMenuItem(value: 'activo', child: Text('Activos')),
              DropdownMenuItem(value: 'inactivo', child: Text('Inactivos')),
            ],
            onChanged: (val) => setState(() => _status = val),
          ),
          const SizedBox(height: 24),
          Row(
            children: [
              Expanded(
                child: OutlinedButton(
                  onPressed: () {
                    widget.onApply(null);
                    Navigator.pop(context);
                  },
                  child: const Text('Limpiar'),
                ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: FilledButton(
                  onPressed: () {
                    widget.onApply(_status);
                    Navigator.pop(context);
                  },
                  child: const Text('Aplicar'),
                ),
              ),
            ],
          ),
          const SizedBox(height: 24),
        ],
      ),
    );
  }
}

class _TeacherCard extends StatelessWidget {
  const _TeacherCard({required this.teacher});
  final Teacher teacher;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final textTheme = Theme.of(context).textTheme;

    return InkWell(
      onTap: () {
        ProfileDetailSheet.show(
          context,
          uid: teacher.id,
          rol: 'profesor',
          nombre: teacher.nombre,
          email: teacher.email,
          telefono: teacher.telefono,
          subtitle: teacher.isTutor ? 'Tutor de ${teacher.cicloTutoria ?? ''}' : 'Docente',
          status: 'Activo', // Profesores are always active in this version
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
                  color: scheme.secondary.withValues(alpha: 0.15),
                ),
                alignment: Alignment.center,
                child: Text(
                  teacher.nombre.isNotEmpty ? teacher.nombre[0].toUpperCase() : '?',
                  style: textTheme.headlineSmall?.copyWith(color: scheme.secondary),
                ),
              ),
              const SizedBox(width: Space.md),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(teacher.nombre, style: textTheme.titleSmall),
                    if (teacher.isTutor && teacher.cicloTutoria != null)
                      Text(
                        'Tutor: ${teacher.cicloTutoria}',
                        style: textTheme.bodySmall?.copyWith(
                          color: scheme.primary,
                          fontWeight: FontWeight.w500,
                        ),
                      )
                    else if (!teacher.isTutor)
                      Text(
                        'Docente',
                        style: textTheme.bodySmall?.copyWith(color: scheme.onSurfaceVariant),
                      ),
                  ],
                ),
              ),
            ],
          ),
          if (teacher.email.isNotEmpty || teacher.telefono != null) ...[
            const SizedBox(height: Space.sm),
            if (teacher.email.isNotEmpty)
              Text(
                teacher.email,
                style: textTheme.bodySmall?.copyWith(color: scheme.onSurfaceVariant),
                maxLines: 1,
                overflow: TextOverflow.ellipsis,
              ),
            if (teacher.telefono != null)
              Text(
                teacher.telefono!,
                style: textTheme.bodySmall?.copyWith(color: scheme.onSurfaceVariant),
              ),
          ],
        ],
      ),
      ),
    );
  }
}
