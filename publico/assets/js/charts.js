// charts.js — helpers para Chart.js v4. Colores leidos de CSS vars (reaccionan al tema).
(function () {
  'use strict';
  if (typeof window.Chart === 'undefined') return;

  function cssVar(name) {
    return getComputedStyle(document.documentElement).getPropertyValue(name).trim();
  }

  function baseOptions() {
    return {
      responsive: true,
      maintainAspectRatio: false,
      animation: { duration: 250 },
      plugins: {
        legend: {
          position: 'bottom',
          labels: { color: cssVar('--chart-text'), font: { size: 12 }, boxWidth: 10, boxHeight: 10, padding: 12 }
        }
      }
    };
  }

  function doughnutSexo(canvas, data) {
    if (!canvas || !data) return null;
    return new Chart(canvas, {
      type: 'doughnut',
      data: {
        labels: data.map(function (d) { return d.sexo; }),
        datasets: [{
          data: data.map(function (d) { return parseInt(d.total, 10); }),
          backgroundColor: [cssVar('--chart-1'), cssVar('--chart-2'), cssVar('--chart-5')],
          borderColor: cssVar('--surface'),
          borderWidth: 2
        }]
      },
      options: Object.assign(baseOptions(), { cutout: '65%' })
    });
  }

  function barEdad(canvas, data) {
    if (!canvas || !data) return null;
    // Asegurar todos los buckets presentes aunque la query devuelva 0.
    var labels = ['20-25', '25-30', '30-35', 'mayor-35'];
    var byKey = {};
    data.forEach(function (d) { byKey[d.rango] = parseInt(d.total, 10); });
    return new Chart(canvas, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Colaboradores',
          data: labels.map(function (l) { return byKey[l] || 0; }),
          backgroundColor: cssVar('--chart-1'),
          borderRadius: 6,
          maxBarThickness: 40
        }]
      },
      options: Object.assign(baseOptions(), {
        plugins: Object.assign({}, baseOptions().plugins, { legend: { display: false } }),
        scales: {
          x: { ticks: { color: cssVar('--chart-text') }, grid: { display: false } },
          y: { beginAtZero: true, ticks: { color: cssVar('--chart-text'), precision: 0 }, grid: { color: cssVar('--chart-grid') } }
        }
      })
    });
  }

  window.charts = window.charts || {};
  window.charts.doughnutSexo = doughnutSexo;
  window.charts.barEdad = barEdad;
})();
