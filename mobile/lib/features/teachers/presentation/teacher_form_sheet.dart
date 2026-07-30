import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/theme/app_theme.dart';
import '../data/teachers_repository.dart';

class TeacherFormSheet extends ConsumerStatefulWidget {
  const TeacherFormSheet({super.key, this.teacher});
  final Teacher? teacher;

  static Future<bool?> show(BuildContext context, {Teacher? teacher}) {
    return showModalBottomSheet<bool>(
      context: context,
      isScrollControlled: true,
      builder: (context) => Padding(
        padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
        child: TeacherFormSheet(teacher: teacher),
      ),
    );
  }

  @override
  ConsumerState<TeacherFormSheet> createState() => _TeacherFormSheetState();
}

class _TeacherFormSheetState extends ConsumerState<TeacherFormSheet> {
  final _formKey = GlobalKey<FormState>();
  late final TextEditingController _nombreCtrl;
  late final TextEditingController _emailCtrl;
  late final TextEditingController _telefonoCtrl;
  late final TextEditingController _dniCtrl;
  late final TextEditingController _direccionCtrl;
  late final TextEditingController _fechaNacimientoCtrl;
  late final TextEditingController _ciudadCtrl;
  late final TextEditingController _codigoPostalCtrl;
  late final TextEditingController _observacionesCtrl;
  bool _isLoading = false;
  String? _errorMessage;
  String? _successMessage;

  @override
  void initState() {
    super.initState();
    final t = widget.teacher;
    _nombreCtrl = TextEditingController(text: t?.nombre ?? '');
    _emailCtrl = TextEditingController(text: t?.email ?? '');
    _telefonoCtrl = TextEditingController(text: t?.telefono ?? '');
    _dniCtrl = TextEditingController();
    _direccionCtrl = TextEditingController();
    _fechaNacimientoCtrl = TextEditingController();
    _ciudadCtrl = TextEditingController();
    _codigoPostalCtrl = TextEditingController();
    _observacionesCtrl = TextEditingController();
  }

  @override
  void dispose() {
    _nombreCtrl.dispose();
    _emailCtrl.dispose();
    _telefonoCtrl.dispose();
    _dniCtrl.dispose();
    _direccionCtrl.dispose();
    _fechaNacimientoCtrl.dispose();
    _ciudadCtrl.dispose();
    _codigoPostalCtrl.dispose();
    _observacionesCtrl.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    setState(() {
      _isLoading = true;
      _errorMessage = null;
      _successMessage = null;
    });
    try {
      final data = {
        if (widget.teacher != null) 'idProfesor': widget.teacher!.id,
        'nombreProfesor': _nombreCtrl.text.trim(),
        'emailProfesor': _emailCtrl.text.trim(),
        'telefonoProfesor': _telefonoCtrl.text.trim(),
        'dniProfesor': _dniCtrl.text.trim(),
        'direccionProfesor': _direccionCtrl.text.trim(),
        'fechaNacimientoProfesor': _fechaNacimientoCtrl.text.trim(),
        'ciudadProfesor': _ciudadCtrl.text.trim(),
        'codigoPostalProfesor': _codigoPostalCtrl.text.trim(),
        'observacionesProfesor': _observacionesCtrl.text.trim(),
      };
      
      if (widget.teacher == null) {
        await ref.read(teachersRepositoryProvider).createTeacher(data);
      } else {
        await ref.read(teachersRepositoryProvider).updateTeacher(data);
      }
      if (mounted) {
        setState(() => _successMessage = widget.teacher == null ? 'Profesor creado con éxito' : 'Profesor actualizado con éxito');
        await Future.delayed(const Duration(seconds: 1));
        if (mounted) Navigator.pop(context, true);
      }
    } catch (e) {
      if (mounted) {
        setState(() => _errorMessage = 'Error: $e');
      }
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final textTheme = Theme.of(context).textTheme;

    return Padding(
      padding: const EdgeInsets.all(Space.md),
      child: Form(
        key: _formKey,
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Text(
              widget.teacher == null ? 'Nuevo Profesor' : 'Editar Profesor',
              style: textTheme.titleLarge,
              textAlign: TextAlign.center,
            ),
            if (_errorMessage != null) ...[
              const SizedBox(height: Space.md),
              Container(
                padding: const EdgeInsets.all(Space.sm),
                color: Theme.of(context).colorScheme.errorContainer,
                child: Text(
                  _errorMessage!,
                  style: TextStyle(color: Theme.of(context).colorScheme.onErrorContainer),
                  textAlign: TextAlign.center,
                ),
              ),
            ],
            if (_successMessage != null) ...[
              const SizedBox(height: Space.md),
              Container(
                padding: const EdgeInsets.all(Space.sm),
                color: Colors.green.shade100,
                child: Text(
                  _successMessage!,
                  style: TextStyle(color: Colors.green.shade900),
                  textAlign: TextAlign.center,
                ),
              ),
            ],
            const SizedBox(height: Space.lg),
            TextFormField(
              controller: _nombreCtrl,
              decoration: const InputDecoration(labelText: 'Nombre Completo *'),
              validator: (v) => v == null || v.isEmpty ? 'Requerido' : null,
            ),
            const SizedBox(height: Space.md),
            TextFormField(
              controller: _emailCtrl,
              decoration: const InputDecoration(labelText: 'Correo Electrónico *'),
              keyboardType: TextInputType.emailAddress,
              validator: (v) => v == null || v.isEmpty ? 'Requerido' : null,
            ),
            const SizedBox(height: Space.md),
            TextFormField(
              controller: _telefonoCtrl,
              decoration: const InputDecoration(labelText: 'Teléfono'),
              keyboardType: TextInputType.phone,
            ),
            if (widget.teacher == null) ...[
              const SizedBox(height: Space.md),
              TextFormField(
                controller: _dniCtrl,
                decoration: const InputDecoration(labelText: 'DNI / NIF'),
              ),
            ],
            const SizedBox(height: Space.xl),
            FilledButton(
              onPressed: _isLoading ? null : _submit,
              child: _isLoading ? const CircularProgressIndicator() : const Text('Guardar'),
            ),
          ],
        ),
      ),
    );
  }
}
