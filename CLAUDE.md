# Project: JEvents

## What this is
A Joomla 5.4/6.x extension consisting of:
- **Component**: 
- com_jevents (site + admin MVC)
- JEvents is a long established Events calendar and listing addon for Joomla - and was first released in 2005 when Joomla was born!  JEvents consists of a variety of component views, modules and plugins for Joomla!.
- It is highly configurable and customisable with a long list of addons available to extend its functionality. 
- The Calendar/Event Lists
  - Create single events or powerful repeating events. From a daily repeat to a yearly repeat, edit/change dates and times of the individual repeats from the main event.
  - Repeating event exception handling - you can edit individual repeats or delete them 
  - Several free themes and more club member themes available to allow JEvents  to blend into your site. 
  - Full Joomla! Template override support, you can customise the our templates to the finest detail. 
  - Fully compatible with Joomla MVC framework 
  - iCal based importing / exporting, configurable export page to allow webcal (Calendar subscriptions)
  - CSV based importing of events 
  - 18 Custom JEvents module positions! for positioning modules within JEvents. 
  - Events can be placed in multiple categories and menu items can be customised to show events from all or some of these categories 
  - Layout editing tool to perfect the presentation on your site - you can even customise the event editing page. 
  - Optional checking for overlapping events globally or on a category specific basis
- Modules
  - Mini-calendar module which gives a quick overview of events which can be placed on any page 
  - Latest events module which gives a highly configurable summary of upcoming, recently created or popular events 
  - Events legend - displayed alongside the component gives you a summary of event categories and an easy way to limit the events shown to specific categories 
  - Filter module allows you to filter/search the events being displayed using a variety of criteria
- Plugins
  - A search plugin that enables the global Joomla search to return results from the events calendar 
  - A smart search plugin that allows you to provide the best User experience to your visitors thanks to Joomla! Smart search.

- **Plugins**: 
- `plugins/actionlog/` — Support for Joomla action logs
- `plugins/finter/` — Support for Joomla finder
- `plugins/gwejson/` — System plugin to allow fast JSON interaction avoiding full Joomla stack overhead
- `plugins/jevents/` — content plugin for JEvents
- `plugins/jeventsyootheme/` — Support for YOOtheme
- `plugins/search/` — Support for old Joomla search plugins 
- **Modules**: 
- `modules/mod_jevents_cal/` — mini-calendar module
- `modules/mod_jevents_custom/` — Displays data from customised event detail or event list layouts
- `modules/mod_jevents_dashboard/` — Displays quick links to actions/views in JEvents and its Extensions in backend of Joomla
- `modules/mod_jevents_filter/` — module to display and drive events filtering module
- `modules/mod_jevents_latest/` — Show latest events for Events component 

## Tech stack
- PHP 8.3+, Joomla 6 framework
- Needs backwards compatibility to Joomla 5.4
- MySQL via Joomla's DatabaseDriver
- JS (vanilla/jQuery), CSS
- Joomla's MVC, Form API, ACL, Events system

## Key architectural decisions

### Custom database abstraction layer (not standard Joomla)

JEvents does **not** follow the standard Joomla pattern where each model builds its own SQL queries. Instead it uses a three-layer separation:

```
Controller
    │
    ▼
JEventsDataModel          — presentation / aggregation layer
    │  (component/site/libraries/datamodel.php)
    │  Aggregates data for calendar views (month, week, day, year, range, cat)
    │  Handles URL params, category constraints, keyword filtering, pagination
    │
    ▼
JEventsDBModel            — query construction / caching layer
    │  (component/site/libraries/dbmodel.php  ~5200 lines)
    │  ALL SQL lives here — not in individual models
    │  Converts raw rows → jIcalEventRepeat domain objects
    │  Implements two-level caching (per-request static + Joomla cache)
    │
    └─► JEventsAdminDBModel   — extends JEventsDBModel, admin-specific queries
           (component/admin/libraries/adminqueries.php)
```

**Initialization** — controllers wire the layers together explicitly:

