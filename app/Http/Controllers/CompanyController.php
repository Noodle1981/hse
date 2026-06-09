<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Models\Provincia;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CompanyController extends Controller
{
    /**
     * Show the company index.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        $query = Empresa::query();

        if (! $currentUser->isSuperAdmin()) {
            $query->where('consultora_id', $currentUser->consultora_id);
        }

        if ($request->boolean('showDeleted')) {
            $query->onlyTrashed();
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', "%{$request->search}%")
                    ->orWhere('razon_social', 'like', "%{$request->search}%")
                    ->orWhere('cuit', 'like', "%{$request->search}%");
            });
        }

        $companies = $query->withCount('sedes')
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Gestor/Companies/Index', [
            'companies' => $companies,
            'filters' => $request->only(['search', 'showDeleted']),
        ]);
    }

    /**
     * Show the company builder (create/edit).
     */
    public function create()
    {
        return Inertia::render('Gestor/Companies/Builder', [
            'empresa' => null,
        ]);
    }

    public function edit($id)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        $query = Empresa::query();

        if (! $currentUser->isSuperAdmin()) {
            $query->where('consultora_id', $currentUser->consultora_id);
        }

        $empresa = $query->findOrFail($id);

        return Inertia::render('Gestor/Companies/Builder', [
            'empresa' => $empresa,
        ]);
    }

    /**
     * Store or update company.
     */
    public function save(Request $request)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        $consultoraId = $currentUser->consultora_id;
        $id = $request->input('id');

        $request->validate([
            'nombre' => [
                'required', 'string', 'max:255',
                Rule::unique('empresas', 'nombre')->where('consultora_id', $consultoraId)->ignore($id),
            ],
            'razon_social' => 'required|string|max:255',
            'cuit' => [
                'required', 'string', 'max:13',
                Rule::unique('empresas', 'cuit')->where('consultora_id', $consultoraId)->ignore($id),
            ],
            'direccion' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'primary_color' => 'nullable|string|max:7',
            'logo' => 'nullable|image|max:2048',
        ]);

        $data = $request->only(['nombre', 'razon_social', 'cuit', 'direccion', 'email', 'primary_color']);

        if (! $currentUser->isSuperAdmin()) {
            $data['consultora_id'] = $consultoraId;
        }

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('logos', 'public');
        }

        if ($id) {
            $empresa = Empresa::findOrFail($id);
            $this->ensureTenancy($empresa);
            $empresa->update($data);
            $message = 'Empresa actualizada exitosamente.';
        } else {
            Empresa::create($data);
            $message = 'Empresa creada exitosamente.';
        }

        return redirect()->route('gestor.companies.index')->with('message', $message);
    }

    /**
     * Delete company (soft delete).
     */
    public function destroy(Empresa $company)
    {
        $this->ensureTenancy($company);
        $company->delete();

        return back()->with('message', 'Empresa dada de baja exitosamente.');
    }

    /**
     * Restore company.
     */
    public function restore($id)
    {
        $company = Empresa::withTrashed()->findOrFail($id);
        $this->ensureTenancy($company);
        $company->restore();

        return back()->with('message', 'Empresa restaurada exitosamente.');
    }

    /**
     * Location Manager (Sedes).
     */
    public function locations(Request $request, Empresa $empresa)
    {
        $this->ensureTenancy($empresa);

        $query = $empresa->sedes()->with(['provincia', 'users']);

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre_sede', 'like', "%{$request->search}%")
                    ->orWhere('localidad', 'like', "%{$request->search}%")
                    ->orWhere('direccion', 'like', "%{$request->search}%");
            });
        }

        if ($request->boolean('showDeleted')) {
            $query->onlyTrashed();
        }

        $locations = $query->orderBy('nombre_sede')->get();
        $provincias = Provincia::orderBy('nombre')->get();
        $availableUsers = User::where('rol', 'tecnico')->orderBy('apellidos')->get();

        return Inertia::render('Gestor/Companies/Locations', [
            'empresa' => $empresa,
            'locations' => $locations,
            'provincias' => $provincias,
            'availableUsers' => $availableUsers,
            'filters' => $request->only(['search', 'showDeleted']),
        ]);
    }

    /**
     * Save Location.
     */
    public function saveLocation(Request $request, Empresa $empresa)
    {
        $this->ensureTenancy($empresa);

        $id = $request->input('id');
        $request->validate([
            'nombre_sede' => [
                'required', 'string', 'max:255',
                Rule::unique('sedes', 'nombre_sede')->where('empresa_id', $empresa->id)->ignore($id),
            ],
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

        if ($id) {
            $sede = Sede::findOrFail($id);
            $sede->update($data);
            $message = 'Sede actualizada.';
        } else {
            $empresa->sedes()->create($data);
            $message = 'Sede creada.';
        }

        return back()->with('message', $message);
    }

    /**
     * Delete Location.
     */
    public function deleteLocation(Sede $sede)
    {
        $this->ensureTenancy($sede->empresa);
        $sede->delete();

        return back()->with('message', 'Sede dada de baja.');
    }

    /**
     * Restore Location.
     */
    public function restoreLocation($id)
    {
        $sede = Sede::withTrashed()->findOrFail($id);
        $this->ensureTenancy($sede->empresa);
        $sede->restore();

        return back()->with('message', 'Sede restaurada.');
    }

    /**
     * Toggle User Assignment to Site.
     */
    public function toggleUserAssignment(Request $request, Sede $sede)
    {
        $this->ensureTenancy($sede->empresa);
        $userId = $request->input('user_id');

        if ($sede->users()->where('users.id', $userId)->exists()) {
            $sede->users()->detach($userId);
            $message = 'Técnico desvinculado.';
        } else {
            $sede->users()->attach($userId);
            $message = 'Técnico vinculado.';
        }

        return back()->with('message', $message);
    }

    private function ensureTenancy(Empresa $empresa)
    {
        /** @var \App\Models\User $currentUser */
        $currentUser = Auth::user();
        if (! $currentUser->isSuperAdmin() && $empresa->consultora_id !== $currentUser->consultora_id) {
            abort(403, 'Acceso no autorizado.');
        }
    }
}
