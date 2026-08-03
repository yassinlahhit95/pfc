import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/theme/app_theme.dart';
import '../data/family_repository.dart';

class TutorFormSheet extends ConsumerStatefulWidget {
  const TutorFormSheet({
    super.key,
    required this.idEstudiante,
    this.tutor,
  });

  final int idEstudiante;
  final Tutor? tutor;

  static Future<bool?> show(BuildContext context, int idEstudiante,
      {Tutor? tutor}) {
    return showModalBottomSheet<bool>(
      context: context,
      isScrollControlled: true,
      builder: (context) => Padding(
        padding:
            EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
        child: TutorFormSheet(idEstudiante: idEstudiante, tutor: tutor),
      ),
    );
  }

  @override
  ConsumerState<TutorFormSheet> createState() => _TutorFormSheetState();
}

class _TutorFormSheetState extends ConsumerState<TutorFormSheet> {
  final _formKey = GlobalKey<FormState>();
  late TextEditingController _nombreController;
  late TextEditingController _emailController;
  late TextEditingController _dniController;
  late TextEditingController _telefonoController;
  String _parentesco = 'Madre';
  bool _isLoading = false;
  String? _errorMessage;
  String? _successMessage;

  @override
  void initState() {
    super.initState();
    _nombreController = TextEditingController(text: widget.tutor?.nombre);
    _emailController = TextEditingController(text: widget.tutor?.email);
    _dniController = TextEditingController(text: widget.tutor?.dni);
    _telefonoController = TextEditingController(text: widget.tutor?.telefono);
    _parentesco = widget.tutor?.parentesco ?? 'Madre';
    if (!['Padre', 'Madre', 'Tutor Legal', 'Otro'].contains(_parentesco)) {
      _parentesco = 'Otro';
    }
  }

  @override
  void dispose() {
    _nombreController.dispose();
    _emailController.dispose();
    _dniController.dispose();
    _telefonoController.dispose();
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
      final repo = ref.read(familyRepositoryProvider);
      final data = {
        'nombreTutor': _nombreController.text.trim(),
        'emailTutor': _emailController.text.trim(),
        'dniTutor': _dniController.text.trim(),
        'telefonoTutor': _telefonoController.text.trim(),
        'parentesco': _parentesco,
      };

      if (widget.tutor == null) {
        await repo.addTutorToStudent(widget.idEstudiante, data);
      } else {
        await repo.updateTutor(widget.idEstudiante, widget.tutor!.id, data);
      }

      if (mounted) {
        setState(() => _successMessage = widget.tutor == null
            ? 'Familiar añadido con éxito'
            : 'Familiar actualizado con éxito');
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
      padding: const EdgeInsets.all(Space.xl),
      child: Form(
        key: _formKey,
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Text(
              widget.tutor == null
                  ? 'Añadir Familiar/Tutor'
                  : 'Editar Familiar/Tutor',
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
                  style: TextStyle(
                      color: Theme.of(context).colorScheme.onErrorContainer),
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
            const SizedBox(height: Space.xl),
            TextFormField(
              controller: _nombreController,
              decoration: const InputDecoration(
                  labelText: 'Nombre Completo', border: OutlineInputBorder()),
              validator: (v) => v == null || v.isEmpty ? 'Requerido' : null,
            ),
            const SizedBox(height: Space.md),
            TextFormField(
              controller: _emailController,
              decoration: const InputDecoration(
                  labelText: 'Email', border: OutlineInputBorder()),
              keyboardType: TextInputType.emailAddress,
              validator: (v) =>
                  v == null || !v.contains('@') ? 'Email inválido' : null,
            ),
            const SizedBox(height: Space.md),
            TextFormField(
              controller: _dniController,
              decoration: const InputDecoration(
                  labelText: 'DNI/NIE', border: OutlineInputBorder()),
              validator: (v) => v == null || v.isEmpty ? 'Requerido' : null,
            ),
            const SizedBox(height: Space.md),
            TextFormField(
              controller: _telefonoController,
              decoration: const InputDecoration(
                  labelText: 'Teléfono', border: OutlineInputBorder()),
              keyboardType: TextInputType.phone,
            ),
            const SizedBox(height: Space.md),
            DropdownButtonFormField<String>(
              initialValue: _parentesco,
              decoration: const InputDecoration(
                  labelText: 'Parentesco', border: OutlineInputBorder()),
              items: const [
                DropdownMenuItem(value: 'Madre', child: Text('Madre')),
                DropdownMenuItem(value: 'Padre', child: Text('Padre')),
                DropdownMenuItem(
                    value: 'Tutor Legal', child: Text('Tutor Legal')),
                DropdownMenuItem(value: 'Otro', child: Text('Otro')),
              ],
              onChanged: (val) {
                if (val != null) setState(() => _parentesco = val);
              },
            ),
            const SizedBox(height: Space.xl),
            FilledButton(
              onPressed: _isLoading ? null : _submit,
              child: _isLoading
                  ? const SizedBox(
                      width: 20,
                      height: 20,
                      child: CircularProgressIndicator(
                          strokeWidth: 2, color: Colors.white))
                  : const Text('Guardar'),
            ),
          ],
        ),
      ),
    );
  }
}