```php
$this->dataModel  = new JEventsDataModel("JEventsAdminDBModel");
$this->queryModel = new JEventsDBModel($this->dataModel);
$model->queryModel = $this->queryModel;   // passed into list models
```

Passing `"JEventsAdminDBModel"` to the DataModel constructor swaps in the admin query set; the site front-end defaults to `JEventsDBModel`.

**Responsibilities by layer:**

| Layer | Responsibility |
|---|---|
| `JEventsDataModel` | Calendar data assembly (`getCalendarData()`, `getWeekData()`, …), category setup, filter co-ordination |
| `JEventsDBModel` | All SQL (`listIcalEventsByMonth()`, `listIcalEventsByRange()`, …), `accessibleCategoryList()` for ACL-aware WHERE fragments, object instantiation via `_cachedlistIcalEvents()` |
| Concrete models | Thin wrappers; delegate to `$this->queryModel->methodName()` rather than writing SQL |

**Query reuse mechanisms:**

1. **SQL fragment methods** — `accessibleCategoryList()` returns a permission-filtered `IN(…)` clause that is embedded into every event-listing query. Centralises ACL in one place.
2. **Per-request static cache** — `getCategoryInfo()` / `getChildCategories()` use `static $instances` arrays so the same category query never executes twice per request.
3. **Joomla cache integration** — `_cachedlistIcalEvents()` is the single conversion point from `stdClass` rows to `jIcalEventRepeat` objects; it is called through Joomla's cache so results survive across requests.
4. **Composable JOIN/WHERE arrays** — `listIcalEvents()` and related methods accept `$extrajoin[]` / `$extrawhere[]` arrays assembled by callers, then `implode()` them into the final SQL. Callers add clauses without touching the core query.

**Plugin-extensible filter system:**

Plugins can inject additional WHERE/JOIN clauses without modifying DBModel:

```php
$app->triggerEvent('onListIcalEvents',
    [&$extrafields, &$extratables, &$extrawhere, &$extrajoin, &$needsgroup]);
```

The `jevFilterProcessing` class co-ordinates these injections before the query is executed.

**Domain object: `jIcalEventRepeat`**

All query results are instantiated into `jIcalEventRepeat` (extends `jIcalEventDB` → `jEventCal`). Never work with raw `stdClass` rows from event queries — use the domain object's methods (`title()`, `location()`, `alldayevent()`, `publish_up()`, `checkRepeatMonth()`, etc.).

**When writing new query code:**
- Add new SQL methods to `JEventsDBModel` or `JEventsAdminDBModel`, not to individual models.
- Reuse `accessibleCategoryList()` in any query that filters by category.
- Return `jIcalEventRepeat` objects, not raw rows.
- Use the `$extrajoin` / `$extrawhere` array pattern for optional clauses.

### View helper system and theme customisation

#### Helper files

Each theme has a `helpers/` directory containing small, single-purpose PHP files. Helpers come in two forms:

- **Functions** — named `{Theme}ViewHelperName`, e.g. `DefaultViewHelperHeader($view)`
- **Classes** — named `{Theme}ViewNavTableBarIconic`, instantiated by the theme's abstract view

The `default` theme's `abstract.php` registers two helper search paths:

```php
// 1. The theme's own helpers directory (highest priority)
$this->addHelperPath(".../views/default/helpers");

// 2. Active Joomla template override path (fallback)
$this->addHelperPath("templates/<template>/html/com_jevents/helpers");
```

When `$view->loadHelper("DefaultViewNavTableBarIconic")` is called:
1. If the class/function already exists in memory → return immediately (no double-load)
2. Search registered paths in order → `require_once` the first match found

#### Customising a helper per theme

Three patterns, in order of invasiveness:

| Pattern | When to use | Example |
|---|---|---|
| **Full replacement** | Theme behaviour is completely different | `AlternativeViewNavTableBarIconic` — fully independent class |
| **Extension** | Theme overrides only some behaviour | `GeraintViewNavTableBarIconic extends DefaultViewNavTableBarIconic` — overrides constructor HTML, inherits navigation icon methods |
| **Joomla template override** | Site-specific tweak without forking a theme | Drop a file in `templates/<template>/html/com_jevents/helpers/` — found only if the theme has no helper of that name |

