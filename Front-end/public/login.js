const login_form_message = document.getElementById('login_form_message');
const login_form = document.getElementById('login_form');

if(sessionStorage.getItem('message') !== null){
    login_form_message.textContent = sessionStorage.getItem('message');
    sessionStorage.removeItem('message');
}

login_form.addEventListener('submit', (e) => {
    e.preventDefault(); //Empêche le rechargement de la page

    // Récupération des données du formulaire
    const formData = new FormData(login_form);

    // Envoyer les données du formulaire à l'API
    fetch('http://localhost:8000/api/login', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Mot de passe incorecte'); // ou un autre message d'erreur personnalisé
        }
        return response.json()
    })
    .then(data => {
        console.log(data);
        
        document.cookie = `token=${data.token}; max-age=7200; path=/`;
        window.location.href = 'http://localhost/protected/en/index.html';
    })
    .catch(error => {
        console.error(error); // Gérer les erreurs éventuelles
        login_form_message.textContent = error;
    });
});