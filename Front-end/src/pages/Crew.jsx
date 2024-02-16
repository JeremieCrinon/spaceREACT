import React from 'react';
import { useEffect, useState } from 'react';
import './Destination.css'; //J'importe le css de destination, car quand j'avais fais le site de base, j'avais utilisé des éléments de destination pour la page crew, et j'ai pas envie de m'embeter à les retrouver pour les mettre dans le ficher crew.css
import './Crew.css';
import { useTranslation } from 'react-i18next';
import '../i18n';

function CrewContentNormal({ crew, menuData, changeCrew }){
    document.body.className = 'Crew--body';
    document.documentElement.className = 'Home--html';

    const { t, i18n } = useTranslation();

    let imagePath = crew.image;
    imagePath = imagePath.replace('img/', '');
    let Crew_img_src = "http://localhost:8000/api/crewImg/" + imagePath;

    return (
        <>
            <section className="Crew--image__mobile Crew--image">
        
                <img className="crew_image" src={Crew_img_src} alt={crew.name} />
                
            </section>
            <div className="Crew--bar__mobile"></div>
            <nav className="Crew--text--nav__mobile Crew--text--nav">
                <ul id="crew_menu1">
                    {menuData.map((planet) => (
                        <li><a href="#" onClick={() => changeCrew(planet.id)} className={`Crew--text--nav--link__mobile Crew--text--nav--link ${crew.id === planet.id ? 'Crew--text--nav--link--current__mobile Crew--text--nav--link Crew--text--nav--link--current' : ''}`}></a></li>
                    ))}
                    {/* <li><a class="Crew--text--nav--link__mobile Crew--text--nav--link" href="{{ url('/crew/commander') }}"></a></li>
                    <li><a class="Crew--text--nav--link__mobile Crew--text--nav--link--current__mobile Crew--text--nav--link Crew--text--nav--link--current" href="{{ url('/crew/mission_specialist') }}"></a></li>
                    <li><a class="Crew--text--nav--link__mobile Crew--text--nav--link" href="{{ url('/crew/pilot') }}"></a></li>
                    <li><a class="Crew--text--nav--link__mobile Crew--text--nav--link" href="{{ url('/crew/engineer') }}"></a></li> */}
                </ul>
            </nav>


            <div id="crew_content" className="Crew--container">
                <section className="Crew--text">
                    
                    <p className="Crew--text--role">{ i18n.language === 'fr' ? crew.fr_role : crew.en_role }</p>
                    <p className="Crew--text--name">{crew.name}</p>
                    <p className="Crew--text--description">{ i18n.language === 'fr' ? crew.fr_description : crew.en_description }</p>
                    <nav className="Crew--text--nav">
                        <ul id="crew_menu2">
                            {menuData.map((planet) => (
                                <li><a href="#" onClick={() => changeCrew(planet.id)} className={`Crew--text--nav--link ${crew.id === planet.id ? 'Crew--text--nav--link Crew--text--nav--link--current' : ''}`}></a></li>
                            ))}
                            {/* <li><a class="Crew--text--nav--link Crew--text--nav--link--current" href="{{ url('/crew/commander') }}"></a></li>
                            <li><a class="Crew--text--nav--link" href="{{ url('/crew/mission_specialist') }}"></a></li>
                            <li><a class="Crew--text--nav--link" href="{{ url('/crew/pilot') }}"></a></li>
                            <li><a class="Crew--text--nav--link" href="{{ url('/crew/engineer') }}"></a></li> */}
                        </ul>
                    </nav>
                
                </section>

                <section className="Crew--image">
                
                    <img className="crew_image" src={Crew_img_src} alt={crew.name} />
                
                </section>
            </div>
        </>
    )
}

function CrewContentError(){
    return (
        <h2 className="Destination--error">Error 500 : Internal server error, please try again later!</h2>
    )
}

export default function Crew() {
    const { t, i18n } = useTranslation();

    const [menuData, setMenuData] = useState(null);

    const [crew, setCrew] = useState(null);

    useEffect(() => {
        fetch('http://localhost:8000/api/crews')
          .then(response => {
            if (!response.ok) {
              throw new Error('Erreur réseau');
            }
            return response.json();
          })
          .then(data => {
            setMenuData(data);
            changeCrew(data[0]['id']);
        })
          .catch(error => {
            console.error("Erreur lors de la récupération des données:", error)
          });
      }, []);

    function changeCrew(crewId) {
        fetch(`http://localhost:8000/api/crew/${crewId}`)
          .then(response => {
            if (!response.ok) {
              throw new Error('Erreur réseau');
            }
            return response.json();
          })
          .then(data => {
            setCrew(data);
          })
          .catch(error => {
            console.error("Erreur lors de la récupération des données:", error)
          });
    }

    return (
        <>
            <h1 className="Destination--title"><span>02</span>{t('crew.title')}</h1>
                
            {crew ? <CrewContentNormal crew={crew} menuData={menuData} changeCrew={changeCrew} /> : <CrewContentError />}

        </>
    )
}