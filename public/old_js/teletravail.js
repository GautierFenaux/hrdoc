const quotity = document.querySelector('#teletravail_form_quotite');
const teleworkingDaysInput = document.querySelectorAll('#teletravail_form_journeesTeletravaillees input');
let counter = 0;
let maxDays;
let quotityValue;
let counterToExport = {counter: 0}

if(window.location.pathname.includes('teletravailform/new') || window.location.pathname.includes('reopen')) {

const resetInputs = () => {
    Array.prototype.slice.call(teleworkingDaysInput).map(teleworkingDayInput => {
        teleworkingDayInput.checked = false ;
        counter = 0 ;
    })
}

quotity.addEventListener("change", () => {
    quotityValue = quotity.value;
    resetInputs();
});

document.querySelector('#teletravail_form_quotitePersonnel_1').addEventListener('click', () => {
    resetInputs();
});

document.querySelector('#teletravail_form_quotitePersonnel_0').addEventListener('click', () => {
    resetInputs();
});


Array.prototype.slice.call(teleworkingDaysInput).map(teleworkingDayInput => {
    if(teleworkingDayInput.checked) {
        counter++
        counterToExport['counter']++
    };

    teleworkingDayInput.addEventListener('click', () => {
        
        if(document.querySelector('#teletravail_form_quotitePersonnel_1').checked == false &&
        document.querySelector('#teletravail_form_quotitePersonnel_0').checked == false && 
        quotityValue  == 'Temps partiel') {
            alert('Veuilez indiquer une quotité') ;
            teleworkingDayInput.checked = false;
        }
        quotityValue  == 'Temps partiel' && document.querySelector('#teletravail_form_quotitePersonnel_0').checked ? maxDays = 2 :  
        quotityValue  == 'Temps partiel' && document.querySelector('#teletravail_form_quotitePersonnel_1').checked ?  maxDays = 1 : maxDays = 2 ;
        if (counter >= maxDays && teleworkingDayInput.checked == true) {
            alert('Votre nombre maximal de journée télétravailée est de ' + maxDays + '.');
            teleworkingDayInput.checked = false;
        } else if (counter < maxDays && teleworkingDayInput.checked == true) {
            counter++;
            counterToExport['counter']++
        } else if (counter <= maxDays && teleworkingDayInput.checked == false) {
            counter--;
            counterToExport['counter']--
        }
    })
})

}

export default counterToExport
