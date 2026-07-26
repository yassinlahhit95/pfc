import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/auth/auth_state.dart';
import '../../../core/auth/session.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/premium.dart';
import '../../announcements/presentation/announcements_screen.dart';
import '../../attendance/data/attendance_repository.dart';
import '../../attendance/presentation/attendance_screen.dart';
import '../../classroom/data/classroom_repository.dart';
import '../../classroom/presentation/favorites_screen.dart';
import '../../classroom/presentation/modules_screen.dart';
import '../../events/presentation/events_screen.dart';
import '../../grades/data/grades_repository.dart';
import '../../grades/presentation/grades_screen.dart';
import '../../inventory/presentation/inventory_screen.dart';
import '../../payments/presentation/payments_screen.dart';
import '../../messages/presentation/messages_screen.dart';
import '../../profile/data/profile_repository.dart';
import '../../schedule/data/schedule_repository.dart';
import '../../schedule/presentation/schedule_screen.dart';

class _NavItem {
  const _NavItem(this.icon, this.label, this.subtitle, this.screen);
  final IconData icon;
  final String label;
  final String subtitle;
  final Widget screen;
}

String _greeting() {
  final hour = DateTime.now().hour;
  if (hour < 12) return 'Buenos días';
  if (hour < 20) return 'Buenas tardes';
  return 'Buenas noches';
}

class HomeScreen extends ConsumerWidget {
  const HomeScreen({super.key});

