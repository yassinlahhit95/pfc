import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_exception.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/error_modal.dart';
import '../data/account_repository.dart';

class ChangePasswordScreen extends ConsumerStatefulWidget {
  const ChangePasswordScreen({super.key});

  @override
  ConsumerState<ChangePasswordScreen> createState() => _ChangePasswordScreenState();
}

class _ChangePasswordScreenState extends ConsumerState<ChangePasswordScreen> {
  final _formKey = GlobalKey<FormState>();
  final _currentCtrl = TextEditingController();
  final _newCtrl = TextEditingController();
  final _confirmCtrl = TextEditingController();
  bool _obscureCurrent = true;
  bool _obscureNew = true;
  bool _saving = false;

  @override
  void dispose() {
    _currentCtrl.dispose();
    _newCtrl.dispose();
    _confirmCtrl.dispose();
    super.dispose();
  }

  String? _validateNewPassword(String? v) {
    final value = v ?? '';
    if (value.length < 8) return 'Mínimo 8 caracteres';
    if (!RegExp(r'[A-Z]').hasMatch(value)) return 'Debe incluir una mayúscula';
    if (!RegExp(r'[a-z]').hasMatch(value)) return 'Debe incluir una minúscula';
    if (!RegExp(r'[0-9]').hasMatch(value)) return 'Debe incluir un número';
    return null;
  }

  Future<void> _submit() async {
    if (!_formKey.currentState!.validate()) return;
    if (_newCtrl.text != _confirmCtrl.text) {
      await showErrorAlert(context, 'Las contraseñas nuevas no coinciden.');
      return;
    }
    setState(() => _saving = true);
    try {
      await ref.read(accountRepositoryProvider).changePassword(
            currentPassword: _currentCtrl.text,
            newPassword: _newCtrl.text,
          );
      if (mounted) {
        await showErrorAlert(context, 'Contraseña actualizada.', title: 'Éxito');
        Navigator.of(context).pop();
      }
    } catch (e) {
      final message = e is ApiException ? e.message : 'No se pudo actualizar la contraseña.';
      if (mounted) await showErrorAlert(context, message);
    } finally {
      if (mounted) setState(() => _saving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(title: const Text('Cambiar contraseña')),
      body: SafeArea(
        child: SingleChildScrollView(
          padding: const EdgeInsets.all(Space.xl),
          child: Form(
            key: _formKey,
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                TextFormField(
                  controller: _currentCtrl,
                  obscureText: _obscureCurrent,
                  decoration: InputDecoration(
                    labelText: 'Contraseña actual',
                    suffixIcon: IconButton(
                      icon: Icon(_obscureCurrent ? Icons.visibility_outlined : Icons.visibility_off_outlined),
                      onPressed: () => setState(() => _obscureCurrent = !_obscureCurrent),
                    ),
                  ),
                  validator: (v) => (v == null || v.isEmpty) ? 'Requerido' : null,
                ),
                const SizedBox(height: Space.lg),
                TextFormField(
                  controller: _newCtrl,
                  obscureText: _obscureNew,
                  decoration: InputDecoration(
                    labelText: 'Nueva contraseña',
                    suffixIcon: IconButton(
                      icon: Icon(_obscureNew ? Icons.visibility_outlined : Icons.visibility_off_outlined),
                      onPressed: () => setState(() => _obscureNew = !_obscureNew),
                    ),
                    helperText: 'Mínimo 8 caracteres, con mayúscula, minúscula y número',
                    helperMaxLines: 2,
                  ),
                  validator: _validateNewPassword,
                ),
                const SizedBox(height: Space.lg),
                TextFormField(
                  controller: _confirmCtrl,
                  obscureText: _obscureNew,
                  decoration: const InputDecoration(labelText: 'Confirmar nueva contraseña'),
                  validator: (v) => (v == null || v.isEmpty) ? 'Requerido' : null,
                ),
                const SizedBox(height: Space.xxl),
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
      ),
    );
  }
}
