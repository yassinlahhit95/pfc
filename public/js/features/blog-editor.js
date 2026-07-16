// Editor de texto enriquecido para las entradas del blog (admin y secretaría).
// Envuelve document.execCommand sobre un <div contenteditable>, sincronizando el
// HTML resultante hacia el <textarea name="contenido"> real que recibe el formulario.
function iniciarEditorBlog(config) {
    var $editor = document.getElementById(config.editorId);
    var $textarea = document.getElementById(config.textareaId);
    var $fileInput = document.getElementById(config.fileInputId);
    if (!$editor || !$textarea) return;

    var inicial = config.initialContent || '';
    if (inicial && !/<[a-z][\s\S]*>/i.test(inicial)) {
        // Contenido heredado en texto plano (párrafos separados por línea en blanco)
        $editor.innerHTML = inicial.split(/\n{2,}/).map(function (parrafo) {
            var esc = parrafo.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/\n/g, '<br>');
            return '<p>' + esc + '</p>';
        }).join('');
    } else {
        $editor.innerHTML = inicial;
    }

    function sync() { $textarea.value = $editor.innerHTML; }
    $editor.addEventListener('input', sync);
    sync();

    var $toolbar = document.querySelector('[data-editor-toolbar="' + config.textareaId + '"]');
    if ($toolbar) {
        $toolbar.querySelectorAll('button[data-cmd]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                $editor.focus();
                var cmd = btn.dataset.cmd;
                if (cmd === 'createLink') {
                    var url = prompt('URL del enlace:', 'https://');
                    if (!url) return;
                    document.execCommand('createLink', false, url);
                    sync();
                    return;
                }
                document.execCommand(cmd, false, btn.dataset.value || null);
                sync();
            });
        });

        // Selector de título (Párrafo, H1-H6)
        var $selectBloque = $toolbar.querySelector('select[data-cmd-select]');
        if ($selectBloque) {
            $selectBloque.addEventListener('change', function () {
                if (!$selectBloque.value) return;
                $editor.focus();
                document.execCommand($selectBloque.dataset.cmdSelect, false, $selectBloque.value);
                sync();
                $selectBloque.value = '';
            });
        }

        // Color de texto / resaltado. Se fuerza styleWithCSS solo durante el comando
        // para que produzca <span style="color:..."> en vez de <font> (que el
        // sanitizador no permite), sin afectar al resto de comandos (bold, etc.).
        $toolbar.querySelectorAll('input[data-cmd-color]').forEach(function (input) {
            input.addEventListener('input', function () {
                $editor.focus();
                document.execCommand('styleWithCSS', false, true);
                var cmd = input.dataset.cmdColor;
                try {
                    if (!document.execCommand(cmd, false, input.value) && cmd === 'hiliteColor') {
                        document.execCommand('backColor', false, input.value);
                    }
                } catch (e) {
                    document.execCommand('backColor', false, input.value);
                }
                document.execCommand('styleWithCSS', false, false);
                sync();
            });
        });

        var $btnImagen = $toolbar.querySelector('button[data-accion="imagen"]');
        if ($btnImagen && $fileInput) {
            $btnImagen.addEventListener('click', function () { $fileInput.click(); });
        }

        var $btnVideo = $toolbar.querySelector('button[data-accion="video"]');
        if ($btnVideo) {
            $btnVideo.addEventListener('click', function () {
                var url = prompt('Pega la URL del vídeo de YouTube o Vimeo:');
                if (!url) return;
                var embed = _construirEmbedVideoBlog(url);
                if (!embed) {
                    if (window.Toast) Toast.show('URL de vídeo no reconocida. Usa un enlace de YouTube o Vimeo.', 'error');
                    return;
                }
                $editor.focus();
                document.execCommand('insertHTML', false, embed);
                sync();
            });
        }
    }

    if ($fileInput) {
        $fileInput.addEventListener('change', function () {
            if (!$fileInput.files.length) return;
            var fd = new FormData();
            fd.append('imagen', $fileInput.files[0]);
            fd.append('csrf_token', config.csrfToken);

            var $btnImagenUpload = document.querySelector('[data-editor-toolbar="' + config.textareaId + '"] button[data-accion="imagen"]');
            if ($btnImagenUpload) $btnImagenUpload.disabled = true;
            if (window.Toast) Toast.show('Subiendo imagen...', 'info');

            fetch(config.uploadUrl, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    if (res && res.ok) {
                        $editor.focus();
                        document.execCommand('insertHTML', false, '<img src="' + res.url + '" alt="">');
                        sync();
                    } else if (window.Toast) {
                        Toast.show((res && res.msg) ? res.msg : 'Error al subir la imagen', 'error');
                    }
                    $fileInput.value = '';
                })
                .catch(function () {
                    if (window.Toast) Toast.show('Error de conexión al subir la imagen', 'error');
                    $fileInput.value = '';
                })
                .finally(function () {
                    if ($btnImagenUpload) $btnImagenUpload.disabled = false;
                });
        });
    }

    var $form = $editor.closest('form');
    if ($form) {
        $form.addEventListener('submit', function (e) {
            sync();
            if ($editor.textContent.trim() === '' && $editor.querySelectorAll('img, iframe').length === 0) {
                e.preventDefault();
                if (window.Toast) Toast.show('El contenido de la entrada es obligatorio.', 'error');
                $editor.focus();
            }
        });
    }
}

function _construirEmbedVideoBlog(url) {
    var yt = url.match(/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{6,15})/);
    if (yt) {
        return '<iframe src="https://www.youtube-nocookie.com/embed/' + yt[1] + '" width="560" height="315" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
    }
    var vi = url.match(/vimeo\.com\/(\d+)/);
    if (vi) {
        return '<iframe src="https://player.vimeo.com/video/' + vi[1] + '" width="560" height="315" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen></iframe>';
    }
    return null;
}
