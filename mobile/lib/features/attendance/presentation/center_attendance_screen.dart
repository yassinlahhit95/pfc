import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:flutter_riverpod/legacy.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/i18n/translations.dart';
import '../data/attendance_repository.dart';
import 'justify_sheet.dart';

// Providers for lookups
final lookupsProvider = FutureProvider.autoDispose((ref) {
  return ref.read(attendanceRepositoryProvider).fetchLookups();
});

// State for filters
class AttendanceFilters {
  const AttendanceFilters({
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

  AttendanceFilters copyWith({
    int? nivel,
    int? ciclo,
    String? anio,
    int? grupo,
    String? estado,
    String? q,
  }) {
    return AttendanceFilters(
      nivel: nivel ?? this.nivel,
      ciclo: ciclo ?? this.ciclo,
      anio: anio ?? this.anio,
      grupo: grupo ?? this.grupo,
      estado: estado ?? this.estado,
      q: q ?? this.q,
    );
  }
}

final attendanceFiltersProvider =
    StateProvider<AttendanceFilters>((ref) => const AttendanceFilters());

// Provider for fetching list
final centerAttendanceProvider = FutureProvider.autoDispose<
    ({List<AttendanceRecord> attendance, int total})>((ref) {
  final filters = ref.watch(attendanceFiltersProvider);
  return ref.read(attendanceRepositoryProvider).fetchCenterAttendance(
        nivel: filters.nivel,
        ciclo: filters.ciclo,
        anio: filters.anio,
        grupo: filters.grupo,
        estado: filters.estado,
        q: filters.q,
      );
});

class CenterAttendanceScreen extends ConsumerWidget {
  const CenterAttendanceScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final filters = ref.watch(attendanceFiltersProvider);
    final asyncData = ref.watch(centerAttendanceProvider);
    final scheme = Theme.of(context).colorScheme;
    final t = ref.watch(translationsProvider);

    return Scaffold(
      appBar: AppBar(
        title: Text(t['nav_asistencias_centro'] ?? 'Asistencia Centro'),
        actions: [
          IconButton(
            icon: const Icon(Icons.filter_list),
            onPressed: () {
              _showFiltersSheet(context, ref);
            },
          ),
        ],
      ),
      body: Column(
        children: [
          Padding(
            padding: const EdgeInsets.all(Space.md),
            child: TextField(
              decoration: InputDecoration(
                hintText: 'Buscar alumno...',
                prefixIcon: const Icon(Icons.search),
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: BorderSide.none,
                ),
                filled: true,
                fillColor: scheme.surfaceContainerHighest,
              ),
              onChanged: (val) {
                ref.read(attendanceFiltersProvider.notifier).state =
                    filters.copyWith(q: val);
              },
            ),
          ),
          Expanded(
            child: filters.nivel == null && filters.ciclo == null
                ? EmptyState(
                    icon: Icons.filter_alt_outlined,
                    title: 'Selecciona filtros',
                    description:
                        'Debes seleccionar un Nivel o Ciclo para ver las asistencias.',
                    actionText: 'Abrir filtros',
                    onAction: () => _showFiltersSheet(context, ref),
                  )
                : AsyncView(
                    value: asyncData,
                    onRetry: () => ref.invalidate(centerAttendanceProvider),
                    data: (context, data) {
                      if (data.attendance.isEmpty) {
                        return const EmptyState(
                          icon: Icons.fact_check_outlined,
                          title: 'Sin registros',
                          description:
                              'No hay asistencias que coincidan con los filtros.',
                        );
                      }
                      return ListView.separated(
                        padding: const EdgeInsets.all(Space.md),
                        itemCount: data.attendance.length,
                        separatorBuilder: (_, __) =>
                            const SizedBox(height: Space.sm),
                        itemBuilder: (context, index) {
                          final record = data.attendance[index];
                          return _CenterAttendanceCard(record: record);
                        },
                      );
                    },
                  ),
          ),
        ],
      ),
    );
  }

  void _showFiltersSheet(BuildContext context, WidgetRef ref) {
    showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      builder: (context) {
        return const _FiltersSheet();
      },
    );
  }
}

class _FiltersSheet extends ConsumerStatefulWidget {
  const _FiltersSheet();

  @override
  ConsumerState<_FiltersSheet> createState() => _FiltersSheetState();
}

class _FiltersSheetState extends ConsumerState<_FiltersSheet> {
  int? _nivel;
  int? _ciclo;
  String? _anio;
  int? _grupo;
  String? _estado;

