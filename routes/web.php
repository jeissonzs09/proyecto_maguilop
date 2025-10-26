<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\{
    Auth\AuthenticatedSessionController,
    Auth\NewPasswordController,
    // Auth\PasswordResetController,
    BackupController,
    BitacoraController,
    ClienteController,
    CompraController,
    DetalleCompraController,
    DetallePedidoController,
    DashboardController,
    EmpleadoController,
    EmailVerificationController,
    EmpresaController,
    FacturaController,
    PedidoController,
    PermisoController,
    PersonaController,
    ProductoController,
    ProfileController,
    ReparacionController,
    ReportesController,
    RolController,
    RolPermisoModuloController,
    TwoFactorController,
    UsuarioController,
    VentaController,
    ProveedorController,
    CaiController,
    CuentaPorCobrarController,
    PagoController,
    HelpController,
};

// Ruta de prueba de correo
Route::get('/probar-correo', [EmailVerificationController::class, 'pruebaCorreo']);

// Página de bienvenida
Route::get('/', function () {
    return view('welcome');
});

// Autenticación
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

   // Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
    //Route::post('/password/email', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

// Verificación en dos pasos (2FA)
Route::middleware(['auth'])->group(function () {
    Route::get('/2fa/code', [EmailVerificationController::class, 'showForm'])->name('2fa.code.form');
    Route::post('/2fa/code', [EmailVerificationController::class, 'verifyCode'])->name('2fa.code.verify');
    Route::post('/2fa/resend', [EmailVerificationController::class, 'resendCode'])->name('2fa.code.resend');
    Route::get('/2fa/setup', [TwoFactorController::class, 'setup'])->name('2fa.setup');
    Route::get('/backups/descargar/{archivo}', [BackupController::class, 'download'])->name('backups.download');
});

