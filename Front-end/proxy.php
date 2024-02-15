<?php
ob_start();
// if ($_GET['file'] !== "index.html") {
//     var_dump($_GET);
// }




$token = $_COOKIE['token']??''; // Récupérez le token de la requête

// Vérifiez le token avec l'API Laravel
$isTokenValid = callApiToVerifyToken($token);

// echo phpinfo();

if ($isTokenValid) {
    // Obtenez le chemin réel du fichier demandé
    $filePath = getFilePathBasedOnRequest();

    // Vérifiez que le fichier existe et n'est pas un répertoire
    if (file_exists($filePath) && !is_dir($filePath)) {
        // Obtenir le type MIME du fichier
        if (isset($_GET['file'])) {
            if (endsWith($_GET['file'], ".html")) {
                $mimeType = "text/html";
            } elseif(endsWith($_GET['file'], ".css")){
                $mimeType = "text/css";
            } elseif(endsWith($_GET['file'], ".js")){
                $mimeType = "application/javascript";
            }else {
                $mimeType = getMimeType($filePath);
            }
        } else {
            $mimeType = getMimeType($filePath);
        }
        
        header("Content-Type: " . $mimeType);


        // Servir le fichier
        readfile($filePath);

    } else {
        header("HTTP/1.1 404 Not Found");
        exit('File not found');
    }
} else {
    header("HTTP/1.1 401 Unauthorized");
    echo "<a href='http://localhost/en/login.html'>Se connecter</a><br>";
    exit('Unauthorized');
}

// Fonction pour appeler votre API Laravel et vérifier le token
function callApiToVerifyToken($token) {
    // Données à envoyer
    $data = array(
        "token" => $token
    );

    // Initialiser une session cURL
    $ch = curl_init();

    // Définir l'URL et d'autres options pour la requête POST
    curl_setopt($ch, CURLOPT_URL, "http://localhost:8000/api/token");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Exécuter la requête cURL
    $response = curl_exec($ch);

    // Fermer la session cURL
    curl_close($ch);

    $response = json_decode($response);

    // var_dump($data);

    if(!$response){
        echo "Erreur 500 : Problème serveur";
        die;
    }

    if($response->message === "L'utilisateur est autorisé") {
        // echo "true";
        // echo $response->message;
        return true;
    } else {
        // echo "false";
        // echo $response->message;
        return false;
    }

}

// Fonction pour déterminer le chemin du fichier basé sur la requête
function getFilePathBasedOnRequest() {
    $filePath = $_SERVER['DOCUMENT_ROOT'] . "/protected/" . $_GET['file'];
    return $filePath;
}


function getMimeType($filePath) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $filePath);
    finfo_close($finfo);
    return $mimeType;
}

function endsWith($string, $endString) {
    $len = strlen($endString);
    if ($len == 0) {
        return true;
    }
    return (substr($string, -$len) === $endString);
}

ob_end_flush();