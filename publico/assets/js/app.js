document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.checks').forEach((group) => {
    group.addEventListener('change', () => group.classList.add('touched'));
  });

  const revealItems = document.querySelectorAll('.public-reveal');
  if (!revealItems.length) return;

  const showAll = () => revealItems.forEach((item) => item.classList.add('is-visible'));
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (reducedMotion || !('IntersectionObserver' in window)) {
    showAll();
    return;
  }

  const revealObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach((entry) => {
      if (!entry.isIntersecting) return;

      entry.target.classList.add('is-visible');
      observer.unobserve(entry.target);
    });
  }, {
    rootMargin: '0px 0px -8% 0px',
    threshold: 0.12,
  });

  revealItems.forEach((item) => revealObserver.observe(item));
});
