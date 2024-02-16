import React from 'react';
import { useEffect, useState } from 'react';
import './Destination.css'; //J'importe le css de destination, car quand j'avais fais le site de base, j'avais utilisé des éléments de destination pour la page crew, et j'ai pas envie de m'embeter à les retrouver pour les mettre dans le ficher crew.css
import './Crew.css'; //J'import aussi le css de crew pour les mêmes raisons
import './Tech.css'
import { useTranslation } from 'react-i18next';
import '../i18n';

function TechContentNormal({ tech, menuData, changeTech }){
    document.body.className = 'Tech--body';
    document.documentElement.className = 'Home--html';

    const { t, i18n } = useTranslation();

    let imagePath = tech.image;
    imagePath = imagePath.replace('img/', '');
    let Tech_img_src = "http://localhost:8000/api/techImg/" + imagePath;

    return (
        <>
            <section class="Tech--text">
            
                <nav class="Tech--text--nav">
                    <ul id="tech_menu">
                        {menuData.map((planet, index) => (
                            <li><a href="#" onClick={() => changeTech(planet.id)} className={`Tech--text--nav--link ${tech.id === planet.id ? 'Tech--text--nav--link--current' : ''}`}>{index + 1}</a></li>
                        ))}
                        {/* <li><a class="Tech--text--nav--link Tech--text--nav--link--current" href="{{ url('/tech/launcher') }}">1</a></li>
                        <li><a class="Tech--text--nav--link" href="{{ url('/tech/spaceport') }}">2</a></li>
                        <li><a class="Tech--text--nav--link" href="{{ url('/tech/space_capsule') }}">3</a></li> */}
                    </ul>
                </nav>
                <div class="Tech--text--infos">
                    <h2 class="Tech--text--title">{t('tech.text.title')}</h2>
                    <h3 id="tech_name" class="Tech--text--subtitle">{ i18n.language === 'fr' ? tech.fr_name : tech.en_name }</h3>
                    <p id="tech_description" class="Tech--text--description">{ i18n.language === 'fr' ? tech.fr_description : tech.en_description }</p>
                </div>
                
            </section>
            <section class="Tech--img">
            
                <img id="Tech_img" class={"Tech--img--image"} src={Tech_img_src} alt={ i18n.language === 'fr' ? tech.fr_name : tech.en_name } />

            </section>
        </>
    )
}

function TechContentError(){
    return (
        <h2 className="Destination--error">Error 500 : Internal server error, please try again later!</h2>
    )
}

export default function Tech() {
    const { t, i18n } = useTranslation();

    const [menuData, setMenuData] = useState(null);

    const [tech, setTech] = useState(null);

    useEffect(() => {
        fetch('http://localhost:8000/api/teches')
          .then(response => {
            if (!response.ok) {
              throw new Error('Erreur réseau');
            }
            return response.json();
          })
          .then(data => {
            setMenuData(data);
            changeTech(data[0]['id']);
        })
          .catch(error => {
            console.error("Erreur lors de la récupération des données:", error)
          });
      }, []);

    function changeTech(techId) {
        fetch(`http://localhost:8000/api/tech/${techId}`)
          .then(response => {
            if (!response.ok) {
              throw new Error('Erreur réseau');
            }
            return response.json();
          })
          .then(data => {
            setTech(data);
          })
          .catch(error => {
            console.error("Erreur lors de la récupération des données:", error)
          });
    }

    return (
        <>

            <section id="tech_content">
                <h1 className="Destination--title"><span>03</span>{t('tech.title')}</h1>
                {tech ? <TechContentNormal tech={tech} menuData={menuData} changeTech={changeTech} /> : <TechContentError />}
            </section>    

        </>
    )
}