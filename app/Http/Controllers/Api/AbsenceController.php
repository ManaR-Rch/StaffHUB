<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\StoreAbsenceRequest;
use App\Http\Requests\UpdateAbsenceRequest;
use App\Http\Resources\V1\AbsenceResource;
use App\Models\Absence;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AbsenceController extends BaseController
{
    public function __construct()
    {
        $this->middleware('ability:absence:view,absence:create,absence:update,absence:delete');
    }

    public function index()
    {
        $user = auth()->user();
        $query = Absence::query()->with('employe.utilisateur');

        if ($user->role === 'employe') {
            $query->where('employe_id', $user->employe->id);
        }

        $absences = $query->filter()->paginate(10);

        return $this->sendResponse(
            AbsenceResource::collection($absences),
            'Liste des absences récupérée avec succès'
        );
    }

    public function store(StoreAbsenceRequest $request)
    {
        $validated = $request->validated();
        $validated['employe_id'] = $validated['employe_id'] ?? auth()->user()->employe->id;

        $absence = Absence::create($validated);

        return $this->sendResponse(
            new AbsenceResource($absence->load('employe.utilisateur')),
            'Absence déclarée avec succès',
            Response::HTTP_CREATED
        );
    }

    public function show(Absence $absence)
    {
        return $this->sendResponse(
            new AbsenceResource($absence->load('employe.utilisateur')),
            'Détails de l\'absence récupérés avec succès'
        );
    }

    public function update(UpdateAbsenceRequest $request, Absence $absence)
    {
        $absence->update($request->validated());

        return $this->sendResponse(
            new AbsenceResource($absence->load('employe.utilisateur')),
            'Absence mise à jour avec succès'
        );
    }

    public function destroy(Absence $absence)
    {
        $absence->delete();

        return $this->sendResponse(
            [],
            'Absence supprimée avec succès'
        );
    }

    public function justify(Absence $absence, Request $request)
    {
        $request->validate([
            'justificatif' => 'required|file|mimes:pdf,jpg,png|max:2048',
        ]);

        $path = $request->file('justificatif')->store('justificatifs');

        $absence->update([
            'justificatif' => $path,
            'statut' => 'justifie',
        ]);

        return $this->sendResponse(
            new AbsenceResource($absence->load('employe.utilisateur')),
            'Absence justifiée avec succès'
        );
    }
}