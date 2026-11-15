<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\VisitCertificate;
use App\Models\VisitTraining;
use App\Services\TrainingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TrainingController extends Controller
{
    public function __construct(private readonly TrainingService $trainingService) {}

    /**
     * Display a listing of training templates and history.
     */
    public function index(Request $request)
    {
        return Inertia::render(
            'Gestor/Trainings/Index',
            $this->trainingService->getIndexData(Auth::user(), $request)
        );
    }

    /**
     * Show the form for creating a new training template.
     */
    public function create()
    {
        return Inertia::render(
            'Gestor/Trainings/Builder',
            $this->trainingService->getCreateData(Auth::user())
        );
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

        $this->trainingService->createTraining(
            Auth::user(),
            $validated,
            (bool) $request->input('is_template', false)
        );

        return redirect()->route('gestor.trainings.index')
            ->with('message', 'Capacitación inyectada correctamente.');
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

        $this->trainingService->startInVisit($visit, $request->only([
            'training_id', 'title', 'description', 'duration', 'notes',
        ]));

        return back()->with('message', 'Capacitación iniciada en la visita.');
    }

    /**
     * Add attendee and signature to a training session.
     */
    public function addAttendee(Request $request, VisitTraining $visitTraining)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'dni' => 'nullable|string|max:255',
            'signature' => 'required|string',
        ]);

        $this->trainingService->addAttendee($visitTraining, $validated);

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
        $this->trainingService->removeAttendee($visitTraining, (int) $index);

        return back()->with('message', 'Asistente eliminado.');
    }

    /**
     * Update training session details (description and notes/observations).
     */
    public function updateDetails(Request $request, VisitTraining $visitTraining)
    {
        $validated = $request->validate([
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $visitTraining->update($validated);

        return back()->with('message', 'Detalles de la capacitación actualizados.');
    }

    /**
     * Finalize the training session.
     */
    public function finalize(Request $request, VisitTraining $visitTraining)
    {
        if (empty($visitTraining->attendees) || count($visitTraining->attendees) === 0) {
            return back()->with('error', 'Debe haber al menos un participante con firma registrado para poder finalizar la capacitación.');
        }

        $validated = $request->validate([
            'duration_minutes' => 'required|integer|min:1',
            'is_external_instructor' => 'boolean',
            'trainer_name' => 'required_if:is_external_instructor,true|nullable|string|max:255',
            'instructor_signature' => 'required_if:is_external_instructor,true|nullable|string',
            'responsible_name' => 'required|string|max:255',
            'responsible_signature' => 'required|string',
        ]);

        $closedTrainingVisit = $this->trainingService->finalizeSession($visitTraining, $validated);

        if ($closedTrainingVisit) {
            return redirect()->route('tecnico.checklists.tasks.index', ['tab' => 'trainings'])
                ->with('message', 'Capacitación finalizada y visita cerrada correctamente con firma del responsable.');
        }

        return redirect()->route('tecnico.visits.in-progress', $visitTraining->visit_certificate_id)
            ->with('message', 'Capacitación finalizada y registrada correctamente.');
    }

    /**
     * Edit a training template.
     */
    public function edit(Training $training)
    {
        return Inertia::render(
            'Gestor/Trainings/Builder',
            $this->trainingService->getEditData(Auth::user(), $training)
        );
    }

    /**
     * View attendance details of a completed training session.
     */
    public function attendance($id)
    {
        return Inertia::render('Gestor/Trainings/Attendance', [
            'session' => $this->trainingService->getAttendanceSession($id),
        ]);
    }
}
