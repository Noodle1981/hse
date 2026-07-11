<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ChecklistSection;
use App\Models\DynamicChecklist;
use App\Models\DynamicQuestion;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChecklistController extends Controller
{
    /**
     * Display a listing of checklists.
     */
    public function index(Request $request)
    {
        $checklists = DynamicChecklist::query()
            ->with(['categoryRelation', 'subcategoryRelation'])
            ->when($request->search, function ($query) use ($request) {
                $query->where('title', 'like', '%'.$request->search.'%');
            })
            ->when($request->categoryFilter, function ($query) use ($request) {
                $query->where('category_id', $request->categoryFilter);
            })
            ->when($request->statusFilter === 'active', function ($query) {
                $query->where('is_active', true);
            })
            ->when($request->statusFilter === 'inactive', function ($query) {
                $query->where('is_active', false);
            })
            ->withCount('questions')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Category::active()->orderBy('order')->get();

        return Inertia::render('Gestor/Checklists/Index', [
            'checklists' => $checklists,
            'categories' => $categories,
            'filters' => $request->only(['search', 'categoryFilter', 'statusFilter']),
        ]);
    }

    /**
     * Toggle active status.
     */
    public function toggleStatus(DynamicChecklist $checklist)
    {
        $checklist->update(['is_active' => ! $checklist->is_active]);
        $status = $checklist->is_active ? 'activado' : 'desactivado';

        return back()->with('message', "Checklist $status correctamente.");
    }

    /**
     * Duplicate checklist.
     */
    public function duplicate(DynamicChecklist $original)
    {
        $original->load(['questions', 'sections']);

        // Crear copia como borrador
        $copy = $original->replicate();
        $copy->title = '📝 [BORRADOR] '.$original->title.' (Copia)';
        $copy->is_draft = true;
        $copy->is_active = false;
        $copy->save();

        // Copiar todas las secciones
        $sectionMapping = [];
        foreach ($original->sections as $section) {
            $newSection = $section->replicate();
            $newSection->dynamic_checklist_id = $copy->id;
            $newSection->save();
            $sectionMapping[$section->id] = $newSection->id;
        }

        // Copiar todas las preguntas
        foreach ($original->questions as $question) {
            $newQuestion = $question->replicate();
            $newQuestion->dynamic_checklist_id = $copy->id;

            // Actualizar section_id si existe
            if ($question->section_id && isset($sectionMapping[$question->section_id])) {
                $newQuestion->section_id = $sectionMapping[$question->section_id];
            }

            $newQuestion->save();
        }

        return redirect()->route('gestor.checklists.edit', $copy->id)
            ->with('message', '¡Checklist duplicado! Edita el título y guarda para activarlo.');
    }

    /**
     * Show the checklist builder (create).
     */
    public function create()
    {
        $categories = Category::active()
            ->with(['subcategories' => function ($query) {
                $query->where('is_active', true)->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        return Inertia::render('Gestor/Checklists/Builder', [
            'checklist' => null,
            'categories' => $categories,
        ]);
    }

    /**
     * Show the checklist builder (edit).
     */
    public function edit($id)
    {
        $checklist = DynamicChecklist::with(['questions', 'sections'])->findOrFail($id);

        // Cargar categorías activas con sus subcategorías activas
        $categories = Category::active()
            ->with(['subcategories' => function ($query) {
                $query->where('is_active', true)->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        // Si el checklist tiene categoría y esta no está en el listado de activas, incluirla
        if ($checklist->category_id) {
            $currentCategory = Category::find($checklist->category_id);
            if ($currentCategory && ! $categories->contains('id', $currentCategory->id)) {
                $categories->push($currentCategory);
            }
        }

        // Asegurarnos de que si el checklist tiene una subcategoría, esta se cargue dentro de su categoría
        if ($checklist->subcategory_id) {
            $currentSubcategory = Subcategory::find($checklist->subcategory_id);
            if ($currentSubcategory) {
                // Buscar la categoría del checklist en la colección
                $parentCat = $categories->firstWhere('id', $checklist->category_id);
                if ($parentCat) {
                    // Si no tiene cargada la relación subcategories o no contiene la subcategoría actual, inyectarla
                    if (! $parentCat->relationLoaded('subcategories')) {
                        $parentCat->setRelation('subcategories', collect([$currentSubcategory]));
                    } elseif (! $parentCat->subcategories->contains('id', $currentSubcategory->id)) {
                        $parentCat->subcategories->push($currentSubcategory);
                    }
                }
            }
        }

        return Inertia::render('Gestor/Checklists/Builder', [
            'checklist' => $checklist,
            'categories' => $categories,
        ]);
    }

    /**
     * Save/Update checklist tree.
     */
    public function save(Request $request)
    {
        $id = $request->input('id');

        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
            'questions' => 'required|array|min:1',
        ]);

        // 1. Si no se proveen secciones, auto-generamos la sección base "Inspección General"
        $sectionsInput = $request->input('sections', []);
        if (empty($sectionsInput)) {
            $sectionsInput = [
                [
                    'id' => null,
                    'tempId' => 'default_section_temp',
                    'title' => 'Inspección General',
                    'description' => 'Preguntas generales del checklist.',
                ],
            ];

            $questionsInput = $request->input('questions', []);
            foreach ($questionsInput as &$q) {
                if (empty($q['section_id']) && empty($q['section_tempId'])) {
                    $q['section_tempId'] = 'default_section_temp';
                }
            }
            unset($q);
            $request->merge([
                'sections' => $sectionsInput,
                'questions' => $questionsInput,
            ]);
        }

        // 2. Hardening de Validación: Validar que cada pregunta esté asignada a una sección válida del request o base de datos
        $sections = $request->input('sections', []);
        $sectionTempIds = collect($sections)->pluck('tempId')->filter()->toArray();
        $sectionIds = collect($sections)->pluck('id')->filter()->toArray();

        foreach ($request->input('questions', []) as $index => $q) {
            $hasValidSection = false;

            if (! empty($q['section_tempId']) && in_array($q['section_tempId'], $sectionTempIds)) {
                $hasValidSection = true;
            }

            if (! empty($q['section_id']) && (in_array($q['section_id'], $sectionIds) || ChecklistSection::where('id', $q['section_id'])->exists())) {
                $hasValidSection = true;
            }

            if (! $hasValidSection) {
                return back()->withErrors([
                    'questions' => "La pregunta '".($q['label'] ?? 'N# '.($index + 1))."' debe pertenecer a una sección válida del checklist.",
                ])->withInput();
            }
        }

        // 3. Persistencia de Datos
        $data = $request->only(['title', 'description', 'category_id', 'subcategory_id', 'registry_code', 'is_active']);
        $data['is_draft'] = false;

        if ($id) {
            $checklist = DynamicChecklist::findOrFail($id);
            $checklist->update($data);
            // Limpiar para reconstruir
            $checklist->questions()->delete();
            $checklist->sections()->delete();
        } else {
            $checklist = DynamicChecklist::create($data);
        }

        // Guardar Secciones
        $sectionMapping = [];
        $sections = $request->input('sections', []);
        foreach ($sections as $index => $s) {
            $newSection = ChecklistSection::create([
                'dynamic_checklist_id' => $checklist->id,
                'title' => $s['title'],
                'description' => $s['description'] ?? null,
                'order' => $index,
            ]);
            $sectionMapping[$s['tempId'] ?? $s['id']] = $newSection->id;
        }

        // Guardar Preguntas
        $questions = $request->input('questions', []);
        foreach ($questions as $index => $q) {
            $sectionId = null;
            if (isset($q['section_tempId']) && isset($sectionMapping[$q['section_tempId']])) {
                $sectionId = $sectionMapping[$q['section_tempId']];
            } elseif (isset($q['section_id']) && isset($sectionMapping[$q['section_id']])) {
                $sectionId = $sectionMapping[$q['section_id']];
            }

            DynamicQuestion::create([
                'dynamic_checklist_id' => $checklist->id,
                'section_id' => $sectionId,
                'label' => $q['label'],
                'type' => $q['type'],
                'options' => $q['options'] ?? null,
                'min_value' => (isset($q['min_value']) && $q['min_value'] !== '') ? $q['min_value'] : null,
                'max_value' => (isset($q['max_value']) && $q['max_value'] !== '') ? $q['max_value'] : null,
                'failure_values' => $q['failure_values'] ?? null,
                'failure_logic' => $q['failure_logic'] ?? 'fail_if_selected',
                'help_text' => $q['help_text'] ?? null,
                'is_required' => $q['is_required'] ?? false,
                'vincular_a_metadata' => $q['vincular_a_metadata'] ?? null,
                'order' => $index,
            ]);
        }

        return redirect()->route('gestor.checklists.index')
            ->with('message', $id ? 'Checklist actualizado.' : 'Checklist creado correctamente.');
    }

    /**
     * Emular el llenado de un checklist.
     */
    public function emulate(DynamicChecklist $checklist)
    {
        $checklist->load(['sections.questions']);

        return Inertia::render('Tecnico/Inspections/Wizard', [
            'checklist' => $checklist,
            'isEmulation' => true,
        ]);
    }
}
