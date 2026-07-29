import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_exception.dart';
import '../../../core/auth/session.dart';
import '../../profile/data/profile_repository.dart';
import '../data/account_repository.dart';

// Mirrors api/v1/profile.php's PROFILE_EDITABLE_FIELDS whitelist exactly —
// keep in sync if that changes. label -> field name.
const Map<UserRole, Map<String, String>> _editableFields = {
  UserRole.estudiante: {
    'Teléfono': 'telefonoEstudiante',
    'Dirección': 'direccionEstudiante',
    'Ciudad': 'ciudadEstudiante',
    'Código postal': 'codigoPostalEstudiante',
  },
  UserRole.profesor: {
    'Teléfono': 'telefonoProfesor',
    'Dirección': 'direccionProfesor',
    'Ciudad': 'ciudadProfesor',
    'Código postal': 'codigoPostalProfesor',
  },
  UserRole.director: {
    'Teléfono': 'telefonoDirector',
    'Dirección': 'direccionDirector',
    'Ciudad': 'ciudadDirector',
    'Código postal': 'codigoPostalDirector',
  },
  UserRole.tutor: {'Teléfono': 'telefonoTutor'},
  UserRole.secretaria: {},
};

class EditProfileScreen extends ConsumerStatefulWidget {
  const EditProfileScreen({super.key, required this.role, required this.profile});

  final UserRole role;
  final Profile profile;

  @override
  ConsumerState<EditProfileScreen> createState() => _EditProfileScreenState();
}

class _EditProfileScreenState extends ConsumerState<EditProfileScreen> {
  final _controllers = <String, TextEditingController>{};
  bool _saving = false;

  @override
  void initState() {
    super.initState();
    for (final field in (_editableFields[widget.role] ?? {}).values) {
      _controllers[field] = TextEditingController(text: widget.profile.data[field]?.toString() ?? '');
    }
  }

  @override
  void dispose() {
    for (final c in _controllers.values) {
      c.dispose();
    }
    super.dispose();
  }

  Future<void> _submit() async {
    setState(() => _saving = true);
    try {
      await ref.read(accountRepositoryProvider).updateProfile(
            {for (final e in _controllers.entries) e.key: e.value.text.trim()},
          );
      ref.invalidate(profileProvider);
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Perfil actualizado.')));
        Navigator.of(context).pop();
      }
    } catch (e) {
      final message = e is ApiException ? e.message : 'No se pudo actualizar el perfil.';
      if (mounted) ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(message)));
    } finally {
      if (mounted) setState(() => _saving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final fields = _editableFields[widget.role] ?? {};

    return Scaffold(
      appBar: AppBar(title: const Text('Editar perfil')),
      body: SafeArea(
        child: fields.isEmpty
            ? const Center(
                child: Padding(
                  padding: EdgeInsets.all(24),
                  child: Text('Este rol no tiene datos de contacto editables desde la app.'),
                ),
              )
            : SingleChildScrollView(
                padding: const EdgeInsets.all(20),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.stretch,
                  children: [
                    for (final entry in fields.entries) ...[
                      TextFormField(
                        controller: _controllers[entry.value],
                        decoration: InputDecoration(labelText: entry.key),
                      ),
                      const SizedBox(height: 16),
                    ],
                    const SizedBox(height: 12),
                    FilledButton(
                      onPressed: _saving ? null : _submit,
                      child: _saving
                          ? SizedBox(
                              height: 20,
                              width: 20,
                              child: CircularProgressIndicator(
                                strokeWidth: 2,
                                valueColor: AlwaysStoppedAnimation<Color>(Theme.of(context).colorScheme.onPrimary),
                              ),
                            )
                          : const Text('Guardar'),
                    ),
                  ],
                ),
              ),
      ),
    );
  }
}
