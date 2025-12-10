$(document).ready(function () {
  loadVendors();
});

function loadVendors() {
  $.ajax({
    type: "POST",
    url: BASE_URL + "vendor/GetVendorList",
    data: $("#searchForm").serialize(),
    success: function (response) {
      const Header = $("#vendorTableBody");
      let body = "";
      if (response.status == 200) {
        if ($.fn.DataTable.isDataTable("#vendorsTable")) {
          $("#vendorsTable").DataTable().destroy();
        }
        response.result.forEach((element) => {
          let badge = StatusHandler(element.statusCode);
          body += ` <tr>
                                    <td class="fw-semibold">${element.vendorCode}</td>
                                    <td>${element.companyName}</td>
                                    <td>${element.email}</td>
                                    <td>${element.phone}</td>
                                    <td>${element.businessType}</td>
                                    <td><label class='status-badge ${badge}'>${element.vendorStatus}</label></td>
                                    <td><small class="text-muted">${element.registrationDate}</small></td>
                                </tr>`;
        });
        Header.append(body);
        $("#vendorsTable").DataTable();
      }
    },
    error: function (err) {
      alert("Error loading data");
    },
  });
}
