<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GanadorController;

Route::get('/', function () {
    return redirect()->route('ganadores.index');
});

Route::middleware(['auth'])->group(function () {
    // 1. Listado principal y exportación/importación
    Route::get('/ganadores', [GanadorController::class, 'index'])->name('ganadores.index');
    Route::get('/ganadores-exportar', [GanadorController::class, 'export'])->name('ganadores.export');
    Route::post('/ganadores-importar', [GanadorController::class, 'import'])->name('ganadores.import');

    // 2. Crear registros
    Route::get('/ganadores/crear', [GanadorController::class, 'create'])->name('ganadores.create');
    Route::post('/ganadores', [GanadorController::class, 'store'])->name('ganadores.store');

    // ⚠️ RUTA DE ELIMINACIÓN MASIVA POR AÑO (Ubicada ANTES de las rutas con {id})
    Route::delete('/ganadores/eliminar-por-ano', [GanadorController::class, 'destroyPorAno'])->name('ganadores.destroyPorAno');

    // 3. Rutas individuales con parámetro {id}
    Route::get('/ganadores/{id}/editar', [GanadorController::class, 'edit'])->name('ganadores.edit');
    Route::put('/ganadores/{id}', [GanadorController::class, 'update'])->name('ganadores.update');
    Route::delete('/ganadores/{id}', [GanadorController::class, 'destroy'])->name('ganadores.destroy');
});

require __DIR__.'/auth.php';