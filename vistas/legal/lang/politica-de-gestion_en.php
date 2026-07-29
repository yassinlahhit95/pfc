<div class="legal-hero">
    <h1><i class="fas fa-clipboard-check" style="margin-right:10px;opacity:.9;"></i><?= __('Política de Gestión', 'Management Policy') ?></h1>
    <span class="badge">Service Quality · Security · Continuity</span>
</div>

<main class="legal-main">

    <div class="legal-toc">
        <h2><i class="fas fa-list-ul" style="margin-right:6px;"></i> <?= __('Contenido', 'Contents') ?></h2>
        <ol>
            <li><a href="#objetivo">Objective & Scope</a></li>
            <li><a href="#sla">Service Level Agreement (SLA)</a></li>
            <li><a href="#seguridad">Information Security</a></li>
            <li><a href="#datos">Data & Backup Management</a></li>
            <li><a href="#incidencias">Incident Management</a></li>
            <li><a href="#actualizaciones">Updates & Maintenance</a></li>
            <li><a href="#responsabilidades">Responsibilities</a></li>
            <li><a href="#mejora">Continuous Improvement</a></li>
        </ol>
    </div>

    <section class="legal-section" id="objetivo">
        <h2><i class="fas fa-bullseye"></i> 1. Objective & Scope</h2>
        <p>This <strong>Management Policy</strong> describes the service quality, information security, and continuity commitments of the <strong>AulaPro</strong> school management platform.</p>
    </section>

    <section class="legal-section" id="sla">
        <h2><i class="fas fa-gauge-high"></i> 2. Service Level Agreement (SLA)</h2>
        <div class="sla-grid">
            <div class="sla-card">
                <span class="val">99.5%</span>
                <span class="lbl">Monthly Availability</span>
            </div>
            <div class="sla-card">
                <span class="val">&lt; 4h</span>
                <span class="lbl">Critical Incident Response</span>
            </div>
            <div class="sla-card">
                <span class="val">24h</span>
                <span class="lbl">Maintenance Window</span>
            </div>
            <div class="sla-card">
                <span class="val">Daily</span>
                <span class="lbl">Backups Frequency</span>
            </div>
        </div>
    </section>

    <section class="legal-section" id="seguridad">
        <h2><i class="fas fa-shield-alt"></i> 3. Information Security</h2>
        <p>AulaPro implements advanced security: access controls with bcrypt hashing, 2FA for administrative roles, international IP geoblocking, secure session cookies, HTTPS/TLS protocols, SQL prepared statements, and CSRF/XSS prevention.</p>
    </section>

    <section class="legal-section" id="datos">
        <h2><i class="fas fa-hard-drive"></i> 4. Data & Backup Management</h2>
        <p>Backups are executed daily and stored in encrypted, isolated servers for at least 30 days.</p>
    </section>

    <section class="legal-section" id="incidencias">
        <h2><i class="fas fa-triangle-exclamation"></i> 5. Incident Management</h2>
        <p>Incidents are classified based on severity and have specific response SLAs (ranging from 4 hours for critical issues to 24 hours for minor bugs).</p>
    </section>

    <section class="legal-section" id="actualizaciones">
        <h2><i class="fas fa-rotate"></i> 6. Updates & Maintenance</h2>
        <p>Programmed maintenance is executed during off-peak hours to minimize impact on the school\'s daily activities.</p>
    </section>

    <section class="legal-section" id="responsabilidades">
        <h2><i class="fas fa-handshake"></i> 7. Responsibilities</h2>
        <p>AulaPro handles platform maintenance and encryption; the school is responsible for secure access credentials and staff permissions management.</p>
    </section>

    <section class="legal-section" id="mejora">
        <h2><i class="fas fa-chart-line"></i> 8. Continuous Improvement</h2>
        <p>We execute periodic security audits to test our defenses against external cyberthreats.</p>
        <p style="color:var(--mut);font-size:.85rem;margin-top:16px;">Last update: <?= date('m/d/Y') ?></p>
    </section>

</main>
