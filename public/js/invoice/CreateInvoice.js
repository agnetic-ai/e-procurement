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
      $("#poId").val(result.PoHeader.purchaseOrderId);
      $("#invoiceSummary").addClass("d-none");
      if (result.InvoiceSummary.length > 0) {
        SetInvoiceSummary(result);
        $("#invoiceSummary").removeClass("d-none");
      }
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
  let grandTotal = 0;

  result.InvoiceItem.forEach((element, index) => {
    let qty = parseInt(element.toInvoiceQty);
    let isComplete = qty <= 0;
    let unitPrice = unformatMoneyValue(element.unitPrice);
    let subTotal = unitPrice * qty;
    body += `<tr data-pod-id="${element.purchaseOrderDetailId}">
                 <td>${element.productName}</td>
                 <td>${element.orderedQty + " " + element.unit}</td>
                 <td>${element.receivedQty + " " + element.unit}</td>
                 <td>
                  <input type="number"
                    min="0"
                    max="${qty}"
                    onblur="ChangeToInvoice(this,${index});"
                    name="invQty"
                    class="form-control form-control-sm text-end qty-input_${index}"
                    value="${qty}"
                    ${isComplete ? "readonly" : ""}>
                    <small class="form-text mt-1 qty-info ${
                      isComplete ? "text-success" : "text-danger"
                    }">
                    ${
                      isComplete
                        ? "Complete: All items have been invoiced."
                        : `Remaining: ${qty} of ${element.receivedQty}`
                    }
                  </small>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm text-end unit_price_input_${index}"
                    name="unit_price" value="${element.unitPrice}" onblur="ChangeUnitPrice(this, ${index});">
                </td>
                 <td><span class="subtotal_${index}" name="subtotal">${formatCurrency(subTotal)}</span></td>
             </tr>`;
    grandTotal += unformatMoneyValue(subTotal);
  });
  tbInv.append(body);
  $("#grandTotals").text(formatCurrency(grandTotal));
}

function ChangeToInvoice(input, index) {
  const max = parseInt(input.getAttribute("max")) || 0;
  let value = parseInt(input.value) || 0;

  if (value > max) {
    input.value = max;
  }

  if (value < 0) {
    input.value = 0;
  }
  RecalculateSubTotal(index);
}
function ChangeUnitPrice(param, index) {
  let unitPrice = unformatMoneyValue($(param).val());

  if (unitPrice <= 0) {
    $(param).val(formatCurrency(0));
    return;
  }

  $(param).val(formatCurrency(unitPrice));
  let qty = parseInt($(`.qty-input_${index}`).val()) || 0;
  let subtotal = unitPrice * qty;
  $(`.subtotal_${index}`).text(formatCurrency(subtotal));
  RecalculateGrandTotal();
}

function RecalculateSubTotal(index) {
  let unitPrice = unformatMoneyValue($(`.unit_price_input_${index}`).val());
  let qty = parseInt($(`.qty-input_${index}`).val());
  let subtotal = unitPrice * qty;
  $(`.subtotal_${index}`).text(formatCurrency(subtotal));
  RecalculateGrandTotal();
}

function RecalculateGrandTotal() {
  let grandTotal = 0;

  $("#tableinvoiceBody tr").each(function () {
    let subtotalText = $(this).find("span[name='subtotal']").text();
    let subtotal = unformatMoneyValue(subtotalText);

    grandTotal += subtotal;
  });

  $("#grandTotals").text(formatCurrency(grandTotal));
}

function SubmitDraftInvoice() {
  let invNumber = $("#inv_number").val();
  let poId = parseInt($("#poId").val());
  let invoiceDate = $("#invoice_date").val();
  let dueDate = $("#due_date").val();
  let notes = $("#notes").val();

  let items = [];

  $("#tableinvoiceBody tr").each(function () {
    let podId = $(this).data("pod-id");
    let qty = parseInt($(this).find("input[name='invQty']").val()) || 0;
    let unitPrice = unformatMoneyValue(
      $(this).find("input[name='unit_price']").val(),
    );

    if (qty > 0) {
      items.push({
        purchaseOrderDetailId: podId,
        qty: qty,
        unitPrice: unitPrice,
      });
    }
  });

  var dto = {
    invoiceNumber: invNumber,
    poId: poId,
    invoiceDate: invoiceDate,
    dueDate: dueDate,
    createdBy: 0,
    notes: notes,
    items: items,
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
      hideLoading();
      Swal.fire({
        title: "Failed!",
        text: err.responseJSON.message,
        icon: "error",
      });
    },
  });
}

function SetInvoiceSummary(result) {
  let body = ``;
  result.InvoiceSummary.forEach((element) => {
    let badge = StatusHandler(element.statusCode);
    body += ` <tr>
                  <td>${element.invoiceNumber}</td>
                  <td>${element.invoiceDate}</td>
                  <td>${element.totalQty}</td>
                  <td>${element.totalAmount}</td>
                  <td><label class='status-badge ${badge}'>${
                    element.statusName
                  }</label></td>
              </tr>`;
  });
  $("#tableInvSummary").append(body);
}
