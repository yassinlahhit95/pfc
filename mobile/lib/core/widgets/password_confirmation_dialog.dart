import 'package:flutter/material.dart';

import '../theme/app_theme.dart';

class PasswordConfirmationDialog extends StatefulWidget {
  const PasswordConfirmationDialog(
      {super.key, required this.title, required this.message});
  final String title;
  final String message;

  static Future<String?> show(BuildContext context,
      {required String title, required String message}) {
    return showDialog<String>(
      context: context,
      builder: (context) =>
          PasswordConfirmationDialog(title: title, message: message),
    );
  }

  @override
  State<PasswordConfirmationDialog> createState() =>
      _PasswordConfirmationDialogState();
}

class _PasswordConfirmationDialogState
    extends State<PasswordConfirmationDialog> {
  final _formKey = GlobalKey<FormState>();
  final _passwordCtrl = TextEditingController();
  bool _obscureText = true;

  @override
  void dispose() {
    _passwordCtrl.dispose();
    super.dispose();
  }

  void _submit() {
    if (_formKey.currentState!.validate()) {
      Navigator.of(context).pop(_passwordCtrl.text);
    }
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      title: Text(widget.title),
      content: Form(
        key: _formKey,
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(widget.message),
            const SizedBox(height: Space.md),
            TextFormField(
              controller: _passwordCtrl,
              obscureText: _obscureText,
              decoration: InputDecoration(
                labelText: 'Tu Contraseña',
                suffixIcon: IconButton(
                  icon: Icon(
                      _obscureText ? Icons.visibility : Icons.visibility_off),
                  onPressed: () => setState(() => _obscureText = !_obscureText),
                ),
              ),
              validator: (v) => v == null || v.isEmpty ? 'Requerido' : null,
            ),
          ],
        ),
      ),
      actions: [
        TextButton(
          onPressed: () => Navigator.of(context).pop(),
          child: const Text('Cancelar'),
        ),
        FilledButton(
          onPressed: _submit,
          style: FilledButton.styleFrom(
              backgroundColor: Theme.of(context).colorScheme.error),
          child: const Text('Confirmar'),
        ),
      ],
    );
  }
}
