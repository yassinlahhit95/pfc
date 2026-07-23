import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../core/widgets/async_view.dart';
import '../data/classroom_repository.dart';

class ModuleDetailScreen extends ConsumerWidget {
  const ModuleDetailScreen({super.key, required this.module});
  final ClassroomModule module;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return DefaultTabController(
      length: 2,
      child: Scaffold(
        appBar: AppBar(
          title: Text(module.nombre),
          bottom: const TabBar(tabs: [
            Tab(text: 'Archivos', icon: Icon(Icons.folder_outlined)),
            Tab(text: 'Tareas', icon: Icon(Icons.assignment_outlined)),
          ]),
        ),
        body: TabBarView(
          children: [
            _FilesTab(idModulo: module.id),
            _TasksTab(idModulo: module.id),
          ],
        ),
      ),
    );
  }
}

class _FilesTab extends ConsumerStatefulWidget {
  const _FilesTab({required this.idModulo});
  final int idModulo;

  @override
  ConsumerState<_FilesTab> createState() => _FilesTabState();
}

class _FilesTabState extends ConsumerState<_FilesTab> {
  int? _openFolderId;
  String? _openFolderName;

  @override
  Widget build(BuildContext context) {
    if (_openFolderId != null) {
      return _FileList(
        idModulo: widget.idModulo,
        idCarpeta: _openFolderId,
        header: ListTile(
          leading: const Icon(Icons.arrow_back),
          title: Text(_openFolderName ?? ''),
          onTap: () => setState(() {
            _openFolderId = null;
            _openFolderName = null;
          }),
        ),
      );
    }

    final foldersAsync = ref.watch(_foldersProvider(widget.idModulo));
    return AsyncView<List<ClassroomFolder>>(
      value: foldersAsync,
      onRetry: () => ref.invalidate(_foldersProvider(widget.idModulo)),
      data: (context, folders) {
        // Expanded only works inside a Flex (Column/Row) — folders render as
        // a small, non-scrolling header block, the file list below owns the
        // actual scrolling via its own ListView (not nested inside one).
        return Column(
          children: [
            if (folders.isNotEmpty)
              ...folders.map((f) => ListTile(
                    leading: Icon(Icons.folder, color: Theme.of(context).colorScheme.primary),
                    title: Text(f.nombre),
                    subtitle: Text('${f.totalArchivos} archivo(s)'),
                    trailing: const Icon(Icons.chevron_right),
                    onTap: () => setState(() {
                      _openFolderId = f.id;
                      _openFolderName = f.nombre;
                    }),
                  )),
            if (folders.isNotEmpty) const Divider(height: 1),
            Expanded(child: _FileList(idModulo: widget.idModulo)),
          ],
        );
      },
    );
  }
}

final _foldersProvider = FutureProvider.autoDispose.family<List<ClassroomFolder>, int>(
  (ref, idModulo) => ref.read(classroomRepositoryProvider).fetchFolders(idModulo),
);

final _filesProvider = FutureProvider.autoDispose.family<List<ClassroomFile>, (int, int?)>(
  (ref, key) => ref.read(classroomRepositoryProvider).fetchFiles(key.$1, idCarpeta: key.$2),
);

class _FileList extends ConsumerWidget {
  const _FileList({required this.idModulo, this.idCarpeta, this.header});
  final int idModulo;
  final int? idCarpeta;
  final Widget? header;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final filesAsync = ref.watch(_filesProvider((idModulo, idCarpeta)));
    return AsyncView<List<ClassroomFile>>(
      value: filesAsync,
      onRetry: () => ref.invalidate(_filesProvider((idModulo, idCarpeta))),
      data: (context, files) {
        if (files.isEmpty && header == null) {
          return const EmptyState(icon: Icons.insert_drive_file_outlined, title: 'Sin archivos');
        }
        return ListView(
          padding: const EdgeInsets.symmetric(vertical: 8),
          children: [
            if (header != null) header!,
            if (files.isEmpty)
              const Padding(
                padding: EdgeInsets.all(24),
                child: Center(child: Text('Sin archivos en esta carpeta')),
              ),
            for (final f in files) _FileTile(file: f),
          ],
        );
      },
    );
  }
}

