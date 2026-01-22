function dos() {
  // Invoice Summary
  $("#invoice_id").val(data.invoice_id);
  $("#invoice_number").val(data.invoiceNumber);
  $("#vendor_name").val(data.vendorName);
  $("#due_date").val(data.dueDate);
  $("#total_amount").val(formatMoney(data.totalAmount));
  $("#paid_amount_summary").val(formatMoney(data.paidAmount));
  $("#remaining_amount").val(formatMoney(data.remainingAmount));
  $("#bank_name").val(data.bankName);
  $("#account_number").val(data.accountNumber);
  $("#account_name").val(data.accountName);
}

function SubmitPayment() {
  var formData = new FormData();

  formData.append("invoice_id", $("#invoice_id").val());
  formData.append("payment_date", $("input[name='payment_date']").val());
  formData.append("paid_amount", $("#paid_amount").val());
  formData.append("payment_method", $("select[name='payment_method']").val());
  formData.append(
    "reference_number",
    $("input[name='reference_number']").val(),
  );
  formData.append("notes", $("textarea[name='notes']").val());

  var fileInput = document.querySelector("input[name='payment_proof']");
  if (fileInput.files.length > 0) {
    formData.append("payment_proof", fileInput.files[0]);
  }

  console.log(formData);
  showLoading();

  $.ajax({
    type: "POST",
    url: BASE_URL + "Payment/SubmitPayment",
    data: formData,
    processData: false,
    contentType: false,
    dataType: "json",
    success: function (response) {
      hideLoading();
      Swal.fire("Success", response.result, "success").then(
        () => (window.location = BASE_URL + "Payment"),
      );
    },
    error: function (err) {
      hideLoading();
      Swal.fire("Failed", err.responseJSON.message, "error");
    },
  });
}
