$(document).ready(function () {
  loadApproval();
});

function loadApproval() {
  $.ajax({
    type: "POST",
    url: BASE_URL + "ordersApproval/GetApprovalList",
    data: $("#searchForm").serialize(),
    success: function (response) {
      const Header = $("#approvalTableBody");
      let body = "";
      if (response.status == 200) {
        if ($.fn.DataTable.isDataTable("#approvalTable")) {
          $("#approvalTable").DataTable().destroy();
        }
        Header.empty();
        response.result.forEach((element) => {
          let badge = StatusHandler(element.statusCode);
          body += ` <tr>
                        <td class="fw-semibold">${element.prNumber}</td>
                        <td>${element.department}</td>
                        <td>${element.title}</td>
                        <td>${element.requestDate}</td>
                        <td>${element.requestedBy}</td>
                        <td><label class='status-badge ${badge}'>${element.statusName}</label></td>
                        <td style="white-space: nowrap;">
                        <div class="buttons">
                        
                           <a href="${BASE_URL}ordersApproval/GetApprovalDetail?prNumber=${element.prNumber}" class="btn btn-outline-primary btn-sm">
                              <i data-feather="edit"></i>
                          </a>
                          <a type="button" class="btn btn-outline-danger btn-sm" onclick="ConfirmDelete(${element.prNumber});">
                              <i data-feather="trash-2"></i>
                          </a>
                          </div>
                        </td>
                    </tr>`;
        });

        Header.append(body);
        feather.replace();
        $("#approvalTable").DataTable({ ordering: false });
      }
    },
    error: function (err) {
      alert("Error loading data");
    },
  });
}
