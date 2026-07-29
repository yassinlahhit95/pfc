import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:image_picker/image_picker.dart';
import 'package:intl/intl.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/error_modal.dart';
import '../../../core/widgets/photo_picker_sheet.dart';
import '../data/gastos_repository.dart';

Future<bool> showGastoFormSheet(
  BuildContext context,
  WidgetRef ref, {
  required List<CategoriaGasto> categorias,
}) async {
  final conceptoController = TextEditingController();
  final importeController = TextEditingController();
  final fechaController = TextEditingController(text: DateFormat('yyyy-MM-dd').format(DateTime.now()));
  int? selectedCategoria;
  File? archivo;

  final sent = await showModalBottomSheet<bool>(
    context: context,
    isScrollControlled: true,
    backgroundColor: Colors.transparent,
    builder: (ctx) => StatefulBuilder(
      builder: (ctx, setSheetState) => Container(
        padding: const EdgeInsets.fromLTRB(Space.xl, Space.md, Space.xl, Space.xl),
        decoration: BoxDecoration(
          color: Theme.of(ctx).colorScheme.surface,
          borderRadius: const BorderRadius.vertical(top: Radius.circular(Radii.xl)),
        ),
        child: Padding(
          padding: EdgeInsets.only(bottom: MediaQuery.of(ctx).viewInsets.bottom),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Container(
                width: 36,
                height: 4,
                margin: const EdgeInsets.only(bottom: Space.lg),
                decoration: BoxDecoration(
                    color: Theme.of(ctx).colorScheme.outlineVariant, borderRadius: BorderRadius.circular(Radii.pill)),
              ),
              Text('Registrar Gasto', style: Theme.of(ctx).textTheme.titleMedium),
              const SizedBox(height: Space.xl),
              DropdownButtonFormField<int>(
                value: selectedCategoria,
                decoration: const InputDecoration(labelText: 'Categoría'),
                items: categorias.map((cat) {
                  return DropdownMenuItem(
                    value: cat.idCategoria,
                    child: Text(cat.nombre),
                  );
                }).toList(),
                onChanged: (val) => setSheetState(() => selectedCategoria = val),
              ),
              const SizedBox(height: Space.md),
              TextField(
                controller: conceptoController,
                decoration: const InputDecoration(labelText: 'Concepto', prefixIcon: Icon(Icons.description_outlined)),
              ),
              const SizedBox(height: Space.md),
              TextField(
                controller: importeController,
                keyboardType: const TextInputType.numberWithOptions(decimal: true),
                decoration: const InputDecoration(labelText: 'Importe (€)', prefixIcon: Icon(Icons.euro_rounded)),
              ),
              const SizedBox(height: Space.md),
              TextField(
                controller: fechaController,
                readOnly: true,
                decoration: const InputDecoration(labelText: 'Fecha', prefixIcon: Icon(Icons.calendar_today_outlined)),
                onTap: () async {
                  final date = await showDatePicker(
                    context: ctx,
                    initialDate: DateTime.now(),
                    firstDate: DateTime(2000),
                    lastDate: DateTime(2100),
                  );
                  if (date != null) {
                    setSheetState(() => fechaController.text = DateFormat('yyyy-MM-dd').format(date));
                  }
                },
              ),
              const SizedBox(height: Space.lg),
              if (archivo != null)
                ListTile(
                  contentPadding: EdgeInsets.zero,
                  leading: const Icon(Icons.image, color: Colors.green),
                  title: const Text('Foto adjunta'),
                  trailing: IconButton(
                    icon: const Icon(Icons.delete_outline, color: Colors.red),
                    onPressed: () => setSheetState(() => archivo = null),
                  ),
                )
              else
                OutlinedButton.icon(
                  icon: const Icon(Icons.add_a_photo_outlined),
                  label: const Text('Adjuntar foto del ticket'),
                  onPressed: () async {
                    final picker = ImagePicker();
                    final picked = await picker.pickImage(source: ImageSource.gallery, imageQuality: 70, maxWidth: 800);
                    if (picked != null) setSheetState(() => archivo = File(picked.path));
                  },
                ),
              const SizedBox(height: Space.xl),
              FilledButton(
                onPressed: () async {
                  final concepto = conceptoController.text.trim();
                  final importe = double.tryParse(importeController.text.trim()) ?? 0;
                  if (selectedCategoria == null || concepto.isEmpty || importe <= 0) {
                    await showErrorAlert(ctx, 'Concepto, importe y categoría son obligatorios.');
                    return;
                  }
                  try {
                    await ref.read(gastosRepositoryProvider).registrarGasto(
                          idCategoria: selectedCategoria!,
                          concepto: concepto,
                          importe: importe,
                          fecha: fechaController.text,
                          archivo: archivo,
                        );
                    if (ctx.mounted) Navigator.of(ctx).pop(true);
                  } catch (_) {
                    if (ctx.mounted) {
                      await showErrorAlert(ctx, 'Error al registrar el gasto');
                    }
                  }
                },
                child: const Text('Guardar gasto'),
              ),
            ],
          ),
        ),
      ),
    ),
  );
  return sent == true;
}
