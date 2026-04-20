<?php
// web routes
use App\Http\Controllers\Web\ActivityWebController;
use App\Http\Controllers\Web\Auth\AuthWebController;
use App\Http\Controllers\Web\Auth\VerifyEmailWebController;
use App\Http\Controllers\Web\DashboardWebController;
use App\Http\Controllers\Web\PermissionWebController;
use App\Http\Controllers\Web\ProfileWebController;
use App\Http\Controllers\Web\RoleWebController;
use App\Http\Controllers\Web\UserWebController;
use App\Http\Controllers\Web\AgendaWebController;
use App\Http\Controllers\Web\CircularWebController;
use App\Http\Controllers\Web\MeetingWebController;
use App\Http\Controllers\Web\MemberWebController;
use App\Http\Controllers\Web\ReportWebController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/', function () {
    return redirect()->route('dashboard.index');
});

// ── Email Verification (Public) ───────────────────────────────────────────────
Route::get('/verify-email', [VerifyEmailWebController::class, 'verify'])->name('verify-email');
Route::get('/verify-email-pending', function (Request $request) {
    return view('auth.verify-email-pending', ['email' => $request->query('email')]);
})->name('verify-email-pending');
Route::post('/resend-verification-email', [VerifyEmailWebController::class, 'resend'])->name('resend-verification-email');

