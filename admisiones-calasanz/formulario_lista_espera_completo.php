<section class="testimonials text-center bg-light" id="espera">
  <div class="container">
    <div class="well">
      <h4 class="mb-5" id="form">Formulario para acceder a la lista de espera</h4>
    </div>
    <div class="alert alert-info alert-dismissible">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
      <strong>Atención:</strong> Se recomienda completar el formulario desde un ordenador para su correcta visualización.
    </div>

    <div class="card mx-auto" style="max-width: 900px;">
      <div class="card-header bg-primary text-white text-left">
        <strong>Datos personales del solicitante a lista de espera</strong>
      </div>
      <div class="card-body">
        <form action="insert_noadmit.php" method="post" enctype="multipart/form-data">
          <div class="form-row">
            <div class="form-group col-md-6">
              <label>Nombre del solicitante</label>
              <input type="text" name="nombre" class="form-control" maxlength="50" placeholder="Tu nombre" required>
            </div>
            <div class="form-group col-md-6">
              <label>Apellidos del solicitante</label>
              <input type="text" name="apellidos" class="form-control" maxlength="50" placeholder="Tus apellidos" required>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label>DNI o NIE</label>
              <input type="text" name="dni" class="form-control" maxlength="9" placeholder="Ej: 12345678A" pattern="(([X-Z]{1})([-]?)(\d{7})([-]?)([A-Z]{1}))|((\d{8})([-]?)([A-Z]{1}))" required>
            </div>
            <div class="form-group col-md-6">
              <label>Teléfono</label>
              <input type="text" name="telefono" class="form-control" maxlength="9" placeholder="Ej: 600123456" required>
            </div>
          </div>

          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" maxlength="50" placeholder="ejemplo@correo.com" required>
          </div>

          <div class="form-group">
            <label>Estudios aportados para el acceso</label>
            <input type="text" name="estudios" class="form-control" maxlength="100" placeholder="Introduce tus estudios" required>
          </div>

          <div class="form-group">
            <label>¿Realizaste la preinscripción en junio?</label>
            <select class="form-control" name="preinscripcion" required>
              <option value="Defecto">- Selecciona una opción -</option>
              <option value="Si">Sí, realicé la preinscripción para el curso 25/26.</option>
              <option value="No">No, no realicé la preinscripción.</option>
            </select>
          </div>

          <div class="form-row">
            <div class="form-group col-md-4">
              <label>Primera opción deseada</label>
              <select class="form-control" name="ciclo_1" required><option value="Defecto">- Selecciona un ciclo -</option>
<option value="LACB3">Laboratorio Cl&iacute;nico y Biom&eacute;dico</option>
<option value="IDMN3">Imagen para el Diagn&oacute;stico y Medicina Nuclear</option>
<option value="DADSA3">Documentaci&oacute;n y Administraci&oacute;n Sanitaria</option>
<option value="EDIN3">Educaci&oacute;n Infantil</option>
<option value="INSO3">Integraci&oacute;n Social</option>
<option value="ADFI3">Administraci&oacute;n y Finanzas</option>
<option value="MAPU3">Marketing y Publicidad</option>
<option value="GA2">Gesti&oacute;n Administrativa</option>
<option value="FAPA2">Farmacia y Parafarmacia</option>
<option value="EN2">Cuidados Auxiliares de Enfermer&iacute;a</option>
<option value="PESD2">Atenci&oacute;n a Personas en Situaci&oacute;n de Dependencia</option>
<option value="FPB">Formaci&oacute;n Profesional B&aacute;sica</option>
<option value="RTDS">Radioterapia y dosimetría</option>
<option value="EMSA">Emergencias Sanitarias</option></select>
            </div>
            <div class="form-group col-md-4">
              <label>Segunda opción deseada</label>
              <select class="form-control" name="ciclo_2" required><option value="Defecto">- Selecciona un ciclo -</option>
<option value="LACB3">Laboratorio Cl&iacute;nico y Biom&eacute;dico</option>
<option value="IDMN3">Imagen para el Diagn&oacute;stico y Medicina Nuclear</option>
<option value="DADSA3">Documentaci&oacute;n y Administraci&oacute;n Sanitaria</option>
<option value="EDIN3">Educaci&oacute;n Infantil</option>
<option value="INSO3">Integraci&oacute;n Social</option>
<option value="ADFI3">Administraci&oacute;n y Finanzas</option>
<option value="MAPU3">Marketing y Publicidad</option>
<option value="GA2">Gesti&oacute;n Administrativa</option>
<option value="FAPA2">Farmacia y Parafarmacia</option>
<option value="EN2">Cuidados Auxiliares de Enfermer&iacute;a</option>
<option value="PESD2">Atenci&oacute;n a Personas en Situaci&oacute;n de Dependencia</option>
<option value="FPB">Formaci&oacute;n Profesional B&aacute;sica</option>
<option value="RTDS">Radioterapia y dosimetría</option>
<option value="EMSA">Emergencias Sanitarias</option></select>
            </div>
            <div class="form-group col-md-4">
              <label>Tercera opción deseada</label>
              <select class="form-control" name="ciclo_3" required><option value="Defecto">- Selecciona un ciclo -</option>
<option value="LACB3">Laboratorio Cl&iacute;nico y Biom&eacute;dico</option>
<option value="IDMN3">Imagen para el Diagn&oacute;stico y Medicina Nuclear</option>
<option value="DADSA3">Documentaci&oacute;n y Administraci&oacute;n Sanitaria</option>
<option value="EDIN3">Educaci&oacute;n Infantil</option>
<option value="INSO3">Integraci&oacute;n Social</option>
<option value="ADFI3">Administraci&oacute;n y Finanzas</option>
<option value="MAPU3">Marketing y Publicidad</option>
<option value="GA2">Gesti&oacute;n Administrativa</option>
<option value="FAPA2">Farmacia y Parafarmacia</option>
<option value="EN2">Cuidados Auxiliares de Enfermer&iacute;a</option>
<option value="PESD2">Atenci&oacute;n a Personas en Situaci&oacute;n de Dependencia</option>
<option value="FPB">Formaci&oacute;n Profesional B&aacute;sica</option>
<option value="RTDS">Radioterapia y dosimetría</option>
<option value="EMSA">Emergencias Sanitarias</option></select>
            </div>
          </div>

          <div class="form-group">
            <div class="form-check">
              <input class="form-check-input" type="radio" name="radios" id="radio1" value="option1" required>
              <label class="form-check-label" for="radio1">
                He leído, comprendo y acepto los <a href="https://calasanz.eus/politica-de-privacidad/" target="_blank">términos y condiciones del aviso legal</a>
              </label>
            </div>
          </div>

          <div class="form-group text-center">
            <button class="btn btn-lg btn-primary" type="submit">Enviar datos para la lista de espera</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>
