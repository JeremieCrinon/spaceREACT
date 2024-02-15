<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class VerifyToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');

        if (!$token || !$this->isTokenValid($token)) {
            // Gérer l'accès refusé si le jeton est invalide
            return response()->json(['message' => 'Accès refusé, le jeton n\'est pas valide!'], 403);
        }

        return $next($request);
        // return response()->json(['message' => 'Accès refusé, le jeton n\'est pas valide!'], 403); //Modification temporaire pour vérifier que le middleware est bien utilisé.
    }

    private function isTokenValid($token)
    {
        $user = User::where('remember_token', $token)->first();
        if(!$user){
            return false;
        }
        return true;
    }
}
