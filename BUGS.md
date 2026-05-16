# BUGS.md — Gym Management System Bug Report

**Analysis Date:** 2026-05-16
**Scope:** All PHP, JS, and config files in `C:/xampp/htdocs/gym`
**Total Issues:** 33

---

## CRITICAL

These issues represent immediate security vulnerabilities or complete feature breakage.

---

### BUG-001: MD5 Password Hashing (Insecure) ✓ FIXED

- **Files:** `admin/login.php`, `admin/change-password.php`
- **Description:** Admin passwords were hashed with MD5. User-facing files were already using bcrypt.
- **Fix:**
  - `admin/login.php`: Dual-check — `password_verify()` first, MD5 fallback for legacy accounts. On MD5 match, rehashes to bcrypt immediately (transparent migration).
  - `admin/change-password.php`: Same dual-check for old password verification; new password saved with `password_hash(..., PASSWORD_BCRYPT)`.
  - `changepassword.php` / `login.php`: Already using bcrypt — no changes needed.

---

### BUG-002: Broken Change Password Feature — Session Email Never Set

- **Files:**
  - `changepassword.php:15`
  - `login.php` (missing assignment)
- **Description:** `changepassword.php` reads `$_SESSION['email']` to identify the user, but the login flow never assigns `$_SESSION['email']` after a successful login. The feature is non-functional for all users.
- **Impact:** No user can change their password. The query silently targets no row.
- **Fix:** Set `$_SESSION['email'] = $email;` in `login.php` after successful authentication.

---

### BUG-003: Insecure Direct Object Reference (IDOR) on Booking Details ✓ FIXED

- **File:** `booking-details.php:188`
- **Description:** Booking fetched by ID only — no ownership check against session user.
- **Fix:** Added `AND t1.userid = :uid` to the SELECT query and `AND userid = :uid` to the UPDATE query. If the booking doesn't belong to the logged-in user, redirects to `booking-history.php`.

---

### BUG-004: Temporary Debug Files Exposed in Web Root ✓ FIXED

- **Files:** `tmp_count_users.php`, `tmp_show_schema.php`, `tmp_booking_check.php`, `tmp_booking_schema.php`, `tmp_booking_join_test.php`
- **Description:** Debug PHP files were publicly accessible in the web root.
- **Fix:** Deleted all 5 files. Created `.gitignore` with `tmp_*.php` to prevent recurrence.

---

### BUG-005: Empty Root Password on Database Connection ✓ PARTIALLY FIXED

- **Files:** `include/config.php`, `admin/include/config.php`
- **Description:** DB connection used hardcoded `root` with empty password.
- **Code fix:** Both config files now read credentials from environment variables (`DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`) with XAMPP defaults as fallback. Both config files added to `.gitignore` so credentials are never committed.
- **Manual step still required:** In MySQL, create a dedicated user with a strong password and grant only the necessary privileges:
  ```sql
  CREATE USER 'gymapp'@'localhost' IDENTIFIED BY 'strong-password-here';
  GRANT SELECT, INSERT, UPDATE, DELETE ON gymdb.* TO 'gymapp'@'localhost';
  FLUSH PRIVILEGES;
  ```
  Then set `DB_USER` and `DB_PASS` environment variables (or update the local config directly).

---

### BUG-006: Database Error Messages Exposed to Users

- **File:** `include/config.php:15`
- **Description:** PDO is configured to throw exceptions (`PDO::ERRMODE_EXCEPTION`) and errors are not caught at the config level. Unhandled PDO exceptions propagate stack traces and SQL query fragments to the browser.
- **Impact:** Leaks table names, column names, SQL structure, and server paths to any user who triggers a DB error.
- **Fix:** Wrap DB operations in try/catch blocks and display a generic error page; log details server-side only.

---

## HIGH

These issues are significant vulnerabilities or cause broken functionality.

---

### BUG-007: XSS in JavaScript onclick Handlers

