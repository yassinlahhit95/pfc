import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';

import '../../../core/theme/app_theme.dart';
import '../../attendance/presentation/center_attendance_screen.dart'
    show lookupsProvider;
import '../data/students_repository.dart';

class AddStudentScreen extends ConsumerStatefulWidget {
  const AddStudentScreen({super.key, this.student});
  final Student? student;

  @override
  ConsumerState<AddStudentScreen> createState() => _AddStudentScreenState();
}

class _AddStudentScreenState extends ConsumerState<AddStudentScreen> {
  final _formKey = GlobalKey<FormState>();
  late TextEditingController _nombreController;
  late TextEditingController _emailController;
  late TextEditingController _telefonoController;
  late TextEditingController _dniController;
  late TextEditingController _direccionController;
  late TextEditingController _ciudadController;
  late TextEditingController _codigoPostalController;
  late TextEditingController _observacionesController;

  DateTime? _fechaNacimiento;
  int? _idCiclo;
  int? _idGrupo;
  String _curso = 'Grado Medio';
  String? _anioEstudio;

  bool _isSaving = false;

  @override
  void initState() {
    super.initState();
    final st = widget.student;
    _nombreController = TextEditingController(text: st?.nombre ?? '');
    _emailController = TextEditingController(text: st?.email ?? '');
    _telefonoController = TextEditingController(text: st?.telefono ?? '');
    _dniController = TextEditingController(text: st?.dni ?? '');
    _direccionController = TextEditingController(text: st?.direccion ?? '');
    _ciudadController = TextEditingController(text: st?.ciudad ?? '');
    _codigoPostalController =
        TextEditingController(text: st?.codigoPostal ?? '');
    _observacionesController =
        TextEditingController(text: st?.observaciones ?? '');

    _idCiclo = st?.idCiclo;
    _idGrupo = st?.idGrupo;
    _curso = (st?.course != null && st!.course.isNotEmpty)
        ? st.course
        : 'Grado Medio';
    _anioEstudio = st?.year;

    if (st?.fechaNacimiento != null && st!.fechaNacimiento!.isNotEmpty) {
      try {
        _fechaNacimiento = DateTime.parse(st.fechaNacimiento!);
      } catch (_) {}
    }
  }

  @override
  void dispose() {
    _nombreController.dispose();
    _emailController.dispose();
    _telefonoController.dispose();
    _dniController.dispose();
    _direccionController.dispose();
    _ciudadController.dispose();
    _codigoPostalController.dispose();
    _observacionesController.dispose();
    super.dispose();
  }

  Future<void> _pickDate() async {
    final date = await showDatePicker(
      context: context,
      initialDate: _fechaNacimiento ??
          DateTime.now().subtract(const Duration(days: 365 * 15)),
      firstDate: DateTime(1900),
      lastDate: DateTime.now(),
    );
    if (date != null) {
      setState(() => _fechaNacimiento = date);
    }
  }

