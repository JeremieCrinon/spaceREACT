import React from 'react';
import { useState } from 'react';
import './Home.css';
import { useTranslation } from 'react-i18next';
import '../../i18n';

export default function Header() {
    const { t, i18n } = useTranslation();

    return (
        <>
            <section className="Home--title--container">
                <h1 className="Home--title">{t('home.title')}<br /><strong>{t('home.title.strong')}</strong></h1>
                <p className="Home--undertitle">{t('home.subtitle')}</p>
            </section>

            <section className="Home--main_button">
                <a href="#" className="Home--main_button--link">{t('home.button')}</a>
            </section>
        </>
    )
}