(() => {
 const bind = () => document.querySelectorAll('.avela-menu-toggle').forEach(button => {
  if (button.dataset.avelaBound) return;
  const nav = document.getElementById(button.getAttribute('aria-controls'));
  if (!nav) return;
  button.dataset.avelaBound = 'true';
  const close = () => {button.setAttribute('aria-expanded', 'false'); nav.classList.remove('is-open');};
  button.addEventListener('click', () => {const open = button.getAttribute('aria-expanded') !== 'true';button.setAttribute('aria-expanded', String(open));nav.classList.toggle('is-open', open);});
  document.addEventListener('keydown', e => {if(e.key === 'Escape' && button.getAttribute('aria-expanded') === 'true'){close();button.focus();}});
  document.addEventListener('click', e => {if(!nav.contains(e.target) && !button.contains(e.target)) close();});
  nav.addEventListener('click', e => {if(e.target.closest('a')) close();});
  window.matchMedia('(min-width: 801px)').addEventListener('change', close);
 });
 bind();
 let hooked = false;
 const hookElementor = () => {if (!hooked && window.elementorFrontend && window.elementorFrontend.hooks) {hooked = true; window.elementorFrontend.hooks.addAction('frontend/element_ready/avela-navigation.default', bind);}};
 window.addEventListener('elementor/frontend/init', hookElementor);
 if (window.jQuery) window.jQuery(window).on('elementor/frontend/init', hookElementor);
 hookElementor();
})();
