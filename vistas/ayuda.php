<?php
declare(strict_types=1);
require_once __DIR__ . "/../modelos/conectar.php";
require_once __DIR__ . "/../include/Security.php";
Security::initSession();

$role = '';
if (isset($_SESSION['idAdmin'])) {
    $role = 'admin';
} elseif (isset($_SESSION['idProfesor'])) {
    $role = 'profesor';
} elseif (isset($_SESSION['idTutor'])) {
    $role = 'tutor';
} elseif (isset($_SESSION['idSecretaria'])) {
    $role = 'secretaria';
} elseif (isset($_SESSION['idEstudiante'])) {
    $role = 'estudiante';
}

if (!$role) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . "/../include/I18n.php";
$titulo_pagina = "" . __('Help_Center', 'Centro de Ayuda');
$Titulo_Pagina = $Titulo_Pagina;

// Carga la Plantilla de Navegación Correcta Según el Rol Activo
If ($Role === 'Admin') {    $Seccion = 'Ayuda';    Include_Once __Dir__ . "/Admin/Comunes/Nav.php";
} elseif ($role === 'profesor') {
    $seccionActual = 'ayuda';
    include_once __DIR__ . "/profesores/comunes/nav.php";
} elseif ($role === 'tutor') {
    $seccion = 'ayuda';
    include_once __DIR__ . "/tutores/comunes/nav.php";
} elseif ($role === 'secretaria') {
    $seccion = 'ayuda';
    include_once __DIR__ . "/secretaria/comunes/nav.php";
} elseif ($role === 'estudiante') {
    $seccionActual = 'ayuda';
    include_once __DIR__ . "/estudiantes/comunes/nav.php";
}

$lang = I18n::getLang();

