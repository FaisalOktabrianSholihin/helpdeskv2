/* Settings / Roles */
document.addEventListener('click', e => {
  const btn = e.target.closest('#page-setting button');
  if (btn) btn.animate([{transform:'scale(1)'},{transform:'scale(.97)'},{transform:'scale(1)'}],{duration:160});
});
