<?php

// ─────────────────────────────────────────────────────────────────────────────
// routes/web.php — Complete Route File
// ─────────────────────────────────────────────────────────────────────────────

use App\Http\Controllers\Web\ActivityWebController;
use App\Http\Controllers\Web\AgendaBuilderController;
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
use App\Http\Controllers\Web\PublicVoteController;
use App\Http\Controllers\Web\ReportWebController;
use App\Http\Controllers\Web\VoteReportController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// ── Root ──────────────────────────────────────────────────────────────────────
Route::get('/', function () {
    return redirect()->route('dashboard.index');
});

// ── Email Verification (Public) ───────────────────────────────────────────────
Route::get('/verify-email', [VerifyEmailWebController::class, 'verify'])
    ->name('verify-email');

Route::get('/verify-email-pending', function (Request $request) {
    return view('auth.verify-email-pending', ['email' => $request->query('email')]);
})->name('verify-email-pending');

Route::post('/resend-verification-email', [VerifyEmailWebController::class, 'resend'])
    ->name('resend-verification-email');

// ── Guest Auth ────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',            [AuthWebController::class, 'loginForm'])->name('login');
    Route::post('/login',           [AuthWebController::class, 'login'])->name('auth.login.submit');
    Route::get('/register',         [AuthWebController::class, 'registerForm'])->name('auth.register');
    Route::post('/register',        [AuthWebController::class, 'register'])->name('auth.register.submit');
    Route::get('/verify-mfa',       [AuthWebController::class, 'verifyMfaForm'])->name('auth.mfa.form');
    Route::post('/verify-mfa',      [AuthWebController::class, 'verifyMfa'])->name('auth.verify-mfa');
    Route::get('/forgot-password',  [AuthWebController::class, 'forgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthWebController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password',   [AuthWebController::class, 'resetPasswordForm'])->name('password.reset');
    Route::post('/reset-password',  [AuthWebController::class, 'resetPassword'])->name('password.update');
});

