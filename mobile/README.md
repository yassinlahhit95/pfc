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

## Phase 2+ prerequisite (not yet configured)

Push notifications require a native Android app registered in the existing
Firebase project (`FIREBASE_PROJECT_ID`, currently `pfc1-5c23c`) via the
Firebase console, with `google-services.json` placed at
`android/app/google-services.json`. This is a manual step in the Firebase
console — not something that can be automated from the repo.
