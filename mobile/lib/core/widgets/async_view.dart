import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../api/api_exception.dart';
import '../theme/app_theme.dart';
import 'skeleton_loader.dart';

/// Renders an [AsyncValue] with consistent loading/error/data handling
/// across every feature screen, so each screen only has to describe its
/// data view.
class AsyncView<T> extends StatelessWidget {
  const AsyncView({
    super.key,
    required this.value,
    required this.data,
    this.onRetry,
  });

  final AsyncValue<T> value;
  final Widget Function(BuildContext context, T data) data;
  final VoidCallback? onRetry;

  @override
  Widget build(BuildContext context) {
    return value.when(
      data: (d) => data(context, d),
      loading: () => const SkeletonLoader(),
      error: (error, stack) {
        // Unexpected (non-API) errors are logged with their real type +
        // stack trace — the UI only ever shows a friendly fallback message,
        // but this makes them diagnosable from `flutter run` output.
        if (error is! ApiException && error is! ApiConnectionException) {
          debugPrint(
              'AsyncView unexpected error: ${error.runtimeType}: $error\n$stack');
        }
        return ErrorRetry(error: error, onRetry: onRetry);
      },
    );
  }
}

class ErrorRetry extends StatelessWidget {
  const ErrorRetry({super.key, required this.error, this.onRetry});

  final Object error;
  final VoidCallback? onRetry;

  String get _message {
    if (error is ApiException) return (error as ApiException).message;
    if (error is ApiConnectionException) {
      return 'No se pudo conectar. Comprueba tu conexión e inténtalo de nuevo.';
    }
    return 'Ha ocurrido un error inesperado.';
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(Space.xxxl),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 56,
              height: 56,
              decoration: BoxDecoration(
                  color: scheme.error.withValues(alpha: 0.08),
                  shape: BoxShape.circle),
              child:
                  Icon(Icons.wifi_off_rounded, size: 24, color: scheme.error),
            ),
            const SizedBox(height: Space.lg),
            Text(
              _message,
              textAlign: TextAlign.center,
              style: Theme.of(context)
                  .textTheme
                  .bodyMedium
                  ?.copyWith(color: scheme.onSurfaceVariant),
            ),
            if (onRetry != null) ...[
              const SizedBox(height: Space.xl),
              OutlinedButton(
                  onPressed: onRetry, child: const Text('Reintentar')),
            ],
          ],
        ),
      ),
    );
  }
}

/// Matches the web app's `.vacio` pattern — an inline empty state used
/// inside an existing list/table shell, not a full-panel replacement. Icon
/// sits in a soft neutral circle rather than a colored badge.
class EmptyState extends StatelessWidget {
  const EmptyState({
    super.key,
    required this.icon,
    required this.title,
    this.description,
    this.actionText,
    this.onAction,
  });

  final IconData icon;
  final String title;
  final String? description;
  final String? actionText;
  final VoidCallback? onAction;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(Space.xxxl),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 56,
              height: 56,
              decoration: BoxDecoration(
                  color: scheme.surfaceContainerHighest,
                  shape: BoxShape.circle),
              child: Icon(icon, size: 24, color: scheme.onSurfaceVariant),
            ),
            const SizedBox(height: Space.lg),
            Text(title,
                style: Theme.of(context).textTheme.titleSmall,
                textAlign: TextAlign.center),
            if (description != null) ...[
              const SizedBox(height: Space.xs),
              Text(
                description!,
                textAlign: TextAlign.center,
                style: Theme.of(context).textTheme.bodySmall,
              ),
            ],
            if (actionText != null && onAction != null) ...[
              const SizedBox(height: Space.lg),
              FilledButton(onPressed: onAction, child: Text(actionText!)),
            ],
          ],
        ),
      ),
    );
  }
}
