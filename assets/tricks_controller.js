let page = 1;
const loadMoreTricksBtn = document.querySelector('#loadMoreTricksBtn');
const trickContainer = document.querySelector("#trick-container");

if (loadMoreTricksBtn) {
    loadMoreTricksBtn.addEventListener("click", () => {
        page++;
        fetch(`/load-more-tricks/${page}`).then(response => response.json()).then(data => {
            trickContainer.insertAdjacentHTML('beforeend', data.html);
            if (page === data.pages) {
                loadMoreTricksBtn.style.display = 'none';
            }
        }).catch(error => console.log(error));
    });
}