const form_create_planet = document.getElementById('form_create_planet');
const create_message = document.getElementById('create_message');
const fr_name = document.getElementById('fr_name');
const en_name = document.getElementById('en_name');
const fr_description = document.getElementById('fr_description');
const en_description = document.getElementById('en_description');

function getCookieValue(name) {
    let cookies = document.cookie; // Obtient tous les cookies sous forme de chaîne
    let cookieArray = cookies.split('; '); // Sépare les cookies en un tableau

    for (let cookie of cookieArray) {
        let [cookieName, cookieValue] = cookie.split('='); // Sépare le nom et la valeur du cookie
        if (cookieName === name) {
            return cookieValue; // Retourne la valeur si le nom correspond
        }
    }
    return null; // Retourne null si le cookie n'est pas trouvé
}

form_create_planet.addEventListener('submit', function(e){
    e.preventDefault();

    create_message.textContent = "";
    
    // Création de l'objet FormData
    let formData = new FormData(form_create_planet);

    // Récupération du fichier
    let fileInput = document.getElementById('image');
    let file = fileInput.files[0];

    // Vérification si le fichier est une image
    if (!file.type.match('image.*')) {
        create_message.textContent = "Please select an image file for the planet";
        return;
    }

    // Ajout du fichier à l'objet FormData
    formData.append('image', file);
    formData.append('fr_name', fr_name.value);
    formData.append('en_name', en_name.value);
    formData.append('fr_description', fr_description.value);
    formData.append('en_description', en_description.value);

    // Configuration et envoi de la requête
    fetch('http://localhost:8000/api/tech', {
        method: 'POST',
        headers: {
            'Authorization': getCookieValue('token'),
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Réponse serveur non OK');
        }
        return response.json();
    })
    .then(data => {
        console.log(data)
        sessionStorage.setItem('message', "Tech created successfully");
        window.location.href = 'tech.html';
    })
    .catch(error => {
        create_message.textContent = "An error occurred while creating the planet";
        console.error(error)
    });
});