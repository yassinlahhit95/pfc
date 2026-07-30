import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../features/chat/data/chat_repository.dart';
import '../../features/chat/presentation/chat_detail_screen.dart';
import '../theme/app_theme.dart';

class ProfileDetailSheet extends ConsumerStatefulWidget {
  const ProfileDetailSheet({
    super.key,
    required this.uid,
    required this.rol,
    required this.nombre,
    required this.email,
    required this.telefono,
    this.subtitle,
    this.status,
    this.extraActions,
  });

  final int uid;
  final String rol;
  final String nombre;
  final String email;
  final String? telefono;
  final String? subtitle;
  final String? status;
  final List<Widget>? extraActions;

  static Future<void> show(
    BuildContext context, {
    required int uid,
    required String rol,
    required String nombre,
    required String email,
    required String? telefono,
    String? subtitle,
    String? status,
    List<Widget>? extraActions,
  }) {
    return showModalBottomSheet(
      context: context,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (context) => ProfileDetailSheet(
        uid: uid,
        rol: rol,
        nombre: nombre,
        email: email,
        telefono: telefono,
        subtitle: subtitle,
        status: status,
        extraActions: extraActions,
      ),
    );
  }

  @override
  ConsumerState<ProfileDetailSheet> createState() => _ProfileDetailSheetState();
}

class _ProfileDetailSheetState extends ConsumerState<ProfileDetailSheet> {
  bool _startingChat = false;

  Future<void> _call() async {
    if (widget.telefono == null || widget.telefono!.isEmpty) return;
    final url = Uri.parse('tel:${widget.telefono}');
    if (await canLaunchUrl(url)) {
      await launchUrl(url);
    }
  }

  Future<void> _whatsapp() async {
    if (widget.telefono == null || widget.telefono!.isEmpty) return;
    
    // Clean non-digit characters
    String phone = widget.telefono!.replaceAll(RegExp(r'\D'), '');
    
    // Default to Spain (+34) if it's a 9-digit local number
    if (phone.length == 9 && !phone.startsWith('34')) {
      phone = '34$phone';
    } else if (phone.startsWith('00')) {
      phone = phone.substring(2);
    }

    final url = Uri.parse('https://wa.me/$phone');
    if (await canLaunchUrl(url)) {
      await launchUrl(url, mode: LaunchMode.externalApplication);
    }
  }

  Future<void> _openChat() async {
    setState(() => _startingChat = true);
    try {
      final convId = await ref.read(chatRepositoryProvider).startConversation(
        targetRol: widget.rol,
        targetId: widget.uid,
      );
      if (mounted) {
        Navigator.of(context).pop(); // Close sheet
        Navigator.of(context).push(
          MaterialPageRoute(
            builder: (_) => ChatDetailScreen(
              convId: convId,
              otherNombre: widget.nombre,
            ),
          ),
        );
      }
    } catch (e) {
      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Error al iniciar chat: $e')));
        setState(() => _startingChat = false);
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final textTheme = Theme.of(context).textTheme;
    final hasPhone = widget.telefono != null && widget.telefono!.trim().isNotEmpty;

    return Container(
      decoration: BoxDecoration(
        color: scheme.surface,
        borderRadius: const BorderRadius.vertical(top: Radius.circular(Radii.xl)),
      ),
      padding: const EdgeInsets.fromLTRB(Space.xl, Space.md, Space.xl, Space.xxxl),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Container(
            width: 36,
            height: 4,
            margin: const EdgeInsets.only(bottom: Space.xl),
            decoration: BoxDecoration(
              color: scheme.onSurfaceVariant.withValues(alpha: 0.4),
              borderRadius: BorderRadius.circular(2),
            ),
          ),
          
          Container(
            width: 80,
            height: 80,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              color: scheme.primary.withValues(alpha: 0.15),
            ),
            alignment: Alignment.center,
            child: Text(
              widget.nombre.isNotEmpty ? widget.nombre[0].toUpperCase() : '?',
              style: textTheme.headlineMedium?.copyWith(color: scheme.primary),
            ),
          ),
          const SizedBox(height: Space.md),
          Text(widget.nombre, style: textTheme.titleLarge, textAlign: TextAlign.center),
          
          if (widget.subtitle != null) ...[
            const SizedBox(height: Space.xs),
            Text(widget.subtitle!, style: textTheme.bodyMedium?.copyWith(color: scheme.onSurfaceVariant)),
          ],
          
          const SizedBox(height: Space.xl),
          
          // Data rows
          _DataRow(icon: Icons.email_outlined, text: widget.email),
          if (hasPhone) _DataRow(icon: Icons.phone_outlined, text: widget.telefono!),
          
          const SizedBox(height: Space.xxl),
          
          // Action Buttons
          Row(
            mainAxisAlignment: MainAxisAlignment.spaceEvenly,
            children: [
              _ActionButton(
                icon: Icons.chat_bubble_outline_rounded,
                label: 'Chat',
                onTap: _startingChat ? null : _openChat,
                isLoading: _startingChat,
                color: scheme.primary,
              ),
              _ActionButton(
                icon: Icons.phone_outlined,
                label: 'Llamar',
                onTap: hasPhone ? _call : null,
                color: Colors.green,
              ),
              _ActionButton(
                icon: Icons.message_outlined, // Fallback for whatsapp icon
                label: 'WhatsApp',
                onTap: hasPhone ? _whatsapp : null,
                color: const Color(0xFF25D366),
              ),
            ],
          ),
          if (widget.extraActions != null && widget.extraActions!.isNotEmpty) ...[
            const SizedBox(height: Space.xl),
            const Divider(),
            const SizedBox(height: Space.md),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              children: widget.extraActions!,
            ),
          ],
        ],
      ),
    );
  }
}

class _DataRow extends StatelessWidget {
  const _DataRow({required this.icon, required this.text});
  final IconData icon;
  final String text;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: Space.xs),
      child: Row(
        children: [
          Icon(icon, size: 20, color: Theme.of(context).colorScheme.onSurfaceVariant),
          const SizedBox(width: Space.md),
          Expanded(child: Text(text, style: Theme.of(context).textTheme.bodyMedium)),
        ],
      ),
    );
  }
}

class _ActionButton extends StatelessWidget {
  const _ActionButton({
    required this.icon,
    required this.label,
    required this.onTap,
    required this.color,
    this.isLoading = false,
  });

  final IconData icon;
  final String label;
  final VoidCallback? onTap;
  final Color color;
  final bool isLoading;

  @override
  Widget build(BuildContext context) {
    final disabled = onTap == null || isLoading;
    final effectiveColor = disabled ? Colors.grey : color;
    
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(Radii.md),
      child: Padding(
        padding: const EdgeInsets.all(Space.sm),
        child: Column(
          children: [
            Container(
              padding: const EdgeInsets.all(Space.md),
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: effectiveColor.withValues(alpha: 0.15),
              ),
              child: isLoading 
                  ? SizedBox(width: 24, height: 24, child: CircularProgressIndicator(strokeWidth: 2, color: effectiveColor))
                  : Icon(icon, color: effectiveColor),
            ),
            const SizedBox(height: Space.xs),
            Text(
              label,
              style: Theme.of(context).textTheme.labelSmall?.copyWith(
                color: disabled ? Colors.grey : Theme.of(context).colorScheme.onSurface,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
