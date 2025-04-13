<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\StoreCongeRequest;
use App\Http\Requests\UpdateCongeRequest;
use App\Http\Resources\V1\CongeResource;
use App\Models\Conge;
use App\Models\Employe;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CongeController extends BaseController
{
    public function __construct()
    {
        $this->middleware('ability:conge:view,conge:create,conge:update,conge:delete,conge:approve');
    }

    public function index()
    {
        $user = auth()->user();
        $query = Conge::query()->with('employe.utilisateur');

        // Filtrage selon le rôle
        if ($user->role === 'employe') {
            $query->where('employe_id', $user->employe->id);
        } elseif ($user->role === 'manager') {
            $query->whereHas('employe', function($q) use ($user) {
                $q->where('departement', $user->employe->departement);
            });
        }

        // Filtres supplémentaires
        if ($employeId = request('employe_id')) {
            $query->where('employe_id', $employeId);
        }
        if ($statut = request('statut')) {
            $query->where('statut', $statut);
        }
        if ($type = request('type')) {
            $query->where('type', $type);
        }

        $conges = $query->filter()->paginate(10);

        return $this->sendResponse(
            CongeResource::collection($conges),
            'Liste des congés récupérée avec succès'
        );
    }

    public function store(StoreCongeRequest $request)
    {
        $validated = $request->validated();
        $validated['employe_id'] = $validated['employe_id'] ?? auth()->user()->employe->id;
        $validated['statut'] = 'en_attente';

        // Calcul de la durée
        $dateDebut = Carbon::parse($validated['date_debut']);
        $dateFin = Carbon::parse($validated['date_fin']);
        $validated['duree'] = $dateDebut->diffInDays($dateFin) + 1;

        $conge = Conge::create($validated);

        return $this->sendResponse(
            new CongeResource($conge->load('employe.utilisateur')),
            'Demande de congé créée avec succès',
            Response::HTTP_CREATED
        );
    }

    public function show(Conge $conge)
    {
        return $this->sendResponse(
            new CongeResource($conge->load('employe.utilisateur')),
            'Détails du congé récupérés avec succès'
        );
    }

    public function update(UpdateCongeRequest $request, Conge $conge)
    {
        // Vérification des autorisations
        if ($request->has('statut') && !auth()->user()->tokenCan('conge:approve')) {
            return $this->sendError(
                'Non autorisé à modifier le statut',
                Response::HTTP_FORBIDDEN
            );
        }

        $validated = $request->validated();
        
        // Si les dates changent, recalculer la durée
        if ($request->has(['date_debut', 'date_fin'])) {
            $dateDebut = Carbon::parse($validated['date_debut']);
            $dateFin = Carbon::parse($validated['date_fin']);
            $validated['duree'] = $dateDebut->diffInDays($dateFin) + 1;
        }

        $conge->update($validated);

        return $this->sendResponse(
            new CongeResource($conge->load('employe.utilisateur')),
            'Congé mis à jour avec succès'
        );
    }

    public function destroy(Conge $conge)
    {
        $conge->delete();

        return $this->sendResponse(
            [],
            'Congé supprimé avec succès'
        );
    }

    public function approve(Conge $conge)
    {
        if ($conge->statut !== 'en_attente') {
            return $this->sendError(
                'Seuls les congés en attente peuvent être approuvés',
                Response::HTTP_BAD_REQUEST
            );
        }

        // Vérifier le solde de congé
        $soldeRestant = $conge->employe->solde_conge - $conge->duree;
        if ($soldeRestant < 0) {
            return $this->sendError(
                'Solde de congé insuffisant',
                Response::HTTP_BAD_REQUEST
            );
        }

        $conge->update([
            'statut' => 'approuve',
            'solde_restant' => $soldeRestant,
        ]);

        // TODO: Envoyer une notification

        return $this->sendResponse(
            new CongeResource($conge->load('employe.utilisateur')),
            'Congé approuvé avec succès'
        );
    }

    public function reject(Conge $conge)
    {
        if ($conge->statut !== 'en_attente') {
            return $this->sendError(
                'Seuls les congés en attente peuvent être rejetés',
                Response::HTTP_BAD_REQUEST
            );
        }

        $conge->update([
            'statut' => 'rejete',
        ]);

        // TODO: Envoyer une notification

        return $this->sendResponse(
            new CongeResource($conge->load('employe.utilisateur')),
            'Congé rejeté avec succès'
        );
    }
}