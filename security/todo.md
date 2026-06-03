# Security & Code-Quality TODO

Items discovered during security review. Entries are grouped by severity.
Add new findings here as they are discovered; remove or move to CHANGELOG when fixed.

---

## Security vulnerabilities

### S1. SSRF via iCal URL import — no protocol or IP restriction

**File:** `component/site/libraries/iCalImport.php` lines 113–129
**Access required:** Joomla administrator (or frontend user with `feimport=1` enabled)
**Severity:** High for cloud-hosted deployments; Medium for on-premises

The iCal calendar subscription feature fetches a user-supplied URL with `curl` and, if curl fails, falls back to `file_get_contents()`. Neither call restricts the protocol or destination address.

**What is missing:**

```php
// current — no restrictions
curl_setopt($ch, CURLOPT_URL, $file);              // any scheme: file://, ftp://, gopher://
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);       // follows redirects to internal addresses
$this->rawData = curl_exec($ch);
if ($this->rawData === false || $this->rawData == "") {
    $this->rawData = @file_get_contents($file);    // file:// works here too
}
```

**Attack scenarios:**
- Admin supplies `file:///etc/passwd` → curl may block this, but `file_get_contents()` fallback reads it and the content lands in the DB
- Admin supplies `http://169.254.169.254/latest/meta-data/` → exposes AWS/GCP/Azure instance credentials on cloud hosts
- Admin supplies an internal address (e.g. `http://10.0.0.1:8080/`) to probe private services; `CURLOPT_FOLLOWLOCATION` allows open-redirect chains to reach internal targets

**Note on `webcal://`:** `iCalICSFile.php` (lines 81–97) converts `webcal://` → `http://` and `webcals://` → `https://` *before* the URL reaches `iCalImport`. By the time curl is called the scheme is already `http` or `https`, so `CURLPROTO_HTTP | CURLPROTO_HTTPS` is the complete and correct whitelist — no `webcal` constant is needed.

**Recommended fix:**
```php
// 1. Validate scheme BEFORE setting CURLOPT_URL
$parsed = parse_url($file);
$scheme = $parsed['scheme'] ?? '';
if (!in_array($scheme, ['http', 'https'], true)) {
    // webcal:// is already rewritten to http(s):// upstream in iCalICSFile.php
    throw new \InvalidArgumentException('URL scheme not permitted');
}

// 2. Restrict curl to http/https only (belt-and-braces after validation)
curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
curl_setopt($ch, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);

curl_setopt($ch, CURLOPT_URL, $file);   // set URL after validation

// 3. Disable redirect following (prevents open-redirect SSRF chains)
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);

// 4. Add timeouts
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

// 5. Remove the file_get_contents() fallback entirely — it has no protocol restriction
```

The fsockopen fallback (lines 148–157) already whitelists `http/https/webcal` correctly; the curl path needs the same treatment applied first.

---

### S2. `gwejson` plugin — `$folder`/`$plugin` parameters not sanitised before file include

**File:** `plugins/gwejson/gwejson.php` lines 108–156
**Access required:** Any user with a valid Joomla session token (effectively unauthenticated)
**Severity:** Medium (constrained by filename prefix requirement and file-existence check)

The gwejson fast-JSON endpoint builds a file path from user-supplied `folder` and `plugin` GET parameters using Joomla's `'string'` input filter, which does **not** strip `../` sequences. The constructed path is passed to `file_exists()` and then `include_once()`.

**The constraint that limits impact:** the `file` parameter (filtered by `'cmd'`, which strips `/`) is forced to start with `gwejson_`, so only a file named `gwejson_<alphanumeric>.php` can be included. System files and arbitrary PHP files are unreachable unless one happens to have that naming pattern.

**The factor that makes it worse:** `include_once()` fires **before** the session token is validated:

```php
include_once($path . $file . ".php");   // line 156 — runs immediately

// token check happens after:
if (!function_exists("gwejson_skiptoken") || !gwejson_skiptoken()) {
    if ($token != $input->get('token', '', 'string')) {
        PlgSystemGwejson::throwerror("bad token");
    }
}
```

If the included file defines `gwejson_skiptoken()` returning `true`, the token check is skipped for all subsequent gwejson requests in that request cycle.

**Recommended fix:**
```php
// Use 'cmd' filter on folder and plugin, not 'string'
$folder = $input->get('folder', '', 'cmd');   // strips ../
$plugin = $input->get('plugin', '', 'cmd');   // strips ../

// Or explicitly validate after construction:
$resolved = realpath($path . $file . ".php");
$base     = realpath($paths[$pathKey]);
if ($resolved === false || strpos($resolved, $base) !== 0) {
    PlgSystemGwejson::throwerror("Invalid path");
    return true;
}

// Move the token check BEFORE the include
```

