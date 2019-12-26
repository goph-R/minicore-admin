(function() {
    
    const messageDeleteButtons = document.querySelectorAll('.notification .delete');
    messageDeleteButtons.forEach(function (e) {
        e.addEventListener('click', function(event) {
            event.target.parentElement.style.display = 'none';
        });
    });
    
})();
