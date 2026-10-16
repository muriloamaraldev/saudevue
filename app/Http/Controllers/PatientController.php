<?php

namespace App\Http\Controllers;

use App\Contracts\PublishesEvents;
use App\Events\PatientRegistered;
use App\Events\PatientUpdated;
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
     *
     * A publicação do evento é a última etapa e nunca derruba a requisição:
     * o registro já está no banco quando ela acontece.
     */
    public function store(StorePatientRequest $request, PublishesEvents $publisher): RedirectResponse
    {
        $paciente = $request->user()->patients()->create($request->validated());

        $publisher->publish(
            PatientRegistered::ROUTING_KEY,
            PatientRegistered::fromPatient($paciente)->toPayload(),
        );

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
    public function update(UpdatePatientRequest $request, Patient $patient, PublishesEvents $publisher): RedirectResponse
    {
        $patient->update($request->validated());

        $publisher->publish(
            PatientUpdated::ROUTING_KEY,
            PatientUpdated::fromPatient($patient)->toPayload(),
        );

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
