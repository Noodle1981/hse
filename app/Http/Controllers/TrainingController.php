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

        $history = VisitTraining::with(['visit.sede', 'training'])
            ->latest()
            ->paginate(15);

        return Inertia::render('Gestor/Trainings/Index', [
            'templates' => $templates,
            'history' => $history,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new training template.
     */
    public function create()
    {
        return Inertia::render('Gestor/Trainings/Builder', [
            'sedes' => Sede::with('empresa')->get(),
        ]);
    }

    /**
     * Store a new training template.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'nullable|integer',
            'type' => 'required|string',
        ]);

        Training::create([
            ...$request->all(),
            'is_template' => true,
            'created_by_user_id' => Auth::id(),
            'status' => 'active',
        ]);

        return redirect()->route('gestor.trainings.index')
            ->with('message', 'Plantilla de capacitación creada exitosamente.');
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

        $visitTraining = VisitTraining::create([
            'visit_certificate_id' => $visit->id,
            'training_id' => $request->training_id,
            'is_adhoc' => !$request->training_id,
            'title' => $training ? $training->title : $request->title,
            'description' => $training ? $training->description : $request->description,
            'duration_minutes' => $training ? $training->duration : ($request->duration ?: 15),
            'attendees' => [],
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
            'signature' => 'required|string',
        ]);

        $attendees = $visitTraining->attendees ?? [];
        $attendees[] = [
            'name' => $request->name,
            'position' => $request->position,
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
