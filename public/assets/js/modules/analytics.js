/* Analytics menu */
document.addEventListener('click', e => {
  const page = e.target.closest('#page-analitik');
  if (page) document.documentElement.style.setProperty('--analytics-active','1');
});
