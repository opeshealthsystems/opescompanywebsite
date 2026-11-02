# OPES Health mobile app

Expo app for OPES Health Systems accounts. This is a separate app from OpesCare Finder and uses the OPES Health Systems API at `https://opeshealthsystems.com/api/v1`.

## Current state

- App version: `0.1.0` private preview.
- Five health-role and eleven hotel-operations sample profiles are available as one-tap local previews. The Super admin and hotel roles are fictional demonstration profiles, not privileged sessions or server accounts. All samples use `example.test` identities, clear any stored sign-in token, make no API calls and cannot modify real accounts.
- Sign-in uses OAuth 2.0 authorization code with S256 PKCE and the OPES callback `opeshealth://oauth/callback`.
- Sign-in has no role chooser. The app reads `/me` after OAuth and selects the workspace from the account's RBAC roles and effective API scopes.
- API access is checked for the signed-in user on every request. The app stores mobile tokens with `expo-secure-store`; browser preview keeps them in memory only.
- Set `EXPO_PUBLIC_OHS_OAUTH_CLIENT_ID` after an administrator has reviewed and provisioned the public app client. Do not commit client secrets or user tokens.
- EAS Update is configured with app-version runtime compatibility and `preview`/`production` channels. Native builds check for compatible JavaScript updates on launch; changes to native modules still require a new build.
- Preview builds and OTA updates use the same EAS `preview` environment for the public API URL and reviewed OAuth app client ID. Compatible updates download on launch and apply after the next restart.
- The native Android splash screen uses the OPES app icon on the platform background color; splash configuration changes require a new APK.
- Public partner directory browsing and partner applications work without sign-in; private partner account management is not yet in the live API contract.
- Staff/admin contact and enquiry management uses the published `/admin/enquiries/leads` API and requires both the account's admin role and effective `enquiries:read` or `enquiries:write` scope.

## Run locally

```sh
npm install
npx expo install expo-auth-session expo-crypto expo-secure-store expo-updates expo-web-browser
npx expo start
```

For the native OAuth callback, use a development build or installed app with the `opeshealth` scheme. The reviewed API client must register the exact callback `opeshealth://oauth/callback` and the scopes needed by the reviewed app. Each account receives only scopes authorized for its active roles.

## API-backed workspaces

- Staff/administrators: quotes, customer profiles, contacts/enquiries, partner applications and CRM deals. Quote and contact actions follow live OAuth scope and server role checks.
- Customers: profile, service requests, tickets and invoices.
- Practitioners: profile, courses and certificates.
- Testers: assigned tests and issue reporting.
- Partners: public directory and application submission; private account management awaits partner self-service API operations.

All demo accounts are fictional and local only. Health-role demos use sample health workflows; hotel-role demos show simulated properties, reservations, room readiness, stock, finance and food-service workflows. Sample actions stay in local app state and reset when the demo is exited. No hotel API or live hotel account integration is published by this preview. Real account sign-in routes supported OPES operations to the published API; the app does not create demo logins on the server.

## Release history

See [OPES Health app versions and updates](https://opeshealthsystems.com/en/mobile-app#updates).

For every successful EAS build that is distributed, add a release entry to `config/mobile_app.php` with the app version, Android build number, release date, current/superseded status, English and French summary, and user-facing changes. Update the current build number and SHA-256 there, then replace `public/mobile-app/opes-health-0.1.0.apk` and its `.sha256` file with the verified APK. Add each published OTA group separately under `ota_updates`, with its runtime, release date, group ID and changes; OTA groups do not increment the native Android build number. The public page renders both lists from this manifest, so the download card and history stay aligned.
