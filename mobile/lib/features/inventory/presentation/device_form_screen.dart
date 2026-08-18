import 'dart:convert';
import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:image_picker/image_picker.dart';

import '../../../core/api/api_client.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/error_modal.dart';
import '../../../core/i18n/translations.dart';
import '../data/inventory_repository.dart';

class DeviceFormScreen extends ConsumerStatefulWidget {
  const DeviceFormScreen({super.key, this.device});
  final Device? device;

  @override
  ConsumerState<DeviceFormScreen> createState() => _DeviceFormScreenState();
}

class _DeviceFormScreenState extends ConsumerState<DeviceFormScreen> {
  late final TextEditingController _nameController;
  late final TextEditingController _serialController;
  late final TextEditingController _cantidadController;
  String _status = 'disponible';
  bool _loading = false;

  File? _imageFile;
  final _picker = ImagePicker();

  @override
  void initState() {
    super.initState();
    _nameController = TextEditingController(text: widget.device?.nombre ?? '');
    _serialController =
        TextEditingController(text: widget.device?.numeroSerie ?? '');
    _cantidadController =
        TextEditingController(text: widget.device?.cantidad.toString() ?? '1');
    if (widget.device != null) {
      _status = widget.device!.estado;
    }
  }

  @override
  void dispose() {
    _nameController.dispose();
    _serialController.dispose();
    _cantidadController.dispose();
    super.dispose();
  }

  Future<void> _takePhoto() async {
    final pickedFile = await _picker.pickImage(
        source: ImageSource.camera, imageQuality: 70, maxWidth: 800);
    if (pickedFile != null) {
      setState(() {
        _imageFile = File(pickedFile.path);
      });
    }
  }

  Future<void> _pickGallery() async {
    final pickedFile = await _picker.pickImage(
        source: ImageSource.gallery, imageQuality: 70, maxWidth: 800);
    if (pickedFile != null) {
      setState(() {
        _imageFile = File(pickedFile.path);
      });
    }
  }

