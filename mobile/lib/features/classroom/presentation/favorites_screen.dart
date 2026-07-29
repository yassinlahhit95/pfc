import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/error_modal.dart';
import '../data/classroom_repository.dart';

class FavoritesScreen extends ConsumerWidget {
  const FavoritesScreen({super.key});

  IconData _fileIcon(String extension) => switch (extension.toLowerCase()) {
        'pdf' => Icons.picture_as_pdf_outlined,
        'doc' || 'docx' => Icons.description_outlined,
        'xls' || 'xlsx' || 'csv' => Icons.table_chart_outlined,
        'ppt' || 'pptx' => Icons.slideshow_outlined,
        'jpg' || 'jpeg' || 'png' || 'gif' || 'webp' => Icons.image_outlined,
        'zip' || 'rar' => Icons.folder_zip_outlined,
        _ => Icons.insert_drive_file_outlined,
      };

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final favoritesAsync = ref.watch(classroomFavoritesProvider);
    final scheme = Theme.of(context).colorScheme;

    return Scaffold(
      appBar: AppBar(title: const Text('Archivos favoritos')),
      body: AsyncView<List<ClassroomFile>>(
        value: favoritesAsync,
        onRetry: () => ref.invalidate(classroomFavoritesProvider),
        data: (context, files) {
          if (files.isEmpty) {
            return const EmptyState(
              icon: Icons.star_outline_rounded,
              title: 'Aún no tienes favoritos',
              description: 'Pulsa la estrella en los archivos del aula digital para guardarlos aquí.',
            );
          }
          return RefreshIndicator(
            onRefresh: () async => ref.invalidate(classroomFavoritesProvider),
            child: ListView.separated(
              padding: const EdgeInsets.symmetric(vertical: Space.sm),
              itemCount: files.length,
              separatorBuilder: (_, __) => Divider(height: 1, indent: Space.xl, color: scheme.outlineVariant),
              itemBuilder: (context, i) {
                final f = files[i];
                return ListTile(
                  contentPadding: const EdgeInsets.symmetric(horizontal: Space.xl, vertical: Space.xs),
                  leading: Icon(_fileIcon(f.extension), size: 22, color: scheme.onSurfaceVariant),
                  title: Text(f.nombreOriginal, style: const TextStyle(fontWeight: FontWeight.w500)),
                  subtitle: Text('${f.humanSize} · ${f.nombreProfesor}'),
                  trailing: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      IconButton(
                        icon: const Icon(
                          Icons.star_rounded,
                          size: 20,
                          color: Colors.amber,
                        ),
                        onPressed: () async {
                          try {
                            await ref.read(classroomRepositoryProvider).toggleFavorite(f.id);
                            ref.invalidate(classroomFavoritesProvider);
                            ref.invalidate(classroomModulesProvider);
                          } catch (_) {
                            if (context.mounted) {
                              await showErrorAlert(context, 'No se pudo quitar de favoritos.');
                            }
                          }
                        },
                      ),
                      Icon(Icons.file_download_outlined, size: 20, color: scheme.onSurfaceVariant),
                    ],
                  ),
                  onTap: () async {
                    final url = ref.read(classroomRepositoryProvider).downloadUrl(f.id);
                    final uri = Uri.parse(url);
                    final ok = await launchUrl(uri, mode: LaunchMode.externalApplication);
                    if (!ok && context.mounted) {
                      await showErrorAlert(context, 'No se pudo abrir el archivo.');
                    }
                  },
                );
              },
            ),
          );
        },
      ),
    );
  }
}
