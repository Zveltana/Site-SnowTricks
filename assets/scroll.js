$(window).scroll(function() {
    if ($(this).scrollTop() > 800) {
        $('#chevron').removeClass('hidden');
    } else {
        $('#chevron').addClass('hidden');
    }
});