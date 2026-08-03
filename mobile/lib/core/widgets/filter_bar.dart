import 'package:flutter/material.dart';

import '../theme/app_theme.dart';

/// A single tappable filter pill — shows the current selection, opens a
/// menu of options on tap. `null` value means "no filter" (shows [allLabel]).
class FilterPill<T> extends StatelessWidget {
  const FilterPill({
    super.key,
    required this.label,
    required this.value,
    required this.options,
    required this.onChanged,
    this.allLabel = 'Todos',
  });

  final String label;
  final T? value;
  final List<(T, String)> options; // (value, display label)
  final ValueChanged<T?> onChanged;
  final String allLabel;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final active = value != null;
    final currentLabel =
        active ? options.firstWhere((o) => o.$1 == value).$2 : label;

    return PopupMenuButton<T?>(
      onSelected: onChanged,
      offset: const Offset(0, 8),
      shape:
          RoundedRectangleBorder(borderRadius: BorderRadius.circular(Radii.md)),
      itemBuilder: (context) => [
        CheckedPopupMenuItem<T?>(
          value: null,
          checked: value == null,
          child: Text(allLabel),
        ),
        for (final o in options)
          CheckedPopupMenuItem<T?>(
            value: o.$1,
            checked: value == o.$1,
            child: Text(o.$2),
          ),
      ],
      child: Container(
        padding: const EdgeInsets.symmetric(
            horizontal: Space.md, vertical: Space.sm),
        decoration: BoxDecoration(
          color: active
              ? scheme.primary.withValues(alpha: 0.1)
              : scheme.surfaceContainerHighest,
          borderRadius: BorderRadius.circular(Radii.pill),
          border: active
              ? Border.all(color: scheme.primary.withValues(alpha: 0.3))
              : null,
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(
              currentLabel,
              style: TextStyle(
                fontSize: 13,
                fontWeight: FontWeight.w600,
                color: active ? scheme.primary : scheme.onSurfaceVariant,
              ),
            ),
            const SizedBox(width: 4),
            Icon(Icons.expand_more_rounded,
                size: 16,
                color: active ? scheme.primary : scheme.onSurfaceVariant),
          ],
        ),
      ),
    );
  }
}

/// Horizontal scrollable row of [FilterPill]s — the container every list
/// screen with filters uses, so the placement/padding is consistent.
class FilterBar extends StatelessWidget {
  const FilterBar({super.key, required this.children});
  final List<Widget> children;

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      height: 44,
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: Space.xl),
        itemCount: children.length,
        separatorBuilder: (_, __) => const SizedBox(width: Space.sm),
        itemBuilder: (context, i) => children[i],
      ),
    );
  }
}
