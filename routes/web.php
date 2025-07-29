<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\RolPermisoModuloController;
use App\Helpers\PermisosHelper;
use App\Http\Controllers\ReparacionController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\DetalleCompraController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\DetallePedidoController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\BitacoraController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\ReportesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\PersonaController;



Route::middleware(['auth'])->group(function () {
    Route::resource('usuarios', UsuarioController::class);
    Route::get('/reparaciones/exportar-pdf', [ReparacionController::class, 'exportarPDF'])
         ->name('reparaciones.exportarPDF');
         Route::get('/producto/exportar-pdf', [ProductoController::class, 'exportarPDF'])
        ->name('producto.exportarPDF');
        Route::get('/ventas/exportar-pdf', [VentaController::class, 'exportarPDF'])
     ->name('ventas.exportarPDF');
     Route::get('/pedidos/exportar-pdf', [PedidoController::class, 'exportarPDF'])
     ->name('pedidos.exportarPDF');
     Route::get('/facturas/exportar-pdf', [FacturaController::class, 'exportarPDF'])
     ->name('facturas.exportarPDF');
     Route::get('/compras/exportar-pdf', [CompraController::class, 'exportarPDF'])
     ->name('compras.exportarPDF');
     Route::get('proveedores/exportar-pdf', [ProveedorController::class, 'exportarPDF'])->name('proveedores.exportarPDF');
     Route::get('empleados/exportar-pdf', [EmpleadoController::class, 'exportarPDF'])->name('empleados.exportarPDF');
     Route::get('clientes/exportarPDF', [ClienteController::class, 'exportarPDF'])->name('clientes.exportarPDF');
     Route::get('/facturas/{id}/pdf', [FacturaController::class, 'generarFacturaPDF'])->name('facturas.pdf');
     Route::get('empresa/exportar-pdf', [EmpresaController::class, 'exportarPDF'])->name('empresa.exportarPDF');
     Route::get('/detallecompras/exportar-pdf', [DetalleCompraController::class, 'exportarPDF'])
         ->name('detallecompras.exportarPDF');


});


Route::middleware(['auth'])->group(function () {
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

   Route::resource('usuarios', UsuarioController::class)->middleware(['auth']);

});


Route::get('/2fa/code', [EmailVerificationController::class, 'showForm'])
    ->middleware('auth')->name('2fa.code.form');

Route::post('/2fa/code', [EmailVerificationController::class, 'verifyCode'])
    ->middleware('auth')->name('2fa.code.verify');


Route::get('/2fa/setup', [TwoFactorController::class, 'setup'])->middleware('auth')->name('2fa.setup');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/2fa/resend', [EmailVerificationController::class, 'resendCode'])
    ->middleware('auth')
    ->name('2fa.code.resend');

Route::middleware(['auth'])->group(function () {
    Route::get('/roles', [RolController::class, 'index'])->name('roles.index');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/permisos', [PermisoController::class, 'index'])->name('permisos.index');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/roles', [RolController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RolController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RolController::class, 'store'])->name('roles.store');
    Route::get('/roles/{id}/edit', [RolController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{id}', [RolController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{id}', [RolController::class, 'destroy'])->name('roles.destroy');
});

Route::resource('permisos', PermisoController::class);

Route::get('/roles/{id}/permisos', [RolController::class, 'editPermisos'])->name('roles.permisos.edit');
Route::put('/roles/{id}/permisos', [RolController::class, 'updatePermisos'])->name('roles.permisos.update');

Route::get('/roles/permisos', [RolPermisoModuloController::class, 'index'])->name('roles.permisos');
Route::post('/roles/permisos/guardar', [RolPermisoModuloController::class, 'guardar'])->name('roles.permisos.guardar');

Route::resource('reparaciones', ReparacionController::class);

Route::resource('producto', ProductoController::class);

Route::resource('empleados', EmpleadoController::class);

Route::resource('clientes', ClienteController::class);


