import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/auth/auth_state.dart';
import '../../../core/auth/session.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/i18n/translations.dart';
import '../../../core/widgets/premium.dart';
import '../../announcements/presentation/announcements_screen.dart';
import '../../attendance/data/attendance_repository.dart';
import '../../attendance/presentation/attendance_screen.dart';
import '../../attendance/presentation/center_attendance_screen.dart';
import '../../attendance/presentation/staff_justify_screen.dart';
import '../../chat/presentation/conversations_screen.dart';
import '../../classroom/data/classroom_repository.dart';
import '../../classroom/presentation/favorites_screen.dart';
import '../../classroom/presentation/modules_screen.dart';
import '../../events/presentation/events_screen.dart';
import '../../grades/data/grades_repository.dart';
import '../../grades/presentation/grades_screen.dart';
import '../../inventory/presentation/inventory_screen.dart';
import '../../payments/presentation/payments_screen.dart';
import '../../payments/presentation/my_payments_screen.dart';
import '../../messages/presentation/messages_screen.dart';
import '../../profile/data/profile_repository.dart';
import '../../schedule/data/schedule_repository.dart';
import '../../schedule/presentation/schedule_screen.dart';
import '../../history/presentation/history_screen.dart';

import '../../students/presentation/students_screen.dart';
import '../../teachers/presentation/teachers_screen.dart';
import '../../secretarias/presentation/secretarias_screen.dart';
import '../data/dashboard_repository.dart';
import '../../tareas/presentation/tareas_screen.dart';
import '../../retos/presentation/retos_screen.dart';
import '../../gastos/presentation/gastos_screen.dart';

class _NavItem {
  const _NavItem(this.icon, this.label, this.subtitle, this.screen);
  final IconData icon;
  final String label;
  final String subtitle;
  final Widget screen;
}

String _greeting(Map<String, String> t) {
  final hour = DateTime.now().hour;
  if (hour < 12) return t['greeting_morning'] ?? 'Buenos días';
  if (hour < 20) return t['greeting_afternoon'] ?? 'Buenas tardes';
  return t['greeting_evening'] ?? 'Buenas noches';
}

class HomeScreen extends ConsumerWidget {
  const HomeScreen({super.key});