Each theme's `abstract/abstract.php` explicitly names the helper class it instantiates, e.g.:

```php
// ext/abstract/abstract.php
function viewNavTableBarIconic(...) {
    $this->loadHelper("ExtViewNavTableBarIconic");
    $var = new ExtViewNavTableBarIconic($this, ...);
}
```

This means swapping a helper for a theme requires both: the file in `helpers/` AND the wiring in `abstract.php`.

#### `DefaultLoadedFromTemplate` — the event layout engine

`component/site/views/default/helpers/defaultloadedfromtemplate.php` contains a single large function `DefaultLoadedFromTemplate()`. This is the core rendering engine for **all event layouts** (list rows, calendar cells, event detail pages, etc.). It is not a standard Joomla pattern.

**What it does:**

1. **Resolves which template to use** for a given `$template_name` (e.g. `icalevent.detail_body`, `icalevent.list_row`, `month.calendar_cell`). Resolution order:
   - `#__jev_defaults` DB table, filtered by current language then category
   - Falls back to `.html` file: `templates/<template>/html/com_jevents/<viewname>/defaults/<name>.html`
   - Falls back to: `templates/<template>/html/com_jevents/defaults/<name>.html`
   - Falls back to: `<theme>/views/defaults/<name>.html`
   - Falls back to: admin `views/defaults/tmpl/<name>.html`
   - Returns `false` if nothing found

2. **Per-category template selection** — templates can be configured per category in `#__jev_defaults`. The function walks the event's categories (including multi-category assignments) and checks parent categories (up to two levels) before falling back to the global `catid=0` default.

3. **Per-language templates** — templates stored with a specific language tag take precedence over wildcard `*` entries.

4. **`{{TOKEN}}` substitution** — the template body contains `{{TOKEN}}` placeholders. The function resolves ~80 built-in tokens including:
   - Event data: `{{TITLE}}`, `{{DESCRIPTION}}`, `{{LINK}}`, `{{STARTDATE}}`, `{{ENDDATE}}`, `{{STARTTIME}}`, `{{ENDTIME}}`, `{{COLOUR}}`, `{{CATEGORY}}`, etc.
   - Computed: `{{COUNTDOWN}}`, `{{DURATION}}`, `{{REPEATSUMMARY}}`, `{{ISOSTART}}`/`{{ISOEND}}`
   - UI elements: `{{EDITBUTTON}}`, `{{ICALBUTTON}}`, `{{MANAGEMENT}}`
   - Translation strings: `{{_LANG_KEY}}` → `Text::_('LANG_KEY')`
   - Custom plugin fields: resolved by the `jevents` plugin group before this function runs

5. **Module embedding** — template params can specify Joomla module IDs whose output is injected via `{{MODULESTART#id}}` / `{{MODULEEND}}` markers.

6. **Custom CSS/JS** — templates can embed `{{CUSTOMCSS}}...{{/CUSTOMCSS}}` and `{{CUSTOMJS}}...{{/CUSTOMJS}}` blocks; these are extracted and injected into the document once per request (deduped with `static` arrays).

7. **`static` caching throughout** — category lookups, template DB rows, and processed CSS/JS are all cached in `static` variables so the function is safe to call once per event in a list without repeated queries.

**When writing new layout tokens or modifying event display:**
- Add new `{{TOKEN}}` cases to the large `switch` in `DefaultLoadedFromTemplate()`
- Do NOT output event HTML directly from view files where a layout template could be used instead
- The function returns `false` when no template is found — callers must handle this gracefully
- `$template_name` values correspond to rows in `#__jev_defaults` — the admin UI manages these

## MVC Flow Map

### Database tables (shared by admin and site)

