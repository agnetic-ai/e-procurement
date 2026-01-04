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

function submitApproval() {
  var form = $("#orderApprovalForm");
  var dto = {
    prNumber: form.find("label[name='prNumber']").text(),
    level: parseInt(form.find("label[name='currentLevel']").text()),
    approvalStatus: form.find("select[name='action_approval']").val(),
    remarks: form.find("textarea[name='remarks']").val(),
  };

  showLoading();
  $.ajax({
    type: "POST",
    url: BASE_URL + "ordersApproval/SubmitApproval",
    contentType: "application/json",
    data: JSON.stringify(dto),
    dataType: "json",
    success: function (response) {
      hideLoading();
      if (response.status == 201) {
        Swal.fire({
          title: "Success!",
          text: response.message,
          icon: "success",
        }).then(() => {
          window.location = BASE_URL + "ordersApproval/index";
        });
      }
    },
    error: function (err) {
      hideLoading();
      Swal.fire({
        title: "Failed!",
        text: err.responseJSON.message,
        icon: "error",
      });
    },
  });
}
