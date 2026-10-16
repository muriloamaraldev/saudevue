<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class MedicalRecordController extends Controller
{
    /**
     * Prontuários do tenant autenticado, mais recentes primeiro.
     */
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', MedicalRecord::class);

        $pacientes = $request->user()
            ->patients()
            ->orderBy('name')
            ->get(['id', 'name', 'cpf']);

        $prontuarios = MedicalRecord::query()
            ->ownedBy($request->user())
            ->with('patient:id,name')
            ->orderByDesc('occurred_at')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('MedicalRecords/Index', [
            'prontuarios' => $prontuarios,
            'pacientes' => $pacientes,
        ]);
    }
}
