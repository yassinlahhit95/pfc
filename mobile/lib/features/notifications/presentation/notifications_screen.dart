import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/error_modal.dart';
import '../../../core/i18n/translations.dart';
import '../data/notifications_repository.dart';

/// Icon/color keyed off the free-text `tipo` values crearNotificacion()'s
/// callers use today (modulo_asignado, ciclo_asignado, nota_publicada,
/// grade_tfg) — anything else (future event types) falls back to a generic
/// bell rather than failing to render.
(IconData, Color) _iconForTipo(String tipo) => switch (tipo) {
      'modulo_asignado' || 'ciclo_asignado' => (
          Icons.school_rounded,
          AppColors.accent
        ),
      'nota_publicada' || 'grade_tfg' => (
          Icons.grade_rounded,
          AppColors.naranjaLight
        ),
      _ => (Icons.notifications_rounded, AppColors.accent),
    };

class NotificationsScreen extends ConsumerWidget {
  const NotificationsScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final notificationsAsync = ref.watch(notificationsListProvider);
    final t = ref.watch(translationsProvider);

    return Scaffold(
      appBar: AppBar(
        title: Text(t['title_notificaciones'] ?? 'Notificaciones'),
        actions: [
          TextButton(
            onPressed: () async {
              try {
                await ref
                    .read(notificationsRepositoryProvider)
                    .markAllRead();
                ref.invalidate(notificationsListProvider);
                ref.invalidate(unreadNotificationsCountProvider);
              } catch (_) {
                if (context.mounted) {
                  await showErrorAlert(
                      context, 'No se pudo actualizar las notificaciones.');
                }
              }
            },
            child: Text(t['notif_marcar_leido'] ?? 'Marcar todo leído'),
          ),
        ],
      ),
      body: AsyncView<List<AppNotification>>(
        value: notificationsAsync,
        onRetry: () => ref.invalidate(notificationsListProvider),
        data: (context, items) {
          if (items.isEmpty) {
            return EmptyState(
              icon: Icons.notifications_none_rounded,
              title: t['notif_sin_notificaciones'] ?? 'Sin notificaciones',
              description: t['notif_sin_notificaciones_desc'] ??
                  'Aquí verás los avisos importantes del centro.',
            );
          }
          return RefreshIndicator(
            onRefresh: () async {
              ref.invalidate(notificationsListProvider);
              ref.invalidate(unreadNotificationsCountProvider);
            },
            child: ListView.separated(
              padding: const EdgeInsets.symmetric(
                  horizontal: Space.md, vertical: Space.md),
              itemCount: items.length,
              separatorBuilder: (_, __) => const SizedBox(height: Space.sm),
              itemBuilder: (context, i) => _NotificationTile(item: items[i]),
            ),
          );
        },
      ),
    );
  }
}

class _NotificationTile extends ConsumerWidget {
  const _NotificationTile({required this.item});
  final AppNotification item;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final scheme = Theme.of(context).colorScheme;
    final (icon, color) = _iconForTipo(item.tipo);
    final date = DateTime.tryParse(item.fechaCreacion.replaceFirst(' ', 'T'));

    return Material(
      color: item.leido
          ? scheme.surfaceContainerHighest.withValues(alpha: 0.5)
          : scheme.surfaceContainerHighest,
      borderRadius: BorderRadius.circular(Radii.md),
      child: InkWell(
        borderRadius: BorderRadius.circular(Radii.md),
        onTap: item.leido
            ? null
            : () async {
                try {
                  await ref
                      .read(notificationsRepositoryProvider)
                      .markRead([item.id]);
                  ref.invalidate(notificationsListProvider);
                  ref.invalidate(unreadNotificationsCountProvider);
                } catch (_) {
                  // best-effort — leave it unread rather than interrupt with an alert
                }
              },
        child: Padding(
          padding: const EdgeInsets.all(Space.md),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                width: 40,
                height: 40,
                decoration: BoxDecoration(
                  color: color.withValues(alpha: 0.12),
                  shape: BoxShape.circle,
                ),
                alignment: Alignment.center,
                child: Icon(icon, size: 20, color: color),
              ),
              const SizedBox(width: Space.md),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      item.mensaje,
                      style: TextStyle(
                        fontWeight:
                            item.leido ? FontWeight.normal : FontWeight.w600,
                      ),
                    ),
                    if (date != null) ...[
                      const SizedBox(height: 2),
                      Text(
                        DateFormat('d MMM yyyy, HH:mm').format(date),
                        style: Theme.of(context).textTheme.bodySmall?.copyWith(
                            color: scheme.onSurfaceVariant),
                      ),
                    ],
                  ],
                ),
              ),
              if (!item.leido)
                Container(
                  width: 8,
                  height: 8,
                  margin: const EdgeInsets.only(top: 4),
                  decoration: const BoxDecoration(
                      color: AppColors.accent, shape: BoxShape.circle),
                ),
            ],
          ),
        ),
      ),
    );
  }
}
