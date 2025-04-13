<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Http\Resources\V1\DocumentResource;
use App\Models\Document;
use App\Models\Employe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class DocumentController extends BaseController
{
    public function __construct()
    {
        $this->middleware('ability:document:view,document:create,document:update,document:delete');
    }

    public function index()
    {
        $user = auth()->user();
        $query = Document::query()->with('employe.utilisateur');

        // if ($user->role === 'employe') {
        //     $query->where('employe_id', $user->employe->id);
        // } elseif ($employeId = request('employe_id')) {
        //     $query->where('employe_id', $employeId);
        // }

        $documents = $query->filter()->paginate(10);

        return $this->sendResponse(
            DocumentResource::collection($documents),
            'Liste des documents récupérée avec succès'
        );
    }

    public function store(StoreDocumentRequest $request)
    {
        $validated = $request->validated();
        $file = $request->file('fichier');
        
        $path = $file->store('documents');
        
        $document = Document::create([
            'employe_id' => $validated['employe_id'],
            'nom' => $validated['nom'],
            'type' => $validated['type'],
            'chemin' => $path,
            'taille' => $file->getSize(),
            'extension' => $file->extension(),
        ]);

        return $this->sendResponse(
            new DocumentResource($document->load('employe.utilisateur')),
            'Document téléversé avec succès',
            Response::HTTP_CREATED
        );
    }

    public function show(Document $document)
    {
        return $this->sendResponse(
            new DocumentResource($document->load('employe.utilisateur')),
            'Détails du document récupérés avec succès'
        );
    }

    public function update(UpdateDocumentRequest $request, Document $document)
    {
        $document->update($request->validated());

        return $this->sendResponse(
            new DocumentResource($document->load('employe.utilisateur')),
            'Document mis à jour avec succès'
        );
    }

    public function destroy(Document $document)
    {
        Storage::delete($document->chemin);
        $document->delete();

        return $this->sendResponse(
            [],
            'Document supprimé avec succès'
        );
    }

    public function download(Document $document)
    {
        if (!Storage::exists($document->chemin)) {
            return $this->sendError(
                'Fichier non trouvé',
                Response::HTTP_NOT_FOUND
            );
        }

        return Storage::download($document->chemin, $document->nom . '.' . $document->extension);
    }
}