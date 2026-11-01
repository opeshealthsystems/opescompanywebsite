# OPES Health mobile app

Expo app for OPES Health Systems accounts. This is a separate app from OpesCare Finder and uses the OPES Health Systems API at `https://opeshealthsystems.com/api/v1`.

## Current state

- App version: `0.1.0` private preview.
- No demo accounts or business sample data are included.
- Sign-in uses OAuth 2.0 authorization code with S256 PKCE and the OPES callback `opeshealth://oauth/callback`.
- API access is checked for the signed-in user on every request. The app stores mobile tokens with `expo-secure-store`; browser preview keeps them in memory only.
- Set `EXPO_PUBLIC_OHS_OAUTH_CLIENT_ID` after an administrator has reviewed and provisioned the public app client. Do not commit client secrets or user tokens.
- Public partner directory browsing works without sign-in; partner self-service APIs are not yet available in the live API contract.
- Staff contact-directory management also awaits a published contact endpoint. The current live contract supports CRM deals and partner applications.

## Run locally

```sh
npm install
npx expo install expo-auth-session expo-crypto expo-secure-store expo-web-browser
npx expo start
```

For the native OAuth callback, use a development build or installed app with the `opeshealth` scheme. The reviewed API client must register the exact callback `opeshealth://oauth/callback` and the scopes needed by the reviewed app. Each account receives only scopes authorized for its active roles.

## API-backed workspaces

- Staff/administrators: quotes, customer profiles, partner applications and CRM deals.
- Customers: profile, service requests, tickets and invoices.
- Practitioners: profile, courses and certificates.
- Testers: assigned tests and issue reporting.
- Partners: public directory; private account management awaits partner self-service API operations.

## Release history

See [OPES Health app versions and updates](https://opeshealthsystems.com/en/mobile-app#updates).
