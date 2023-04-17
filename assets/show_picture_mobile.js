let seeMoreButton = document.getElementById("see-more");
let pictureVideo = document.getElementById("picture-video");
const seeMoreLabel = document.querySelector("label[for='see-more']");

seeMoreButton.addEventListener("click", function () {
    pictureVideo.classList.toggle("hidden");
    if (seeMoreButton.checked) {
        seeMoreLabel.textContent = "Réduire";
    } else {
        seeMoreLabel.textContent = "Voir les médias";
    }
});