$(document).ready(function () {
  SearchInvoice();
});
function ResetInvoiceFilter() {
  document.getElementById("searchForm").reset();

  if ($(".select2").length) {
    $(".select2").val("").trigger("change");
  }

  SearchInvoice();
}

function SearchInvoice() {
  showLoading();
  $.ajax({
    type: "POST",
    url: BASE_URL + "invoice/GetInvoiceProcurement",
    data: $("#searchForm").serialize(),
    success: function (response) {
      hideLoading();
      const Header = $("#invoiceBody");
      let body = "";
      if (response.status == 200) {
        if ($.fn.DataTable.isDataTable("#invoiceTable")) {
          $("#invoiceTable").DataTable().destroy();
        }
        Header.empty();
        response.result.forEach((element) => {
          let badge = StatusHandler(element.statusCode);
          body += ` <tr>
                        <td class="fw-semibold">${element.invoiceNumber}</td>
                        <td>${element.poNumber}</td>
                        <td>${element.vendorName}</td>
                        <td>${element.totalAmount}</td>
                        <td>${element.dueDate}</td>
                        <td><label class='status-badge ${badge}'>${element.statusName}</label></td>
                        <td>${element.createdAt}</td>
                    </tr>`;
        });
        Header.append(body);
        feather.replace();
        $("#invoiceTable").DataTable();
      }
    },
    error: function (err) {
      alert("Error loading data");
    },
  });
}