// ── Guest Auth ────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',           [AuthWebController::class, 'loginForm'])->name('login');
    Route::post('/login',          [AuthWebController::class, 'login'])->name('auth.login.submit');
    Route::get('/register',        [AuthWebController::class, 'registerForm'])->name('auth.register');
    Route::post('/register',       [AuthWebController::class, 'register'])->name('auth.register.submit');
    Route::get('/verify-mfa',      [AuthWebController::class, 'verifyMfaForm'])->name('auth.mfa.form');
    Route::post('/verify-mfa',     [AuthWebController::class, 'verifyMfa'])->name('auth.verify-mfa');
    Route::get('/forgot-password', [AuthWebController::class, 'forgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthWebController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password',  [AuthWebController::class, 'resetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthWebController::class, 'resetPassword'])->name('password.update');
});

// ── Authenticated ─────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // ── Dashboard ──────────────────────────────────────────────────────────────
    Route::get('/dashboard', [DashboardWebController::class, 'index'])->name('dashboard.index');

    // ── Profile ────────────────────────────────────────────────────────────────
    Route::get('/profile',         [ProfileWebController::class, 'index'])->name('profile.index');
    Route::put('/profile',         [ProfileWebController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileWebController::class, 'updateAvatar'])->name('profile.avatar');

    // ── Activity Logs ──────────────────────────────────────────────────────────
    Route::get('/activity',      [ActivityWebController::class, 'index'])->name('activity.index');
    Route::get('/activity/logs', [ActivityWebController::class, 'logs'])->name('activity.logs');

    // ── Agenda Management ──────────────────────────────────────────────────────
    Route::prefix('agendas')->name('agendas.')->group(function () {
        Route::get('/',                               [AgendaWebController::class, 'index'])->name('index');
        Route::get('/create',                         [AgendaWebController::class, 'create'])->name('create');
        Route::post('/',                              [AgendaWebController::class, 'store'])->name('store');

        // ── Export (before /{agenda} to avoid route conflict) ──────────
        Route::get('/export/pdf',                     [AgendaWebController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export/print',                   [AgendaWebController::class, 'printView'])->name('export.print');

        Route::get('/{agenda}',                       [AgendaWebController::class, 'show'])->name('show');
        Route::get('/{agenda}/edit',                  [AgendaWebController::class, 'edit'])->name('edit');
        Route::put('/{agenda}',                       [AgendaWebController::class, 'update'])->name('update');
        Route::delete('/{agenda}',                    [AgendaWebController::class, 'destroy'])->name('destroy');
        // Workflow actions
        Route::post('/{agenda}/submit',               [AgendaWebController::class, 'submit'])->name('submit');
        Route::post('/{agenda}/approve',              [AgendaWebController::class, 'approve'])->name('approve');
        Route::post('/{agenda}/reject',               [AgendaWebController::class, 'reject'])->name('reject');
        // Document management
        Route::delete('/{agenda}/documents/{document}', [AgendaWebController::class, 'deleteDocument'])->name('documents.destroy');
    });

    // ── Meeting Management ─────────────────────────────────────────────────────
    Route::prefix('meetings')->name('meetings.')->group(function () {

        // ── CRUD ───────────────────────────────────────────────────────────────
        Route::get('/',               [MeetingWebController::class, 'index'])->name('index');
        Route::get('/create',         [MeetingWebController::class, 'create'])->name('create');
        Route::post('/',              [MeetingWebController::class, 'store'])->name('store');
        Route::get('/{meeting}',      [MeetingWebController::class, 'show'])->name('show');
        Route::get('/{meeting}/edit', [MeetingWebController::class, 'edit'])->name('edit');
        Route::put('/{meeting}',      [MeetingWebController::class, 'update'])->name('update');
        Route::delete('/{meeting}',   [MeetingWebController::class, 'destroy'])->name('destroy');

        // ── Lifecycle Actions ──────────────────────────────────────────────────
        Route::post('/{meeting}/cancel',     [MeetingWebController::class, 'cancel'])->name('cancel');
        Route::post('/{meeting}/reschedule', [MeetingWebController::class, 'reschedule'])->name('reschedule');
        Route::post('/{meeting}/complete',   [MeetingWebController::class, 'complete'])->name('complete');
        Route::post('/{meeting}/notify',     [MeetingWebController::class, 'sendNotifications'])->name('notify');
        Route::post('/{meeting}/rsvp',       [MeetingWebController::class, 'rsvp'])->name('rsvp');
        Route::get('/{meeting}/ical',        [MeetingWebController::class, 'downloadIcal'])->name('ical');

        // ── Agenda Builder ─────────────────────────────────────────────────────
        Route::get('/{meeting}/agenda-builder', [MeetingWebController::class, 'agendaBuilder'])->name('agenda-builder');

        // Agenda Items (AJAX)
        Route::post('/{meeting}/agenda-items',              [MeetingWebController::class, 'storeAgendaItem'])->name('agenda-items.store');
        Route::put('/{meeting}/agenda-items/{item}',        [MeetingWebController::class, 'updateAgendaItem'])->name('agenda-items.update');
        Route::delete('/{meeting}/agenda-items/{item}',     [MeetingWebController::class, 'destroyAgendaItem'])->name('agenda-items.destroy');
        Route::post('/{meeting}/agenda-items/reorder',      [MeetingWebController::class, 'reorderAgendaItems'])->name('agenda-items.reorder');

        // ── Minutes ────────────────────────────────────────────────────────────
        Route::get('/{meeting}/minutes',  [MeetingWebController::class, 'minutes'])->name('minutes');
        Route::post('/{meeting}/minutes', [MeetingWebController::class, 'saveMinutes'])->name('minutes.save');

        // ── Document Management ────────────────────────────────────────────────
        Route::delete('/{meeting}/documents/{document}', [MeetingWebController::class, 'deleteDocument'])->name('documents.destroy');

        // ── Document Management ────────────────────────────────────────────────
        Route::delete('/{meeting}/documents/{document}', [MeetingWebController::class, 'deleteDocument'])->name('documents.destroy');

        // ── Export / Print ─────────────────────────────────────────────────────
        Route::get('/{meeting}/export/agenda',  [MeetingWebController::class, 'exportAgendaPdf'])->name('export.agenda');
        Route::get('/{meeting}/export/minutes', [MeetingWebController::class, 'exportMinutesPdf'])->name('export.minutes');
        Route::get('/{meeting}/print/agenda',   [MeetingWebController::class, 'printAgenda'])->name('print.agenda');
        Route::get('/{meeting}/print/minutes',  [MeetingWebController::class, 'printMinutes'])->name('print.minutes');
    });

    // ── Users (Admin / MD only) ────────────────────────────────────────────────
    Route::middleware('role:admin,md')->group(function () {
        Route::resource('users', UserWebController::class);
        Route::post('/users/{user}/activate',       [UserWebController::class, 'activate'])->name('users.activate');
        Route::post('/users/{user}/deactivate',     [UserWebController::class, 'deactivate'])->name('users.deactivate');
        Route::post('/users/{user}/reset-password', [UserWebController::class, 'resetPassword'])->name('users.reset-password');
        Route::get('/users/{user}/activity',        [ActivityWebController::class, 'userLogs'])->name('users.activity');
    });

    // ── Roles (Admin only) ─────────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::resource('roles', RoleWebController::class);
        Route::post('/roles/{role}/sync-permissions', [RoleWebController::class, 'syncPermissions'])->name('roles.sync-permissions');
    });

    // ── Permissions (Admin only) ───────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::resource('permissions', PermissionWebController::class);
    });

    // ── Logout ─────────────────────────────────────────────────────────────────
    Route::post('/logout', [AuthWebController::class, 'logout'])->name('auth.logout');

    // ── Members Management ─────────────────────────────────────────────────────
    Route::prefix('members')->name('members.')->middleware('role:admin,md')->group(function () {
        Route::get('/',                          [MemberWebController::class, 'index'])->name('index');
        Route::get('/create',                    [MemberWebController::class, 'create'])->name('create');
        Route::post('/',                         [MemberWebController::class, 'store'])->name('store');
        Route::get('/{member}',                  [MemberWebController::class, 'show'])->name('show');
        Route::get('/{member}/edit',             [MemberWebController::class, 'edit'])->name('edit');
        Route::put('/{member}',                  [MemberWebController::class, 'update'])->name('update');
        Route::delete('/{member}',               [MemberWebController::class, 'destroy'])->name('destroy');
        Route::post('/{member}/activate',        [MemberWebController::class, 'activate'])->name('activate');
        Route::post('/{member}/deactivate',      [MemberWebController::class, 'deactivate'])->name('deactivate');
        Route::delete('/{member}/photo',         [MemberWebController::class, 'removePhoto'])->name('photo.remove');
    });

    // In web.php — inside the auth middleware group

    // ── Circulars ──────────────────────────────────────────────────────────────
    Route::prefix('circulars')->name('circulars.')->group(function () {

        Route::get('/',                                            [CircularWebController::class, 'index'])->name('index');

        // Admin-only write operations
        Route::middleware('role:admin')->group(function () {
            Route::get('/create',                                  [CircularWebController::class, 'create'])->name('create');
            Route::post('/',                                       [CircularWebController::class, 'store'])->name('store');
            Route::get('/{circular}/edit',                         [CircularWebController::class, 'edit'])->name('edit');
            Route::put('/{circular}',                              [CircularWebController::class, 'update'])->name('update');
            Route::delete('/{circular}',                           [CircularWebController::class, 'destroy'])->name('destroy');
            Route::post('/{circular}/publish',                     [CircularWebController::class, 'publish'])->name('publish');
            Route::post('/{circular}/unpublish',                   [CircularWebController::class, 'unpublish'])->name('unpublish');
            Route::post('/{circular}/send-reminder',               [CircularWebController::class, 'sendReminder'])->name('send-reminder');
            Route::delete('/{circular}/attachments/{attachment}',  [CircularWebController::class, 'deleteAttachment'])->name('attachments.destroy');
        });

        Route::get('/{circular}',                                  [CircularWebController::class, 'show'])->name('show');
    });

    // ── Reports ────────────────────────────────────────────────────────────────────
    Route::prefix('reports')->name('reports.')->group(function () {

        Route::get('/',                  [ReportWebController::class, 'index'])->name('index');

        // Meeting & attendance
        Route::get('/meetings',          [ReportWebController::class, 'meetings'])->name('meetings');
        Route::get('/attendance',        [ReportWebController::class, 'attendance'])->name('attendance');

        // Minutes / extract
        Route::get('/minutes',           [ReportWebController::class, 'minutes'])->name('minutes');

        // Agenda / implementation
        Route::get('/agendas',           [ReportWebController::class, 'agendas'])->name('agendas');

        // Department-wise
        Route::get('/departments',       [ReportWebController::class, 'departments'])->name('departments');

        // Member activity
        Route::get('/member-activity',   [ReportWebController::class, 'memberActivity'])->name('member-activity');

        // Audit trail (admin / md only)
        Route::get('/audit-trail',       [ReportWebController::class, 'auditTrail'])
            ->name('audit-trail')
            ->middleware('role:admin,md');
    });
});
