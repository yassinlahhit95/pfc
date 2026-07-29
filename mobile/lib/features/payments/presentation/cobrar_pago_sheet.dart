import 'dart:io';

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/photo_picker_sheet.dart';
import '../data/payments_repository.dart';

class CobrarPagoSheetChrome extends StatelessWidget {
  const CobrarPagoSheetChrome({super.key, required this.child});
  final Widget child;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Container(
      padding: const EdgeInsets.fromLTRB(Space.xl, Space.md, Space.xl, Space.xl),
      decoration: BoxDecoration(
        color: scheme.surface,
        borderRadius: const BorderRadius.vertical(top: Radius.circular(Radii.xl)),
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            width: 36,
            height: 4,
            margin: const EdgeInsets.only(bottom: Space.lg),
            decoration: BoxDecoration(color: scheme.outlineVariant, borderRadius: BorderRadius.circular(Radii.pill)),
          ),
          child,
        ],
      ),
    );
  }
}

Future<bool> showCobrarPagoSheet(
  BuildContext context,
  WidgetRef ref, {
  required int idEstudiante,
  required String nombreEstudiante,
  required double deudaActual,
}) async {
  final montoController = TextEditingController(text: deudaActual.toStringAsFixed(2));
  final proximoPagoController = TextEditingController(
    text: DateFormat('yyyy-MM-dd').format(DateTime(DateTime.now().year, DateTime.now().month + 1, DateTime.now().day)),
  );
  String tipoPago = 'mensual';
  File? archivo;

  final sent = await showModalBottomSheet<bool>(
    context: context,
    isScrollControlled: true,
    backgroundColor: Colors.transparent,
    builder: (ctx) => StatefulBuilder(
      builder: (ctx, setSheetState) => CobrarPagoSheetChrome(
        child: Padding(
          padding: EdgeInsets.only(bottom: MediaQuery.of(ctx).viewInsets.bottom),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.stretch,
            children: [
              Text('Registrar Cobro de Pago', style: Theme.of(ctx).textTheme.titleMedium),
              const SizedBox(height: 4),
              Text(nombreEstudiante, style: Theme.of(ctx).textTheme.bodySmall),
              const SizedBox(height: Space.xl),
              TextField(
                controller: montoController,
                keyboardType: const TextInputType.numberWithOptions(decimal: true),
                decoration: const InputDecoration(
                  labelText: 'Monto recibido (€)',
                  prefixIcon: Icon(Icons.euro_rounded),
                ),
              ),
              const SizedBox(height: Space.md),
              DropdownButtonFormField<String>(
                initialValue: tipoPago,
                decoration: const InputDecoration(labelText: 'Tipo de Pago'),
                items: const [
                  DropdownMenuItem(value: 'mensual', child: Text('Mensualidad')),
                  DropdownMenuItem(value: 'trimestral', child: Text('Trimestral')),
                  DropdownMenuItem(value: 'semestral', child: Text('Semestral')),
                  DropdownMenuItem(value: 'unico', child: Text('Pago Único')),
                ],
                onChanged: (val) {
                  if (val != null) {
                    setSheetState(() {
                      tipoPago = val;
                      if (val == 'unico') {
                        proximoPagoController.clear();
                      } else {
                        DateTime nextDate = DateTime.now();
                        if (val == 'mensual') nextDate = DateTime(nextDate.year, nextDate.month + 1, nextDate.day);
                        else if (val == 'trimestral') nextDate = DateTime(nextDate.year, nextDate.month + 3, nextDate.day);
                        else if (val == 'semestral') nextDate = DateTime(nextDate.year, nextDate.month + 6, nextDate.day);
                        proximoPagoController.text = DateFormat('yyyy-MM-dd').format(nextDate);
                      }
                    });
                  }
                },
              ),
              const SizedBox(height: Space.md),
              TextField(
                controller: proximoPagoController,
                readOnly: true,
                decoration: const InputDecoration(
                  labelText: 'Fecha del próximo pago (opcional)',
                  prefixIcon: Icon(Icons.calendar_month_rounded),
                ),
                onTap: () async {
                  final picked = await showDatePicker(
                    context: ctx,
                    initialDate: DateTime.now().add(const Duration(days: 30)),
                    firstDate: DateTime.now(),
                    lastDate: DateTime.now().add(const Duration(days: 365)),
                  );
                  if (picked != null) {
                    setSheetState(() {
                      proximoPagoController.text = DateFormat('yyyy-MM-dd').format(picked);
                    });
                  }
                },
              ),
              const SizedBox(height: Space.md),
              OutlinedButton.icon(
                onPressed: () async {
                  final picked = await pickPhoto(ctx);
                  if (picked != null) setSheetState(() => archivo = picked);
                },
                icon: Icon(archivo == null ? Icons.add_a_photo_outlined : Icons.check_circle_outline),
                label: Text(archivo == null ? 'Foto del justificante de pago (opcional)' : 'Foto adjuntada'),
              ),
              const SizedBox(height: Space.xl),
              FilledButton(
                onPressed: () async {
                  final montoVal = double.tryParse(montoController.text) ?? 0.0;
                  if (montoVal <= 0) {
                    ScaffoldMessenger.of(ctx).showSnackBar(
                      const SnackBar(content: Text('El monto debe ser un número válido mayor a 0.')),
                    );
                    return;
                  }
                  try {
                    await ref.read(paymentsRepositoryProvider).registrarCobroPago(
                          idEstudiante: idEstudiante,
                          monto: montoVal,
                          tipoPago: tipoPago,
                          fechaProximoPago: proximoPagoController.text.isNotEmpty ? proximoPagoController.text : null,
                          archivo: archivo,
                        );
                    if (ctx.mounted) Navigator.of(ctx).pop(true);
                  } catch (_) {
                    if (ctx.mounted) {
                      ScaffoldMessenger.of(ctx).showSnackBar(
                        const SnackBar(content: Text('No se pudo registrar el pago.')),
                      );
                    }
                  }
                },
                child: const Text('Registrar Pago'),
              ),
            ],
          ),
        ),
      ),
    ),
  );
  return sent == true;
}
