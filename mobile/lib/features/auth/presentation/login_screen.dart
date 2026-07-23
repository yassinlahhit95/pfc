import 'dart:math' as math;
import 'dart:ui';

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

import '../../../core/api/api_exception.dart';
import '../application/login_controller.dart';

// Same three blob colors as the web app's premium .bg-mesh background
// (public/css/dashboard.css) — kept in sync deliberately, just pushed
// further into a dark glassmorphic treatment for the mobile login.
const _blobIndigo = Color(0xFF4F46E5);
const _blobCyan = Color(0xFF22D3EE);
const _blobPink = Color(0xFFF472B6);
const _bgDeep = Color(0xFF07060F);

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
  bool _obscure = true;
  late final AnimationController _drift;

  @override
  void initState() {
    super.initState();
    _drift =
        AnimationController(vsync: this, duration: const Duration(seconds: 24))
          ..repeat();
  }

  @override
  void dispose() {
    _drift.dispose();
    _emailCtrl.dispose();
    _passwordCtrl.dispose();
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

  @override
  Widget build(BuildContext context) {
    final loginState = ref.watch(loginControllerProvider);
    final isLoading = loginState.isLoading;

    ref.listen(loginControllerProvider, (previous, next) {
      final error = next.error;
      if (error != null) {
        final message = error is ApiException
            ? error.message
            : 'No se pudo conectar. Comprueba tu conexión e inténtalo de nuevo.';
        ScaffoldMessenger.of(context)
          ..hideCurrentSnackBar()
          ..showSnackBar(SnackBar(
            content: Text(message),
            backgroundColor: const Color(0xFF1D2638),
          ));
      }
    });

    return AnnotatedRegion<SystemUiOverlayStyle>(
      // This screen is dark while the rest of the app is light-themed —
      // force light status bar icons here specifically rather than globally.
      value: SystemUiOverlayStyle.light,
      child: Scaffold(
        backgroundColor: _bgDeep,
        body: Stack(
          fit: StackFit.expand,
          children: [
            AnimatedBuilder(
              animation: _drift,
              builder: (context, _) => Stack(
                children: [
                  _Blob(
                      color: _blobIndigo,
                      t: _drift.value,
                      phase: 0,
                      size: 340,
                      alignX: -0.9,
                      alignY: -0.8),
                  _Blob(
                      color: _blobCyan,
                      t: _drift.value,
                      phase: 0.33,
                      size: 300,
                      alignX: 1.0,
                      alignY: -0.5),
                  _Blob(
                      color: _blobPink,
                      t: _drift.value,
                      phase: 0.66,
                      size: 280,
                      alignX: -0.3,
                      alignY: 1.1),
                ],
              ),
            ),
            // Subtle dark scrim so text/glass stay legible over bright blobs.
            Container(color: Colors.black.withValues(alpha: 0.25)),
            SafeArea(
              child: LayoutBuilder(
                builder: (context, constraints) {
                  return SingleChildScrollView(
                    padding: const EdgeInsets.symmetric(
                        horizontal: 24, vertical: 32),
                    child: ConstrainedBox(
                      constraints:
                          BoxConstraints(minHeight: constraints.maxHeight - 64),
                      child: Center(
                        child: ConstrainedBox(
                          constraints: const BoxConstraints(maxWidth: 400),
                          child: Column(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              const _LuxLogo(),
                              const SizedBox(height: 40),
                              _GlassCard(
                                child: Form(
                                  key: _formKey,
                                  child: Column(
                                    crossAxisAlignment:
                                        CrossAxisAlignment.stretch,
                                    children: [
                                      const Text(
                                        'Bienvenido de nuevo',
                                        style: TextStyle(
                                          color: Colors.white,
                                          fontSize: 22,
                                          fontWeight: FontWeight.bold,
                                        ),
                                      ),
                                      const SizedBox(height: 4),
                                      Text(
                                        'Inicia sesión para continuar',
                                        style: TextStyle(
                                            color: Colors.white
                                                .withValues(alpha: 0.65)),
                                      ),
                                      const SizedBox(height: 28),
                                      _LuxField(
                                        controller: _emailCtrl,
                                        label: 'Correo electrónico',
                                        icon: Icons.email_outlined,
                                        keyboardType:
                                            TextInputType.emailAddress,
                                        textInputAction: TextInputAction.next,
                                        validator: (v) =>
                                            (v == null || v.trim().isEmpty)
                                                ? 'Requerido'
                                                : null,
                                      ),
                                      const SizedBox(height: 14),
                                      _LuxField(
                                        controller: _passwordCtrl,
                                        label: 'Contraseña',
                                        icon: Icons.lock_outline,
                                        obscureText: _obscure,
                                        textInputAction: TextInputAction.done,
                                        onSubmitted: (_) => _submit(),
                                        validator: (v) =>
                                            (v == null || v.isEmpty)
                                                ? 'Requerido'
                                                : null,
                                        suffix: IconButton(
                                          icon: Icon(
                                            _obscure
                                                ? Icons.visibility_outlined
                                                : Icons.visibility_off_outlined,
                                            color: Colors.white
                                                .withValues(alpha: 0.6),
                                          ),
                                          onPressed: () => setState(
                                              () => _obscure = !_obscure),
                                        ),
                                      ),
                                      const SizedBox(height: 26),
                                      _LuxButton(
                                        loading: isLoading,
                                        onPressed: isLoading ? null : _submit,
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                              const SizedBox(height: 24),
                              Text(
                                'AulaPro · Gestión académica',
                                style: TextStyle(
                                    color: Colors.white.withValues(alpha: 0.45),
                                    fontSize: 12),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ),
                  );
                },
              ),
            ),
          ],
        ),
      ),
    );
  }
}

/// Slow organic drift for a blurred blob, matching the web's `drift1`/
/// `drift2` keyframes conceptually (translate + scale on a sine cycle).
class _Blob extends StatelessWidget {
  const _Blob({
    required this.color,
    required this.t,
    required this.phase,
    required this.size,
    required this.alignX,
    required this.alignY,
  });

  final Color color;
  final double t; // 0..1 looping
  final double phase;
  final double size;
  final double alignX;
  final double alignY;

  @override
  Widget build(BuildContext context) {
    final cycle = (t + phase) % 1.0;
    final angle = cycle * 2 * math.pi;
    final dx = math.sin(angle) * 24;
    final dy = math.cos(angle) * 18;
    final scale = 1.0 + math.sin(angle) * 0.08;

    return Align(
      alignment: Alignment(alignX, alignY),
      child: Transform.translate(
        offset: Offset(dx, dy),
        child: Transform.scale(
          scale: scale,
          child: ImageFiltered(
            imageFilter: ImageFilter.blur(sigmaX: 70, sigmaY: 70),
            child: Container(
              width: size,
              height: size,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                gradient: RadialGradient(
                  colors: [
                    color.withValues(alpha: 0.9),
                    color.withValues(alpha: 0.0)
                  ],
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}

class _LuxLogo extends StatelessWidget {
  const _LuxLogo();

  @override
  Widget build(BuildContext context) {
    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        Container(
          width: 68,
          height: 68,
          decoration: BoxDecoration(
            gradient: const LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: [Colors.white, Color(0xFFE2E8F0)],
            ),
            borderRadius: BorderRadius.circular(20),
            boxShadow: [
              BoxShadow(
                  color: _blobIndigo.withValues(alpha: 0.5),
                  blurRadius: 28,
                  offset: const Offset(0, 12)),
            ],
          ),
          child: Center(
            child: Transform.rotate(
              angle: 0.14,
              child: Container(
                width: 24,
                height: 24,
                decoration: BoxDecoration(
                    color: _blobIndigo, borderRadius: BorderRadius.circular(7)),
              ),
            ),
          ),
        ),
        const SizedBox(height: 18),
        const Text(
          'AulaPro',
          style: TextStyle(
              color: Colors.white,
              fontSize: 30,
              fontWeight: FontWeight.w800,
              letterSpacing: 0.2),
        ),
        const SizedBox(height: 4),
        Text('Tu centro, en el bolsillo',
            style: TextStyle(color: Colors.white.withValues(alpha: 0.7))),
      ],
    );
  }
}

class _GlassCard extends StatelessWidget {
  const _GlassCard({required this.child});
  final Widget child;

  @override
  Widget build(BuildContext context) {
    return ClipRRect(
      borderRadius: BorderRadius.circular(28),
      child: BackdropFilter(
        filter: ImageFilter.blur(sigmaX: 24, sigmaY: 24),
        child: Container(
          padding: const EdgeInsets.fromLTRB(22, 28, 22, 22),
          decoration: BoxDecoration(
            color: Colors.white.withValues(alpha: 0.08),
            borderRadius: BorderRadius.circular(28),
            border: Border.all(color: Colors.white.withValues(alpha: 0.18)),
            boxShadow: [
              BoxShadow(
                  color: Colors.black.withValues(alpha: 0.3),
                  blurRadius: 40,
                  offset: const Offset(0, 20)),
            ],
          ),
          child: child,
        ),
      ),
    );
  }
}

class _LuxField extends StatelessWidget {
  const _LuxField({
    required this.controller,
    required this.label,
    required this.icon,
    this.obscureText = false,
    this.keyboardType,
    this.textInputAction,
    this.onSubmitted,
    this.validator,
    this.suffix,
  });

  final TextEditingController controller;
  final String label;
  final IconData icon;
  final bool obscureText;
  final TextInputType? keyboardType;
  final TextInputAction? textInputAction;
  final ValueChanged<String>? onSubmitted;
  final String? Function(String?)? validator;
  final Widget? suffix;

  @override
  Widget build(BuildContext context) {
    return TextFormField(
      controller: controller,
      obscureText: obscureText,
      keyboardType: keyboardType,
      textInputAction: textInputAction,
      onFieldSubmitted: onSubmitted,
      validator: validator,
      autocorrect: false,
      style: const TextStyle(color: Colors.white),
      cursorColor: Colors.white,
      decoration: InputDecoration(
        labelText: label,
        labelStyle: TextStyle(color: Colors.white.withValues(alpha: 0.65)),
        prefixIcon: Icon(icon, color: Colors.white.withValues(alpha: 0.65)),
        suffixIcon: suffix,
        filled: true,
        fillColor: Colors.white.withValues(alpha: 0.07),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: BorderSide(color: Colors.white.withValues(alpha: 0.15)),
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: BorderSide(color: Colors.white.withValues(alpha: 0.15)),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(14),
          borderSide: const BorderSide(color: _blobCyan, width: 1.4),
        ),
        errorStyle: const TextStyle(color: Color(0xFFF87171)),
      ),
    );
  }
}

class _LuxButton extends StatelessWidget {
  const _LuxButton({required this.loading, required this.onPressed});
  final bool loading;
  final VoidCallback? onPressed;

  @override
  Widget build(BuildContext context) {
    return Container(
      height: 52,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(14),
        gradient:
            const LinearGradient(colors: [_blobIndigo, Color(0xFF6D28D9)]),
        boxShadow: [
          BoxShadow(
              color: _blobIndigo.withValues(alpha: 0.45),
              blurRadius: 20,
              offset: const Offset(0, 10)),
        ],
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          borderRadius: BorderRadius.circular(14),
          onTap: onPressed,
          child: Center(
            child: loading
                ? const SizedBox(
                    height: 20,
                    width: 20,
                    child: CircularProgressIndicator(
                        strokeWidth: 2, color: Colors.white),
                  )
                : const Text(
                    'Entrar',
                    style: TextStyle(
                        color: Colors.white,
                        fontWeight: FontWeight.bold,
                        fontSize: 16),
                  ),
          ),
        ),
      ),
    );
  }
}
