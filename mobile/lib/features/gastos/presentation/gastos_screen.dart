import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/async_view.dart';
import '../../../core/widgets/error_modal.dart';
import '../data/gastos_repository.dart';
import 'gasto_form_sheet.dart';

class GastosScreen extends ConsumerWidget {
  const GastosScreen({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final gastosAsync = ref.watch(gastosListProvider);
    final scheme = Theme.of(context).colorScheme;

    return Scaffold(
      appBar: AppBar(title: const Text('Gastos del Centro')),
      floatingActionButton: gastosAsync.valueOrNull != null
          ? FloatingActionButton.extended(
              onPressed: () async {
                final categorias = gastosAsync.valueOrNull!.categorias;
                final bool created = await showGastoFormSheet(context, ref,
                    categorias: categorias);
                if (created) ref.invalidate(gastosListProvider);
              },
              icon: const Icon(Icons.add),
              label: const Text('Registrar Gasto'),
            )
          : null,
      body: AsyncView<({List<Gasto> gastos, List<CategoriaGasto> categorias})>(
        value: gastosAsync,
        onRetry: () => ref.invalidate(gastosListProvider),
        data: (context, data) {
          if (data.gastos.isEmpty) {
            return const EmptyState(
              icon: Icons.receipt_long_outlined,
              title: 'No hay gastos registrados',
            );
          }

          final formatCurrency = NumberFormat.simpleCurrency(locale: 'es_ES');

          return ListView.separated(
            padding: const EdgeInsets.all(Space.md).copyWith(bottom: 100),
            itemCount: data.gastos.length,
            separatorBuilder: (_, __) => const SizedBox(height: Space.sm),
            itemBuilder: (context, index) {
              final gasto = data.gastos[index];
              return Card(
                elevation: 0,
                color: scheme.surfaceContainerHighest,
                child: ListTile(
                  leading: CircleAvatar(
                    backgroundColor: _hexToColor(gasto.colorCategoria)
                        .withValues(alpha: 0.2),
                    child: Icon(Icons.shopping_bag_outlined,
                        color: _hexToColor(gasto.colorCategoria)),
                  ),
                  title: Text(gasto.concepto,
                      style: const TextStyle(fontWeight: FontWeight.bold)),
                  subtitle: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text('${gasto.nombreCategoria} • ${gasto.fecha}'),
                      if (gasto.nombreCreador != null)
                        Text(
                          'Registrado por: ${gasto.nombreCreador}',
                          style: TextStyle(
                            fontSize: 12,
                            color:
                                scheme.onSurfaceVariant.withValues(alpha: 0.8),
                          ),
                        ),
                    ],
                  ),
                  trailing: Text(
                    formatCurrency.format(gasto.importe),
                    style: TextStyle(
                      color: scheme.error,
                      fontWeight: FontWeight.bold,
                      fontSize: 16,
                    ),
                  ),
                  onTap: () async {
                    if (gasto.archivoJustificante != null &&
                        gasto.archivoJustificante != 'null') {
                      final url = ref
                          .read(gastosRepositoryProvider)
                          .downloadUrl(gasto.archivoJustificante!);
                      launchUrl(Uri.parse(url),
                          mode: LaunchMode.externalApplication);
                    } else {
                      await showErrorAlert(
                          context, 'Este gasto no tiene ticket adjunto');
                    }
                  },
                ),
              );
            },
          );
        },
      ),
    );
  }

  Color _hexToColor(String hexString) {
    var hexColor = hexString.replaceAll("#", "");
    if (hexColor.length == 6) hexColor = "FF$hexColor";
    return Color(int.parse("0x$hexColor"));
  }
}
