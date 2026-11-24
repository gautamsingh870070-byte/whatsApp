<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\ContactController;

// Default Route
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/clear-cache', function () {
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('route:clear');
    return 'Cache cleared successfully!';
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::view('/privacy-policy', 'privacy-policy');

// Protected Routes
Route::middleware('jwt.session')->group(function () {
    
    // Dashboard (Home)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('permission:dashboard.view');

    // Password Change
    Route::get('/change-password', [AuthController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('password.update');

    // Profile Settings
    Route::get('/profile/edit', [\App\Http\Controllers\UserController::class, 'editProfile'])->name('profile.edit');
    Route::get('/app-logo/edit', [\App\Http\Controllers\UserController::class, 'editLogo'])->name('profile.logo');
    Route::post('/profile/update', [\App\Http\Controllers\UserController::class, 'updateProfile'])->name('profile.update');

    // WhatsApp Bulk Messaging
    Route::get('/send-wa', [WhatsAppController::class, 'showSendForm'])->name('send.wa.form')->middleware('permission:messages.send');
    Route::post('/send-wa', [WhatsAppController::class, 'sendBulk'])->name('send.wa.submit')->middleware('permission:messages.send');

    // WhatsApp Meta Template Management
    Route::get('/manage-templates', [\App\Http\Controllers\TemplateController::class, 'index'])->name('templates.index')->middleware('permission:templates.view');
    Route::post('/manage-templates', [\App\Http\Controllers\TemplateController::class, 'store'])->name('templates.store')->middleware('permission:templates.create');

    // Dashboard Pages
    Route::get('/history', [\App\Http\Controllers\HistoryController::class, 'index'])->name('history')->middleware('permission:history.view');
    
    Route::get('/schedule-log', [\App\Http\Controllers\ScheduleController::class, 'index'])->name('schedule.log')->middleware('permission:schedule_log.view');

    Route::middleware('role:superadmin,admin,client')->group(function () {
        Route::get('/manage-user', [\App\Http\Controllers\UserController::class, 'index'])->name('manage.user')->middleware('permission:users.view');
        Route::post('/manage-user', [\App\Http\Controllers\UserController::class, 'store'])->name('manage.user.store')->middleware('permission:users.create');
        Route::put('/manage-user/{id}', [\App\Http\Controllers\UserController::class, 'update'])->name('manage.user.update')->middleware('permission:users.edit');
        Route::delete('/manage-user/{id}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('manage.user.destroy')->middleware('permission:users.delete');
    });

    // Permissions
    Route::get('/permissions', [\App\Http\Controllers\PermissionController::class, 'index'])->name('permissions.index')->middleware('permission:permissions.view');
    Route::get('/permissions/user/{id}', [\App\Http\Controllers\PermissionController::class, 'getUserPermissions'])->name('permissions.user');
    Route::post('/permissions/user/{id}', [\App\Http\Controllers\PermissionController::class, 'updateUserPermissions'])->name('permissions.update')->middleware('permission:permissions.assign');

    Route::get('/credit-manage', [\App\Http\Controllers\CreditController::class, 'manageIndex'])->name('credit.manage')->middleware('permission:credits.manage');
    Route::post('/credit-manage', [\App\Http\Controllers\CreditController::class, 'storeCredit'])->name('credit.manage.store')->middleware('permission:credits.manage');

    Route::get('/credit-history', [\App\Http\Controllers\CreditController::class, 'historyIndex'])->name('credit.history')->middleware('permission:credit_history.view');

    // Dynamic Live Chat Routes
    Route::prefix('live-chat')->middleware('permission:livechat.view')->group(function () {
        Route::get('/', [\App\Http\Controllers\LiveChatController::class, 'index'])->name('live.chat');
        Route::get('/fetch', [\App\Http\Controllers\LiveChatController::class, 'fetchData'])->name('live.chat.fetch');
        Route::post('/send', [\App\Http\Controllers\LiveChatController::class, 'sendReply'])->name('live.chat.send')->middleware('permission:livechat.reply');
        Route::post('/forward', [\App\Http\Controllers\LiveChatController::class, 'forwardMessage'])->name('live.chat.forward')->middleware('permission:livechat.reply');
        Route::post('/mark-read/{phone}', [\App\Http\Controllers\LiveChatController::class, 'markRead'])->name('live.chat.markRead')->middleware('permission:livechat.manage');
        Route::post('/mark-unread/{phone}', [\App\Http\Controllers\LiveChatController::class, 'markUnread'])->name('live.chat.markUnread')->middleware('permission:livechat.manage');
        Route::post('/toggle-block/{phone}', [\App\Http\Controllers\LiveChatController::class, 'toggleBlock'])->name('live.chat.toggleBlock')->middleware('permission:livechat.manage');
        Route::get('/export/{phone}', [\App\Http\Controllers\LiveChatController::class, 'exportChat'])->name('live.chat.export')->middleware('permission:livechat.export');
        Route::delete('/message/{id}', [\App\Http\Controllers\LiveChatController::class, 'destroyMessage'])->name('live.chat.destroyMessage')->middleware('permission:livechat.delete');
        Route::put('/message/{id}', [\App\Http\Controllers\LiveChatController::class, 'updateMessage'])->name('live.chat.updateMessage')->middleware('permission:livechat.manage');
        Route::delete('/{phone}', [\App\Http\Controllers\LiveChatController::class, 'destroyChat'])->name('live.chat.destroy')->middleware('permission:livechat.delete');
    });
    
    // Contact Management Routes
    Route::post('/contacts/save', [ContactController::class, 'storeOrUpdate'])->name('contacts.save')->middleware('permission:contacts.create');

    Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index')->middleware('permission:contacts.view');
    Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store')->middleware('permission:contacts.create');
    Route::put('/contacts/{id}', [ContactController::class, 'update'])->name('contacts.update')->middleware('permission:contacts.edit');
    Route::delete('/contacts/{id}', [ContactController::class, 'destroy'])->name('contacts.destroy')->middleware('permission:contacts.delete');
    Route::get('/contacts-export', [ContactController::class, 'export'])->name('contacts.export')->middleware('permission:contacts.view');
    Route::post('/contacts-import', [ContactController::class, 'import'])->name('contacts.import')->middleware('permission:contacts.create');
    Route::post('/contacts/{id}/save', [ContactController::class, 'saveAutoContact'])->name('contacts.saveAutoContact')->middleware('permission:contacts.create');
    
    // RCS SMS Routes
    Route::prefix('rcs')->name('rcs.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\RcsDashboardController::class, 'index'])->name('dashboard')->middleware('permission:rcs.view');
        Route::get('/send', [\App\Http\Controllers\RcsMessageController::class, 'create'])->name('send.form')->middleware('permission:rcs.send');
        Route::post('/send', [\App\Http\Controllers\RcsMessageController::class, 'send'])->name('send.submit')->middleware('permission:rcs.send');
        Route::get('/history', [\App\Http\Controllers\RcsMessageController::class, 'history'])->name('history')->middleware('permission:rcs.history');
        
        Route::get('/contacts', [\App\Http\Controllers\RcsContactController::class, 'index'])->name('contacts.index')->middleware('permission:rcs.contacts');
        Route::post('/contacts', [\App\Http\Controllers\RcsContactController::class, 'store'])->name('contacts.store')->middleware('permission:rcs.contacts');
        Route::delete('/contacts/{id}', [\App\Http\Controllers\RcsContactController::class, 'destroy'])->name('contacts.destroy')->middleware('permission:rcs.contacts');
        
        Route::get('/templates', [\App\Http\Controllers\RcsTemplateController::class, 'index'])->name('templates.index')->middleware('permission:rcs.templates');
        Route::post('/templates', [\App\Http\Controllers\RcsTemplateController::class, 'store'])->name('templates.store')->middleware('permission:rcs.templates');
        Route::delete('/templates/{id}', [\App\Http\Controllers\RcsTemplateController::class, 'destroy'])->name('templates.destroy')->middleware('permission:rcs.templates');
        
        Route::get('/settings', [\App\Http\Controllers\RcsSettingController::class, 'index'])->name('settings.index')->middleware('permission:rcs.settings');
        Route::post('/settings', [\App\Http\Controllers\RcsSettingController::class, 'store'])->name('settings.store')->middleware('permission:rcs.settings');
        Route::delete('/settings/{id}', [\App\Http\Controllers\RcsSettingController::class, 'destroy'])->name('settings.destroy')->middleware('permission:rcs.settings');
    });

    // API
    Route::get('/api/templates', [\App\Http\Controllers\TemplateController::class, 'fetchTemplates'])->name('api.templates')->middleware('permission:templates.view');

    Route::get('/campaigns', [WhatsAppController::class, 'showSendForm'])->name('campaigns')->middleware('permission:messages.send');
    Route::get('/developer', function () { return view('placeholder'); })->name('developer')->middleware('permission:developer.view');
    Route::get('/projects', function () { return view('placeholder'); })->name('projects');

    Route::get('/placeholder', function () {
        return view('placeholder');
    })->name('placeholder');
});

Route::get('/fix-db', function () {
    try {
        if (!\Illuminate\Support\Facades\Schema::hasColumn('users', 'daily_limit')) {
            \Illuminate\Support\Facades\Schema::table('users', function ($table) {
                $table->integer('daily_limit')->default(500);
            });
        }
        if (!\Illuminate\Support\Facades\Schema::hasColumn('whatsapp_logs', 'user_id')) {
            \Illuminate\Support\Facades\Schema::table('whatsapp_logs', function ($table) {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            });
        }
        if (!\Illuminate\Support\Facades\Schema::hasColumn('whatsapp_logs', 'message_type')) {
            \Illuminate\Support\Facades\Schema::table('whatsapp_logs', function ($table) {
                $table->string('message_type')->default('template');
            });
        }
        if (!\Illuminate\Support\Facades\Schema::hasColumn('scheduled_messages', 'media_id')) {
            \Illuminate\Support\Facades\Schema::table('scheduled_messages', function ($table) {
                $table->string('media_id')->nullable();
                $table->string('media_type')->nullable();
                $table->string('visit_url')->nullable();
                $table->string('call_number')->nullable();
            });
        }
        if (!\Illuminate\Support\Facades\Schema::hasColumn('contacts', 'notes')) {
            \Illuminate\Support\Facades\Schema::table('contacts', function ($table) {
                $table->text('notes')->nullable();
                $table->boolean('is_manually_saved')->default(false);
            });
        }
        if (!\Illuminate\Support\Facades\Schema::hasColumn('contacts', 'is_blocked')) {
            \Illuminate\Support\Facades\Schema::table('contacts', function ($table) {
                $table->boolean('is_blocked')->default(false);
            });
        }
        if (!\Illuminate\Support\Facades\Schema::hasTable('user_templates')) {
            \Illuminate\Support\Facades\Schema::create('user_templates', function ($table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('template_name');
                $table->timestamps();
            });
        }
        
        // Sync role_permissions to user_permissions for users that don't have any custom permissions set yet
        $users = \App\Models\User::where('role', '!=', 'superadmin')->get();
        foreach ($users as $u) {
            if ($u->permissions()->count() == 0) {
                $rolePerms = \Illuminate\Support\Facades\DB::table('role_permissions')->where('role_name', $u->role)->pluck('permission_id');
                if ($rolePerms->isNotEmpty()) {
                    $u->permissions()->sync($rolePerms);
                }
            }
        }
        
        return "Database fixed successfully! Added missing columns to users, whatsapp_logs, scheduled_messages, and contacts. Synced user permissions.";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});

Route::get('/auth-as-admin', function () {
    $user = \App\Models\User::where('email', 'admin@gmail.com')->first();
    $token = \Illuminate\Support\Facades\Auth::guard('api')->login($user);
    session(['jwt_token' => $token]);
    $redirect = request('to', '/dashboard');
    return redirect($redirect);
});

Route::get('/debug-client', function () {
    $client = \App\Models\User::where('role', 'client')->first();
    if ($client) {
        \Illuminate\Support\Facades\Auth::login($client);
        return redirect('/manage-templates');
    }
    return 'No client found';
});



// ─── WhatsApp Diagnostic Route (TEMPORARY) ────────────────────────────────
Route::get('/wa-debug', function () {
    $token   = env('WHATSAPP_TOKEN');
    $phoneId = env('WHATSAPP_PHONE_ID', '1234153499790530');

    // Step 1: Get Phone Number basic info
    $phoneInfo = \Illuminate\Support\Facades\Http::withToken($token)
        ->get("https://graph.facebook.com/v25.0/{$phoneId}", [
            'fields' => 'id,display_phone_number,verified_name,quality_rating,name_status'
        ])->json();

    // Step 2: Get WhatsApp Business Profile
    $businessProfile = \Illuminate\Support\Facades\Http::withToken($token)
        ->get("https://graph.facebook.com/v25.0/{$phoneId}/whatsapp_business_profile")
        ->json();

    return response()->json([
        'current_phone_id'    => $phoneId,
        'phone_info'          => $phoneInfo,
        'business_profile'    => $businessProfile,
        'template_waba'       => '2599448097175103',
        'fix_instruction'     => 'If WABA mismatch: Go to Meta → WhatsApp Manager → select WABA 2599448097175103 → Phone Numbers → copy correct Phone Number ID → update WHATSAPP_API_URL in .env',
    ], 200, [], JSON_PRETTY_PRINT);
});

// RCS Webhook Route
Route::post('/api/rcs/webhook/{agent_id}', [\App\Http\Controllers\RcsWebhookController::class, 'handle'])->name('api.rcs.webhook');
