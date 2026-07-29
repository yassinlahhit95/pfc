import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:shared_preferences/shared_preferences.dart';

import '../../../core/api/api_exception.dart';
import '../../../core/theme/app_theme.dart';
import '../../../core/widgets/error_modal.dart';
import '../../../core/widgets/premium.dart';
import '../application/login_controller.dart';

class LoginScreen extends ConsumerStatefulWidget {
  const LoginScreen({super.key});

  @override
  ConsumerState<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends ConsumerState<LoginScreen>
    with SingleTickerProviderStateMixin {
  final _formKey = GlobalKey<FormState>();
  final _emailCtrl = TextEditingController();
  final _passwordCtrl = TextEditingController();
  final _pageController = PageController();

  bool _obscure = true;
  bool _showOnboarding = false;
  int _onboardingPageIndex = 0;

  late final AnimationController _entrance;

  @override
  void initState() {
    super.initState();
    _entrance = AnimationController(vsync: this, duration: const Duration(milliseconds: 500));
    _checkOnboarding();
  }

  Future<void> _checkOnboarding() async {
    final prefs = await SharedPreferences.getInstance();
    final completed = prefs.getBool('has_seen_onboarding') ?? false;
    if (!completed) {
      setState(() => _showOnboarding = true);
    } else {
      _entrance.forward();
    }
  }

  Future<void> _completeOnboarding() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool('has_seen_onboarding', true);
    setState(() => _showOnboarding = false);
    _entrance.forward();
  }

  @override
  void dispose() {
    _entrance.dispose();
    _emailCtrl.dispose();
    _passwordCtrl.dispose();
    _pageController.dispose();
    super.dispose();
  }

  Future<void> _submit() async {
    FocusScope.of(context).unfocus();
    if (!_formKey.currentState!.validate()) return;
    await ref.read(loginControllerProvider.notifier).submit(
          email: _emailCtrl.text.trim(),
          password: _passwordCtrl.text,
        );
  }

  Future<void> _submitGoogle() async {
    FocusScope.of(context).unfocus();
    await ref.read(loginControllerProvider.notifier).submitGoogle();
  }

  @override
  Widget build(BuildContext context) {
    final loginState = ref.watch(loginControllerProvider);
    final isLoading = loginState.isLoading;
    final scheme = Theme.of(context).colorScheme;
    final isDark = Theme.of(context).brightness == Brightness.dark;

    ref.listen(loginControllerProvider, (previous, next) {
      final error = next.error;
      if (error != null) {
        final message = error is ApiException
            ? error.message
            : 'No se pudo conectar. Comprueba tu conexión e inténtalo de nuevo.';
        showErrorAlert(context, message, title: 'Error al conectar');
      }
    });

    return AnnotatedRegion<SystemUiOverlayStyle>(
      value: isDark ? SystemUiOverlayStyle.light : SystemUiOverlayStyle.dark,
      child: Scaffold(
        backgroundColor: scheme.surface,
        body: Stack(
          fit: StackFit.expand,
          children: [
            // A single, restrained glow from the app's own accent — not a
            // pair of unrelated gradient blobs invented for this screen alone.
            Positioned(
              left: -160,
              top: -160,
              child: _AccentGlow(color: AppColors.accent, opacity: isDark ? 0.16 : 0.10),
            ),
            if (_showOnboarding) _buildOnboardingView(scheme) else _buildLoginView(isLoading, scheme),
          ],
        ),
      ),
    );
  }