// Dashboard
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Perfil de usuario
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas protegidas (recursos y CRUD)
Route::middleware(['auth'])->group(function () {
    Route::resource('usuarios', UsuarioController::class);
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
    Route::resource('empresa', EmpresaController::class);
    Route::resource('permisos', PermisoController::class);

    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::put('pedido/{pedido}', [PedidoController::class, 'update'])->name('pedido.update');

    // Roles
    Route::get('/roles', [RolController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RolController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RolController::class, 'store'])->name('roles.store');
    Route::get('/roles/{id}/edit', [RolController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{id}', [RolController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{id}', [RolController::class, 'destroy'])->name('roles.destroy');

    Route::get('/roles/{id}/permisos', [RolController::class, 'editPermisos'])->name('roles.permisos.edit');
    Route::put('/roles/{id}/permisos', [RolController::class, 'updatePermisos'])->name('roles.permisos.update');
    Route::get('/roles/permisos', [RolPermisoModuloController::class, 'index'])->name('roles.permisos');
    Route::post('/roles/permisos/guardar', [RolPermisoModuloController::class, 'guardar'])->name('roles.permisos.guardar');

    // Bitácora
    Route::resource('bitacoras', BitacoraController::class);

    // Backups
    Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
    Route::post('/backups', [BackupController::class, 'store'])->name('backups.store');
    Route::post('/backups/{id}/restore', [BackupController::class, 'restore'])->name('backups.restore');

    // Facturas
    Route::resource('facturas', FacturaController::class);
    Route::put('/facturas/{id}/cancelar', [FacturaController::class, 'cancelar'])->name('facturas.cancelar');

    // Reportes
    Route::get('reportes', [ReportesController::class, 'index'])->name('reporte.index');
    Route::get('reportes/{tipo}', [ReportesController::class, 'show'])->name('reporte.show');
    Route::get('reportes/create', [ReportesController::class, 'create'])->name('reporte.create');
    Route::post('reportes', [ReportesController::class, 'store'])->name('reporte.store');
    Route::get('reportes/{id}/edit', [ReportesController::class, 'edit'])->name('reporte.edit');
    Route::put('reportes/{id}', [ReportesController::class, 'update'])->name('reporte.update');

    // Persona
    Route::get('/persona', [PersonaController::class, 'index'])->name('persona.index');
    Route::post('/persona', [PersonaController::class, 'store'])->name('persona.store');
    Route::put('/persona/{id}', [PersonaController::class, 'update'])->name('persona.update');
    Route::delete('/persona/{id}', [PersonaController::class, 'destroy'])->name('persona.destroy');
    Route::get('/persona/exportar-pdf', [PersonaController::class, 'exportarPDF'])->name('persona.exportarPDF');

    //CAI
    Route::get('/cai', [CaiController::class, 'index'])->name('cai.index');
    Route::post('/cai', [CaiController::class, 'store'])->name('cai.store');

    //Pagos
    Route::post('/pagos', [PagoController::class, 'store'])->name('pagos.store');

    //registro de pagos
    Route::get('/cuentas-por-cobrar/{id}/pagos', [App\Http\Controllers\PagoController::class, 'historial'])->name('pagos.historial');
    Route::get('/cuentas-por-cobrar', [CuentaPorCobrarController::class, 'index'])->name('cuentas-por-cobrar.index');

    //Foto Perfil
    Route::post('/perfil/foto', [ProfileController::class, 'updateAvatar'])
    ->middleware('auth')
    ->name('perfil.foto');

    //Help
    Route::get('/help', [HelpController::class, 'index'])->name('help.index');

    // Exportar PDF
    Route::get('/reparaciones/exportar-pdf', [ReparacionController::class, 'exportarPDF'])->name('reparaciones.exportarPDF');
    Route::get('/producto/exportar-pdf', [ProductoController::class, 'exportarPDF'])->name('producto.exportarPDF');
    Route::get('/ventas/exportar-pdf', [VentaController::class, 'exportarPDF'])->name('ventas.exportarPDF');
    Route::get('/pedidos/exportar-pdf', [PedidoController::class, 'exportarPDF'])->name('pedidos.exportarPDF');
    Route::get('/facturas/exportar-pdf', [FacturaController::class, 'exportarPDF'])->name('facturas.exportarPDF');
    Route::get('/compras/exportar-pdf', [CompraController::class, 'exportarPDF'])->name('compras.exportarPDF');
    Route::get('/proveedores/exportar-pdf', [ProveedorController::class, 'exportarPDF'])->name('proveedores.exportarPDF');
    Route::get('/empleados/exportar-pdf', [EmpleadoController::class, 'exportarPDF'])->name('empleados.exportarPDF');
    Route::get('/clientes/exportarPDF', [ClienteController::class, 'exportarPDF'])->name('clientes.exportarPDF');
    Route::get('/facturas/{id}/pdf', [FacturaController::class, 'generarFacturaPDF'])->name('facturas.pdf');
    Route::get('/empresa/exportar-pdf', [EmpresaController::class, 'exportarPDF'])->name('empresa.exportarPDF');
    Route::get('/detallecompras/exportar-pdf', [DetalleCompraController::class, 'exportarPDF'])->name('detallecompras.exportarPDF');
});

// Ruta de prueba de PDF
Route::get('/test-pdf', function () {
    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHtml('<h1 style="color: navy">PDF generado correctamente</h1><p>¡Hola, Jeisson!</p>');
    return $pdf->download('test.pdf');
});

// Ruta de prueba de correo simple
Route::get('/test-email', function () {
    Mail::raw('Correo de prueba enviado desde Laravel', function ($message) {
        $message->to('tu-email@dominio.com')
                ->subject('Prueba de correo');
    });
    return 'Correo enviado';
});

// Rutas adicionales de Bitácora
Route::get('/bitacoras/exportar-pdf', [BitacoraController::class, 'exportarPDF'])
     ->name('bitacoras.exportarPDF');

Route::get('/bitacoras/exportar-excel', [BitacoraController::class, 'exportarExcel'])
     ->name('bitacoras.exportarExcel');

// Eliminar toda la bitácora con respaldo PDF
Route::delete('/bitacoras/destroy-pdf', [BitacoraController::class, 'destroyWithPDF'])
     ->name('bitacoras.destroyPDF');


// Activar/Desactivar bitácora
Route::post('/bitacoras/toggle', [BitacoraController::class, 'toggleBitacora'])
     ->name('bitacoras.toggle');


// Rutas de recurso básicas (CRUD)
Route::resource('bitacoras', BitacoraController::class)->except(['show']);

Route::get('/personas/exportar-excel', [PersonaController::class, 'exportarExcel'])->name('persona.exportarExcel');



// Incluir otras rutas de autenticación si existen
require __DIR__.'/auth.php';