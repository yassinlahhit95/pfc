import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/auth/auth_state.dart';
import '../../../core/auth/session.dart';
import '../../announcements/presentation/announcements_screen.dart';
import '../../attendance/presentation/attendance_screen.dart';
import '../../classroom/presentation/modules_screen.dart';
import '../../events/presentation/events_screen.dart';
import '../../grades/presentation/grades_screen.dart';
import '../../inventory/presentation/inventory_screen.dart';
import '../../payments/presentation/payments_screen.dart';
import '../../profile/data/profile_repository.dart';
import '../../schedule/presentation/schedule_screen.dart';

class HomeScreen extends ConsumerWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final role = ref.watch(sessionControllerProvider).valueOrNull?.role;
    final profileAsync = ref.watch(profileProvider);
    final scheme = Theme.of(context).colorScheme;

    final personal = role == UserRole.estudiante || role == UserRole.profesor || role == UserRole.tutor;
    // Dirección/secretaría can supervise any aula digital resource too
    // (same permission the web panel already grants — see verArchivo.php).
    final hasClassroom = role != UserRole.tutor;
    // Asistencias: estudiante/tutor view+justify, profesor marks+resolves.
    // Director/secretaria use the web dashboard for this (per API 403).
    final hasAttendance = role == UserRole.estudiante || role == UserRole.profesor || role == UserRole.tutor;
    final isBackOffice = role == UserRole.director || role == UserRole.secretaria;

    return Scaffold(
      body: CustomScrollView(
        slivers: [
          SliverAppBar(
            expandedHeight: 140,
            pinned: true,
            backgroundColor: scheme.surface,
            surfaceTintColor: Colors.transparent,
            flexibleSpace: FlexibleSpaceBar(
              titlePadding: const EdgeInsets.only(left: 16, bottom: 16),
              title: Text(
                profileAsync.valueOrNull?.displayName.split(' ').first ?? 'AulaPro',
                style: TextStyle(color: scheme.onSurface, fontWeight: FontWeight.bold),
              ),
              background: Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: [scheme.primary.withValues(alpha: 0.18), scheme.surface],
                  ),
                ),
              ),
            ),
          ),
          SliverPadding(
            padding: const EdgeInsets.all(16),
            sliver: SliverList(
              delegate: SliverChildListDelegate([
                Text('Accesos rápidos', style: Theme.of(context).textTheme.titleMedium),
                const SizedBox(height: 12),
                GridView.count(
                  crossAxisCount: 2,
                  shrinkWrap: true,
                  physics: const NeverScrollableScrollPhysics(),
                  mainAxisSpacing: 12,
                  crossAxisSpacing: 12,
                  childAspectRatio: 1.5,
                  children: [
                    if (personal)
                      _QuickCard(
                        icon: Icons.calendar_month_rounded,
                        label: 'Horario',
                        color: const Color(0xFF3B82F6),
                        onTap: () => _push(context, const ScheduleScreen()),
                      ),
                    if (personal)
                      _QuickCard(
                        icon: Icons.grade_rounded,
                        label: 'Notas',
                        color: const Color(0xFF10B981),
                        onTap: () => _push(context, const GradesScreen()),
                      ),
                    if (hasClassroom)
                      _QuickCard(
                        icon: Icons.menu_book_rounded,
                        label: 'Aula digital',
                        color: const Color(0xFF4F46E5),
                        onTap: () => _push(context, const ModulesScreen()),
                      ),
                    if (hasAttendance)
                      _QuickCard(
                        icon: Icons.event_available_rounded,
                        label: 'Asistencias',
                        color: const Color(0xFF06B6D4),
                        onTap: () => _push(context, const AttendanceScreen()),
                      ),
                    _QuickCard(
                      icon: Icons.campaign_rounded,
                      label: 'Anuncios',
                      color: const Color(0xFFF59E0B),
                      onTap: () => _push(context, const AnnouncementsScreen()),
                    ),
                    _QuickCard(
                      icon: Icons.event_rounded,
                      label: 'Eventos',
                      color: const Color(0xFF8B5CF6),
                      onTap: () => _push(context, const EventsScreen()),
                    ),
                    if (isBackOffice)
                      _QuickCard(
                        icon: Icons.payments_rounded,
                        label: 'Pagos',
                        color: const Color(0xFF10B981),
                        onTap: () => _push(context, const PaymentsScreen()),
                      ),
                    if (isBackOffice)
                      _QuickCard(
                        icon: Icons.inventory_2_rounded,
                        label: 'Inventario',
                        color: const Color(0xFFEC4899),
                        onTap: () => _push(context, const InventoryScreen()),
                      ),
                  ],
                ),
              ]),
            ),
          ),
        ],
      ),
    );
  }

  void _push(BuildContext context, Widget screen) {
    Navigator.of(context).push(MaterialPageRoute(builder: (_) => screen));
  }
}

class _QuickCard extends StatelessWidget {
  const _QuickCard({required this.icon, required this.label, required this.color, required this.onTap});
  final IconData icon;
  final String label;
  final Color color;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: Theme.of(context).colorScheme.surface,
      borderRadius: BorderRadius.circular(16),
      child: InkWell(
        borderRadius: BorderRadius.circular(16),
        onTap: onTap,
        child: Container(
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(16),
            border: Border.all(color: Theme.of(context).colorScheme.outlineVariant),
          ),
          padding: const EdgeInsets.all(14),
          child: Row(
            children: [
              Container(
                width: 40,
                height: 40,
                decoration: BoxDecoration(color: color.withValues(alpha: 0.15), borderRadius: BorderRadius.circular(12)),
                child: Icon(icon, color: color),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: Text(label, style: const TextStyle(fontWeight: FontWeight.w600)),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
