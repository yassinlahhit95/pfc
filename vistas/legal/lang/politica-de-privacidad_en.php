<div class="legal-hero">
    <h1><i class="fas fa-user-shield" style="margin-right:10px;opacity:.9;"></i><?= __('Política de Privacidad', 'Privacy Policy') ?></h1>
    <span class="badge">GDPR · LOPD-GDD · Art. 13 & 14</span>
</div>

<main class="legal-main">

    <div class="legal-toc">
        <h2><i class="fas fa-list-ul" style="margin-right:6px;"></i> <?= __('Contenido', 'Contents') ?></h2>
        <ol>
            <li><a href="#responsable">Data Controller</a></li>
            <li><a href="#datos">Data We Process</a></li>
            <li><a href="#finalidades">Purposes & Legal Basis</a></li>
            <li><a href="#conservacion">Data Retention</a></li>
            <li><a href="#destinatarios">Recipients & Transfers</a></li>
            <li><a href="#derechos">Your Rights</a></li>
            <li><a href="#menores">Protection of Minors</a></li>
            <li><a href="#app-movil">Mobile Application</a></li>
            <li><a href="#seguridad">Data Security</a></li>
            <li><a href="#cambios">Changes to This Policy</a></li>
        </ol>
    </div>

    <div class="legal-info-box verde">
        <i class="fas fa-check-circle"></i>
        <span>We commit to processing your personal data with transparency, in compliance with the <strong>General Data Protection Regulation (GDPR)</strong>.</span>
    </div>

    <?php
    $direccionCompleta = trim(($cfg['direccionCentro'] ?? '') . ', ' . ($cfg['ciudadCentro'] ?? '') . ' ' . ($cfg['cpCentro'] ?? ''), ", ");
    $nifCif = trim($cfg['nifCifCentro'] ?? '');
    ?>
    <section class="legal-section" id="responsable">
        <h2><i class="fas fa-building"></i> 1. Data Controller</h2>
        <ul>
            <li><strong>Name:</strong> <?= htmlspecialchars($nombreCentro) ?></li>
            <?php if ($nifCif !== ''): ?>
            <li><strong>NIF/CIF:</strong> <?= htmlspecialchars($nifCif) ?></li>
            <?php endif; ?>
            <?php if ($direccionCompleta !== ''): ?>
            <li><strong>Address:</strong> <?= htmlspecialchars($direccionCompleta) ?></li>
            <?php endif; ?>
            <?php if (!empty($cfg['telefonoCentro'])): ?>
            <li><strong>Phone:</strong> <?= htmlspecialchars($cfg['telefonoCentro']) ?></li>
            <?php endif; ?>
            <?php if (!empty($emailCentro)): ?>
            <li><strong>Contact:</strong> <a href="mailto:<?= htmlspecialchars($emailCentro) ?>"><?= htmlspecialchars($emailCentro) ?></a></li>
            <?php endif; ?>
        </ul>
    </section>

    <section class="legal-section" id="datos">
        <h2><i class="fas fa-database"></i> 2. Data We Process</h2>
        <p>Depending on your relationship with the center, we process:</p>
        <h3>Administrators</h3>
        <ul>
            <li>Full name, email, and password (hashed).</li>
            <li>Activity logs.</li>
        </ul>
        <h3>Teachers</h3>
        <ul>
            <li>Full name, email, password, modules, and classes assigned.</li>
        </ul>
        <h3>Students</h3>
        <ul>
            <li>Full name, email, password, birth date, national ID (DNI), and grades.</li>
            <li>Tuition payment history and attendance.</li>
        </ul>
    </section>

    <section class="legal-section" id="finalidades">
        <h2><i class="fas fa-bullseye"></i> 3. Purposes & Legal Basis</h2>
        <table style="width:100%;border-collapse:collapse;font-size:.875rem;margin-top:12px;">
            <thead>
                <tr style="background:var(--bg);">
                    <th style="padding:10px 14px;text-align:left;border-bottom:2px solid var(--border);color:var(--mut);font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Purpose</th>
                    <th style="padding:10px 14px;text-align:left;border-bottom:2px solid var(--border);color:var(--mut);font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;">Legal Basis</th>
                </tr>
            </thead>
            <tbody>
                <tr><td style="padding:10px 14px;border-bottom:1px solid var(--border);">Academic management (grades, modules, timetables)</td><td style="padding:10px 14px;border-bottom:1px solid var(--border);">Contractual relationship / legal obligation</td></tr>
                <tr><td style="padding:10px 14px;border-bottom:1px solid var(--border);">Tuition payments management</td><td style="padding:10px 14px;border-bottom:1px solid var(--border);">Contractual relationship</td></tr>
                <tr><td style="padding:10px 14px;border-bottom:1px solid var(--border);">Security and access controls (activity logs)</td><td style="padding:10px 14px;border-bottom:1px solid var(--border);">Legitimate interest / legal obligation</td></tr>
            </tbody>
        </table>
    </section>

    <section class="legal-section" id="conservacion">
        <h2><i class="fas fa-clock"></i> 4. Data Retention</h2>
        <ul>
            <li><strong>Academic records:</strong> Minimum of 5 years.</li>
            <li><strong>Access logs:</strong> Maximum of 12 months.</li>
            <li><strong>Payment data:</strong> 6 years, as required by financial laws.</li>
        </ul>
    </section>

    <section class="legal-section" id="destinatarios">
        <h2><i class="fas fa-share-nodes"></i> 5. Recipients & Transfers</h2>
        <p>Only authorized center personnel can access your data. International data transfers (e.g. via FCM for push notifications managed by Google Firebase) comply with EU Standard Contractual Clauses.</p>
    </section>

    <section class="legal-section" id="derechos">
        <h2><i class="fas fa-hand-holding-heart"></i> 6. Your Rights</h2>
        <p>You have the right to access, rectify, delete, restrict, oppose, and export your personal data. You can request a PDF copy from your profile page or send an email to the school.</p>
    </section>

    <section class="legal-section" id="menores">
        <h2><i class="fas fa-child"></i> 7. Protection of Minors</h2>
        <p>Processing data of children under 14 requires parent or legal guardian consent.</p>
    </section>

    <section class="legal-section" id="app-movil">
        <h2><i class="fas fa-mobile-screen-button"></i> 8. Mobile Application</h2>
        <p>When accessing via the AulaPro app, the Firebase Cloud Messaging token is processed for notifications. Device parameters and access tokens are saved locally and encrypted (AES-256 via Android Keystore).</p>
    </section>

    <section class="legal-section" id="seguridad">
        <h2><i class="fas fa-lock"></i> 9. Data Security</h2>
        <p>We apply appropriate security measures, including symmetric encryption (AES-256) on sensitive database fields (DNI, phone, address, MFA secrets), HTTPS, and brute-force mitigation locks.</p>
    </section>

    <section class="legal-section" id="cambios">
        <h2><i class="fas fa-history"></i> 10. Changes to this Policy</h2>
        <p>Any updates to this policy will be published on this page and notified in the platform.</p>
        <p style="color:var(--mut);font-size:.85rem;margin-top:16px;">Last update: <?= date('m/d/Y') ?></p>
    </section>

</main>
