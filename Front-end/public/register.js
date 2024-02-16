const register_form = document.getElementById('register_form');
const register_form_message = document.getElementById('register_form_message');

register_form.addEventListener('submit', (e) => {
    e.preventDefault(); //Empêche le rechargement de la page

    // Récupération des données du formulaire
    const formData = new FormData(register_form);

    // Envoyer les données du formulaire à l'API
    fetch('http://localhost:8000/api/register', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        sessionStorage.setItem('message', 'Votre compte à bien été créé, veuillez vous connecter.');
        window.location.href = 'login.html';
    })
    .catch(error => {
        console.error(error); // Gérer les erreurs éventuelles
        register_form_message.textContent = error;
    });
});