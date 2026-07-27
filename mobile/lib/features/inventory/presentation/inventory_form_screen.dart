import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/theme/app_theme.dart';
import '../data/inventory_repository.dart';

class InventoryFormScreen extends ConsumerStatefulWidget {
  const InventoryFormScreen({super.key, this.id});
  final int? id;

  @override
  ConsumerState<InventoryFormScreen> createState() => _InventoryFormScreenState();
}

class _InventoryFormScreenState extends ConsumerState<InventoryFormScreen> {
  late final TextEditingController _nameController;
  late final TextEditingController _descriptionController;
  late final TextEditingController _quantityController;
  bool _loading = false;

  @override
  void initState() {
    super.initState();
    _nameController = TextEditingController();
    _descriptionController = TextEditingController();
    _quantityController = TextEditingController(text: '0');

    if (widget.id != null) {
      _loadItem();
    }
  }

  @override
  void dispose() {
    _nameController.dispose();
    _descriptionController.dispose();
    _quantityController.dispose();
    super.dispose();
  }

  Future<void> _loadItem() async {
    try {
      final item = await ref.read(inventoryRepositoryProvider).fetchItem(widget.id!);
      if (mounted) {
        _nameController.text = item.nombre;
        _descriptionController.text = item.descripcion ?? '';
        _quantityController.text = item.cantidad.toString();
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error al cargar el artículo: $e')),
        );
      }
    }
  }

  Future<void> _submit() async {
    final nombre = _nameController.text.trim();
    if (nombre.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(content: Text('El nombre del artículo es requerido')),
      );
      return;
    }

    final cantidad = int.tryParse(_quantityController.text) ?? 0;

    setState(() => _loading = true);
    try {
      if (widget.id == null) {
        // Create
        await ref.read(inventoryRepositoryProvider).createItem(
          nombre: nombre,
          descripcion: _descriptionController.text.isEmpty ? null : _descriptionController.text,
          cantidad: cantidad,
        );
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Artículo creado exitosamente')),
          );
          ref.invalidate(inventoryItemsListProvider);
          Navigator.of(context).pop(true);
        }
      } else {
        // Update
        await ref.read(inventoryRepositoryProvider).updateItem(
          id: widget.id!,
          nombre: nombre,
          descripcion: _descriptionController.text.isEmpty ? null : _descriptionController.text,
          cantidad: cantidad,
        );
        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Artículo actualizado exitosamente')),
          );
          ref.invalidate(inventoryItemsListProvider);
          ref.invalidate(inventoryItemsProvider(widget.id!));
          Navigator.of(context).pop(true);
        }
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(content: Text('Error: $e')),
        );
      }
    } finally {
      if (mounted) {
        setState(() => _loading = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(widget.id == null ? 'Nuevo Artículo' : 'Editar Artículo'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(Space.lg),
        child: Column(
          children: [
            TextField(
              controller: _nameController,
              enabled: !_loading,
              decoration: const InputDecoration(
                labelText: 'Nombre del artículo *',
                hintText: 'Ej: Laptop, Proyector, Libros',
                prefixIcon: Icon(Icons.inventory_2_outlined),
              ),
            ),
            const SizedBox(height: Space.md),
            TextField(
              controller: _descriptionController,
              enabled: !_loading,
              maxLines: 3,
              decoration: const InputDecoration(
                labelText: 'Descripción',
                hintText: 'Detalles adicionales sobre el artículo',
                prefixIcon: Icon(Icons.description_outlined),
                alignLabelWithHint: true,
              ),
            ),
            const SizedBox(height: Space.md),
            TextField(
              controller: _quantityController,
              enabled: !_loading,
              keyboardType: TextInputType.number,
              decoration: const InputDecoration(
                labelText: 'Cantidad disponible',
                hintText: '0',
                prefixIcon: Icon(Icons.numbers),
              ),
            ),
            const SizedBox(height: Space.xl),
            SizedBox(
              width: double.infinity,
              child: FilledButton.icon(
                onPressed: _loading ? null : _submit,
                icon: _loading ? const SizedBox(width: 20, height: 20, child: CircularProgressIndicator(strokeWidth: 2)) : const Icon(Icons.save),
                label: Text(widget.id == null ? 'Crear Artículo' : 'Guardar Cambios'),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
