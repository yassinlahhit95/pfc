import 'package:dio/dio.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';
import '../../../core/auth/auth_state.dart';

class ClassroomModule {
  const ClassroomModule({
    required this.id,
    required this.nombre,
    required this.codigo,
    this.nombreCiclo,
    this.nombreNivel,
  });

  factory ClassroomModule.fromJson(Map<String, dynamic> json) =>
      ClassroomModule(
        id: json['idModulo'] as int,
        nombre: json['nombreModulo'] as String? ?? '',
        codigo: json['codigoModulo'] as String? ?? '',
        nombreCiclo: json['nombreCiclo'] as String?,
        nombreNivel: json['nombreNivel'] as String?,
      );

  final int id;
  final String nombre;
  final String codigo;
  final String? nombreCiclo;
  final String? nombreNivel;
}

class ClassroomFolder {
  const ClassroomFolder(
      {required this.id, required this.nombre, required this.totalArchivos});

  factory ClassroomFolder.fromJson(Map<String, dynamic> json) =>
      ClassroomFolder(
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
    required this.idModulo,
    required this.nombreOriginal,
    required this.extension,
    required this.tamanio,
    required this.descripcion,
    required this.nombreProfesor,
    required this.esFavorito,
  });

  factory ClassroomFile.fromJson(Map<String, dynamic> json) => ClassroomFile(
        id: json['idArchivo'] as int,
        idModulo: json['idModulo'] as int? ?? 0,
        nombreOriginal: json['nombreOriginal'] as String? ?? '',
        extension: json['extension'] as String? ?? '',
        tamanio: json['tamanio'] as int? ?? 0,
        descripcion: json['descripcion'] as String?,
        nombreProfesor: json['nombreProfesor'] as String? ?? '',
        esFavorito: json['esFavorito'] as bool? ?? false,
      );

  final int id;
  final int idModulo;
  final String nombreOriginal;
  final String extension;
  final int tamanio;
  final String? descripcion;
  final String nombreProfesor;
  final bool esFavorito;

  String get humanSize {
    if (tamanio < 1024) return '$tamanio B';
    if (tamanio < 1024 * 1024)
      return '${(tamanio / 1024).toStringAsFixed(1)} KB';
    return '${(tamanio / (1024 * 1024)).toStringAsFixed(1)} MB';
  }
}

class ClassroomTask {
  const ClassroomTask({
    required this.id,
    required this.idModulo,
    required this.titulo,
    required this.descripcion,
    required this.nombreProfesor,
    required this.fechaCreacion,
    required this.archivoAdjunto,
    required this.nota,
    required this.estado,
    required this.comentario,
    required this.publicado,
    required this.totalEntregas,
    required this.totalCorregidas,
    required this.nombreModulo,
    required this.archivoEntrega,
  });

  factory ClassroomTask.fromJson(Map<String, dynamic> json) {
    final entrega = json['miEntrega'] as Map<String, dynamic>?;
    return ClassroomTask(
      id: json['idTarea'] as int,
      idModulo: json['idModulo'] as int? ?? 0,
      titulo: json['titulo'] as String? ?? '',
      descripcion: json['descripcion'] as String? ?? '',
      nombreProfesor: json['nombreProfesor'] as String? ?? '',
      fechaCreacion: json['fechaCreacion'] as String? ?? '',
      archivoAdjunto: json['archivoAdjunto'] as String?,
      nota: json['nota']?.toString() ?? entrega?['nota']?.toString(),
      estado: json['entregado'] == true
          ? 'entregado'
          : entrega?['estado']?.toString(),
      comentario: entrega?['comentarioCalificacion']?.toString(),
      publicado: (json['publicado'] as int? ?? 1) == 1,
      totalEntregas: json['totalEntregas'] as int? ?? 0,
      totalCorregidas: json['totalCorregidas'] as int? ?? 0,
      nombreModulo: json['nombreModulo'] as String? ?? '',
      archivoEntrega: json['archivoEntrega']?.toString() ?? entrega?['archivoEntrega']?.toString(),
    );
  }

  final int id;
  final int idModulo;
  final String titulo;
  final String descripcion;
  final String nombreProfesor;
  final String fechaCreacion;
  final String? archivoAdjunto;
  final String? nota;
  final String? estado;
  final String? comentario;
  final bool publicado;
  final int totalEntregas;
  final int totalCorregidas;
  final String nombreModulo;
  final String? archivoEntrega;
}

class ClassroomSubmission {
  const ClassroomSubmission({
    required this.idEntrega,
    required this.idEstudiante,
    required this.nombreEstudiante,
    required this.archivoEntrega,
    required this.archivoCorreccion,
    required this.respuesta,
    required this.version,
    required this.fechaEntrega,
    required this.nota,
    required this.estado,
    required this.comentarioCalificacion,
  });