```
#__jevents_vevent        — master event records
#__jevents_vevdetail     — event detail / translations per language
#__jevents_repetition    — individual occurrences of each event
#__jevents_rrule         — recurrence rules
#__jevents_exception     — edited / deleted individual occurrences
#__jevents_icsfile       — iCal calendar source files
#__jev_defaults          — custom layout/template defaults
#__jev_users             — JEvents-specific user permissions
#__categories            — Joomla categories (extension='com_jevents')
#__jevents_catmap        — event → category mappings (multi-category mode)
```

### Admin side (`component/admin/`)

```
Request
  │
  ▼
Controller
  ├─ cpanel        ──────────────────────────────► view: cpanel
  │                                                  tmpl: cpanel.php, support.php
  │
  ├─ icalevent     ──► model: icalevent            ► view: icalevent
  │                    (vevent + vevdetail +          tmpl: overview.php, edit.php,
  │                     repetition + rrule)                  select.php, csvimport.php,
  │                                                          translate.php
  │
  ├─ icalrepeat    ──► model: icalevent            ► view: icalrepeat
  │                    (repetition + vevdetail +     tmpl: overview.php, edit.php,
  │                     exception)                          edit_datetime.php, select.php
  │
  ├─ icals         ──► model: ical                 ► view: icals
  │                    (icsfile table)               tmpl: overview.php, edit.php
  │
  ├─ defaults      ──► model: defaults             ► view: defaults
  │                    (jev_defaults table)          tmpl: overview.php, edit.php,
  │                                                         edit_icalevent.*.php,
  │                                                         edit_month.*.php, etc.
  │
  ├─ params        ──► model: params               ► view: params
  │                    (extension table)             tmpl: edit.php, edit2.php,
  │                                                         dbsetup.php
  │
  ├─ user          ──► model: user                 ► view: user
  │                    (jev_users table)             tmpl: overview.php, edit.php
  │
  ├─ customcss     ──► model: customcss            ► view: customcss
  │                    (CSS files on disk)           tmpl: default.php
  │
  ├─ import        ──────────────────────────────► view: import
  │                                                  tmpl: default.php
  │
  └─ plugin        ──► delegates via onJEventsPluginController event
```

### Site side (`component/site/`)

```
Request → router.php (SEO URL parsing)
  │
  ▼
Controller
  ├─ month / week / day / year / range
  │    └──► (no dedicated model; data fetched via JEventsDataModel
  │           → JEventsDBModel → vevent + repetition + vevdetail)
  │         Theme-based views (default / flat / alternative / ext / geraint)
  │           tmpl: month.php, week.php, day.php, year.php, range.php
  │
  ├─ cat / list / search
  │    └──► theme views: cat.php / list layout / search.php
  │
  ├─ icalevent     ──► model: icalevent            ► view: icalevent
  │                                                  tmpl: default.php, details.php
  │
  ├─ icalrepeat    ──────────────────────────────► view: icalrepeat (occurrence detail)
  │
  ├─ icals         ──────────────────────────────► iCal feed export (text/calendar)
  │
  ├─ jevent        ──────────────────────────────► theme view: jevent
  │                                                  tmpl: detail.php, link.php
  │
  ├─ admin         ──► delegates to admin MVC for front-end event editing
  │
  ├─ getjson       ──► JSON API (AJAX, avoids full Joomla stack)
  │
  ├─ crawler       ──────────────────────────────► view: crawler / tmpl: events.php
  │
  ├─ modcal        ──► supports mod_jevents_cal module rendering
  └─ modlatest     ──► supports mod_jevents_latest module rendering
```

### Theme system

Site views use a pluggable theme layer. The active theme is selected via component parameters. Each theme lives under `component/site/views/<theme>/` and provides its own tmpl files. Bundled themes: **default**, **flat**, **alternative**, **ext**, **geraint**. Joomla template overrides are also supported on top of any theme.

