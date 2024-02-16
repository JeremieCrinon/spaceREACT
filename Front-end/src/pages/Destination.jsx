import React from 'react';
import { useEffect, useState } from 'react';
import './Destination.css';
import { useTranslation } from 'react-i18next';
import '../i18n';

function DestinationContentNormal({ destination, menuData, changeDestination }){
    document.body.className = 'Destination--body';
    document.documentElement.className = 'Home--html';

    const { t, i18n } = useTranslation();

    let imagePath = destination.image;
    imagePath = imagePath.replace('img/', '');
    let Destination_img_src = "http://localhost:8000/api/planetImg/" + imagePath;

    return (
        <>
            <img className="Destination--img" id="Destination_img" src={Destination_img_src} />

            <section className="Destination--text">
                <nav id="Destination_nav" className="Destination--nav">
                    {menuData.map((planet) => (
                        <a href="#" onClick={() => changeDestination(planet.id)} className={`Destination--nav__link ${destination.id === planet.id ? 'Destination--nav__link__current' : ''}`}>{i18n.language === 'fr' ? planet.fr_name : planet.en_name}</a>
                    ))}
                    {/* <a href="{{ url('/destination/moon') }}" class="Destination--nav__link Destination--nav__link__current">Moon</a>
                    <a href="{{ url('/destination/mars') }}" class="Destination--nav__link">Mars</a>
                    <a href="{{ url('/destination/europe') }}" class="Destination--nav__link">Europe</a>
                    <a href="{{ url('/destination/titan') }}" class="Destination--nav__link">Titan</a> */}
                </nav>

                <h2 id="Destination_text_title" className="Destination--text__title">{ i18n.language === 'fr' ? destination.fr_name : destination.en_name }</h2>
                <p id="Destination_text_subtitle" className="Destination--text__p">{ i18n.language === 'fr' ? destination.fr_description : destination.en_description }</p>
                <div className="Destination--text--bar"></div>
                <div className="Destination--text--infos">
                    <div className="Destination--text--distance Destination--text--infos--container">
                        <p className="Destination--text--distance__title Destination--text--infos--title">{t('destination.distance')}</p>
                        <p id="Destination_text_distance" className="Destination--text--distance__p Destination--text--infos--p">{destination.distance}</p>
                    </div>
                    <div className="Destination--text--time Destination--text--infos--container">
                        <p className="Destination--text--time__title Destination--text--infos--title">{t('destination.duration')}</p>
                        <p id="Destination_text_time" className="Destination--text--time__p Destination--text--infos--p">{destination.time}</p>
                    </div>
                </div>
            </section>
        </>
    )
}

function DestinationContentError(){
    return (
        <h2 class="Destination--error">Error 500 : Internal server error, please try again later!</h2>
    )
}

export default function Destination() {
    const { t, i18n } = useTranslation();

    const [menuData, setMenuData] = useState(null);

    const [destination, setDestination] = useState(null);

    useEffect(() => {
        fetch('http://localhost:8000/api/planets')
          .then(response => {
            if (!response.ok) {
              throw new Error('Erreur réseau');
            }
            return response.json();
          })
          .then(data => {
            setMenuData(data);
            changeDestination(data[0]['id']);
        })
          .catch(error => {
            console.error("Erreur lors de la récupération des données:", error)
          });
      }, []);

    function changeDestination(destinationId) {
        fetch(`http://localhost:8000/api/planet/${destinationId}`)
          .then(response => {
            if (!response.ok) {
              throw new Error('Erreur réseau');
            }
            return response.json();
          })
          .then(data => {
            setDestination(data);
          })
          .catch(error => {
            console.error("Erreur lors de la récupération des données:", error)
          });
    }

    return (
        <>
            <h1 className="Destination--title"><span>01</span>{t('destination.title')}</h1>

            <div id="Destination_content">
                
                {destination ? <DestinationContentNormal destination={destination} menuData={menuData} changeDestination={changeDestination} /> : <DestinationContentError />}
                
            </div>
        </>
    )
}