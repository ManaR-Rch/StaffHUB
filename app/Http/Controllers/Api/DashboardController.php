<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Models\Conge;
use App\Models\Employe;
use App\Models\Tache;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DashboardController extends BaseController
{
    public function stats()
    {
        $user = auth()->user();
        $stats = [];


        if ($user->isAdminRH()) {
            $stats['total_employes'] = Employe::count();
            $stats['employes_actifs'] = Employe::where('statut', 'actif')->count();
            $stats['conges_en_attente'] = Conge::where('statut', 'en_attente')->count();
        }

        if ($user->role === 'manager') {
            $stats['employes_equipe'] = Employe::where('departement', $user->employe->departement)
                ->count();
            $stats['conges_equipe'] = Conge::whereHas('employe', function($q) use ($user) {
                $q->where('departement', $user->employe->departement);
            })->where('statut', 'en_attente')->count();
        }

        $stats['mes_conges'] = [
            'en_attente' => $user->employe->conges()->where('statut', 'en_attente')->count(),
            'approuves' => $user->employe->conges()->where('statut', 'approuve')->count(),
            'rejetes' => $user->employe->conges()->where('statut', 'rejete')->count(),
        ];

        $stats['mes_taches'] = [
            'en_cours' => $user->employe->taches()->where('statut', 'en_cours')->count(),
            'terminees' => $user->employe->taches()->where('statut', 'terminee')->count(),
            'en_retard' => $user->employe->taches()
                ->where('statut', 'en_cours')
                ->where('date_echeance', '<', now())
                ->count(),
        ];

        return $this->sendResponse(
            $stats,
            'Statistiques du dashboard récupérées avec succès'
        );
    }

    public function recentActivities()
    {
        $user = auth()->user();
        $activities = [];


        $activities['conges'] = $user->employe->conges()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();


        $activities['taches'] = $user->employe->taches()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();


        if ($user->employe->documents()->exists()) {
            $activities['documents'] = $user->employe->documents()
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();
        }

        return $this->sendResponse(
            $activities,
            'Activités récentes récupérées avec succès'
        );
    }
}