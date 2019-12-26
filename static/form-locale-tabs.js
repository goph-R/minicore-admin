function adjustLocalizedRows(currentLocale) {
    const localizedRows = document.querySelectorAll('.row-localized');
    localizedRows.forEach(function (row) {
        const locale = row.getAttribute('data-locale');
        row.style.display = locale === currentLocale ? 'flex' : 'none';
    });
}
    
(function() {
    
    const localeTabs = document.querySelectorAll('.tab-locale');        
    localeTabs.forEach(function (tab) {
        const locale = tab.getAttribute('data-locale');
        tab.addEventListener('click', function() {
            adjustLocalizedRows(locale);
            localeTabs.forEach(function(tab) {
                tab.classList.remove('is-active');
            });
            tab.classList.add('is-active');
        });
    });
        
})();
