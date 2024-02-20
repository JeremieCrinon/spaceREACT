<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\ModalToggle;
use Illuminate\Http\Request;
use App\Models\Crew;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;

class CrewScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'crews' => Crew::latest()->get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Edit the crews';
    }

    /**
     * The description is displayed on the user's screen under the heading
     */
    public function description(): ?string
    {
        return 'Use this page to edit the crews that will be displayed on the website.';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Add Crew')
            ->modal('crewModal')
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
            Layout::table('crews', [
                // TD::make('fr_name'),
                // TD::make('en_name'),
                // TD::make('fr_description'),
                // TD::make('en_description'),
                // TD::make('distance'),
                // TD::make('time'),
                TD::make('fr_role', 'French Role')
                    ->render(function (Crew $crew) {
                        return $crew->fr_role;
                    }),

                TD::make('en_role', 'English Role')
                    ->render(function (Crew $crew) {
                        return $crew->en_role;
                    }),

                TD::make('fr_description', 'French Description')
                    ->render(function (Crew $crew) {
                        return $this->truncateText($crew->fr_description, 50); // 50
                    }),

                TD::make('en_description', 'English Description')
                    ->render(function (Crew $crew) {
                        return $this->truncateText($crew->en_description, 50);
                    }),

                TD::make('name', 'Name')
                    ->render(function (Crew $crew) {
                        return $crew->name;
                    }),

                TD::make('Edit Crew')
                    ->render(function (Crew $crew) {
                        return ModalToggle::make('Edit Crew')
                            ->modal('crewEditModal')
                            ->method('update') // Assurez-vous que la méthode est correcte
                            ->icon('pencil')
                            ->modalTitle('Edit Crew')
                            ->asyncParameters([
                                'crewId' => $crew->id,
                            ]);
                    }),

                TD::make('Delete Crew')
                    ->render(function (Crew $crew) {
                        return Button::make('Delete Crew')
                            ->confirm('After deleting, the crew will be gone forever.')
                            ->method('delete', ['crew' => $crew->id]);
                    }),

                
            ]),

            Layout::modal('crewEditModal', Layout::rows([
                Input::make('crew.fr_role')
                    ->title('Role in french')
                    ->required()
                    ->value('crew.fr_role'),

                Input::make('crew.en_role')
                    ->title('Role in english')
                    ->required()
                    ->value('crew.en_role'),

                Input::make('crew.fr_description')
                    ->title('Description in french')
                    ->required()
                    ->value('crew.fr_description'),

                Input::make('crew.en_description')
                    ->title('Description in english')
                    ->required()
                    ->value('crew.en_description'),
                
                Input::make('crew.name')
                    ->title('Name')
                    ->required()
                    ->value('crew.name'),

                Input::make('crew.image')
                    ->title('Image')
                    ->type('file') // Specify the input type as 'file' for uploading images
                    ->accept('image/*') // Accept only image files
                    ->placeholder('Upload crew image')
                    ->required()
                    ->help('Upload a new image of the crew if you want to change it.'),

                
            ]))
                ->async('asyncGetCrew')
                ->title('Edit Crew')
                ->applyButton('Edit the Crew'),

            

            Layout::modal('crewModal', Layout::rows([
                Input::make('crew.fr_role')
                    ->title('Role in french')
                    ->placeholder('Enter planet french role')
                    ->required()
                    ->help('The french role of the crew to be created.'),

                Input::make('crew.en_role')
                    ->title('Role in english')
                    ->placeholder('Enter crew english role')
                    ->required()
                    ->help('The english role of the crew to be created.'),

                Input::make('crew.fr_description')
                    ->title('Description in french')
                    ->placeholder('Enter crew french description')
                    ->required()
                    ->help('The french description of the crew to be created.'),

                Input::make('crew.en_description')
                    ->title('Description in english')
                    ->placeholder('Enter crew english description')
                    ->required()
                    ->help('The english description of the crew to be created.'),
                
                Input::make('crew.name')
                    ->title('Name')
                    ->placeholder('Enter crew name')
                    ->required()
                    ->help('The name of the crew to be created.'),

                Input::make('crew.image')
                    ->title('Image')
                    ->type('file') // Specify the input type as 'file' for uploading images
                    ->accept('image/*') // Accept only image files
                    ->placeholder('Upload crew image')
                    ->required()
                    ->help('Upload an image of the crew.'),

                
            ]))
                ->title('Create Crew')
                ->applyButton('Add Crew'),
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
            'crew.fr_role' => 'required|max:20',
            'crew.en_role' => 'required|max:20',
            'crew.fr_description' => 'required|max:500',
            'crew.en_description' => 'required|max:500',
            'crew.name' => 'required|max:20',
            'crew.image' => 'required|image',
        ]);

        $crew = new crew();

        if ($request->hasFile('crew.image')) {
            $crew->image = $request->file('crew.image')->store('img', 'public');
        } else {
            // return response()->json(['message' => 'Image not send'], 400);
        }

        $crew->fr_role = $request->input('crew.fr_role');
        $crew->en_role = $request->input('crew.en_role');
        $crew->fr_description = $request->input('crew.fr_description');
        $crew->en_description = $request->input('crew.en_description');
        $crew->name = $request->input('crew.name');
        // $planet->image = $request->input('planet.image');

        $crew->save();
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
    public function delete(Crew $crew)
    {
        // Construit le chemin complet de l'image
        $imagePath = public_path("../storage/app/public/" . $crew->image);

        // Supprime l'image si elle existe
        if (file_exists($imagePath)) {
            $result = "Le crew à correctement été supprimée !";
            unlink($imagePath);
        } else {
            $result = "Le crew n'existe pas, elle se trouve normalement à l'emplacement " . $imagePath;
        }
        $crew->delete();
    }

    public function asyncGetCrew(int $crewId): array
    {
        $crew = Crew::find($crewId);

        return [
            'crew' => [
                'fr_role' => $crew->fr_role,
                'en_role' => $crew->en_role,
                'fr_description' => $crew->fr_description,
                'en_description' => $crew->en_description,
                'name' => $crew->name,
                'image' => $crew->image,
            ], // Renvoyer les données de la planète
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $crewId)
    {

        // dd($request->all());

        // Validation des données
        $validatedData = $request->validate([
            'crew.fr_role' => 'required|max:20',
            'crew.en_role' => 'required|max:20',
            'crew.fr_description' => 'required|max:500',
            'crew.en_description' => 'required|max:500',
            'crew.name' => 'required|max:20',
            'crew.image' => 'nullable|image',
        ]);

        // Trouver la planète par son ID
        $crew = Crew::findOrFail($crewId);

        // Mise à jour des données
        $crew->fr_role = $validatedData['crew']['fr_role'];
        $crew->en_role = $validatedData['crew']['en_role'];
        $crew->fr_description = $validatedData['crew']['fr_description'];
        $crew->en_description = $validatedData['crew']['en_description'];
        $crew->name = $validatedData['crew']['name'];

        // Gestion de l'upload de l'image
        if ($request->hasFile('crew.image')) {
            // On fais le lien qui mène vers l'enciène image
            $imagePath = public_path("../storage/app/public/" . $crew->image);

            // Supprime l'image si elle existe
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            $crew->image = $request->file('crew.image')->store('img', 'public');
            // $planet->image = $request->image->store('img', 'public');
        }

        // Sauvegarder les modifications
        $crew->save();
    }

}
