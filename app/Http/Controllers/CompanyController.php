<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Sede;
use App\Models\User;
use App\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function __construct(protected CompanyService $companyService) {}

    public function index(Request $request)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        return Inertia::render('Gestor/Companies/Index', $this->companyService->getIndexData($currentUser, $request));
    }

    public function create()
    {
        return Inertia::render('Gestor/Companies/Builder', ['empresa' => null]);
    }

    public function edit($id)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        return Inertia::render('Gestor/Companies/Builder', [
            'empresa' => $this->companyService->findForEdit($currentUser, $id),
        ]);
    }

    public function save(Request $request)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        $id = $request->input('id') ? (int) $request->input('id') : null;
        $consultoraId = $this->companyService->resolveConsultoraId(
            $currentUser,
            $id,
            $request->input('consultora_id') ? (int) $request->input('consultora_id') : null
        );

        $request->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('empresas', 'nombre')->where('consultora_id', $consultoraId)->ignore($id)],
            'razon_social' => 'required|string|max:255',
            'cuit' => ['required', 'string', 'max:13', Rule::unique('empresas', 'cuit')->where('consultora_id', $consultoraId)->ignore($id)],
            'direccion' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'primary_color' => 'nullable|string|max:7',
            'logo' => 'nullable|image|max:2048',
        ]);

        if (! $id && $limitError = $this->companyService->checkLimitForCreate($consultoraId)) {
            return back()->withErrors(['nombre' => $limitError]);
        }

        $data = $request->only(['nombre', 'razon_social', 'cuit', 'direccion', 'email', 'primary_color']);
        $message = $this->companyService->saveCompany($currentUser, $data, $id, $consultoraId, $request->file('logo'));

        return redirect()->route('gestor.companies.index')->with('message', $message);
    }

    public function destroy(Empresa $company)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        $this->companyService->ensureTenancy($currentUser, $company);
        $company->delete();

        return back()->with('message', 'Empresa dada de baja exitosamente.');
    }

    public function restore($id)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        $result = $this->companyService->restoreCompany($currentUser, $id);

        return back()->with($result['ok'] ? 'message' : 'error', $result['message']);
    }

    public function locations(Request $request, Empresa $empresa)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        return Inertia::render('Gestor/Companies/Locations', $this->companyService->getLocationsData($currentUser, $empresa, $request));
    }

    public function saveLocation(Request $request, Empresa $empresa)
    {
        $id = $request->input('id') ? (int) $request->input('id') : null;
        $request->validate([
            'nombre_sede' => ['required', 'string', 'max:255', Rule::unique('sedes', 'nombre_sede')->where('empresa_id', $empresa->id)->ignore($id)],
            'direccion' => 'nullable|string|max:255',
            'localidad' => 'nullable|string|max:255',
            'provincia_id' => 'nullable|exists:provincias,id',
            'telefono' => 'nullable|string|max:50',
            'correo' => 'nullable|email|max:255',
            'tipo_sede' => 'nullable|string|max:100',
            'rubro' => 'nullable|string|max:100',
            'dias_operativos' => 'nullable|array',
            'hora_apertura' => 'nullable|string|max:10',
            'hora_cierre' => 'nullable|string|max:10',
            'es_roster_24_7' => 'boolean',
            'radio_tolerancia_metros' => 'integer|min:0',
            'latitud' => 'nullable|numeric|between:-90,90',
            'longitud' => 'nullable|numeric|between:-180,180',
        ]);

        $data = $request->only([
            'nombre_sede', 'direccion', 'localidad', 'provincia_id',
            'telefono', 'correo', 'tipo_sede', 'rubro',
            'responsable_1_nombre', 'responsable_1_cargo', 'responsable_1_email',
            'responsable_2_nombre', 'responsable_2_cargo', 'responsable_2_email',
            'responsable_3_nombre', 'responsable_3_cargo', 'responsable_3_email',
            'dias_operativos', 'hora_apertura', 'hora_cierre', 'es_roster_24_7', 'radio_tolerancia_metros',
            'latitud', 'longitud',
        ]);

        /** @var User $currentUser */
        $currentUser = Auth::user();
        $message = $this->companyService->saveLocation($currentUser, $empresa, $data, $id);

        return back()->with('message', $message);
    }

    public function deleteLocation(Sede $sede)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        $this->companyService->ensureTenancy($currentUser, $sede->empresa);
        $sede->delete();

        return back()->with('message', 'Sede dada de baja.');
    }

    public function restoreLocation($id)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        $this->companyService->restoreLocation($currentUser, $id);

        return back()->with('message', 'Sede restaurada.');
    }

    public function toggleUserAssignment(Request $request, Sede $sede)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();
        $message = $this->companyService->toggleUserAssignment($currentUser, $sede, (int) $request->input('user_id'));

        return back()->with('message', $message);
    }
}
