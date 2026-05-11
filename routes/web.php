<?php

use App\Http\Controllers\Admin\CandidateController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\VoterController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\VoterAccessController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GradeController;
use App\Models\Voter;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('home');

Route::post('/ingresar-cedula', [VoterAccessController::class, 'enter'])->name('voter.enter');

Route::get('/votar', [VoteController::class, 'create'])
    ->middleware('voter.session')
    ->name('vote.create');

Route::post('/votar', [VoteController::class, 'store'])
    ->middleware('voter.session')
    ->name('vote.store');

Route::get('/voto-exitoso', function () {
    $voterId = session('voter_id');

    if (!$voterId) {
        return redirect()->route('home');
    }

    $voter = Voter::find($voterId);
    session()->forget('voter_id');

    return view('voter.success', compact('voter'));
})->name('voter.success');

Route::prefix('admin')->group(function () {

    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login.post');

    Route::middleware(['auth', 'admin'])->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        Route::resource('/candidatos', CandidateController::class)->names('admin.candidatos');
        Route::resource('/votantes', VoterController::class)->names('admin.votantes');

        Route::post('/votantes/import', [VoterController::class, 'importExcel'])
            ->name('admin.votantes.import');

        Route::post('/votantes/upload-photos', [VoterController::class, 'uploadPhotos'])
            ->name('admin.votantes.upload_photos');

        Route::post('/candidatos/import', [CandidateController::class, 'importExcel'])
            ->name('admin.candidatos.import');

        Route::post('/candidatos/upload-photos', [CandidateController::class, 'uploadPhotos'])
            ->name('admin.candidatos.upload_photos');

        Route::get('/reporte/pdf', [ReportController::class, 'pdf'])->name('admin.report.pdf');

        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

        // --- ELIMINADO el Route::prefix('admin') y middleware(['auth']) duplicado ---
        // Ya están dentro del prefijo 'admin' y del middleware 'auth' arriba
        
        Route::get('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('admin.settings');
        Route::post('/settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('admin.settings.update');

        Route::get('/grados', [GradeController::class, 'index'])->name('admin.grades.index');
        Route::post('/grados/active', [GradeController::class, 'updateActive'])->name('admin.grades.active');
        Route::post('/grados/reset', [GradeController::class, 'resetVotes'])->name('admin.grades.reset');
        Route::post('/grados/signatures', [GradeController::class, 'updateSignatures'])->name('admin.grades.signatures');

    });
});