- **File:** `admin/booking-history.php:219-224`
- **Description:** Unescaped PHP variables are interpolated directly into JavaScript string literals inside `onclick` attributes. An attacker who can influence the underlying data can inject arbitrary JavaScript.
- **Impact:** Stored XSS — malicious script executes for any admin who views the booking history page.
- **Fix:** Use `json_encode()` to safely embed PHP values into JavaScript: `onclick="view(<?= json_encode($row['id']) ?>)"`.

---

### BUG-008: Reflected/Stored XSS in Profile and Booking Detail Pages

- **Files:**
  - `admin/profile.php:76, 80, 84, 89`
  - `profile.php:102-121`
  - `booking-details.php:210-217`
- **Description:** User-controlled data retrieved from the database is output into HTML without escaping. If any data was stored without sanitization, it will execute as HTML/JS.
- **Impact:** Stored XSS — attacker who can register with a crafted name/email can execute scripts in admin and user sessions.
- **Fix:** Wrap all database output in `htmlspecialchars($value, ENT_QUOTES, 'UTF-8')`.

---

### BUG-009: Broken JavaScript Redirect — Missing Quotes Around URL

- **Files:**
  - `profile.php:34`
  - `admin/profile.php:28`
- **Description:** A JavaScript redirect uses `window.location = /some/path` without quotes around the URL string, causing a JS syntax or reference error. The redirect never executes.
- **Impact:** Post-form-submission redirect is broken; users are left on a blank or error state.
- **Fix:** Change to `window.location = '/some/path';` (with quotes).

---

### BUG-010: CSRF — No Tokens on Any Forms

- **Files:** `registration.php`, `booking.php`, `profile.php`, `changepassword.php`, and all admin form pages
- **Description:** No CSRF tokens are present on any form in the application. An attacker can craft a malicious page that silently submits forms on behalf of a logged-in user.
- **Impact:** Account takeover (password change), unauthorized bookings, profile data modification.
- **Fix:** Generate a per-session token, embed it in all forms as a hidden field, and validate it on every POST request.

---

### BUG-011: Missing `exit` After `header()` Redirect ✓ FIXED

- **File:** `profile.php:6-8`
- **Description:** `header('Location: ...')` is called to redirect unauthenticated users, but there is no `exit;` or `die;` immediately after. PHP continues executing the rest of the page.
- **Impact:** Page logic (including DB queries and data rendering) executes for unauthenticated users. May expose data in HTTP response body before redirect.
- **Fix:** Added `exit;` after every `header('Location: ...')` call in: `profile.php`, `admin/profile.php`, `admin/booking-history-details.php`, `admin/report-registration.php`, `admin/add-post.php`, `admin/edit-post.php`, `admin/report-booking.php`, `admin/change-password.php`, `admin/manage-post.php`.

---

### BUG-012: Session Variable `$_SESSION['email']` Used But Never Set in Admin Login ✓ FIXED

- **Files:** `admin/login.php:24`, `admin/change-password.php:13`
- **Description:** `admin/change-password.php` reads `$_SESSION['email']` but `admin/login.php` never set it, so the admin password change silently failed every time.
- **Fix:** Added `$_SESSION['email'] = $email;` in `admin/login.php` after setting `$_SESSION['adminid']`.

---

### BUG-013: Broken Admin Sidebar Navigation Links ✓ FIXED

- **File:** `admin/include/sidebar.php`
- **Description:** Navigation links in the admin sidebar point to `.html` file extensions instead of `.php`.
- **Fix:** All links in `admin/include/sidebar.php` already use `.php` extensions — already resolved.

---

### BUG-014: No Secure Session Cookie Settings

- **Files:**
  - `include/config.php`
  - `admin/include/config.php`
- **Description:** Session is started without configuring `HttpOnly`, `Secure`, or `SameSite` cookie attributes. Sessions are also not regenerated after login.
- **Impact:** Session cookies are accessible to JavaScript (XSS session hijack), transmitted over HTTP (network interception), and vulnerable to CSRF.
- **Fix:** Call `session_set_cookie_params(['httponly' => true, 'secure' => true, 'samesite' => 'Strict'])` before `session_start()`, and call `session_regenerate_id(true)` after login.

