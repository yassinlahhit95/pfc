import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/auth/auth_state.dart';
import '../../../core/auth/session.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/premium.dart';
import '../../../core/i18n/translations.dart';
import 'help_center_screen.dart';
import '../../account/presentation/change_password_screen.dart';
import '../../account/presentation/edit_profile_screen.dart';
import '../../auth/data/auth_repository.dart';
import '../../payments/presentation/my_payments_screen.dart';
import '../data/profile_repository.dart';

class ProfileScreen extends ConsumerWidget {
  const ProfileScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final profileAsync = ref.watch(profileProvider);
    final role = ref.watch(sessionControllerProvider).valueOrNull?.role;
    final scheme = Theme.of(context).colorScheme;
    final hasPayments = role == UserRole.estudiante || role == UserRole.tutor;
    final t = ref.watch(translationsProvider);
    final currentLocale = ref.watch(localeProvider);

    String getLanguageLabel(String locale) {
      if (locale == 'es') return t['spanish'] ?? 'Español';
      if (locale == 'en') return t['english'] ?? 'Inglés';
      if (locale == 'ca') return t['catalan'] ?? 'Catalán';
      if (locale == 'eu') return t['basque'] ?? 'Euskera';
      return locale;
    }

    return Scaffold(
      appBar: AppBar(title: Text(t['profile'] ?? 'Perfil')),
      body: AsyncView<Profile>(
        value: profileAsync,
        onRetry: () => ref.invalidate(profileProvider),
        data: (context, profile) => ListView(
          padding: const EdgeInsets.fromLTRB(
              Space.xl, Space.lg, Space.xl, 120.0),
          children: [
            Row(
              children: [
                InitialsAvatar(name: profile.displayName, radius: 28),
                const SizedBox(width: Space.lg),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(profile.displayName,
                          style: Theme.of(context).textTheme.titleLarge),
                      const SizedBox(height: 2),
                      Text(
                        profile.roleLabel,
                        style: Theme.of(context)
                            .textTheme
                            .bodySmall
                            ?.copyWith(color: scheme.onSurfaceVariant),
                      ),
                    ],
                  ),
                ),
              ],
            ),
            const SizedBox(height: Space.xxxl),
            if (profile.email.isNotEmpty || profile.ciclo != null) ...[
              SectionLabel(t['personal_data'] ?? 'Información'),
              AppCard(
                padding: EdgeInsets.zero,
                child: Column(
                  children: [
                    if (profile.email.isNotEmpty)
                      _InfoRow(
                          icon: Icons.mail_outline_rounded,
                          label: t['email'] ?? 'Correo',
                          value: profile.email),
                    if (profile.email.isNotEmpty && profile.ciclo != null)
                      Divider(
                          height: 1, indent: 52, color: scheme.outlineVariant),
                    if (profile.ciclo != null)
                      _InfoRow(
                        icon: Icons.school_outlined,
                        label: t['course_cycle'] ?? 'Ciclo formativo',
                        value:
                            '${profile.ciclo!['nombreCiclo'] ?? ''} · ${profile.ciclo!['abreviaturaCiclo'] ?? ''}',
                      ),
                  ],
                ),
              ),
              const SizedBox(height: Space.xxl),
            ],
            SectionLabel(t['settings'] ?? 'Ajustes'),
            AppCard(
              padding: EdgeInsets.zero,
              child: Column(
                children: [
                  _ActionRow(
                    icon: Icons.person_outline_rounded,
                    label: t['edit_profile'] ?? 'Editar perfil',
                    onTap: () => Navigator.of(context).push(
                      MaterialPageRoute(
                          builder: (_) =>
                              EditProfileScreen(role: role!, profile: profile)),
                    ),
                  ),
                  Divider(height: 1, indent: 52, color: scheme.outlineVariant),
                  _ActionRow(
                    icon: Icons.lock_outline_rounded,
                    label: t['change_password'] ?? 'Cambiar contraseña',
                    onTap: () => Navigator.of(context).push(
                      MaterialPageRoute(
                          builder: (_) => const ChangePasswordScreen()),
                    ),
                  ),
                  Divider(height: 1, indent: 52, color: scheme.outlineVariant),
                  _ActionRow(
                    icon: Icons.language_rounded,
                    label:
                        '${t['language'] ?? 'Idioma'}: ${getLanguageLabel(currentLocale)}',
                    onTap: () {
                      showDialog(
                        context: context,
                        builder: (context) => AlertDialog(
                          title: Text(
                              t['select_language'] ?? 'Seleccionar Idioma'),
                          content: Column(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              ListTile(
                                title: Text(t['spanish'] ?? 'Español'),
                                onTap: () {
                                  ref
                                      .read(localeProvider.notifier)
                                      .setLocale('es');
                                  Navigator.pop(context);
                                },
                              ),
                              ListTile(
                                title: Text(t['english'] ?? 'Inglés'),
                                onTap: () {
                                  ref
                                      .read(localeProvider.notifier)
                                      .setLocale('en');
                                  Navigator.pop(context);
                                },
                              ),
                              ListTile(
                                title: Text(t['catalan'] ?? 'Catalán'),
                                onTap: () {
                                  ref
                                      .read(localeProvider.notifier)
                                      .setLocale('ca');
                                  Navigator.pop(context);
                                },
                              ),
                              ListTile(
                                title: Text(t['basque'] ?? 'Euskera'),
                                onTap: () {
                                  ref
                                      .read(localeProvider.notifier)
                                      .setLocale('eu');
                                  Navigator.pop(context);
                                },
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
                  Divider(height: 1, indent: 52, color: scheme.outlineVariant),
                  _ActionRow(
                    icon: Icons.help_outline_rounded,
                    label: t['help_center'] ?? 'Centro de Ayuda',
                    onTap: () => Navigator.of(context).push(
                      MaterialPageRoute(
                          builder: (_) => const HelpCenterScreen()),
                    ),
                  ),
                  if (hasPayments) ...[
                    Divider(
                        height: 1, indent: 52, color: scheme.outlineVariant),
                    _ActionRow(
                      icon: Icons.receipt_long_outlined,
                      label: t['payments'] ?? 'Mis pagos',
                      onTap: () => Navigator.of(context).push(
                        MaterialPageRoute(
                            builder: (_) => const MyPaymentsScreen()),
                      ),
                    ),
                  ],
                ],
              ),
            ),
            const SizedBox(height: Space.xxl),
            OutlinedButton.icon(
              onPressed: () async {
                await ref.read(authRepositoryProvider).logout();
                await ref.read(sessionControllerProvider.notifier).clear();
              },
              style: OutlinedButton.styleFrom(
                foregroundColor: scheme.error,
                side: BorderSide(color: scheme.error.withValues(alpha: 0.35)),
              ),
              icon: const Icon(Icons.logout_rounded, size: 18),
              label: Text(t['logout'] ?? 'Cerrar sesión'),
            ),
          ],
        ),
      ),
    );
  }
}

class _InfoRow extends StatelessWidget {
  const _InfoRow(
      {required this.icon, required this.label, required this.value});
  final IconData icon;
  final String label;
  final String value;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Padding(
      padding:
          const EdgeInsets.symmetric(horizontal: Space.lg, vertical: Space.lg),
      child: Row(
        children: [
          Icon(icon, size: 20, color: scheme.onSurfaceVariant),
          const SizedBox(width: Space.md),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(label, style: Theme.of(context).textTheme.bodySmall),
                Text(value,
                    style: Theme.of(context)
                        .textTheme
                        .bodyLarge
                        ?.copyWith(fontWeight: FontWeight.w500)),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _ActionRow extends StatelessWidget {
  const _ActionRow(
      {required this.icon, required this.label, required this.onTap});
  final IconData icon;
  final String label;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        child: Padding(
          padding: const EdgeInsets.symmetric(
              horizontal: Space.lg, vertical: Space.lg),
          child: Row(
            children: [
              Icon(icon, size: 20, color: scheme.onSurfaceVariant),
              const SizedBox(width: Space.md),
              Expanded(
                  child: Text(label,
                      style: const TextStyle(fontWeight: FontWeight.w500))),
              Icon(Icons.chevron_right_rounded,
                  size: 20,
                  color: scheme.onSurfaceVariant.withValues(alpha: 0.6)),
            ],
          ),
        ),
      ),
    );
  }
}
