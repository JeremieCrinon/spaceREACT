<?php

namespace App\Http\Controllers;

use App\Models\Tech;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response; 

class TechController extends Controller
{
    /**
     * Display a listing of the resource for the menu.
     */
    public function indexForMenu()
    {
        $planets = Tech::select('id', 'fr_name', 'en_name')->get();
        return response()->json($planets);
    }

    /**
     * Display all the planets.
     */
    public function index()
    {
        $planets = Tech::all();
        return response()->json($planets);
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
            'fr_name' => 'required|max:20',
            'en_name' => 'required|max:20',
            'fr_description' => 'required|max:500',
            'en_description' => 'required|max:500',
            'image' => 'required|image',
        ]);
        $planet = new Tech;
        if ($request->hasFile('image')) {
            $planet->image = $request->file('image')->store('img', 'public');
        } else {
            return response()->json(['message' => 'Image not send'], 400);
        }
        $planet->fr_name = $request->fr_name;
        $planet->en_name = $request->en_name;
        $planet->fr_description = $request->fr_description;
        $planet->en_description = $request->en_description;
        $planet->image = $request->image->store('img', 'public');
        // $planet->image = "vvv";
        $planet->save();
        return response()->json(['message' => "La technologie a bien été créée !"]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Tech $id)
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
    public function edit(Tech $planet)
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
            'fr_name' => 'required|max:20',
            'en_name' => 'required|max:20',
            'fr_description' => 'required|max:500',
            'en_description' => 'required|max:500',
            'image' => 'nullable|image',
        ]);

        // Trouver la planète par son ID
        $planet = Tech::findOrFail($id);

        // Mise à jour des données
        $planet->fr_name = $validatedData['fr_name'];
        $planet->en_name = $validatedData['en_name'];
        $planet->fr_description = $validatedData['fr_description'];
        $planet->en_description = $validatedData['en_description'];

        // Gestion de l'upload de l'image
        if ($request->hasFile('image')) {
            // On fais le lien qui mène vers l'enciène image
            $imagePath = public_path("../storage/app/public/" . $planet->image);

            // Supprime l'image si elle existe
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            $planet->image = $request->file('image')->store('img', 'public');
            $planet->image = $request->image->store('img', 'public');
        }

        // Sauvegarder les modifications
        $planet->save();

        // Rediriger avec un message
        return response()->json(['message' => "La technologie a bien été modifiée !"]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {


        $planet = Tech::findOrFail($id); // Trouve la planète ou renvoie une erreur 404 si elle n'existe pas
        // Construit le chemin complet de l'image
        $imagePath = public_path("../storage/app/public/" . $planet->image);

        // Supprime l'image si elle existe
        if (file_exists($imagePath)) {
            $result = "La planete à correctement été supprimée !";
            unlink($imagePath);
        } else {
            $result = "L'image n'existe pas, elle se trouve normalement à l'emplacement " . $imagePath;
        }
        $planet->delete(); // Supprime la planète de la base de données

        return response()->json(['message' => $result]);
    }
}
