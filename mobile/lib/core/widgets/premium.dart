import 'package:flutter/material.dart';

import '../theme/app_theme.dart';

/// The one card surface used everywhere instead of every screen hand-rolling
/// its own `Container(decoration: BoxDecoration(...))` — same radius, same
/// border, same (very subtle) shadow, same tap feedback. This single point
/// of consistency is most of what makes a screen sweep look "designed"
/// rather than "assembled".
class AppCard extends StatelessWidget {
  const AppCard({
    super.key,
    required this.child,
    this.onTap,
    this.padding = const EdgeInsets.all(Space.lg),
    this.margin,
  });

  final Widget child;
  final VoidCallback? onTap;
  final EdgeInsets padding;
  final EdgeInsets? margin;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final card = Container(
      margin: margin,
      clipBehavior: Clip.antiAlias,
      decoration: BoxDecoration(
        color: scheme.surface,
        borderRadius: BorderRadius.circular(Radii.lg),
        border: Border.all(color: scheme.outlineVariant),
        boxShadow: cardShadow(scheme.brightness),
      ),
      child: onTap == null
          ? Padding(padding: padding, child: child)
          : Material(
              color: Colors.transparent,
              borderRadius: BorderRadius.circular(Radii.lg),
              child: InkWell(
                borderRadius: BorderRadius.circular(Radii.lg),
                onTap: onTap,
                child: Padding(padding: padding, child: child),
              ),
            ),
    );
    return card;
  }
}

/// Small uppercase tracked eyebrow label — the "RECENT", "OVERVIEW" style
/// section marker premium dashboards use instead of a plain bold Text as
/// a section header.
class SectionLabel extends StatelessWidget {
  const SectionLabel(this.text, {super.key, this.trailing});
  final String text;
  final Widget? trailing;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Padding(
      padding: const EdgeInsets.only(bottom: Space.md),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            text.toUpperCase(),
            style: Theme.of(context).textTheme.labelSmall?.copyWith(color: scheme.onSurfaceVariant),
          ),
          if (trailing != null) trailing!,
        ],
      ),
    );
  }
}

/// Softly-tinted status indicator — a small dot + label, not a heavy
/// saturated fill. Used for grade/attendance/payment/device status
/// everywhere instead of each screen inventing its own chip styling.
class StatusPill extends StatelessWidget {
  const StatusPill({super.key, required this.label, required this.color});
  final String label;
  final Color color;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.12),
        borderRadius: BorderRadius.circular(Radii.pill),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(width: 6, height: 6, decoration: BoxDecoration(color: color, shape: BoxShape.circle)),
          const SizedBox(width: 6),
          Text(
            label,
            style: TextStyle(color: color, fontSize: 12, fontWeight: FontWeight.w600),
          ),
        ],
      ),
    );
  }
}

/// Neutral initials avatar (people — chat contacts, profile) as a flat
/// tinted circle. Kept separate from icon-badge usage: this is the one
/// place a tinted circular container is idiomatic (it represents a
/// specific person), everywhere else prefers a plain icon.
class InitialsAvatar extends StatelessWidget {
  const InitialsAvatar({super.key, required this.name, this.radius = 20});
  final String name;
  final double radius;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return CircleAvatar(
      radius: radius,
      backgroundColor: scheme.primary.withValues(alpha: 0.1),
      child: Text(
        name.isNotEmpty ? name[0].toUpperCase() : '?',
        style: TextStyle(color: scheme.primary, fontWeight: FontWeight.w700, fontSize: radius * 0.75),
      ),
    );
  }
}
