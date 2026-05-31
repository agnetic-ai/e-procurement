$(document).ready(function () {
  feather.replace();
});

function ChoosePurchaseOrder() {
  let selOrder = $("select[name='selPurchaseOrder']").val();

  $.ajax({
    type: "GET",
    url: BASE_URL + "GoodsReceipts/GetDraftCreateGoodsRecipts",
    data: { poNumber: selOrder },
    dataType: "json",
    success: function (response) {
      let result = response.result;
      window.currentPurchaseOrderId = result.PoHeader.purchaseOrderId;
      SetHeaderPo(result);

      if (result.HistoryGr && result.HistoryGr.length > 0) {
        SetHistoryGr(result);
        $("#formHistory").removeClass("d-none");
      } else {
        $("#formHistory").addClass("d-none");
      }

      renderGrDetails(result);
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

function SetHeaderPo(params) {
  $("#txt_po").val(params.PoHeader.poNumber);
  $("#txt_pr").val(params.PoHeader.prNumber);
  $("#txt_dep").val(params.PoHeader.department);
  $("#po_date").val(params.PoHeader.poDate);
  $("#po_by").val(params.PoHeader.receivedBy);
}

function SetHistoryGr(params) {
  $("#smPo").text(params.PoHeader.poNumber);
  let body = "";
  let header = $("#bodyHistoryGr");
  header.empty();

  params.HistoryGr.forEach((element) => {
    let badge = StatusHandler(element.statusCode);
    body += `
      <tr>
        <td>${element.grNumber}</td>
        <td>${element.receiptDate}</td>
        <td>${element.totalItemsReceived}</td>
        <td><label class="status-badge ${badge}">${element.statusName}</label></td>
        <td>${element.receivedBy}</td>
      </tr>`;
  });

  header.append(body);
}

function renderGrDetails(params) {
  const $tbody = $("#goodsReceiptsDetailTable tbody");
  $tbody.empty();

  if (!params.GrDetail || params.GrDetail.length === 0) {
    $tbody.html(
      `<tr><td colspan="5" class="text-center">No items found.</td></tr>`,
    );
    return;
  }

  let rows = "";

  params.GrDetail.forEach((detail) => {
    let qtyOrdered = parseFloat(detail.quantityOrdered) || 0;
    let qtyReceivedBefore = parseFloat(detail.totalReceived) || 0;
    let remaining = parseFloat(detail.remainingQty) || 0;
    let isComplete = remaining <= 0;
    let isUnit = detail.unit === "Unit";

    let productNameCell = `<td>${detail.productName}</td>`;
    let unitCell = `<td><span class="unit-badge">${detail.unit}</span></td>`;
    let qtyOrderedCell = `<td>${qtyOrdered}</td>`;

    let qtyReceivedInput = `
  <input type="number"
      class="form-control received-qty"
      name="receivedQty[${detail.poDetailId}]"
      min="0"
      max="${remaining}"
      value="0"
      ${isComplete ? "readonly" : ""}>
    <small class="form-text mt-1 qty-info ${
      isComplete ? "text-success" : "text-danger"
    }"
      data-qty-order="${qtyOrdered}"
      data-qty-received="${qtyReceivedBefore}">
      ${
        isComplete
          ? "Complete: All items have been received."
          : `Remaining: ${remaining} of ${qtyOrdered}`
      }
  </small>`;

    let qtyReceivedCell = `<td>${qtyReceivedInput}</td>`;

    let serialInputs = "";

    if (isUnit) {
      if (isComplete) {
        serialInputs = `<span class="text-muted">Completed</span>`;
      } else {
        serialInputs = `
          <div class="serial-container" data-po-detail-id="${detail.poDetailId}">
            <small class="text-muted">Enter received quantity to generate serial numbers</small>
          </div>`;
      }
    } else {
      serialInputs = `<input type="text" class="form-control" value="N/A" readonly>`;
    }

    let serialCell = `<td>${serialInputs}</td>`;

    rows += `
      <tr data-id="${detail.poDetailId}" data-product-id="${detail.productId}">
        ${productNameCell}
        ${unitCell}
        ${qtyOrderedCell}
        ${qtyReceivedCell}
        ${serialCell}
      </tr>`;
  });

  $tbody.html(rows);
  feather.replace();
}

$(document).on("blur", ".received-qty", function () {
  const qty = parseInt($(this).val()) || 0;
  const max = parseInt($(this).attr("max")) || 0;

  if (qty > max) {
    $(this).val(max);
    return;
  }

  const $row = $(this).closest("tr");
  const poDetailId = $row.data("id");
  const $serialContainer = $row.find(".serial-container");

  if ($serialContainer.length === 0) return;

  $serialContainer.empty();

  if (qty <= 0) {
    $serialContainer.html(
      `<small class="text-muted">Enter received quantity to generate serial numbers</small>`,
    );
    return;
  }

  for (let i = 0; i < qty; i++) {
    $serialContainer.append(`
      <input type="text"
          class="form-control mb-2 serial-input"
          name="serialNumber[${poDetailId}][]"
          placeholder="Serial #${i + 1}"
          required>
    `);
  }
});

function SubmitGoodsReceipts() {
  try {
    const payload = buildSubmitPayload();
    showLoading();
    $.ajax({
      type: "POST",
      url: BASE_URL + "GoodsReceipts/SubmitGoodsReceipts",
      data: JSON.stringify(payload),
      contentType: "application/json",
      dataType: "json",
      success: function (response) {
        hideLoading();
        if (response.status == 201) {
          Swal.fire({
            title: "Success!",
            text: response.message,
            icon: "success",
          }).then(() => {
            window.location = BASE_URL + "GoodsReceipts";
          });
        }
      },
      error: function (err) {
        hideLoading();
        Swal.fire("Error", err.responseJSON.message, "error");
      },
    });
  } catch (e) {
    hideLoading();
    Swal.fire("Warning", e.message, "warning");
  }
}

function buildSubmitPayload() {
  const payload = {
    purchaseOrderId: null,
    notes: $("#gr_notes").val() || "",
    receivedQty: {},
    serialNumber: {},
    productMap: {},
  };

  payload.purchaseOrderId = window.currentPurchaseOrderId;
  if (!payload.purchaseOrderId) {
    throw new Error("Purchase Order ID not found.");
  }

  $("#goodsReceiptsDetailTable tbody tr").each(function () {
    const poDetailId = $(this).data("id");
    const qty = parseInt($(this).find(".received-qty").val()) || 0;

    if (qty <= 0) return;

    payload.receivedQty[poDetailId] = qty;

    const serialInputs = $(this).find(".serial-input");
    if (serialInputs.length > 0) {
      payload.serialNumber[poDetailId] = [];
      serialInputs.each(function () {
        const val = $(this).val().trim();
        if (!val) {
          throw new Error("Serial number wajib diisi.");
        }
        payload.serialNumber[poDetailId].push(val);
      });
    }

    // productMap wajib (diambil dari attribute data)
    const productId = $(this).data("product-id");
    if (!productId) {
      throw new Error(
        "Product ID tidak ditemukan untuk PO detail " + poDetailId,
      );
    }

    payload.productMap[poDetailId] = productId;
  });

  if (Object.keys(payload.receivedQty).length === 0) {
    throw new Error("Minimal satu item harus di-receive.");
  }

  return payload;
}