  double _calculateAverageGrade(Map<String, dynamic>? data) {
    if (data == null) return 0.0;
    final modulos =
        (data['modulos'] as List?)?.cast<Map<String, dynamic>>() ?? [];
    double sum = 0.0;
    int count = 0;
    for (final m in modulos) {
      for (final key in [
        'nota_1final',
        'nota_2final',
        'nota_1ev',
        'nota_2ev'
      ]) {
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
    // ponytail: watch role once, derive everything else from it to reduce rebuild surface
    final role =
        ref.watch(sessionControllerProvider.select((s) => s.value?.role));
    final profileAsync = ref.watch(profileProvider);

    final t = ref.watch(translationsProvider);

    final personal = role == UserRole.estudiante ||
        role == UserRole.profesor ||
        role == UserRole.tutor;
    final hasClassroom = role != UserRole.tutor &&
        role != UserRole.director &&
        role != UserRole.secretaria;
    final hasAttendance = role == UserRole.estudiante ||
        role == UserRole.profesor ||
        role == UserRole.tutor;
    final isTutorTeacher = role == UserRole.profesor &&
        (profileAsync.value?.data['esTutor'] == 1 ||
            profileAsync.value?.data['esTutor'] == '1' ||
            profileAsync.value?.data['esTutor'] == true);
    final hasStaffJustify = isTutorTeacher ||
        role == UserRole.director ||
        role == UserRole.secretaria;
    final isBackOffice =
        role == UserRole.director || role == UserRole.secretaria;
    final academico = <_NavItem>[
      if (personal)
        _NavItem(Icons.calendar_today_rounded, t['nav_horario']!,
            t['nav_horario_sub']!, const ScheduleScreen()),
      if (personal)
        _NavItem(Icons.school_outlined, t['nav_notas']!, t['nav_notas_sub']!,
            const GradesScreen()),
      if (hasClassroom)
        _NavItem(Icons.auto_stories_outlined, t['nav_aula']!,
            t['nav_aula_sub']!, const ModulesScreen()),
      if (hasClassroom)
        _NavItem(Icons.assignment_outlined, t['nav_tareas']!,
            t['nav_tareas_sub']!, const TareasScreen()),
      if (hasClassroom)
        _NavItem(Icons.emoji_events_outlined, t['nav_retos']!,
            t['nav_retos_sub']!, const RetosScreen()),
      if (role == UserRole.estudiante)
        _NavItem(Icons.star_rounded, t['nav_favoritos']!,
            t['nav_favoritos_sub']!, const FavoritesScreen()),
      if (hasAttendance)
        _NavItem(Icons.fact_check_outlined, t['nav_asistencias']!,
            t['nav_asistencias_sub']!, AttendanceScreen()),
    ];
    final isEstudianteOrProfesor =
        role == UserRole.estudiante || role == UserRole.profesor;
    final centro = <_NavItem>[
      _NavItem(Icons.campaign_outlined, t['nav_anuncios']!,
          t['nav_anuncios_sub']!, AnnouncementsScreen()),
      // Mensajería solo para director/secretaría/tutor (NO para estudiante/profesor)
      if (!isEstudianteOrProfesor)
        _NavItem(Icons.mail_outline_rounded, t['nav_mensajeria']!,
            t['nav_mensajeria_sub']!, MessagesScreen()),
      // Chat directo: cualquier rol puede recibir un mensaje iniciado desde su
      // perfil (ver ProfileDetailSheet, usado desde listados de profesores/
      // alumnos) — sin esta entrada, quien recibe un chat no tenía forma
      // alguna de verlo ni responder.
      _NavItem(Icons.chat_bubble_outline_rounded, t['nav_chat']!,
          t['nav_chat_sub']!, const ConversationsScreen()),
      _NavItem(Icons.event_outlined, t['nav_eventos']!, t['nav_eventos_sub']!,
          EventsScreen()),
    ];
    final gestion = <_NavItem>[
      if (isBackOffice)
        _NavItem(Icons.receipt_long_outlined, t['nav_pagos']!,
            t['nav_pagos_sub']!, const PaymentsScreen()),
      if (isBackOffice)
        _NavItem(Icons.shopping_bag_outlined, t['nav_gastos']!,
            t['nav_gastos_sub']!, const GastosScreen()),
      if (isBackOffice)
        _NavItem(Icons.people_outlined, t['nav_alumnos']!,
            t['nav_alumnos_sub']!, const StudentsScreen()),
      if (isBackOffice)
        _NavItem(Icons.fact_check_outlined, t['nav_asistencias_centro']!,
            t['nav_asistencias_centro_sub']!, const CenterAttendanceScreen()),
      if (isBackOffice)
        _NavItem(Icons.school_outlined, t['nav_profesores']!,
            t['nav_profesores_sub']!, const TeachersScreen()),
      if (role == UserRole.director)
        _NavItem(
            Icons.admin_panel_settings_outlined,
            t['nav_secretarias'] ?? 'Secretarías',
            t['nav_secretarias_sub'] ?? 'Gestión de personal',
            const SecretariasScreen()),
      if (role == UserRole.director)
        _NavItem(
            Icons.history_rounded,
            t['nav_historial'] ?? 'Historial',
            t['nav_historial_sub'] ?? 'Ver actividad del centro',
            const HistoryScreen()),
      if (isBackOffice)
        _NavItem(Icons.inventory_2_outlined, t['nav_inventario']!,
            t['nav_inventario_sub']!, const InventoryScreen()),
      if (hasStaffJustify)
        _NavItem(Icons.edit_note_outlined, t['nav_justificar']!,
            t['nav_justificar_sub']!, const StaffJustifyScreen()),
    ];

    final displayName = profileAsync.value?.displayName ?? 'AulaPro';
    // Watch metrics only when needed per role, using .select() to avoid cascading rebuilds
    final attendanceMine = role == UserRole.estudiante
        ? ref.watch(attendanceMineProvider.select((a) => a.value ?? []))
        : [];
    final studentPendingTasks = role == UserRole.estudiante
        ? ref.watch(
            studentPendingTasksCountProvider.select((t) => t.value ?? 0))
        : 0;
    final studentGrades = role == UserRole.estudiante
        ? ref.watch(gradesProvider.select((g) => g.value))
        : null;

    // Watch professor metrics
    final scheduleSlots =
        (role == UserRole.profesor || role == UserRole.estudiante)
            ? ref.watch(scheduleProvider.select((s) => s.value ?? []))
            : [];
    final pendingGradesCount = role == UserRole.profesor
        ? ref
            .watch(pendingGradesCountProvider.select((p) => p.value ?? 0))
        : 0;

    // Watch admin metrics
    final dashboardStats =
        (role == UserRole.director || role == UserRole.secretaria)
            ? ref.watch(dashboardStatsProvider).value
            : null;

    // Build role-specific metric cards
    final metrics = <Widget>[];
    if (role == UserRole.estudiante) {
      final avgGrade = _calculateAverageGrade(studentGrades);
      final faltas = attendanceMine.where((a) => a.estado == 'Falta').length;

      metrics.addAll([
        _MetricCard(
          value: avgGrade.toStringAsFixed(1),
          label: t['metric_media']!,
          icon: Icons.insights_rounded,
          color: const Color(0xFF2563EB),
          onTap: () => Navigator.of(context)
              .push(MaterialPageRoute(builder: (_) => const GradesScreen())),
        ),
        _MetricCard(
          value: studentPendingTasks.toString(),
          label: t['metric_tareas']!,
          icon: Icons.assignment_turned_in_rounded,
          color: const Color(0xFFD97706),
          onTap: () => Navigator.of(context)
              .push(MaterialPageRoute(builder: (_) => const TareasScreen())),
        ),
        _MetricCard(
          value: faltas.toString(),
          label: t['metric_faltas']!,
          icon: Icons.warning_amber_rounded,
          color: const Color(0xFFE11D48),
          onTap: () => Navigator.of(context)
              .push(MaterialPageRoute(builder: (_) => AttendanceScreen())),
        ),
      ]);
    } else if (role == UserRole.profesor) {
      final todayName = [
        'Lunes',
        'Martes',
        'Miércoles',
        'Jueves',
        'Viernes',
        'Sábado',
        'Domingo'
      ][DateTime.now().weekday - 1];
      final clasesHoyCount =
          scheduleSlots.where((s) => s.diaSemana == todayName).length;
      final tutorCicloAbreviatura =
          profileAsync.value?.ciclo?['abreviaturaCiclo'] as String? ??
              'Ninguna';

      metrics.addAll([
        _MetricCard(
          value: clasesHoyCount.toString(),
          label: 'Clases Hoy',
          icon: Icons.schedule_rounded,
          color: const Color(0xFF2563EB),
          onTap: () => Navigator.of(context)
              .push(MaterialPageRoute(builder: (_) => const ScheduleScreen())),
        ),
        _MetricCard(
          value: pendingGradesCount.toString(),
          label: 'Por Corregir',
          icon: Icons.rate_review_rounded,
          color: const Color(0xFFE11D48),
          onTap: () => Navigator.of(context)
              .push(MaterialPageRoute(builder: (_) => const ModulesScreen())),
        ),
        _MetricCard(
          value: tutorCicloAbreviatura,
          label: 'Aula Tutoría',
          icon: Icons.room_rounded,
          color: const Color(0xFF0D9488),
          onTap: isTutorTeacher
              ? () => Navigator.of(context).push(
                  MaterialPageRoute(builder: (_) => const StaffJustifyScreen()))
              : null,
        ),
      ]);
    } else if (role == UserRole.director || role == UserRole.secretaria) {
      metrics.addAll([
        _MetricCard(
          value: dashboardStats != null
              ? dashboardStats.totalEstudiantes.toString()
              : '...',
          label: t['metric_estudiantes']!,
          icon: Icons.people_rounded,
          color: const Color(0xFF2563EB),
          onTap: () => Navigator.of(context)
              .push(MaterialPageRoute(builder: (_) => const StudentsScreen())),
        ),
        _MetricCard(
          value: dashboardStats != null
              ? dashboardStats.totalProfesores.toString()
              : '...',
          label: t['metric_profesores']!,
          icon: Icons.school_rounded,
          color: const Color(0xFFD97706),
          onTap: () => Navigator.of(context)
              .push(MaterialPageRoute(builder: (_) => const TeachersScreen())),
        ),
        _MetricCard(
          value: dashboardStats != null
              ? '${dashboardStats.gastosMes.toStringAsFixed(0)} €'
              : '...',
          label: t['metric_gastos']!,
          icon: Icons.receipt_long_rounded,
          color: const Color(0xFFE11D48),
          onTap: () => Navigator.of(context)
              .push(MaterialPageRoute(builder: (_) => const GastosScreen())),
        ),
        _MetricCard(
          value: dashboardStats != null
              ? '${dashboardStats.pagosMes.toStringAsFixed(0)} €'
              : '...',
          label: t['metric_pagos']!,
          icon: Icons.account_balance_wallet_rounded,
          color: const Color(0xFF0D9488),
          onTap: () => Navigator.of(context)
              .push(MaterialPageRoute(builder: (_) => const PaymentsScreen())),
        ),
      ]);
    } else if (role == UserRole.tutor) {
      final hijosCount = profileAsync.value?.data['hijos_count'] ?? 0;

      metrics.addAll([
        _MetricCard(
          value: hijosCount.toString(),
          label: t['metric_hijo']!,
          icon: Icons.family_restroom_rounded,
          color: const Color(0xFF4F46E5),
          onTap: () => Navigator.of(context)
              .push(MaterialPageRoute(builder: (_) => const GradesScreen())),
        ),
        _MetricCard(
          value: t['metric_al_dia']!,
          label: t['metric_recibos']!,
          icon: Icons.receipt_rounded,
          color: const Color(0xFF059669),
          onTap: () => Navigator.of(context).push(
              MaterialPageRoute(builder: (_) => const MyPaymentsScreen())),
        ),
        _MetricCard(
          value:
              '0', // Faltas pending implementation of a combined tutor attendance metric
          label: t['metric_faltas']!,
          icon: Icons.notification_important_rounded,
          color: const Color(0xFFE11D48),
          onTap: () => Navigator.of(context)
              .push(MaterialPageRoute(builder: (_) => AttendanceScreen())),
        ),
      ]);
    }

    return Scaffold(
      backgroundColor: const Color(0xFFFAFAFA),
      body: Stack(
        children: [
          Positioned(
            top: 0,
            left: 0,
            right: 0,
            height: 350,
            child: Container(
              decoration: const BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                  colors: [Color(0xFFF3E8FF), Color(0xFFFAFAFA)],
                ),
              ),
            ),
          ),
          SafeArea(
            child: RefreshIndicator(
              onRefresh: () async {
                ref.invalidate(profileProvider);
                ref.invalidate(attendanceMineProvider);
                ref.invalidate(studentPendingTasksCountProvider);
                ref.invalidate(gradesProvider);
                ref.invalidate(scheduleProvider);
                ref.invalidate(pendingGradesCountProvider);
                ref.invalidate(dashboardStatsProvider);
              },
              child: ListView(
                padding: const EdgeInsets.fromLTRB(0, Space.xl, 0, Space.xxxl),
                children: [
                  _AnimatedEntrance(
                    delayIndex: 0,
                    child: _WelcomeHeader(
                      displayName: displayName,
                      role: role,
                      t: t,
                    ),
                  ),
                  const SizedBox(height: Space.xl),
                  const _AnimatedEntrance(delayIndex: 1, child: _HeroBanner()),
                  const SizedBox(height: Space.xl),
                  if (metrics.isNotEmpty)
                    Padding(
                      padding: const EdgeInsets.symmetric(horizontal: Space.md),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text('Acceso Rápido',
                              style: Theme.of(context)
                                  .textTheme
                                  .titleMedium
                                  ?.copyWith(
                                      fontWeight: FontWeight.bold,
                                      color: Colors.black87)),
                          Text('Ver todo',
                              style: Theme.of(context)
                                  .textTheme
                                  .bodyMedium
                                  ?.copyWith(color: Colors.black54)),
                        ],
                      ),
                    ),
                  const SizedBox(height: Space.md),
                  if (metrics.isNotEmpty)
                    Padding(
                      padding: const EdgeInsets.symmetric(horizontal: Space.md),
                      child: _AnimatedEntrance(
                        delayIndex: 1,
                        child: LayoutBuilder(
                          builder: (context, constraints) {
                            return GridView.count(
                              crossAxisCount: constraints.maxWidth > 600
                                  ? metrics.length
                                  : 2,
                              shrinkWrap: true,
                              physics: const NeverScrollableScrollPhysics(),
                              mainAxisSpacing: Space.md,
                              crossAxisSpacing: Space.md,
                              childAspectRatio: 1.0,
                              children: metrics,
                            );
                          },
                        ),
                      ),
                    ),
                  const SizedBox(height: Space.xxl),
                  if (academico.isNotEmpty) ...[
                    _AnimatedEntrance(
                      delayIndex: 2,
                      child: Padding(
                          padding:
                              const EdgeInsets.symmetric(horizontal: Space.md),
                          child: SectionLabel(t['section_academico']!)),
                    ),
                    _AnimatedEntrance(
                      delayIndex: 3,
                      child: Padding(
                          padding:
                              const EdgeInsets.symmetric(horizontal: Space.md),
                          child: _NavGroup(items: academico)),
                    ),
                    const SizedBox(height: Space.xxl),
                  ],
                  _AnimatedEntrance(
                    delayIndex: 4,
                    child: Padding(
                        padding:
                            const EdgeInsets.symmetric(horizontal: Space.md),
                        child: SectionLabel(t['section_centro']!)),
                  ),
                  _AnimatedEntrance(
                    delayIndex: 5,
                    child: Padding(
                        padding:
                            const EdgeInsets.symmetric(horizontal: Space.md),
                        child: _NavGroup(items: centro)),
                  ),
                  if (gestion.isNotEmpty) ...[
                    const SizedBox(height: Space.xxl),
                    _AnimatedEntrance(
                      delayIndex: 6,
                      child: Padding(
                          padding:
                              const EdgeInsets.symmetric(horizontal: Space.md),
                          child: SectionLabel(t['section_gestion']!)),
                    ),
                    _AnimatedEntrance(
                      delayIndex: 7,
                      child: Padding(
                          padding:
                              const EdgeInsets.symmetric(horizontal: Space.md),
                          child: _NavGroup(items: gestion)),
                    ),
                  ],
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _WelcomeHeader extends StatelessWidget {
  const _WelcomeHeader(
      {required this.displayName, required this.role, required this.t});
  final String displayName;
  final UserRole? role;
  final Map<String, String> t;

  @override
  Widget build(BuildContext context) {
    final textTheme = Theme.of(context).textTheme;
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: Space.md),
      child: Row(
        children: [
          Container(
            width: 50,
            height: 50,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: Colors.white,
              border: Border.all(color: Colors.white, width: 2),
              boxShadow: [
                BoxShadow(
                    color: Colors.black.withValues(alpha: 0.05),
                    blurRadius: 10,
                    offset: const Offset(0, 4))
              ],
            ),
            alignment: Alignment.center,
            child: Text(
              displayName.isNotEmpty ? displayName[0].toUpperCase() : 'U',
              style: textTheme.titleLarge?.copyWith(
                  color: AppColors.accent, fontWeight: FontWeight.bold),
            ),
          ),
          const SizedBox(width: Space.md),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  _greeting(t),
                  style: textTheme.bodyMedium?.copyWith(
                      color: Colors.black54, fontWeight: FontWeight.w500),
                ),
                Text(
                  displayName.trim().split(' ').take(2).join(' '),
                  style: textTheme.titleLarge?.copyWith(
                      color: Colors.black87, fontWeight: FontWeight.bold),
                ),
              ],
            ),
          ),
          Container(
            width: 45,
            height: 45,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: Colors.white,
              boxShadow: [
                BoxShadow(
                    color: Colors.black.withValues(alpha: 0.05),
                    blurRadius: 10,
                    offset: const Offset(0, 4))
              ],
            ),
            child: const Icon(Icons.notifications_none_rounded,
                color: Colors.black87),
          ),
        ],
      ),
    );
  }
}