  factory ClassroomSubmission.fromJson(Map<String, dynamic> json) =>
      ClassroomSubmission(
        idEntrega: json['idEntrega'] as int?,
        idEstudiante: json['idEstudiante'] as int? ?? 0,
        nombreEstudiante: json['nombreEstudiante'] as String?,
        archivoEntrega: json['archivoEntrega'] as String?,
        archivoCorreccion: json['archivoCorreccion'] as String?,
        respuesta: json['respuesta'] as String?,
        version: json['version'] as int?,
        fechaEntrega: json['fechaEntrega'] as String?,
        nota: json['nota']?.toString(),
        estado: json['estado'] as String?,
        comentarioCalificacion: json['comentarioCalificacion'] as String?,
      );

  final int? idEntrega;
  final int idEstudiante;
  final String? nombreEstudiante;
  final String? archivoEntrega;
  final String? archivoCorreccion;
  final String? respuesta;
  final int? version;
  final String? fechaEntrega;
  final String? nota;
  final String? estado;
  final String? comentarioCalificacion;

  bool get hasSubmitted => idEntrega != null;
}

class ClassroomSession {
  const ClassroomSession({
    required this.id,
    required this.titulo,
    required this.descripcion,
    required this.fechaSesion,
    required this.horaSesion,
    required this.enlaceReunion,
    required this.plataforma,
    required this.nombreProfesor,
  });

  factory ClassroomSession.fromJson(Map<String, dynamic> json) =>
      ClassroomSession(
        id: json['idSesion'] as int,
        titulo: json['titulo'] as String? ?? '',
        descripcion: json['descripcion'] as String?,
        fechaSesion: json['fechaSesion'] as String? ?? '',
        horaSesion: json['horaSesion'] as String? ?? '',
        enlaceReunion: json['enlaceReunion'] as String?,
        plataforma: json['plataforma'] as String?,
        nombreProfesor: json['nombreProfesor'] as String? ?? '',
      );

  final int id;
  final String titulo;
  final String? descripcion;
  final String fechaSesion;
  final String horaSesion;
  final String? enlaceReunion;
  final String? plataforma;
  final String nombreProfesor;
}

class ClassroomRepository {
  ClassroomRepository(this._client, this._ref);
  final ApiClient _client;
  final Ref _ref;

  Future<List<ClassroomModule>> fetchModules() async {
    final data =
        await _client.get('/classroom.php', query: {'action': 'modules'});
    return (data['modules'] as List)
        .cast<Map<String, dynamic>>()
        .map(ClassroomModule.fromJson)
        .toList();
  }

  Future<List<ClassroomFolder>> fetchFolders(int idModulo) async {
    final data = await _client.get('/classroom.php',
        query: {'action': 'folders', 'idModulo': idModulo});
    return (data['folders'] as List)
        .cast<Map<String, dynamic>>()
        .map(ClassroomFolder.fromJson)
        .toList();
  }

  Future<List<ClassroomFile>> fetchFiles(int idModulo, {int? idCarpeta}) async {
    final data = await _client.get('/classroom.php', query: {
      'action': 'files',
      'idModulo': idModulo,
      if (idCarpeta != null) 'idCarpeta': idCarpeta,
    });
    return (data['files'] as List)
        .cast<Map<String, dynamic>>()
        .map(ClassroomFile.fromJson)
        .toList();
  }

  Future<List<ClassroomTask>> fetchTasks(int idModulo) async {
    final data = await _client.get('/classroom.php',
        query: {'action': 'tasks', 'idModulo': idModulo});
    return (data['tasks'] as List)
        .cast<Map<String, dynamic>>()
        .map(ClassroomTask.fromJson)
        .toList();
  }

  /// Estudiante's own entrega for a task, or null if not submitted yet.
  Future<ClassroomSubmission?> fetchSubmission(int idTarea) async {
    final data = await _client.get('/classroom.php',
        query: {'action': 'submission', 'idTarea': idTarea});
    final raw = data['submission'] as Map<String, dynamic>?;
    return raw == null ? null : ClassroomSubmission.fromJson(raw);
  }

  /// Profesor's full roster (every student in the ciclo, with or without an entrega).
  Future<List<ClassroomSubmission>> fetchSubmissions(int idTarea) async {
    final data = await _client.get('/classroom.php',
        query: {'action': 'submissions', 'idTarea': idTarea});
    return (data['submissions'] as List)
        .cast<Map<String, dynamic>>()
        .map(ClassroomSubmission.fromJson)
        .toList();
  }

  Future<void> submit(
      {required int idTarea,
      String respuesta = '',
      String? filePath,
      String? fileName}) {
    return _client.post('/classroom.php',
        data: FormData.fromMap({
          'idTarea': idTarea,
          'respuesta': respuesta,
          if (filePath != null)
            'archivoEntrega':
                MultipartFile.fromFileSync(filePath, filename: fileName),
        }),
        query: {'action': 'submit'});
  }

  Future<void> grade({
    required int idEntrega,
    required double nota,
    String comentario = '',
    String? correctionFilePath,
    String? correctionFileName,
  }) {
    return _client.post('/classroom.php',
        data: FormData.fromMap({
          'idEntrega': idEntrega,
          'nota': nota,
          'comentario': comentario,
          if (correctionFilePath != null)
            'archivoCorreccion': MultipartFile.fromFileSync(correctionFilePath,
                filename: correctionFileName),
        }),
        query: {'action': 'grade'});
  }

