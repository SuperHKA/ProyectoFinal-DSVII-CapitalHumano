// nav.js — sidebar con submenus (dropdown), menu mobile, ARIA.
(function () {
  'use strict';

  function toggleDropdown(group) {
    var open = group.getAttribute('data-open') === 'true';
    // Cerrar todos los demas grupos antes de abrir este.
    document.querySelectorAll('.dropdown').forEach(function (g) {
      if (g !== group) {
        g.removeAttribute('data-open');
        var t = g.querySelector('.dropdown__toggle');
        if (t) t.setAttribute('aria-expanded', 'false');
      }
    });
    if (open) {
      group.removeAttribute('data-open');
      group.querySelector('.dropdown__toggle').setAttribute('aria-expanded', 'false');
    } else {
      group.setAttribute('data-open', 'true');
      group.querySelector('.dropdown__toggle').setAttribute('aria-expanded', 'true');
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    // Submenus
    document.querySelectorAll('.dropdown__toggle').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.preventDefault();
        toggleDropdown(btn.closest('.dropdown'));
      });
    });

    // Auto-abrir el grupo que contiene el item activo
    var active = document.querySelector('.dropdown__menu a[aria-current="page"]');
    if (active) {
      var group = active.closest('.dropdown');
      if (group) {
        group.setAttribute('data-open', 'true');
        group.querySelector('.dropdown__toggle').setAttribute('aria-expanded', 'true');
      }
    }

    // Mobile menu
    var toggle = document.querySelector('[data-menu-toggle]');
    var sidebar = document.querySelector('.sidebar');
    var backdrop = document.querySelector('.sidebar-backdrop');
    function setMobile(open) {
      if (!sidebar) return;
      sidebar.classList.toggle('is-open', open);
      if (backdrop) backdrop.setAttribute('data-open', open ? 'true' : 'false');
    }
    if (toggle) toggle.addEventListener('click', function () { setMobile(!sidebar.classList.contains('is-open')); });
    if (backdrop) backdrop.addEventListener('click', function () { setMobile(false); });

    // Escape cierra menu mobile + dropdowns
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        setMobile(false);
        document.querySelectorAll('.dropdown[data-open="true"]').forEach(function (g) {
          g.removeAttribute('data-open');
          var t = g.querySelector('.dropdown__toggle');
          if (t) t.setAttribute('aria-expanded', 'false');
        });
      }
    });
  });
})();
