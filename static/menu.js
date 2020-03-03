(function() {

const burgerLink = document.getElementById('navbar_burger');
burgerLink.addEventListener('click', function() {
    const burger = document.querySelector('.burger');
    const nav = document.getElementById('navbar_menu');
    burger.classList.toggle('is-active');
    nav.classList.toggle('is-active');    
});

})();