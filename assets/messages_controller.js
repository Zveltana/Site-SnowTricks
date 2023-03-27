let page = 1;
let trickId = window.location.pathname.split('/').pop();
const loadMoreMessagesBtn = document.querySelector('#loadMoreMessagesBtn');
const messageContainer = document.querySelector('#message-container');

if (loadMoreMessagesBtn) {
    loadMoreMessagesBtn.addEventListener('click', () => {
        page++;
        fetch(`/load-more-messages/${page}/${trickId}`)
            .then(response => {
                console.log(response);
                return response.json();
            })
            .then(data => {
                messageContainer.insertAdjacentHTML('beforeend', data.html);
                if (page >= data.pages) {
                    loadMoreMessagesBtn.style.display = 'none';
                }
            })
            .catch(error => console.log(error));
    });
}