## File structure overview
- Files here are softlinked into Joomla installation at /black/var/www/clients/j6 
- `component/site/` — frontend MVC
- `component/admin` — backend MVC
- `component/media` — media files
- `libraries` — libraries used by JEvents and its addons
- `plugins/actionlog/` — Support for Joomla action logs
- `plugins/finter/` — Support for Joomla finder 
- `plugins/gwejson/` — System plugin to allow fast JSON interaction avoiding full Joomla stack overhead
- `plugins/jevents/` — content plugin for JEvents
- `plugins/jeventsyootheme/` — Support for YOOtheme
- `plugins/search/` — Support for old Joomla search plugins
- `modules/mod_jevents_cal/` — mini-calendar module
- `modules/mod_jevents_custom/` — Displays data from customised event detail or event list layouts
- `modules/mod_jevents_dashboard/` — Displays quick links to actions/views in JEvents and its Extensions in backend of Joomla
- `modules/mod_jevents_filter/` — module to display and drive events filtering module
- `modules/mod_jevents_latest/` — Show latest events for Events component


## Coding Standards

- This project has evolved over time from Joomla 1.5 and earlier so some existing code may not be up to modern standards.
- These are goals of where I'm aiming
- Target **Joomla 6** coding patterns and APIs as the primary standard
- Maintain **backwards compatibility with Joomla 5.4** — do not use any
  API, class, or method introduced after Joomla 5.4 without a fallback
- **Namespacing**: The codebase currently uses legacy non-namespaced MVC layout (Joomla 3 style). The goal is to migrate fully to namespaced `src/` MVC structure. Rules:
  - Do not create new files using the legacy layout
  - Use namespaced `src/` structure for all new code
  - When touching existing legacy files, leave them in their current style unless the task specifically involves migration
