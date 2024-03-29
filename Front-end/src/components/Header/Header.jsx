import React from 'react';
import { useState } from 'react';
import './Header.css';
import { useTranslation } from 'react-i18next';
import '../../i18n';
import { Link, useNavigate } from "react-router-dom";
import { useLocation } from 'react-router-dom';
import { useEffect } from 'react';

export default function Header({ changeLanguage }) {
    // État pour suivre si la classe du hamburger est active
    const [isActive, setIsActive] = useState(false);
    const [firstPlanet, setFirstPlanet] = useState(false);
    const [firstCrew, setFirstCrew] = useState(false);
    const [firstTech, setFirstTech] = useState(false);

    const { t, i18n } = useTranslation();

    const location = useLocation();

    // Gestionnaire d'événements pour basculer l'état de l'hamburger
    const toggleBurgerClass = () => {
        setIsActive(!isActive);
    };

    // page = page.page;
            
    useEffect(() => {
        fetch('http://localhost:8000/api/planets')
          .then(response => {
            if (!response.ok) {
              throw new Error('Erreur réseau');
            }
            return response.json();
          })
          .then(data => {
            setFirstPlanet(data[0]);
            })
            .catch(error => {
                console.error("Erreur lors de la récupération des données:", error)
            });
        }, []);

    useEffect(() => {
        fetch('http://localhost:8000/api/crews')
            .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau');
            }
            return response.json();
            })
            .then(data => {
            setFirstCrew(data[0]);
        })
            .catch(error => {
            console.error("Erreur lors de la récupération des données:", error)
            });
        }, []);

    useEffect(() => {
        fetch('http://localhost:8000/api/teches')
            .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau');
            }
            return response.json();
            })
            .then(data => {
            setFirstTech(data[0]);
        })
            .catch(error => {
            console.error("Erreur lors de la récupération des données:", error)
            });
        }, []);

    return (
        <header className="Website--header">

            <div className="Website--header--logo">
                <svg width="48px" height="48px" viewBox="0 0 48 48" version="1.1"  xmlns="http://www.w3.org/2000/svg">
                    <path d="M24 0C10.7452 0 0 10.7452 0 24C0 37.2548 10.7452 48 24 48C37.2548 48 48 37.2548 48 24C48 10.7452 37.2548 0 24 0ZM24 0C24 0 22.9412 10.9412 16.9412 16.9412C10.9412 22.9412 0 24 0 24C0 24 10.9412 25.0588 16.9412 31.0588C22.9412 37.0588 24 48 24 48C24 48 25.0588 37.0588 31.0588 31.0588C37.0588 25.0588 48 24 48 24C48 24 37.0588 22.9412 31.0588 16.9412C25.0588 10.9412 24 0 24 0Z" id="Ellipse-Difference" fill="#FFFFFF" fillRule="evenodd" stroke="none" />
                </svg>
            </div>

            <div className="Website--header--bar"></div>

            <div className={`Website--header--menu--hamburger ${isActive ? 'Website--header--menu--hamburger--active' : ''}`} onClick={toggleBurgerClass}>
                    <div className="Website--header--menu--hamburger--line"></div>
                    <div className="Website--header--menu--hamburger--line"></div>
                    <div className="Website--header--menu--hamburger--line"></div>
                </div>

            <div className={`Website--header--menu ${isActive ? 'Website--header--menu--active' : ''}`}>
                <ul>
                    {/* <li className={page === 'home' ? 'Website--header--li--current' : ''}><Link to="/"><span>00</span> {t('header.home')}</Link></li> */}
                    <li className={location.pathname === '/' ? 'Website--header--li--current' : ''}><Link to="/"><span>00</span> {t('header.home')}</Link></li>
                    <li className={location.pathname === '/destination' ? 'Website--header--li--current' : ''}><Link to={`/destination?id=${firstPlanet.id}&planet_name=${i18n.language === 'fr' ? firstPlanet.fr_name : firstPlanet.en_name}`}><span>01</span> {t('header.destination')}</Link></li>
                    <li className={location.pathname === '/crew' ? 'Website--header--li--current' : ''}><Link to={`/crew?id=${firstCrew.id}&crew_name=${firstCrew.name}`}><span>02</span> {t('header.crew')}</Link></li>
                    <li className={location.pathname === '/tech' ? 'Website--header--li--current' : ''}><Link to={`/tech?id=${firstTech.id}&tech_name=${i18n.language === 'fr' ? firstTech.fr_name : firstTech.en_name}`}><span>03</span> {t('header.technology')}</Link></li>
                    <a className="Website--header--language" onClick={(e) => { e.preventDefault(); changeLanguage('en'); }} href="#">English</a>
                    <a className="Website--header--language" onClick={(e) => { e.preventDefault(); changeLanguage('fr'); }} href="#">Français</a>

                </ul>
            </div>    

             {/* Script js pour le header */}
            {/* <script src="../js/header.js"></script> */}
            
        </header>
    );
}