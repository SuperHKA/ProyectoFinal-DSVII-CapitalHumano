// theme.js — toggle claro/oscuro + persistencia en localStorage.
// El script inline en <head> (ver layouts/header.php) setea data-theme
// antes del primer paint para evitar FOUC. Este archivo solo maneja el
// boton de toggle.
(function () {
  'use strict';
  var KEY = 'ch-theme';
  function get() {
    try { return localStorage.getItem(KEY) || 'light'; } catch (e) { return 'light'; }
  }
  function set(value) {
    try { localStorage.setItem(KEY, value); } catch (e) {}
    document.documentElement.setAttribute('data-theme', value);
  }
  // Aplica al cargar (caso: navegacion con cache, sin script inline)
  set(get());

  document.addEventListener('DOMContentLoaded', function () {
    var btn = document.querySelector('[data-theme-toggle]');
    if (!btn) return;
    btn.addEventListener('click', function () {
      set(get() === 'dark' ? 'light' : 'dark');
    });
  });
})();
