# DO NOT UPLOAD TO SERVER

All files/folders in this directory (`noDeploy/`) must never be FTP'd to production.

Additional root-level files/folders that must also stay off the server (they have to physically live at the project root for their own tooling to work — npm/Composer both expect their manifest at the invocation directory — but their *contents* still shouldn't be exposed):

| File / Folder                 | Reason                                                        |
|--------------------------------|----------------------------------------------------------------|
| `CLAUDE.md`                    | AI dev instructions — internal only                            |
| `README.md`                    | Dev documentation                                               |
| `composer.json` / `composer.lock` | Exposes dependency names/exact versions                     |
| `package.json` / `package-lock.json` | Build-tool manifest — meaningless on a PHP server, and exposes tooling versions |
| `node_modules/`                | Gitignored — Node build-tool deps, never needed at runtime      |
| `.gitignore`                   | Dev tooling file                                                |
| `.env`                         | Credentials (gitignored, never commit)                          |
| `config/db.php`                | DB credentials (gitignored) — legacy fallback, see CLAUDE.md    |
| `config/service-account.json`  | Firebase key (gitignored)                                       |
| `vendor/`                      | Rebuild with `composer install --no-dev` on server, or FTP as-is (no CI/CD here — see CLAUDE.md) |
| `logs/`                        | Gitignored — local dev logs only                                |
| `docs/`                        | Gitignored — local dev notes only                               |
| `noDeploy/`                    | This folder itself                                              |

**`install/` is the one exception that *does* get deployed** — it's the production setup wizard, meant to run once on a fresh server. It self-locks after completion (`install/.installed` + a `directores`-table check, both independent — see `install/lib/helpers.php` and the "Installation" section of `CLAUDE.md`) so it's safe to leave deployed; removing the directory after first use is optional extra caution, not required.

**`noDeploy/database.sql` is the one *file* in this folder that also has to be uploaded** — the wizard's DB step (`install/steps/2_basedatos.php` → `install/lib/helpers.php`) reads it at `noDeploy/database.sql` on the server to import the schema. Upload `noDeploy/database.sql` (just that one file, not the rest of this folder) alongside `install/` for a fresh deploy; deleting it afterward, like `install/` itself, is optional extra caution once setup has completed.

## Contents of noDeploy/

```
noDeploy/
  database.sql                    ← THE schema file — complete, current, no migrations layer (see CLAUDE.md "Database schema").
                                     The one exception that does get uploaded, see note above.
  build-assets.js                 ← npm run build:assets script (package.json stays at root; this doesn't need to)
  generar_htaccess_vendor.php     ← composer post-install/update hook — regenerates vendor/.htaccess
  install-check.php               ← composer post-install/update hook — checks PHP/extensions, points to /install/
  API_DOCS.md                     ← REST API v1 request/response reference
  CLOUDFLARE_R2_SETUP.md          ← R2 bucket setup walkthrough
  INSTALL_WIZARD_SETUP.md         ← wizard implementation notes
  VENDOR_SECURITY_ALERT.md        ← tracks unpatched CVEs in composer deps (moved from root — was a stray doc, same category as the others above)
  DO_NOT_UPLOAD.md                ← this file
```