---

### S3. Client-side-only MIME validation on iCal/CSV file uploads

**File:** `component/site/libraries/iCalImport.php` lines 75–84
**Access required:** Administrator or front-end event creator
**Severity:** Low (files are not written to webroot; no direct RCE path)

The upload handler trusts `$_FILES['upload']['type']`, which is supplied by the browser and trivially spoofable. It accepts `application/octet-stream` and `text/html` in addition to `text/calendar` and `text/csv`. No server-side MIME detection (`finfo`) or file-extension whitelist is applied.

In the current flow, uploaded files are read from the PHP temp directory and parsed in memory — they are never written to a web-accessible location — so this is not an RCE vector in a standard configuration. The risk is that malformed or malicious content bypasses the intended type gate and reaches the iCal parser.

**Recommended fix:**
```php
// Replace client-side MIME check with server-side detection
$finfo    = new \finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($uploadfile['tmp_name']);
$allowed  = ['text/calendar', 'text/csv', 'text/plain'];
if (!in_array($mimeType, $allowed, true)) {
    // reject
}

// Also whitelist extensions
$ext = strtolower(pathinfo($uploadfile['name'], PATHINFO_EXTENSION));
if (!in_array($ext, ['ics', 'ical', 'csv'], true)) {
    // reject
}
```

---

### S4. Uploaded iCal filename stored unsanitised in database

**File:** `component/site/libraries/iCalICSFile.php` line 140
**Access required:** Administrator or front-end event creator
**Severity:** Low

```php
$temp->filename = $file['name'];   // raw browser-supplied filename
```

`$_FILES['upload']['name']` is a client-controlled string. It is stored verbatim in `#__jevents_icsfile.filename`. If this value is later:
- **echoed unescaped** in admin views → stored XSS in the admin panel
- **used to construct a filesystem path** → path traversal

Check all places where `filename` from `#__jevents_icsfile` is output in a template or used in file operations, and ensure `htmlspecialchars()` / `basename()` is applied at those points.

**Recommended fix at storage time:**
```php
$temp->filename = basename($file['name']);   // strip any directory components
```

---

## Bugs to fix (not security vulnerabilities, but should be corrected)

### 1. `Savedfilters.php` — `$db->quote()` swallows `modid` condition

**Files:** `component/site/libraries/filters/Savedfilters.php` lines 58 and 111
**Impact:** Logic / UX — users see their own saved filters from *all* filter module instances on the site, not just the one they are interacting with. No cross-user data exposure (MySQL's implicit integer cast preserves the `userid` constraint by accident).

**Root cause:** Both the user ID and the modid condition are concatenated into a single string and passed as one argument to `$db->quote()`, so the entire value is wrapped in quotes and the `modid` clause is treated as string content rather than SQL.

```php
// Current (broken) — modid is inside the quoted string, ignored by MySQL
$db->quote(Factory::getUser()->id . " and modid=" . $activemodid)

// Fixed — two separate typed conditions
"WHERE userid = " . intval(Factory::getUser()->id) . " AND modid = " . intval($activemodid)
```

Fix required in both `_createfilterHTML()` (line 58) and `_createfilterHTMLUIkit()` (line 111).

---

### 2. `Save.php` — `_createfilterHtmlUIkit()` still uses deprecated `$app->activeModule`

**File:** `component/site/libraries/filters/Save.php`
**Impact:** PHP deprecation notice; dynamic property access will throw in a future PHP version.

The sibling method `_createfilterHTML()` was already updated. `_createfilterHtmlUIkit()` was missed. Update to use the same pattern as the corrected method.

---

## Good hygiene improvements (low priority)

### 3. `deletefilter` GET parameter — no CSRF token

**File:** `component/site/libraries/filters/Save.php` (called via `helper.php` `getFilterValues()`)
**Impact:** An attacker can craft a link that, when clicked by a logged-in user, deletes one of that user's saved filter preferences. Only the victim's own data is affected; no privilege escalation is possible.

The `deletefilter` value is read from request input on every JEvents page load and triggers a DELETE if non-zero, with no `Session::checkToken()` call.

**Recommendation:** Add a token to the delete link generated in `_createfilterHTML()` / `_createfilterHTMLUIkit()` and verify it server-side before executing the DELETE. Alternatively, require the delete to arrive via POST only and add a token to the form.

Pre-existing since 2016 — not introduced by recent changes.

---

## Completed / closed

_(Move items here when resolved, with the version that fixed them.)_