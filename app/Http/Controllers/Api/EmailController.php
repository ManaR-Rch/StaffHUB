<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\StoreEmailRequest;
use App\Http\Resources\V1\EmailResource;
use App\Models\Email;
use App\Models\Employe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificationEmail;

class EmailController extends BaseController
{
    public function __construct()
    {
        $this->middleware('ability:email:view,email:create');
    }

    public function index()
    {
        $user = auth()->user();
        $query = Email::query()->with('destinataire.utilisateur');

        if ($user->role === 'employe') {
            $query->where('destinataire_id', $user->employe->id);
        } elseif ($employeId = request('destinataire_id')) {
            $query->where('destinataire_id', $employeId);
        }

        $emails = $query->filter()->paginate(10);

        return $this->sendResponse(
            EmailResource::collection($emails),
            'Liste des emails récupérée avec succès'
        );
    }

    public function store(StoreEmailRequest $request)
    {
        $validated = $request->validated();
        
        // Envoi de l'email
        $destinataire = Employe::find($validated['destinataire_id']);
        
        Mail::to($destinataire->utilisateur->email)
            ->send(new NotificationEmail(
                $validated['sujet'],
                $validated['message']
            ));

        // Enregistrement en base
        $email = Email::create([
            'destinataire_id' => $validated['destinataire_id'],
            'sujet' => $validated['sujet'],
            'message' => $validated['message'],
            'type' => $validated['type'],
            'statut' => 'envoye',
        ]);

        return $this->sendResponse(
            new EmailResource($email->load('destinataire.utilisateur')),
            'Email envoyé avec succès',
            Response::HTTP_CREATED
        );
    }

    public function show(Email $email)
    {
        return $this->sendResponse(
            new EmailResource($email->load('destinataire.utilisateur')),
            'Détails de l\'email récupérés avec succès'
        );
    }

    public function types()
    {
        $types = [
            'conges' => 'Notification de congé',
            'paies' => 'Notification de paie',
            'documents' => 'Notification de document',
            'general' => 'Notification générale',
        ];

        return $this->sendResponse(
            $types,
            'Types d\'emails récupérés avec succès'
        );
    }
}