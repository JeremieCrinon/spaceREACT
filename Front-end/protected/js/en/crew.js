const planet_container = document.getElementById('planet_container');
const create_message = document.getElementById('create_message');

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

if (sessionStorage.getItem('message') !== null && typeof(sessionStorage.getItem('message')) !== "undefined" ) {
    create_message.textContent = sessionStorage.getItem('message');
    sessionStorage.setItem('message', '');
}

fetch('http://localhost:8000/api/admin/crews', {
    method: 'GET', // Méthode HTTP
    headers: {
        'Content-Type': 'application/json',
        'Authorization': getCookieValue('token'),
    }
})
.then(response => {
    if (!response.ok) {
        throw new Error('Réponse réseau non OK');
    }
    return response.json();
})
.then(data => {
    data.forEach(item => {
        console.log(item); // Traitez chaque élément ici
        let li = document.createElement('li');
        li.classList.add('Edit--planets--container--element');
        
        let pId = document.createElement('p');
        pId.classList.add('Edit--planets--container--element--element');
        pId.classList.add('Edit--planets--container--element--id');
        pId.textContent = item.id;

        let pFrName = document.createElement('p');
        pFrName.classList.add('Edit--planets--container--element--element');
        pFrName.classList.add('Edit--planets--container--element--fr_role');
        pFrName.textContent = item.fr_role;

        let pEnName = document.createElement('p');
        pEnName.classList.add('Edit--planets--container--element--element');
        pEnName.classList.add('Edit--planets--container--element--en_role');
        pEnName.textContent = item.en_role;

        let pFrDescription = document.createElement('p');
        pFrDescription.classList.add('Edit--planets--container--element--element');
        pFrDescription.classList.add('Edit--planets--container--element--fr_description');
        pFrDescription.textContent = item.fr_description;

        let pEnDescription = document.createElement('p');
        pEnDescription.classList.add('Edit--planets--container--element--element');
        pEnDescription.classList.add('Edit--planets--container--element--en_description');
        pEnDescription.textContent = item.en_description;

        let pDistance = document.createElement('p');
        pDistance.classList.add('Edit--planets--container--element--element');
        pDistance.classList.add('Edit--planets--container--element--name');
        pDistance.textContent = item.name;

        let aShow = document.createElement('a');
        aShow.classList.add('Edit--planets--container--element--element');
        aShow.classList.add('Edit--planets--container--element--show');
        aShow.textContent = "Show";
        aShow.href = "#";

        aShow.addEventListener('click', function(){
            sessionStorage.setItem('planetToGo', item.id);
            window.location.href = '../../en/crew.html';
        })

        let aEdit = document.createElement('a');
        aEdit.classList.add('Edit--planets--container--element--element');
        aEdit.classList.add('Edit--planets--container--element--edit');
        aEdit.textContent = "Edit";
        aEdit.href = "edit_crew.html?id=" + item.id;

        let fDestroy = document.createElement('form');
        fDestroy.classList.add('Edit--planets--container--element--element');
        fDestroy.classList.add('Edit--planets--container--element--delete');
        fDestroy.method = "POST";

        let bDestroy = document.createElement('button');
        bDestroy.classList.add('Edit--planets--container--element--element');
        bDestroy.classList.add('Edit--planets--container--element--delete--button');
        bDestroy.textContent = "Del.";
        bDestroy.type = "submit";
        fDestroy.appendChild(bDestroy);
        bDestroy.addEventListener('click', function(e){
            e.preventDefault(); 
            fetch('http://localhost:8000/api/admin/crew/' + item.id + '/destroy', {
                method: 'GET',
                headers: {
                    'Authorization': getCookieValue('token'),
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Réponse serveur non OK');
                }
                return response.json();
            })
            .then(data => {
                console.log(data)
                create_message.textContent = "Crew deleted successfully";
                li.remove();
            })
            .catch(error => {
                create_message.textContent = "An error occurred while deleting the crew";
                console.error(error)
            });
        });

        li.appendChild(pId);
        li.appendChild(pFrName);
        li.appendChild(pEnName);
        li.appendChild(pDistance);
        li.appendChild(pFrDescription);
        li.appendChild(pEnDescription);
        li.appendChild(aShow);
        li.appendChild(aEdit);
        li.appendChild(fDestroy);

        planet_container.appendChild(li)
    });
})
.catch(error => {
    console.error('Il y a eu un problème avec l\'opération fetch: ', error);
});
