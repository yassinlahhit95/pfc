<div class="legal-hero">
    <h1><i class="fas fa-scale-balanced" style="margin-right:10px;opacity:.9;"></i><?= __('Aviso Legal', 'Legal Notice') ?></h1>
    <span class="badge">LSSI-CE · Art. 10</span>
</div>

<main class="legal-main">

    <div class="legal-toc">
        <h2><i class="fas fa-list-ul" style="margin-right:6px;"></i> <?= __('Contenido', 'Contents') ?></h2>
        <ol>
            <li><a href="#titular">Owner Identification</a></li>
            <li><a href="#uso">Terms of Use</a></li>
            <li><a href="#propiedad">Intellectual Property</a></li>
            <li><a href="#responsabilidad">Liability & Warranties</a></li>
            <li><a href="#enlaces">Links Policy</a></li>
            <li><a href="#ley">Applicable Law</a></li>
        </ol>
    </div>

    <section class="legal-section" id="titular">
        <h2><i class="fas fa-building"></i> 1. Owner Identification</h2>
        <p>In compliance with Article 10 of <strong>Law 34/2002, of July 11, on Information Society Services and Electronic Commerce (LSSI-CE)</strong>, the owner information is stated below:</p>
        <ul>
            <li><strong>Name/Denomination:</strong> <?= htmlspecialchars($nombreCentro) ?></li>
            <li><strong>NIF/CIF:</strong> [Enter NIF/CIF of the center]</li>
            <li><strong>Address:</strong> <?= htmlspecialchars(trim(($cfg['direccionCentro'] ?? '') . ' ' . ($cfg['ciudadCentro'] ?? '') . ' ' . ($cfg['cpCentro'] ?? ''))) ?: '[Enter center address]' ?></li>
            <li><strong>Phone:</strong> <?= !empty($cfg['telefonoCentro']) ? htmlspecialchars($cfg['telefonoCentro']) : '[Center phone]' ?></li>
            <li><strong>Email:</strong> <?= !empty($emailCentro) ? '<a href="mailto:' . htmlspecialchars($emailCentro) . '">' . htmlspecialchars($emailCentro) . '</a>' : '[Center email]' ?></li>
            <li><strong>Main Activity:</strong> Vocational training and educational management center.</li>
            <li><strong>Technology Platform:</strong> AulaPro — school management platform developed by AulaPro SaaS.</li>
        </ul>
    </section>

    <section class="legal-section" id="uso">
        <h2><i class="fas fa-laptop"></i> 2. Terms of Use</h2>
        <p>Access and use of this platform attributes the condition of <strong>user</strong> (hereinafter, "the User") and implies full and unreserved acceptance of these terms of use.</p>
        <h3>Restricted Access</h3>
        <p>This platform is <strong>exclusively intended for authorized users</strong> of the center: administrators, teachers, students, and legal guardians. Unauthorized access is strictly prohibited and may result in legal action.</p>
        <h3>Correct Use</h3>
        <p>The User agrees to:</p>
        <ul>
            <li>Not introduce false data or data of third parties without their consent.</li>
            <li>Not perform activities that damage, interrupt, or overload the platform.</li>
            <li>Maintain the confidentiality of their credentials.</li>
            <li>Not use the platform for unlawful or public-order-violating purposes.</li>
        </ul>
        <h3>Modifications</h3>
        <p>The center reserves the right to modify these terms at any time by publishing the updated version on this page.</p>
    </section>

    <section class="legal-section" id="propiedad">
        <h2><i class="fas fa-copyright"></i> 3. Intellectual & Industrial Property</h2>
        <p>All elements of this platform — including, but not limited to, software, source code, designs, marks, logos, texts, and images — are protected by Spanish and European Union <strong>intellectual and industrial property laws</strong>.</p>
        <p>Any reproduction, distribution, transformation, or public communication of any element of the platform without express written authorization is strictly prohibited.</p>
    </section>

    <section class="legal-section" id="responsabilidad">
        <h2><i class="fas fa-shield-halved"></i> 4. Liability & Warranties</h2>
        <h3>Service Availability</h3>
        <p>The center does not guarantee uninterrupted availability of the platform, which may be suspended for technical maintenance, upgrades, or external factors.</p>
        <h3>Accuracy of Information</h3>
        <p>The owner strives to ensure information is accurate, but does not guarantee the absence of errors. Academic data (grades, schedules, etc.) is for information purposes and the physical paper copy or center records prevail in case of discrepancy.</p>
        <h3>User Generated Content</h3>
        <p>The center is not responsible for content uploaded by users, but reserves the right to delete content that infringes the law or platform rules.</p>
        <div class="legal-info-box">
            <i class="fas fa-info-circle"></i>
            <span>If you notice inappropriate content, please contact us at: <strong><?= !empty($emailCentro) ? htmlspecialchars($emailCentro) : '[center email]' ?></strong></span>
        </div>
    </section>

    <section class="legal-section" id="enlaces">
        <h2><i class="fas fa-link"></i> 5. Links Policy</h2>
        <p>The platform may contain links to third-party websites. These are provided as references and the center does not control or assume responsibility for their content, privacy, or availability.</p>
    </section>

    <section class="legal-section" id="ley">
        <h2><i class="fas fa-gavel"></i> 6. Applicable Law & Jurisdiction</h2>
        <p>These terms of use are governed by <strong>Spanish legislation</strong>, including LSSI-CE, LOPD-GDD, and GDPR (EU Regulation 2016/679).</p>
        <p>For any dispute, the parties submit to the <strong>Courts and Tribunals of the owner\'s domicile</strong>, waiving any other jurisdiction.</p>
        <p style="color:var(--mut);font-size:.85rem;margin-top:16px;">Last update: <?= date('m/d/Y') ?></p>
    </section>

</main>
