import 'dart:math' as math;

import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/auth/auth_state.dart';
import '../../../core/theme/app_theme.dart';

class OnboardingScreen extends ConsumerStatefulWidget {
  const OnboardingScreen({super.key});

  @override
  ConsumerState<OnboardingScreen> createState() => _OnboardingScreenState();
}

class _OnboardingScreenState extends ConsumerState<OnboardingScreen> {
  final PageController _pageController = PageController();
  int _currentPage = 0;

  final List<_Slide> _slides = const [
    _Slide(
      title: 'Control académico total',
      description:
          'Accede a tus clases, consulta el calendario y mantén tus horarios siempre a la mano.',
      icon: Icons.calendar_today_rounded,
      satellites: [Icons.menu_book_rounded, Icons.access_time_filled_rounded],
      color: Color(0xFF6366F1), // Indigo
    ),
    _Slide(
      title: 'Comunicación en tiempo real',
      description:
          'Mensajería directa y avisos importantes para estar siempre al tanto de lo que ocurre en el centro.',
      icon: Icons.chat_bubble_rounded,
      satellites: [Icons.notifications_active_rounded, Icons.forum_rounded],
      color: Color(0xFF0EA5E9), // Sky Blue
    ),
    _Slide(
      title: 'Tareas y Aula Digital',
      description:
          'Descarga materiales adjuntos, entrega tus trabajos y consulta tus calificaciones al instante.',
      icon: Icons.assignment_rounded,
      satellites: [Icons.cloud_download_rounded, Icons.grade_rounded],
      color: Color(0xFF10B981), // Emerald Green
    ),
  ];

  void _onSkip() {
    ref.read(onboardingCompletedControllerProvider.notifier).complete();
  }

  void _onNext() {
    if (_currentPage < _slides.length - 1) {
      _pageController.nextPage(
        duration: const Duration(milliseconds: 300),
        curve: Curves.easeInOutCubic,
      );
    } else {
      _onSkip();
    }
  }