  @override
  void initState() {
    super.initState();
    final filters = ref.read(attendanceFiltersProvider);
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
        left: Space.md,
        right: Space.md,
        top: Space.md,
      ),
      child: lookupsAsync.when(
        loading: () => const SizedBox(
            height: 200, child: Center(child: CircularProgressIndicator())),
        error: (e, st) {
          final textTheme = Theme.of(context).textTheme;
          return SizedBox(
            height: 200,
            child: Center(
              child: Padding(
                padding: const EdgeInsets.all(16),
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Text('Error cargando filtros:',
                        style: textTheme.titleSmall),
                    const SizedBox(height: 8),
                    Text(e.toString(),
                        textAlign: TextAlign.center,
                        style: textTheme.bodySmall),
                  ],
                ),
              ),
            ),
          );
        },
        data: (lookups) {
          final nivelesData = lookups['niveles'] as List? ?? [];
          final ciclos = lookups['ciclos'] as List? ?? [];
          final activeNivelIds = ciclos.map((c) => c['idNivel'] as int).toSet();
          final niveles = nivelesData
              .where((n) => activeNivelIds.contains(n['idNivel'] as int))
              .toList();
          final grupos = lookups['grupos'] as List? ?? [];
          final textTheme = Theme.of(context).textTheme;

          // ponytail: filter ciclos by selected nivel dynamically
          final ciclosByNivel = <int, List<Map<String, dynamic>>>{};
          for (final c in ciclos) {
            final idNivel = c['idNivel'] as int;
            ciclosByNivel.putIfAbsent(idNivel, () => []).add(c);
          }

          // if nivel is selected, show only ciclos for that nivel; else show all ciclos
          final cicloOptions = _nivel != null
              ? (ciclosByNivel[_nivel] ?? <Map<String, dynamic>>[])
              : ciclos;

          // reset ciclo if it's no longer valid for the selected nivel
          if (_nivel != null &&
              !cicloOptions.any((c) => c['idCiclo'] == _ciclo)) {
            _ciclo = null;
          }

          return Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Text('Filtros', style: textTheme.titleLarge),
              const SizedBox(height: Space.md),
              DropdownButtonFormField<int?>(
                initialValue: _nivel,
                decoration: const InputDecoration(labelText: 'Nivel'),
                items: [
                  const DropdownMenuItem(
                      value: null, child: Text('Todos los niveles')),
                  ...niveles.map((n) => DropdownMenuItem(
                      value: n['idNivel'] as int,
                      child: Text(n['nombreNivel']))),
                ],
                onChanged: (val) => setState(() {
                  _nivel = val;
                  _ciclo = null; // reset ciclo when nivel changes
                }),
              ),
              const SizedBox(height: Space.md),
              DropdownButtonFormField<int?>(
                initialValue: _ciclo,
                decoration: const InputDecoration(labelText: 'Ciclo'),
                items: [
                  const DropdownMenuItem(
                      value: null, child: Text('Todos los ciclos')),
                  ...cicloOptions.map((c) => DropdownMenuItem(
                      value: c['idCiclo'] as int,
                      child: Text(c['nombreCiclo']))),
                ],
                onChanged: (val) => setState(() => _ciclo = val),
              ),
              const SizedBox(height: Space.md),
              DropdownButtonFormField<int?>(
                initialValue: _grupo,
                decoration: const InputDecoration(labelText: 'Grupo'),
                items: [
                  const DropdownMenuItem(
                      value: null, child: Text('Todos los grupos')),
                  ...grupos.map((g) => DropdownMenuItem(
                      value: g['idGrupo'] as int,
                      child: Text(g['nombreGrupo']))),
                ],
                onChanged: (val) => setState(() => _grupo = val),
              ),
              const SizedBox(height: Space.md),
              DropdownButtonFormField<String?>(
                initialValue: _estado,
                decoration: const InputDecoration(labelText: 'Estado'),
                items: const [
                  DropdownMenuItem(
                      value: null, child: Text('Todos los estados')),
                  DropdownMenuItem(value: 'ausente', child: Text('Ausente')),
                  DropdownMenuItem(value: 'retraso', child: Text('Retraso')),
                  DropdownMenuItem(
                      value: 'justificado', child: Text('Justificado')),
                ],
                onChanged: (val) => setState(() => _estado = val),
              ),
              const SizedBox(height: Space.lg),
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton(
                      onPressed: () {
                        ref.read(attendanceFiltersProvider.notifier).state =
                            const AttendanceFilters();
                        Navigator.pop(context);
                      },
                      child: const Text('Limpiar'),
                    ),
                  ),
                  const SizedBox(width: Space.md),
                  Expanded(
                    child: FilledButton(
                      onPressed: () {
                        final currentFilters =
                            ref.read(attendanceFiltersProvider);
                        ref.read(attendanceFiltersProvider.notifier).state =
                            currentFilters.copyWith(
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
              const SizedBox(height: Space.lg),
            ],
          );
        },
      ),
    );
  }
}