class _FileTile extends ConsumerWidget {
  const _FileTile({required this.file});
  final ClassroomFile file;

  IconData get _icon => switch (file.extension.toLowerCase()) {
        'pdf' => Icons.picture_as_pdf_outlined,
        'doc' || 'docx' => Icons.description_outlined,
        'xls' || 'xlsx' || 'csv' => Icons.table_chart_outlined,
        'ppt' || 'pptx' => Icons.slideshow_outlined,
        'jpg' || 'jpeg' || 'png' || 'gif' || 'webp' => Icons.image_outlined,
        'zip' || 'rar' => Icons.folder_zip_outlined,
        _ => Icons.insert_drive_file_outlined,
      };

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    return ListTile(
      leading: Icon(_icon, color: Theme.of(context).colorScheme.primary),
      title: Text(file.nombreOriginal),
      subtitle: Text('${file.humanSize} · ${file.nombreProfesor}'),
      trailing: const Icon(Icons.download_outlined),
      onTap: () async {
        final url = ref.read(classroomRepositoryProvider).downloadUrl(file.id);
        final uri = Uri.parse(url);
        final ok = await launchUrl(uri, mode: LaunchMode.externalApplication);
        if (!ok && context.mounted) {
          ScaffoldMessenger.of(context)
              .showSnackBar(const SnackBar(content: Text('No se pudo abrir el archivo.')));
        }
      },
    );
  }
}

final _tasksProvider = FutureProvider.autoDispose.family<List<ClassroomTask>, int>(
  (ref, idModulo) => ref.read(classroomRepositoryProvider).fetchTasks(idModulo),
);

class _TasksTab extends ConsumerWidget {
  const _TasksTab({required this.idModulo});
  final int idModulo;

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final tasksAsync = ref.watch(_tasksProvider(idModulo));
    return AsyncView<List<ClassroomTask>>(
      value: tasksAsync,
      onRetry: () => ref.invalidate(_tasksProvider(idModulo)),
      data: (context, tasks) {
        if (tasks.isEmpty) {
          return const EmptyState(icon: Icons.assignment_outlined, title: 'Sin tareas publicadas');
        }
        return RefreshIndicator(
          onRefresh: () async => ref.invalidate(_tasksProvider(idModulo)),
          child: ListView.builder(
            padding: const EdgeInsets.all(12),
            itemCount: tasks.length,
            itemBuilder: (context, i) => _TaskCard(task: tasks[i]),
          ),
        );
      },
    );
  }
}

class _TaskCard extends StatelessWidget {
  const _TaskCard({required this.task});
  final ClassroomTask task;

  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    final graded = task.estado == 'corregida';
    final date = DateTime.tryParse(task.fechaCreacion.replaceFirst(' ', 'T'));

    return Card(
      margin: const EdgeInsets.only(bottom: 10),
      child: Padding(
        padding: const EdgeInsets.all(14),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Expanded(
                  child: Text(task.titulo, style: Theme.of(context).textTheme.titleSmall?.copyWith(fontWeight: FontWeight.bold)),
                ),
                if (task.nota != null)
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                    decoration: BoxDecoration(
                      color: graded ? const Color(0xFF10B981).withValues(alpha: 0.15) : scheme.surfaceContainerHighest,
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Text(
                      task.nota!,
                      style: TextStyle(
                        fontWeight: FontWeight.bold,
                        color: graded ? const Color(0xFF10B981) : scheme.onSurface,
                      ),
                    ),
                  ),
              ],
            ),
            const SizedBox(height: 6),
            Text(task.descripcion, style: Theme.of(context).textTheme.bodyMedium),
            const SizedBox(height: 6),
            Text(
              '${task.nombreProfesor}${date != null ? ' · ${DateFormat('d MMM yyyy').format(date)}' : ''}',
              style: Theme.of(context).textTheme.bodySmall,
            ),
            if (task.comentario != null && task.comentario!.isNotEmpty) ...[
              const SizedBox(height: 8),
              Container(
                width: double.infinity,
                padding: const EdgeInsets.all(10),
                decoration: BoxDecoration(
                  color: scheme.surfaceContainerHighest,
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Text('💬 ${task.comentario}', style: Theme.of(context).textTheme.bodySmall),
              ),
            ],
          ],
        ),
      ),
    );
  }
}
