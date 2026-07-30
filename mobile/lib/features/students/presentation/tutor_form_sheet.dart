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

  static Future<bool?> show(BuildContext context, int idEstudiante, {Tutor? tutor}) {
    return showModalBottomSheet<bool>(
      context: context,
      isScrollControlled: true,
      builder: (context) => Padding(
        padding: EdgeInsets.only(bottom: MediaQuery.of(context).viewInsets.bottom),
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
  late TextEditingController _parentescoController;
  bool _isLoading = false;

  @override
  void initState() {
    super.initState();
    _nombreController = TextEditingController(text: widget.tutor?.nombre);
    _emailController = TextEditingController(text: widget.tutor?.email);
    _dniController = TextEditingController(text: widget.tutor?.dni);
    _telefonoController = TextEditingController(text: widget.tutor?.telefono);
    _parentescoController = TextEditingController(text: widget.tutor?.parentesco ?? 'Tutor');
  }

  @override
  void dispose() {
    _nombreController.dispose();
    _emailController.dispose();
    _dniController.dispose();
    _telefonoController.dispose();
    _parentescoController.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;

    setState(() => _isLoading = true);

    try {
      final repo = ref.read(familyRepositoryProvider);
      final data = {
        'nombreTutor': _nombreController.text.trim(),
        'emailTutor': _emailController.text.trim(),
        'dniTutor': _dniController.text.trim(),
        'telefonoTutor': _telefonoController.text.trim(),
        'parentesco': _parentescoController.text.trim(),
      };

      if (widget.tutor == null) {
        await repo.addTutorToStudent(widget.idEstudiante, data);
      } else {
        await repo.updateTutor(widget.idEstudiante, widget.tutor!.id, data);
      }

      if (mounted) {
        Navigator.pop(context, true);
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error: $e')),
        );
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
              widget.tutor == null ? 'Añadir Familiar/Tutor' : 'Editar Familiar/Tutor',
              style: textTheme.titleLarge,
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: Space.xl),
            TextFormField(
              controller: _nombreController,
              decoration: const InputDecoration(labelText: 'Nombre Completo', border: OutlineInputBorder()),
              validator: (v) => v == null || v.isEmpty ? 'Requerido' : null,
            ),
            const SizedBox(height: Space.md),
            TextFormField(
              controller: _emailController,
              decoration: const InputDecoration(labelText: 'Email', border: OutlineInputBorder()),
              keyboardType: TextInputType.emailAddress,
              validator: (v) => v == null || !v.contains('@') ? 'Email inválido' : null,
            ),
            const SizedBox(height: Space.md),
            TextFormField(
              controller: _dniController,
              decoration: const InputDecoration(labelText: 'DNI/NIE', border: OutlineInputBorder()),
              validator: (v) => v == null || v.isEmpty ? 'Requerido' : null,
            ),
            const SizedBox(height: Space.md),
            TextFormField(
              controller: _telefonoController,
              decoration: const InputDecoration(labelText: 'Teléfono', border: OutlineInputBorder()),
              keyboardType: TextInputType.phone,
            ),
            const SizedBox(height: Space.md),
            TextFormField(
              controller: _parentescoController,
              decoration: const InputDecoration(labelText: 'Parentesco (ej. Padre, Madre)', border: OutlineInputBorder()),
            ),
            const SizedBox(height: Space.xl),
            FilledButton(
              onPressed: _isLoading ? null : _submit,
              child: _isLoading 
                ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                : const Text('Guardar'),
            ),
          ],
        ),
      ),
    );
  }
}
