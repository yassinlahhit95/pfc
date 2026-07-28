import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/filter_bar.dart';
import '../../../core/widgets/profile_detail_sheet.dart';
import '../data/students_repository.dart';

class StudentsScreen extends StatefulWidget {
  const StudentsScreen({super.key});

  @override
  State<StudentsScreen> createState() => _StudentsScreenState();
}

class _StudentsScreenState extends State<StudentsScreen> {
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
      appBar: AppBar(title: const Text('Alumnos')),
      body: Consumer(
        builder: (context, ref, _) {
          final studentsAsync = ref.watch(
            studentsProvider(
              (
                limit: _pageSize,
                offset: _currentOffset,
                cicloId: null,
                status: _selectedStatus,
                query: _searchQuery.isNotEmpty ? _searchQuery : null,
              ),
            ),
          );

          return AsyncView<({List<Student> students, int total})>(
            value: studentsAsync,
            onRetry: () => ref.invalidate(
              studentsProvider(
                (
                  limit: _pageSize,
                  offset: _currentOffset,
                  cicloId: null,
                  status: _selectedStatus,
                  query: _searchQuery.isNotEmpty ? _searchQuery : null,
                ),
              ),
            ),
            data: (context, data) {
              if (data.students.isEmpty) {
                return const EmptyState(icon: Icons.people_outlined, title: 'Sin alumnos registrados');
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
                        // Status filter
                        SizedBox(
                          height: 36,
                          child: ListView(
                            scrollDirection: Axis.horizontal,
                            children: [
                              _FilterChip(
                                label: 'Todos',
                                isSelected: _selectedStatus == null,
                                onPressed: () => setState(() {
                                  _selectedStatus = null;
                                  _currentOffset = 0;
                                }),
                              ),
                              const SizedBox(width: Space.sm),
                              _FilterChip(
                                label: 'Activos',
                                isSelected: _selectedStatus == 'activo',
                                onPressed: () => setState(() {
                                  _selectedStatus = 'activo';
                                  _currentOffset = 0;
                                }),
                              ),
                              const SizedBox(width: Space.sm),
                              _FilterChip(
                                label: 'Inactivos',
                                isSelected: _selectedStatus == 'inactivo',
                                onPressed: () => setState(() {
                                  _selectedStatus = 'inactivo';
                                  _currentOffset = 0;
                                }),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                  Expanded(
                    child: ListView.separated(
                      controller: _scrollController,
                      itemCount: data.students.length,
                      separatorBuilder: (_, __) => const SizedBox(height: Space.sm),
                      itemBuilder: (context, index) {
                        final student = data.students[index];
                        return _StudentCard(student: student);
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

class _StudentCard extends StatelessWidget {
  const _StudentCard({required this.student});
  final Student student;

  Color _getStatusColor(BuildContext context) {
    final dark = Theme.of(context).brightness == Brightness.dark;
    return student.estado == 'activo'
        ? (dark ? AppColors.verdeDark : AppColors.verdeLight)
        : (dark ? AppColors.rojoDark : AppColors.rojoLight);
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final textTheme = Theme.of(context).textTheme;

    return InkWell(
      onTap: () {
        ProfileDetailSheet.show(
          context,
          uid: student.id,
          rol: 'estudiante',
          nombre: student.nombre,
          email: student.email,
          telefono: student.telefono,
          subtitle: '${student.course} - ${student.year ?? ''} (${student.abreviaturaCiclo})',
          status: student.estado == 'activo' ? 'Activo' : 'Inactivo',
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
                  student.nombre.isNotEmpty ? student.nombre[0].toUpperCase() : '?',
                  style: textTheme.headlineSmall?.copyWith(color: scheme.primary),
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
                      style: textTheme.bodySmall?.copyWith(color: scheme.onSurfaceVariant),
                    ),
                  ],
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(
                  color: _getStatusColor(context).withValues(alpha: 0.15),
                  borderRadius: BorderRadius.circular(Radii.pill),
                ),
                child: Text(
                  student.estado == 'activo' ? 'Activo' : 'Inactivo',
                  style: textTheme.labelSmall?.copyWith(
                    color: _getStatusColor(context),
                  ),
                ),
              ),
            ],
          ),
          if (student.year != null || student.course != null) ...[
            const SizedBox(height: Space.sm),
            Text(
              '${student.course ?? 'N/A'} - ${student.year ?? ''}',
              style: textTheme.bodySmall?.copyWith(color: scheme.onSurfaceVariant),
            ),
          ],
        ],
      ),
      ),
    );
  }
}
