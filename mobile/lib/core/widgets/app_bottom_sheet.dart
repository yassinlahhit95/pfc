import 'package:flutter/material.dart';

import '../theme/app_theme.dart';

/// Shared bottom-sheet chrome — rounded top, drag handle, scrollable body.
/// Used by every modal bottom sheet in the classroom/tareas feature so they
/// all look and behave the same (previously each sheet duplicated its own
/// container/decoration, one small style drift away from looking different).
class AppBottomSheet extends StatelessWidget {
  const AppBottomSheet({super.key, required this.child});
  final Widget child;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Container(
      padding:
          const EdgeInsets.fromLTRB(Space.xl, Space.md, Space.xl, Space.xl),
      constraints: BoxConstraints(
        maxHeight: MediaQuery.of(context).size.height * 0.88,
      ),
      decoration: BoxDecoration(
        color: scheme.surface,
        borderRadius:
            const BorderRadius.vertical(top: Radius.circular(Radii.xl)),
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            width: 36,
            height: 4,
            margin: const EdgeInsets.only(bottom: Space.lg),
            decoration: BoxDecoration(
                color: scheme.outlineVariant,
                borderRadius: BorderRadius.circular(Radii.pill)),
          ),
          Flexible(child: SingleChildScrollView(child: child)),
        ],
      ),
    );
  }
}
