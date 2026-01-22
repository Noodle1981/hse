<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\ChecklistAsignController;
use App\Http\Controllers\ChecklistOtController;
use App\Http\Controllers\CorreoContactoController;
use App\Http\Controllers\CorreoController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\GeneradorConstanciaController;
use App\Http\Controllers\InformeController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SedeController;
use App\Http\Controllers\SedeUserController;
use App\Http\Controllers\ResponsableConstanciaController;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\Auth\UserController;
use App\Http\Controllers\CapacitacionPresencialController;

use App\Http\Controllers\PDFController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/view', function () {
    return view('informePDF');
});


Route::get('/test-mail', [CorreoContactoController::class, 'test']);
Route::post('/consulta', [CorreoContactoController::class, 'consulta']);

Route::get('/create-example-user', [UserController::class, 'createExampleUser']);
Route::post('/login', [UserController::class, 'login']);

Route::get('/listar_colaboradores_admin', [UserController::class, 'index']); //->middleware('auth:sanctum');
Route::get('/listar_admins', [UserController::class, 'admins']); //->middleware('auth:sanctum');
Route::get('/listar_tecnicos', [UserController::class, 'colaboradores']); //->middleware('auth:sanctum');
Route::get('/obtener_datos_colaborador_admin/{id}', [UserController::class, 'datos']); //->middleware('auth:sanctum');
Route::post('registro_colaborador_admin', [UserController::class, 'store'])->middleware('auth:sanctum');
Route::put('/editar_colaborador_admin/{id} ', [UserController::class, 'update']); //->middleware('auth:sanctum');
Route::put('/cambiar_estado_colaboradores_admin/{id} ', [UserController::class, 'estado']); //->middleware('auth:sanctum');
Route::delete('/eliminar_colaborador_admin/{id}', [UserController::class, 'destroy']); //->middleware('auth:sanctum');

Route::get('/listar_empresas', [EmpresaController::class, 'index'])->middleware('auth:sanctum');
Route::post('/registrar_empresa', [EmpresaController::class, 'store']); //->middleware('auth:sanctum');
Route::put('/editar_empresa/{id} ', [EmpresaController::class, 'update']); //->middleware('auth:sanctum');
Route::get('/obtener_datos_empresa/{id}', [EmpresaController::class, 'datos']); //->middleware('auth:sanctum');
Route::delete('/eliminar_empresa/{id}', [EmpresaController::class, 'destroy']); //->middleware('auth:sanctum');

Route::get('/listar_sedes', [SedeController::class, 'index']); //->middleware('auth:sanctum');
Route::post('/registrar_sede', [SedeController::class, 'store']); //->middleware('auth:sanctum');
Route::put('/editar_sede/{id} ', [SedeController::class, 'update']); //->middleware('auth:sanctum');
Route::delete('/eliminar_sede/{id}', [SedeController::class, 'destroy']); //->middleware('auth:sanctum');


Route::get('/todas_constancias_visitas_asignadas', [GeneradorConstanciaController::class, 'index'])->middleware('auth:sanctum');
Route::get('/listar_constancias_visitas/{id}/{cant?}', [GeneradorConstanciaController::class, 'constancias'])->middleware('auth:sanctum');
Route::post('/crear_constancia_admin', [GeneradorConstanciaController::class, 'store'])->middleware('auth:sanctum');
Route::put('/actualizar-constancia/{id} ', [GeneradorConstanciaController::class, 'update']); //->middleware('auth:sanctum');
Route::get('/obtener_datos_constancia_admin/{id}', [GeneradorConstanciaController::class, 'datos']); //->middleware('auth:sanctum');
Route::delete('/eliminar_constancia/{id}', [GeneradorConstanciaController::class, 'destroy']); //->middleware('auth:sanctum');

Route::post('/enviar_token', [CorreoController::class, 'sendToken']);

Route::get('/hash', [ResponsableConstanciaController::class, 'hashing']);
Route::post('/verify_token', [ResponsableConstanciaController::class, 'verifyToken']);
Route::post('/verify_code', [ResponsableConstanciaController::class, 'verifyCode']);
Route::post('/firma_responsable', [ResponsableConstanciaController::class, 'firmaResponsable']);
Route::post('/email_constancia_responsable', [CorreoController::class, 'constanciaResponsable']);

Route::get('/constancia-pdf/{id}', [PDFController::class, 'generatePDF']);
Route::get('/test-pdf', [PDFController::class, 'testPDF']);

