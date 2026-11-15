<?php

namespace App\Services;

use App\Models\Category;
use App\Models\ChecklistSection;
use App\Models\DynamicChecklist;
use App\Models\DynamicQuestion;
use App\Models\Subcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ChecklistBuilderService
{
    public function getIndexData(Request $request): array
    {
        $checklists = DynamicChecklist::query()
            ->with(['categoryRelation', 'subcategoryRelation'])
            ->when($request->search, fn ($q) => $q->where('title', 'like', '%'.$request->search.'%'))
            ->when($request->categoryFilter, fn ($q) => $q->where('category_id', $request->categoryFilter))
            ->when($request->statusFilter === 'active', fn ($q) => $q->where('is_active', true))
            ->when($request->statusFilter === 'inactive', fn ($q) => $q->where('is_active', false))
            ->withCount('questions')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return [
            'checklists' => $checklists,
            'categories' => Category::active()->orderBy('order')->get(),
            'filters' => $request->only(['search', 'categoryFilter', 'statusFilter']),
        ];
    }

    public function duplicate(DynamicChecklist $original): DynamicChecklist
    {
        return DB::transaction(function () use ($original) {
            $original->load(['questions', 'sections']);

            $copy = $original->replicate();
            $copy->title = '📝 [BORRADOR] '.$original->title.' (Copia)';
            $copy->is_draft = true;
            $copy->is_active = false;
            $copy->save();

            $sectionMapping = [];
            foreach ($original->sections as $section) {
                $newSection = $section->replicate();
                $newSection->dynamic_checklist_id = $copy->id;
                $newSection->save();
                $sectionMapping[$section->id] = $newSection->id;
            }

            foreach ($original->questions as $question) {
                $newQuestion = $question->replicate();
                $newQuestion->dynamic_checklist_id = $copy->id;
                if ($question->section_id && isset($sectionMapping[$question->section_id])) {
                    $newQuestion->section_id = $sectionMapping[$question->section_id];
                }
                $newQuestion->save();
            }

            return $copy;
        });
    }

    public function getActiveCategories()
    {
        return Category::active()
            ->with(['subcategories' => fn ($q) => $q->where('is_active', true)->orderBy('order')])
            ->orderBy('order')
            ->get();
    }

    public function getEditData(int|string $id): array
    {
        $checklist = DynamicChecklist::with(['questions', 'sections'])->findOrFail($id);
        $categories = $this->getActiveCategories();

        if ($checklist->category_id) {
            $currentCategory = Category::find($checklist->category_id);
            if ($currentCategory && ! $categories->contains('id', $currentCategory->id)) {
                $categories->push($currentCategory);
            }
        }

        if ($checklist->subcategory_id) {
            $currentSubcategory = Subcategory::find($checklist->subcategory_id);
            if ($currentSubcategory) {
                $parentCat = $categories->firstWhere('id', $checklist->category_id);
                if ($parentCat) {
                    if (! $parentCat->relationLoaded('subcategories')) {
                        $parentCat->setRelation('subcategories', collect([$currentSubcategory]));
                    } elseif (! $parentCat->subcategories->contains('id', $currentSubcategory->id)) {
                        $parentCat->subcategories->push($currentSubcategory);
                    }
                }
            }
        }

        return [
            'checklist' => $checklist,
            'categories' => $categories,
        ];
    }

    public function saveChecklist(Request $request): DynamicChecklist
    {
        $id = $request->input('id');
        $sectionsInput = $request->input('sections', []);
        $questionsInput = $request->input('questions', []);

        if (empty($sectionsInput)) {
            $sectionsInput = [[
                'id' => null,
                'tempId' => 'default_section_temp',
                'title' => 'Inspección General',
                'description' => 'Preguntas generales del checklist.',
            ]];

            foreach ($questionsInput as &$q) {
                if (empty($q['section_id']) && empty($q['section_tempId'])) {
                    $q['section_tempId'] = 'default_section_temp';
                }
            }
            unset($q);
        }

        $sectionTempIds = collect($sectionsInput)->pluck('tempId')->filter()->toArray();
        $sectionIds = collect($sectionsInput)->pluck('id')->filter()->toArray();

        foreach ($questionsInput as $index => $q) {
            $hasValidSection = (! empty($q['section_tempId']) && in_array($q['section_tempId'], $sectionTempIds))
                || (! empty($q['section_id']) && (in_array($q['section_id'], $sectionIds) || ChecklistSection::where('id', $q['section_id'])->exists()));

            if (! $hasValidSection) {
                throw ValidationException::withMessages([
                    'questions' => "La pregunta '".($q['label'] ?? 'N# '.($index + 1))."' debe pertenecer a una sección válida del checklist.",
                ]);
            }
        }

        return DB::transaction(function () use ($request, $id, $sectionsInput, $questionsInput) {
            $data = $request->only(['title', 'description', 'category_id', 'subcategory_id', 'registry_code', 'is_active']);
            $data['is_draft'] = false;

            if ($id) {
                $checklist = DynamicChecklist::findOrFail($id);
                $checklist->update($data);
                $checklist->questions()->delete();
                $checklist->sections()->delete();
            } else {
                $checklist = DynamicChecklist::create($data);
            }

            $sectionMapping = [];
            foreach ($sectionsInput as $index => $s) {
                $newSection = ChecklistSection::create([
                    'dynamic_checklist_id' => $checklist->id,
                    'title' => $s['title'],
                    'description' => $s['description'] ?? null,
                    'order' => $index,
                ]);
                $sectionMapping[$s['tempId'] ?? $s['id']] = $newSection->id;
            }

            foreach ($questionsInput as $index => $q) {
                $sectionId = null;
                if (isset($q['section_tempId'], $sectionMapping[$q['section_tempId']])) {
                    $sectionId = $sectionMapping[$q['section_tempId']];
                } elseif (isset($q['section_id'], $sectionMapping[$q['section_id']])) {
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

            return $checklist;
        });
    }
}
