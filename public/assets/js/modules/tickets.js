/* Ticket menu micro-interactions */
document.addEventListener('click', e => {
  const action = e.target.closest('[data-open]');
  if (action) action.classList.add('row-opening');
});
