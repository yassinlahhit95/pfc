import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';
import '../../../core/auth/auth_state.dart';

class ClassroomModule {
  const ClassroomModule({required this.id, required this.nombre, required this.codigo});

  factory ClassroomModule.fromJson(Map<String, dynamic> json) => ClassroomModule(
        id: json['idModulo'] as int,
        nombre: json['nombreModulo'] as String? ?? '',
        codigo: json['codigoModulo'] as String? ?? '',
      );

  final int id;
  final String nombre;
  final String codigo;
}

class ClassroomFolder {
  const ClassroomFolder({required this.id, required this.nombre, required this.totalArchivos});

  factory ClassroomFolder.fromJson(Map<String, dynamic> json) => ClassroomFolder(
        id: json['idCarpeta'] as int,
        nombre: json['nombre'] as String? ?? '',
        totalArchivos: json['totalArchivos'] as int? ?? 0,
      );

  final int id;
  final String nombre;
  final int totalArchivos;
}

class ClassroomFile {
  const ClassroomFile({
    required this.id,
    required this.nombreOriginal,
    required this.extension,
    required this.tamanio,
    required this.descripcion,
    required this.nombreProfesor,
  });

  factory ClassroomFile.fromJson(Map<String, dynamic> json) => ClassroomFile(
        id: json['idArchivo'] as int,
        nombreOriginal: json['nombreOriginal'] as String? ?? '',
        extension: json['extension'] as String? ?? '',
        tamanio: json['tamanio'] as int? ?? 0,
        descripcion: json['descripcion'] as String?,
        nombreProfesor: json['nombreProfesor'] as String? ?? '',
      );

  final int id;
  final String nombreOriginal;
  final String extension;
  final int tamanio;
  final String? descripcion;
  final String nombreProfesor;

  String get humanSize {
    if (tamanio < 1024) return '$tamanio B';
    if (tamanio < 1024 * 1024) return '${(tamanio / 1024).toStringAsFixed(1)} KB';
    return '${(tamanio / (1024 * 1024)).toStringAsFixed(1)} MB';
  }
}

class ClassroomTask {
  const ClassroomTask({
    required this.id,
    required this.titulo,
    required this.descripcion,
    required this.nombreProfesor,
    required this.fechaCreacion,
    required this.nota,
    required this.estado,
    required this.comentario,
  });

  factory ClassroomTask.fromJson(Map<String, dynamic> json) {
    final entrega = json['miEntrega'] as Map<String, dynamic>?;
    return ClassroomTask(
      id: json['idTarea'] as int,
      titulo: json['titulo'] as String? ?? '',
      descripcion: json['descripcion'] as String? ?? '',
      nombreProfesor: json['nombreProfesor'] as String? ?? '',
      fechaCreacion: json['fechaCreacion'] as String? ?? '',
      nota: entrega?['nota'] as String?,
      estado: entrega?['estado'] as String?,
      comentario: entrega?['comentarioCalificacion'] as String?,
    );
  }

  final int id;
  final String titulo;
  final String descripcion;
  final String nombreProfesor;
  final String fechaCreacion;
  final String? nota;
  final String? estado;
  final String? comentario;
}

class ClassroomRepository {
  ClassroomRepository(this._client, this._ref);
  final ApiClient _client;
  final Ref _ref;

  Future<List<ClassroomModule>> fetchModules() async {
    final data = await _client.get('/classroom.php', query: {'action': 'modules'});
    return (data['modules'] as List).cast<Map<String, dynamic>>().map(ClassroomModule.fromJson).toList();
  }

  Future<List<ClassroomFolder>> fetchFolders(int idModulo) async {
    final data = await _client.get('/classroom.php', query: {'action': 'folders', 'idModulo': idModulo});
    return (data['folders'] as List).cast<Map<String, dynamic>>().map(ClassroomFolder.fromJson).toList();
  }

  Future<List<ClassroomFile>> fetchFiles(int idModulo, {int? idCarpeta}) async {
    final data = await _client.get('/classroom.php', query: {
      'action': 'files',
      'idModulo': idModulo,
      if (idCarpeta != null) 'idCarpeta': idCarpeta,
    });
    return (data['files'] as List).cast<Map<String, dynamic>>().map(ClassroomFile.fromJson).toList();
  }

  Future<List<ClassroomTask>> fetchTasks(int idModulo) async {
    final data = await _client.get('/classroom.php', query: {'action': 'tasks', 'idModulo': idModulo});
    return (data['tasks'] as List).cast<Map<String, dynamic>>().map(ClassroomTask.fromJson).toList();
  }

  /// Direct download URL — carries the token as a query param since this is
  /// opened by an external viewer/browser, not fetched via the app's own
  /// authenticated HTTP client (see api/v1/classroom.php's download action).
  String downloadUrl(int idArchivo) {
    final token = _ref.read(sessionControllerProvider).valueOrNull?.token ?? '';
    return '$apiBaseUrl/api/v1/classroom.php?action=download&id=$idArchivo&token=$token';
  }
}

final classroomRepositoryProvider = Provider<ClassroomRepository>(
  (ref) => ClassroomRepository(ref.read(apiClientProvider), ref),
);

final classroomModulesProvider = FutureProvider.autoDispose<List<ClassroomModule>>(
  (ref) => ref.read(classroomRepositoryProvider).fetchModules(),
);
