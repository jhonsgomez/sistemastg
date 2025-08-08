<?php

use App\Http\Controllers\AjustesController;
use App\Http\Controllers\BaseDocumentalController;
use App\Http\Controllers\FechasController;
use App\Http\Controllers\HistoricoController;
use App\Http\Controllers\LineasInvestigacionController;
use App\Http\Controllers\ModalidadController;
use App\Http\Controllers\NivelController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProyectoGradoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RoadMapController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SolicitudBancoController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Rutas para Dashboard
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/dashboard', function () {
        return redirect()->route('dashboard');
    })->name('init');

    // Rutas para Roles
    Route::middleware('permission:view_roles')->prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/data', [RoleController::class, 'getData'])->name('roles.data');
        Route::post('/', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/{id}', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/{id}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::get('/{id}/permissions', [RoleController::class, 'getPermissions'])->name('roles.permissions');
        Route::post('/{id}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.updatePermissions');
    });

    // Rutas para Permisos
    Route::middleware('permission:view_permissions')->prefix('permissions')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])->name('permissions.index');
        Route::get('/data', [PermissionController::class, 'getData'])->name('permissions.data');
        Route::post('/', [PermissionController::class, 'store'])->name('permissions.store');
        Route::get('/{id}', [PermissionController::class, 'edit'])->name('permissions.edit');
        Route::put('/{id}', [PermissionController::class, 'update'])->name('permissions.update');
        Route::delete('/{id}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    });

    // Rutas para Usuarios
    Route::middleware('permission:view_users')->prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('/data', [UserController::class, 'getData'])->name('users.data');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::put('/{id}', [UserController::class, 'update'])->name('users.update');
        Route::get('/{id}/roles', [UserController::class, 'getRoles'])->name('users.roles');
        Route::post('/{id}/roles', [UserController::class, 'updateRoles'])->name('users.updateRoles');
        Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Rutas publica para Usuarios
    Route::prefix('users')->group(function () {
        Route::get('/{id}', [UserController::class, 'edit'])->name('users.edit');
    });

    // Rutas para modalidades
    Route::prefix('modalidades')->group(function () {
        Route::get('/{id}', [ModalidadController::class, 'getModalidad'])->name('modalidades.info');
    });

    // Rutas para niveles
    Route::prefix('niveles')->group(function () {
        Route::get('/{id}', [NivelController::class, 'getNivel'])->name('niveles.info');
    });

    // Rutas para lineas de investigación
    Route::prefix('lineas-investigacion')->group(function () {
        Route::get('/{id}', [LineasInvestigacionController::class, 'getLinea'])->name('lineas_investigacion.info');
    });

    // Rutas para propuestas del banco de ideas
    Route::middleware('permission:view_propuestas_banco')->prefix('propuestas-banco')->group(function () {
        Route::get('/', [SolicitudBancoController::class, 'index'])->name('propuestas.index');
        Route::get('/data', [SolicitudBancoController::class, 'getData'])->name('propuestas.data');
        Route::post('/', [SolicitudBancoController::class, 'store'])->name('propuestas.store');
        Route::get('/{id}/campos', [SolicitudBancoController::class, 'getValorCampos'])->name('propuestas.campos');
        Route::post('/responder', [SolicitudBancoController::class, 'responderSolicitud'])->name('propuestas.responder');
        Route::post('/publicar', [SolicitudBancoController::class, 'repostSolicitud'])->name('propuestas.publicar');
    });

    // Rutas para el banco de ideas
    Route::middleware('permission:view_banco_ideas')->prefix('banco')->group(function () {
        Route::get('/', [SolicitudBancoController::class, 'bancoIndex'])->name('banco.index');
        Route::get('/data', [SolicitudBancoController::class, 'bancoData'])->name('banco.data');
        Route::delete('/{id}', [SolicitudBancoController::class, 'destroySolicitud'])->name('banco.destroy');

        Route::prefix('reporte')->group(function () {
            Route::post('/', [SolicitudBancoController::class, 'generarReporte'])->name('banco.reporte');
        });
    });

    // Rutas para propuestas de proyectos de grado
    Route::middleware('permission:view_proyectos_grado')->prefix('proyectos')->group(function () {
        // Rutas para propuestas de proyectos de grado
        Route::get('/', [ProyectoGradoController::class, 'index'])->name('proyectos.index');
        Route::get('/data', [ProyectoGradoController::class, 'getData'])->name('proyectos.data');
        Route::post('/', [ProyectoGradoController::class, 'store'])->name('proyectos.store');
        Route::get('/{id}/campos', [SolicitudBancoController::class, 'getValorCampos'])->name('proyectos.campos');
        Route::post('/responder', [ProyectoGradoController::class, 'responderSolicitud'])->name('proyectos.responder');

        // Rutas para habilitar y deshabilitar proyectos
        Route::post('/deshabilitar', [ProyectoGradoController::class, 'deshabilitarProyecto'])->name('proyectos.deshabilitar');
        Route::post('/habilitar', [ProyectoGradoController::class, 'habilitarProyecto'])->name('proyectos.habilitar');

        // Rutas para estimulo ICFES
        Route::post('/icfes', [ProyectoGradoController::class, 'estimuloIcfesEstudiante'])->name('proyectos.estimulo_icfes_estudiante');
        Route::post('/icfes/responder', [ProyectoGradoController::class, 'estimuloIcfesComite'])->name('proyectos.estimulo_icfes_comite');

        // Rutas para RoadMap
        Route::post('/seguimiento', [RoadMapController::class, 'index'])->name('roadmap.index');
        Route::get('/seguimiento', function () {
            return redirect()->route('dashboard');
        })->name('roadmap.get');

        // Rutas para configurar el proyecto
        Route::post('/configurar/admin', [ProyectoGradoController::class, 'configAdmin'])->name('proyectos.configurar_admin');
        Route::post('/configurar', [ProyectoGradoController::class, 'configEstudiante'])->name('proyectos.configurar_estudiante');

        // Rutas para fases del proyecto
        Route::prefix('fase1')->group(function () {
            Route::post('/', [RoadMapController::class, 'fase1'])->name('roadmap.fase_1');
            Route::post('/responder', [RoadMapController::class, 'reply_fase1'])->name('roadmap.reply_fase1');
        });

        Route::prefix('fase2')->group(function () {
            Route::post('/', [RoadMapController::class, 'fase2'])->name('roadmap.fase_2');
            Route::post('/responder', [RoadMapController::class, 'reply_fase2'])->name('roadmap.reply_fase2');
        });

        Route::prefix('fase3')->group(function () {
            Route::post('/responder', [RoadMapController::class, 'reply_fase3'])->name('roadmap.reply_fase3');
        });

        Route::prefix('fase4')->group(function () {
            Route::post('/', [RoadMapController::class, 'fase4'])->name('roadmap.fase_4');
            Route::post('/responder', [RoadMapController::class, 'reply_fase4'])->name('roadmap.reply_fase4');
        });

        Route::prefix('fase5')->group(function () {
            Route::post('/responder', [RoadMapController::class, 'reply_fase5'])->name('roadmap.reply_fase5');
        });

        Route::prefix('reporte')->group(function () {
            Route::post('/', [ProyectoGradoController::class, 'generarReporte'])->name('proyectos.reporte');
        });
    });

    // Rutas para base documental
    Route::prefix('base-documental')->group(function () {
        Route::get('/', [BaseDocumentalController::class, 'index'])->name('documental.index');
    });

    // Rutas para modulo historico
    Route::middleware('permission:view_historico')->prefix('historico')->group(function () {
        Route::get('/', [HistoricoController::class, 'index'])->name('historico.index');
        Route::post('/', [HistoricoController::class, 'store'])->name('historico.store');
        Route::post('/masivo', [HistoricoController::class, 'storeMasivo'])->name('historico.store.masivo');
        Route::get('/data', [HistoricoController::class, 'getData'])->name('historico.data');
        Route::get('/{id}', [HistoricoController::class, 'edit'])->name('historico.edit');
        Route::put('/{id}', [HistoricoController::class, 'update'])->name('historico.update');
        Route::delete('/{id}', [HistoricoController::class, 'destroy'])->name('historico.destroy');
        
        Route::prefix('reporte')->group(function () {
            Route::post('/', [HistoricoController::class, 'generarReporte'])->name('historico.reporte');
        });
    });

    // Rutas para reportes
    Route::prefix('reportes')->group(function () {
        Route::post('/enviar', [ReporteController::class, 'enviarReporte'])->name('reportes.enviar');
    });

    // Rutas para ajustes
    Route::prefix('ajustes')->group(function () {
        Route::get('/', [AjustesController::class, 'index'])->name('ajustes.index');
        Route::post('/fechas', [AjustesController::class, 'fechas'])->name('ajustes.fechas');
        Route::post('/backups', [AjustesController::class, 'backups'])->name('ajustes.backups');
    });

    // Rutas para fechas
    Route::prefix('fechas')->group(function () {
        Route::get('/{periodo}', [FechasController::class, 'getFechas'])->name('fechas.info');
    });

    // Rutas para directores
    Route::prefix('director')->group(function () {
        Route::get('/', [ProyectoGradoController::class, 'index'])->name('director.index');

        // Rutas para RoadMap
        Route::post('/seguimiento', [RoadMapController::class, 'index'])->name('director.roadmap');
        Route::get('/seguimiento', function () {
            return redirect()->route('dashboard');
        })->name('director.roadmap.get');
    });

    // Rutas para evaluadores
    Route::prefix('evaluador')->group(function () {
        Route::get('/', [ProyectoGradoController::class, 'index'])->name('evaluador.index');

        // Rutas para RoadMap
        Route::post('/seguimiento', [RoadMapController::class, 'index'])->name('evaluador.roadmap');
        Route::get('/seguimiento', function () {
            return redirect()->route('dashboard');
        })->name('evaluador.roadmap.get');
    });
});
