/* Master data menu enhancements */
document.addEventListener('focusin', e => {
  if (e.target.matches('#page-karyawan input, #page-divisi input')) {
    e.target.closest('.form-field, .search-box, .filter-box')?.classList.add('field-focus');
  }
});
