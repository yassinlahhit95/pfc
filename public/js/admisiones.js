$(document).ready(function() {
    let currentStep = 1;
    let idPreMatricula = null;

    // Navegación del Wizard
    function showStep(step) {
        $('.step-content').removeClass('active');
        $(`.step-content[data-step="${step}"]`).addClass('active');
        
        $('.step-item').removeClass('active');
        for (let i = 1; i <= step; i++) {
            $(`.step-item[data-step="${i}"]`).addClass(i < step ? 'completed' : 'active');
        }

        // Mostrar/Ocultar botones
        if (step === 1) {
            $('.btn-prev').hide();
        } else {
            $('.btn-prev').show();
        }

        if (step === 3) {
            $('.btn-next').text('Finalizar');
        } else {
            $('.btn-next').text('Siguiente');
        }
    }

    // Botón Siguiente
    $('.btn-next').click(function() {
        if (currentStep === 1) {
            submitStep1();
        } else if (currentStep === 2) {
            // Validar que se hayan subido archivos (opcional)
            currentStep++;
            showStep(currentStep);
            updateSummary();
        } else if (currentStep === 3) {
            finalizeWizard();
        }
    });

    // Botón Anterior
    $('.btn-prev').click(function() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });

    function submitStep1() {
        const formData = {
            dni: $('#dni').val(),
            nombre: $('#nombre').val(),
            apellidos: $('#apellidos').val(),
            email: $('#email').val(),
            telefono: $('#telefono').val(),
            idCiclo: $('#idCiclo').val(),
            curso: $('#curso').val()
        };

        // Validaciones básicas
        if (!formData.dni || !formData.nombre || !formData.email || !formData.idCiclo) {
            Swal.fire('Error', 'Por favor, rellena todos los campos obligatorios', 'error');
            return;
        }

        $.ajax({
            url: '../../controladores/admisiones/acciones.php?action=step1',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.status === 'success') {
                    idPreMatricula = response.idPreMatricula;
                    currentStep++;
                    showStep(currentStep);
                } else {
                    Swal.fire('Error', response.message || 'Error al guardar los datos', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Error de conexión con el servidor', 'error');
            }
        });
    }

    // Manejo de subida de archivos
    $('.file-input').change(function() {
        const input = $(this);
        const tipo = input.data('tipo');
        const file = input[0].files[0];
        
        if (!file) return;

        const formData = new FormData();
        formData.append('idPreMatricula', idPreMatricula);
        formData.append('tipoDocumento', tipo);
        formData.append('archivo', file);

        // Mostrar loading
        const statusEl = $(`.file-status[data-tipo="${tipo}"]`);
        statusEl.html('<span class="text-info">Subiendo...</span>');

        $.ajax({
            url: '../../controladores/admisiones/acciones.php?action=upload',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    statusEl.html('<span class="text-success">✔ Subido</span>');
                } else {
                    statusEl.html('<span class="text-danger">✖ Error</span>');
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function() {
                statusEl.html('<span class="text-danger">✖ Error</span>');
            }
        });
    });

    function updateSummary() {
        $('#summary-nombre').text($('#nombre').val() + ' ' + $('#apellidos').val());
        $('#summary-ciclo').text($('#idCiclo option:selected').text());
        $('#summary-email').text($('#email').val());
    }

    function finalizeWizard() {
        $.ajax({
            url: '../../controladores/admisiones/acciones.php?action=finalize',
            type: 'POST',
            data: { idPreMatricula: idPreMatricula },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        title: '¡Solicitud Enviada!',
                        text: 'Tu solicitud de pre-matrícula ha sido registrada correctamente. Revisaremos tu documentación en breve.',
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonText: 'Nueva Solicitud',
                        cancelButtonText: 'Volver al Inicio',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload();
                        } else {
                            window.location.href = '/';
                        }
                    });
                }
            }
        });
    }
});
