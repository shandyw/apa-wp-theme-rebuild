import '../css/components/popups.css';
import { initPopups } from './modules/popups.js';

document.addEventListener('DOMContentLoaded', () => {
  initPopups(document);
});

window.apache2026InitPopups = initPopups;
