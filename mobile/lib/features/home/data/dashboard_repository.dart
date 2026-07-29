import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/api/api_client.dart';

class DashboardStats {
  const DashboardStats({
    required this.totalEstudiantes,
    required this.totalProfesores,
    required this.gastosMes,
    required this.pagosMes,
  });

  factory DashboardStats.fromJson(Map<String, dynamic> json) => DashboardStats(
        totalEstudiantes: json['total_estudiantes'] as int? ?? 0,
        totalProfesores: json['total_profesores'] as int? ?? 0,
        gastosMes: (json['gastos_mes'] as num?)?.toDouble() ?? 0.0,
        pagosMes: (json['pagos_mes'] as num?)?.toDouble() ?? 0.0,
      );

  final int totalEstudiantes;
  final int totalProfesores;
  final double gastosMes;
  final double pagosMes;
}

class DashboardRepository {
  const DashboardRepository(this.api);
  final ApiClient api;

  Future<DashboardStats> getStats() async {
    final res = await api.get('/dashboard.php');
    return DashboardStats.fromJson(res);
  }
}

final dashboardRepositoryProvider = Provider((ref) => DashboardRepository(ref.watch(apiClientProvider)));

final dashboardStatsProvider = FutureProvider.autoDispose<DashboardStats>((ref) {
  return ref.watch(dashboardRepositoryProvider).getStats();
});
