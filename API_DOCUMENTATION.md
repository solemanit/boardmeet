# User & Role Management REST API Documentation

## Overview

This is a comprehensive User & Role Management REST API system built with Laravel, featuring:
- **Role-based Access Control (RBAC)** with granular permissions
- **Secure Authentication** with token-based authentication (Sanctum)
- **Password Policy** enforcement
- **Multi-Factor Authentication (MFA)** support (TOTP, SMS, Email)
- **User Activation/Deactivation**
- **Activity Logging**
- **Account Lockout** after failed login attempts

## System Features

### 1. **Multiple Roles**
- **Admin** (Priority: 100) - Full system access
- **Managing Director (MD)** (Priority: 80) - High-level access
- **Chairman** (Priority: 75) - Board and company access
- **Company Secretary** (Priority: 70) - Board and company management
- **Board Member** (Priority: 50) - Read-only board access

### 2. **Granular Permissions**
The system includes 26+ granular permissions organized by module:
- **Users Module**: view, create, edit, delete, activate, deactivate, reset_password, view_activity
- **Roles Module**: view, create, edit, delete, manage_permissions
- **Permissions Module**: view, create, edit, delete
- **Board Module**: view_meetings, create_meetings, edit_meetings, delete_meetings, view_minutes, create_minutes
- **Company Module**: view, edit
- **Reports Module**: view, generate

### 3. **Security Features**
- Password requirements:
  - Minimum 8 characters
  - At least one uppercase letter (A-Z)
  - At least one lowercase letter (a-z)
  - At least one number (0-9)
  - At least one special character (!@#$%^&*)
- Account lockout after 5 failed login attempts (15 minutes)
- API token-based authentication using Laravel Sanctum
- Activity logging for all user actions

## Installation & Setup

### 1. **Run Migrations**
```bash
php artisan migrate
```

### 2. **Seed Roles, Permissions & Sample Users**
```bash
php artisan db:seed
```

This will create:
- 5 roles with their respective permissions
- 1 admin user: `admin@boardmeet.local` / `Password@123`
- 4 sample users (one for each other role)

### 3. **Update Middleware Registration**

Add the middleware aliases to `bootstrap/app.php`:

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
    })
    // ... other configuration
```

## API Endpoints

### Authentication Endpoints

#### 1. **User Registration**
```
POST /api/v1/auth/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "SecurePass@123",
    "password_confirmation": "SecurePass@123",
    "role_id": 5
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "User registered successfully. Please wait for account activation.",
    "user_id": 1
}
```

#### 2. **User Login**
```
POST /api/v1/auth/login
Content-Type: application/json

{
    "email": "admin@boardmeet.local",
    "password": "Password@123"
}
```

**Response (200) - Without MFA:**
```json
{
    "success": true,
    "message": "Login successful",
    "user": {
        "id": 1,
        "name": "Admin User",
        "email": "admin@boardmeet.local",
        "role": {
            "id": 1,
            "name": "Admin",
            "slug": "admin"
        },
        "permissions": ["users.view", "users.create", ...],
        "is_active": true,
        "mfa_enabled": false,
        "created_at": "2026-02-16T10:00:00Z"
    },
    "token": "api-token-here"
}
```

**Response (200) - With MFA Required:**
```json
{
    "success": true,
    "message": "OTP sent to your email",
    "mfa_required": true,
    "user_id": 1,
    "temp_token": "temp-token-here"
}
```

#### 3. **Verify MFA OTP**
```
POST /api/v1/auth/verify-mfa
Content-Type: application/json

{
    "user_id": 1,
    "otp": "123456",
    "temp_token": "temp-token-here"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "MFA verification successful",
    "user": {...},
    "token": "api-token-here"
}
```

#### 4. **Get Current User**
```
GET /api/v1/auth/me
Authorization: Bearer <token>
```

**Response (200):**
```json
{
    "success": true,
    "user": {...}
}
```

#### 5. **Change Password**
```
POST /api/v1/auth/change-password
Authorization: Bearer <token>
Content-Type: application/json

