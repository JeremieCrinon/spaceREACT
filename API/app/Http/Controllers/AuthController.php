<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request){
        if(User::exists()){
            return response()->json(['message' => "Un seul compte ne peut être créé, celui de l'administrateur !"], 400);
        }
        // $data = $request->validate([
        //     'name' => 'required|max:20',
        //     'email' => 'required|email|unique:users|confirmed',
        //     'password' => 'required|confirmed',
        // ]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:20',
            'email' => 'required|email|unique:users|confirmed',
            'password' => 'required|confirmed',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        

        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email; 
        $user->password = bcrypt($request->password);
        $user->save();
        return response()->json(['message' => "L'utilisateur a bien été créé !"]);
    }

    public function login(Request $request){
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json(['message' => "L'email ou le mot de passe est incorrect !"], 401);
        }

        $token = Hash::make($user->id . '|' . now());

        $user->remember_token = $token;
        $user->save();

        return response()->json(['message' => "Vous êtes connecté.e", 'token' => $token], 200);
    }

    public function checkToken(Request $request){
        $user = User::where('remember_token', $request->token)->first();
        if(!$user){
            return response()->json(['message' => "L'utilisateur n'est pas autorisé"], 401);
        }
        return response()->json(['message' => "L'utilisateur est autorisé"], 200);
    }

    public function giveUserInfo(){
        $user = User::where('remember_token', request()->header('Authorization'))->first();
        return response()->json(['name' => $user->name, 'email' => $user->email], 200);
    }

    public function changeUserName(Request $request){
        $user = User::where('remember_token', request()->header('Authorization'))->first();

        if(!$user){
            return response()->json(['L\'utilisateur n\'existe pas!'], 404);
        }

        $user->name = $request->name;

        $user->save();

        return response()->json(['message' => "Le nom a bien été modifié !"], 200);
    }

    public function changeUserMail(Request $request){
        $user = User::where('remember_token', request()->header('Authorization'))->first();

        if(!$user){
            return response()->json(['L\'utilisateur n\'existe pas!'], 404);
        }

        $user->email = $request->email;

        $user->save();

        return response()->json(['message' => "L'email à bien été modifié !"], 200);
    }

    public function changeUserPasswd(Request $request){
        $data = $request->validate([
            'password' => 'required',
            'new_passwd' => 'required',
            'confirm_passwd' => 'required',
        ]);

        $user = User::where('remember_token', request()->header('Authorization'))->first();

        if(!$user){
            return response()->json(['L\'utilisateur n\'existe pas!'], 404);
        }
        
        if(!$user || !Hash::check($request->password, $user->password)){
            return response()->json(['message' => "Le mot de passe est incorrect !"], 401); //On précise que le mot de passe est incorecte, car la personne qui se trouve ici à l'email de l'utilisateur affiché sur cette page
        }

        if ($request->new_passwd !== $request->confirm_passwd){
            return response()->json(['message' => "Le nouveau mot de passe et la confirmation du nouveau mot de passe ne correspondent pas!"]);
        }

        $user->password = bcrypt($request->new_passwd);

        $user -> save();

        return response()->json(['message' => "Le mot de passe à bien été changé !"]);
        
    }
}