class _HeroBanner extends StatelessWidget {
  const _HeroBanner();

  @override
  Widget build(BuildContext context) {
    final textTheme = Theme.of(context).textTheme;
    return Container(
      margin: const EdgeInsets.symmetric(horizontal: Space.md),
      padding: const EdgeInsets.all(Space.xl),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xFFE0E7FF), Color(0xFFC7D2FE)],
        ),
        borderRadius: BorderRadius.circular(Radii.xl),
        boxShadow: [
          BoxShadow(
              color: const Color(0xFFC7D2FE).withValues(alpha: 0.5),
              blurRadius: 20,
              offset: const Offset(0, 10))
        ],
      ),
      child: Row(
        children: [
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('Ready to Manage? 🌟',
                    style: textTheme.titleLarge?.copyWith(
                        fontWeight: FontWeight.bold,
                        color: const Color(0xFF3730A3))),
                const SizedBox(height: Space.sm),
                Text('Accede rápidamente a tus reportes y resumen académico.',
                    style: textTheme.bodyMedium
                        ?.copyWith(color: const Color(0xFF4F46E5))),
                const SizedBox(height: Space.md),
                ElevatedButton(
                  onPressed: () {},
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF6366F1),
                    foregroundColor: Colors.white,
                    shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(Radii.pill)),
                    elevation: 0,
                  ),
                  child: const Text('Ver Horario',
                      style: TextStyle(fontWeight: FontWeight.bold)),
                ),
              ],
            ),
          ),
          const SizedBox(width: Space.md),
          const Icon(Icons.school_rounded, size: 80, color: Color(0xFF818CF8)),
        ],
      ),
    );
  }
}