{
    "current_password": "CurrentPass@123",
    "new_password": "NewSecurePass@456",
    "new_password_confirmation": "NewSecurePass@456"
}
```

#### 6. **Logout**
```
POST /api/v1/auth/logout
Authorization: Bearer <token>
```

---

### User Management Endpoints (Admin/MD Only)

#### 1. **Get All Users**
```
GET /api/v1/users?page=1&per_page=15&role_id=5&is_active=true&search=john
Authorization: Bearer <token>
```

**Required Permission:** `users.view` (or Admin/MD role)

**Response (200):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "phone_number": "+1234567890",
            "role": {
                "id": 5,
                "name": "Board Member",
                "slug": "board_member"
            },
            "permissions": ["board.view_meetings", ...],
            "is_active": true,
            "activated_at": "2026-02-16T10:00:00Z",
            "mfa_enabled": false,
            "mfa_method": null,
            "phone_verified": false,
            "last_login_at": "2026-02-16T11:00:00Z",
            "last_password_changed_at": "2026-02-16T10:00:00Z",
            "created_at": "2026-02-16T10:00:00Z",
            "updated_at": "2026-02-16T10:00:00Z"
        }
    ],
    "pagination": {
        "total": 10,
        "per_page": 15,
        "current_page": 1,
        "last_page": 1
    }
}
```

#### 2. **Create User**
```
POST /api/v1/users
Authorization: Bearer <token>
Content-Type: application/json

{
    "name": "Jane Smith",
    "email": "jane@example.com",
    "password": "SecurePass@123",
    "role_id": 4,
    "is_active": true
}
```

#### 3. **Get User by ID**
```
GET /api/v1/users/{user_id}
Authorization: Bearer <token>
```

#### 4. **Update User**
```
PUT /api/v1/users/{user_id}
Authorization: Bearer <token>
Content-Type: application/json

{
    "name": "Updated Name",
    "email": "newemail@example.com",
    "role_id": 5,
    "phone_number": "+1234567890"
}
```

#### 5. **Activate User**
```
POST /api/v1/users/{user_id}/activate
Authorization: Bearer <token>
```

#### 6. **Deactivate User**
```
POST /api/v1/users/{user_id}/deactivate
Authorization: Bearer <token>
```

#### 7. **Reset User Password**
```
POST /api/v1/users/{user_id}/reset-password
Authorization: Bearer <token>
Content-Type: application/json

{
    "password": "NewPassword@456"
}
```

#### 8. **Get User Activity Logs**
```
GET /api/v1/users/{user_id}/activity?page=1
Authorization: Bearer <token>
```

**Response (200):**
```json
{
    "success": true,
    "logs": [
        {
            "id": 1,
            "user_id": 1,
            "action": "login",
            "ip_address": "192.168.1.1",
            "user_agent": "Mozilla/5.0...",
            "details": {"timestamp": "2026-02-16T11:00:00Z"},
            "created_at": "2026-02-16T11:00:00Z"
        }
    ],
    "pagination": {
        "total": 5,
        "per_page": 20,
        "current_page": 1
    }
}
```

#### 9. **Delete User**
```
DELETE /api/v1/users/{user_id}
Authorization: Bearer <token>
```

---

### Role Management Endpoints (Admin Only)

#### 1. **Get All Roles**
```
GET /api/v1/roles?page=1&per_page=15&is_active=true&search=admin
Authorization: Bearer <token>
```

**Required Permission:** Admin role

#### 2. **Create Role**
```
POST /api/v1/roles
Authorization: Bearer <token>
Content-Type: application/json

{
    "name": "Custom Role",
    "slug": "custom_role",
    "description": "A custom role",
    "priority": 60,
    "permissions": [1, 2, 3, 4, 5]
}
```

