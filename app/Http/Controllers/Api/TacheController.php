<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\StoreTacheRequest;
use App\Http\Requests\UpdateTacheRequest;
use App\Http\Resources\V1\TacheResource;
use App\Models\Tache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TacheController extends BaseController
{
  
    public function __construct()
    {
        $this->middleware('ability:tache:view,tache:create,tache:update,tache:delete,tache:assign');
    }
    public function index()
    {
        $user = auth()->user();
        $query = Tache::query()->with('employe.utilisateur', 'createur.utilisateur');

        if ($user->role === 'employe') {
            $query->where('employe_id', $user->employe->id);
        } elseif ($employeId = request('employe_id')) {
            $query->where('employe_id', $employeId);
        }

        if ($statut = request('statut')) {
            $query->where('statut', $statut);
        }

        $taches = $query->filter()->paginate(10);

        return $this->sendResponse(
            TacheResource::collection($taches),
            'Liste des tâches récupérée avec succès'
        );
    }

    public function store(StoreTacheRequest $request)
    {
        $validated = $request->validated();
        $validated['createur_id'] = auth()->user()->employe->id;

        $tache = Tache::create($validated);

        // TODO: Envoyer une notification

        return $this->sendResponse(
            new TacheResource($tache->load('employe.utilisateur', 'createur.utilisateur')),
            'Tâche créée avec succès',
            Response::HTTP_CREATED
        );
    }

    public function show(Tache $tache)
    {
        return $this->sendResponse(
            new TacheResource($tache->load('employe.utilisateur', 'createur.utilisateur')),
            'Détails de la tâche récupérés avec succès'
        );
    }

    public function update(UpdateTacheRequest $request, Tache $tache)
    {
        $tache->update($request->validated());

        return $this->sendResponse(
            new TacheResource($tache->load('employe.utilisateur', 'createur.utilisateur')),
            'Tâche mise à jour avec succès'
        );
    }

    public function destroy(Tache $tache)
    {
        $tache->delete();

        return $this->sendResponse(
            [],
            'Tâche supprimée avec succès'
        );
    }

    public function complete(Tache $tache)
    {
        if ($tache->employe_id !== auth()->user()->employe->id) {
            return $this->sendError(
                'Seul l\'employé assigné peut marquer la tâche comme terminée',
                Response::HTTP_FORBIDDEN
            );
        }

        $tache->update([
            'statut' => 'terminee',
            'date_completion' => now(),
        ]);

        return $this->sendResponse(
            new TacheResource($tache->load('employe.utilisateur', 'createur.utilisateur')),
            'Tâche marquée comme terminée avec succès'
        );
    }
}