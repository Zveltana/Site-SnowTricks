/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.scss in this case)
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';

let page = 1;
const loadMoreTricksBtn = document.querySelector('#loadMoreTricksBtn');
const trickContainer = document.querySelector('#trick-container');

loadMoreTricksBtn.addEventListener('click', () => {
    page++;
    fetch(`/load-more-tricks/${page}`)
        .then(response => response.json())
        .then(data => {
            trickContainer.insertAdjacentHTML('beforeend', data.html);
        })
        .catch(error => console.log(error));
});
