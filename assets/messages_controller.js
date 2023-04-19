let page = 1;
const trickId = document.querySelector('#loadMoreMessagesBtn').dataset.trickId;

console.log(document.querySelector('#loadMoreMessagesBtn').dataset.trickId)
const loadMoreMessagesBtn = document.querySelector('#loadMoreMessagesBtn');
const messageContainer = document.querySelector('#message-container');

if (loadMoreMessagesBtn) {
    loadMoreMessagesBtn.addEventListener('click', () => {
        page++;
        fetch(`/load-more-messages/${page}/${trickId}`).then(response => response.json()).then(data => {
            messageContainer.insertAdjacentHTML("beforeend", data.html);
            if (page >= data.pages) {
                loadMoreMessagesBtn.style.display = "none";
            }
        }).catch(error => console.log(error));
    });
}