  /// Toggles the task's published state server-side; returns the new value.
  Future<bool> togglePublish(int idTarea) async {
    final data = await _client.post('/classroom.php',
        data: {'idTarea': idTarea}, query: {'action': 'publish'});
    return data['publicado'] == 1;
  }

  Future<List<ClassroomSession>> fetchSessions(int idModulo) async {
    final data = await _client.get('/classroom.php',
        query: {'action': 'sessions', 'idModulo': idModulo});
    return (data['sessions'] as List)
        .cast<Map<String, dynamic>>()
        .map(ClassroomSession.fromJson)
        .toList();
  }

  Future<void> createSession({
    required int idModulo,
    required String titulo,
    String descripcion = '',
    required String fechaSesion,
    required String horaSesion,
    String enlaceReunion = '',
    String plataforma = '',
  }) {
    return _client.post('/classroom.php', data: {
      'idModulo': idModulo,
      'titulo': titulo,
      'descripcion': descripcion,
      'fechaSesion': fechaSesion,
      'horaSesion': horaSesion,
      'enlaceReunion': enlaceReunion,
      'plataforma': plataforma,
    }, query: {
      'action': 'create-session'
    });
  }

  Future<List<ClassroomFile>> fetchFavorites() async {
    try {
      final data =
          await _client.get('/classroom.php', query: {'action': 'favorites'});
      final favoritesList = data['favorites'];
      if (favoritesList is! List) {
        return [];
      }
      return favoritesList
          .cast<Map<String, dynamic>>()
          .map((j) => ClassroomFile.fromJson({...j, 'esFavorito': true}))
          .toList();
    } catch (e) {
      rethrow;
    }
  }

  /// Toggles favorite state server-side; returns the new value.
  Future<bool> toggleFavorite(int idArchivo) async {
    final data = await _client.post('/classroom.php',
        data: {'idArchivo': idArchivo}, query: {'action': 'favorite'});
    return data['favorito'] == 1;
  }

  /// Direct download URL — carries the token as a query param since this is
  /// opened by an external viewer/browser, not fetched via the app's own
  /// authenticated HTTP client (see api/v1/classroom.php's download action).
  String downloadUrl(int idArchivo) {
    final token = _ref.read(sessionControllerProvider).value?.token ?? '';
    return '$apiBaseUrl/api/v1/classroom.php?action=download&id=$idArchivo&token=$token';
  }

  /// Same idea as [downloadUrl] but for the estudiante's own submitted file
  /// (kind: 'entrega') or the profesor's corrected file sent back (kind:
  /// 'correccion') — a different storage path, see api/v1/classroom.php.
  String submissionFileUrl(int idTarea, {required String kind}) {
    final token = _ref.read(sessionControllerProvider).value?.token ?? '';
    return '$apiBaseUrl/api/v1/classroom.php?action=download&kind=$kind&id=$idTarea&token=$token';
  }

  /// The profesor's own attachment on the task itself (aula_tareas.archivoAdjunto),
  /// as opposed to [submissionFileUrl]'s entrega/correccion files.
  String taskAttachmentUrl(int idTarea) {
    final token = _ref.read(sessionControllerProvider).value?.token ?? '';
    return '$apiBaseUrl/api/v1/classroom.php?action=download&kind=tarea&id=$idTarea&token=$token';
  }
}

final classroomRepositoryProvider = Provider<ClassroomRepository>(
  (ref) => ClassroomRepository(ref.read(apiClientProvider), ref),
);

final classroomModulesProvider =
    FutureProvider.autoDispose<List<ClassroomModule>>(
  (ref) => ref.read(classroomRepositoryProvider).fetchModules(),
);

final classroomFavoritesProvider =
    FutureProvider.autoDispose<List<ClassroomFile>>(
  (ref) => ref.read(classroomRepositoryProvider).fetchFavorites(),
);

final pendingGradesCountProvider = FutureProvider.autoDispose<int>((ref) async {
  final repo = ref.read(classroomRepositoryProvider);
  try {
    final modules = await repo.fetchModules();
    int count = 0;
    for (final m in modules) {
      final tasks = await repo.fetchTasks(m.id);
      for (final t in tasks) {
        count += (t.totalEntregas - t.totalCorregidas).clamp(0, 999);
      }
    }
    return count;
  } catch (_) {
    return 0;
  }
});

final studentPendingTasksCountProvider =
    FutureProvider.autoDispose<int>((ref) async {
  final repo = ref.read(classroomRepositoryProvider);
  try {
    final modules = await repo.fetchModules();
    int count = 0;
    for (final m in modules) {
      final tasks = await repo.fetchTasks(m.id);
      for (final t in tasks) {
        if (!t.publicado) continue;
        if (t.estado == null || t.estado == '') {
          count++;
        }
      }
    }
    return count;
  } catch (_) {
    return 0;
  }
});
