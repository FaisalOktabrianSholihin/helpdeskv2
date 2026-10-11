/* Dashboard interactions */
document.addEventListener('click', e => {
  const card = e.target.closest('#page-dashboard .kpi-card');
  if (card) {
    card.animate([{transform:'scale(1)'},{transform:'scale(.985)'},{transform:'scale(1)'}],{duration:180});
  }
});
