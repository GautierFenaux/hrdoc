

const avis = document.querySelector('.pattern') ? document.querySelector('.pattern') : null;
const reopen = document.querySelector('#astreinte_timeModification') ? document.querySelector('#astreinte_timeModification') : null;



if(reopen)
    reopen.addEventListener('click', () => {
    const valuedTimeWrapper = document.querySelector('.valued-time-wrapper');

    if (reopen.checked) {
        valuedTimeWrapper.style.display = 'none';
        avis.style.opacity = 1;
        avis.classList.add('active');
    } else {
        valuedTimeWrapper.style.display = 'flex';
        avis.style.opacity = 0;
        avis.classList.remove('active');
    }

    })





document.querySelector('form').addEventListener('click', () => {

    // Rajouter une classe générique pour mettre ça sur tous les formulaires
    const desapprobationRadio = document.querySelectorAll('.radio-wrapper input')[1]

    if (desapprobationRadio.checked) {
        avis.style.opacity = 1;
        avis.classList.add('active');
    } else {
        avis.style.opacity = 0;
        avis.classList.remove('active');
    }


})