#### 3. **Get Role by ID**
```
GET /api/v1/roles/{role_id}
Authorization: Bearer <token>
```

#### 4. **Update Role**
```
PUT /api/v1/roles/{role_id}
Authorization: Bearer <token>
Content-Type: application/json

{
    "name": "Updated Role Name",
    "description": "Updated description",
    "priority": 65,
    "is_active": true,
    "permissions": [1, 2, 3]
}
```

#### 5. **Sync Role Permissions**
```
POST /api/v1/roles/{role_id}/permissions
Authorization: Bearer <token>
Content-Type: application/json

{
    "permissions": [1, 2, 3, 4, 5, 10, 15]
}
```

#### 6. **Delete Role**
```
DELETE /api/v1/roles/{role_id}
Authorization: Bearer <token>
```

**Note:** Cannot delete a role that has users assigned.

---

### Permission Management Endpoints (Admin Only)

#### 1. **Get All Permissions**
```
GET /api/v1/permissions?page=1&per_page=50&module=users&group_by_module=true
Authorization: Bearer <token>
```

#### 2. **Get Permission Modules**
```
GET /api/v1/permissions/modules
Authorization: Bearer <token>
```

**Response (200):**
```json
{
    "success": true,
    "modules": ["users", "roles", "permissions", "board", "company", "reports"]
}
```

#### 3. **Create Permission**
```
POST /api/v1/permissions
Authorization: Bearer <token>
Content-Type: application/json

{
    "name": "View Custom Module",
    "slug": "custom.view",
    "description": "Can view custom module",
    "module": "custom"
}
```

#### 4. **Get Permission by ID**
```
GET /api/v1/permissions/{permission_id}
Authorization: Bearer <token>
```

#### 5. **Update Permission**
```
PUT /api/v1/permissions/{permission_id}
Authorization: Bearer <token>
Content-Type: application/json

{
    "name": "Updated Permission Name",
    "description": "Updated description",
    "module": "custom",
    "is_active": true
}
```

#### 6. **Delete Permission**
```
DELETE /api/v1/permissions/{permission_id}
Authorization: Bearer <token>
```

**Note:** Cannot delete a permission that is assigned to any role.

---

## User Role Hierarchy

```
Admin (100)
  ├─ Full access to all features
  ├─ Can manage users, roles, permissions
  └─ Can manage board, company, and reports

Managing Director (80)
  ├─ Can view and manage users
  ├─ Can view roles and permissions
  └─ Can manage board meetings and minutes

Chairman (75)
  ├─ Can manage board meetings
  ├─ Can manage company information
  └─ Can view reports

Company Secretary (70)
  ├─ Can view users
  ├─ Can manage board meetings
  ├─ Can manage company information
  └─ Can generate reports

Board Member (50)
  ├─ Can view board meetings and minutes
  ├─ Can view company information
  └─ Can view reports
```

## Using Middleware in Routes

### **Check Role Middleware**
```php
Route::middleware('role:admin,md')->group(function () {
    // Only admin and md users can access these routes
});
```

### **Check Permission Middleware**
```php
Route::middleware('permission:users.create,users.edit')->group(function () {
    // User must have at least one of these permissions
});
```

## Password Policy

