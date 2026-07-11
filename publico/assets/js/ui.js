// ui.js — utilidades globales: password toggle, toasts, table search, modal, confirm.
(function () {
  'use strict';

  // Toast
  function toast(message) {
    var host = document.querySelector('.toast-host');
    if (!host) {
      host = document.createElement('div');
      host.className = 'toast-host';
      host.style.cssText = 'position:fixed;right:16px;bottom:16px;z-index:200;display:grid;gap:8px;';
      document.body.appendChild(host);
    }
    var el = document.createElement('div');
    el.className = 'card';
    el.style.cssText = 'padding:10px 14px;font-size:13px;box-shadow:var(--shadow-md);animation:toast-in .2s var(--ease);';
    el.textContent = message;
    host.appendChild(el);
    setTimeout(function () { el.style.opacity = '0'; el.style.transform = 'translateY(8px)'; el.style.transition = 'opacity .2s, transform .2s'; }, 2400);
    setTimeout(function () { el.remove(); }, 2700);
  }
  window.toast = toast;

  // Password toggle
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-password-toggle]');
    if (!btn) return;
    var input = btn.parentElement.querySelector('input');
    if (!input) return;
    var showing = input.type === 'text';
    input.type = showing ? 'password' : 'text';
    btn.setAttribute('aria-label', showing ? 'Mostrar contrasena' : 'Ocultar contrasena');
  });

  // Confirm dialog
  document.addEventListener('submit', function (e) {
    var form = e.target.closest('form[data-confirm]');
    if (!form) return;
    if (!window.confirm(form.getAttribute('data-confirm'))) e.preventDefault();
  });

  // Table search (filter client-side)
  document.addEventListener('input', function (e) {
    var input = e.target.closest('[data-table-search]');
    if (!input) return;
    var target = input.getAttribute('data-table-search');
    var rows = document.querySelectorAll('tbody[data-table="' + target + '"] tr');
    var q = input.value.toLowerCase().trim();
    rows.forEach(function (r) {
      r.hidden = q !== '' && r.textContent.toLowerCase().indexOf(q) === -1;
    });
  });

  // Modal helpers
  window.openModal = function (sel) {
    var m = typeof sel === 'string' ? document.querySelector(sel) : sel;
    if (m) m.setAttribute('data-open', 'true');
  };
  window.closeModal = function (sel) {
    var m = typeof sel === 'string' ? document.querySelector(sel) : sel;
    if (m) m.removeAttribute('data-open');
  };
  document.addEventListener('click', function (e) {
    if (e.target.closest('[data-modal-close]')) {
      var m = e.target.closest('.modal');
      if (m) m.removeAttribute('data-open');
    }
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal[data-open="true"]').forEach(function (m) { m.removeAttribute('data-open'); });
    }
  });
})();