  double _calculateAverageGrade(Map<String, dynamic>? data) {
    if (data == null) return 0.0;
    final modulos = (data['modulos'] as List?)?.cast<Map<String, dynamic>>() ?? [];
    double sum = 0.0;
    int count = 0;
    for (final m in modulos) {
      for (final key in ['nota_1final', 'nota_2final', 'nota_1ev', 'nota_2ev']) {
        final val = double.tryParse(m[key]?.toString() ?? '');
        if (val != null) {
          sum += val;
          count++;
          break;
        }
      }
    }
    return count > 0 ? sum / count : 0.0;
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final role = ref.watch(sessionControllerProvider).valueOrNull?.role;
    final profileAsync = ref.watch(profileProvider);

    final personal = role == UserRole.estudiante || role == UserRole.profesor || role == UserRole.tutor;
    final hasClassroom = role != UserRole.tutor && role != UserRole.director;
    final hasAttendance = role == UserRole.estudiante || role == UserRole.profesor || role == UserRole.tutor;
    final isBackOffice = role == UserRole.director || role == UserRole.secretaria;

    final academico = <_NavItem>[
      if (personal) const _NavItem(Icons.calendar_today_rounded, 'Horario', 'Ver calendario de clases', ScheduleScreen()),
      if (personal) const _NavItem(Icons.school_outlined, 'Notas', 'Consultar calificaciones', GradesScreen()),
      if (hasClassroom) const _NavItem(Icons.auto_stories_outlined, 'Aula digital', 'Temas, recursos y entregas', ModulesScreen()),
      if (role == UserRole.estudiante) const _NavItem(Icons.star_rounded, 'Favoritos', 'Tus archivos guardados', FavoritesScreen()),
      if (hasAttendance) const _NavItem(Icons.fact_check_outlined, 'Asistencias', 'Registro de faltas y asistencia', AttendanceScreen()),
    ];
    final centro = <_NavItem>[
      const _NavItem(Icons.campaign_outlined, 'Anuncios', 'Comunicados oficiales y avisos', AnnouncementsScreen()),
      const _NavItem(Icons.mail_outline_rounded, 'Mensajería', 'Bandeja de entrada y reclamaciones', MessagesScreen()),
      const _NavItem(Icons.event_outlined, 'Eventos', 'Próximas actividades y fechas', EventsScreen()),
    ];
    final gestion = <_NavItem>[
      if (isBackOffice) const _NavItem(Icons.receipt_long_outlined, 'Pagos', 'Ver recibos y cobros', PaymentsScreen()),
      if (isBackOffice) const _NavItem(Icons.inventory_2_outlined, 'Inventario', 'Control de material y recursos', InventoryScreen()),
    ];

    final displayName = profileAsync.valueOrNull?.displayName ?? 'AulaPro';

    // Watch values for student metrics
    final attendanceMine = role == UserRole.estudiante ? ref.watch(attendanceMineProvider).valueOrNull ?? [] : [];
    final studentPendingTasks = role == UserRole.estudiante ? ref.watch(studentPendingTasksCountProvider).valueOrNull ?? 0 : 0;
    final studentGrades = role == UserRole.estudiante ? ref.watch(gradesProvider).valueOrNull : null;

    // Watch values for professor metrics
    final scheduleSlots = (role == UserRole.profesor || role == UserRole.estudiante) ? ref.watch(scheduleProvider).valueOrNull ?? [] : [];
    final pendingGradesCount = role == UserRole.profesor ? ref.watch(pendingGradesCountProvider).valueOrNull ?? 0 : 0;
    final userProfile = ref.watch(profileProvider).valueOrNull;

    // Build role-specific metric cards
    final metrics = <Widget>[];
    if (role == UserRole.estudiante) {
      final totalAttendanceCount = attendanceMine.length;
      final presentAttendanceCount = attendanceMine.where((r) => r.estado == 'presente' || r.estado == 'justificado').length;
      final attendancePercent = totalAttendanceCount > 0
          ? '${((presentAttendanceCount / totalAttendanceCount) * 100).toStringAsFixed(1)}%'
          : '100%';
      final avgGrade = _calculateAverageGrade(studentGrades);
      final avgGradeStr = avgGrade > 0 ? avgGrade.toStringAsFixed(1) : '—';

      metrics.addAll([
        _MetricCard(value: attendancePercent, label: 'Asistencia', icon: Icons.person_search_rounded, color: const Color(0xFF059669)),
        _MetricCard(value: studentPendingTasks.toString(), label: 'Tareas disp.', icon: Icons.assignment_rounded, color: const Color(0xFF7C3AED)),
        _MetricCard(value: avgGradeStr, label: 'Nota Media', icon: Icons.stars_rounded, color: const Color(0xFFD97706)),
      ]);
    } else if (role == UserRole.profesor) {
      final todayName = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'][DateTime.now().weekday - 1];
      final clasesHoyCount = scheduleSlots.where((s) => s.diaSemana == todayName).length;
      final tutorCicloAbreviatura = userProfile?.ciclo?['abreviaturaCiclo'] as String? ?? 'Ninguna';

      metrics.addAll([
        _MetricCard(value: clasesHoyCount.toString(), label: 'Clases Hoy', icon: Icons.schedule_rounded, color: const Color(0xFF2563EB)),
        _MetricCard(value: pendingGradesCount.toString(), label: 'Por Corregir', icon: Icons.rate_review_rounded, color: const Color(0xFFE11D48)),
        _MetricCard(value: tutorCicloAbreviatura, label: 'Aula Tutoría', icon: Icons.room_rounded, color: const Color(0xFF0D9488)),
      ]);
    } else if (role == UserRole.director || role == UserRole.secretaria) {
      metrics.addAll([
        const _MetricCard(value: '142', label: 'Matrículas', icon: Icons.people_rounded, color: Color(0xFF2563EB)),
        const _MetricCard(value: '98.2%', label: 'Asistencia', icon: Icons.trending_up_rounded, color: Color(0xFF059669)),
        const _MetricCard(value: 'Al día', label: 'Estado SaaS', icon: Icons.verified_user_rounded, color: Color(0xFF7C3AED)),
      ]);
    } else if (role == UserRole.tutor) {
      metrics.addAll([
        const _MetricCard(value: '1', label: 'Hijo', icon: Icons.family_restroom_rounded, color: Color(0xFF4F46E5)),
        const _MetricCard(value: 'Al día', label: 'Recibos', icon: Icons.receipt_rounded, color: Color(0xFF059669)),
        const _MetricCard(value: '0', label: 'Faltas', icon: Icons.notification_important_rounded, color: Color(0xFFE11D48)),
      ]);
    }

    return Scaffold(
      body: SafeArea(
        child: RefreshIndicator(
          onRefresh: () async {
            ref.invalidate(profileProvider);
            ref.invalidate(attendanceMineProvider);
            ref.invalidate(studentPendingTasksCountProvider);
            ref.invalidate(gradesProvider);
            ref.invalidate(scheduleProvider);
            ref.invalidate(pendingGradesCountProvider);
          },
          child: ListView(
          padding: const EdgeInsets.fromLTRB(Space.xl, Space.xl, Space.xl, Space.xxxl),
          children: [
            _AnimatedEntrance(
              delayIndex: 0,
              child: _WelcomeHeader(
                displayName: displayName,
                role: role,
              ),
            ),
            const SizedBox(height: Space.xxl),
            
            // Render metrics row if not empty
            if (metrics.isNotEmpty) ...[
              _AnimatedEntrance(
                delayIndex: 1,
                child: Row(
                  children: [
                    for (var i = 0; i < metrics.length; i++) ...[
                      metrics[i],
                      if (i != metrics.length - 1) const SizedBox(width: Space.md),
                    ],
                  ],
                ),
              ),
              const SizedBox(height: Space.xxl),
            ],

            if (academico.isNotEmpty) ...[
              const _AnimatedEntrance(
                delayIndex: 2,
                child: SectionLabel('Académico'),
              ),
              _AnimatedEntrance(
                delayIndex: 3,
                child: _NavGroup(items: academico),
              ),
              const SizedBox(height: Space.xxl),
            ],
            const _AnimatedEntrance(
              delayIndex: 4,
              child: SectionLabel('Centro'),
            ),
            _AnimatedEntrance(
              delayIndex: 5,
              child: _NavGroup(items: centro),
            ),
            if (gestion.isNotEmpty) ...[
              const SizedBox(height: Space.xxl),
              const _AnimatedEntrance(
                delayIndex: 6,
                child: SectionLabel('Gestión'),
              ),
              _AnimatedEntrance(
                delayIndex: 7,
                child: _NavGroup(items: gestion),
              ),
            ],
          ],
          ),
        ),
      ),
    );
  }
}

