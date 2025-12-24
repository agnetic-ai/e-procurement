let currentStep = 1;
let editRow = null;

function nextStep() {
  if (currentStep === 1) {
    let prTitle = $('input[name="title"]').val();
    let department = $('select[name="department_id"]').val();
    let requestDate = $('input[name="request_date"]').val();
    let itemCount = $(".table-preview tbody tr").length;

    if (!prTitle || prTitle.trim() === "") {
      Swal.fire("Warning!", "PR Title wajib diisi.", "warning");
      return;
    }

    if (!department) {
      Swal.fire("Warning!", "Department wajib dipilih.", "warning");
      return;
    }

    if (!requestDate) {
      Swal.fire("Warning!", "Request Date wajib diisi.", "warning");
      return;
    }

    if (itemCount < 1) {
      Swal.fire(
        "Warning!",
        "Minimal harus ada 1 item pada Preview Request.",
        "warning"
      );
      return;
    }
  }

  if (currentStep === 2) {
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

function SelectedProduct(params) {
  let productId = parseInt($(params).val());
  let unitPrice = $(params).find("option:selected").data("price");
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
        $('input[name="unit_price"]').val(unitPrice);
        $('input[name="estimated"]').val(0);
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

function calculateEstimatedPrice(params) {
  let quantity = parseInt($(params).val());
  let unitPrice = unformatMoneyValue($('input[name="unit_price"]').val());
  if (!quantity || isNaN(quantity) || quantity <= 0) {
    $('input[name="estimated"]').val(0);
    return;
  }

  if (!unitPrice || unitPrice <= 0) {
    alert("Please select a product first");
    $('input[name="qty"]').val("");
    $('input[name="estimated"]').val("");
    return;
  }

  var quantityNum = parseInt(quantity);
  var unitPriceNum = parseFloat(unitPrice);
  var total = quantityNum * unitPriceNum;
  $('input[name="estimated"]').val(formatCurrency(total));
}

function PreviewProduct() {
  let selectedProduct = $('select[name="product_id"] option:selected');
  let productId = parseInt(selectedProduct.val());
  let productText = selectedProduct.text();
  let productName = productText.split(" - ")[0];
  let vendorName = $('select[name="vendor_id"] option:selected');
  let vendorId = parseInt($('select[name="vendor_id"] option:selected').val());
  let unit = $('select[name="uof"]').val();
  let productDesc = $('textarea[name="product_desc"]').val();
  let quantity = parseFloat($('input[name="qty"]').val());
  let unitPrice = parseFloat(
    unformatMoneyValue($('input[name="unit_price"]').val())
  );

  if (!productId) {
    Swal.fire("Warning!", "Silakan pilih produk.", "warning");
    return;
  }

  if (vendorName.val() == null || vendorName.val() == "") {
    Swal.fire("Warning!", "Vendor wajib dipilih.", "warning");
    return;
  }

  if (!quantity || quantity <= 0) {
    Swal.fire("Warning!", "Quantity harus diisi dan lebih dari 0.", "warning");
    return;
  }

  if (!unit) {
    Swal.fire("Warning!", "UOF / Satuan wajib dipilih.", "warning");
    return;
  }

  if (!unitPrice || unitPrice <= 0) {
    Swal.fire(
      "Warning!",
      "Harga satuan harus diisi dan lebih dari 0.",
      "warning"
    );
    return;
  }

  let total = quantity * unitPrice;
  let isDuplicate = false;
  $(".table-preview tbody tr").each(function () {
    if (editRow && this === editRow) return;
    if (
      parseInt($(this).data("id")) === productId &&
      String($(this).data("vendor")) === String(vendorName.val())
    ) {
      isDuplicate = true;
      return false;
    }
  });

  if (isDuplicate) {
    Swal.fire(
      "Warning!",
      "Produk dengan vendor yang sama sudah ada.",
      "warning"
    );
    return;
  }

  let rowNumber = $(".table-preview tbody tr").length + 1;
  if (editRow) {
    $(editRow).data("id", productId);
    $(editRow).data("vendor", vendorName.val());

    $(editRow).html(`
    <td class="row-number"></td>
    <td><span name='vendor_id' hidden>${vendorId}</span>${productName} - ${vendorName.text()}</td>
    <td>${productDesc || "-"}</td>
    <td>${quantity}</td>
    <td>${unit}</td>
    <td>${formatCurrency(unitPrice)}</td>
    <td>${formatCurrency(total)}</td>
    <td>
      <button class="btn btn-sm btn-outline-primary me-1" onclick="editItem(this)">
        <i data-feather="edit"></i>
      </button>
      <button class="btn btn-sm btn-outline-danger" onclick="removeItem(this)">
        <i data-feather="trash-2"></i>
      </button>
    </td>
  `);

    editRow = null;
    updateRowNumber();
  } else {
    var tableHTML = `
    <tr 
      data-product-id="${productId}"
      data-vendor-id="${vendorId}"
      data-product-desc="${productDesc}"
      data-quantity="${quantity}"
      data-unit="${unit}"
      data-unit-price="${unitPrice}"
      data-subtotal="${total}"
    >
      <td class="row-number">${rowNumber}</td>
      <td><span name='vendor_id' hidden>${vendorId}</span>${productName}${
      vendorName.text() ? " - " + vendorName.text() : ""
    }</td>
      <td>${productDesc || "-"}</td>
      <td>${quantity}</td>
      <td>${unit}</td>
      <td>${formatCurrency(unitPrice)}</td>
      <td>${formatCurrency(total)}</td>
      <td>
        <button class="btn btn-sm btn-outline-primary me-1" onclick="editItem(this)">
          <i data-feather="edit"></i>
        </button>
        <button class="btn btn-sm btn-outline-danger" onclick="removeItem(this)">
          <i data-feather="trash-2"></i>
        </button>
      </td>
    </tr>
    `;
  }
  $(".table-preview tbody").append(tableHTML);
  feather.replace();
  ResetRequestForm();
  $(".text-end h5 strong").text(updateSubtotalTotal());
}

function ResetRequestForm() {
  $('select[name="product_id"]').val("").trigger("change");
  $('select[name="uof"]').val("").trigger("change");
  $('input[name="qty"]').val("");
  $('input[name="unit_price"]').val("");
  $('textarea[name="product_desc"]').val("");
}

function removeItem(button) {
  Swal.fire({
    title: "Hapus item?",
    text: "Data produk ini akan dihapus.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Ya, hapus",
    cancelButtonText: "Batal",
  }).then((result) => {
    if (result.isConfirmed) {
      $(button).closest("tr").remove();
      updateRowNumber();
      updateSubtotalTotal();
    }
  });
}

function updateRowNumber() {
  $(".table-preview tbody tr").each(function (index) {
    $(this)
      .find(".row-number")
      .text(index + 1);
  });
}

function editItem(button) {
  editRow = $(button).closest("tr")[0];

  let productId = $(editRow).data("id");
  let vendorId = $(editRow).data("vendor");

  $('select[name="product_id"]').val(productId).trigger("change");

  setTimeout(() => {
    $('select[name="vendor_id"]').val(vendorId).trigger("change");
  }, 300);

  $('textarea[name="product_desc"]').val($(editRow).find("td:eq(2)").text());
  $('input[name="qty"]').val($(editRow).find("td:eq(3)").text());
  $('select[name="uof"]')
    .val($(editRow).find("td:eq(4)").text())
    .trigger("change");

  let priceText = $(editRow).find("td:eq(5)").text();
  $('input[name="unit_price"]').val(unformatMoneyValue(priceText));
}

function updateSubtotalTotal() {
  let grandTotal = 0;
  $(".table-preview tbody tr").each(function () {
    let subtotalText = $(this).find("td:eq(6)").text();
    let subtotal = unformatMoneyValue(subtotalText) || 0;
    grandTotal += parseFloat(subtotal);
  });

  $(".text-end h5 strong").text(formatCurrency(grandTotal));
}

function GetApprovalWorkflows() {
  var dto = {
    amount: unformatMoneyValue($(".text-end h5 strong").text()),
  };
  $.ajax({
    type: "POST",
    url: BASE_URL + "approvalWorkflow/GetApprovalWorkflow",
    contentType: "application/json",
    data: JSON.stringify(dto),
    dataType: "json",
    success: function (response) {
      var approvalHtml = ``;
      $(".table-approval tbody").empty();
      if (response.status == 200) {
        response.result.forEach((element) => {
          approvalHtml += `<tr data-approval-level ="${element.approvalLevel}" data-approval-id="${element.userId}">
                                <td>${element.approvalLevel}</td>
                                <td>${element.roleName}</td>
                                <td>${element.approverName}</td>
                            </tr>`;
        });

        $(".table-approval tbody").append(approvalHtml);
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

function GetPreviewProduct() {
  let items = [];
  $(".table-preview tbody tr").each(function () {
    let obj = {
      productId: parseInt($(this).data("product-id")),
      vendorId: parseInt($(this).data("vendor-id")),
      productDesc: $(this).data("product-desc"),
      quantity: parseInt($(this).data("quantity")),
      unit: $(this).data("unit"),
      unitPrice: $(this).data("unit-price"),
      subtotal: $(this).data("subtotal"),
    };
    items.push(obj);
  });
  return items;
}

function GetApprovalPreview() {
  let approval = [];
  $(".table-approval tbody tr").each(function () {
    let obj = {
      level: parseInt($(this).data("approval-level")),
      approverId: parseInt($(this).data("approval-id")),
    };
    approval.push(obj);
  });
  return approval;
}

function SubmitPurchaseForm() {
  const purchaseForm = $("#purchaseForm");
  var dto = {
    title: purchaseForm.find("input[name='title']").val(),
    department: purchaseForm.find("select[name='department_id']").val(),
    requestedBy: "",
    requestDate: purchaseForm.find("input[name='request_date']").val(),
    budgetEstimate: unformatMoneyValue($(".text-end h5 strong").text()),
    maxApproval: parseInt(
      purchaseForm.find("input[name='max_approval']").val()
    ),
    notes: purchaseForm.find("textarea[name='notes']").val(),
    billingAddress: purchaseForm.find("textarea[name='billing_address']").val(),
    shippingAddress: purchaseForm
      .find("textarea[name='shipping_address']")
      .val(),
    productPreview: GetPreviewProduct(),
    approvalPreview: GetApprovalPreview(),
  };

  showLoading();
  $.ajax({
    type: "POST",
    url: BASE_URL + "purchase/SubmitPurchaseForm",
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
          window.location = BASE_URL + "vendor/index";
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
