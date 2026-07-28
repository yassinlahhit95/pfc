import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/profile_detail_sheet.dart';
import '../data/teachers_repository.dart';

class TeachersScreen extends StatefulWidget {
  const TeachersScreen({super.key});

  @override
  State<TeachersScreen> createState() => _TeachersScreenState();
}

class _TeachersScreenState extends State<TeachersScreen> {
  late ScrollController _scrollController;
  String _searchQuery = '';
  String? _selectedStatus;
  int _currentOffset = 0;
  static const int _pageSize = 20;

  @override
  void initState() {
    super.initState();
    _scrollController = ScrollController();
  }

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Profesores')),
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
              if (data.teachers.isEmpty) {
                return const EmptyState(icon: Icons.school_outlined, title: 'Sin profesores registrados');
              }

              return Column(
                children: [
                  Padding(
                    padding: const EdgeInsets.all(Space.md),
                    child: Column(
                      children: [
                        // Search bar
                        TextField(
                          onChanged: (value) {
                            setState(() {
                              _searchQuery = value;
                              _currentOffset = 0;
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
                        const SizedBox(height: Space.md),
                      ],
                    ),
                  ),
                  Expanded(
                    child: ListView.separated(
                      controller: _scrollController,
                      itemCount: data.teachers.length,
                      separatorBuilder: (_, __) => const SizedBox(height: Space.sm),
                      itemBuilder: (context, index) {
                        final teacher = data.teachers[index];
                        return _TeacherCard(teacher: teacher);
                      },
                    ),
                  ),
                  if (data.total > _currentOffset + _pageSize)
                    Padding(
                      padding: const EdgeInsets.all(Space.md),
                      child: ElevatedButton.icon(
                        onPressed: () => setState(() => _currentOffset += _pageSize),
                        icon: const Icon(Icons.arrow_downward),
                        label: const Text('Cargar más'),
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

class _FilterChip extends StatelessWidget {
  const _FilterChip({required this.label, required this.isSelected, required this.onPressed});
  final String label;
  final bool isSelected;
  final VoidCallback onPressed;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return FilterChip(
      label: Text(label),
      selected: isSelected,
      onSelected: (_) => onPressed(),
      backgroundColor: scheme.surfaceContainerHighest,
      selectedColor: scheme.primary,
      labelStyle: TextStyle(
        color: isSelected ? scheme.onPrimary : scheme.onSurfaceVariant,
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
