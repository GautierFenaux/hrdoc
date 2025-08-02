
const form = document.querySelector('form');
const select = document.querySelector('#teletravail_form_quotite') ? document.querySelector('#teletravail_form_quotite') : '';
const quotiteField = document.querySelector('.quotite-personnel');
const inputs = document.querySelectorAll('.cet-choice');
const deleteForm = document.querySelector('.form-delete');
const btnFinalValidation = document.querySelector('.final-validation');

window.addEventListener('DOMContentLoaded', () => {

  if (window.location.pathname.includes('/show')) {
    document.getElementById('rh-teletravail-form-button').remove();
  }

})

if (deleteForm != null) {
  deleteForm.addEventListener('click', () => {
    if (confirm('Êtes-vous sur de vouloir supprimer cet élément ?')) {
      deleteForm.submit();
    };
  })

}

const avis = document.querySelector('.drh-comment') ?
  document.querySelector('.drh-comment') :
  (document.querySelector('.manager-comment') ?
    document.querySelector('.manager-comment') :
    null);


// Permet d'afficher l'input de texte au clique du bouton radio défavorable
if (form != null) {

  form.addEventListener('click', () => {
    const desapprobationRadio = document.querySelector('#teletravail_form_avisManager_1') ?
      document.querySelector('#teletravail_form_avisManager_1') :
      (document.querySelector('#teletravail_form_avisDRH_1') ?
        document.querySelector('#teletravail_form_avisDRH_1') :
        null);


    if (desapprobationRadio != null) {
      if (desapprobationRadio.checked) {
        avis.style.opacity = 1;
        avis.classList.add('active');
      } else {
        avis.style.opacity = 0;
        avis.classList.remove('active');
      }
    }

  })


  // Check si les infos sont présentes avant validation pour les différents types de formulaire
  form.addEventListener('submit', function (e) {
    e.preventDefault();

    // TODO : Revoir cette solution (problème car j'utilise le même template que pour les autres formulaires, afficher la modale peut-être...)
    if (
      (window.location.pathname.includes('/cet/new') ||
        (window.location.pathname.includes('/cet') &&
          window.location.pathname.includes('/edit')) ||
        window.location.pathname.includes('/retour-sur-site')
        && inputs != null)) {

          form.submit();
          btnFinalValidation.disabled = true;

    } else if ((window.location.pathname).includes('/teletravailform')) {

      if (window.location.pathname.includes('/new')) {
        async function loadModule() {
          const counterObj = await import('./teletravail.js');
          if (counterObj.default.counter <= 0) {
            alert('Veuillez indiquer vos journées télétravaillées.');
          }
          else {
            form.submit();
            btnFinalValidation.disabled = true;
          }
        }
        loadModule()
      } else if (window.location.pathname.includes('/edit') && !(window.location.pathname.includes('/reopen')) ) {

        form.submit();

      } else if (window.location.pathname.includes('/reopen')) {
        async function loadModule() {

          const counterObj = await import('./teletravail.js');

          if (counterObj.default.counter <= 0) {
            alert('Veuillez indiquer vos journées télétravaillées.')
          } else {
            form.submit();
            btnFinalValidation.disabled = true;
          }

        }

        loadModule()
      }

    } else {
      form.submit();
      btnFinalValidation.disabled = true;
    }

  });

}


const checkInputs = () => {

  let checkInputs = [];

  for (let input of Array.prototype.slice.call(inputs)) {
    if (input.checked == false) {
      checkInputs.push(input)
    }
  }

  if (checkInputs.length >= 3) {
    alert('Veuillez cocher un type de demande')
    checkInputs = []
    return false
  }

  return true
}

select && select.addEventListener('change', () => {
  if (select.value == "Temps partiel") {
    quotiteField.classList.add('active');
  } else {
    quotiteField.classList.remove('active');
  }
})
