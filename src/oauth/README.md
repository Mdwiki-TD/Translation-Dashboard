# OAuth Module

## Project Overview

The `src/oauth/` directory is currently **empty**. It exists as a placeholder but contains no files.

### Context

Despite being empty, the `OAuth\Settings\Settings` class is used extensively across the codebase (8+ files). This class is provided by the `src/backend/settings.php` file, not from this directory. The `src/include_all.php` file warns: "don't use OAuth\Settings\Settings here, Instance is not created yet", indicating the Settings singleton is initialized later in the bootstrap process.

### Why This Directory Exists

The directory may have been intended as a local namespace directory for OAuth-related code, but the implementation was placed in `src/backend/settings.php` instead. The PSR-4 autoload mapping in `composer.json` does not map to this directory.

---

## Status

| Aspect | Status |
|--------|--------|
| Files | 0 |
| PHP Code | None |
| Namespace | Not mapped in composer.json |
| Purpose | Placeholder / unused |

---

## Related Code

The `OAuth\Settings\Settings` class used throughout the application is defined in:
- `src/backend/settings.php` -- Singleton configuration class

Usage locations:
- `src/backend/userinfos_wrap.php` -- Authentication
- `src/backend/results/getcats.php` -- Category fetching
- `src/backend/td_api_wrap/td_api.php` -- API client
- `src/leaderboard/index.php` -- Leaderboard router
- `src/head.php` -- Environment detection
- `src/missing_old.php` -- Missing articles page
- `src/sitelinks.php` -- Sitelinks page

---

## Recommendation

This directory can be safely removed if no future plans require it. Alternatively, it could be used to house OAuth-related code if the authentication system is refactored.