Passwords must meet ALL of the following requirements:
- ✅ Minimum 8 characters
- ✅ At least 1 uppercase letter (A-Z)
- ✅ At least 1 lowercase letter (a-z)
- ✅ At least 1 number (0-9)
- ✅ At least 1 special character (!@#$%^&*)

**Example Valid Password:** `SecureP@ss123`

## MFA (Multi-Factor Authentication)

### Methods Supported
1. **TOTP** (Time-based One-Time Password) - Google Authenticator, Authy
2. **SMS** - If phone number is provided
3. **Email** - Default method

### Enable MFA
Users can enable MFA on their account (implementation in extensions).

### MFA Flow
1. User logs in with correct credentials
2. System requests OTP via specified method
3. User verifies OTP
4. System grants access

## Error Responses

### 400 - Bad Request
```json
{
    "success": false,
    "errors": {
        "email": ["Email is required"]
    }
}
```

### 401 - Unauthorized
```json
{
    "success": false,
    "message": "Invalid credentials"
}
```

### 403 - Forbidden
```json
{
    "success": false,
    "message": "Unauthorized: Required role(s): admin"
}
```

### 422 - Unprocessable Entity
```json
{
    "success": false,
    "message": "Password does not meet policy requirements",
    "requirements": [
        "At least 8 characters",
        "At least one uppercase letter (A-Z)",
        "At least one lowercase letter (a-z)",
        "At least one number (0-9)",
        "At least one special character (!@#$%^&*)"
    ]
}
```

## Testing the API

### Using cURL

**Login:**
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@boardmeet.local",
    "password": "Password@123"
  }'
```

**Get Users (with token):**
```bash
curl -X GET http://localhost:8000/api/v1/users \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## Database Schema

### Users Table
```sql
- id (Primary Key)
- role_id (Foreign Key -> roles)
- name
- email (Unique)
- password (Hashed)
- is_active (Boolean)
- activated_at (Timestamp)
- last_password_changed_at (Timestamp)
- failed_login_attempts (Integer)
- last_login_at (Timestamp)
- locked_until (Timestamp)
- mfa_enabled (Boolean)
- mfa_method (String: totp|sms|email)
- mfa_secret (Encrypted)
- phone_number
- phone_verified (Boolean)
- deleted_at (Soft delete)
- timestamps
```

### Roles Table
```sql
- id (Primary Key)
- name (Unique)
- slug (Unique)
- description
- priority (Integer)
- is_active (Boolean)
- timestamps
```

### Permissions Table
```sql
- id (Primary Key)
- name (Unique)
- slug (Unique)
- description
- module
- is_active (Boolean)
- timestamps
```

### User Activity Logs Table
```sql
- id (Primary Key)
- user_id (Foreign Key -> users)
- action (String)
- ip_address
- user_agent
- details (JSON)
- created_at
```

## Default Admin Credentials

After running migrations and seeders:

- **Email:** `admin@boardmeet.local`
- **Password:** `Password@123`
- **Role:** Admin

⚠️ **Important:** Change the admin password immediately in production!

## File Structure Created

```
app/
  ├── Http/
  │   ├── Controllers/
  │   │   ├── AuthController.php
  │   │   ├── UserController.php
  │   │   ├── RoleController.php
  │   │   └── PermissionController.php
  │   └── Middleware/
  │       ├── CheckRole.php
  │       └── CheckPermission.php
  ├── Models/
  │   ├── User.php (Updated)
  │   ├── Role.php
  │   ├── Permission.php
  │   └── UserActivityLog.php
  └── Services/
      ├── PasswordPolicyService.php
      └── MFAService.php
database/
  ├── migrations/
  │   ├── 2026_02_16_000000_create_roles_table.php
  │   ├── 2026_02_16_000001_create_permissions_table.php
  │   ├── 2026_02_16_000002_create_role_permission_table.php
  │   ├── 2026_02_16_000003_add_fields_to_users_table.php
  │   └── 2026_02_16_000004_create_user_activity_logs_table.php
  └── seeders/
      ├── RoleAndPermissionSeeder.php
      └── DatabaseSeeder.php (Updated)
routes/
  └── api.php (New)
```

## Next Steps

1. ✅ Run migrations: `php artisan migrate`
2. ✅ Seed data: `php artisan db:seed`
3. ✅ Test endpoints with provided credentials
4. ✅ Integrate with your frontend application
5. ✅ Customize roles, permissions, and password policies as needed

---

**System Version:** 1.0.0
**Last Updated:** February 16, 2026
