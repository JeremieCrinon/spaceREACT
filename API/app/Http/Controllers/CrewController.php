<?php

namespace App\Http\Controllers;

use App\Models\Crew;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response; 

class CrewController extends Controller
{
    /**
     * Display a listing of the resource for the menu.
     */
    public function indexForMenu()
    {
        $crews = Crew::select('id', 'name')->get();
        return response()->json($crews);
    }

    /**
     * Display all the planets.
     */
    public function index()
    {
        $crews = Crew::all();
        return response()->json($crews);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'fr_role' => 'required|max:50',
            'en_role' => 'required|max:50',
            'fr_description' => 'required|max:500',
            'en_description' => 'required|max:500',
            'name' => 'required|max:50',
            'image' => 'required|image',
        ]);
        $crew = new Crew;
        if ($request->hasFile('image')) {
            $crew->image = $request->file('image')->store('img', 'public');
        } else {
            return response()->json(['message' => 'Image not send'], 400);
        }
        $crew->fr_role = $request->fr_role;
        $crew->en_role = $request->en_role;
        $crew->fr_description = $request->fr_description;
        $crew->en_description = $request->en_description;
        $crew->name = $request->name;
        $crew->image = $request->image->store('img', 'public');
        // $planet->image = "vvv";
        $crew->save();
        return response()->json(['message' => "Le crew a bien été créée !"]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Crew $id)
    {
        return response()->json($id);
    }

    /**
     * Display the image of a planet.
     */
    public function getImg($imgName)
    {
        $path = storage_path("app/public/img/" . $imgName);
    
        if (!File::exists($path)) {
            abort(404);
            // return response()->json(['message' => $path], 404);
        }

        $file = File::get($path);
        $type = File::mimeType($path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);

        return $response;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Crew $crew)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validation des données
        $validatedData = $request->validate([
            'fr_role' => 'required|max:50',
            'en_role' => 'required|max:50',
            'fr_description' => 'required|max:500',
            'en_description' => 'required|max:500',
            'name' => 'required|max:50',
            'image' => 'nullable|image',
        ]);

        // Trouver la planète par son ID
        $crew = Crew::findOrFail($id);

        // Mise à jour des données
        $crew->fr_role = $validatedData['fr_role'];
        $crew->en_role = $validatedData['en_role'];
        $crew->fr_description = $validatedData['fr_description'];
        $crew->en_description = $validatedData['en_description'];
        $crew->name = $validatedData['name'];

        // Gestion de l'upload de l'image
        if ($request->hasFile('image')) {
            // On fais le lien qui mène vers l'enciène image
            $imagePath = public_path("../storage/app/public/" . $crew->image);

            // Supprime l'image si elle existe
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            $crew->image = $request->file('image')->store('img', 'public');
            $crew->image = $request->image->store('img', 'public');
        }

        // Sauvegarder les modifications
        $crew->save();

        // Rediriger avec un message
        return response()->json(['message' => "Le crew a bien été modifiée !"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {


        $crew = Crew::findOrFail($id); // Trouve la planète ou renvoie une erreur 404 si elle n'existe pas
        // Construit le chemin complet de l'image
        $imagePath = public_path("../storage/app/public/" . $crew->image);

        // Supprime l'image si elle existe
        if (file_exists($imagePath)) {
            $result = "Le crew à correctement été supprimée !";
            unlink($imagePath);
        } else {
            $result = "L'image n'existe pas, elle se trouve normalement à l'emplacement " . $imagePath;
        }
        $crew->delete(); // Supprime la planète de la base de données

        return response()->json(['message' => $result]);
    }
}
