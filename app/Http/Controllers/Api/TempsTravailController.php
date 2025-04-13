<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\StoreTempsTravailRequest;
use App\Http\Resources\V1\TempsTravailResource;
use App\Models\TempsTravail;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class TempsTravailController extends BaseController
{
    public function __construct()
    {
        $this->middleware('ability:temps:view,temps:create,temps:update');
    }

    public function index()
    {
        $user = auth()->user();
        $query = TempsTravail::query()->with('employe.utilisateur');

        if ($user->role === 'employe') {
            $query->where('employe_id', $user->employe->id);
        } elseif ($employeId = request('employe_id')) {
            $query->where('employe_id', $employeId);
        }

        if ($date = request('date')) {
            $query->where('date', $date);
        }

        if ($mois = request('mois')) {
            $query->whereMonth('date', Carbon::parse($mois)->month)
                 ->whereYear('date', Carbon::parse($mois)->year);
        }

        $tempsTravail = $query->filter()->paginate(10);

        return $this->sendResponse(
            TempsTravailResource::collection($tempsTravail),
            'Liste des temps de travail récupérée avec succès'
        );
    }

    public function store(StoreTempsTravailRequest $request)
    {
        $validated = $request->validated();
        $validated['employe_id'] = $validated['employe_id'] ?? auth()->user()->employe->id;

        // Calcul des heures travaillées
        $heureDebut = Carbon::parse($validated['heure_debut']);
        $heureFin = Carbon::parse($validated['heure_fin']);
        $validated['total_heures'] = $heureDebut->diffInHours($heureFin) + $heureDebut->diffInMinutes($heureFin) % 60 / 60;

        $tempsTravail = TempsTravail::create($validated);

        return $this->sendResponse(
            new TempsTravailResource($tempsTravail->load('employe.utilisateur')),
            'Temps de travail enregistré avec succès',
            Response::HTTP_CREATED
        );
    }

    public function show(TempsTravail $tempsTravail)
    {
        return $this->sendResponse(
            new TempsTravailResource($tempsTravail->load('employe.utilisateur')),
            'Détails du temps de travail récupérés avec succès'
        );
    }

    public function update(StoreTempsTravailRequest $request, TempsTravail $tempsTravail)
    {
        $validated = $request->validated();

        // Recalcul si les heures changent
        if ($request->has(['heure_debut', 'heure_fin'])) {
            $heureDebut = Carbon::parse($validated['heure_debut']);
            $heureFin = Carbon::parse($validated['heure_fin']);
            $validated['total_heures'] = $heureDebut->diffInHours($heureFin) + $heureDebut->diffInMinutes($heureFin) % 60 / 60;
        }

        $tempsTravail->update($validated);

        return $this->sendResponse(
            new TempsTravailResource($tempsTravail->load('employe.utilisateur')),
            'Temps de travail mis à jour avec succès'
        );
    }

    public function stats()
    {
       
     
        $stats = $query->selectRaw('
            YEAR(date) as annee,
            MONTH(date) as mois,
            SUM(total_heures) as total_heures,
            COUNT(DISTINCT date) as jours_travailles
        ')
        ->groupByRaw('YEAR(date), MONTH(date)')
        ->orderByRaw('YEAR(date) DESC, MONTH(date) DESC')
        ->get();

        return $this->sendResponse(
            $stats,
            'Statistiques récupérées avec succès'
        );
    }
}