Route::get('/prueba', [CorreoController::class, 'test']);
Route::get('/attach', [CorreoController::class, 'attach']);
Route::post('/email_constancia', [CorreoController::class, 'constancia']);

Route::get('/listar_asignaciones', [SedeUserController::class, 'index']); //->middleware('auth:sanctum');
Route::get('/listar_asignar_tecnico/{id}', [SedeUserController::class, 'listar_tecnicos_sede']); //->middleware('auth:sanctum');
Route::get('/listar_desasignar_tecnico/{id}', [SedeUserController::class, 'listar_tecnicos_no_sede']); //->middleware('auth:sanctum');
Route::post('/asignar_tecnico', [SedeUserController::class, 'store']); //->middleware('auth:sanctum');
Route::put('/editar_asignar_tecnico ', [SedeUserController::class, 'update']); //->middleware('auth:sanctum');
Route::delete('/eliminar_asignar_tecnico', [SedeUserController::class, 'destroy']); //->middleware('auth:sanctum');

Route::get('/todos_informes_asignados', [InformeController::class, 'index'])->middleware('auth:sanctum');
Route::get('/listar_informes/{id}', [InformeController::class, 'informes'])->middleware('auth:sanctum');
Route::post('/registrar_informe', [InformeController::class, 'store']); //->middleware('auth:sanctum');
Route::put('/editar_informe/{id} ', [InformeController::class, 'update']); //->middleware('auth:sanctum');
Route::get('/obtener_datos_informe/{id}', [InformeController::class, 'datos']); //->middleware('auth:sanctum');
Route::delete('/eliminar_informe/{id}', [InformeController::class, 'destroy']); //->middleware('auth:sanctum');

Route::get('/listar_items', [ItemController::class, 'index']); //->middleware('auth:sanctum');
Route::post('/registrar_item', [ItemController::class, 'store']); //->middleware('auth:sanctum');
Route::put('/editar_item/{id} ', [ItemController::class, 'update']); //->middleware('auth:sanctum');
Route::get('/obtener_datos_item/{id}', [ItemController::class, 'datos']); //->middleware('auth:sanctum');
Route::delete('/eliminar_item/{id}', [ItemController::class, 'destroy']); //->middleware('auth:sanctum');

Route::get('listar-checklist-asignacion', [ChecklistAsignController::class, 'index'])->middleware('auth:sanctum');
Route::get('/listar_checklist/{id}', [ChecklistAsignController::class, 'asignaciones'])->middleware('auth:sanctum');
Route::post('/crear_checklist', [ChecklistAsignController::class, 'store']); //->middleware('auth:sanctum');
Route::put('/editar_checklist_asignacion/{id} ', [ChecklistAsignController::class, 'update']); //->middleware('auth:sanctum');
Route::get('/obtener_datos_checklist/{id}', [ChecklistAsignController::class, 'datos']); //->middleware('auth:sanctum');
Route::delete('/eliminar_checklist_asignacion/{id}', [ChecklistAsignController::class, 'destroy']); //->middleware('auth:sanctum');

Route::get('listar-capacitaciones', [CapacitacionPresencialController::class, 'index'])->middleware('auth:sanctum');
Route::get('/listar_capacitaciones/{id}', [CapacitacionPresencialController::class, 'capacitaciones'])->middleware('auth:sanctum');
Route::post('/crear_capacitacion_presencial', [CapacitacionPresencialController::class, 'store'])->middleware('auth:sanctum');
Route::put('/editar_checklist_capacitacion_presencial/{id} ', [CapacitacionPresencialController::class, 'update']); //->middleware('auth:sanctum');
Route::get('/obtener_datos_capacitacion_presencial/{id}', [CapacitacionPresencialController::class, 'datos'])->middleware('auth:sanctum');
Route::delete('/eliminar_capacitacion_presencial/{id}', [CapacitacionPresencialController::class, 'destroy']); //->middleware('auth:sanctum');

Route::get('actividades', [RegistroController::class, 'index'])->middleware('auth:sanctum');

// Route::get('listar-checklist-ot', [ChecklistOtController::class, 'index']); //->middleware('auth:sanctum');
// Route::post('checklist-ot/crear', [ChecklistOtController::class, 'store']); //->middleware('auth:sanctum');
// Route::put('checklist-ot/editar/{id} ', [ChecklistOtController::class, 'update']); //->middleware('auth:sanctum');
// Route::delete('checklist-ot/eliminar/{id}', [ChecklistOtController::class, 'destroy']); //->middleware('auth:sanctum');
