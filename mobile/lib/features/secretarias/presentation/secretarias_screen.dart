import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/password_confirmation_dialog.dart';
import '../../../core/auth/session.dart';
import '../../../core/auth/auth_state.dart';
import '../data/secretarias_repository.dart';
import 'secretaria_form_sheet.dart';

class SecretariasScreen extends ConsumerStatefulWidget {
  const SecretariasScreen({super.key});

  @override
  ConsumerState<SecretariasScreen> createState() => _SecretariasScreenState();
}

class _SecretariasScreenState extends ConsumerState<SecretariasScreen> {
  final String _searchQuery = '';

  @override
  Widget build(BuildContext context) {
    final session = ref.watch(sessionControllerProvider).value;
    final canManage = session?.role == UserRole.director;
    final secretariasAsync = ref.watch(
      secretariasProvider(_searchQuery.isNotEmpty ? _searchQuery : null),
    );

    return Scaffold(
      appBar: AppBar(
        title: const Text('Secretarías'),
      ),
      floatingActionButton: canManage
          ? FloatingActionButton(
              onPressed: () async {
                final result = await SecretariaFormSheet.show(context);
                if (result == true) {
                  ref.invalidate(secretariasProvider);
                }
              },
              child: const Icon(Icons.add),
            )
          : null,
      body: AsyncView<({List<Secretaria> secretarias, int total})>(
        value: secretariasAsync,
        onRetry: () => ref.invalidate(
          secretariasProvider(_searchQuery.isNotEmpty ? _searchQuery : null),
        ),
        data: (context, data) {
          if (data.secretarias.isEmpty) {
            return const Center(child: Text('No se encontraron secretarías.'));
          }
          return RefreshIndicator(
            onRefresh: () async {
              ref.invalidate(secretariasProvider);
            },
            child: ListView.separated(
              padding: const EdgeInsets.all(Space.md),
              itemCount: data.secretarias.length,
              separatorBuilder: (_, __) => const SizedBox(height: Space.sm),
              itemBuilder: (context, index) {
                final secretaria = data.secretarias[index];
                return _SecretariaCard(
                    secretaria: secretaria, canManage: canManage);
              },
            ),
          );
        },
      ),
    );
  }
}

class _SecretariaCard extends ConsumerWidget {
  const _SecretariaCard({required this.secretaria, required this.canManage});
  final Secretaria secretaria;
  final bool canManage;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final scheme = Theme.of(context).colorScheme;
    final textTheme = Theme.of(context).textTheme;

    return Card(
      elevation: 0,
      color: scheme.surfaceContainerHighest,
      shape:
          RoundedRectangleBorder(borderRadius: BorderRadius.circular(Radii.md)),
      child: Padding(
        padding: const EdgeInsets.all(Space.md),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                CircleAvatar(
                  backgroundColor: scheme.primary.withValues(alpha: 0.15),
                  child: Text(
                    secretaria.nombre.isNotEmpty
                        ? secretaria.nombre[0].toUpperCase()
                        : '?',
                    style: TextStyle(
                        color: scheme.primary, fontWeight: FontWeight.w600),
                  ),
                ),
                const SizedBox(width: Space.md),
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(secretaria.nombre, style: textTheme.titleMedium),
                      const SizedBox(height: 2),
                      Text(secretaria.email,
                          style: textTheme.bodyMedium
                              ?.copyWith(color: scheme.onSurfaceVariant)),
                    ],
                  ),
                ),
                if (canManage)
                  PopupMenuButton<String>(
                    icon: const Icon(Icons.more_vert),
                    onSelected: (value) async {
                      if (value == 'edit') {
                        final result = await SecretariaFormSheet.show(context,
                            secretaria: secretaria);
                        if (result == true) {
                          ref.invalidate(secretariasProvider);
                        }
                      } else if (value == 'delete') {
                        final password = await PasswordConfirmationDialog.show(
                          context,
                          title: 'Eliminar Secretaria',
                          message:
                              'Introduce tu contraseña para confirmar la eliminación de ${secretaria.nombre}.',
                        );
                        if (password != null) {
                          try {
                            await ref
                                .read(secretariasRepositoryProvider)
                                .deleteSecretaria(secretaria.id, password);
                            ref.invalidate(secretariasProvider);
                          } catch (e) {
                            if (context.mounted) {
                              ScaffoldMessenger.of(context).showSnackBar(
                                SnackBar(
                                    content: Text('Error: $e'),
                                    backgroundColor: scheme.error),
                              );
                            }
                          }
                        }
                      }
                    },
                    itemBuilder: (context) => [
                      const PopupMenuItem(value: 'edit', child: Text('Editar')),
                      const PopupMenuItem(
                          value: 'delete',
                          child: Text('Eliminar',
                              style: TextStyle(color: Colors.red))),
                    ],
                  ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}