- Follow Joomla Coding Standards (https://developer.joomla.org/coding-standards)
- PHP 8.3+ syntax is fine; avoid features not available in PHP 8.2
- When deprecations differ between J5.4 and J6, note this in a code comment

# Strict Boundaries — Read Carefully

Claude must ONLY modify, or create files within these paths under /black/var/www/clients/gitjquery/JEvents/:
- `component`
- `libraries`
- `plugins`
- `modules`

Claude can read files within /black/var/www/clients/j6 (the live Joomla installation).
IMPORTANT: Only read from these two full paths — do NOT attempt to read or list /black/var/www/clients/ itself:
- `/black/var/www/clients/gitjquery/JEvents/` — the JEvents source
- `/black/var/www/clients/j6/` — Joomla core (read-only reference, e.g. for framework class implementations)

### NEVER modify:
- Any Joomla core files within /black/var/www/clients/j6 (`libraries/`, `includes/`,
  `administrator/includes/`, `index.php` etc.)
- Any third-party extensions not listed above
- `configuration.php`
- `.htaccess` or `web.config`
- Any file outside the paths listed above

If a task seems to require changing files outside these paths,
STOP and tell the user instead of making the change.

---

## Plugin audit — bundled plugins

### `actionlog/jevents` — Audit logging

Hooks into JEvents-specific events (not standard Joomla action-log events) and writes to `#__action_logs`.

| Hook | What it does | DB write |
|---|---|---|
| `onAfterSaveEvent($event, $dryrun)` | Logs event create or update; skips if `$dryrun=true` | INSERT `#__action_logs` |
| `onPublishEvent($ids, $state)` | Logs publish/unpublish/trash state change | INSERT `#__action_logs` |
| `onAfterDeleteEvent($events[])` | Logs deletion of each event in the array | INSERT `#__action_logs` |
| `onAfterDeleteEventRepeat($event)` | Logs deletion of a single repeat occurrence | INSERT `#__action_logs` |
| `onAfterStoreRepeatException($data)` | Logs an edited occurrence being stored as an exception | INSERT `#__action_logs` |
| `afterSaveUser($user)` | Logs JEvents-specific user account add/update | INSERT `#__action_logs` |
| `onAfterRemoveUser($users[])` | Logs removal of JEvents user accounts | INSERT `#__action_logs` |
| `onSaveTranslation($data, $success)` | Stub — not yet implemented | None |

All writes go through a shared `addLog()` helper that stores a JSON message blob, user ID, IP address (configurable), and item ID.

---

### `finder/jevents` — Smart Search indexer

Maintains the Joomla Smart Search (`#__finder_links`) index for JEvents events. File `jevents.php` auto-selects `jevents3.php` or `jevents4.php` based on Joomla version.

| Hook | What it does | DB side-effects |
|---|---|---|
| `onAfterSaveEvent(&$vevent, $dryrun)` | Re-indexes event after save; skips dryrun | Updates Finder index |
| `onPublishEvent($ids, $newstate)` | Triggers re-index for each affected event | Updates Finder index |
| `onFinderAfterSave($context, $row, $isNew)` | Re-indexes on `com_jevents.event` / `com_jevents.form` context; handles access-level changes | Updates Finder index |
| `onFinderAfterDelete($context, $table)` | Removes event from Finder index | Deletes Finder index entry |
| `onFinderChangeState($context, $pks, $value)` | Syncs Finder publication state | Updates Finder index |
| `onFinderGarbageCollection()` (J4) | Removes stale index entries for deleted events | Deletes orphaned Finder rows |
| `onFinderResult(&$result, &$query)` (J4) | Enriches Finder result objects with event data; fires `onJEventsFinderResult` | None |

The internal `index()` method joins `#__jevents_vevdetail`, `#__jevents_repetition`, `#__jevents_vevent`, `#__categories`, `#__users` and fires `onJevFinderIndexing` to allow add-ons to enrich the indexed item before it is committed to the Finder store.

---

### `system/gwejson` — Fast JSON endpoint

Intercepts requests with `task=gwejson` in `onAfterInitialise()` **before** the full Joomla dispatch cycle, includes a named file from the component/plugin/module tree, calls `ProcessJsonRequest()` from that file, outputs JSON, and calls `exit()`. This avoids the full MVC stack for AJAX calls.

Security: validates a Joomla session token (unless the included file declares `gwejson_skiptoken()`). Only includes files whose name starts with `gwejson_`.

`onContentPrepareForm()` injects theme-specific XML field definitions into menu item and module forms by scanning `views/*/menuconfig.xml` files.

`onInstallerBeforePackageDownload()` rewrites JEvents update download URLs to append the site's club licence code.

---

### `content/jevents` — Category protection

| Hook | What it does | DB write |
|---|---|---|
| `onContentBeforeSave($context, $data)` | Blocks trashing/archiving a category that still has events; returns `false` to veto the save and enqueues a warning | None |
| `onCategoryChangeState($extension, $pks, $value)` | If a category with events is being trashed/archived, reverts it to published=1 and warns the user | UPDATE `#__categories` SET published=1 |

Both hooks query `#__jevents_vevent` (and `#__jevents_catmap` in multi-category mode) to detect events in the affected categories.

---

### `search/eventsearch` — Legacy Search plugin

Implements Joomla's old Search API (`onContentSearch`). Builds a large multi-join query across `#__jevents_vevent`, `#__jevents_vevdetail`, `#__jevents_repetition`, `#__jevents_rrule`, `#__jevents_exception`, `#__jevents_icsfile`, `#__categories`, `#__users`. Supports exact/all/any phrase modes and five ordering modes. Temporarily changes PHP's default timezone during the query (restores on exit). Fires `onListIcalEvents` and `onSearchEvents` to let add-ons inject extra WHERE/JOIN clauses into the search query.

---

### `system/jeventsyootheme` — YOOtheme integration

Fires only on `onAfterInitialise()` when the YOOtheme Application class is present. Registers JEvents LESS styles and two listener classes (`StylerListener`, `SettingsListener`) into the YOOtheme customizer pipeline. No DB writes or file mutations; reads a static `config/customizer.json`.

---

## Custom plugin hooks fired by JEvents

JEvents fires its own events using `$app->triggerEvent()`. All are in the `jevents` plugin group unless otherwise noted. The `jevents` plugin group is imported at component bootstrap and in the mini-calendar module.

### Naming convention

All custom events use **by-reference parameters** rather than return values. Plugins mutate the passed arrays/objects in place; callers ignore the return values from `triggerEvent`. The only exception is `onDisplayCustomFields` / `onAfterDisplayContent` where returned HTML strings are collected and rendered.

---

### Event lifecycle hooks

These fire during save and delete operations in `component/admin/libraries/saveIcalEvent.php`, `iCalICSFile.php`, `iCalEvent.php`, and the admin controllers.

| Event | Arguments (by ref unless noted) | When it fires |
|---|---|---|
| `onBeforeSaveEvent` | `&$array, &$rrule, $dryrun` | Before any DB write; `$dryrun=true` for validation-only checks. Plugins can veto or transform event data. |
| `onAfterSaveEvent` | `&$vevent, $dryrun` | After event + all repetitions are written. Skipped on dryrun. |
| `onStoreCustomEvent` | `&$event` | After the main event row is stored; use to write to addon-specific tables. |
| `onStoreCustomDetails` | `&$eventDetail` | After event detail (vevdetail) row is stored. |
| `onDeleteCustomEvent` | `&$veventIdString` | When event is being deleted; clean up addon tables keyed on event ID. |
| `onDeleteEventDetails` | `$detailIdString` | When vevdetail rows are being deleted (comma-separated IDs). |
| `onCleanCustomDetails` | `$detailIds[]` | Bulk cleanup of custom detail data. |
| `onAfterDeleteEvent` | `&$events[]` | After all event data is removed; final cleanup. |

---

### Repeat / occurrence hooks

Fired from `component/admin/controllers/icalrepeat.php`.

| Event | Arguments | When it fires |
|---|---|---|
| `onStoreCustomRepeat` | `&$rpt` | After a repeat occurrence row is stored. |
| `onAfterStoreRepeatException` | `&$exceptionData` | After an edited single occurrence is stored as an exception row. |
| `onDeleteEventRepeat` | `$rp_id` | Per-occurrence delete; fired up to three times depending on context (delete one, delete future, delete all). |
| `onAfterDeleteEventRepeat` | `&$event` | After all occurrence deletion processing is complete. |

---

### Publish / state hooks

| Event | Arguments | When it fires |
|---|---|---|
| `onPublishEvent` | `$cid[], $newstate` | When publish state is toggled on one or more events. |
| `afterSaveUser` | `$user` | After a JEvents-specific user permission record is saved. |
| `onAfterRemoveUser` | `$users[]` | After JEvents user records are deleted. |
| `afterSaveCategory` | `$category` | After an event category is saved. |

---

### Event listing / query hooks (most frequently fired)

These fire inside `JEventsDBModel` for every event-listing query. They are the primary extension point for add-ons that store data in separate tables and need to join it into event lists.

| Event | Arguments | When it fires |
|---|---|---|
| `onListIcalEvents` | `&$extrafields, &$extrawhere[], &$extrajoin[], &$extratables, &$needsgroup` | Before every major listing query (month, week, day, year, range, cat, latest, search, …). Plugins append SQL fragments. |
| `onListEventsById` | `&$extrafields, &$extrawhere[], &$extrajoin[], &$extratables` | When fetching events by specific IDs. |
| `onSearchEvents` | `&$extrasearchfields, &$extrajoin[], &$needsgroup` | When building the keyword search query. |
| `fetchListIcalEvents` (and variants) | `&$skipJEvents, &$rows, $startdate, $enddate, $limit, …` | Fired BEFORE JEvents builds a query. If a plugin sets `$skipJEvents=true` and populates `$rows`, JEvents skips its own query entirely. Variants exist for `Recent`, `RecentlyModified`, `Popular`, `Latest`, `Random`, `ByCreator`, `ByKeyword`. |

---

### Custom field display hooks

These allow add-ons to inject HTML into rendered event output.

| Event | Arguments | Return value used? | When it fires |
|---|---|---|---|
| `onDisplayCustomFields` | `&$row` | YES — HTML strings collected and rendered | Single event detail page (once per event) |
| `onDisplayCustomFieldsMultiRow` | `&$rows[]` | YES — HTML per event collected | Bulk processing for event lists |
| `onDisplayCustomFieldsMultiRowUncached` | `&$rows[]` | Not specified | Same as above, bypassing cache |
| `onAfterDisplayContent` | `&$row, &$params, $page` | YES — strings concatenated and echoed | After event detail body is output |

---

### Access control hooks

| Event | Arguments | When it fires |
|---|---|---|
| `onGetAccessibleCategories` | `&$cats[]` | When building the accessible-category list for a query; plugins add/remove entries. |
| `onGetAccessibleCategoriesForEditing` | `&$cats[]` | When loading category choices in an edit form. |
| `onGetCategoryData` | `&$cats[]` | When fetching category metadata. |
| `getAuthorisedUser` | `&$where[], &$join[]` | When querying authorised JEvents users; plugins add SQL clauses. |
| `onIsEventCreator` | `&$isEventCreator` | When checking if the current user owns an event. |
| `onIsEventPublisher` | `$type, &$isEventPublisher[$type]` | When checking category-level publish permission. |

---

### Admin UI / editing hooks

| Event | Arguments | When it fires |
|---|---|---|
| `onEventEdit` | `&$extraTabs[], &$row, &$params` | When the event edit form is displayed; plugins add tab definitions. |
| `onEditCustom` | `&$row, &$customfields[]` | When rendering the custom-fields section of the edit form. |
| `onEditMenuItem` | `&$data[], &$value, $type, $name, $id, $form` | When rendering extra fields in a menu item config form. |
| `onEditLocation` | `&$event` | When rendering the location field in the event editor. |
| `onTranslateEvent` | `&$row, $lang` | When the translation view is opened. |
| `onSaveTranslation` | `$array, true` | After a translation is saved. |

---

### View rendering hooks

| Event | Arguments | When it fires |
|---|---|---|
| `onJEventsHeader` | `$view` | Start of every JEvents view render; plugins can output markup or load assets. |
| `onJEventsFooter` | `$view` (optional) | End of every JEvents view render. |
| `onJEventsLatestFooter` | _(none)_ | End of the Latest Events module render. |
| `onBeforeLoadView` | `$view, $theme, $viewType, $subtask, $useCache` | Before a view object is instantiated in the site icalevent/icalrepeat controllers. |
| `onJEventsPluginController` | `$plugin, $task` | When a task prefixed with `plugin.` is dispatched to the admin controller; delegates to a plugin-provided controller. |
| `onJEventsPluginOutput` | _(none)_ | When a task prefixed with `plugin.` is dispatched to the site component entry point. |
| `onJEventsRoute` | _(none)_ | During URL build/parse in the component router. |

---

### Conflict detection, export, import, and misc hooks

| Event | Arguments | When it fires |
|---|---|---|
| `onCheckEventOverlaps` | `&$testevent, &$overlaps[], $eventid, $requestObject` | During overlap/conflict checks for an event. |
| `onCheckRepeatOverlaps` | `&$repeat, &$overlaps[], $eventid, $requestObject` | During overlap checks for a repeat occurrence. |
| `onExportRow` | `&$row` | For each event row during iCal export; plugins can modify exported fields. |
| `onGetExportYears` | `&$minyear, &$maxyear` | When building the year range for the iCal export form. |
| `onImportCsvFile` | `&$rawData` | Before CSV import data is parsed; plugins can pre-process the raw content. |
| `onSendAdminMail` | `&$mail, $event` | Before the admin notification email is sent for a new event. |
| `onMissingEvent` | `&$row, $rpid, $jevtype, $year, $month, $day, $uid` | When an event occurrence cannot be found in the DB; plugins can supply fallback data. |
| `onMissingEventById` | `&$testevent, $evid, &$Itemid` | When an event cannot be found by ID in the admin controller. |
| `onGetEventData` | `&$row` | After event data is loaded in the DataModel detail method. |
| `onJeventsGetter` | `&$this, $name, &$available` | Magic-property fallback on `jEventCal`; plugins can provide dynamic properties. |
| `onSelectIcals` | `&$query` | When building the query to select iCal calendar source files. |