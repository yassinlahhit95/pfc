import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:shared_preferences/shared_preferences.dart';

class LocaleNotifier extends StateNotifier<String> {
  LocaleNotifier() : super('es') {
    _loadLocale();
  }

  Future<void> _loadLocale() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final saved = prefs.getString('app_locale');
      if (saved != null) {
        state = saved;
      }
    } catch (_) {}
  }

  Future<void> setLocale(String locale) async {
    if (['es', 'en', 'ca', 'eu'].contains(locale)) {
      state = locale;
      try {
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('app_locale', locale);
      } catch (_) {}
    }
  }
}

final localeProvider = StateNotifierProvider<LocaleNotifier, String>((ref) {
  return LocaleNotifier();
});

final translationsProvider = Provider<Map<String, String>>((ref) {
  final locale = ref.watch(localeProvider);
  return _translations[locale] ?? _translations['es']!;
});

class FAQItem {
  final String question;
  final String answer;
  const FAQItem(this.question, this.answer);
}

final faqsProvider = Provider<Map<String, List<FAQItem>>>((ref) {
  final locale = ref.watch(localeProvider);
  return _faqs[locale] ?? _faqs['es']!;
});

const _translations = {
  'es': {
    'nav_alumnos': 'Alumnos',
    'nav_alumnos_sub': 'Gestión de estudiantes',
    'nav_asistencias_centro': 'Asistencia Centro',
    'nav_asistencias_centro_sub': 'Control general',
    'nav_profesores': 'Profesores',
    'nav_profesores_sub': 'Claustro docente',
    'nav_secretarias': 'Secretarías',
    'nav_secretarias_sub': 'Gestión de personal',
    'greeting_morning': 'Buenos días',
    'greeting_afternoon': 'Buenas tardes',
    'greeting_evening': 'Buenas noches',
    'nav_inventario': 'Inventario',
    'nav_inventario_sub': 'Material y dispositivos',

    'profile': 'Mi Perfil',
    'personal_data': 'Datos Personales',
    'settings': 'Ajustes',
    'language': 'Idioma',
    'select_language': 'Seleccionar Idioma',
    'help_center': 'Centro de Ayuda',
    'faqs': 'Preguntas Frecuentes',
    'logout': 'Cerrar Sesión',
    'spanish': 'Español',
    'basque': 'Euskera',
    'catalan': 'Catalán',
    'english': 'Inglés',
    'email': 'Correo electrónico',
    'phone': 'Teléfono',
    'dni': 'DNI / Identificación',
    'active': 'Activo',
    'inactive': 'Inactivo',
    'cancel': 'Cancelar',
    'confirm': 'Confirmar',
    'security_desc': 'Seguridad y RGPD local',
    'school_center': 'AulaPro Centro escolar',
      'nav_horario': 'Horario',
    'nav_horario_sub': 'Ver calendario de clases',
    'nav_notas': 'Notas',
    'nav_notas_sub': 'Consultar calificaciones',
    'nav_aula': 'Aula digital',
    'nav_aula_sub': 'Temas, recursos y sesiones',
    'nav_tareas': 'Tareas',
    'nav_tareas_sub': 'Todos tus deberes y entregas',
    'nav_retos': 'Retos',
    'nav_retos_sub': 'Desafíos con material extra',
    'nav_favoritos': 'Favoritos',
    'nav_favoritos_sub': 'Tus archivos guardados',
    'nav_asistencias': 'Asistencias',
    'nav_asistencias_sub': 'Registro de faltas y asistencia',
    'nav_anuncios': 'Anuncios',
    'nav_anuncios_sub': 'Comunicados oficiales y avisos',
    'nav_mensajeria': 'Mensajería',
    'nav_mensajeria_sub': 'Bandeja de entrada y reclamaciones',
    'nav_eventos': 'Eventos',
    'nav_eventos_sub': 'Próximas actividades y fechas',
    'nav_pagos': 'Pagos',
    'nav_pagos_sub': 'Ver recibos y cobros',
    'nav_gastos': 'Gastos',
    'nav_gastos_sub': 'Control de compras y recibos',
    'nav_justificar': 'Justificar Faltas',
    'nav_justificar_sub': 'Gestión de ausencias',
    'section_academico': 'Académico',
    'section_centro': 'Centro',
    'section_gestion': 'Gestión',
    'metric_media': 'Media',
    'metric_tareas': 'Tareas',
    'metric_faltas': 'Faltas',
    'metric_corregir': 'Por Corregir',
    'metric_tutoria': 'Aula Tutoría',
    'metric_ver': 'Ver',
    'metric_estudiantes': 'Estudiantes',
    'metric_profesores': 'Profesores',
    'metric_pagos': 'Pagos',
    'metric_gastos': 'Gastos',
    'metric_hijo': 'Hijo',
    'metric_recibos': 'Recibos',
    'metric_al_dia': 'Al día',
},
  'en': {
    'nav_alumnos': 'Students',
    'nav_alumnos_sub': 'Student management',
    'nav_asistencias_centro': 'Center Attendance',
    'nav_asistencias_centro_sub': 'General control',
    'nav_profesores': 'Teachers',
    'nav_profesores_sub': 'Teaching staff',
    'nav_secretarias': 'Secretaries',
    'nav_secretarias_sub': 'Staff management',
    'greeting_morning': 'Good morning',
    'greeting_afternoon': 'Good afternoon',
    'greeting_evening': 'Good evening',
    'nav_inventario': 'Inventory',
    'nav_inventario_sub': 'Material and devices',

    'profile': 'My Profile',
    'personal_data': 'Personal Data',
    'settings': 'Settings',
    'language': 'Language',
    'select_language': 'Select Language',
    'help_center': 'Help Center',
    'faqs': 'FAQs',
    'logout': 'Log Out',
    'spanish': 'Spanish',
    'basque': 'Basque',
    'catalan': 'Catalan',
    'english': 'English',
    'email': 'Email address',
    'phone': 'Phone number',
    'dni': 'National ID (DNI)',
    'active': 'Active',
    'inactive': 'Inactive',
    'cancel': 'Cancel',
    'confirm': 'Confirm',
    'security_desc': 'Security & Local GDPR',
    'school_center': 'AulaPro School Center',
      'nav_horario': 'Schedule',
    'nav_horario_sub': 'View class calendar',
    'nav_notas': 'Grades',
    'nav_notas_sub': 'Check grades',
    'nav_aula': 'Digital Classroom',
    'nav_aula_sub': 'Topics, resources and sessions',
    'nav_tareas': 'Tasks',
    'nav_tareas_sub': 'All your homework and assignments',
    'nav_retos': 'Challenges',
    'nav_retos_sub': 'Challenges with extra material',
    'nav_favoritos': 'Favorites',
    'nav_favoritos_sub': 'Your saved files',
    'nav_asistencias': 'Attendance',
    'nav_asistencias_sub': 'Attendance record',
    'nav_anuncios': 'Announcements',
    'nav_anuncios_sub': 'Official communications and notices',
    'nav_mensajeria': 'Messages',
    'nav_mensajeria_sub': 'Inbox and claims',
    'nav_eventos': 'Events',
    'nav_eventos_sub': 'Upcoming activities and dates',
    'nav_pagos': 'Payments',
    'nav_pagos_sub': 'View receipts and charges',
    'nav_gastos': 'Expenses',
    'nav_gastos_sub': 'Control of purchases and receipts',
    'nav_justificar': 'Justify Absences',
    'nav_justificar_sub': 'Absence management',
    'section_academico': 'Academic',
    'section_centro': 'Center',
    'section_gestion': 'Management',
    'metric_media': 'Average',
    'metric_tareas': 'Tasks',
    'metric_faltas': 'Absences',
    'metric_corregir': 'To Grade',
    'metric_tutoria': 'Tutoring Room',
    'metric_ver': 'View',
    'metric_estudiantes': 'Students',
    'metric_profesores': 'Teachers',
    'metric_pagos': 'Payments',
    'metric_gastos': 'Expenses',
    'metric_hijo': 'Child',
    'metric_recibos': 'Receipts',
    'metric_al_dia': 'Up to date',
},
  'ca': {
    'nav_alumnos': 'Alumnes',
    'nav_alumnos_sub': 'Gestió d\'estudiants',
    'nav_asistencias_centro': 'Assistència Centre',
    'nav_asistencias_centro_sub': 'Control general',
    'nav_profesores': 'Professorat',
    'nav_profesores_sub': 'Claustre docent',
    'nav_secretarias': 'Secretaries',
    'nav_secretarias_sub': 'Gestió de personal',
    'greeting_morning': 'Bon dia',
    'greeting_afternoon': 'Bona tarda',
    'greeting_evening': 'Bona nit',
    'nav_inventario': 'Inventari',
    'nav_inventario_sub': 'Material i dispositius',

    'profile': 'El meu Perfil',
    'personal_data': 'Dades Personals',
    'settings': 'Ajustos',
    'language': 'Idioma',
    'select_language': 'Seleccionar Idioma',
    'help_center': 'Centre d\'Ajuda',
    'faqs': 'Preguntes Freqüents',
    'logout': 'Tancar Sessió',
    'spanish': 'Castellà',
    'basque': 'Basc',
    'catalan': 'Català',
    'english': 'Anglès',
    'email': 'Correu electrònic',
    'phone': 'Telèfon',
    'dni': 'DNI / Identificació',
    'active': 'Actiu',
    'inactive': 'Inactiu',
    'cancel': 'Cancel·lar',
    'confirm': 'Confirmar',
    'security_desc': 'Seguretat i RGPD local',
    'school_center': 'AulaPro Centre escolar',
      'nav_horario': 'Horari',
    'nav_horario_sub': 'Veure calendari de classes',
    'nav_notas': 'Notes',
    'nav_notas_sub': 'Consultar qualificacions',
    'nav_aula': 'Aula digital',
    'nav_aula_sub': 'Temes, recursos i sessions',
    'nav_tareas': 'Tasques',
    'nav_tareas_sub': 'Tots els teus deures i lliuraments',
    'nav_retos': 'Reptes',
    'nav_retos_sub': 'Desafiaments amb material extra',
    'nav_favoritos': 'Favorits',
    'nav_favoritos_sub': 'Els teus arxius desats',
    'nav_asistencias': 'Assistències',
    'nav_asistencias_sub': 'Registre de faltes i assistència',
    'nav_anuncios': 'Anuncis',
    'nav_anuncios_sub': 'Comunicats oficials i avisos',
    'nav_mensajeria': 'Missatgeria',
    'nav_mensajeria_sub': 'Safata d\'entrada i reclamacions',
    'nav_eventos': 'Esdeveniments',
    'nav_eventos_sub': 'Properes activitats i dates',
    'nav_pagos': 'Pagaments',
    'nav_pagos_sub': 'Veure rebuts i cobraments',
    'nav_gastos': 'Despeses',
    'nav_gastos_sub': 'Control de compres i rebuts',
    'nav_justificar': 'Justificar Faltes',
    'nav_justificar_sub': 'Gestió d\'absències',
    'section_academico': 'Acadèmic',
    'section_centro': 'Centre',
    'section_gestion': 'Gestió',
    'metric_media': 'Mitjana',
    'metric_tareas': 'Tasques',
    'metric_faltas': 'Faltes',
    'metric_corregir': 'Per Corregir',
    'metric_tutoria': 'Aula Tutoria',
    'metric_ver': 'Veure',
    'metric_estudiantes': 'Estudiants',
    'metric_profesores': 'Professors',
    'metric_pagos': 'Pagaments',
    'metric_gastos': 'Despeses',
    'metric_hijo': 'Fill',
    'metric_recibos': 'Rebuts',
    'metric_al_dia': 'Al dia',
},
  'eu': {
    'nav_alumnos': 'Ikasleak',
    'nav_alumnos_sub': 'Ikasleen kudeaketa',
    'nav_asistencias_centro': 'Zentroko Asistentzia',
    'nav_asistencias_centro_sub': 'Kontrol orokorra',
    'nav_profesores': 'Irakasleak',
    'nav_profesores_sub': 'Irakasle taldea',
    'nav_secretarias': 'Idazkariak',
    'nav_secretarias_sub': 'Langileen kudeaketa',
    'greeting_morning': 'Egun on',
    'greeting_afternoon': 'Arratsalde on',
    'greeting_evening': 'Gabon',
    'nav_inventario': 'Inbentarioa',
    'nav_inventario_sub': 'Materiala eta gailuak',

    'profile': 'Nire Profila',
    'personal_data': 'Datu Pertsonalak',
    'settings': 'Ezarpenak',
    'language': 'Hizkuntza',
    'select_language': 'Hautatu Hizkuntza',
    'help_center': 'Laguntza Zentroa',
    'faqs': 'Maiz Egindako Galderak',
    'logout': 'Saioa Itxi',
    'spanish': 'Gaztelania',
    'basque': 'Euskara',
    'catalan': 'Katalana',
    'english': 'Ingelesa',
    'email': 'E-posta helbidea',
    'phone': 'Telefonoa',
    'dni': 'NAN / Identifikazioa',
    'active': 'Aktibo',
    'inactive': 'Inaktibo',
    'cancel': 'Utzi',
    'confirm': 'Berretsi',
    'security_desc': 'Segurtasuna eta DBLO',
    'school_center': 'AulaPro Ikastetxea',
      'nav_horario': 'Ordutegia',
    'nav_horario_sub': 'Ikusi klaseen egutegia',
    'nav_notas': 'Notak',
    'nav_notas_sub': 'Kontsultatu kalifikazioak',
    'nav_aula': 'Ikasgela digitala',
    'nav_aula_sub': 'Gaiak, baliabideak eta saioak',
    'nav_tareas': 'Zereginak',
    'nav_tareas_sub': 'Zure lan eta bidalketa guztiak',
    'nav_retos': 'Erronkak',
    'nav_retos_sub': 'Material gehigarriko erronkak',
    'nav_favoritos': 'Gogokoak',
    'nav_favoritos_sub': 'Zure fitxategi gordeak',
    'nav_asistencias': 'Asistentziak',
    'nav_asistencias_sub': 'Falten eta asistentziaren erregistroa',
    'nav_anuncios': 'Iragarkiak',
    'nav_anuncios_sub': 'Komunikatu ofizialak eta oharrak',
    'nav_mensajeria': 'Mezularitza',
    'nav_mensajeria_sub': 'Sarrera-ontzia eta erreklamazioak',
    'nav_eventos': 'Ekitaldiak',
    'nav_eventos_sub': 'Datozen jarduerak eta datak',
    'nav_pagos': 'Ordainketak',
    'nav_pagos_sub': 'Ikusi ordainagiriak eta kobrantzak',
    'nav_gastos': 'Gastuak',
    'nav_gastos_sub': 'Erosketen eta ordainagirien kontrola',
    'nav_justificar': 'Faltak Justifikatu',
    'nav_justificar_sub': 'Absentzien kudeaketa',
    'section_academico': 'Akademikoa',
    'section_centro': 'Zentroa',
    'section_gestion': 'Kudeaketa',
    'metric_media': 'Batez bestekoa',
    'metric_tareas': 'Zereginak',
    'metric_faltas': 'Faltak',
    'metric_corregir': 'Zuzentzeko',
    'metric_tutoria': 'Tutoretza Gela',
    'metric_ver': 'Ikusi',
    'metric_estudiantes': 'Ikasleak',
    'metric_profesores': 'Irakasleak',
    'metric_pagos': 'Ordainketak',
    'metric_gastos': 'Gastuak',
    'metric_hijo': 'Seme-alaba',
    'metric_recibos': 'Ordainagiriak',
    'metric_al_dia': 'Egunean',
  }
};

