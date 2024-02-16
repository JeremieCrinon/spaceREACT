const Edit_profile_name = document.getElementById('Edit_profile_name');
const Edit_profile_email = document.getElementById('Edit_profile_email');
const Edit_profile_message = document.getElementById('Edit_profile_message');
const Edit_profile_error = document.getElementById('Edit_profile_error');
const Edit_profile_confirm = document.getElementById('Edit_profile_confirm');
const logout = document.getElementById('logout');
const Edit_password_confirm = document.getElementById('Edit_password_confirm');
const Edit_password_password = document.getElementById('Edit_password_password');
const Edit_password_new_passwd = document.getElementById('Edit_password_new_passwd');
const Edit_password_confirm_password = document.getElementById('Edit_password_confirm_password');


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

fetch('http://localhost:8000/api/user', {
    method: 'GET', // Spécifie la méthode GET
    headers: {
        'Content-Type': 'application/json', // Indique le type de contenu attendu
        'Authorization': getCookieValue('token'),
    }
})
.then(response => response.json()) // Convertit la réponse en JSON
.then(data => {
    console.log(data);
    Edit_profile_name.value = data.name;
    Edit_profile_email.value = data.email;
}) // Affiche les données dans la console
.catch(error => console.error('Erreur:', error)); // Gère les erreurs

Edit_profile_confirm.addEventListener('click', function(event){
    event.preventDefault();

    // Création d'un nouvel objet FormData
    let nameFormData = new FormData();
    let mailFormData = new FormData();

    // Ajout de paires clé/valeur à formData
    nameFormData.append('name', Edit_profile_name.value);
    mailFormData.append('email', Edit_profile_email.value);

    let error = false;

    fetch('http://localhost:8000/api/user/edit/name', {
        method: 'POST',
        headers: {
            'Authorization': getCookieValue('token'),
        },
        body: nameFormData,
    })
    .then(response => response.json())  // Traitement de la réponse
    .then(result => {
        console.log('Succès:', result);
    })
    .catch(error => {
        error = true;
        console.error('Erreur:', error);
    });


    fetch('http://localhost:8000/api/user/edit/mail', {
        method: 'POST',
        headers: {
            'Authorization': getCookieValue('token'),
        },
        body: mailFormData,
    })
    .then(response => response.json())  // Traitement de la réponse
    .then(result => {
        console.log('Succès:', result);
    })
    .catch(error => {
        error = true;
    });

    if(error){
        Edit_profile_error.textContent = "Il y à eu une erreur lors du changement des informations de profil, veuillez réessayer !";
        Edit_profile_message.textContent = "";
    } else {
        Edit_profile_message.textContent = "Les informations du profil ont bien été changés !";
        Edit_profile_error.textContent = "";
    }

})

logout.addEventListener('click', function(){
    document.cookie = `token=; max-age=7200; path=/`;
    window.location.href = '../../en/index.html';
})

Edit_password_confirm.addEventListener('click', function(event){
    event.preventDefault();

    // Création d'un nouvel objet FormData
    let formData = new FormData();

    if (Edit_password_new_passwd.value !== Edit_password_confirm_password.value){
        Edit_profile_error.textContent = "Les mots de passe ne correspondent pas !";
    } else {
        // Ajout de paires clé/valeur à formData
        formData.append('password', Edit_password_password.value);
        formData.append('new_passwd', Edit_password_new_passwd.value);
        formData.append('confirm_passwd', Edit_password_confirm_password.value)


        let error = false;

        fetch('http://localhost:8000/api/user/edit/password', {
            method: 'POST',
            headers: {
                'Authorization': getCookieValue('token'),
            },
            body: formData,
        })
        .then(response => {
            if (!response.ok) {
                Edit_profile_error.textContent = "Mot de passe incorecte !";
                Edit_profile_message.textContent = "";
                throw new Error('Mot de passe incorecte'); // ou un autre message d'erreur personnalisé
            }
            return response.json();
        })
        .then(result => {
            console.log('Succès:', result);
            Edit_profile_message.textContent = "Le mot de passe à bien été changé !";
            Edit_profile_error.textContent = "";
            Edit_password_password.value = "";
            Edit_password_new_passwd.value = "";
            Edit_password_confirm_password.value = "";
        })
        .catch(error => {
            if (error.message !== "Mot de passe incorecte"){
                console.error('Erreur:', error);
                Edit_profile_error.textContent = "Il y à eu une erreur lors du changement du mot de passe, veuillez réessayer !";
                Edit_profile_message.textContent = "";
            }
            
        });
    }
})
