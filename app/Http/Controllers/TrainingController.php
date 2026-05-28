<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\VisitTraining;
use App\Models\VisitCertificate;
use App\Models\TrainingAttendance;
use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TrainingController extends Controller
{
    /**
     * Display a listing of training templates and history.
     */
    public function index(Request $request)
    {
        $templates = Training::where('is_template', true)
            ->with(['empresa', 'creator'])
            ->latest()
            ->get();

        $history = VisitTraining::query()
            ->with(['visit.sede.empresa', 'visit.user', 'training.responsibleUser', 'training.creator'])
            ->when($request->search, function ($query) use ($request) {
                $query->where('title', 'like', '%' . $request->search . '%')
                      ->orWhere('description', 'like', '%' . $request->search . '%');
            })
            ->when($request->empresaFilter, function ($query) use ($request) {
                $query->whereHas('visit.sede', function($q) use ($request) {
                    $q->where('empresa_id', $request->empresaFilter);
                });
            })
            ->when($request->sedeFilter, function ($query) use ($request) {
                $query->whereHas('visit', function($q) use ($request) {
                    $q->where('sede_id', $request->sedeFilter);
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Gestor/Trainings/Index', [
            'templates' => $templates,
            'history' => $history,
            'empresas' => \App\Models\Empresa::get(),
            'sedes' => $request->empresaFilter ? Sede::where('empresa_id', $request->empresaFilter)->get() : [],
            'filters' => $request->only(['search', 'empresaFilter', 'sedeFilter']),
        ]);
    }

    /**
     * Show the form for creating a new training template.
     */
    public function create()
    {
        return Inertia::render('Gestor/Trainings/Builder', [
            'sedes' => Sede::with('empresa')->get(),
            'empresas' => \App\Models\Empresa::get(),
            'tecnicos' => \App\Models\User::where('rol', 'tecnico')->get(),
            'checklists' => \App\Models\DynamicChecklist::active()->get(),
        ]);
    }

    /**
     * Store a new training template.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer',
            'type' => 'required|string',
            'empresa_id' => 'required|exists:empresas,id',
            'sede_id' => 'required|exists:sedes,id',
            'user_id' => 'required|exists:users,id',
            'trainer_name' => 'nullable|string|max:255',
            'date' => 'required|date',
            'status' => 'required|string',
            'dynamic_checklist_id' => 'nullable|exists:dynamic_checklists,id',
            'is_template' => 'boolean',
        ]);

        $training = Training::create(array_merge($validated, [
            'created_by_user_id' => Auth::id(),
            'is_template' => $request->input('is_template', false),
        ]));

        return redirect()->route('gestor.trainings.index')->with('message', 'Capacitación inyectada correctamente.');
    }

    /**
     * Start a training session within a visit.
     */
    public function startInVisit(Request $request, VisitCertificate $visit)
    {
        $request->validate([
            'training_id' => 'nullable|exists:trainings,id',
            'title' => 'required_without:training_id|string|max:255',
        ]);

        $training = null;
        if ($request->training_id) {
            $training = Training::find($request->training_id);
        }

        // Buscar si existe un plan anual programado para esta sede y checklist
        $durationMinutes = 15;
        $modality = 'presencial';
        $multiTrainer = false;

        if ($training && $training->dynamic_checklist_id) {
            $plan = \App\Models\AnnualPlan::where('sede_id', $visit->sede_id)
                ->where('checklist_id', $training->dynamic_checklist_id)
                ->first();

            if ($plan) {
                $durationMinutes = $plan->duration_minutes ?: 15;
                $modality = $plan->modality ?: 'presencial';
                $multiTrainer = (bool)$plan->multi_trainer;
            }
        }

        $visitTraining = VisitTraining::create([
            'visit_certificate_id' => $visit->id,
            'training_id' => $request->training_id,
            'is_adhoc' => !$request->training_id,
            'title' => $training ? $training->title : $request->title,
            'description' => $training ? $training->description : $request->description,
            'duration_minutes' => $training ? ($training->duration ?: $durationMinutes) : ($request->duration ?: $durationMinutes),
            'attendees' => [],
            // Almacenar información heredada del plan para auditoría interna
            'notes' => $training ? null : $request->notes,
        ]);

        return back()->with('message', 'Capacitación iniciada en la visita.');
    }

    /**
     * Add attendee and signature to a training session.
     */
    public function addAttendee(Request $request, VisitTraining $visitTraining)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'dni' => 'nullable|string|max:255',
            'signature' => 'required|string',
        ]);

        $attendees = $visitTraining->attendees ?? [];
        $attendees[] = [
            'name' => $request->name,
            'position' => $request->position,
            'dni' => $request->dni,
            'signature' => $request->signature,
            'timestamp' => now()->toDateTimeString(),
        ];

        $visitTraining->update(['attendees' => $attendees]);

        return back()->with('message', 'Asistente registrado.');
    }

    /**
     * Display the live session for adding attendees and signatures.
     */
    public function session(VisitTraining $visitTraining)
    {
        $visitTraining->load('visit.sede.empresa');
        
        return Inertia::render('Tecnico/Trainings/Session', [
            'session' => $visitTraining,
        ]);
    }

    /**
     * Delete an attendee from a training session.
     */
    public function removeAttendee(Request $request, VisitTraining $visitTraining, $index)
    {
        $attendees = $visitTraining->attendees ?? [];
        if (isset($attendees[$index])) {
            unset($attendees[$index]);
            $visitTraining->update(['attendees' => array_values($attendees)]);
        }

        return back()->with('message', 'Asistente eliminado.');
    }

    /**
     * Update training session details (description and notes/observations).
     */
    public function updateDetails(Request $request, VisitTraining $visitTraining)
    {
        $request->validate([
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $visitTraining->update([
            'description' => $request->description,
            'notes' => $request->notes,
        ]);

        return back()->with('message', 'Detalles de la capacitación actualizados.');
    }

    /**
     * Finalize the training session.
     */
    public function finalize(Request $request, VisitTraining $visitTraining)
    {
        $request->validate([
            'duration_minutes' => 'required|integer|min:1',
            'is_external_instructor' => 'boolean',
            'trainer_name' => 'required_if:is_external_instructor,true|nullable|string|max:255',
            'instructor_signature' => 'required_if:is_external_instructor,true|nullable|string',
        ]);

        $visitTraining->update([
            'duration_minutes' => $request->duration_minutes,
        ]);

        $signaturePath = null;
        if ($request->is_external_instructor && $request->instructor_signature) {
            $sigData = $request->instructor_signature;
            if (preg_match('/^data:image\/(\w+);base64,/', $sigData, $type)) {
                $sigData = substr($sigData, strpos($sigData, ',') + 1);
                $type = strtolower($type[1]);
            } else {
                $type = 'png';
            }
            $sigData = base64_decode($sigData);

            if ($sigData !== false) {
                $filename = 'signatures/instructor_' . uniqid() . '.' . $type;
                \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $sigData);
                $signaturePath = $filename;
            }
        }

        if ($visitTraining->training) {
            $updateData = [
                'status' => 'completed',
                'duration' => $request->duration_minutes,
            ];
            if ($request->is_external_instructor && $request->trainer_name) {
                $updateData['trainer_name'] = $request->trainer_name;
            }
            $visitTraining->training->update($updateData);
        }

        $visit = $visitTraining->visit;
        if ($visit) {
            if ($request->is_external_instructor && $signaturePath) {
                $visit->instructor_signature = $signaturePath;
                $visit->instructor_signature_date = now();
            } else if (!$request->is_external_instructor) {
                $visit->instructor_signature = null;
                $visit->instructor_signature_date = null;
            }
            $visit->save();
        }

        if ($visit && $visit->visit_type === 'training') {
            $visit->update([
                'end_time' => now(),
                'status' => 'completed',
                'general_observations' => 'Capacitación dictada con éxito. Total asistentes: ' . count($visitTraining->attendees ?? []),
                'notes' => 'Visita de capacitación finalizada automáticamente al completar la charla: ' . $visitTraining->title,
                'responsible_name' => $request->input('responsible_name', 'Responsable de Sede'),
            ]);

            return redirect()->route('tecnico.checklists.tasks.index', ['tab' => 'trainings'])
                ->with('message', 'Capacitación finalizada y visita cerrada correctamente.');
        }

        return redirect()->route('tecnico.visits.in-progress', $visitTraining->visit_certificate_id)
            ->with('message', 'Capacitación finalizada y registrada correctamente.');
    }

    /**
     * Edit a training template.
     */
    public function edit(Training $training)
    {
        return Inertia::render('Gestor/Trainings/Builder', [
            'training' => $training,
            'sedes' => Sede::with('empresa')->get(),
        ]);
    }

    /**
     * View attendance details of a completed training session.
     */
    public function attendance($id)
    {
        $session = VisitTraining::with(['visit.sede.empresa', 'training'])->findOrFail($id);
        
        return Inertia::render('Gestor/Trainings/Attendance', [
            'session' => $session,
        ]);
    }
}
