import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/filter_bar.dart';
import '../../../core/widgets/profile_detail_sheet.dart';
import '../../../core/utils/debounce.dart';
import '../data/students_repository.dart';
import '../../attendance/presentation/center_attendance_screen.dart' show lookupsProvider;

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

final studentsFiltersProvider = StateProvider<StudentsFilters>((ref) => const StudentsFilters());

class StudentsScreen extends ConsumerStatefulWidget {
  const StudentsScreen({super.key});

  @override
  ConsumerState<StudentsScreen> createState() => _StudentsScreenState();
}

class _StudentsScreenState extends ConsumerState<StudentsScreen> {
  late ScrollController _scrollController;
  final Debounce _debounce = Debounce();
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
      final filters = ref.read(studentsFiltersProvider);
      final asyncData = ref.read(studentsProvider(
        (
          limit: _pageSize,
          offset: _currentOffset,
          cicloId: filters.ciclo,
          nivelId: filters.nivel,
          status: filters.estado,
          query: filters.q,
        ),
      ));
      
      if (asyncData.hasValue && asyncData.value != null) {
        final total = asyncData.value!.total;
        if (total > _currentOffset + _pageSize) {
          // Debounce the load to prevent rapid multiple increments
          _debounce(const Duration(milliseconds: 300), () {
            setState(() => _currentOffset += _pageSize);
          });
        }
      }
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
    final filters = ref.watch(studentsFiltersProvider);
    final studentsAsync = ref.watch(
      studentsProvider(
        (
          limit: _pageSize,
          offset: _currentOffset,
          cicloId: filters.ciclo,
          nivelId: filters.nivel,
          status: filters.estado,
          query: filters.q,
        ),
      ),
    );

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
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(Space.md),
            child: TextField(
              onChanged: (value) {
                _debounce(const Duration(milliseconds: 500), () {
                  ref.read(studentsFiltersProvider.notifier).state = filters.copyWith(q: value);
                  setState(() => _currentOffset = 0);
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
            child: AsyncView<({List<Student> students, int total})>(
              value: studentsAsync,
              onRetry: () => ref.invalidate(studentsProvider),
              data: (context, data) {
                if (data.students.isEmpty) {
                  return const EmptyState(icon: Icons.people_outlined, title: 'Sin alumnos registrados');
                }
                return Column(
                  children: [
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
  ConsumerState<_StudentsFiltersSheet> createState() => _StudentsFiltersSheetState();
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
        loading: () => const SizedBox(height: 200, child: Center(child: CircularProgressIndicator())),
        error: (e, st) => const SizedBox(height: 200, child: Center(child: Text('Error cargando filtros'))),
        data: (lookups) {
          final niveles = lookups['niveles'] as List? ?? [];
          final ciclos = lookups['ciclos'] as List? ?? [];

          return Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              const Text('Filtros', style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold)),
              const SizedBox(height: 16),
              DropdownButtonFormField<int?>(
                value: _nivel,
                decoration: const InputDecoration(labelText: 'Nivel'),
                items: [
                  const DropdownMenuItem(value: null, child: Text('Todos los niveles')),
                  ...niveles.map((n) => DropdownMenuItem(value: n['idNivel'] as int, child: Text(n['nombreNivel']))),
                ],
                onChanged: (val) => setState(() => _nivel = val),
              ),
              const SizedBox(height: 16),
              DropdownButtonFormField<int?>(
                value: _ciclo,
                decoration: const InputDecoration(labelText: 'Ciclo'),
                items: [
                  const DropdownMenuItem(value: null, child: Text('Todos los ciclos')),
                  ...ciclos.map((c) => DropdownMenuItem(value: c['idCiclo'] as int, child: Text(c['nombreCiclo']))),
                ],
                onChanged: (val) => setState(() => _ciclo = val),
              ),
              const SizedBox(height: 16),
              DropdownButtonFormField<String?>(
                value: _estado,
                decoration: const InputDecoration(labelText: 'Estado'),
                items: const [
                  DropdownMenuItem(value: null, child: Text('Todos los estados')),
                  DropdownMenuItem(value: 'activo', child: Text('Activos')),
                  DropdownMenuItem(value: 'inactivo', child: Text('Inactivos')),
                ],
                onChanged: (val) => setState(() => _estado = val),
              ),
              const SizedBox(height: 24),
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton(
                      onPressed: () {
                        ref.read(studentsFiltersProvider.notifier).state = const StudentsFilters();
                        Navigator.pop(context);
                      },
                      child: const Text('Limpiar'),
                    ),
                  ),
                  const SizedBox(width: 16),
                  Expanded(
                    child: FilledButton(
                      onPressed: () {
                        final currentFilters = ref.read(studentsFiltersProvider);
                        ref.read(studentsFiltersProvider.notifier).state = currentFilters.copyWith(
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