  Future<void> _submit() async {
    final nombre = _nameController.text.trim();
    final serial = _serialController.text.trim();
    final cantidad = int.tryParse(_cantidadController.text.trim()) ?? 1;

    if (nombre.isEmpty || serial.isEmpty) {
      await showErrorAlert(
          context, 'El nombre y número de serie son obligatorios');
      return;
    }

    setState(() => _loading = true);
    try {
      String? base64Image;
      if (_imageFile != null) {
        final bytes = await _imageFile!.readAsBytes();
        base64Image = base64Encode(bytes);
      }

      if (widget.device == null) {
        // Create
        await ref.read(inventoryRepositoryProvider).addDevice(
              nombreArticulo: nombre,
              numeroSerie: serial,
              cantidad: cantidad,
              fotoBase64: base64Image,
            );
        if (mounted) {
          await showErrorAlert(context, 'Dispositivo añadido exitosamente',
              title: 'Éxito');
          ref.invalidate(devicesProvider);
          if (mounted) {
            Navigator.of(context).pop(true);
          }
        }
      } else {
        // Update
        await ref.read(inventoryRepositoryProvider).editDevice(
              idArticulo: widget.device!.id,
              nombreArticulo: nombre,
              numeroSerie: serial,
              estado: _status,
              cantidad: cantidad,
              fotoBase64: base64Image,
            );
        if (mounted) {
          await showErrorAlert(context, 'Dispositivo actualizado exitosamente',
              title: 'Éxito');
          ref.invalidate(devicesProvider);
          if (mounted) {
            Navigator.of(context).pop(true);
          }
        }
      }
    } catch (e) {
      if (mounted) {
        await showErrorAlert(context, 'Error: $e');
        setState(() => _loading = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
          title: Text(widget.device == null
              ? (ref.watch(translationsProvider)['title_anadir_dispositivo'] ??
                  'Añadir Dispositivo')
              : (ref.watch(translationsProvider)['title_editar_dispositivo'] ??
                  'Editar Dispositivo'))),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(Space.xl),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            TextField(
              controller: _nameController,
              decoration:
                  const InputDecoration(labelText: 'Nombre del dispositivo'),
              enabled: !_loading,
            ),
            const SizedBox(height: Space.lg),
            TextField(
              controller: _serialController,
              decoration: const InputDecoration(labelText: 'Número de Serie'),
              enabled: !_loading,
            ),
            const SizedBox(height: Space.lg),
            TextField(
              controller: _cantidadController,
              decoration: const InputDecoration(labelText: 'Cantidad'),
              keyboardType: TextInputType.number,
              enabled: !_loading,
            ),
            const SizedBox(height: Space.lg),
            if (widget.device != null) ...[
              DropdownButtonFormField<String>(
                initialValue: _status,
                decoration: const InputDecoration(labelText: 'Estado'),
                items: const [
                  DropdownMenuItem(
                      value: 'disponible', child: Text('Disponible')),
                  DropdownMenuItem(value: 'prestado', child: Text('Prestado')),
                  DropdownMenuItem(value: 'baja', child: Text('Baja / Roto')),
                ],
                onChanged:
                    _loading ? null : (v) => setState(() => _status = v!),
              ),
              const SizedBox(height: Space.lg),
            ],
            Text('Fotografía', style: Theme.of(context).textTheme.titleSmall),
            const SizedBox(height: Space.sm),
            if (_imageFile != null)
              ClipRRect(
                borderRadius: BorderRadius.circular(Radii.md),
                child: Image.file(_imageFile!,
                    height: 200, width: double.infinity, fit: BoxFit.cover),
              )
            else if (widget.device?.foto != null &&
                widget.device!.foto!.isNotEmpty)
              ClipRRect(
                borderRadius: BorderRadius.circular(Radii.md),
                child: CachedNetworkImage(
                  imageUrl:
                      '$apiBaseUrl/public/uploads/equipos/${widget.device!.foto}',
                  height: 200,
                  width: double.infinity,
                  fit: BoxFit.cover,
                  placeholder: (context, url) => Container(
                    height: 200,
                    color:
                        Theme.of(context).colorScheme.surfaceContainerHighest,
                    child: const Center(child: CircularProgressIndicator()),
                  ),
                  errorWidget: (context, url, error) => Container(
                    height: 200,
                    color:
                        Theme.of(context).colorScheme.surfaceContainerHighest,
                    child: Center(
                        child: Icon(Icons.broken_image,
                            color: Theme.of(context)
                                .colorScheme
                                .onSurfaceVariant)),
                  ),
                ),
              )
            else
              Container(
                height: 120,
                decoration: BoxDecoration(
                  color: Theme.of(context).colorScheme.surfaceContainerHighest,
                  borderRadius: BorderRadius.circular(Radii.md),
                  border: Border.all(
                      color: Theme.of(context).colorScheme.outlineVariant),
                ),
                child: Center(
                    child: Icon(Icons.camera_alt_outlined,
                        size: 40,
                        color: Theme.of(context).colorScheme.onSurfaceVariant)),
              ),
            const SizedBox(height: Space.md),
            Row(
              children: [
                Expanded(
                  child: OutlinedButton.icon(
                    icon: const Icon(Icons.camera_alt),
                    label: const Text('Cámara'),
                    onPressed: _loading ? null : _takePhoto,
                  ),
                ),
                const SizedBox(width: Space.md),
                Expanded(
                  child: OutlinedButton.icon(
                    icon: const Icon(Icons.photo_library),
                    label: const Text('Galería'),
                    onPressed: _loading ? null : _pickGallery,
                  ),
                ),
              ],
            ),
            const SizedBox(height: Space.xxxl),
            FilledButton.icon(
              onPressed: _loading ? null : _submit,
              icon: _loading
                  ? SizedBox(
                      width: 20,
                      height: 20,
                      child: CircularProgressIndicator(
                        strokeWidth: 2,
                        valueColor: AlwaysStoppedAnimation<Color>(
                            Theme.of(context).colorScheme.onPrimary),
                      ),
                    )
                  : const Icon(Icons.save),
              label: Text(_loading ? 'Guardando...' : 'Guardar Dispositivo'),
            ),
          ],
        ),
      ),
    );
  }
}
