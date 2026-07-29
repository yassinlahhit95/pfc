import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/profile_detail_sheet.dart';
import '../../../core/utils/debounce.dart';
import '../data/teachers_repository.dart';

class TeachersScreen extends ConsumerStatefulWidget {
  const TeachersScreen({super.key});

  @override
  ConsumerState<TeachersScreen> createState() => _TeachersScreenState();
}

class _TeachersScreenState extends ConsumerState<TeachersScreen> {
  late ScrollController _scrollController;
  final Debounce _debounce = Debounce();
  String _searchQuery = '';
  // Removed: _selectedStatus — all teachers are active (no status filter exists in DB)
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
      // Debounce pagination to avoid rapid multiple increments
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

  @override
  Widget build(BuildContext context) {
    final teachersAsync = ref.watch(
      teachersProvider(
        (
          limit: _pageSize,
          offset: _currentOffset,
          query: _searchQuery.isNotEmpty ? _searchQuery : null,
        ),
      ),
    );

    return Scaffold(
      appBar: AppBar(
        title: const Text('Profesores'),
        // Filter action removed — teachers have no status field; all are active
      ),
      body: AsyncView<({List<Teacher> teachers, int total})>(
        value: teachersAsync,
        onRetry: () => ref.invalidate(
          teachersProvider(
            (
              limit: _pageSize,
              offset: _currentOffset,
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
