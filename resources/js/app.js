import './bootstrap';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import '../css/pos-ui.css';

import Alpine from 'alpinejs';
import React from 'react';
import { createRoot } from 'react-dom/client';
import PosLogin from './components/PosLogin';

window.Alpine = Alpine;

Alpine.start();

const mountReactApp = (elementId, Component) => {
    const element = document.getElementById(elementId);

    if (!element) {
        return;
    }

    let props = {};

    try {
        props = element.dataset.props ? JSON.parse(element.dataset.props) : {};
    } catch (error) {
        console.error(`Unable to mount ${elementId}`, error);
    }

    createRoot(element).render(React.createElement(Component, props));
};

mountReactApp('pos-login-root', PosLogin);
