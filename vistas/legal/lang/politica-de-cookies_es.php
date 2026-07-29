<div class="legal-hero">
    <h1><i class="fas fa-cookie-bite" style="margin-right:10px;opacity:.9;"></i><?= __('Política de Cookies', 'Política de Cookies') ?></h1>
    <span class="badge">Directiva ePrivacy · RGPD · Art. 22 LSSI-CE</span>
</div>

<main class="legal-main">

    <div class="legal-toc">
        <h2><i class="fas fa-list-ul" style="margin-right:6px;"></i> <?= __('Contenido', 'Contenido') ?></h2>
        <ol>
            <li><a href="#que-son">¿Qué son las cookies?</a></li>
            <li><a href="#tipos">Tipos de cookies</a></li>
            <li><a href="#tabla">Cookies utilizadas</a></li>
            <li><a href="#terceros">Cookies de terceros</a></li>
            <li><a href="#gestion">Gestión y desactivación</a></li>
            <li><a href="#actualizacion">Actualización</a></li>
        </ol>
    </div>

    <section class="legal-section" id="que-son">
        <h2><i class="fas fa-question-circle"></i> 1. ¿Qué son las Cookies?</h2>
        <p>Las <strong>cookies</strong> son pequeños archivos de texto que los sitios web almacenan en el dispositivo del usuario para recordar información entre visitas. Pueden ser cookies de <em>sesión</em> o <em>persistentes</em>.</p>
        <p>De acuerdo con la legislación vigente, las cookies que no sean estrictamente necesarias para el funcionamiento del servicio requieren el consentimiento previo e informado del usuario.</p>
        <div class="legal-info-box">
            <i class="fas fa-info-circle"></i>
            <span>Esta plataforma es de <strong>acceso restringido a usuarios autorizados</strong>. Al iniciar sesión, aceptas el uso de las cookies estrictamente necesarias para garantizar el funcionamiento del servicio.</span>
        </div>
    </section>

    <section class="legal-section" id="tipos">
        <h2><i class="fas fa-tags"></i> 2. Tipos de Cookies</h2>
        <ul>
            <li><strong>Técnicas / Necesarias:</strong> Imprescindibles para el funcionamiento de la plataforma (autenticación, seguridad). No requieren consentimiento.</li>
            <li><strong>De preferencias / Funcionales:</strong> Permiten recordar configuraciones del usuario (idioma, tema). Requieren consentimiento.</li>
            <li><strong>Analíticas / De medición:</strong> Recogen información estadística de uso para mejorar la plataforma. Requieren consentimiento.</li>
            <li><strong>De marketing / Publicidad:</strong> Esta plataforma <strong>NO utiliza cookies de marketing</strong>.</li>
        </ul>
    </section>

    <section class="legal-section" id="tabla">
        <h2><i class="fas fa-table"></i> 3. Cookies Utilizadas en esta Plataforma</h2>
        <p>A continuación se detallan las cookies que utiliza esta plataforma:</p>
        <div class="cookies-table-wrap">
            <table class="cookies-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Tipo</th>
                        <th>Duración</th>
                        <th>Finalidad</th>
                        <th>Origen</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>PHPSESSID</code></td>
                        <td><span class="cookie-tipo necesaria">Necesaria</span></td>
                        <td>Sesión</td>
                        <td>Identifica la sesión del usuario autenticado. Sin esta cookie la plataforma no funciona.</td>
                        <td>Propia</td>
                    </tr>
                    <tr>
                        <td><code>csrf_token</code> (sesión)</td>
                        <td><span class="cookie-tipo necesaria">Necesaria</span></td>
                        <td>Sesión</td>
                        <td>Token de seguridad para prevenir ataques CSRF.</td>
                        <td>Propia</td>
                    </tr>
                    <tr>
                        <td><code>_fg_ts</code>, <code>_fg_data</code> (sesión)</td>
                        <td><span class="cookie-tipo funcional">Funcional</span></td>
                        <td>5 min (caché)</td>
                        <td>Caché de configuración de módulos activos para evitar consultas repetidas.</td>
                        <td>Propia</td>
                    </tr>
                    <tr>
                        <td><code>lang</code> (preferencia)</td>
                        <td><span class="cookie-tipo funcional">Funcional</span></td>
                        <td>1 año</td>
                        <td>Guarda la preferencia de idioma seleccionada por el usuario (Español, Euskera, Catalán, Inglés).</td>
                        <td>Propia</td>
                    </tr>
                    <tr>
                        <td>Firebase / FCM</td>
                        <td><span class="cookie-tipo funcional">Funcional</span></td>
                        <td>Persistente</td>
                        <td>Token de dispositivo para el envío de notificaciones push en tiempo real. Gestionado por Google LLC.</td>
                        <td>Tercero (Google)</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p style="font-size:.82rem;color:var(--mut);margin-top:8px;"><i class="fas fa-info-circle"></i> Esta lista se actualiza cuando se añaden nuevas funcionalidades. Fecha de revisión: <?= date('d/m/Y') ?></p>
    </section>

    <section class="legal-section" id="terceros">
        <h2><i class="fas fa-globe"></i> 4. Cookies de Terceros</h2>
        <p>Esta plataforma utiliza servicios de terceros que pueden instalar sus propias cookies:</p>
        <ul>
            <li><a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer">Política de privacidad de Google</a></li>
            <li><a href="https://firebase.google.com/support/privacy" target="_blank" rel="noopener noreferrer">Privacidad de Firebase</a></li>
        </ul>
        <div class="legal-info-box rojo">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Esta plataforma <strong>no utiliza Google Analytics, Meta Pixel, ni ninguna tecnología de seguimiento publicitario</strong>.</span>
        </div>
    </section>

    <section class="legal-section" id="gestion">
        <h2><i class="fas fa-sliders"></i> 5. Gestión y Desactivación de Cookies</h2>
        <p>Puedes además gestionar o eliminar las cookies desde la configuración de tu navegador. Ten en cuenta que deshabilitar las <strong>cookies necesarias</strong> impedirá el funcionamiento correcto de la plataforma y no podrás iniciar sesión.</p>
    </section>

    <section class="legal-section" id="actualizacion">
        <h2><i class="fas fa-history"></i> 6. Actualización de esta Política</h2>
        <p>Esta política de cookies puede modificarse cuando se actualicen las funcionalidades de la plataforma o cambie la normativa aplicable.</p>
        <p style="color:var(--mut);font-size:.85rem;margin-top:16px;">Última actualización: <?= date('d/m/Y') ?></p>
    </section>

</main>
