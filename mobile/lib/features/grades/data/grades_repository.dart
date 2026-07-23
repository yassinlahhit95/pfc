import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';

/// grades.php's response shape depends on the caller's role (estudiante:
/// {modulos, retos}; profesor: {modulos:[{estudiantes:[...]}]}; tutor:
/// {students:[{modulos:[...]}]}) — kept as a raw map here and interpreted
/// per-role in the presentation layer rather than forcing one shared model.
class GradesRepository {
  GradesRepository(this._client);
  final ApiClient _client;

  Future<Map<String, dynamic>> fetchGrades() => _client.get('/grades.php');
}

final gradesRepositoryProvider = Provider<GradesRepository>(
  (ref) => GradesRepository(ref.read(apiClientProvider)),
);

final gradesProvider = FutureProvider.autoDispose<Map<String, dynamic>>(
  (ref) => ref.read(gradesRepositoryProvider).fetchGrades(),
);
