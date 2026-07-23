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

## Contents of noDeploy/

```
noDeploy/
  database.sql                    ← Schema source of truth for a fresh install (see CLAUDE.md "DB Migrations")
  demo_data.sql                   ← Rich FAKE data for local dev only (students, teachers, cycles...) — never seed production with this
  seed_minimal.sql                ← Minimal real seed for a fresh install without the /install/ wizard (one admin + center row)
  migrations/
    001_blog_posts.sql .. 007_tours_completados.sql   ← numbered, applied in order to an EXISTING db
    aplicar_todas_produccion.sql  ← maintained concatenation of all of the above, single-command convenience
  build-assets.js                 ← npm run build:assets script (package.json stays at root; this doesn't need to)
  generar_htaccess_vendor.php     ← composer post-install/update hook — regenerates vendor/.htaccess
  install-check.php               ← composer post-install/update hook — checks PHP/extensions, points to /install/
  smoke_test.php                  ← CLI-only, run against a deployed URL after FTP'ing to catch a broken deploy
  verificar_motor_academico.php   ← CLI-only, one-off comparison tool (old vs configurable grading engine)
  API_DOCS.md                     ← REST API v1 request/response reference
  CLOUDFLARE_R2_SETUP.md          ← R2 bucket setup walkthrough
  DO_NOT_UPLOAD.md                ← this file
```

Note: `migrar_cifrado_pii.php` and `optimizar_imagenes.php` stay at the **project root**, not here, despite being one-time maintenance scripts — both are explicitly meant to be deployed and run once *after* an FTP upload (see their own header comments), unlike everything listed above which never needs to touch the server at all.
