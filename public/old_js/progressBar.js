const prevBtns = document.querySelector(".btn-prev");
const nextBtns = document.querySelector(".btn-next");
const formSteps = document.querySelectorAll(".form-step");
const signatureButton = document.querySelector('.validate-form.validation');

let formStepsNum = 0;

let regex = new RegExp("new", "i");

if (prevBtns) prevBtns.style.display = 'none'


nextBtns?.addEventListener("click", () => {
  formStepsNum++;
  updateFormSteps();
  if (formStepsNum == 1) {
    nextBtns.style.display = 'none';
    prevBtns.style.display = 'block';
    signatureButton.style.display = 'block';
  }
});

prevBtns?.addEventListener("click", () => {
  formStepsNum--;
  updateFormSteps();
  if (formStepsNum == 0) {
    nextBtns.style.display = 'block';
    prevBtns.style.display = 'none';
    signatureButton.style.display = 'none';
  }
});

function updateFormSteps() {

  formSteps.forEach((formStep) => {
    formStep.classList.contains("form-step-active") &&
      formStep.classList.remove("form-step-active");
  });

  formSteps[formStepsNum].classList.add("form-step-active");

}
