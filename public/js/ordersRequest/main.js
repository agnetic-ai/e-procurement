$(document).ready(function () {
  loadRequest();
});

function loadRequest() {
  $.ajax({
    type: "POST",
    url: BASE_URL + "ordersRequest/GetRequestList",
    data: $("#searchForm").serialize(),
    success: function (response) {
      const Header = $("#requestTableBody");
      let body = "";
      if (response.status == 200) {
        if ($.fn.DataTable.isDataTable("#requestTable")) {
          $("#requestTable").DataTable().destroy();
        }
        Header.empty();

        if (response.result && response.result.length > 0) {
          response.result.forEach((element) => {
            let badge = StatusHandler(element.statusCode);
            let approvalLevel = element.currentApprovalLevel + " / " + element.maxApprovalLevel;
            body += ` <tr>
                          <td class="fw-semibold">${element.prNumber}</td>
                          <td>${element.title}</td>
                          <td>${element.department}</td>
                          <td>${element.requestDate}</td>
                          <td class="text-end">Rp ${element.totalEstimated}</td>
                          <td class="text-center">
                            <span class="badge bg-info">${approvalLevel}</span>
                          </td>
                          <td><label class='status-badge ${badge}'>${element.statusName}</label></td>
                          <td style="white-space: nowrap;">
                            <div class="buttons">
                              <a href="${BASE_URL}ordersRequest/GetRequestDetail?prNumber=${element.prNumber}" class="btn btn-outline-primary btn-sm" title="View Detail">
                                <i data-feather="eye"></i>
                              </a>
                            </div>
                          </td>
                      </tr>`;
          });
        }

        Header.append(body);
        $("#requestTable").DataTable({
          ordering: false,
          language: {
            emptyTable: "No purchase requests found",
          },
        });
        feather.replace();
      }
    },
    error: function (err) {
      Swal.fire({
        title: "Error!",
        text: "Failed to load data. Please try again.",
        icon: "error",
      });
    },
  });
}
