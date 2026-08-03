import 'package:flutter/material.dart';

import '../theme/app_theme.dart';
import 'premium.dart';

/// Reusable skeleton loader for list items. Animates between two opacity levels
/// to create a "loading" pulse effect — much friendlier than bare spinners.
class SkeletonLoader extends StatefulWidget {
  const SkeletonLoader({super.key, this.itemCount = 3, this.itemBuilder});

  final int itemCount;
  final Widget Function(BuildContext context, int index)? itemBuilder;

  @override
  State<SkeletonLoader> createState() => _SkeletonLoaderState();
}

class _SkeletonLoaderState extends State<SkeletonLoader>
    with SingleTickerProviderStateMixin {
  late AnimationController _controller;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      duration: const Duration(milliseconds: 1500),
      vsync: this,
    )..repeat(reverse: true);
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;

    if (widget.itemBuilder != null) {
      return ListView.builder(
        padding:
            const EdgeInsets.fromLTRB(Space.xl, Space.md, Space.xl, Space.xxxl),
        itemCount: widget.itemCount,
        itemBuilder: (context, i) => widget.itemBuilder!(context, i),
      );
    }

    // Default skeleton: list of card-like placeholders
    return ListView.builder(
      padding:
          const EdgeInsets.fromLTRB(Space.xl, Space.md, Space.xl, Space.xxxl),
      itemCount: widget.itemCount,
      itemBuilder: (context, i) => AnimatedBuilder(
        animation: _controller,
        builder: (context, _) => Opacity(
          opacity: 0.6 + (_controller.value * 0.2),
          child: AppCard(
            margin: const EdgeInsets.only(bottom: Space.md),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  width: 120,
                  height: 16,
                  decoration: BoxDecoration(
                    color: scheme.surfaceContainerHighest,
                    borderRadius: BorderRadius.circular(Radii.sm),
                  ),
                ),
                const SizedBox(height: Space.md),
                Container(
                  width: double.infinity,
                  height: 12,
                  decoration: BoxDecoration(
                    color: scheme.surfaceContainerHighest,
                    borderRadius: BorderRadius.circular(Radii.sm),
                  ),
                ),
                const SizedBox(height: 8),
                Container(
                  width: 200,
                  height: 12,
                  decoration: BoxDecoration(
                    color: scheme.surfaceContainerHighest,
                    borderRadius: BorderRadius.circular(Radii.sm),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
