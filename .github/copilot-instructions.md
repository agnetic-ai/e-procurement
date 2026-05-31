# e-Procurement AI Coding Instructions

## Architecture Overview

This is a PHP-based e-procurement system with a three-tier architecture:

- **Frontend**: Bootstrap 5 UI (Voler template) + jQuery + DataTables, served from `public/`
- **Backend**: PHP MVC with custom framework in `app/`
- **Data Layer**: MySQL database accessed via PDO singleton pattern

### Core Components

**Routing** (`index.php`):

- URL query parameter `?url=controller/method` routes to controllers
- Redirects unauthenticated users to `auth/login`, authenticated to `dashboard`
- Uses `switch` statement to load controllers

**Models** (`app/models/`):

- Direct database access via `Database::getInstance()->getConnection()`
- Prepared statements with parameter binding
- Return associative arrays; complex queries use JOIN with renamed columns for clarity

**Controllers** (`app/controllers/`):

- Extend `Controller` base class; initialize session in constructor
- Use `$this->view()` to render PHP templates with data extraction
- JSON responses via `ResponseHelper::json()` with structure: `{status, message, result, meta}`

**Services** (`app/service/`):

- Business logic for specific features (e.g., vendor creation)
- Mix of PHP controllers and JavaScript utilities

**Views** (`app/views/`):

- PHP templates with data variables passed as array
- Layouts injected by `Controller::view()` (header/footer/sidebar)
- Voler template integration for dashboard pages

## Key Patterns & Conventions

### Database Layer

- Singleton pattern: `Database::getInstance()->getConnection()` returns PDO connection
- All queries use parameterized statements: `$stmt->execute(['key' => $value])`
- Models fetch via `fetch()` or `fetchAll()` without explicit loops
- Use descriptive aliases: `v.id AS vendorId`, `c.city_name AS cityName`

### Response Format

All API endpoints use standardized JSON response (see `ResponseHelper.php`):

```php
ResponseHelper::success($data, 'Message', $meta);  // Status 200
ResponseHelper::created($data, 'Created', $meta);  // Status 201
ResponseHelper::error('Error message');             // Status 400+
```

### Authentication & Sessions

- `Session` class manages auth state with timeout checking (30 min default)
- Store user data in `$_SESSION` via `$this->session->set()`
- Check auth: `$this->session->isLoggedIn()` or `$this->session->has('user_id')`
- Login attempts tracked in DB; 5 failed attempts locks account for 15 minutes

### Frontend Integration

- Forms use jQuery for AJAX: `$.ajax()` to POST to controller methods
- DataTables initialized in views for vendor listings
- JavaScript in `app/service/vendor/` handles form submission logic
- CSS from `public/voler/assets/css/` (Voler template) + custom Bootstrap overrides

## Critical Developer Workflows

### Adding a New Feature

1. **Create Model** in `app/models/FeatureModel.php` with CRUD methods
2. **Create Controller** in `app/controllers/FeatureController.php`
3. **Add routing** in `index.php` switch statement
4. **Create View** in `app/views/feature/index.php`
5. **Add services** in `app/service/feature/` if complex logic exists

### Database Queries

- Always use prepared statements with named parameters
- Use aliases for multi-table queries to avoid column name collisions
- Example (from VendorModel):

```php
$query = "SELECT v.id AS vendorId, c.city_name FROM vendors v
          INNER JOIN cities c ON v.city_id = c.id WHERE v.id = ?";
$stmt = $this->db->prepare($query);
$stmt->execute([$vendorId]);
```

### Frontend Form Submission

- Forms attach submit handler with `$("#formId").on("submit", function(e) { e.preventDefault(); })`
- Collect form data with `formElement.find('input[name="field"]').val()`
- POST via AJAX to controller method that returns JSON
- Example in `CreateVendor.js`: collect vendor form fields, POST to create endpoint

### Session & Redirect

- Use `$this->session->set()` and `$this->session->get()` in PHP
- Redirect with `$this->redirect('path')` or PHP `header('Location: ...')`
- Flash messages: `$this->session->setFlash('error', 'message')` then `getFlash('error')`

## File Organization Reference

```
app/
  core/           → Base classes (Controller, Database, Session)
  models/         → Data access (UserModel, VendorModel, etc.)
  controllers/    → Route handlers (AuthController, VendorController)
  views/          → PHP templates (organized by feature)
  service/        → Business logic & utilities (vendor creation, etc.)
  helpers/        → Utilities (ResponseHelper for JSON formatting)
  config/         → Autoloader, constants, database credentials

public/
  voler/          → Pre-built UI theme (Bootstrap 5)
  node_modules/   → jQuery, DataTables, Bootstrap (see package.json)
  assets/         → Custom CSS, JS, images
```

## Configuration

**Database**: `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME` defined in `constants.php`  
**Session timeout**: `SESSION_TIMEOUT` constant (1800 seconds = 30 min)  
**Login lockout**: `MAX_LOGIN_ATTEMPTS` (5) + `LOGIN_TIMEOUT` (900 sec = 15 min)  
**Debug mode**: `DEBUG_MODE` constant controls error visibility

## Common Pitfalls

- Models return data as-is; always escape output in views with `htmlspecialchars()`
- Form data accessed via `$_POST` directly in controllers; trim & validate before use
- `ResponseHelper::json()` calls `exit` automatically; no code after response call
- Views receive data as `$data` array; access via `$data['key']` or use `extract($data)`
- Controllers must inherit from `Controller` to access `$this->session` and `$this->view()`