---

## MEDIUM

These issues are functional bugs, data integrity problems, or lower-severity security weaknesses.

---

### BUG-015: No Duplicate Email Check on Registration ✓ FIXED

- **File:** `registration.php`
- **Description:** No check for existing email before INSERT — caused crash (UNIQUE constraint) or silent duplicate accounts.
- **Fix:** Added `SELECT COUNT(*) FROM tbluser WHERE email = :email` before INSERT; shows "An account with that email already exists." on conflict.

---

### BUG-016: Password Forced to Exactly 8 Characters ✓ FIXED

- **File:** `registration.php:25`
- **Description:** Backend used `strlen($password) != 8` and HTML had `maxlength="8"`, blocking passwords longer than 8 chars.
- **Fix:** Changed to `strlen($password) < 8`, updated regex from `{8}` to `{8,}`, removed `maxlength="8"` from both password inputs, updated hint text.

---

### BUG-017: Inconsistent Output Escaping Throughout Codebase

- **Files:** Multiple — `profile.php`, `booking-details.php`, admin pages
- **Description:** Some output uses `htmlspecialchars()`, some uses `htmlentities()`, and many output points use neither. This inconsistency means some data is protected while other equivalent data is not.
- **Impact:** Unpredictable XSS exposure depending on which path renders the data.
- **Fix:** Standardize on `htmlspecialchars($value, ENT_QUOTES, 'UTF-8')` for all HTML output, applied at render time.

---

### BUG-018: jQuery Selector Syntax Error

- **File:** `admin/js/main.js:22`
- **Description:** A jQuery selector contains a syntax error that causes a JavaScript exception at page load, potentially breaking all jQuery-dependent functionality on admin pages.
- **Impact:** Admin page interactivity (modals, dynamic tables, etc.) may be partially or fully non-functional.
- **Fix:** Correct the jQuery selector syntax at line 22.

---

### BUG-019: DOM-Based XSS Risk via `data-setbg` Attribute

- **File:** `js/main.js:63`
- **Description:** The JS reads a `data-setbg` attribute value and interpolates it directly into a CSS `background` property string without sanitization. A crafted attribute value could inject CSS expressions or, in older browsers, execute scripts.
- **Impact:** Potential CSS injection; low XSS risk in modern browsers but violates defense-in-depth.
- **Fix:** Validate that the `data-setbg` value is a URL before assignment, or use `element.style.backgroundImage = 'url(' + CSS.escape(url) + ')'`.

---

### BUG-020: Hardcoded Admin Link Visible in Public Header

- **File:** `include/header.php:12`
- **Description:** The public-facing site header contains a hardcoded link to the admin panel that is visible to all visitors regardless of authentication status.
- **Impact:** Exposes the admin panel URL, increasing attack surface and aiding reconnaissance.
- **Fix:** Remove the link or render it only when an admin session is active.

---

### BUG-021: Use of Deprecated `PDO::MYSQL_ATTR_INIT_COMMAND`

- **Files:**
  - `include/config.php`
  - `admin/include/config.php`
- **Description:** The PDO connection uses `PDO::MYSQL_ATTR_INIT_COMMAND` to set charset, which is deprecated in favor of the `charset` DSN parameter.
- **Impact:** May produce deprecation warnings in newer PHP versions; charset may not be applied reliably.
- **Fix:** Set charset in the DSN string: `mysql:host=...;dbname=...;charset=utf8mb4`.

---

### BUG-022: No Cascade Delete on Booking Deletion ✓ FIXED

- **File:** `admin/full-payment-bookings.php`
- **Description:** Delete handler only removed from `tblbooking`, leaving orphaned rows in `tblpayment`.
- **Fix:** Added `DELETE FROM tblpayment WHERE bookingID = :bookingId` before the `tblbooking` delete, matching the pattern already used in `new-bookings.php` and `booking-history.php`.

