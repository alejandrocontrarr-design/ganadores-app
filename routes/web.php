<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GanadorController;
use App\Http\Controllers\MencionController;
use App\Http\Controllers\Admin\LimpiezaMencionesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('ganadores.index');
});

// Grupo de rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {

    // 1. Listado principal y exportación/importación de ganadores
    Route::get('/ganadores', [GanadorController::class, 'index'])->name('ganadores.index');
    Route::get('/ganadores-exportar', [GanadorController::class, 'export'])->name('ganadores.export');
    Route::post('/ganadores-importar', [GanadorController::class, 'import'])->name('ganadores.import');

    // 2. Crear registros de ganadores
    Route::get('/ganadores/crear', [GanadorController::class, 'create'])->name('ganadores.create');
    Route::post('/ganadores', [GanadorController::class, 'store'])->name('ganadores.store');
    Route::delete('/ganadores/eliminar-por-ano', [GanadorController::class, 'destroyPorAno'])->name('ganadores.destroyPorAno');

    // 3. Rutas individuales de ganadores
    Route::get('/ganadores/{id}/editar', [GanadorController::class, 'edit'])->name('ganadores.edit');
    Route::put('/ganadores/{id}', [GanadorController::class, 'update'])->name('ganadores.update');
    Route::delete('/ganadores/{id}', [GanadorController::class, 'destroy'])->name('ganadores.destroy');

    // 4. Rutas de Menciones
    Route::get('/menciones', [MencionController::class, 'index'])->name('menciones.index');
    Route::post('/menciones', [MencionController::class, 'storeOrUpdate'])->name('menciones.store');
    Route::post('/menciones/multiples', [MencionController::class, 'storeMultiple'])->name('menciones.storeMultiple');

    // 5. Rutas de Limpieza y Exportación de Menciones por Mes (Administrador)
    Route::post('/menciones/limpiar-mes', [LimpiezaMencionesController::class, 'limpiarMes'])->name('menciones.limpiarMes');
    Route::post('/menciones/exportar-mes', [LimpiezaMencionesController::class, 'exportarMes'])->name('menciones.exportarMes');

}); // <--- ESTA LLAVE CIERRA EL GRUPO Route::middleware(['auth'])->group(function () {

require __DIR__.'/auth.php';