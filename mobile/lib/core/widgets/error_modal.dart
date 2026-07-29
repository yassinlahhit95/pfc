import 'package:flutter/material.dart';

import '../theme/app_theme.dart';

/// Shows a smooth, modal error dialog that respects safe areas and keyboard zones.
/// Fluid animation, proper spacing, and best practices for mobile UX.
Future<void> showErrorModal({
  required BuildContext context,
  required String title,
  required String message,
  String? actionLabel,
  VoidCallback? onAction,
}) {
  return showDialog<void>(
    context: context,
    barrierDismissible: true,
    builder: (ctx) => _ErrorDialog(
      title: title,
      message: message,
      actionLabel: actionLabel,
      onAction: onAction,
    ),
  );
}

class _ErrorDialog extends StatefulWidget {
  const _ErrorDialog({
    required this.title,
    required this.message,
    this.actionLabel,
    this.onAction,
  });

  final String title;
  final String message;
  final String? actionLabel;
  final VoidCallback? onAction;

  @override
  State<_ErrorDialog> createState() => _ErrorDialogState();
}

class _ErrorDialogState extends State<_ErrorDialog> with SingleTickerProviderStateMixin {
  late AnimationController _animController;
  late Animation<double> _scaleAnim;
  late Animation<double> _opacityAnim;

  @override
  void initState() {
    super.initState();
    _animController = AnimationController(
      duration: const Duration(milliseconds: 300),
      vsync: this,
    );

    _scaleAnim = Tween<double>(begin: 0.8, end: 1.0).animate(
      CurvedAnimation(parent: _animController, curve: Curves.easeOutBack),
    );

    _opacityAnim = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(parent: _animController, curve: Curves.easeInOut),
    );

    _animController.forward();
  }

  @override
  void dispose() {
    _animController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final scheme = theme.colorScheme;

    return AnimatedBuilder(
      animation: _animController,
      builder: (context, child) => Dialog(
        backgroundColor: Colors.transparent,
        insetPadding: const EdgeInsets.symmetric(horizontal: Space.lg),
        alignment: Alignment.center,
        child: Opacity(
          opacity: _opacityAnim.value,
          child: Transform.scale(
            scale: _scaleAnim.value,
            child: child,
          ),
        ),
      ),
      child: SingleChildScrollView(
        child: Container(
          decoration: BoxDecoration(
            color: scheme.surface,
            borderRadius: BorderRadius.circular(Radii.xl),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.15),
                blurRadius: 20,
                offset: const Offset(0, 8),
              ),
            ],
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              // Icon + Title
              Padding(
                padding: const EdgeInsets.only(
                  top: Space.xxl,
                  left: Space.lg,
                  right: Space.lg,
                  bottom: Space.md,
                ),
                child: Column(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(Space.lg),
                      decoration: BoxDecoration(
                        color: scheme.error.withOpacity(0.1),
                        shape: BoxShape.circle,
                      ),
                      child: Icon(
                        Icons.error_outline_rounded,
                        size: 32,
                        color: scheme.error,
                      ),
                    ),
                    const SizedBox(height: Space.lg),
                    Text(
                      widget.title,
                      style: theme.textTheme.titleMedium?.copyWith(
                        fontWeight: FontWeight.w600,
                        color: scheme.onSurface,
                      ),
                      textAlign: TextAlign.center,
                    ),
                  ],
                ),
              ),

              // Message
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: Space.lg),
                child: Text(
                  widget.message,
                  style: theme.textTheme.bodyMedium?.copyWith(
                    color: scheme.onSurfaceVariant,
                    height: 1.5,
                  ),
                  textAlign: TextAlign.center,
                ),
              ),

              const SizedBox(height: Space.xxl),

              // Actions
              Padding(
                padding: const EdgeInsets.fromLTRB(Space.lg, 0, Space.lg, Space.lg),
                child: Row(
                  children: [
                    if (widget.onAction != null) ...[
                      Expanded(
                        child: OutlinedButton(
                          onPressed: () => Navigator.of(context).pop(),
                          child: const Text('Cancelar'),
                        ),
                      ),
                      const SizedBox(width: Space.md),
                    ] else
                      const SizedBox.shrink(),
                    Expanded(
                      child: FilledButton.tonal(
                        onPressed: () {
                          Navigator.of(context).pop();
                          widget.onAction?.call();
                        },
                        child: Text(widget.actionLabel ?? 'Entendido'),
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

/// Convenience wrapper for common error scenarios (no action button needed)
Future<void> showErrorAlert(BuildContext context, String message, {String title = 'Error'}) {
  return showErrorModal(
    context: context,
    title: title,
    message: message,
  );
}