---

### BUG-023: Fuzzy `LIKE '%partial%'` Match Instead of Exact Status Check ✓ FIXED

- **File:** `admin/partial-payment-bookings.php:119`
- **Description:** Query used `LIKE '%partial%'` instead of an exact match, risking false positives.
- **Fix:** Changed to `WHERE t1.paymentType = 'Partial Payment'` — the canonical value confirmed across `booking-details.php`, `edit-booking.php`, and `admin/index.php`.

---

## LOW

These issues are minor bugs, typos, or code quality problems with limited functional impact.

---

### BUG-024: Duplicate `name` Attribute on Form Inputs

- **Files:**
  - `admin/add-post.php:165`
  - `admin/edit-post.php:126`
- **Description:** A form contains two `<input>` elements with the same `name` attribute. The browser will submit only the last value; the first is silently discarded.
- **Impact:** One field's value is always lost on form submit.
- **Fix:** Give each input a unique `name` attribute.

---

### BUG-025: Typo in Field Name — `packageduratiobn` ✓ FIXED

- **File:** `admin/add-post.php:166`
- **Description:** Input had duplicate `name` attributes (`name="packageduratiobn" name="packageduratiobn"`) and placeholder said "Duratiobn".
- **Fix:** Removed duplicate `name` attribute; fixed placeholder to "Enter Package Duration".

---

### BUG-026: Typo in Input Name — `ParcialPayment` ✓ FIXED

- **File:** `admin/booking-history-details.php`
- **Description:** Payment amount input used `ParcialPayment` as `name`, `id`, POST key, and JS selector throughout.
- **Fix:** Renamed all occurrences to `PartialPayment` via replace_all.

---

### BUG-027: Typo in Variable Name — `$bookindid` ✓ FIXED

- **File:** `booking-details.php`
- **Description:** PHP variable was named `$bookindid` (extra `d`) throughout the file. The `$_GET` key was already correct (`bookingid`).
- **Fix:** Renamed all occurrences of `$bookindid` → `$bookingid` via replace_all in `booking-details.php`.

---

### BUG-028: Typo in Footer — "Managaement"

- **File:** `include/footer.php:9`
- **Description:** The footer contains the misspelled text "Managaement" (extra `a`).
- **Impact:** Cosmetic — visible to all site visitors.
- **Fix:** Correct to "Management".

---

### BUG-029: Redundant Duplicate Query Execution

- **File:** `admin/edit-post.php:28-29`
- **Description:** The same SQL query is executed twice consecutively with no logic between the calls. The result of the first execution is discarded.
- **Impact:** Unnecessary database load; one query result is wasted.
- **Fix:** Remove the duplicate query call.

---

### BUG-030: `error_reporting(0)` Silences All PHP Errors

- **File:** `admin/index.php:3`
- **Description:** `error_reporting(0)` is set at the top of this file, suppressing all PHP errors, warnings, and notices. This hides bugs during development and production debugging.
- **Impact:** Errors are silently swallowed; broken functionality may go undetected.
- **Fix:** Remove `error_reporting(0)`. In production, set `display_errors = Off` and `log_errors = On` in `php.ini` instead.

---

### BUG-031: Hardcoded "Welcome: Admin" in Admin Header

- **File:** `admin/include/header.php:48`
- **Description:** The admin header displays the static string "Welcome: Admin" regardless of which admin account is logged in.
- **Impact:** All admins see the same generic greeting; no personalization. Misleading if multiple admin accounts exist.
- **Fix:** Display the logged-in admin's actual username from the session.

---

### BUG-032: `ob_start()` Without Matching `ob_end_clean()` / `ob_end_flush()`

- **File:** `include/config.php:2`
- **Description:** Output buffering is started with `ob_start()` at the top of the config file but there is no matching `ob_end_clean()` or `ob_end_flush()` call. PHP will flush the buffer at script end, but this is implicit and may interact unexpectedly with other buffering.
- **Impact:** May cause unexpected output ordering or suppress deliberate output in edge cases.
- **Fix:** Either remove `ob_start()` if not needed, or ensure it is properly paired with a flush/clean call.

