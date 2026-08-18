import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/error_modal.dart';
import '../../../core/auth/auth_state.dart';
import '../../../core/auth/session.dart';
import '../../../core/i18n/translations.dart';
import '../data/retos_repository.dart';

class RetosScreen extends ConsumerWidget {
  const RetosScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final retosAsync = ref.watch(retosProvider);
    final isProfesor = ref.watch(sessionControllerProvider).value?.role ==
        UserRole.profesor;
    final t = ref.watch(translationsProvider);

    return Scaffold(
      appBar: AppBar(title: Text(t['title_mis_retos'] ?? 'Mis Retos')),
      body: AsyncView<List<Reto>>(
        value: retosAsync,
        onRetry: () => ref.invalidate(retosProvider),
        data: (context, retos) {
          if (retos.isEmpty) {
            return EmptyState(
              icon: Icons.emoji_events_outlined,
              title: isProfesor
                  ? 'No has publicado retos'
                  : 'Sin retos pendientes',
            );
          }
          return ListView.separated(
            padding: const EdgeInsets.all(Space.md),
            itemCount: retos.length,
            separatorBuilder: (_, __) => const SizedBox(height: Space.md),
            itemBuilder: (context, index) {
              final reto = retos[index];
              return _RetoTile(reto: reto);
            },
          );
        },
      ),
    );
  }
}

class _RetoTile extends ConsumerWidget {
  const _RetoTile({required this.reto});
  final Reto reto;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final scheme = Theme.of(context).colorScheme;
    final textTheme = Theme.of(context).textTheme;

    return Card(
      elevation: 0,
      color: scheme.surfaceContainerHighest,
      shape:
          RoundedRectangleBorder(borderRadius: BorderRadius.circular(Radii.md)),
      child: InkWell(
        onTap: () async {
          if (reto.archivoAdjunto != null) {
            final url = ref.read(retosRepositoryProvider).downloadUrl(reto.id);
            final uri = Uri.parse(url);
            final ok =
                await launchUrl(uri, mode: LaunchMode.externalApplication);
            if (!ok && context.mounted) {
              await showErrorAlert(
                  context, 'No se pudo abrir el PDF del reto.');
            }
          } else {
            await showErrorAlert(context, 'Este reto no tiene un PDF adjunto.');
          }
        },
        borderRadius: BorderRadius.circular(Radii.md),
        child: Padding(
          padding: const EdgeInsets.all(Space.lg),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    padding: const EdgeInsets.all(8),
                    decoration: BoxDecoration(
                      color: scheme.primary.withValues(alpha: 0.1),
                      shape: BoxShape.circle,
                    ),
                    child: Icon(Icons.picture_as_pdf_outlined,
                        color: scheme.primary),
                  ),
                  const SizedBox(width: Space.md),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(reto.titulo, style: textTheme.titleMedium),
                        const SizedBox(height: Space.xs),
                        Text(
                          '${reto.nombreModulo} • ${reto.nombreProfesor}',
                          style: textTheme.bodySmall
                              ?.copyWith(color: scheme.onSurfaceVariant),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
              if (reto.descripcion.isNotEmpty) ...[
                const SizedBox(height: Space.md),
                Text(
                  reto.descripcion,
                  style: textTheme.bodyMedium,
                ),
              ],
              const SizedBox(height: Space.md),
              Align(
                alignment: Alignment.centerRight,
                child: Text(
                  'Toca para ver el PDF',
                  style: textTheme.labelSmall?.copyWith(color: scheme.primary),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
