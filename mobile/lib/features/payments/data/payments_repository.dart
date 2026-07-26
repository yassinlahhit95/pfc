import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_client.dart';

class Payment {
  const Payment({
    required this.id,
    required this.monto,
    required this.fechaPago,
    required this.tipoPago,
    required this.estadoComprobante,
    required this.nombreEstudiante,
    required this.nombreCiclo,
    required this.nivel,
  });

  factory Payment.fromJson(Map<String, dynamic> json) => Payment(
        id: json['idPago'] as int,
        monto: json['monto'] as String? ?? '0',
        fechaPago: json['fechaPago'] as String? ?? '',
        tipoPago: json['tipoPago'] as String? ?? '',
        estadoComprobante: json['estadoComprobante'] as String? ?? '',
        nombreEstudiante: json['nombreEstudiante'] as String? ?? '',
        nombreCiclo: json['nombreCiclo'] as String? ?? '',
        // "curso" on estudiantes is actually the nivel enum (Grado Medio /
        // Grado Superior), not an academic year — same field the web app
        // filters students by.
        nivel: json['curso'] as String? ?? '',
      );

  final int id;
  final String monto;
  final String fechaPago;
  final String tipoPago;
  final String estadoComprobante;
  final String nombreEstudiante;
  final String nombreCiclo;
  final String nivel;
}

class PendingPayment {
  const PendingPayment({
    required this.idEstudiante,
    required this.nombreEstudiante,
    required this.nombreCiclo,
    required this.deuda,
  });

  factory PendingPayment.fromJson(Map<String, dynamic> json) => PendingPayment(
        idEstudiante: json['idEstudiante'] as int,
        nombreEstudiante: json['nombreEstudiante'] as String? ?? '',
        nombreCiclo: json['nombreCiclo'] as String? ?? '',
        deuda: json['deuda']?.toString() ?? '0',
      );

  final int idEstudiante;
  final String nombreEstudiante;
  final String nombreCiclo;
  final String deuda;
}

class FinancialStatus {
  const FinancialStatus({required this.totalPagado, required this.precioCiclo, required this.restante});

  factory FinancialStatus.fromJson(Map<String, dynamic> json) => FinancialStatus(
        totalPagado: (json['totalPagado'] as num?)?.toDouble() ?? 0,
        precioCiclo: (json['precioCiclo'] as num?)?.toDouble() ?? 0,
        restante: (json['restante'] as num?)?.toDouble() ?? 0,
      );

  final double totalPagado;
  final double precioCiclo;
  final double restante;
}

class StudentPaymentsGroup {
  const StudentPaymentsGroup({
    required this.idEstudiante,
    required this.nombreEstudiante,
    required this.payments,
    required this.estado,
  });

  factory StudentPaymentsGroup.fromJson(Map<String, dynamic> json) => StudentPaymentsGroup(
        idEstudiante: json['idEstudiante'] as int,
        nombreEstudiante: json['nombreEstudiante'] as String? ?? '',
        payments: (json['payments'] as List).cast<Map<String, dynamic>>().map(Payment.fromJson).toList(),
        estado: FinancialStatus.fromJson((json['estado'] as Map).cast<String, dynamic>()),
      );

  final int idEstudiante;
  final String nombreEstudiante;
  final List<Payment> payments;
  final FinancialStatus estado;
}

class PaymentsRepository {
  PaymentsRepository(this._client);
  final ApiClient _client;

  Future<List<Payment>> fetchAll() async {
    final data = await _client.get('/payments.php');
    return (data['payments'] as List).cast<Map<String, dynamic>>().map(Payment.fromJson).toList();
  }

  Future<List<PendingPayment>> fetchPending() async {
    final data = await _client.get('/payments.php', query: {'pending': 1});
    return (data['pending'] as List).cast<Map<String, dynamic>>().map(PendingPayment.fromJson).toList();
  }

  /// estudiante: own payment history + running balance.
  Future<({List<Payment> payments, FinancialStatus estado})> fetchMine() async {
    final data = await _client.get('/payments.php');
    return (
      payments: (data['payments'] as List).cast<Map<String, dynamic>>().map(Payment.fromJson).toList(),
      estado: FinancialStatus.fromJson((data['estado'] as Map).cast<String, dynamic>()),
    );
  }

  /// tutor: payment history + balance per linked child.
  Future<List<StudentPaymentsGroup>> fetchForTutor() async {
    final data = await _client.get('/payments.php');
    return (data['students'] as List).cast<Map<String, dynamic>>().map(StudentPaymentsGroup.fromJson).toList();
  }
}

final paymentsRepositoryProvider = Provider<PaymentsRepository>(
  (ref) => PaymentsRepository(ref.read(apiClientProvider)),
);

final paymentsProvider = FutureProvider.autoDispose<List<Payment>>(
  (ref) => ref.read(paymentsRepositoryProvider).fetchAll(),
);

final pendingPaymentsProvider = FutureProvider.autoDispose<List<PendingPayment>>(
  (ref) => ref.read(paymentsRepositoryProvider).fetchPending(),
);

final myPaymentsProvider =
    FutureProvider.autoDispose<({List<Payment> payments, FinancialStatus estado})>(
  (ref) => ref.read(paymentsRepositoryProvider).fetchMine(),
);

final tutorPaymentsProvider = FutureProvider.autoDispose<List<StudentPaymentsGroup>>(
  (ref) => ref.read(paymentsRepositoryProvider).fetchForTutor(),
);
