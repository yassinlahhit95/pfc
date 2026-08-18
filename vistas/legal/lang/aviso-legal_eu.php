<div class="legal-hero">
    <h1><i class="fas fa-scale-balanced" style="margin-right:10px;opacity:.9;"></i><?= __('Aviso Legal', 'Lege Oharra') ?></h1>
    <span class="badge">LSSI-CE · Art. 10</span>
</div>

<main class="legal-main">

    <div class="legal-toc">
        <h2><i class="fas fa-list-ul" style="margin-right:6px;"></i> <?= __('Contenido', 'Edukia') ?></h2>
        <ol>
            <li><a href="#titular">Titularraren identifikazioa</a></li>
            <li><a href="#uso">Erabilera baldintzak</a></li>
            <li><a href="#propiedad">Jabetza intelektuala</a></li>
            <li><a href="#responsabilidad">Erantzukizuna</a></li>
            <li><a href="#enlaces">Esteken politika</a></li>
            <li><a href="#ley">Lege aplikagarria</a></li>
        </ol>
    </div>

    <section class="legal-section" id="titular">
        <h2><i class="fas fa-building"></i> 1. Titularraren Identifikazioa</h2>
        <p><strong>Informazioaren Gizartearen eta Merkataritza Elektronikoaren Zerbitzuei buruzko uztailaren 11ko 34/2002 Legearen (LSSI-CE)</strong> 10. artikulua betez, honako datu hauen berri ematen da:</p>
        <ul>
            <li><strong>Izendapena:</strong> <?= htmlspecialchars($nombreCentro) ?></li>
            <li><strong>IFZ/IFK:</strong> [Sartu hemen zentroaren IFZ edo IFK]</li>
            <li><strong>Helbidea:</strong> <?= htmlspecialchars(trim(($cfg['direccionCentro'] ?? '') . ' ' . ($cfg['ciudadCentro'] ?? '') . ' ' . ($cfg['cpCentro'] ?? ''))) ?: '[Sartu hemen zentroaren helbidea]' ?></li>
            <li><strong>Telefonoa:</strong> <?= !empty($cfg['telefonoCentro']) ? htmlspecialchars($cfg['telefonoCentro']) : '[Zentroaren telefonoa]' ?></li>
            <li><strong>Helbide elektronikoa:</strong> <?= !empty($emailCentro) ? '<a href="mailto:' . htmlspecialchars($emailCentro) . '">' . htmlspecialchars($emailCentro) . '</a>' : '[Zentroaren e-posta]' ?></li>
            <li><strong>Jarduera nagusia:</strong> Lanbide heziketako eta hezkuntza kudeaketako zentroa.</li>
            <li><strong>Plataforma teknologikoa:</strong> <?= htmlspecialchars(FeatureGuard::getCenterName()) ?> — <?= htmlspecialchars(FeatureGuard::getCenterName()) ?> SaaSek garatutako eskola-kudeaketa sistema.</li>
        </ul>
    </section>

    <section class="legal-section" id="uso">
        <h2><i class="fas fa-laptop"></i> 2. Erabilera Baldintzak</h2>
        <p>Plataforma honetara sartzeak eta erabiltzeak <strong>erabiltzaile</strong> izaera ematen dio (aurrerantzean, «Erabiltzailea») eta erabilera baldintza hauen onarpen osoa dakar.</p>
        <h3>Sarbide mugatua</h3>
        <p>Plataforma hau zentroko <strong>baimendutako erabiltzaileentzat bakarrik da</strong>: administratzaileak, irakasleak, ikasleak eta legezko tutoreak. Baimenik gabeko sarbidea debekatuta dago eta legezko neurriak ekar ditzake.</p>
        <h3>Erabilera egokia</h3>
        <p>Erabiltzaileak konpromiso hauek hartzen ditu:</p>
        <ul>
            <li>Ez sartu datu faltsurik edo hirugarrenen daturik euren baimenik gabe.</li>
            <li>Ez egin plataformari kalte egingo dioten jarduerarik.</li>
            <li>Sarbide-kredentzialen konfidentzialtasuna mantendu.</li>
            <li>Ez erabili plataforma legez kanpoko helburuetarako.</li>
        </ul>
    </section>

    <section class="legal-section" id="propiedad">
        <h2><i class="fas fa-copyright"></i> 3. Jabetza Intelektuala eta Industriala</h2>
        <p>Plataforma honen elementu guztiak — kodea, diseinua, markak, logotipoak, testuak eta irudiak barne — Espainiako eta Europar Batasuneko <strong>jabetza intelektual eta industrialaren legeek</strong> babesten dituzte.</p>
        <p>Debekatuta dago plataformako edozein elementu kopiatzea edo hedatzea jabearen idatzizko baimenik gabe.</p>
    </section>

    <section class="legal-section" id="responsabilidad">
        <h2><i class="fas fa-shield-halved"></i> 4. Erantzukizuna eta Bermeak</h2>
        <p>Zentroak ez du bermatzen plataformaren etengabeko erabilgarritasuna, mantentze-lanengatik eten daitekeena. Datu akademikoak informatiboak dira; zentroko jatorrizko paperak du balioa discrepancies egonez gero.</p>
        <div class="legal-info-box">
            <i class="fas fa-info-circle"></i>
            <span>Edukiren bat desegokia bada, jarri harremanetan hemen: <strong><?= !empty($emailCentro) ? htmlspecialchars($emailCentro) : '[zentroaren e-posta]' ?></strong></span>
        </div>
    </section>

    <section class="legal-section" id="enlaces">
        <h2><i class="fas fa-link"></i> 5. Esteken Politika</h2>
        <p>Plataformak hirugarrenen estekak eduki ditzake. Zentroak ez du bere gain hartzen estekatutako gune horien edukien edo pribatutasunaren erantzukizunik.</p>
    </section>

    <section class="legal-section" id="ley">
        <h2><i class="fas fa-gavel"></i> 6. Lege Aplikagarria eta Jurisdikzioa</h2>
        <p>Baldintza hauek <strong>Espainiako legeriaren</strong> arabera arautzen dira (LSSI-CE, DBLO-DEB eta DBEO/GDPR barne).</p>
        <p>Edozein gatazkatarako, alderdiek <strong>titularraren egoitzako Epaitegietara</strong> joko dute.</p>
        <p style="color:var(--mut);font-size:.85rem;margin-top:16px;">Azken eguneratzea: <?= date('d/m/Y') ?></p>
    </section>

</main>