// ── Authenticated ─────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // ── Logout ────────────────────────────────────────────────────────────────
    Route::post('/logout', [AuthWebController::class, 'logout'])->name('auth.logout');

    // ── Dashboard ─────────────────────────────────────────────────────────────
    Route::get('/dashboard', [DashboardWebController::class, 'index'])->name('dashboard.index');

    // ── Profile ───────────────────────────────────────────────────────────────
    Route::get('/profile',         [ProfileWebController::class, 'index'])->name('profile.index');
    Route::put('/profile',         [ProfileWebController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileWebController::class, 'updateAvatar'])->name('profile.avatar');

    // ── Activity Logs ─────────────────────────────────────────────────────────
    Route::get('/activity',      [ActivityWebController::class, 'index'])->name('activity.index');
    Route::get('/activity/logs', [ActivityWebController::class, 'logs'])->name('activity.logs');

    // ─────────────────────────────────────────────────────────────────────────
    // AGENDA MANAGEMENT
    // ─────────────────────────────────────────────────────────────────────────
    Route::prefix('agendas')->name('agendas.')->group(function () {

        Route::get('/',       [AgendaWebController::class, 'index'])->name('index');
        Route::get('/create', [AgendaWebController::class, 'create'])->name('create');
        Route::post('/',      [AgendaWebController::class, 'store'])->name('store');

        // Export (must come before /{agenda} to avoid conflict)
        Route::get('/export/pdf',   [AgendaWebController::class, 'exportPdf'])->name('export.pdf');
        Route::get('/export/print', [AgendaWebController::class, 'printView'])->name('export.print');

        Route::get('/{agenda}',      [AgendaWebController::class, 'show'])->name('show');
        Route::get('/{agenda}/edit', [AgendaWebController::class, 'edit'])->name('edit');
        Route::put('/{agenda}',      [AgendaWebController::class, 'update'])->name('update');
        Route::delete('/{agenda}',   [AgendaWebController::class, 'destroy'])->name('destroy');

        // Workflow
        Route::post('/{agenda}/submit',  [AgendaWebController::class, 'submit'])->name('submit');
        Route::post('/{agenda}/approve', [AgendaWebController::class, 'approve'])->name('approve');
        Route::post('/{agenda}/reject',  [AgendaWebController::class, 'reject'])->name('reject');

        // Document management
        Route::delete('/{agenda}/documents/{document}', [AgendaWebController::class, 'deleteDocument'])
            ->name('documents.destroy');
    });

    // ─────────────────────────────────────────────────────────────────────────
    // MEETING MANAGEMENT
    // ─────────────────────────────────────────────────────────────────────────
    Route::prefix('meetings')->name('meetings.')->group(function () {

        // ── Core CRUD ─────────────────────────────────────────────────────────
        Route::get('/',               [MeetingWebController::class, 'index'])->name('index');
        Route::get('/create',         [MeetingWebController::class, 'create'])->name('create');
        Route::post('/',              [MeetingWebController::class, 'store'])->name('store');
        Route::get('/{meeting}',      [MeetingWebController::class, 'show'])->name('show');
        Route::get('/{meeting}/edit', [MeetingWebController::class, 'edit'])->name('edit');
        Route::put('/{meeting}',      [MeetingWebController::class, 'update'])->name('update');
        Route::delete('/{meeting}',   [MeetingWebController::class, 'destroy'])->name('destroy');

        // ── Lifecycle ─────────────────────────────────────────────────────────
        Route::post('/{meeting}/cancel',     [MeetingWebController::class, 'cancel'])->name('cancel');
        Route::post('/{meeting}/reschedule', [MeetingWebController::class, 'reschedule'])->name('reschedule');
        Route::post('/{meeting}/complete',   [MeetingWebController::class, 'complete'])->name('complete');
        Route::post('/{meeting}/notify',     [MeetingWebController::class, 'sendNotifications'])->name('notify');
        Route::post('/{meeting}/rsvp',       [MeetingWebController::class, 'rsvp'])->name('rsvp');
        Route::get('/{meeting}/ical',        [MeetingWebController::class, 'downloadIcal'])->name('ical');

        // ── Meeting Documents ─────────────────────────────────────────────────
        Route::delete('/{meeting}/documents/{document}', [MeetingWebController::class, 'deleteDocument'])
            ->name('documents.destroy');

        // ── Export / Print ────────────────────────────────────────────────────
        Route::get('/{meeting}/export/agenda',  [MeetingWebController::class, 'exportAgendaPdf'])->name('export.agenda');
        Route::get('/{meeting}/export/minutes', [MeetingWebController::class, 'exportMinutesPdf'])->name('export.minutes');
        Route::get('/{meeting}/print/agenda',   [MeetingWebController::class, 'printAgenda'])->name('print.agenda');
        Route::get('/{meeting}/print/minutes',  [MeetingWebController::class, 'printMinutes'])->name('print.minutes');

        // ── Minutes (legacy flat per agenda-item) ─────────────────────────────
        Route::get('/{meeting}/minutes',  [MeetingWebController::class, 'minutes'])->name('minutes');
        Route::post('/{meeting}/minutes', [MeetingWebController::class, 'saveMinutes'])->name('minutes.save');

        // ─────────────────────────────────────────────────────────────────────
        // AGENDA BUILDER  (new section → memo system)
        // ─────────────────────────────────────────────────────────────────────

        // ── Builder page ──────────────────────────────────────────────────────
        Route::get('/{meeting}/agenda-builder', [AgendaBuilderController::class, 'index'])
            ->name('agenda-builder');

        // ── Sections ──────────────────────────────────────────────────────────
        Route::post('/{meeting}/sections',                [AgendaBuilderController::class, 'storeSection'])->name('sections.store');
        Route::put('/{meeting}/sections/{section}',       [AgendaBuilderController::class, 'updateSection'])->name('sections.update');
        Route::delete('/{meeting}/sections/{section}',    [AgendaBuilderController::class, 'destroySection'])->name('sections.destroy');
        Route::post('/{meeting}/sections/reorder',        [AgendaBuilderController::class, 'reorderSections'])->name('sections.reorder');

        // ── Memos ─────────────────────────────────────────────────────────────
        Route::post('/{meeting}/sections/{section}/memos',         [AgendaBuilderController::class, 'storeMemo'])->name('memos.store');
        Route::post('/{meeting}/sections/{section}/memos/reorder', [AgendaBuilderController::class, 'reorderMemos'])->name('memos.reorder');
        Route::put('/{meeting}/memos/{memo}',                      [AgendaBuilderController::class, 'updateMemo'])->name('memos.update');
        Route::delete('/{meeting}/memos/{memo}',                   [AgendaBuilderController::class, 'destroyMemo'])->name('memos.destroy');
        Route::post('/{meeting}/memos/{memo}/move',                [AgendaBuilderController::class, 'moveMemo'])->name('memos.move');

        // ── Memo Documents ────────────────────────────────────────────────────
        Route::post('/{meeting}/memos/{memo}/documents',                  [AgendaBuilderController::class, 'uploadDocument'])->name('memos.documents.store');
        Route::delete('/{meeting}/memos/{memo}/documents/{document}',     [AgendaBuilderController::class, 'deleteDocument'])->name('memos.documents.destroy');

        // ── Votes ─────────────────────────────────────────────────────────────
        Route::post('/{meeting}/memos/{memo}/vote',  [AgendaBuilderController::class, 'castVote'])->name('memos.vote');
        Route::get('/{meeting}/memos/{memo}/votes',  [AgendaBuilderController::class, 'voteResults'])->name('memos.votes');

        // ── Per-memo Minutes ──────────────────────────────────────────────────
        Route::post('/{meeting}/memos/{memo}/minutes', [AgendaBuilderController::class, 'saveMemoMinutes'])->name('memos.minutes');

        // ── Vote Link Management ───────────────────────────────────────────────
        Route::get('/{meeting}/vote-link',           [AgendaBuilderController::class, 'getVoteLink'])->name('vote-link.get');
        Route::post('/{meeting}/vote-link/generate', [AgendaBuilderController::class, 'generateVoteLink'])->name('vote-link.generate');
        Route::post('/{meeting}/vote-link/revoke',   [AgendaBuilderController::class, 'revokeVoteLink'])->name('vote-link.revoke');

        // ── Vote Report ────────────────────────────────────────────────────────
        Route::get('/{meeting}/vote-report',         [VoteReportController::class, 'show'])->name('vote-report');
        Route::get('/{meeting}/vote-report/pdf',     [VoteReportController::class, 'exportPdf'])->name('vote-report.pdf');

        // ─────────────────────────────────────────────────────────────────────
        // LEGACY Agenda Items (old flat builder — keep for backward compat)
        // ─────────────────────────────────────────────────────────────────────
        Route::get('/{meeting}/agenda-builder-legacy',          [MeetingWebController::class, 'agendaBuilder'])->name('agenda-builder-legacy');
        Route::post('/{meeting}/agenda-items',                  [MeetingWebController::class, 'storeAgendaItem'])->name('agenda-items.store');
        Route::put('/{meeting}/agenda-items/{item}',            [MeetingWebController::class, 'updateAgendaItem'])->name('agenda-items.update');
        Route::delete('/{meeting}/agenda-items/{item}',         [MeetingWebController::class, 'destroyAgendaItem'])->name('agenda-items.destroy');
        Route::post('/{meeting}/agenda-items/reorder',          [MeetingWebController::class, 'reorderAgendaItems'])->name('agenda-items.reorder');
    }); // end meetings prefix

    // ─────────────────────────────────────────────────────────────────────────
    // MEMBERS MANAGEMENT  (admin / md only)
    // ─────────────────────────────────────────────────────────────────────────
    Route::prefix('members')->name('members.')->middleware('role:admin,md')->group(function () {
        Route::get('/',                     [MemberWebController::class, 'index'])->name('index');
        Route::get('/create',               [MemberWebController::class, 'create'])->name('create');
        Route::post('/',                    [MemberWebController::class, 'store'])->name('store');
        Route::get('/{member}',             [MemberWebController::class, 'show'])->name('show');
        Route::get('/{member}/edit',        [MemberWebController::class, 'edit'])->name('edit');
        Route::put('/{member}',             [MemberWebController::class, 'update'])->name('update');
        Route::delete('/{member}',          [MemberWebController::class, 'destroy'])->name('destroy');
        Route::post('/{member}/activate',   [MemberWebController::class, 'activate'])->name('activate');
        Route::post('/{member}/deactivate', [MemberWebController::class, 'deactivate'])->name('deactivate');
        Route::delete('/{member}/photo',    [MemberWebController::class, 'removePhoto'])->name('photo.remove');
    });

    // ─────────────────────────────────────────────────────────────────────────
    // USERS  (admin / md only)
    // ─────────────────────────────────────────────────────────────────────────
    Route::middleware('role:admin,md')->group(function () {
        Route::resource('users', UserWebController::class);
        Route::post('/users/{user}/activate',       [UserWebController::class, 'activate'])->name('users.activate');
        Route::post('/users/{user}/deactivate',     [UserWebController::class, 'deactivate'])->name('users.deactivate');
        Route::post('/users/{user}/reset-password', [UserWebController::class, 'resetPassword'])->name('users.reset-password');
        Route::get('/users/{user}/activity',        [ActivityWebController::class, 'userLogs'])->name('users.activity');
    });

    // ─────────────────────────────────────────────────────────────────────────
    // ROLES  (admin only)
    // ─────────────────────────────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::resource('roles', RoleWebController::class);
        Route::post('/roles/{role}/sync-permissions', [RoleWebController::class, 'syncPermissions'])
            ->name('roles.sync-permissions');
    });

    // ─────────────────────────────────────────────────────────────────────────
    // PERMISSIONS  (admin only)
    // ─────────────────────────────────────────────────────────────────────────
    Route::middleware('role:admin')->group(function () {
        Route::resource('permissions', PermissionWebController::class);
    });

    // ─────────────────────────────────────────────────────────────────────────
    // CIRCULARS
    // ─────────────────────────────────────────────────────────────────────────
    Route::prefix('circulars')->name('circulars.')->group(function () {

        Route::get('/', [CircularWebController::class, 'index'])->name('index');

        // Admin-only write operations
        Route::middleware('role:admin')->group(function () {
            Route::get('/create',                                 [CircularWebController::class, 'create'])->name('create');
            Route::post('/',                                      [CircularWebController::class, 'store'])->name('store');
            Route::get('/{circular}/edit',                        [CircularWebController::class, 'edit'])->name('edit');
            Route::put('/{circular}',                             [CircularWebController::class, 'update'])->name('update');
            Route::delete('/{circular}',                          [CircularWebController::class, 'destroy'])->name('destroy');
            Route::post('/{circular}/publish',                    [CircularWebController::class, 'publish'])->name('publish');
            Route::post('/{circular}/unpublish',                  [CircularWebController::class, 'unpublish'])->name('unpublish');
            Route::post('/{circular}/send-reminder',              [CircularWebController::class, 'sendReminder'])->name('send-reminder');
            Route::delete('/{circular}/attachments/{attachment}', [CircularWebController::class, 'deleteAttachment'])
                ->name('attachments.destroy');
        });

        Route::get('/{circular}', [CircularWebController::class, 'show'])->name('show');
    });

    // ─────────────────────────────────────────────────────────────────────────
    // REPORTS
    // ─────────────────────────────────────────────────────────────────────────
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/',                [ReportWebController::class, 'index'])->name('index');
        Route::get('/meetings',        [ReportWebController::class, 'meetings'])->name('meetings');
        Route::get('/attendance',      [ReportWebController::class, 'attendance'])->name('attendance');
        Route::get('/minutes',         [ReportWebController::class, 'minutes'])->name('minutes');
        Route::get('/agendas',         [ReportWebController::class, 'agendas'])->name('agendas');
        Route::get('/departments',     [ReportWebController::class, 'departments'])->name('departments');
        Route::get('/member-activity', [ReportWebController::class, 'memberActivity'])->name('member-activity');
        Route::get('/audit-trail',     [ReportWebController::class, 'auditTrail'])
            ->name('audit-trail')
            ->middleware('role:admin,md');
    });
}); // end auth middleware

// ── Public Voting ────────────────────────────────────────────────────────
Route::get('/vote/{token}',        [PublicVoteController::class, 'show'])->name('vote.show');
Route::post('/vote/{token}',       [PublicVoteController::class, 'submit'])->name('vote.submit');
Route::get('/vote/{token}/thanks', [PublicVoteController::class, 'thanks'])->name('vote.thanks');
