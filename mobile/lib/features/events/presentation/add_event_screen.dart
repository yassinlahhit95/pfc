import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';

import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/premium.dart';
import '../data/events_repository.dart';

class AddEventScreen extends ConsumerStatefulWidget {
  const AddEventScreen({super.key, this.eventToEdit});
  final SchoolEvent? eventToEdit;

  @override
  ConsumerState<AddEventScreen> createState() => _AddEventScreenState();
}

class _AddEventScreenState extends ConsumerState<AddEventScreen> {
  final _formKey = GlobalKey<FormState>();
  late final TextEditingController _tituloController;
  late final TextEditingController _descripcionController;
  late final TextEditingController _ubicacionController;

  DateTime? _selectedDate;
  TimeOfDay? _selectedTime;
  bool _isSaving = false;

  @override
  void initState() {
    super.initState();
    _tituloController = TextEditingController(text: widget.eventToEdit?.titulo ?? '');
    _descripcionController = TextEditingController(text: widget.eventToEdit?.descripcion ?? '');
    _ubicacionController = TextEditingController(text: widget.eventToEdit?.ubicacion ?? '');

    if (widget.eventToEdit != null) {
      try {
        _selectedDate = DateTime.parse(widget.eventToEdit!.fecha);
      } catch (_) {}
      try {
        final parts = widget.eventToEdit!.hora.split(':');
        _selectedTime = TimeOfDay(hour: int.parse(parts[0]), minute: int.parse(parts[1]));
      } catch (_) {}
    }
  }

  @override
  void dispose() {
    _tituloController.dispose();
    _descripcionController.dispose();
    _ubicacionController.dispose();
    super.dispose();
  }

  Future<void> _pickDate() async {
    final now = DateTime.now();
    final date = await showDatePicker(
      context: context,
      initialDate: _selectedDate ?? now,
      firstDate: now.subtract(const Duration(days: 365)),
      lastDate: now.add(const Duration(days: 365 * 2)),
    );
    if (date != null) {
      setState(() => _selectedDate = date);
    }
  }

  Future<void> _pickTime() async {
    final time = await showTimePicker(
      context: context,
      initialTime: _selectedTime ?? TimeOfDay.now(),
    );
    if (time != null) {
      setState(() => _selectedTime = time);
    }
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) return;
    if (_selectedDate == null) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Debe seleccionar una fecha')));
      return;
    }
    if (_selectedTime == null) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Debe seleccionar una hora')));
      return;
    }

    setState(() => _isSaving = true);

    try {
      final repo = ref.read(eventsRepositoryProvider);
      final fechaStr = DateFormat('yyyy-MM-dd').format(_selectedDate!);
      final horaStr = '${_selectedTime!.hour.toString().padLeft(2, '0')}:${_selectedTime!.minute.toString().padLeft(2, '0')}:00';

      final newEvent = SchoolEvent(
        id: widget.eventToEdit?.id ?? 0,
        titulo: _tituloController.text.trim(),
        descripcion: _descripcionController.text.trim(),
        fecha: fechaStr,
        hora: horaStr,
        ubicacion: _ubicacionController.text.trim(),
      );

      if (widget.eventToEdit != null) {
        await repo.updateEvent(newEvent);
      } else {
        await repo.createEvent(newEvent);
      }

      ref.invalidate(eventsProvider);
      if (mounted) Navigator.pop(context);
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Error: $e')));
      }
    } finally {
      if (mounted) setState(() => _isSaving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final isEditing = widget.eventToEdit != null;

    return Scaffold(
      appBar: AppBar(title: Text(isEditing ? 'Editar Evento' : 'Nuevo Evento')),
      body: _isSaving
          ? const Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              padding: const EdgeInsets.all(Space.xl),
              child: Form(
                key: _formKey,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    TextFormField(
                      controller: _tituloController,
                      decoration: const InputDecoration(labelText: 'Título', border: OutlineInputBorder()),
                      validator: (v) => v == null || v.trim().isEmpty ? 'Requerido' : null,
                    ),
                    const SizedBox(height: Space.lg),
                    TextFormField(
                      controller: _descripcionController,
                      maxLines: 4,
                      decoration: const InputDecoration(labelText: 'Descripción', border: OutlineInputBorder()),
                    ),
                    const SizedBox(height: Space.lg),
                    Row(
                      children: [
                        Expanded(
                          child: OutlinedButton.icon(
                            onPressed: _pickDate,
                            icon: const Icon(Icons.calendar_today),
                            label: Text(_selectedDate == null ? 'Fecha' : DateFormat('dd/MM/yyyy').format(_selectedDate!)),
                          ),
                        ),
                        const SizedBox(width: Space.md),
                        Expanded(
                          child: OutlinedButton.icon(
                            onPressed: _pickTime,
                            icon: const Icon(Icons.access_time),
                            label: Text(_selectedTime == null ? 'Hora' : _selectedTime!.format(context)),
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: Space.lg),
                    TextFormField(
                      controller: _ubicacionController,
                      decoration: const InputDecoration(labelText: 'Ubicación', border: OutlineInputBorder()),
                    ),
                    const SizedBox(height: Space.xxxl),
                    FilledButton(
                      onPressed: _save,
                      child: const Text('Guardar'),
                    ),
                  ],
                ),
              ),
            ),
    );
  }
}