class _WelcomeHeader extends StatelessWidget {
  const _WelcomeHeader({required this.displayName, required this.role});
  final String displayName;
  final UserRole? role;

  String _roleName(UserRole? role) {
    switch (role) {
      case UserRole.director:
        return 'Dirección';
      case UserRole.profesor:
        return 'Profesor';
      case UserRole.secretaria:
        return 'Secretaría';
      case UserRole.estudiante:
        return 'Estudiante';
      case UserRole.tutor:
        return 'Tutor Familiar';
      default:
        return 'Usuario';
    }
  }

  /// Full name, but broken after the 3rd word for long names — a plain
  /// unbounded Text would either overflow the header's fixed row or, with
  /// wrapping, break mid-word at an arbitrary width; this keeps it to a
  /// predictable 2-line shape regardless of how long the name is.
  String _wrappedName(String name) {
    final words = name.trim().split(RegExp(r'\s+'));
    if (words.length <= 3) return name;
    return '${words.take(3).join(' ')}\n${words.skip(3).join(' ')}';
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final textTheme = Theme.of(context).textTheme;

    // Both gradient stops are derived from the single accent token instead of
    // two separately-invented hex pairs, so this stays in sync if the app's
    // accent color ever changes.
    final gradientColors = isDark
        ? [Color.lerp(AppColors.accent, AppColors.bgDark, 0.55)!, AppColors.bgDark]
        : [AppColors.accent, Color.lerp(AppColors.accent, Colors.black, 0.35)!];

    return Container(
      padding: const EdgeInsets.all(Space.xxl),
      decoration: BoxDecoration(
        gradient: LinearGradient(begin: Alignment.topLeft, end: Alignment.bottomRight, colors: gradientColors),
        borderRadius: BorderRadius.circular(Radii.xl),
        boxShadow: cardShadow(Theme.of(context).brightness),
      ),
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  _greeting(),
                  style: textTheme.bodyMedium?.copyWith(color: Colors.white.withValues(alpha: 0.8)),
                ),
                const SizedBox(height: Space.xs),
                Text(
                  _wrappedName(displayName),
                  style: textTheme.headlineSmall?.copyWith(color: Colors.white),
                ),
                const SizedBox(height: Space.md),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                  decoration: BoxDecoration(
                    color: Colors.white.withValues(alpha: 0.15),
                    borderRadius: BorderRadius.circular(Radii.pill),
                  ),
                  child: Text(
                    _roleName(role).toUpperCase(),
                    style: textTheme.labelSmall?.copyWith(color: Colors.white),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(width: Space.md),
          Container(
            width: 54,
            height: 54,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: Colors.white.withValues(alpha: 0.18),
              border: Border.all(color: Colors.white.withValues(alpha: 0.25), width: 1.5),
            ),
            alignment: Alignment.center,
            child: Text(
              displayName.isNotEmpty ? displayName[0].toUpperCase() : 'U',
              style: textTheme.titleLarge?.copyWith(color: Colors.white),
            ),
          ),
        ],
      ),
    );
  }
}

