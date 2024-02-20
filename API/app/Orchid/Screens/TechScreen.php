<?php

namespace App\Orchid\Screens;

use Orchid\Screen\Screen;
use Orchid\Screen\Fields\Input;
use Orchid\Support\Facades\Layout;
use Orchid\Screen\Actions\ModalToggle;
use Illuminate\Http\Request;
use App\Models\Tech;
use Orchid\Screen\TD;
use Orchid\Screen\Actions\Button;

class TechScreen extends Screen
{
    /**
     * Fetch data to be displayed on the screen.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'teches' => Tech::latest()->get(),
        ];
    }

    /**
     * The name of the screen displayed in the header.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return 'Edit the teches';
    }

    /**
     * The description is displayed on the user's screen under the heading
     */
    public function description(): ?string
    {
        return 'Use this page to edit the teches that will be displayed on the website.';
    }

    /**
     * The screen's action buttons.
     *
     * @return \Orchid\Screen\Action[]
     */
    public function commandBar(): iterable
    {
        return [
            ModalToggle::make('Add Tech')
            ->modal('techModal')
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
            Layout::table('teches', [
                // TD::make('fr_name'),
                // TD::make('en_name'),
                // TD::make('fr_description'),
                // TD::make('en_description'),
                // TD::make('distance'),
                // TD::make('time'),
                TD::make('fr_name', 'French Name')
                    ->render(function (Tech $planet) {
                        return $planet->fr_name;
                    }),

                TD::make('en_name', 'English Name')
                    ->render(function (Tech $planet) {
                        return $planet->en_name;
                    }),

                TD::make('fr_description', 'French Description')
                    ->render(function (Tech $planet) {
                        return $this->truncateText($planet->fr_description, 50); // 50
                    }),

                TD::make('en_description', 'English Description')
                    ->render(function (Tech $planet) {
                        return $this->truncateText($planet->en_description, 50);
                    }),

                TD::make('Edit Tech')
                    ->render(function (Tech $planet) {
                        return ModalToggle::make('Edit Tech')
                            ->modal('techEditModal')
                            ->method('update') // Assurez-vous que la méthode est correcte
                            ->icon('pencil')
                            ->modalTitle('Edit Tech')
                            ->asyncParameters([
                                'techId' => $planet->id,
                            ]);
                    }),

                TD::make('Delete Tech')
                    ->render(function (Tech $planet) {
                        return Button::make('Delete Tech')
                            ->confirm('After deleting, the tech will be gone forever.')
                            ->method('delete', ['tech' => $planet->id]);
                    }),

                
            ]),

            Layout::modal('techEditModal', Layout::rows([
                Input::make('tech.fr_name')
                    ->title('Name in french')
                    ->value('tech.fr_name'),

                Input::make('tech.en_name')
                    ->title('Name in english')
                    ->value('tech.en_name'),

                Input::make('tech.fr_description')
                    ->title('Description in french')
                    ->value('tech.fr_description'),

                Input::make('tech.en_description')
                    ->title('Description in english')
                    ->value('tech.en_description'),
                
                Input::make('tech.image')
                    ->title('Image')
                    ->type('file') // Specify the input type as 'file' for uploading images
                    ->accept('image/*') // Accept only image files
                    ->placeholder('Upload tech image')
                    ->help('Upload a new image of the tech if you want to change it.'),

                
            ]))
                ->async('asyncGetPlanet')
                ->title('Edit Tech')
                ->applyButton('Edit the Tech'),

            

            Layout::modal('techModal', Layout::rows([
                Input::make('tech.fr_name')
                    ->title('Name in french')
                    ->placeholder('Enter tech french name')
                    ->help('The french name of the tech to be created.'),

                Input::make('tech.en_name')
                    ->title('Name in english')
                    ->placeholder('Enter tech english name')
                    ->help('The english name of the tech to be created.'),

                Input::make('tech.fr_description')
                    ->title('Description in french')
                    ->placeholder('Enter tech french description')
                    ->help('The french description of the tech to be created.'),

                Input::make('tech.en_description')
                    ->title('Description in english')
                    ->placeholder('Enter tech english description')
                    ->help('The english description of the tech to be created.'),
                
                Input::make('tech.image')
                    ->title('Image')
                    ->type('file') // Specify the input type as 'file' for uploading images
                    ->accept('image/*') // Accept only image files
                    ->placeholder('Upload tech image')
                    ->help('Upload an image of the tech.'),

                
            ]))
                ->title('Create Tech')
                ->applyButton('Add Tech'),
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
            'tech.fr_name' => 'required|max:20',
            'tech.en_name' => 'required|max:20',
            'tech.fr_description' => 'required|max:500',
            'tech.en_description' => 'required|max:500',
            'tech.image' => 'required|image',
        ]);

        $planet = new tech();

        if ($request->hasFile('tech.image')) {
            $planet->image = $request->file('tech.image')->store('img', 'public');
        } else {
            // return response()->json(['message' => 'Image not send'], 400);
        }

        $planet->fr_name = $request->input('tech.fr_name');
        $planet->en_name = $request->input('tech.en_name');
        $planet->fr_description = $request->input('tech.fr_description');
        $planet->en_description = $request->input('tech.en_description');
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
    public function delete(Tech $planet)
    {
        // Construit le chemin complet de l'image
        $imagePath = public_path("../storage/app/public/" . $planet->image);

        // Supprime l'image si elle existe
        if (file_exists($imagePath)) {
            $result = "La technologie à correctement été supprimée !";
            unlink($imagePath);
        } else {
            $result = "L'image n'existe pas, elle se trouve normalement à l'emplacement " . $imagePath;
        }
        $planet->delete();
    }

    public function asyncGetPlanet(int $techId): array
    {
        $planet = Tech::find($techId);

        return [
            'tech' => [
                'fr_name' => $planet->fr_name,
                'en_name' => $planet->en_name,
                'fr_description' => $planet->fr_description,
                'en_description' => $planet->en_description,
                'image' => $planet->image,
            ], // Renvoyer les données de la planète
        ];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $techId)
    {

        // dd($request->all());

        // Validation des données
        $validatedData = $request->validate([
            'tech.fr_name' => 'required|max:20',
            'tech.en_name' => 'required|max:20',
            'tech.fr_description' => 'required|max:500',
            'tech.en_description' => 'required|max:500',
            'tech.image' => 'nullable|image',
        ]);

        // Trouver la planète par son ID
        $planet = Tech::findOrFail($techId);

        // Mise à jour des données
        $planet->fr_name = $validatedData['tech']['fr_name'];
        $planet->en_name = $validatedData['tech']['en_name'];
        $planet->fr_description = $validatedData['tech']['fr_description'];
        $planet->en_description = $validatedData['tech']['en_description'];

        // Gestion de l'upload de l'image
        if ($request->hasFile('tech.image')) {
            // On fais le lien qui mène vers l'enciène image
            $imagePath = public_path("../storage/app/public/" . $planet->image);

            // Supprime l'image si elle existe
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            $planet->image = $request->file('tech.image')->store('img', 'public');
            // $planet->image = $request->image->store('img', 'public');
        }

        // Sauvegarder les modifications
        $planet->save();
    }

}
