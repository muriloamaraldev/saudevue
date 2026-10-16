<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class PatientController extends Controller
{
    /**
     * Listar os pacientes do tenant autenticado.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        Gate::authorize('viewAny', Patient::class);

        $filtros = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $busca = trim($filtros['search'] ?? '');

        $pacientes = Patient::query()
            ->ownedBy($user)
            ->when($busca !== '', function ($query) use ($busca) {
                $termo = '%'.$busca.'%';

                $query->where(function ($sub) use ($termo,$busca) {
                    $sub->where('name', 'like', $termo)
                        ->orWhere('cpf', 'like', preg_replace('/\D/', '', $busca) !== ''
                            ? '%'.preg_replace('/\D/', '', $busca).'%'
                            : $termo);
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Patients/Index', [
            'pacientes' => $pacientes,
            'filtros' => ['search' => $busca],
        ]);
    }

    /**
     * Formulário de cadastro.
     */
    public function create(): Response
    {
        Gate::authorize('create', Patient::class);

        return Inertia::render('Patients/Create');
    }

    /**
     * Persistir um novo paciente no tenant autenticado.
     */
    public function store(StorePatientRequest $request): RedirectResponse
    {
        $paciente = $request->user()->patients()->create($request->validated());

        return Redirect::route('patients.show', $paciente)
            ->with('status', 'Paciente cadastrado.');
    }

    /**
     * Detalhe do paciente + prontuários.
     */
    public function show(Request $request, Patient $patient): Response
    {
        Gate::authorize('view', $patient);

        return Inertia::render('Patients/Show', [
            'paciente' => $patient->load('medicalRecords'),
        ]);
    }

    /**
     * Formulário de edição.
     */
    public function edit(Request $request, Patient $patient): Response
    {
        Gate::authorize('update', $patient);

        return Inertia::render('Patients/Edit', [
            'paciente' => $patient,
        ]);
    }

    /**
     * Atualizar um paciente do tenant autenticado.
     */
    public function update(UpdatePatientRequest $request, Patient $patient): RedirectResponse
    {
        $patient->update($request->validated());

        return Redirect::route('patients.show', $patient)
            ->with('status', 'Paciente atualizado.');
    }

    /**
     * Exclusão lógica — o prontuário é documento médico-legal.
     */
    public function destroy(Request $request, Patient $patient): RedirectResponse
    {
        Gate::authorize('delete', $patient);

        $patient->delete();

        return Redirect::route('patients.index')
            ->with('status', 'Paciente removido.');
    }
}
