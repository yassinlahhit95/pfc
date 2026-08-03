import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/theme/app_theme.dart';
import '../data/secretarias_repository.dart';

class SecretariaFormSheet extends ConsumerStatefulWidget {
  const SecretariaFormSheet({super.key, this.secretaria});
  final Secretaria? secretaria;

  static Future<bool?> show(BuildContext context, {Secretaria? secretaria}) {
    return showModalBottomSheet<bool>(
      context: context,
      isScrollControlled: true,
      builder: (ctx) => Padding(
        padding: EdgeInsets.only(bottom: MediaQuery.of(ctx).viewInsets.bottom),
        child: SecretariaFormSheet(secretaria: secretaria),
      ),
    );
  }

  @override
  ConsumerState<SecretariaFormSheet> createState() =>
      _SecretariaFormSheetState();
}

class _SecretariaFormSheetState extends ConsumerState<SecretariaFormSheet> {
  final _formKey = GlobalKey<FormState>();
  late final TextEditingController _nombreController;
  late final TextEditingController _emailController;
  bool _isLoading = false;
  String? _errorMessage;
  String? _successMessage;

  @override
  void initState() {
    super.initState();
    _nombreController = TextEditingController(text: widget.secretaria?.nombre);
    _emailController = TextEditingController(text: widget.secretaria?.email);
  }

  @override
  void dispose() {
    _nombreController.dispose();
    _emailController.dispose();
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
      final repo = ref.read(secretariasRepositoryProvider);
      final data = {
        'nombreSecretaria': _nombreController.text.trim(),
        'emailSecretaria': _emailController.text.trim(),
      };

      if (widget.secretaria == null) {
        await repo.createSecretaria(data);
      } else {
        await repo.updateSecretaria({
          'idSecretaria': widget.secretaria!.id,
          ...data,
        });
      }
      if (mounted) {
        setState(() => _successMessage = widget.secretaria == null
            ? 'Secretaría creada con éxito'
            : 'Secretaría actualizada con éxito');
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
    return Padding(
      padding: const EdgeInsets.all(Space.lg),
      child: Form(
        key: _formKey,
        child: Column(
          mainAxisSize: MainAxisSize.min,
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Text(
              widget.secretaria == null
                  ? 'Nueva Secretaría'
                  : 'Editar Secretaría',
              style: Theme.of(context).textTheme.titleLarge,
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
            const SizedBox(height: Space.lg),
            TextFormField(
              controller: _nombreController,
              decoration: const InputDecoration(labelText: 'Nombre *'),
              validator: (v) => v!.isEmpty ? 'Requerido' : null,
            ),
            const SizedBox(height: Space.md),
            TextFormField(
              controller: _emailController,
              decoration: const InputDecoration(labelText: 'Email *'),
              keyboardType: TextInputType.emailAddress,
              validator: (v) => v!.isEmpty ? 'Requerido' : null,
            ),
            const SizedBox(height: Space.xl),
            FilledButton(
              onPressed: _isLoading ? null : _submit,
              child: _isLoading
                  ? const CircularProgressIndicator()
                  : const Text('Guardar'),
            ),
          ],
        ),
      ),
    );
  }
}