const _faqs = {
  'es': {

    'Acceso y Autenticación': [
      FAQItem(
        '¿Cómo inicio sesión con Google?',
        'Si tu correo registrado en el centro coincide con tu cuenta de Google, puedes iniciar sesión instantáneamente pulsando "Iniciar sesión con Google".',
      ),
      FAQItem(
        '¿Cómo restablezco mi contraseña?',
        'En la pantalla de login, pulsa en "¿Olvidaste tu contraseña?" para recibir un enlace de recuperación.',
      ),
    ],
    'Seguridad y Privacidad': [
      FAQItem(
        '¿Cómo se protegen mis datos?',
        'Tus datos sensibles se almacenan en servidores seguros dentro de la UE y se cifran mediante algoritmos avanzados AES-256.',
      ),
    ],
},
  'en': {

    'Access & Authentication': [
      FAQItem(
        'How do I log in with Google?',
        'If your registered school email matches your Google account, you can sign in instantly by pressing the Google Login button.',
      ),
      FAQItem(
        'How can I reset my password?',
        'Click "Forgot password?" on the login screen to receive a secure recovery email link.',
      ),
    ],
    'Security & Privacy': [
      FAQItem(
        'How is my data protected?',
        'Your sensitive data is stored on secure EU-based servers and is encrypted using advanced AES-256 algorithms.',
      ),
    ],
  },
  'ca': {

    'Accés i Autenticació': [
      FAQItem(
        'Com inicio sessió amb Google?',
        'Si el teu correu registrat al centre coincideix amb el teu compte de Google, pots iniciar sessió instantàniament prement "Iniciar sessió amb Google".',
      ),
      FAQItem(
        'Com restableixo la contrasenya?',
        'A la pantalla de login, prem sobre "Has oblidat la contrasenya?" per rebre un enllaç de recuperació.',
      ),
    ],
    'Seguretat i Privacitat': [
      FAQItem(
        'Com es protegeixen les meves dades?',
        'Les teves dades sensibles s\'emmagatzemen en servidors segurs dins de la UE i es xifren mitjançant algoritmes avançats AES-256.',
      ),
    ],
  },
  'eu': {

    'Sarbidea eta Egiaztapena': [
      FAQItem(
        'Nola has dezaket saioa Googlekin?',
        'Zentroan erregistratutako e-posta Googleko kontuarekin bat badator, saioa has dezakezu "Googlekin hasi saioa" sakatuta.',
      ),
      FAQItem(
        'Nola berrezar dezaket pasahitza?',
        'Saio-hasiera pantailan, sakatu "Pasahitza ahaztu duzu?" estekan berreskurapen helbidea jasotzeko.',
      ),
    ],
    'Segurtasuna eta Datuen Babesa': [
      FAQItem(
        'Nola babesten dira nire datuak?',
        'Zure datu pertsonalak EBko zerbitzari seguruetan gordetzen dira eta AES-256 algoritmoen bidez zifratuta daude.',
      ),
    ],
  }
};
