let currentStep = 1;

function nextStep() {
  if (currentStep < 3) {
    document.getElementById(`step-${currentStep}`).classList.add("d-none");
    currentStep++;
    document.getElementById(`step-${currentStep}`).classList.remove("d-none");
    updateStepper();
  }
}

function prevStep() {
  if (currentStep > 1) {
    document.getElementById(`step-${currentStep}`).classList.add("d-none");
    currentStep--;
    document.getElementById(`step-${currentStep}`).classList.remove("d-none");
    updateStepper();
  }
}

function updateStepper() {
  document
    .querySelectorAll(".step")
    .forEach((step) => step.classList.remove("active"));
  document
    .querySelector(`.step[data-step="${currentStep}"]`)
    .classList.add("active");
}
