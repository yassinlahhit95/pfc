import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';
import '../../classroom/data/classroom_repository.dart';

class TareasRepository {
  TareasRepository(this._client);
  final ApiClient _client;

  Future<List<ClassroomTask>> fetchAllTasks() async {
    final data = await _client.get('/tareas.php', query: {'action': 'list'});
    return (data['tasks'] as List)
        .cast<Map<String, dynamic>>()
        .map(ClassroomTask.fromJson)
        .toList();
  }
}

final tareasRepositoryProvider = Provider<TareasRepository>(
  (ref) => TareasRepository(ref.read(apiClientProvider)),
);

final allTasksProvider = FutureProvider.autoDispose<List<ClassroomTask>>(
  (ref) => ref.read(tareasRepositoryProvider).fetchAllTasks(),
);
