<div class="legal-hero">
    <h1><i class="fas fa-scale-balanced" style="margin-right:10px;opacity:.9;"></i><?= __('Aviso Legal', 'Avís Legal') ?></h1>
    <span class="badge">LSSI-CE · Art. 10</span>
</div>

<main class="legal-main">

    <div class="legal-toc">
        <h2><i class="fas fa-list-ul" style="margin-right:6px;"></i> <?= __('Contenido', 'Contingut') ?></h2>
        <ol>
            <li><a href="#titular">Identificació del titular</a></li>
            <li><a href="#uso">Condicions d'ús</a></li>
            <li><a href="#propiedad">Propietat intel·lectual</a></li>
            <li><a href="#responsabilidad">Responsabilitat</a></li>
            <li><a href="#enlaces">Política d'enllaços</a></li>
            <li><a href="#ley">Llei aplicable</a></li>
        </ol>
    </div>

    <section class="legal-section" id="titular">
        <h2><i class="fas fa-building"></i> 1. Identificació del Titular</h2>
        <p>En compliment de l'article 10 de la <strong>Llei 34/2002, d'11 de juliol, de Serveis de la Societat de la Informació i del Comerç Electrònic (LSSI-CE)</strong>, s'informa de les següents dades:</p>
        <ul>
            <li><strong>Denominació:</strong> <?= htmlspecialchars($nombreCentro) ?></li>
            <li><strong>NIF/CIF:</strong> [Introdueix aquí el NIF o CIF del centre]</li>
            <li><strong>Domicili:</strong> <?= htmlspecialchars(trim(($cfg['direccionCentro'] ?? '') . ' ' . ($cfg['ciudadCentro'] ?? '') . ' ' . ($cfg['cpCentro'] ?? ''))) ?: "[Introdueix aquí l'adreça del centre]" ?></li>
            <li><strong>Telèfon:</strong> <?= !empty($cfg['telefonoCentro']) ? htmlspecialchars($cfg['telefonoCentro']) : '[Telèfon del centre]' ?></li>
            <li><strong>Correu electrònic:</strong> <?= !empty($emailCentro) ? '<a href="mailto:' . htmlspecialchars($emailCentro) . '">' . htmlspecialchars($emailCentro) . '</a>' : '[Email del centre]' ?></li>
            <li><strong>Activitat principal:</strong> Centre de formació professional i gestió educativa.</li>
            <li><strong>Plataforma tecnològica:</strong> <?= htmlspecialchars(FeatureGuard::getCenterName()) ?> — sistema de gestió escolar desenvolupat per <?= htmlspecialchars(FeatureGuard::getCenterName()) ?> SaaS.</li>
        </ul>
    </section>

    <section class="legal-section" id="uso">
        <h2><i class="fas fa-laptop"></i> 2. Condicions d'Ús</h2>
        <p>L'accés i ús d'aquesta plataforma atribueix la condició d'<strong>usuari</strong> (en endavant, «l'Usuari») i implica l'acceptació plena i sense reserves de les presents condicions d'ús.</p>
        <h3>Accés restringit</h3>
        <p>Aquesta plataforma està <strong>destinada exclusivament a usuaris autoritzats</strong> del centre: administradors, docents, estudiants i tutors legals. L'accés no autoritzat està prohibit i podrà ser objecte de les accions legals corresponents.</p>
        <h3>Ús correcte</h3>
        <p>L'Usuari es compromet a:</p>
        <ul>
            <li>No introduir dades falses o de terceres persones sense el seu consentiment.</li>
            <li>No realitzar activitats que danyin, interrompin o sobrecarreguin la plataforma.</li>
            <li>Mantenir la confidencialitat de les seves credencials d'accés.</li>
            <li>No utilitzar la plataforma per a fins il·lícits o contraris a l'ordre públic.</li>
        </ul>
    </section>

    <section class="legal-section" id="propiedad">
        <h2><i class="fas fa-copyright"></i> 3. Propietat Intel·lectual i Industrial</h2>
        <p>Tots els elements d'aquesta plataforma — incloent-hi codi, disseny, marques, logotips, textos i imatges — estan protegits per les lleis de <strong>propietat intel·lectual i industrial</strong> espanyoles i de la Unió Europea.</p>
        <p>Queda expressament prohibida la reproducció o comunicació pública de qualsevol element de la plataforma sense autorització escrita.</p>
    </section>

    <section class="legal-section" id="responsabilidad">
        <h2><i class="fas fa-shield-halved"></i> 4. Responsabilitat i Garanties</h2>
        <p>El centre no garanteix la disponibilitat ininterrompuda de la plataforma. Les dades acadèmiques tenen caràcter informatiu i el document oficial en paper del centre preval en cas de discrepància.</p>
        <div class="legal-info-box">
            <i class="fas fa-info-circle"></i>
            <span>En cas de detectar contingut inapropiat, pot notificar-ho al correu: <strong><?= !empty($emailCentro) ? htmlspecialchars($emailCentro) : '[email del centre]' ?></strong></span>
        </div>
    </section>

    <section class="legal-section" id="enlaces">
        <h2><i class="fas fa-link"></i> 5. Política d'Enllaços</h2>
        <p>La plataforma pot contenir enllaços a llocs web de tercers. El centre no assumeix responsabilitat pels continguts, privacitat o disponibilitat de tals llocs externs.</p>
    </section>

    <section class="legal-section" id="ley">
        <h2><i class="fas fa-gavel"></i> 6. Llei Aplicable i Jurisdicció</h2>
        <p>Les presents condicions es regeixen per la <strong>legislació espanyola</strong> (incloent-hi la LSSI-CE, LOPD-GDD i RGPD).</p>
        <p>Per a la resolució de conflictes, les parts es sotmeten als <strong>Jutjats i Tribunals del domicili del titular</strong>.</p>
        <p style="color:var(--mut);font-size:.85rem;margin-top:16px;">Última actualització: <?= date('d/m/Y') ?></p>
    </section>

</main>
