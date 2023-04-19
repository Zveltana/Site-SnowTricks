window.onscroll = function () {
    scrollFunction()
};

function scrollFunction() {
    if (document.body.scrollTop > 1500 || document.documentElement.scrollTop > 1500) {
        document.querySelector(".back-to-top").style.display = "block";
    } else {
        document.querySelector(".back-to-top").style.display = "hidden";
    }
}