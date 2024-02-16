const form_create_crew = document.getElementById('form_create_crew');
const create_message = document.getElementById('create_message');
const fr_role = document.getElementById('fr_role');
const en_role = document.getElementById('en_role');
const fr_description = document.getElementById('fr_description');
const en_description = document.getElementById('en_description');
const nameCrew = document.getElementById('name');

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

form_create_crew.addEventListener('submit', function(e){
    e.preventDefault();

    create_message.textContent = "";
    
    // Création de l'objet FormData
    let formData = new FormData(form_create_crew);

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
    formData.append('fr_role', fr_role.value);
    formData.append('en_role', en_role.value);
    formData.append('fr_description', fr_description.value);
    formData.append('en_description', en_description.value);
    formData.append('name', nameCrew.value);

    // Configuration et envoi de la requête
    fetch('http://localhost:8000/api/crew', {
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
        sessionStorage.setItem('message', "Crew created successfully");
        window.location.href = 'crew.html';
    })
    .catch(error => {
        create_message.textContent = "An error occurred while creating the planet";
        console.error(error)
    });
});