  @override
  void dispose() {
    _pageController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final activeColor = _slides[_currentPage].color;

    return Scaffold(
      backgroundColor: scheme.surface,
      body: Stack(
        fit: StackFit.expand,
        children: [
          // "Live wallpaper" — a slow, continuous drift behind everything,
          // recolored to match whichever slide is showing.
          _LiveBackground(color: activeColor),
          SafeArea(
            child: Column(
              children: [
                Align(
                  alignment: Alignment.topRight,
                  child: Padding(
                    padding:
                        const EdgeInsets.only(right: Space.lg, top: Space.sm),
                    child: _currentPage < _slides.length - 1
                        ? TextButton(
                            onPressed: _onSkip,
                            child: const Text('Omitir'),
                          )
                        : const SizedBox(height: 48),
                  ),
                ),
                Expanded(
                  child: PageView.builder(
                    controller: _pageController,
                    itemCount: _slides.length,
                    onPageChanged: (index) =>
                        setState(() => _currentPage = index),
                    itemBuilder: (context, index) =>
                        _OnboardingPage(slide: _slides[index]),
                  ),
                ),
                Padding(
                  padding: const EdgeInsets.all(Space.xl),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      _DotIndicator(
                        count: _slides.length,
                        activePage: _currentPage,
                        color: activeColor,
                      ),
                      const SizedBox(height: Space.xxl),
                      SizedBox(
                        width: double.infinity,
                        child: FilledButton(
                          onPressed: _onNext,
                          style: FilledButton.styleFrom(
                            backgroundColor: activeColor,
                            padding: const EdgeInsets.symmetric(
                                vertical: Space.md),
                          ),
                          child: Text(
                            _currentPage == _slides.length - 1
                                ? 'Comenzar'
                                : 'Siguiente',
                            style: const TextStyle(
                                fontWeight: FontWeight.bold, fontSize: 16),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _Slide {
  const _Slide({
    required this.title,
    required this.description,
    required this.icon,
    required this.satellites,
    required this.color,
  });

  final String title;
  final String description;
  final IconData icon;
  final List<IconData> satellites;
  final Color color;
}

class _OnboardingPage extends StatelessWidget {
  const _OnboardingPage({required this.slide});
  final _Slide slide;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: Space.xl),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          _AnimatedIllustration(slide: slide),
          const SizedBox(height: Space.xxxl),
          Text(
            slide.title,
            textAlign: TextAlign.center,
            style: Theme.of(context).textTheme.headlineMedium?.copyWith(
                  fontWeight: FontWeight.w800,
                  color: scheme.onSurface,
                ),
          ),
          const SizedBox(height: Space.md),
          Text(
            slide.description,
            textAlign: TextAlign.center,
            style: Theme.of(context).textTheme.bodyLarge?.copyWith(
                  color: scheme.onSurfaceVariant,
                  height: 1.5,
                ),
          ),
        ],
      ),
    );
  }
}

/// A small "vector illustration" scene, not just an icon in a circle: a
/// rounded device-like card carrying the slide's main glyph, orbited by two
/// smaller satellite glyphs that float independently — the continuous,
/// looping motion is what reads as "alive" rather than a static png.
class _AnimatedIllustration extends StatefulWidget {
  const _AnimatedIllustration({required this.slide});
  final _Slide slide;

  @override
  State<_AnimatedIllustration> createState() => _AnimatedIllustrationState();
}

class _AnimatedIllustrationState extends State<_AnimatedIllustration>
    with SingleTickerProviderStateMixin {
  late final AnimationController _controller = AnimationController(
    vsync: this,
    duration: const Duration(seconds: 6),
  )..repeat();

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final color = widget.slide.color;
    final dark = scheme.brightness == Brightness.dark;

    return SizedBox(
      width: 220,
      height: 220,
      child: AnimatedBuilder(
        animation: _controller,
        builder: (context, _) {
          final t = _controller.value * 2 * math.pi;
          final pulse = 1.0 + math.sin(t) * 0.03;
          final float1 = math.sin(t + math.pi / 2) * 10;
          final float2 = math.sin(t) * 10;

          return Stack(
            alignment: Alignment.center,
            children: [
              // Slowly breathing glow behind the card.
              Transform.scale(
                scale: pulse,
                child: Container(
                  width: 190,
                  height: 190,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    color: color.withValues(alpha: dark ? 0.16 : 0.10),
                  ),
                ),
              ),
              // Main card.
              Transform.scale(
                scale: pulse,
                child: Container(
                  width: 128,
                  height: 128,
                  decoration: BoxDecoration(
                    color: color,
                    borderRadius: BorderRadius.circular(Radii.xl),
                    boxShadow: [
                      BoxShadow(
                        color: color.withValues(alpha: 0.35),
                        blurRadius: 24,
                        offset: const Offset(0, 12),
                      ),
                    ],
                  ),
                  alignment: Alignment.center,
                  child: Icon(widget.slide.icon,
                      color: Colors.white, size: 54),
                ),
              ),
              // Satellite 1 — top-right, floats up/down.
              Positioned(
                right: 12,
                top: 30 + float1,
                child: _SatelliteChip(
                  icon: widget.slide.satellites[0],
                  color: color,
                ),
              ),
              // Satellite 2 — bottom-left, floats opposite phase.
              Positioned(
                left: 8,
                bottom: 28 + float2,
                child: _SatelliteChip(
                  icon: widget.slide.satellites[1],
                  color: color,
                ),
              ),
            ],
          );
        },
      ),
    );
  }
}

class _SatelliteChip extends StatelessWidget {
  const _SatelliteChip({required this.icon, required this.color});
  final IconData icon;
  final Color color;

  @override
  Widget build(BuildContext context) {
    return Container(
      width: 44,
      height: 44,
      decoration: BoxDecoration(
        color: Theme.of(context).colorScheme.surface,
        shape: BoxShape.circle,
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.12),
            blurRadius: 10,
            offset: const Offset(0, 4),
          ),
        ],
      ),
      alignment: Alignment.center,
      child: Icon(icon, color: color, size: 20),
    );
  }
}

/// Two large, slowly drifting/rotating blurred blobs behind the whole
/// screen — the "live wallpaper" feel, cheap to run (no external asset,
/// no video decoder) since it's just two Transform-animated gradients.
class _LiveBackground extends StatefulWidget {
  const _LiveBackground({required this.color});
  final Color color;

  @override
  State<_LiveBackground> createState() => _LiveBackgroundState();
}

class _LiveBackgroundState extends State<_LiveBackground>
    with SingleTickerProviderStateMixin {
  late final AnimationController _controller = AnimationController(
    vsync: this,
    duration: const Duration(seconds: 20),
  )..repeat();

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final dark = Theme.of(context).brightness == Brightness.dark;
    final opacity = dark ? 0.14 : 0.09;
    return AnimatedBuilder(
      animation: _controller,
      builder: (context, _) {
        final t = _controller.value * 2 * math.pi;
        return Stack(
          children: [
            Positioned(
              left: -140 + math.sin(t) * 30,
              top: -140 + math.cos(t) * 20,
              child: AnimatedContainer(
                duration: const Duration(milliseconds: 400),
                width: 380,
                height: 380,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  gradient: RadialGradient(colors: [
                    widget.color.withValues(alpha: opacity),
                    Colors.transparent,
                  ]),
                ),
              ),
            ),
            Positioned(
              right: -120 + math.cos(t) * 24,
              bottom: -120 + math.sin(t) * 26,
              child: AnimatedContainer(
                duration: const Duration(milliseconds: 400),
                width: 320,
                height: 320,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  gradient: RadialGradient(colors: [
                    widget.color.withValues(alpha: opacity),
                    Colors.transparent,
                  ]),
                ),
              ),
            ),
          ],
        );
      },
    );
  }
}

class _DotIndicator extends StatelessWidget {
  const _DotIndicator(
      {required this.count, required this.activePage, required this.color});
  final int count;
  final int activePage;
  final Color color;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: List.generate(count, (index) {
        final active = index == activePage;
        return AnimatedContainer(
          duration: const Duration(milliseconds: 300),
          margin: const EdgeInsets.symmetric(horizontal: 4),
          width: active ? 24 : 8,
          height: 8,
          decoration: BoxDecoration(
            color: active ? color : scheme.outlineVariant,
            borderRadius: BorderRadius.circular(4),
          ),
        );
      }),
    );
  }
}
