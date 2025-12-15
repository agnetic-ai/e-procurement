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

function SelectedProduct(params) {
  let productId = parseInt($(params).val());

  var dto = {
    productId: productId,
  };

  $.ajax({
    type: "POST",
    url: BASE_URL + "vendor/GetVendorProduct",
    contentType: "application/json",
    data: JSON.stringify(dto),
    dataType: "json",
    success: function (response) {
      let vendors = $('select[name="vendor_id"]');
      vendors
        .empty()
        .append(
          '<option value="" style="opacity: 0.5; !important">--Pilih Vendor--</option>'
        );

      if (response.status == 200) {
        response.result.forEach((element) => {
          let newOption = new Option(
            element.companyName,
            element.vendorId,
            false,
            false
          );
          vendors.append(newOption);
        });
        vendors.trigger("change");
      }
    },
    error: function (err) {
      Swal.fire({
        title: "Failed!",
        text: err.responseJSON.message,
        icon: "error",
      });
    },
  });
}
