function VerifyInvoice() {
  ConfirmVerify();
}

function ConfirmVerify() {
  Swal.fire({
    title: "Verify Invoice?",
    text: "This invoice will be marked as VERIFIED and cannot be edited.",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#28a745",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Yes, Verify",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      SubmitVerifyInvoice();
    }
  });
}

function RejectInvoice() {
  Swal.fire({
    title: "Reject Invoice?",
    text: "Please provide a reason for rejecting this invoice.",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#dc3545",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Reject Invoice",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      // TODO: panggil API reject invoice
      SubmitRejectInvoice();
    }
  });
}

function SubmitVerifyInvoice() {
  var dto = {
    invoiceNumber: $("#inv_number").val(),
    verifiedBy: "",
    notes: $("#notes_verify").val(),
  };

  showLoading();
  $.ajax({
    type: "POST",
    url: BASE_URL + "InvoiceVerification/VerifyInvoice",
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
          window.location = BASE_URL + "InvoiceVerification";
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

function SubmitRejectInvoice() {
  var dto = {
    invoiceNumber: $("#inv_number").val(),
    userId: "",
    notes: $("#notes_verify").val(),
  };

  showLoading();
  $.ajax({
    type: "POST",
    url: BASE_URL + "InvoiceVerification/RejectInvoice",
    contentType: "application/json",
    data: JSON.stringify(dto),
    dataType: "json",
    success: function (response) {
      console.log(response);
      hideLoading();
      if (response.status == 201) {
        Swal.fire({
          title: "Success!",
          text: response.message,
          icon: "success",
        }).then(() => {
          window.location = BASE_URL + "InvoiceVerification";
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
