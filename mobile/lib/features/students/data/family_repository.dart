import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/api/api_client.dart';

class Tutor {
  final int id;
  final String nombre;
  final String email;
  final String dni;
  final String telefono;
  final String parentesco;

  const Tutor({
    required this.id,
    required this.nombre,
    required this.email,
    required this.dni,
    required this.telefono,
    required this.parentesco,
  });

  factory Tutor.fromJson(Map<String, dynamic> json) {
    return Tutor(
      id: json['idTutor'] as int,
      nombre: json['nombreTutor'] ?? '',
      email: json['emailTutor'] ?? '',
      dni: json['dniTutor'] ?? '',
      telefono: json['telefonoTutor'] ?? '',
      parentesco: json['parentesco'] ?? 'Tutor',
    );
  }
}

class FamilyRepository {
  final ApiClient _client;
  FamilyRepository(this._client);

  Future<List<Tutor>> fetchFamily(int idEstudiante) async {
    final data = await _client.get('/estudiante-familia.php', query: {'idEstudiante': idEstudiante});
    final tutores = data['tutores'] as List;
    return tutores.map((t) => Tutor.fromJson(t)).toList();
  }

  Future<void> addTutorToStudent(int idEstudiante, Map<String, dynamic> tutorData) async {
    await _client.post('/estudiante-familia.php', data: {
      'idEstudiante': idEstudiante,
      ...tutorData,
    });
  }

  Future<void> updateTutor(int idEstudiante, int idTutor, Map<String, dynamic> tutorData) async {
    await _client.put('/estudiante-familia.php', data: {
      'idEstudiante': idEstudiante,
      'idTutor': idTutor,
      ...tutorData,
    });
  }

  Future<void> removeTutorFromStudent(int idEstudiante, int idTutor) async {
    await _client.delete('/estudiante-familia.php', query: {
      'idEstudiante': idEstudiante,
      'idTutor': idTutor,
    });
  }
}

final familyRepositoryProvider = Provider((ref) => FamilyRepository(ref.watch(apiClientProvider)));

final studentFamilyProvider = FutureProvider.autoDispose.family<List<Tutor>, int>((ref, idEstudiante) {
  return ref.read(familyRepositoryProvider).fetchFamily(idEstudiante);
});
