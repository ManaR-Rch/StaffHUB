<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\StorePaieRequest;
use App\Http\Requests\UpdatePaieRequest;
use App\Http\Resources\V1\PaieResource;
use App\Models\Paie;
use App\Models\Employe;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class PaieController extends BaseController
{
    public function __construct()
    {
        $this->middleware('ability:paie:view,paie:create,paie:update,paie:delete');
    }

    public function index()
    {
        $user = auth()->user();
        $query = Paie::query()->with('employe.utilisateur');

        if ($user->role === 'employe') {
            $query->where('employe_id', $user->employe->id);
        } elseif ($employeId = request('employe_id')) {
            $query->where('employe_id', $employeId);
        }

        if ($mois = request('mois')) {
            $query->where('mois', $mois);
        }

        $paies = $query->filter()->paginate(10);

        return $this->sendResponse(
            PaieResource::collection($paies),
            'Liste des paies récupérée avec succès'
        );
    }

    public function store(StorePaieRequest $request)
    {
        $validated = $request->validated();
        
        // Calcul du salaire net
        $salaireNet = $validated['salaire_base'] 
                     + $validated['primes'] 
                     - $validated['deductions'];

        $paie = Paie::create([
            'employe_id' => $validated['employe_id'],
            'mois' => $validated['mois'],
            'salaire_base' => $validated['salaire_base'],
            'primes' => $validated['primes'],
            'deductions' => $validated['deductions'],
            'salaire_net' => $salaireNet,
            'statut' => 'brouillon',
        ]);

        return $this->sendResponse(
            new PaieResource($paie->load('employe.utilisateur')),
            'Fiche de paie créée avec succès',
            Response::HTTP_CREATED
        );
    }

    public function show(Paie $paie)
    {
        return $this->sendResponse(
            new PaieResource($paie->load('employe.utilisateur')),
            'Détails de la paie récupérés avec succès'
        );
    }

    public function update(UpdatePaieRequest $request, Paie $paie)
    {
        $validated = $request->validated();
        
        if ($paie->statut === 'valide') {
            return $this->sendError(
                'Une paie validée ne peut pas être modifiée',
                Response::HTTP_BAD_REQUEST
            );
        }

        // Recalcul si les valeurs changent
        if ($request->hasAny(['salaire_base', 'primes', 'deductions'])) {
            $validated['salaire_net'] = $validated['salaire_base'] 
                                       + $validated['primes'] 
                                       - $validated['deductions'];
        }

        $paie->update($validated);

        return $this->sendResponse(
            new PaieResource($paie->load('employe.utilisateur')),
            'Paie mise à jour avec succès'
        );
    }

    public function destroy(Paie $paie)
    {
        if ($paie->statut === 'valide') {
            return $this->sendError(
                'Une paie validée ne peut pas être supprimée',
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($paie->fichier_pdf) {
            Storage::delete($paie->fichier_pdf);
        }

        $paie->delete();

        return $this->sendResponse(
            [],
            'Paie supprimée avec succès'
        );
    }

    public function validatePaie(Paie $paie)
    {
        if ($paie->statut !== 'brouillon') {
            return $this->sendError(
                'Seules les paies en brouillon peuvent être validées',
                Response::HTTP_BAD_REQUEST
            );
        }

        // Génération du PDF
        $pdf = Pdf::loadView('paies.pdf', ['paie' => $paie]);
        $filename = 'paie_' . $paie->employe->numero_employe . '_' . $paie->mois . '.pdf';
        $path = 'paies/' . $filename;
        
        Storage::put($path, $pdf->output());

        $paie->update([
            'statut' => 'valide',
            'fichier_pdf' => $path,
            'date_validation' => now(),
        ]);

        // TODO: Envoyer un email avec la fiche de paie

        return $this->sendResponse(
            new PaieResource($paie->load('employe.utilisateur')),
            'Paie validée avec succès'
        );
    }

    public function download(Paie $paie)
    {
        if (!$paie->fichier_pdf || !Storage::exists($paie->fichier_pdf)) {
            return $this->sendError(
                'Fiche de paie non disponible',
                Response::HTTP_NOT_FOUND
            );
        }

        return Storage::download($paie->fichier_pdf, 'fiche_paie_' . $paie->mois . '.pdf');
    }
}