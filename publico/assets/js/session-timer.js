// session-timer.js — countdown + heartbeat para auto-logout tras 15 min de inactividad.
(function () {
  'use strict';

  var host = document.querySelector('.app[data-session-heartbeat]');
  if (!host) return;

  var HEARTBEAT_URL = host.getAttribute('data-session-heartbeat');
  var TIMEOUT = parseInt(host.getAttribute('data-session-timeout') || '900', 10);
  var INITIAL = parseInt(host.getAttribute('data-session-seconds-left') || '0', 10);
  var textEl = document.querySelector('[data-session-timer-text]');
  var timerEl = document.querySelector('[data-session-timer]');

  var secondsLeft = isFinite(INITIAL) ? INITIAL : TIMEOUT;
  var lastBeatAt = Date.now();
  var WARN_AT = 120; // los ultimos 2 min se ponen en estado "warn"

  function format(s) {
    var m = Math.floor(s / 60);
    var sec = s % 60;
    return (m < 10 ? '0' : '') + m + ':' + (sec < 10 ? '0' : '') + sec;
  }

  function render() {
    if (!textEl) return;
    textEl.textContent = format(Math.max(0, secondsLeft));
    if (timerEl) {
      timerEl.classList.toggle('is-warn', secondsLeft <= WARN_AT && secondsLeft > 30);
      timerEl.classList.toggle('is-danger', secondsLeft <= 30);
    }
  }

  function tick() {
    var elapsed = Math.floor((Date.now() - lastBeatAt) / 1000);
    secondsLeft = Math.max(0, secondsLeft - 1);
    render();
    if (secondsLeft <= 0) {
      window.location.href = (typeof url === 'function' ? url('logout') : '?route=logout');
    }
  }

  function beat() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', HEARTBEAT_URL, true);
    xhr.timeout = 5000;
    xhr.onload = function () {
      try {
        var data = JSON.parse(xhr.responseText);
        if (data && data.expired) {
          window.location.href = (typeof url === 'function' ? url('logout') : '?route=logout');
          return;
        }
        if (data && typeof data.seconds_left === 'number') {
          secondsLeft = data.seconds_left;
          lastBeatAt = Date.now();
          render();
        }
      } catch (e) { /* ignore */ }
    };
    xhr.onerror = function () { /* keep ticking */ };
    xhr.send();
  }

  // 1s tick visual + heartbeat al servidor cada 60s para re-sincronizar
  setInterval(tick, 1000);
  setInterval(beat, 60 * 1000);
  render();

  // Eventos que cuentan como actividad: cualquier interaccion del usuario
  var activityEvents = ['mousedown', 'keydown', 'scroll', 'touchstart', 'mousemove'];
  var lastActivitySync = 0;
  function onActivity() {
    var now = Date.now();
    // throttle: como mucho una vez cada 30s
    if (now - lastActivitySync < 30 * 1000) return;
    lastActivitySync = now;
    beat();
  }
  activityEvents.forEach(function (ev) {
    window.addEventListener(ev, onActivity, { passive: true });
  });
})();