Route::resource('proveedores', ProveedorController::class);

Route::resource('compras', CompraController::class);

Route::resource('detallecompras', DetalleCompraController::class);

Route::resource('ventas', VentaController::class);

Route::resource('pedidos', PedidoController::class);

Route::resource('detalle_pedidos', DetallePedidoController::class);


Route::put('pedido/{pedido}', [PedidoController::class, 'update'])->name('pedido.update');

// Rutas simples y funcionales para backups
Route::middleware(['auth'])->group(function () {
    Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
    Route::post('/backups', [BackupController::class, 'store'])->name('backups.store');
    Route::post('/backups/{id}/restore', [BackupController::class, 'restore'])->name('backups.restore');
});




Route::middleware(['auth'])->group(function () {
    Route::get('/bitacoras', [BitacoraController::class, 'index'])->name('bitacoras.index');
    Route::get('/bitacoras/create', [BitacoraController::class, 'create'])->name('bitacoras.create');
    Route::post('/bitacoras', [BitacoraController::class, 'store'])->name('bitacoras.store');
    Route::get('/bitacoras/{id}/edit', [BitacoraController::class, 'edit'])->name('bitacoras.edit');
    Route::put('/bitacoras/{id}', [BitacoraController::class, 'update'])->name('bitacoras.update');
    Route::delete('/bitacoras/{id}', [BitacoraController::class, 'destroy'])->name('bitacoras.destroy');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/facturas', [FacturaController::class, 'index'])->name('facturas.index');
    Route::get('/facturas/create', [FacturaController::class, 'create'])->name('facturas.create');
    Route::post('/facturas', [FacturaController::class, 'store'])->name('facturas.store');
    Route::get('/facturas/{id}/edit', [FacturaController::class, 'edit'])->name('facturas.edit');
    Route::put('/facturas/{id}', [FacturaController::class, 'update'])->name('facturas.update');
    Route::delete('/facturas/{id}', [FacturaController::class, 'destroy'])->name('facturas.destroy');
    Route::put('/facturas/{id}/cancelar', [App\Http\Controllers\FacturaController::class, 'cancelar'])->name('facturas.cancelar');

});



// Página principal del módulo de reportes
Route::get('reportes', [ReportesController::class, 'index'])->name('reporte.index');

// Mostrar un tipo específico de reporte (bitácoras, ventas, compras, movimientos)
Route::get('reportes/{tipo}', [ReportesController::class, 'show'])->name('reporte.show');

// Si usas un formulario para crear una configuración de reporte
Route::get('reportes/create', [ReportesController::class, 'create'])->name('reporte.create');
Route::post('reportes', [ReportesController::class, 'store'])->name('reporte.store');

// Si deseas permitir edición de una configuración de reporte (opcional)
Route::get('reportes/{id}/edit', [ReportesController::class, 'edit'])->name('reporte.edit');
Route::put('reportes/{id}', [ReportesController::class, 'update'])->name('reporte.update');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])
    ->name('dashboard');

    Route::resource('ventas', VentaController::class);

    Route::resource('empresa', EmpresaController::class);

        

    Route::get('/test-pdf', function () {
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHtml('<h1 style="color: navy">PDF generado correctamente</h1><p>¡Hola, Jeisson!</p>');
    return $pdf->download('test.pdf');
});




Route::middleware(['auth'])->group(function () {
    Route::get('/persona', [PersonaController::class, 'index'])->name('persona.index');
    Route::post('/persona', [PersonaController::class, 'store'])->name('persona.store');
    Route::put('/persona/{id}', [PersonaController::class, 'update'])->name('persona.update');
    Route::delete('/persona/{id}', [PersonaController::class, 'destroy'])->name('persona.destroy');

    // Ruta para exportar PDF
    Route::get('/persona/exportar-pdf', [PersonaController::class, 'exportarPDF'])->name('persona.exportarPDF');
});


require __DIR__.'/auth.php';