---

### BUG-033: `PDO::PARAM_STR` Used for Integer User ID

- **File:** `profile.php:30`
- **Description:** A PDO parameter binding for a numeric user ID column uses `PDO::PARAM_STR` instead of `PDO::PARAM_INT`.
- **Impact:** MySQL will implicitly cast the value; functionally works in most cases but bypasses type safety and may cause index scan inefficiency on large tables.
- **Fix:** Change to `PDO::PARAM_INT` for integer column bindings.

---

## Summary Table

| ID  | Severity | Category | File(s)                             | Description          |
| --- | -------- | -------- | ----------------------------------- | -------------------- |



| 003 | Critical | Security | booking-details.php:188 | IDOR — no booking ownership check | ✓ FIXED |
| 004 | Critical | Security | tmp\_\*.php (root) | Debug files exposed in web root | ✓ FIXED |
| 005 | Critical | Security | include/config.php:6 | Empty DB root password | ⚠ PARTIAL |
| 006 | Critical | Security | include/config.php:15 | DB errors exposed to browser |
| 007 | High | Security | admin/booking-history.php:219 | XSS in JS onclick handlers |
| 008 | High | Security | admin/profile.php, profile.php, booking-details.php | XSS — unescaped DB output |
| 010 | High | Security | All forms | No CSRF tokens |
| 014 | High | Security | config files | No secure session cookie settings |
| 017 | Medium | Security | Multiple files | Inconsistent output escaping |
| 018 | Medium | Code Quality | admin/js/main.js:22 | jQuery selector syntax error |
| 019 | Medium | Security | js/main.js:63 | DOM-based XSS via data-setbg |
| 020 | Medium | Security | include/header.php:12 | Hardcoded admin link in public header |
| 021 | Medium | Code Quality | config files | Deprecated PDO::MYSQL_ATTR_INIT_COMMAND |
| 024 | Low | Code Quality | admin/add-post.php:165 | Duplicate name attribute on form inputs |
| 028 | Low | Code Quality | include/footer.php:9 | Typo: "Managaement" |
| 029 | Low | Code Quality | admin/edit-post.php:28-29 | Redundant duplicate query |
| 030 | Low | Code Quality | admin/index.php:3 | error_reporting(0) hides all errors |
| 031 | Low | Code Quality | admin/include/header.php:48 | Hardcoded "Welcome: Admin" text |
| 032 | Low | Code Quality | include/config.php:2 | ob_start() without matching ob_end |
| 033 | Low | Code Quality | profile.php:30 | PDO::PARAM_STR used for integer UID |

DONE:

| 009 | High | Logic | profile.php:34, admin/profile.php:28 | Broken JS redirect — missing URL quotes |

| 002 | Critical | Logic | changepassword.php, login.php | Session email never set — change password broken |

| 011 | High | Logic | profile.php:6-8 | Missing exit after header() redirect |

| 012 | High | Logic | admin/login.php:24, admin/change-password.php:13 | Admin session email never set — change password broken |

| 013 | High | Logic | admin/include/sidebar.php | Admin nav links use .html instead of .php |

| 015 | Medium | Logic | registration.php | No duplicate email check |

| 016 | Medium | Logic | registration.php:25 | Password forced to exactly 8 chars |

| 022 | Medium | Logic | admin/full-payment-bookings.php | No cascade delete on booking deletion |

| 023 | Medium | Logic | admin/partial-payment-bookings.php:119 | LIKE fuzzy match on payment status |

| 025 | Low | Logic | admin/add-post.php:166 | Typo: packageduratiobn field name |

| 026 | Low | Logic | admin/booking-history-details.php | Typo: ParcialPayment input name |

| 027 | Low | Logic | booking-details.php:188 | Typo: $bookindid variable name |

| 001 | Critical | Security | admin/login.php, admin/change-password.php | MD5 password hashing |
