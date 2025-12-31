let currentStep = 1;

function nextStep() {
  if (currentStep === 2) {
    let terms = $("select[name='payment_terms_id']").val();
    if (terms === "" || terms === null) {
      Swal.fire({
        title: "Warning!",
        text: "Please select payment terms before proceeding.",
        icon: "warning",
      });
      return;
    }

    GetApprovalWorkflows();
  }
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

function GetApprovalWorkflows() {
  var dto = {
    amount: unformatMoneyValue($(".text-end h5 strong").text()),
    moduleCode: "PO",
  };
  $.ajax({
    type: "POST",
    url: BASE_URL + "approvalWorkflow/GetApprovalWorkflow",
    contentType: "application/json",
    data: JSON.stringify(dto),
    dataType: "json",
    success: function (response) {
      var approvalHtml = ``;
      $(".table-approval-po tbody").empty();
      if (response.status == 200) {
        response.result.forEach((element) => {
          approvalHtml += `<tr data-approval-level ="${element.approvalLevel}" data-approval-id="${element.userId}">
                                <td>${element.approvalLevel}</td>
                                <td>${element.roleName}</td>
                                <td>${element.approverName}</td>
                            </tr>`;
        });

        $(".table-approval-po tbody").append(approvalHtml);
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

function SubmitPurchaseOrder() {
  let poId = parseInt($("input[name='po_number']").data("id"));
  let poNumber = $("input[name='po_number']").val();
  let paymentTermsId = $("select[name='payment_terms_id']").val();
  let notes = $("textarea[name='notes']").val();
  let receivedBy = $("input[name='received_by']").val();
  let approvalPo = GetApprovalPoPreview();

  var dto = {
    poId: poId,
    poNumber: poNumber,
    paymentTermsId: paymentTermsId,
    notes: notes,
    receivedBy: receivedBy,
    maxApprovalLevel: approvalPo.length,
    approvalPo: approvalPo,
  };

  $.ajax({
    type: "POST",
    url: BASE_URL + "purchaseOrders/SubmitPurchaseOrder",
    contentType: "application/json",
    data: JSON.stringify(dto),
    dataType: "json",
    success: function (response) {
      if (response.status == 200) {
        Swal.fire({
          title: "Success!",
          text: response.message,
          icon: "success",
        }).then(() => {
          window.location.href = BASE_URL + "purchaseOrders";
        });
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

function GetApprovalPoPreview() {
  let approvalPo = [];

  $(".table-approval-po tbody tr").each(function () {
    let approvalLevel = $(this).data("approval-level");
    let approverId = $(this).data("approval-id");
    approvalPo.push({
      level: approvalLevel,
      approverId: approverId,
    });
  });
  return approvalPo;
}