// Localization content maps for Help Center & FAQs
$faqData = [
    'es' => [
        'title' => 'Centro de Ayuda y Soporte',
        'subtitle' => 'Encuentra respuestas a tus preguntas y consulta la documentación legal y técnica del centro.',
        'search_placeholder' => 'Buscar en las preguntas frecuentes...',
        'categories' => [
            'auth' => 'Acceso y Autenticación',
            'classroom' => 'Uso del Aula Digital',
            'payments' => 'Facturación y Pagos',
            'security' => 'Seguridad y RGPD',
            'geoblock' => 'Geobloqueo'
        ],
        'questions' => [
            'auth' => [
                [
                    'q' => '¿Cómo inicio sesión utilizando mi cuenta de Google?',
                    'a' => 'Puedes pulsar el botón "Iniciar sesión con Google" en la pantalla de acceso. El sistema buscará si el correo electrónico de tu cuenta de Google coincide exactamente con el correo electrónico registrado en tu ficha de usuario del centro. Si coincide, accederás automáticamente sin necesidad de contraseña.'
                ],
                [
                    'q' => '¿Qué hago si he olvidado mi contraseña?',
                    'a' => 'En la pantalla de login, pulsa en el enlace "¿Olvidaste tu contraseña?". Introduce tu correo electrónico y recibirás un enlace seguro para restablecerla. Por motivos de seguridad (RGPD), el enlace tiene una validez temporal de una hora.'
                ],
                [
                    'q' => '¿Cómo activo el Doble Factor de Autenticación (2FA/MFA)?',
                    'a' => 'Accede a "Mi Perfil" en el menú inferior. En la sección "Seguridad de la Cuenta", pulsa en "Activar 2FA". Se generará un código QR que deberás escanear con una aplicación de autenticación (Google Authenticator, Authy, etc.). Guarda a buen recaudo los códigos de respaldo para recuperar el acceso si pierdes tu dispositivo.'
                ]
            ],
            'classroom' => [
                [
                    'q' => '¿Cómo entrego una tarea en el Aula Digital?',
                    'a' => 'Navega a Aula Digital -> Tareas. Selecciona la tarea correspondiente, pulsa en "Subir entrega" y adjunta tu archivo (PDF, Word, etc.). Una vez subido, el profesor recibirá una notificación para su corrección y recibirás una alerta en cuanto sea calificado.'
                ],
                [
                    'q' => '¿Dónde puedo consultar mis notas?',
                    'a' => 'En el menú "Calificaciones", podrás ver el boletín con todas tus asignaturas, notas de exámenes, notas finales de evaluaciones y el desglose de notas obtenidas en retos y proyectos.'
                ]
            ],
            'payments' => [
                [
                    'q' => '¿Cómo puedo enviar el comprobante de pago de una matrícula?',
                    'a' => 'Ve a la sección "Pagos", selecciona el recibo pendiente y pulsa en "Subir comprobante". Sube la imagen o el documento PDF del pago realizado desde el banco. El personal de secretaría revisará el documento y lo marcará como cobrado una vez verificado.'
                ]
            ],
            'security' => [
                [
                    'q' => '¿Cómo protege la plataforma mis datos personales?',
                    'a' => 'Cumplimos estrictamente con el RGPD y la LOPD-GDD. Los datos altamente confidenciales (como DNI, teléfonos, direcciones físicas y secretos de MFA) se almacenan en la base de datos cifrados con criptografía simétrica avanzada AES-256 utilizando una clave maestra en el servidor. Esto significa que si hubiera una filtración de datos, la información seguiría siendo completamente ilegible.'
                ],
                [
                    'q' => '¿Qué derechos tengo sobre mis datos en el sistema?',
                    'a' => 'Tienes los derechos de acceso, rectificación, supresión ("derecho al olvido"), limitación del tratamiento y portabilidad. Puedes ejercerlos en cualquier momento solicitando la descarga de tu ficha de datos en formato PDF desde el menú inferior de tu perfil, o contactando con el delegado de protección de datos del centro.'
                ]
            ],
            'geoblock' => [
                [
                    'q' => '¿Por qué me aparece un mensaje de geobloqueo al intentar entrar?',
                    'a' => 'Por motivos de ciberseguridad y para prevenir ataques de fuerza bruta internacionales o accesos no autorizados a privilegios elevados, el acceso para roles administrativos (Directores y Secretarías) está geobloqueado por defecto a direcciones IP exclusivamente ubicadas en España. Si necesitas viajar al extranjero, solicita previamente una excepción temporal a la dirección técnica.'
                ]
            ]
        ]
    ],
    'en' => [
        'title' => 'Help Center & Support',
        'subtitle' => 'Find answers to your questions and consult the school\'s legal and technical documentation.',
        'search_placeholder' => 'Search frequently asked questions...',
        'categories' => [
            'auth' => 'Access & Authentication',
            'classroom' => 'Digital Classroom Usage',
            'payments' => 'Billing & Payments',
            'security' => 'Security & GDPR',
            'geoblock' => 'Geoblocking'
        ],
        'questions' => [
            'auth' => [
                [
                    'q' => 'How do I log in using my Google account?',
                    'a' => 'You can press the "Sign in with Google" button on the login screen. The system will check if your Google email matches your registered school email. If it matches, you will be logged in automatically without needing a password.'
                ],
                [
                    'q' => 'What do I do if I forgot my password?',
                    'a' => 'On the login screen, click "Forgot password?". Enter your email and you will receive a secure reset link. For GDPR safety, the link is only valid for one hour.'
                ],
                [
                    'q' => 'How do I enable Two-Factor Authentication (2FA/MFA)?',
                    'a' => 'Go to "My Profile" in the bottom menu. Under "Account Security", click "Enable 2FA". A QR code will be generated for you to scan with an authenticator app (Google Authenticator, Authy, etc.). Make sure to save the backup codes safely.'
                ]
            ],
            'classroom' => [
                [
                    'q' => 'How do I submit an assignment in the Digital Classroom?',
                    'a' => 'Go to Digital Classroom -> Tasks. Select the assignment, click "Upload submission" and attach your file (PDF, Word, etc.). The teacher will be notified to grade it, and you will receive an alert once graded.'
                ],
                [
                    'q' => 'Where can I check my grades?',
                    'a' => 'Under the "Grades" menu, you can check your report card with all your subjects, exam scores, final evaluation notes, and project breakdowns.'
                ]
            ],
            'payments' => [
                [
                    'q' => 'How do I upload a tuition payment receipt?',
                    'a' => 'Go to "Payments", select the pending receipt, and click "Upload receipt". Upload an image or PDF document of your bank transaction. The secretary staff will verify the document and mark it as paid.'
                ]
            ],
            'security' => [
                [
                    'q' => 'How does the platform protect my personal data?',
                    'a' => 'We strictly comply with GDPR and local data protection regulations. Sensitive personal data (such as national ID numbers, phone numbers, home addresses, and 2FA secrets) is stored in the database encrypted with AES-256 symmetric cryptography. If a data leak occurs, the information remains fully unreadable.'
                ],
                [
                    'q' => 'What rights do I have over my data in the system?',
                    'a' => 'You have the right to access, rectify, delete ("right to be forgotten"), restrict treatment, and export your data. You can download a PDF copy of your records from your profile screen, or contact the school\'s Data Protection Officer.'
                ]
            ],
            'geoblock' => [
                [
                    'q' => 'Why do I see a geoblock message when attempting to log in?',
                    'a' => 'For cybersecurity reasons and to prevent international brute-force attacks on administrative panels, access for Admins and Secretaries is geoblocked by default to IP addresses located in Spain. If traveling abroad, please contact technical support for a temporary access exception.'
                ]
            ]
        ]
    ],
    'ca' => [
        'title' => 'Centre d\'Ajuda i Suport',
        'subtitle' => 'Troba respostes a les teves preguntes i consulta la documentació legal i tècnica del centre.',
        'search_placeholder' => 'Cercar a les preguntes freqüents...',
        'categories' => [
            'auth' => 'Accés i Autenticació',
            'classroom' => 'Ús de l\'Aula Digital',
            'payments' => 'Facturació i Pagaments',
            'security' => 'Seguretat i RGPD',
            'geoblock' => 'Geobloqueig'
        ],
        'questions' => [
            'auth' => [
                [
                    'q' => 'Com inicio sessió utilitzant el meu compte de Google?',
                    'a' => 'Pots prémer el botó "Iniciar sessió amb Google" a la pantalla d\'accés. El sistema comprovarà si el correu electrònic del teu compte de Google coincideix exactament amb el registrat al centre. Si és així, accediràs automàticament sense contrasenya.'
                ],
                [
                    'q' => 'Què faig si he oblidat la meva contrasenya?',
                    'a' => 'A la pantalla de login, prem l\'enllaç "Has oblidat la contrasenya?". Introdueix el teu correu i rebràs un enllaç segur. Per motius de seguretat (RGPD), l\'enllaç té una validesa d\'una hora.'
                ],
                [
                    'q' => 'Com activo el Doble Factor d\'Autenticació (2FA/MFA)?',
                    'a' => 'Accedeix a "El meu Perfil" al menú inferior. A la secció "Seguretat del Compte", prem "Activar 2FA". Es generarà un codi QR que hauràs d\'escanejar amb una app (Google Authenticator, Authy, etc.). Guarda els codis de respatller.'
                ]
            ],
            'classroom' => [
                [
                    'q' => 'Com lliuro una tasca a l\'Aula Digital?',
                    'a' => 'Ves a Aula Digital -> Tasques. Selecciona la tasca, prem "Puja el lliurament" i adjunta el teu fitxer. El docent rebrà una notificació i t\'avisarem quan estigui corregida.'
                ],
                [
                    'q' => 'On puc consultar les meves notes?',
                    'a' => 'Al menú "Qualificacions" podries veure el teu butlletí amb assignatures, notes d\'exàmens, avaluacions finals i desglossament de reptes.'
                ]
            ],
            'payments' => [
                [
                    'q' => 'Com puc pujar el comprovant de pagament d\'una matrícula?',
                    'a' => 'Ves a la secció "Pagaments", selecciona el rebut pendent i prem "Penjar comprovant". Puja la imatge o el PDF de la transacció bancària. Secretaria ho revisarà i ho marcarà com a cobrat.'
                ]
            ],
            'security' => [
                [
                    'q' => 'Com protegeix la plataforma les meves dades personals?',
                    'a' => 'Complim estrictament amb el RGPD. Les dades altament confidencials (com ara DNI, telèfons, adreces i secrets 2FA) s\'emmagatzemen xifrades amb criptografia AES-256 amb una clau mestra del servidor. En cas de filtració, la informació roman totalment il·legible.'
                ],
                [
                    'q' => 'Quins drets tinc sobre les meves dades al sistema?',
                    'a' => 'Tens dret a accedir, rectificar, eliminar ("dret a l\'oblit"), limitar el tractament i exportar les teves dades. Pots descarregar un PDF de la teva fitxa des de la pantalla de perfil, o contactar amb el delegat de protecció de dades.'
                ]
            ],
            'geoblock' => [
                [
                    'q' => 'Per què em surt un missatge de geobloqueig en intentar entrar?',
                    'a' => 'Per ciberseguretat i per prevenir atacs internacionals contra panells d\'administració, l\'accés per a administradors i secretaries està geobloquejat per defecte a adreces IP d\'Espanya. Sol·licita una excepció temporal si viatges a l\'estranger.'
                ]
            ]
        ]
    ],
    'eu' => [
        'title' => 'Laguntza eta Babes Zentroa',
        'subtitle' => 'Aurkitu zure galderei erantzunak eta kontsultatu zentroaren legezko eta teknologia dokumentazioa.',
        'search_placeholder' => 'Bilatu maiz egindako galderetan...',
        'categories' => [
            'auth' => 'Sarbidea eta Egiaztapena',
            'classroom' => 'Gela Digitalaren Erabilera',
            'payments' => 'Fakturazioa eta Ordainketak',
            'security' => 'Segurtasuna eta Datuen Babesa (DBLO)',
            'geoblock' => 'Geoblokeoa'
        ],
        'questions' => [
            'auth' => [
                [
                    'q' => 'Nola has dezaket saioa nire Google kontua erabiliz?',
                    'a' => 'Sarbideko pantailan "Googlekin hasi saioa" botoia sakatu dezakezu. Sistemak egiaztatuko du zure Google kontuko posta elektronikoa zentroan erregistratutakoarekin bat datorren. Bat badator, pasahitzik gabe sartuko zara.'
                ],
                [
                    'q' => 'Zer egingo dut pasahitza ahaztu badut?',
                    'a' => 'Login pantailan, sakatu "Pasahitza ahaztu duzu?" esteka. Sartu zure posta elektronikoa eta berrezartzeko esteka jasoko duzu. Segurtasunagatik (DBLO), estekak ordubeteko iraupena du.'
                ],
                [
                    'q' => 'Nola aktibatzen dut Bi Urratseko Egiaztapena (2FA/MFA)?',
                    'a' => 'Joan beheko menuko "Nire Profila" atalera. "Kontuaren Segurtasuna" atalean, sakatu "Aktibatu 2FA". QR kode bat sortuko da eta egiaztapen aplikazio batekin (Google Authenticator, Authy, adibidez) eskaneatu beharko duzu. Gorde babeskopia kodeak.'
                ]
            ],
            'classroom' => [
                [
                    'q' => 'Nola bidaltzen dut lan bat Gela Digitalean?',
                    'a' => 'Joan Gela Digitala -> Lanak atalera. Hautatu lana, sakatu "Bidalketa igo" eta erantsi fitxategia (PDF, adibidez). Irakasleak jakinarazpen bat jasoko du eta zure kalifikazioa prest dagoenean abisatuko dizugu.'
                ],
                [
                    'q' => 'Non kontsulta ditzaket nire kalifikazioak?',
                    'a' => 'Beheko "Kalifikazioak" menuan, ikasgai guztien fitxa, azterketen notak, ebaluazioak eta proiektuetako xehetasunak ikus ditzakezu.'
                ]
            ],
            'payments' => [
                [
                    'q' => 'Nola igo dezaket ordainketa baten egiaztagiria?',
                    'a' => 'Joan "Ordainketak" atalera, hautatu ordaindu gabeko ordainagiria eta sakatu "Igo egiaztagiria". Igo bankuko transakzioaren irudia edo PDF dokumentua. Idazkaritzako langileek egiaztatu eta ordaindutzat markatuko dute.'
                ]
            ],
            'security' => [
                [
                    'q' => 'Nola babesten ditu plataformak nire datu pertsonalak?',
                    'a' => 'Datuak Babesteko Erregelamendu Orokorra (DBEO) zorrotz betetzen dugu. Konfidentzialtasun handiko datuak (DNI, telefonoak, helbideak eta 2FA sekretuak) AES-256 zifratze algoritmoarekin gordetzen dira datu-basean, zerbitzariko gako maisu baten bidez. Datuen ihes bat gertatuko balitz ere, informazioa ezin izango litzateke irakurri.'
                ],
                [
                    'q' => 'Zer eskubide ditut nire datuen gainean sisteman?',
                    'a' => 'Sarbidea, zuzentzea, ezabatzea ("ahaztua izateko eskubidea"), tratamendua mugatzea eta datuen eramangarritasuna eskatzeko eskubideak dituzu. Zure datuen PDF kopia deskarga dezakezu zure profiletik, edo Datuak Babesteko ordezkariarekin harremanetan jarri.'
                ]
            ],
            'geoblock' => [
                [
                    'q' => 'Zergatik agertzen zait geoblokeo mezu bat sartzen saiatzean?',
                    'a' => 'Zibersegurtasun arrazoiengatik eta nazioarteko erasoak saihesteko, administratzaile eta idazkarien sarbidea geobloatuta dago lehenetsi bezala Espainiako IP helbideetara soilik. Atzerrira bidaiatzean, eskatu aldi baterako salbuespena babes teknikariari.'
                ]
            ]
        ]
    ]
];

