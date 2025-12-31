$(document).ready(function () {
  loadApproval();
});

function loadApproval() {
  $.ajax({
    type: "POST",
    url: BASE_URL + "PurchaseOrders/GetPurchaseOrders",
    data: $("#searchForm").serialize(),
    success: function (response) {
      const Header = $("#poTableBody");
      let body = "";
      if (response.status == 200) {
        if ($.fn.DataTable.isDataTable("#poTable")) {
          $("#poTable").DataTable().destroy();
        }
        Header.empty();
        response.result.forEach((element) => {
          let badge = StatusHandler(element.statusCode);
          body += ` <tr>
                        <td class="fw-semibold">${element.poNumber}</td>
                        <td>${element.prNumber}</td>
                        <td>${element.companyName}</td>
                        <td>${element.department}</td>
                        <td>${element.totalAmount}</td>
                                        <td>${
                                          element.poDate == null
                                            ? "-"
                                            : element.poDate
                                        }</td>
                        <td>${
                          element.receivedBy ? element.receivedBy : "-"
                        }</td>
                         <td><label class='status-badge ${badge}'>${
            element.statusName
          }</label></td>
                                            <td style="white-space: nowrap;">
                                            <div class="buttons">
                                            
                                            <a href="${BASE_URL}PurchaseOrders/GetPurchaseOrderDetail?poNumber=${
            element.poNumber
          }" class="btn btn-outline-primary btn-sm">
                              <i data-feather="edit"></i>
                          </a>
                          </div>
                        </td>
                    </tr>`;
        });

        Header.append(body);
        feather.replace();
        $("#poTable").DataTable({ ordering: false });
      }
    },
    error: function (err) {
      alert("Error loading data");
    },
  });
}