class _MetricCard extends StatelessWidget {
  const _MetricCard({
    required this.value,
    required this.label,
    required this.icon,
    required this.color,
  });

  final String value;
  final String label;
  final IconData icon;
  final Color color;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final textTheme = Theme.of(context).textTheme;
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Expanded(
      child: AppCard(
        padding: const EdgeInsets.symmetric(horizontal: Space.md, vertical: Space.md + 2),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              width: 32,
              height: 32,
              decoration: BoxDecoration(
                color: isDark ? color.withValues(alpha: 0.15) : color.withValues(alpha: 0.1),
                shape: BoxShape.circle,
              ),
              alignment: Alignment.center,
              child: Icon(icon, size: 16, color: color),
            ),
            const SizedBox(height: Space.md),
            Text(value, style: textTheme.titleLarge),
            const SizedBox(height: 2),
            Text(
              label,
              style: textTheme.bodySmall?.copyWith(color: scheme.onSurfaceVariant.withValues(alpha: 0.7)),
              maxLines: 1,
              overflow: TextOverflow.ellipsis,
            ),
          ],
        ),
      ),
    );
  }
}

class _NavGroup extends StatelessWidget {
  const _NavGroup({required this.items});
  final List<_NavItem> items;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return AppCard(
      padding: EdgeInsets.zero,
      child: Column(
        children: [
          for (var i = 0; i < items.length; i++) ...[
            _NavRow(item: items[i]),
            if (i != items.length - 1) Divider(height: 1, indent: 68, color: scheme.outlineVariant),
          ],
        ],
      ),
    );
  }
}

class _NavRow extends StatelessWidget {
  const _NavRow({required this.item});
  final _NavItem item;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final textTheme = Theme.of(context).textTheme;

    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: () => Navigator.of(context).push(MaterialPageRoute(builder: (_) => item.screen)),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: Space.lg, vertical: Space.md + 2),
          child: Row(
            children: [
              // One restrained neutral treatment for every row — accent stays
              // reserved for the header/primary actions, not a rainbow of
              // decorative per-item pastels (see app_theme.dart's own doc
              // comment on AppColors for why).
              Container(
                width: 40,
                height: 40,
                decoration: BoxDecoration(
                  color: scheme.surfaceContainerHighest,
                  borderRadius: BorderRadius.circular(12),
                ),
                alignment: Alignment.center,
                child: Icon(item.icon, size: 20, color: scheme.onSurfaceVariant),
              ),
              const SizedBox(width: Space.md + 4),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(item.label, style: textTheme.titleSmall),
                    const SizedBox(height: 2),
                    Text(
                      item.subtitle,
                      style: textTheme.bodySmall?.copyWith(color: scheme.onSurfaceVariant.withValues(alpha: 0.7)),
                    ),
                  ],
                ),
              ),
              Icon(Icons.chevron_right_rounded, size: 20, color: scheme.onSurfaceVariant.withValues(alpha: 0.5)),
            ],
          ),
        ),
      ),
    );
  }
}

/// Fades + slides a child in, with an actual staggered *start* (not just a
/// longer duration for later items) — each item waits its own delay, then
/// plays the same short, consistent animation. Scaling the duration by index
/// instead (as this used to) makes every item start moving at the same
/// instant and just finish later, which never actually cascades — it only
/// looks like a slightly mushier version of everything animating at once.
class _AnimatedEntrance extends StatefulWidget {
  const _AnimatedEntrance({required this.child, required this.delayIndex});
  final Widget child;
  final int delayIndex;

  @override
  State<_AnimatedEntrance> createState() => _AnimatedEntranceState();
}

class _AnimatedEntranceState extends State<_AnimatedEntrance> with SingleTickerProviderStateMixin {
  late final AnimationController _controller;
  late final Animation<double> _curved;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(vsync: this, duration: const Duration(milliseconds: 320));
    _curved = CurvedAnimation(parent: _controller, curve: Curves.easeOutCubic);
    Future.delayed(Duration(milliseconds: widget.delayIndex * 60), () {
      if (mounted) _controller.forward();
    });
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return AnimatedBuilder(
      animation: _curved,
      builder: (context, child) {
        return Opacity(
          opacity: _curved.value,
          child: Transform.translate(
            offset: Offset(0, 16.0 * (1.0 - _curved.value)),
            child: child,
          ),
        );
      },
      child: widget.child,
    );
  }
}
