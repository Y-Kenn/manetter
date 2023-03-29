$(function(){

    $('.js-toggle_nav_trigger').on('click', function(){
        console.log('trigger');
        $(this).toggleClass('is-active');
        $('.js-toggle_nav_target').toggleClass('is-active');
    })
});