$activeFaq = $faqData[$lang] ?? $faqData['es'];
?>

<div class="cabecera">
    <div>
        <h1><?= htmlspecialchars($activeFaq['title']) ?></h1>
        <p class="subtitulo-encabezado"><?= htmlspecialchars($activeFaq['subtitle']) ?></p>
    </div>
</div>

<div class="ayuda-container" style="max-width:960px; margin: 0 auto; padding-bottom: 50px;">
    
    <!-- Search Bar -->
    <div class="panel" style="margin-bottom: 24px;">
        <div style="position:relative;">
            <i class="fas fa-search" style="position:absolute; left:16px; top:50%; transform:translateY(-50%); color:#94a3b8;"></i>
            <input type="search" id="faqSearch" placeholder="<?= htmlspecialchars($activeFaq['search_placeholder']) ?>" 
                   autocomplete="one-time-code" autocorrect="off" autocapitalize="off" spellcheck="false"
                   data-lpignore="true" data-1p-ignore="true" data-form-type="other"
                   style="width:100%; padding:14px 14px 14px 46px; border-radius:12px; border:1.5px solid #e2e8f0; font-size:1rem; outline:none; transition:all 0.3s;" />
        </div>
    </div>

    <!-- Category grids -->
    <div class="ayuda-grid" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:20px; margin-bottom: 30px;">
        <?php foreach ($activeFaq['categories'] as $catKey => $catName): ?>
            <div class="panel category-card" data-category="<?= $catKey ?>" style="cursor:pointer; transition:transform 0.2s, box-shadow 0.2s;">
                <div style="display:flex; align-items:center; gap:16px;">
                    <div class="cat-icon-container" style="width:48px; height:48px; border-radius:10px; display:flex; align-items:center; justify-content:center; background:var(--azul-suave); color:var(--azul);">
                        <?php if ($catKey === 'auth'): ?>
                            <i class="fas fa-key" style="font-size:1.25rem;"></i>
                        <?php elseif ($catKey === 'classroom'): ?>
                            <i class="fas fa-graduation-cap" style="font-size:1.25rem;"></i>
                        <?php elseif ($catKey === 'payments'): ?>
                            <i class="fas fa-credit-card" style="font-size:1.25rem;"></i>
                        <?php elseif ($catKey === 'security'): ?>
                            <i class="fas fa-shield-halved" style="font-size:1.25rem;"></i>
                        <?php elseif ($catKey === 'geoblock'): ?>
                            <i class="fas fa-earth-europe" style="font-size:1.25rem;"></i>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h4 style="margin:0; font-size:1.05rem; font-weight:700; color:var(--text);"><?= htmlspecialchars($catName) ?></h4>
                        <span style="font-size:.8rem; color:#64748b;"><?= count($activeFaq['questions'][$catKey] ?? []) ?> <?= $lang === 'en' ? 'questions' : 'preguntas' ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- FAQ Accordion Sections -->
    <?php foreach ($activeFaq['categories'] as $catKey => $catName): ?>
        <div class="faq-category-section" id="section-<?= $catKey ?>" style="margin-bottom: 32px;">
            <h3 style="font-size:1.3rem; font-weight:800; color:var(--text); margin-bottom:16px; display:flex; align-items:center; gap:8px;">
                <?php if ($catKey === 'auth'): ?>
                    <i class="fas fa-key" style="color:var(--azul);"></i>
                <?php elseif ($catKey === 'classroom'): ?>
                    <i class="fas fa-graduation-cap" style="color:var(--azul);"></i>
                <?php elseif ($catKey === 'payments'): ?>
                    <i class="fas fa-credit-card" style="color:var(--azul);"></i>
                <?php elseif ($catKey === 'security'): ?>
                    <i class="fas fa-shield-halved" style="color:var(--azul);"></i>
                <?php elseif ($catKey === 'geoblock'): ?>
                    <i class="fas fa-earth-europe" style="color:var(--azul);"></i>
                <?php endif; ?>
                <?= htmlspecialchars($catName) ?>
            </h3>
            
            <div style="display:flex; flex-direction:column; gap:12px;">
                <?php foreach ($activeFaq['questions'][$catKey] ?? [] as $index => $faq): ?>
                    <div class="panel faq-item" style="padding:0; overflow:hidden; border-radius:12px; border:1px solid #e2e8f0; transition:all 0.3s;">
                        <button class="faq-trigger" style="width:100%; text-align:left; background:none; border:none; padding:18px 24px; display:flex; align-items:center; justify-content:space-between; cursor:pointer; outline:none;">
                            <span class="faq-question" style="font-weight:600; font-size:1rem; color:var(--text); padding-right:16px;"><?= htmlspecialchars($faq['q']) ?></span>
                            <i class="fas fa-chevron-down faq-icon" style="color:#94a3b8; transition:transform 0.3s;"></i>
                        </button>
                        <div class="faq-content" style="max-height:0; overflow:hidden; transition:max-height 0.3s ease-out; background:#f8fafc; border-top: 0px solid #e2e8f0;">
                            <div style="padding:20px 24px; font-size:.95rem; line-height:1.6; color:#475569; border-top: 1px solid #f1f5f9;">
                                <?= nl2br(htmlspecialchars($faq['a'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>

</div>

<style>
    .category-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    }
    .faq-item.active {
        border-color: var(--azul) !important;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.05);
    }
    .faq-item.active .faq-icon {
        transform: rotate(180deg);
        color: var(--azul);
    }
    .faq-item.active .faq-content {
        /* max-height loaded via JS */
    }
</style>

<script>
$(function() {
    // Accordion mechanism
    $('.faq-trigger').on('click', function() {
        var item = $(this).closest('.faq-item');
        var content = item.find('.faq-content');
        var icon = item.find('.faq-icon');
        
        if (item.hasClass('active')) {
            content.css('max-height', '0');
            item.removeClass('active');
        } else {
            // Close other items in the same section
            item.siblings('.active').each(function() {
                $(this).removeClass('active');
                $(this).find('.faq-content').css('max-height', '0');
            });
            content.css('max-height', content[0].scrollHeight + 'px');
            item.addClass('active');
        }
    });

    // Category Card click behavior (Smooth scroll to section)
    $('.category-card').on('click', function() {
        var cat = $(this).data('category');
        var targetSection = $('#section-' + cat);
        if (targetSection.length) {
            $('html, body').animate({
                scrollTop: targetSection.offset().top - 90
            }, 600);
        }
    });

    // Live search FAQs
    $('#faqSearch').on('input', function() {
        var query = $(this).val().toLowerCase().trim();
        
        if (query === '') {
            $('.faq-item').show();
            $('.faq-category-section').show();
            return;
        }

        $('.faq-category-section').each(function() {
            var section = $(this);
            var matchesInSection = 0;

            section.find('.faq-item').each(function() {
                var item = $(this);
                var question = item.find('.faq-question').text().toLowerCase();
                var answer = item.find('.faq-content').text().toLowerCase();

                if (question.indexOf(query) !== -1 || answer.indexOf(query) !== -1) {
                    item.show();
                    matchesInSection++;
                } else {
                    item.hide();
                    // close if open
                    if (item.hasClass('active')) {
                        item.removeClass('active');
                        item.find('.faq-content').css('max-height', '0');
                    }
                }
            });

            if (matchesInSection > 0) {
                section.show();
            } else {
                section.hide();
            }
        });
    });
});
</script>

<?php
// Carga el footer correcto
if ($role === 'admin') {
    include_once __DIR__ . "/admin/comunes/footer.php";
} elseif ($role === 'profesor') {
    include_once __DIR__ . "/profesores/comunes/footer.php";
} elseif ($role === 'tutor') {
    include_once __DIR__ . "/tutores/comunes/footer.php";
} elseif ($role === 'secretaria') {
    include_once __DIR__ . "/secretaria/comunes/footer.php";
} elseif ($role === 'estudiante') {
    include_once __DIR__ . "/estudiantes/comunes/footer.php";
}
?>
