const params = new URLSearchParams(window.location.search);
const id = params.get('id');
const planet_img = document.getElementById('planet_img');
const fr_name = document.getElementById('fr_name');
const en_name = document.getElementById('en_name');
const fr_description = document.getElementById('fr_description');
const en_description = document.getElementById('en_description');
const distance = document.getElementById('distance');
const time = document.getElementById('time');
const image = document.getElementById('image');
const edit_planet_form = document.getElementById('edit_planet_form');

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

fetch(`http://localhost:8000/api/planet/${id}`)
    .then(response => response.json())
    .then(data => {
        console.log(data);
        // Destination_text_title.textContent = data.en_name;
        // Destination_text_subtitle.textContent = data.en_description;
        // Destination_text_distance.textContent = data.distance;
        // Destination_text_time.textContent = data.time;
        fr_name.value = data.fr_name;
        en_name.value = data.en_name;
        fr_description.value = data.fr_description;
        en_description.value = data.en_description;
        distance.value = data.distance;
        time.value = data.time;
        imagePath = data.image;
        imagePath = imagePath.replace('img/', '');
        planet_img.src = "http://localhost:8000/api/planetImg/" + imagePath;

    })
    .catch(error => {
        console.error(error)
        sessionStorage.setItem('message', "An error occurred while loading the planet");
        window.location.href = 'planet.html';
    });

edit_planet_form.addEventListener('submit', function(e){
    e.preventDefault();

    // Création de l'objet FormData
    let formData = new FormData(edit_planet_form);

    // Récupération du fichier
    formData.append(fr_name, fr_name.value);
    formData.append(en_name, en_name.value);
    formData.append(fr_description, fr_description.value);
    formData.append(en_description, en_description.value);
    formData.append(distance, distance.value);
    formData.append(time, time.value);

    if(image.files.length > 0){
        let file = image.files[0];
        // Vérification si le fichier est une image
        if (file.type.match('image.*')) {
             // Ajout du fichier à l'objet FormData
            formData.append('image', file);
        }
       
    }

    // Configuration et envoi de la requête
    fetch(`http://localhost:8000/api/admin/planet/${id}/edit`, {
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
        sessionStorage.setItem('message', "Planet edited successfully");
        window.location.href = 'planet.html';
    })
    .catch(error => {
        console.error(error);
        sessionStorage.setItem('message', "A problem occurred while editing the planet");
        window.location.href = 'planet.html';
    });
});