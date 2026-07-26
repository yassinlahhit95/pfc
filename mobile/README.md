# AulaPro Mobile

Flutter client for AulaPro, covering all 5 roles (admin, profesor, secretaria,
estudiante, tutor), synced against the PHP backend's `api/v1/` REST API
(see `../noDeploy/API_DOCS.md`).

This app is being built in phases. Phase 1 (this state): login for all 5
roles, profile, schedule, grades, announcements, events.

## Architecture

- **State management**: Riverpod (`flutter_riverpod`)
- **Networking**: `dio`, one `ApiClient` (`lib/core/api/api_client.dart`)
  attaching `Authorization: Bearer <token>` and centrally handling
  `401 token_expired` / `429 rate_limited`.
- **Routing**: `go_router`, role-based redirect in `lib/core/router/app_router.dart`.
- **Token storage**: `flutter_secure_storage` (Keystore-backed) — see
  `lib/core/storage/secure_storage.dart`. Never store the token in
  `shared_preferences`.
- **Structure**: feature-first — `lib/features/<area>/{data,application,presentation}`,
  cross-cutting concerns under `lib/core/`.

## Running against local Laragon

```
flutter run --dart-define=API_BASE_URL=http://10.0.2.2
```

Use `http://10.0.2.2` (not `pfc.test` or `localhost`) when running on the
Android **emulator** — that address is how the emulator reaches the host
machine's Laragon instance. On a **real device** on the same network, use
the host machine's LAN IP instead (e.g. `http://192.168.1.x`), since
`10.0.2.2` only resolves inside the emulator's virtual network.

Without `--dart-define`, the app defaults to the production API
(`https://aulapro.yassin.agency`).

## Building for production

```
flutter build apk --dart-define=API_BASE_URL=https://aulapro.yassin.agency
```

**Deploying this project under a different domain (e.g. for a client):** the
`--dart-define=API_BASE_URL=...` value here, `APP_URL` in `.env` (set by the
`/install/` wizard or `vistas/admin/saas/estado.php`), and `api/.htaccess`'s
CORS `Access-Control-Allow-Origin` (auto-synced from `APP_URL` by those same
two paths, see the comment in `api/.htaccess`) all need to agree on that
domain. The mobile build is the one piece **not** auto-synced — it's a
build-time flag, so every `flutter build` for a given deployment must pass
the matching `API_BASE_URL` explicitly; there's no server-side way to push
that into an already-built APK/IPA.

## Push notifications — one manual step left

The client (`lib/core/notifications/notifications_service.dart`) and backend
(`api/v1/fcm-token.php`, `controladores/firebase/firebase_helper.php`) are
both fully built. The only thing missing is registering this app as a native
Android app in the existing Firebase project (`FIREBASE_PROJECT_ID`,
currently `pfc1-5c23c`) via the Firebase console (package name
`com.aulapro.aulapro_mobile`), then placing the downloaded config at
`android/app/google-services.json`. That's a manual step in the Firebase
console — not something that can be automated from the repo. Until it's
done, `NotificationsService.init()` fails at the `Firebase.initializeApp()`
call and silently no-ops (caught, logged nowhere, doesn't crash the app) —
everything else keeps working normally via polling, exactly as before.
`android/app/build.gradle.kts` only applies the `google-services` Gradle
plugin when the file is present, so its absence never breaks a build either.