class _CenterAttendanceCard extends ConsumerWidget {
  const _CenterAttendanceCard({required this.record});

  final AttendanceRecord record;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final scheme = Theme.of(context).colorScheme;

    // We can reuse the UI of AttendanceCard, but maybe we want to wrap it or copy parts
    // to include the student name, since the student name is very important for the center view.

    Color getStatusColor() {
      switch (record.estado) {
        case 'presente':
          return scheme.primary;
        case 'ausente':
          return scheme.error;
        case 'retraso':
          return AppColors.naranjaLight;
        case 'justificado':
          return AppColors.azulLight;
        default:
          return scheme.outline;
      }
    }

    final color = getStatusColor();

    return Card(
      elevation: 0,
      color: scheme.surfaceContainerHighest,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
        side: BorderSide(color: scheme.outlineVariant.withValues(alpha: 0.5)),
      ),
      child: InkWell(
        onTap: record.canJustify
            ? () async {
                final res = await showJustifySheet(
                  context,
                  ref,
                  idAsistencia: record.id,
                  subtitulo: '${record.nombreModulo} · ${record.fecha}',
                );
                if (res == true) {
                  ref.invalidate(centerAttendanceProvider);
                }
              }
            : null,
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: color.withValues(alpha: 0.1),
                      borderRadius: BorderRadius.circular(4),
                      border: Border.all(color: color.withValues(alpha: 0.3)),
                    ),
                    child: Text(
                      record.estado.toUpperCase(),
                      style: TextStyle(
                          color: color,
                          fontWeight: FontWeight.bold,
                          fontSize: 12),
                    ),
                  ),
                  const Spacer(),
                  Text(
                    record.hora != null
                        ? '${record.fecha} ${record.hora!.substring(0, 5)}'
                        : record.fecha,
                    style:
                        TextStyle(color: scheme.onSurfaceVariant, fontSize: 13),
                  ),
                ],
              ),
              const SizedBox(height: 12),
              Text(
                record.nombreEstudiante,
                style:
                    const TextStyle(fontWeight: FontWeight.bold, fontSize: 16),
              ),
              const SizedBox(height: 4),
              Text(
                'Módulo: ${record.nombreModulo}',
                style: TextStyle(color: scheme.onSurfaceVariant, fontSize: 14),
              ),
              Text(
                'Profesor: ${record.nombreProfesor}',
                style: TextStyle(color: scheme.onSurfaceVariant, fontSize: 14),
              ),
              if (record.observacion != null &&
                  record.observacion!.isNotEmpty) ...[
                const SizedBox(height: 8),
                Text(
                  'Obs: ${record.observacion}',
                  style: TextStyle(
                      fontStyle: FontStyle.italic,
                      color: scheme.onSurfaceVariant,
                      fontSize: 13),
                ),
              ],
              if (record.justificacion != null) ...[
                const SizedBox(height: 8),
                Container(
                  padding: const EdgeInsets.all(8),
                  decoration: BoxDecoration(
                    color: scheme.surface,
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Icon(Icons.info_outline,
                              size: 14, color: scheme.primary),
                          const SizedBox(width: 4),
                          Text(
                              'Justificación (${record.justificacion!.estado})',
                              style: TextStyle(
                                  fontSize: 12,
                                  fontWeight: FontWeight.bold,
                                  color: scheme.primary)),
                        ],
                      ),
                      const SizedBox(height: 4),
                      Text(record.justificacion!.motivo,
                          style: const TextStyle(fontSize: 13)),
                    ],
                  ),
                ),
              ],
              if (record.canJustify) ...[
                const SizedBox(height: 12),
                Align(
                  alignment: Alignment.centerRight,
                  child: FilledButton.icon(
                    onPressed: null, // Tap handled by InkWell
                    icon: const Icon(Icons.upload_file, size: 18),
                    label: const Text('Justificar'),
                    style: FilledButton.styleFrom(
                      visualDensity: VisualDensity.compact,
                    ),
                  ),
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }
}
