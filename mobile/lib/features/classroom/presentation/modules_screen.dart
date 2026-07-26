import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/filter_bar.dart';
import '../data/classroom_repository.dart';
import 'module_detail_screen.dart';

class ModulesScreen extends ConsumerStatefulWidget {
  const ModulesScreen({super.key});

  @override
  ConsumerState<ModulesScreen> createState() => _ModulesScreenState();
}

class _ModulesScreenState extends ConsumerState<ModulesScreen> {
  String? _ciclo;
  String? _nivel;

  @override
  Widget build(BuildContext context) {
    final modulesAsync = ref.watch(classroomModulesProvider);
    final scheme = Theme.of(context).colorScheme;

    return Scaffold(
      appBar: AppBar(title: const Text('Aula digital')),
      body: AsyncView<List<ClassroomModule>>(
        value: modulesAsync,
        onRetry: () => ref.invalidate(classroomModulesProvider),
        data: (context, allModules) {
          if (allModules.isEmpty) {
            return const EmptyState(
              icon: Icons.school_outlined,
              title: 'Sin módulos disponibles',
            );
          }

          final ciclos = allModules.map((m) => m.nombreCiclo).whereType<String>().where((c) => c.isNotEmpty).toSet().toList()..sort();
          final niveles = allModules.map((m) => m.nombreNivel).whereType<String>().where((n) => n.isNotEmpty).toSet().toList()..sort();

          var modules = allModules;
          if (_ciclo != null) {
            modules = modules.where((m) => m.nombreCiclo == _ciclo).toList();
          }
          if (_nivel != null) {
            modules = modules.where((m) => m.nombreNivel == _nivel).toList();
          }

          return Column(
            children: [
              if (ciclos.length > 1 || niveles.length > 1) ...[
                const SizedBox(height: Space.sm),
                FilterBar(children: [
                  if (ciclos.length > 1)
                    FilterPill<String>(
                      label: 'Ciclo',
                      value: _ciclo,
                      options: [for (final c in ciclos) (c, c)],
                      onChanged: (v) => setState(() => _ciclo = v),
                    ),
                  if (niveles.length > 1)
                    FilterPill<String>(
                      label: 'Grado',
                      value: _nivel,
                      options: [for (final n in niveles) (n, n)],
                      onChanged: (v) => setState(() => _nivel = v),
                    ),
                ]),
              ],
              Expanded(
                child: modules.isEmpty
                    ? const EmptyState(icon: Icons.filter_alt_off_outlined, title: 'Sin resultados para este filtro')
                    : RefreshIndicator(
                        onRefresh: () async => ref.invalidate(classroomModulesProvider),
                        child: ListView.separated(
                          padding: const EdgeInsets.symmetric(vertical: Space.sm),
                          itemCount: modules.length,
                          separatorBuilder: (_, __) => Divider(height: 1, indent: Space.xl, color: scheme.outlineVariant),
                          itemBuilder: (context, i) {
                            final m = modules[i];
                            return Material(
                              color: Colors.transparent,
                              child: InkWell(
                                onTap: () => Navigator.of(context).push(
                                  MaterialPageRoute(builder: (_) => ModuleDetailScreen(module: m)),
                                ),
                                child: Padding(
                                  padding: const EdgeInsets.symmetric(horizontal: Space.xl, vertical: Space.lg),
                                  child: Row(
                                    children: [
                                      Icon(Icons.auto_stories_outlined, size: 21, color: scheme.onSurfaceVariant),
                                      const SizedBox(width: Space.md),
                                      Expanded(
                                        child: Column(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          children: [
                                            Text(m.nombre, style: const TextStyle(fontWeight: FontWeight.w500)),
                                            Text(
                                              m.nombreCiclo != null && m.nombreCiclo!.isNotEmpty
                                                  ? '${m.codigo} · ${m.nombreCiclo}'
                                                  : m.codigo,
                                              style: Theme.of(context).textTheme.bodySmall,
                                            ),
                                          ],
                                        ),
                                      ),
                                      Icon(Icons.chevron_right_rounded, size: 20, color: scheme.onSurfaceVariant.withValues(alpha: 0.6)),
                                    ],
                                  ),
                                ),
                              ),
                            );
                          },
                        ),
                      ),
              ),
            ],
          );
        },
      ),
    );
  }
}
