$(document).ready(function () {
  loadApproval();
});

function loadApproval() {
  $.ajax({
    type: "POST",
    url: BASE_URL + "ApprovalPo/GetApprovalPurchaseOrder",
    data: $("#searchForm").serialize(),
    success: function (response) {
      const Header = $("#poaTableBody");
      let body = "";
      if (response.status == 200) {
        if ($.fn.DataTable.isDataTable("#poaTable")) {
          $("#poaTable").DataTable().destroy();
        }
        Header.empty();
        response.result.forEach((element) => {
          let badge = StatusHandler(element.statusCode);
          body += `
                  <tr>
                      <td class="fw-semibold">${element.poNumber}</td>
                      <td>${element.prNumber}</td>
                      <td>${element.companyName}</td>
                      <td>${element.department}</td>
                      <td>${element.totalAmount}</td>
                      <td>${element.poDate ?? "-"}</td>
                      <td>${element.receivedBy ?? "-"}</td>
                      <td>
                          <label class="status-badge ${badge}">
                              ${element.statusName}
                          </label>
                      </td>
                      <td style="white-space: nowrap;">
                          <div class="buttons">
                              <a href="${BASE_URL}ApprovalPo/GetApprovalPoDetail?poNumber=${
                                element.poNumber
                              }"
                                class="btn btn-outline-primary btn-sm">
                                  ${
                                    element.statusCode === "PO_DRAFT"
                                      ? '<i data-feather="edit"></i>'
                                      : '<i data-feather="eye"></i>'
                                  }
                              </a>
                          </div>
                      </td>
                  </tr>`;
        });

        Header.append(body);
        feather.replace();
        $("#poaTable").DataTable({ ordering: false });
      }
    },
    error: function (err) {
      alert("Error loading data");
    },
  });
}
