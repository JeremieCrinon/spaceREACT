<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\ModalToggle;
use Illuminate\Http\Request;
use App\Models\Planet;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;

class PlanetScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'planets' => Planet::latest()->get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Edit the planets';
    }

    /**
     * The description is displayed on the user's screen under the heading
     */
    public function description(): ?string
    {
        return 'Use this page to edit the planets that will be displayed on the website.';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Add Planet')
            ->modal('planetModal')
            ->method('create')
            ->icon('plus'),
        ];
    }

    /**
     * The screen's layout elements.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::table('planets', [
                // TD::make('fr_name'),
                // TD::make('en_name'),
                // TD::make('fr_description'),
                // TD::make('en_description'),
                // TD::make('distance'),
                // TD::make('time'),
                TD::make('fr_name', 'French Name')
                    ->render(function (Planet $planet) {
                        return $planet->fr_name;
                    }),

                TD::make('en_name', 'English Name')
                    ->render(function (Planet $planet) {
                        return $planet->en_name;
                    }),

                TD::make('fr_description', 'French Description')
                    ->render(function (Planet $planet) {
                        return $this->truncateText($planet->fr_description, 50); // 50
                    }),

                TD::make('en_description', 'English Description')
                    ->render(function (Planet $planet) {
                        return $this->truncateText($planet->en_description, 50);
                    }),

                TD::make('distance', 'Distance')
                    ->render(function (Planet $planet) {
                        return $planet->distance;
                    }),

                TD::make('time', 'Time')
                    ->render(function (Planet $planet) {
                        return $planet->time;
                    }),

                TD::make('Edit Planet')
                    ->render(function (Planet $planet) {
                        return ModalToggle::make('Edit Planet')
                            ->modal('planetEditModal')
                            ->method('update') // Assurez-vous que la méthode est correcte
                            ->icon('pencil')
                            ->modalTitle('Edit Planet')
                            ->asyncParameters([
                                'planetId' => $planet->id,
                            ]);
                    }),

                TD::make('Delete Planet')
                    ->render(function (Planet $planet) {
                        return Button::make('Delete Planet')
                            ->confirm('After deleting, the planet will be gone forever.')
                            ->method('delete', ['planet' => $planet->id]);
                    }),

                
            ]),

            Layout::modal('planetEditModal', Layout::rows([
                Input::make('planet.fr_name')
                    ->title('Name in french')
                    ->value('planet.fr_name'),

                Input::make('planet.en_name')
                    ->title('Name in english')
                    ->value('planet.en_name'),

                Input::make('planet.fr_description')
                    ->title('Description in french')
                    ->value('planet.fr_description'),

                Input::make('planet.en_description')
                    ->title('Description in english')
                    ->value('planet.en_description'),
                
                Input::make('planet.distance')
                    ->title('Distance')
                    ->value('planet.distance'),

                Input::make('planet.time')
                    ->title('Time')
                    ->value('planet.time'),

                Input::make('planet.image')
                    ->title('Image')
                    ->type('file') // Specify the input type as 'file' for uploading images
                    ->accept('image/*') // Accept only image files
                    ->placeholder('Upload planet image')
                    ->help('Upload a new image of the planet if you want to change it.'),

                
            ]))
                ->async('asyncGetPlanet')
                ->title('Edit Planet')
                ->applyButton('Edit the Planet'),

            

            Layout::modal('planetModal', Layout::rows([
                Input::make('planet.fr_name')
                    ->title('Name in french')
                    ->placeholder('Enter planet french name')
                    ->help('The french name of the planet to be created.'),

                Input::make('planet.en_name')
                    ->title('Name in english')
                    ->placeholder('Enter planet english name')
                    ->help('The english name of the planet to be created.'),

                Input::make('planet.fr_description')
                    ->title('Description in french')
                    ->placeholder('Enter planet french description')
                    ->help('The french description of the planet to be created.'),

                Input::make('planet.en_description')
                    ->title('Description in english')
                    ->placeholder('Enter planet english description')
                    ->help('The english description of the planet to be created.'),
                
                Input::make('planet.distance')
                    ->title('Distance')
                    ->placeholder('Enter planet distance')
                    ->help('The distance of the planet to be created.'),

                Input::make('planet.time')
                    ->title('Time')
                    ->placeholder('Enter planet time')
                    ->help('The time of the planet to be created.'),

                Input::make('planet.image')
                    ->title('Image')
                    ->type('file') // Specify the input type as 'file' for uploading images
                    ->accept('image/*') // Accept only image files
                    ->placeholder('Upload planet image')
                    ->help('Upload an image of the planet.'),

                
            ]))
                ->title('Create Planet')
                ->applyButton('Add Planet'),
        ];
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function create(Request $request)
    {
        // Validate form data, save task to database, etc.
        $request->validate([
            'planet.fr_name' => 'required|max:20',
            'planet.en_name' => 'required|max:20',
            'planet.fr_description' => 'required|max:500',
            'planet.en_description' => 'required|max:500',
            'planet.distance' => 'required|max:20',
            'planet.time' => 'required|max:20',
            'planet.image' => 'required|image',
        ]);

        $planet = new planet();

        if ($request->hasFile('planet.image')) {
            $planet->image = $request->file('planet.image')->store('img', 'public');
        } else {
            // return response()->json(['message' => 'Image not send'], 400);
        }

        $planet->fr_name = $request->input('planet.fr_name');
        $planet->en_name = $request->input('planet.en_name');
        $planet->fr_description = $request->input('planet.fr_description');
        $planet->en_description = $request->input('planet.en_description');
        $planet->distance = $request->input('planet.distance');
        $planet->time = $request->input('planet.time');
        // $planet->image = $request->input('planet.image');

        $planet->save();
    }

    /**
     * Truncate text to a specified length and append '...' if necessary.
     *
     * @param string $text
     * @param int $length
     * @return string
     */
    private function truncateText(string $text, int $length): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length) . '...';
    }


    /**
     * @param Task $task
     *
     * @return void
     */
    public function delete(Planet $planet)
    {
        // Construit le chemin complet de l'image
        $imagePath = public_path("../storage/app/public/" . $planet->image);

        // Supprime l'image si elle existe
        if (file_exists($imagePath)) {
            $result = "La planete à correctement été supprimée !";
            unlink($imagePath);
        } else {
            $result = "L'image n'existe pas, elle se trouve normalement à l'emplacement " . $imagePath;
        }
        $planet->delete();
    }

    public function asyncGetPlanet(int $planetId): array
    {
        $planet = Planet::find($planetId);

        return [
            'planet' => [
                'fr_name' => $planet->fr_name,
                'en_name' => $planet->en_name,
                'fr_description' => $planet->fr_description,
                'en_description' => $planet->en_description,
                'distance' => $planet->distance,
                'time' => $planet->time,
                'image' => $planet->image,
            ], // Renvoyer les données de la planète
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $planetId)
    {

        // dd($request->all());

        // Validation des données
        $validatedData = $request->validate([
            'planet.fr_name' => 'required|max:20',
            'planet.en_name' => 'required|max:20',
            'planet.fr_description' => 'required|max:500',
            'planet.en_description' => 'required|max:500',
            'planet.distance' => 'required|max:20',
            'planet.time' => 'required|max:20',
            'planet.image' => 'nullable|image',
        ]);

        // Trouver la planète par son ID
        $planet = Planet::findOrFail($planetId);

        // Mise à jour des données
        $planet->fr_name = $validatedData['planet']['fr_name'];
        $planet->en_name = $validatedData['planet']['en_name'];
        $planet->fr_description = $validatedData['planet']['fr_description'];
        $planet->en_description = $validatedData['planet']['en_description'];
        $planet->distance = $validatedData['planet']['distance'];
        $planet->time = $validatedData['planet']['time'];

        // Gestion de l'upload de l'image
        if ($request->hasFile('planet.image')) {
            // On fais le lien qui mène vers l'enciène image
            $imagePath = public_path("../storage/app/public/" . $planet->image);

            // Supprime l'image si elle existe
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            $planet->image = $request->file('planet.image')->store('img', 'public');
            // $planet->image = $request->image->store('img', 'public');
        }

        // Sauvegarder les modifications
        $planet->save();
    }

}