  Future<void> _save() async {
    if (!_formKey.currentState!.validate()) return;
    if (_idCiclo == null) {
      ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Debe seleccionar un ciclo')));
      return;
    }

    setState(() => _isSaving = true);
    try {
      final repo = ref.read(studentsRepositoryProvider);
      final newStudent = Student(
        id: widget.student?.id ?? 0,
        nombre: _nombreController.text.trim(),
        email: _emailController.text.trim(),
        ciclo: '', // Not needed for save
        abreviaturaCiclo: '', // Not needed for save
        course: _curso,
        year: _anioEstudio,
        estado: widget.student?.estado ?? 'activo',
        dateEnrolled: widget.student?.dateEnrolled,
        telefono: _telefonoController.text.trim(),
        idCiclo: _idCiclo,
        idGrupo: _idGrupo,
        fechaNacimiento: _fechaNacimiento != null
            ? DateFormat('yyyy-MM-dd').format(_fechaNacimiento!)
            : '',
        dni: _dniController.text.trim(),
        direccion: _direccionController.text.trim(),
        ciudad: _ciudadController.text.trim(),
        codigoPostal: _codigoPostalController.text.trim(),
        observaciones: _observacionesController.text.trim(),
      );

      if (widget.student != null) {
        await repo.updateStudent(newStudent);
      } else {
        await repo.createStudent(newStudent);
      }
      if (mounted) Navigator.pop(context, true);
    } catch (e) {
      if (mounted)
        ScaffoldMessenger.of(context)
            .showSnackBar(SnackBar(content: Text('Error: $e')));
    } finally {
      if (mounted) setState(() => _isSaving = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    final isEditing = widget.student != null;
    final lookupsAsync = ref.watch(lookupsProvider);

    return Scaffold(
      appBar: AppBar(
          title: Text(isEditing ? 'Editar Estudiante' : 'Nuevo Estudiante')),
      body: lookupsAsync.when(
        loading: () => const Center(child: CircularProgressIndicator()),
        error: (e, st) => const Center(child: Text('Error cargando datos')),
        data: (lookups) {
          final ciclos = lookups['ciclos'] as List? ?? [];
          final grupos = lookups['grupos'] as List? ?? [];

          return SingleChildScrollView(
            padding: const EdgeInsets.all(Space.xl),
            child: Form(
              key: _formKey,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  TextFormField(
                    controller: _nombreController,
                    decoration: const InputDecoration(
                        labelText: 'Nombre Completo',
                        border: OutlineInputBorder()),
                    validator: (v) =>
                        v == null || v.trim().isEmpty ? 'Requerido' : null,
                  ),
                  const SizedBox(height: Space.lg),
                  TextFormField(
                    controller: _emailController,
                    decoration: const InputDecoration(
                        labelText: 'Email', border: OutlineInputBorder()),
                    keyboardType: TextInputType.emailAddress,
                    validator: (v) =>
                        v == null || !v.contains('@') ? 'Email inválido' : null,
                  ),
                  const SizedBox(height: Space.lg),
                  Row(
                    children: [
                      Expanded(
                        child: TextFormField(
                          controller: _dniController,
                          decoration: const InputDecoration(
                              labelText: 'DNI / NIE',
                              border: OutlineInputBorder()),
                        ),
                      ),
                      const SizedBox(width: Space.md),
                      Expanded(
                        child: TextFormField(
                          controller: _telefonoController,
                          decoration: const InputDecoration(
                              labelText: 'Teléfono',
                              border: OutlineInputBorder()),
                          keyboardType: TextInputType.phone,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: Space.lg),
                  OutlinedButton.icon(
                    onPressed: _pickDate,
                    icon: const Icon(Icons.calendar_today),
                    label: Text(_fechaNacimiento == null
                        ? 'Fecha de Nacimiento'
                        : DateFormat('dd/MM/yyyy').format(_fechaNacimiento!)),
                  ),
                  const SizedBox(height: Space.lg),
                  TextFormField(
                    controller: _direccionController,
                    decoration: const InputDecoration(
                        labelText: 'Dirección', border: OutlineInputBorder()),
                  ),
                  const SizedBox(height: Space.lg),
                  Row(
                    children: [
                      Expanded(
                        flex: 2,
                        child: TextFormField(
                          controller: _ciudadController,
                          decoration: const InputDecoration(
                              labelText: 'Ciudad',
                              border: OutlineInputBorder()),
                        ),
                      ),
                      const SizedBox(width: Space.md),
                      Expanded(
                        flex: 1,
                        child: TextFormField(
                          controller: _codigoPostalController,
                          decoration: const InputDecoration(
                              labelText: 'CP', border: OutlineInputBorder()),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: Space.xxl),
                  const Text('Datos Académicos',
                      style:
                          TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                  const SizedBox(height: Space.lg),
                  DropdownButtonFormField<int?>(
                    initialValue: _idCiclo,
                    isExpanded: true,
                    decoration: const InputDecoration(
                        labelText: 'Ciclo Formativo',
                        border: OutlineInputBorder()),
                    items: ciclos.map<DropdownMenuItem<int?>>((c) {
                      return DropdownMenuItem(
                          value: c['idCiclo'] as int,
                          child: Text(c['nombreCiclo']));
                    }).toList(),
                    onChanged: (val) => setState(() => _idCiclo = val),
                    validator: (v) => v == null ? 'Requerido' : null,
                  ),
                  const SizedBox(height: Space.lg),
                  Row(
                    children: [
                      Expanded(
                        child: DropdownButtonFormField<String>(
                          initialValue: _curso,
                          decoration: const InputDecoration(
                              labelText: 'Curso', border: OutlineInputBorder()),
                          items: const [
                            DropdownMenuItem(
                                value: 'Grado Medio',
                                child: Text('Grado Medio')),
                            DropdownMenuItem(
                                value: 'Grado Superior',
                                child: Text('Grado Superior')),
                            DropdownMenuItem(
                                value: 'Grado Básico',
                                child: Text('Grado Básico')),
                          ],
                          onChanged: (val) => setState(() => _curso = val!),
                        ),
                      ),
                      const SizedBox(width: Space.md),
                      Expanded(
                        child: DropdownButtonFormField<String?>(
                          initialValue: _anioEstudio,
                          decoration: const InputDecoration(
                              labelText: 'Año (1º o 2º)',
                              border: OutlineInputBorder()),
                          items: const [
                            DropdownMenuItem(
                                value: null, child: Text('No definido')),
                            DropdownMenuItem(value: '1º', child: Text('1º')),
                            DropdownMenuItem(value: '2º', child: Text('2º')),
                          ],
                          onChanged: (val) =>
                              setState(() => _anioEstudio = val),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: Space.lg),
                  DropdownButtonFormField<int?>(
                    initialValue: _idGrupo,
                    decoration: const InputDecoration(
                        labelText: 'Grupo', border: OutlineInputBorder()),
                    items: [
                      const DropdownMenuItem(
                          value: null, child: Text('Sin grupo')),
                      ...grupos.map<DropdownMenuItem<int?>>((g) {
                        return DropdownMenuItem(
                            value: g['idGrupo'] as int,
                            child: Text(g['nombreGrupo']));
                      })
                    ],
                    onChanged: (val) => setState(() => _idGrupo = val),
                  ),
                  const SizedBox(height: Space.lg),
                  TextFormField(
                    controller: _observacionesController,
                    decoration: const InputDecoration(
                        labelText: 'Observaciones',
                        border: OutlineInputBorder()),
                    maxLines: 3,
                  ),
                  const SizedBox(height: Space.xxxl),
                  FilledButton(
                    onPressed: _isSaving ? null : _save,
                    child: _isSaving
                        ? const CircularProgressIndicator()
                        : const Text('Guardar'),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}
