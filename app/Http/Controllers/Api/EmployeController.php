<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\StoreEmployeRequest;
use App\Http\Requests\UpdateEmployeRequest;
use App\Http\Resources\V1\EmployeResource;
use App\Models\Employe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeController extends BaseController
{
    public function __construct()
    {
        $this->middleware('ability:employe:view,employe:create,employe:update,employe:delete');
    }

    public function index()
    {
        $employes = Employe::with('utilisateur')
            ->filter()
            ->paginate(10);

        return $this->sendResponse(
            EmployeResource::collection($employes),
            'Liste des employés récupérée avec succès'
        );
    }

    public function store(StoreEmployeRequest $request)
    {
        $validated = $request->validated();

        $password = Str::random(10);
        $utilisateur = Utilisateur::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
            'mot_de_passe' => Hash::make($password),
            'role' => 'employe',
        ]);

        $employe = Employe::create([
            'utilisateur_id' => $utilisateur->id,
            'date_naissance' => $validated['date_naissance'],
            'poste' => $validated['poste'],
            'departement' => $validated['departement'],
            'date_embauche' => $validated['date_embauche'],
            'statut' => $validated['statut'] ?? 'actif',
            'numero_employe' => 'EMP' . str_pad($utilisateur->id, 5, '0', STR_PAD_LEFT),
        ]);



        return $this->sendResponse(
            new EmployeResource($employe->load('utilisateur')),
            'Employé créé avec succès',
            Response::HTTP_CREATED
        );
    }

    public function show(Employe $employe)
    {
        return $this->sendResponse(
            new EmployeResource($employe->load('utilisateur', 'conges', 'absences', 'documents')),
            'Détails de l\'employé récupérés avec succès'
        );
    }

    public function update(UpdateEmployeRequest $request, Employe $employe)
    {
        $validated = $request->validated();

        $employe->utilisateur->update([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'email' => $validated['email'],
        ]);


        $employe->update([
            'date_naissance' => $validated['date_naissance'],
            'poste' => $validated['poste'],
            'departement' => $validated['departement'],
            'date_embauche' => $validated['date_embauche'],
            'statut' => $validated['statut'],
        ]);

        return $this->sendResponse(
            new EmployeResource($employe->load('utilisateur')),
            'Employé mis à jour avec succès'
        );
    }

    public function destroy(Employe $employe)
    {
        $employe->utilisateur->delete();
        $employe->delete();

        return $this->sendResponse(
            [],
            'Employé supprimé avec succès'
        );
    }
}