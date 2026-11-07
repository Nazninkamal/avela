(() => {
 const button = document.querySelector('.avela-menu-toggle');
 const nav = document.getElementById('avela-navigation');
 if (!button || !nav) return;
 const close = () => { button.setAttribute('aria-expanded', 'false'); nav.classList.remove('is-open'); };
 button.addEventListener('click', () => { const open = button.getAttribute('aria-expanded') !== 'true'; button.setAttribute('aria-expanded', String(open)); nav.classList.toggle('is-open', open); });
 document.addEventListener('keydown', event => { if (event.key === 'Escape' && button.getAttribute('aria-expanded') === 'true') { close(); button.focus(); } });
 document.addEventListener('click', event => { if (!nav.contains(event.target) && !button.contains(event.target)) close(); });
 nav.addEventListener('click', event => { if (event.target.closest('a')) close(); });
 window.matchMedia('(min-width: 801px)').addEventListener('change', close);
})();