  Widget _buildOnboardingView(ColorScheme scheme) {
    return SafeArea(
      child: Column(
        children: [
          Align(
            alignment: Alignment.topRight,
            child: Padding(
              padding: const EdgeInsets.only(right: Space.lg, top: Space.sm),
              child: TextButton(
                onPressed: _completeOnboarding,
                child: const Text('Saltar'),
              ),
            ),
          ),
          Expanded(
            child: PageView(
              controller: _pageController,
              onPageChanged: (idx) => setState(() => _onboardingPageIndex = idx),
              children: const [
                _OnboardingSlide(
                  icon: Icons.auto_stories_rounded,
                  title: 'Aula Digital Interactiva',
                  description: 'Accede a tus temas, descarga apuntes y sube tus tareas de forma rápida y sencilla.',
                ),
                _OnboardingSlide(
                  icon: Icons.forum_rounded,
                  title: 'Comunicación Directa',
                  description: 'Chatea con tus profesores y compañeros, y recibe avisos en tiempo real.',
                ),
                _OnboardingSlide(
                  icon: Icons.insights_rounded,
                  title: 'Seguimiento al Día',
                  description: 'Consulta tu horario de clases, asistencia y notas desde un único portal personalizado.',
                ),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: Space.xxl, vertical: Space.xxxl),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                _PageIndicator(count: 3, current: _onboardingPageIndex),
                const SizedBox(height: Space.xxl),
                SizedBox(
                  width: double.infinity,
                  child: FilledButton(
                    onPressed: () {
                      if (_onboardingPageIndex < 2) {
                        _pageController.nextPage(
                          duration: const Duration(milliseconds: 320),
                          curve: Curves.easeOutCubic,
                        );
                      } else {
                        _completeOnboarding();
                      }
                    },
                    child: Text(_onboardingPageIndex == 2 ? 'Comenzar' : 'Siguiente'),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildLoginView(bool isLoading, ColorScheme scheme) {
    final textTheme = Theme.of(context).textTheme;
    return SafeArea(
      child: LayoutBuilder(
        builder: (context, constraints) {
          return SingleChildScrollView(
            padding: const EdgeInsets.symmetric(horizontal: Space.xxl, vertical: Space.xxxl),
            child: ConstrainedBox(
              constraints: BoxConstraints(minHeight: constraints.maxHeight - 64),
              child: Center(
                child: ConstrainedBox(
                  constraints: const BoxConstraints(maxWidth: 380),
                  child: AnimatedBuilder(
                    animation: _entrance,
                    builder: (context, child) {
                      final val = CurvedAnimation(parent: _entrance, curve: Curves.easeOutCubic).value;
                      return Opacity(
                        opacity: val,
                        child: Transform.translate(
                          offset: Offset(0, 20.0 * (1.0 - val)),
                          child: child,
                        ),
                      );
                    },
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        const _AppMark(),
                        const SizedBox(height: Space.xxxl),
                        AppCard(
                          padding: const EdgeInsets.all(Space.xxl),
                          child: Form(
                            key: _formKey,
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.stretch,
                              children: [
                                Text('Bienvenido de nuevo', style: textTheme.headlineSmall),
                                const SizedBox(height: Space.xs),
                                Text(
                                  'Inicia sesión para continuar',
                                  style: textTheme.bodyMedium?.copyWith(color: scheme.onSurfaceVariant),
                                ),
                                const SizedBox(height: Space.xxl),
                                TextFormField(
                                  controller: _emailCtrl,
                                  keyboardType: TextInputType.emailAddress,
                                  textInputAction: TextInputAction.next,
                                  autocorrect: false,
                                  decoration: const InputDecoration(
                                    labelText: 'Correo electrónico',
                                    prefixIcon: Icon(Icons.email_outlined),
                                  ),
                                  validator: (v) => (v == null || v.trim().isEmpty) ? 'Requerido' : null,
                                ),
                                const SizedBox(height: Space.lg),
                                TextFormField(
                                  controller: _passwordCtrl,
                                  obscureText: _obscure,
                                  textInputAction: TextInputAction.done,
                                  onFieldSubmitted: (_) => _submit(),
                                  decoration: InputDecoration(
                                    labelText: 'Contraseña',
                                    prefixIcon: const Icon(Icons.lock_outline),
                                    suffixIcon: IconButton(
                                      icon: Icon(_obscure ? Icons.visibility_outlined : Icons.visibility_off_outlined),
                                      onPressed: () => setState(() => _obscure = !_obscure),
                                    ),
                                  ),
                                  validator: (v) => (v == null || v.isEmpty) ? 'Requerido' : null,
                                ),
                                const SizedBox(height: Space.xl),
                                SizedBox(
                                  height: 50,
                                  width: double.infinity,
                                  child: FilledButton(
                                    onPressed: isLoading ? null : _submit,
                                    child: isLoading
                                        ? SizedBox(
                                            height: 20,
                                            width: 20,
                                            child: CircularProgressIndicator(
                                              strokeWidth: 2.4,
                                              valueColor: AlwaysStoppedAnimation<Color>(Theme.of(context).colorScheme.onPrimary),
                                            ),
                                          )
                                        : const Text('Entrar'),
                                  ),
                                ),
                                const SizedBox(height: Space.lg),
                                Row(
                                  children: [
                                    Expanded(child: Divider(color: scheme.outlineVariant)),
                                    Padding(
                                      padding: const EdgeInsets.symmetric(horizontal: Space.md),
                                      child: Text(
                                        'o continuar con',
                                        style: textTheme.bodySmall?.copyWith(color: scheme.onSurfaceVariant),
                                      ),
                                    ),
                                    Expanded(child: Divider(color: scheme.outlineVariant)),
                                  ],
                                ),
                                const SizedBox(height: Space.lg),
                                SizedBox(
                                  height: 50,
                                  width: double.infinity,
                                  child: OutlinedButton.icon(
                                    onPressed: isLoading ? null : _submitGoogle,
                                    icon: const GoogleLogo(size: 20),
                                    label: const Text('Iniciar sesión con Google'),
                                    style: OutlinedButton.styleFrom(
                                      shape: RoundedRectangleBorder(
                                        borderRadius: BorderRadius.circular(Radii.lg),
                                      ),
                                    ),
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ),
                        const SizedBox(height: Space.xxl),
                        Text(
                          'AulaPro · Gestión académica',
                          style: textTheme.bodySmall?.copyWith(color: scheme.onSurfaceVariant),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          );
        },
      ),
    );
  }
}

/// Soft, single-color radial glow behind the login card — restrained accent
/// usage (one color, low opacity) instead of a pair of unrelated gradient
/// blobs with their own invented palette.
class _AccentGlow extends StatelessWidget {
  const _AccentGlow({required this.color, required this.opacity});
  final Color color;
  final double opacity;

  @override
  Widget build(BuildContext context) {
    return Container(
      width: 480,
      height: 480,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        gradient: RadialGradient(colors: [color.withValues(alpha: opacity), Colors.transparent]),
      ),
    );
  }
}

class _OnboardingSlide extends StatelessWidget {
  const _OnboardingSlide({required this.icon, required this.title, required this.description});

  final IconData icon;
  final String title;
  final String description;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final textTheme = Theme.of(context).textTheme;
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: Space.xxxl),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Container(
            width: 132,
            height: 132,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: AppColors.accent.withValues(alpha: scheme.brightness == Brightness.dark ? 0.16 : 0.1),
            ),
            alignment: Alignment.center,
            child: Icon(icon, color: AppColors.accent, size: 56),
          ),
          const SizedBox(height: Space.xxxl + Space.lg),
          Text(title, textAlign: TextAlign.center, style: textTheme.headlineSmall),
          const SizedBox(height: Space.md),
          Text(
            description,
            textAlign: TextAlign.center,
            style: textTheme.bodyMedium?.copyWith(color: scheme.onSurfaceVariant),
          ),
        ],
      ),
    );
  }
}

class _PageIndicator extends StatelessWidget {
  const _PageIndicator({required this.count, required this.current});
  final int count;
  final int current;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: List.generate(count, (index) {
        final active = index == current;
        return AnimatedContainer(
          duration: const Duration(milliseconds: 250),
          margin: const EdgeInsets.symmetric(horizontal: 4),
          width: active ? 22 : 7,
          height: 7,
          decoration: BoxDecoration(
            color: active ? scheme.primary : scheme.outlineVariant,
            borderRadius: BorderRadius.circular(Radii.pill),
          ),
        );
      }),
    );
  }
}

class _AppMark extends StatelessWidget {
  const _AppMark();

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final textTheme = Theme.of(context).textTheme;
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        Container(
          width: 68,
          height: 68,
          decoration: BoxDecoration(
            color: AppColors.accent,
            borderRadius: BorderRadius.circular(Radii.xl),
            boxShadow: cardShadow(scheme.brightness),
          ),
          alignment: Alignment.center,
          child: Icon(Icons.auto_stories_rounded, color: Theme.of(context).colorScheme.onPrimary, size: 30),
        ),
        const SizedBox(height: Space.lg),
        Text('AulaPro', style: textTheme.headlineSmall),
        const SizedBox(height: Space.xs),
        Text(
          'Tu centro, en el bolsillo',
          style: textTheme.bodyMedium?.copyWith(color: scheme.onSurfaceVariant),
        ),
      ],
    );
  }
}

class GoogleLogo extends StatelessWidget {
  const GoogleLogo({super.key, this.size = 24});
  final double size;

  @override
  Widget build(BuildContext context) {
    return CustomPaint(
      size: Size(size, size),
      painter: _GoogleLogoPainter(),
    );
  }
}

class _GoogleLogoPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final double w = size.width;
    final double h = size.height;

    final Paint paint = Paint()
      ..style = PaintingStyle.stroke
      ..strokeWidth = w * 0.22
      ..strokeCap = StrokeCap.square;

    final double radius = (w - paint.strokeWidth) / 2;
    final Rect rect = Rect.fromCircle(center: Offset(w / 2, h / 2), radius: radius);

    // Red Arc (Top Left-ish)
    paint.color = const Color(0xFFEA4335);
    canvas.drawArc(rect, -2.4, 1.25, false, paint);

    // Yellow Arc (Bottom Left-ish)
    paint.color = const Color(0xFFFBBC05);
    canvas.drawArc(rect, -1.15, -1.25, false, paint);

    // Green Arc (Bottom Right-ish)
    paint.color = const Color(0xFF34A853);
    canvas.drawArc(rect, 0.1, 1.25, false, paint);

    // Blue Arc (Top Right-ish + Bar)
    paint.color = const Color(0xFF4285F4);
    canvas.drawArc(rect, 1.35, 1.35, false, paint);

    // Bar
    final Paint barPaint = Paint()
      ..color = const Color(0xFF4285F4)
      ..style = PaintingStyle.fill;

    final double barStartX = w / 2;
    final double barStartY = h / 2 - paint.strokeWidth / 2;
    canvas.drawRect(
      Rect.fromLTRB(barStartX, barStartY, w - paint.strokeWidth / 2, barStartY + paint.strokeWidth),
      barPaint,
    );
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}
