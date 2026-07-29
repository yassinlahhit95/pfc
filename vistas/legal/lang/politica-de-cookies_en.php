<div class="legal-hero">
    <h1><i class="fas fa-cookie-bite" style="margin-right:10px;opacity:.9;"></i><?= __('Política de Cookies', 'Cookies Policy') ?></h1>
    <span class="badge">ePrivacy Directive · GDPR · Art. 22 LSSI-CE</span>
</div>

<main class="legal-main">

    <div class="legal-toc">
        <h2><i class="fas fa-list-ul" style="margin-right:6px;"></i> <?= __('Contenido', 'Contents') ?></h2>
        <ol>
            <li><a href="#que-son">What are cookies?</a></li>
            <li><a href="#tipos">Types of cookies</a></li>
            <li><a href="#tabla">Cookies used</a></li>
            <li><a href="#terceros">Third-party cookies</a></li>
            <li><a href="#gestion">Management and disabling</a></li>
            <li><a href="#actualizacion">Updates</a></li>
        </ol>
    </div>

    <section class="legal-section" id="que-son">
        <h2><i class="fas fa-question-circle"></i> 1. What are Cookies?</h2>
        <p><strong>Cookies</strong> are small text files that websites store on your device to remember information between visits. They can be session cookies (removed when you close your browser) or persistent cookies.</p>
        <p>According to legislation, cookies that are not strictly necessary for the service require the user\'s prior consent.</p>
        <div class="legal-info-box">
            <i class="fas fa-info-circle"></i>
            <span>This platform is <strong>restricted to authorized users</strong>. By logging in, you accept cookies strictly necessary to ensure the service works.</span>
        </div>
    </section>

    <section class="legal-section" id="tipos">
        <h2><i class="fas fa-tags"></i> 2. Types of Cookies</h2>
        <ul>
            <li><strong>Technical / Necessary:</strong> Essential for the platform to work (authentication, security). No consent required.</li>
            <li><strong>Preferences / Functional:</strong> Remember user settings (language, theme). Consent required.</li>
            <li><strong>Analytics / Measurement:</strong> Collect statistical data on usage. Consent required.</li>
            <li><strong>Marketing / Advertising:</strong> This platform <strong>does NOT use marketing cookies</strong>.</li>
        </ul>
    </section>

    <section class="legal-section" id="tabla">
        <h2><i class="fas fa-table"></i> 3. Cookies Used in this Platform</h2>
        <div class="cookies-table-wrap">
            <table class="cookies-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Duration</th>
                        <th>Purpose</th>
                        <th>Origin</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>PHPSESSID</code></td>
                        <td><span class="cookie-tipo necesaria">Necessary</span></td>
                        <td>Session</td>
                        <td>Identifies the authenticated user session. Without it, the platform cannot function.</td>
                        <td>Own</td>
                    </tr>
                    <tr>
                        <td><code>csrf_token</code></td>
                        <td><span class="cookie-tipo necesaria">Necessary</span></td>
                        <td>Session</td>
                        <td>Security token to prevent CSRF attacks.</td>
                        <td>Own</td>
                    </tr>
                    <tr>
                        <td><code>_fg_ts</code>, <code>_fg_data</code></td>
                        <td><span class="cookie-tipo funcional">Functional</span></td>
                        <td>5 min</td>
                        <td>Cache for active modules configuration.</td>
                        <td>Own</td>
                    </tr>
                    <tr>
                        <td><code>lang</code></td>
                        <td><span class="cookie-tipo funcional">Functional</span></td>
                        <td>1 year</td>
                        <td>Stores the language preference chosen by the user (Spanish, Basque, Catalan, English).</td>
                        <td>Own</td>
                    </tr>
                    <tr>
                        <td>Firebase / FCM</td>
                        <td><span class="cookie-tipo funcional">Functional</span></td>
                        <td>Persistent</td>
                        <td>Device token for push notifications. Managed by Google LLC.</td>
                        <td>Third-party</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p style="font-size:.82rem;color:var(--mut);margin-top:8px;"><i class="fas fa-info-circle"></i> Revision date: <?= date('m/d/Y') ?></p>
    </section>

    <section class="legal-section" id="terceros">
        <h2><i class="fas fa-globe"></i> 4. Third-Party Cookies</h2>
        <p>This platform uses third-party services that may install their own cookies:</p>
        <ul>
            <li><a href="https://policies.google.com/privacy" target="_blank" rel="noopener noreferrer">Google Privacy Policy</a></li>
            <li><a href="https://firebase.google.com/support/privacy" target="_blank" rel="noopener noreferrer">Firebase Privacy</a></li>
        </ul>
        <div class="legal-info-box rojo">
            <i class="fas fa-exclamation-triangle"></i>
            <span>This platform <strong>does not use Google Analytics, Meta Pixel, or advertising tracking technology</strong>.</span>
        </div>
    </section>

    <section class="legal-section" id="gestion">
        <h2><i class="fas fa-sliders"></i> 5. Disabling Cookies</h2>
        <p>You can manage or delete cookies from your browser settings. Disabling <strong>necessary cookies</strong> will prevent the platform from working correctly, and you will not be able to log in.</p>
    </section>

    <section class="legal-section" id="actualizacion">
        <h2><i class="fas fa-history"></i> 6. Updates</h2>
        <p>This cookies policy may change to align with new platform features or regulations.</p>
        <p style="color:var(--mut);font-size:.85rem;margin-top:16px;">Last update: <?= date('m/d/Y') ?></p>
    </section>

</main>
