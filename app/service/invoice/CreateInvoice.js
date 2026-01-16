function ChangePo(params) {
  let poNumber = $(params).val();
  let invoiceContent = $("#invoiceContent");
  let submitBtn = $("#submitBtn");

  if (!poNumber) {
    invoiceContent.addClass("d-none");
    submitBtn.prop("disabled", true);
    return;
  }
  GetDraftCreate(poNumber);
  invoiceContent.removeClass("d-none");
}

function GetDraftCreate(poNumber) {
  $.ajax({
    type: "GET",
    url: BASE_URL + "Invoice/GetDraftCreateInvoice",
    data: { poNumber: poNumber },
    dataType: "json",
    success: function (response) {
      let result = response.result;
      console.log(result);
      $("#poId").val(result.PoHeader.purchaseOrderId);
      SetPoSummary(result);
      SetInvoiceItems(result);
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

function SetPoSummary(result) {
  $("#po_number").val(result.PoHeader.poNumber);
  $("#vendor_name").val(result.PoHeader.companyName);
  $("#po_date").val(result.PoHeader.poDate);
  $("#po_amount").val(result.PoHeader.totalAmount);
  $("#payment_terms").val(result.PoHeader.paymentTerms);
}

function SetInvoiceItems(result) {
  let tbInv = $("#tableinvoiceBody");
  let body = ``;
  tbInv.empty();
  result.InvoiceItem.forEach((element) => {
    body += `<tr>
                 <td>${element.productName}</td>
                 <td>${element.orderedQty + " " + element.unit}</td>
                 <td>${element.receivedQty + " " + element.unit}</td>
                 <td>
                  <input type="number" min="0" onblur="ChangeToInvoice(this);"; class="form-control form-control-sm text-end price-input"
                            name="" value="${element.receivedQty}" required>
                            </td>
                 <td>${element.unitPrice}</td>
                 <td>${element.subtotal}</td>
             </tr>`;
  });
  tbInv.append(body);
}

function ChangeToInvoice(params) {
  let submitBtn = $("#submitBtn");
  let toInv = parseInt($(params).val());
  if (toInv > 0) {
    submitBtn.prop("disabled", false);
  }
}

function SubmitDraftInvoice() {
  let invNumber = $("#inv_number").val();
  let poId = parseInt($("#poId").val());
  let invoiceDate = $("#invoice_date").val();
  let dueDate = $("#due_date").val();
  let notes = $("#notes").val();
  var dto = {
    invoiceNumber: invNumber,
    poId: poId,
    invoiceDate: invoiceDate,
    dueDate: dueDate,
    createdBy: 0,
    notes: notes,
  };
  showLoading();
  $.ajax({
    type: "POST",
    url: BASE_URL + "invoice/SubmitDraftInvoice",
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
          window.location = BASE_URL + "invoice";
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