class _MetricCard extends StatelessWidget {
  const _MetricCard({
    required this.value,
    required this.label,
    required this.color,
    required this.icon,
    this.onTap,
  });

  final String value;
  final String label;
  final Color color;
  final IconData icon;
  final VoidCallback? onTap;

  @override
  Widget build(BuildContext context) {
    final textTheme = Theme.of(context).textTheme;
    final darkColor = Color.lerp(color, Colors.black, 0.15) ?? color;
    
    return GestureDetector(
      onTap: onTap,
      child: Container(
        clipBehavior: Clip.hardEdge,
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [color, darkColor],
          ),
          borderRadius: BorderRadius.circular(Radii.lg),
          boxShadow: [
            BoxShadow(
              color: color.withValues(alpha: 0.3),
              blurRadius: 10,
              offset: const Offset(0, 5),
            )
          ],
        ),
        child: Stack(
          children: [
            // Large rotated icon spilling off the bottom right
            Positioned(
              right: -16,
              bottom: -16,
              child: Transform.rotate(
                angle: 0.35, // ~20 degrees
                child: Icon(
                  icon,
                  size: 80,
                  color: Colors.white.withValues(alpha: 0.2),
                ),
              ),
            ),
            // Text content
            Padding(
              padding: const EdgeInsets.all(Space.md),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    label,
                    style: textTheme.titleMedium?.copyWith(
                      fontWeight: FontWeight.w800,
                      color: Colors.white,
                      fontSize: 17,
                      height: 1.1,
                      letterSpacing: -0.5,
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const Spacer(),
                  Text(
                    value,
                    style: textTheme.headlineSmall?.copyWith(
                      fontWeight: FontWeight.w900,
                      color: Colors.white,
                    ),
                    maxLines: 1,
                  ),
                ],
              ),
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
            if (i != items.length - 1)
              Divider(height: 1, indent: 68, color: scheme.outlineVariant),
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
        onTap: () => Navigator.of(context)
            .push(MaterialPageRoute(builder: (_) => item.screen)),
        child: Padding(
          padding: const EdgeInsets.symmetric(
              horizontal: Space.lg, vertical: Space.md + 2),
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
                child:
                    Icon(item.icon, size: 20, color: scheme.onSurfaceVariant),
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
                      style: textTheme.bodySmall?.copyWith(
                          color:
                              scheme.onSurfaceVariant.withValues(alpha: 0.7)),
                    ),
                  ],
                ),
              ),
              Icon(Icons.chevron_right_rounded,
                  size: 20,
                  color: scheme.onSurfaceVariant.withValues(alpha: 0.5)),
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

class _AnimatedEntranceState extends State<_AnimatedEntrance>
    with SingleTickerProviderStateMixin {
  late final AnimationController _controller;
  late final Animation<double> _curved;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
        vsync: this, duration: const Duration(milliseconds: 320));
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
