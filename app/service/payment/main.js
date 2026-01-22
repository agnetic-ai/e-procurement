$(document).ready(function () {
  SearchInvoicePayment();
});
function ResetInvoiceFilter() {
  document.getElementById("searchForm").reset();

  if ($(".select2").length) {
    $(".select2").val("").trigger("change");
  }

  SearchInvoicePayment();
}

function SearchInvoicePayment() {
  showLoading();
  $.ajax({
    type: "POST",
    url: BASE_URL + "Payment/GetInvoicePayment",
    data: $("#searchForm").serialize(),
    success: function (response) {
      hideLoading();
      const Header = $("#paymentBody");
      let body = "";
      if (response.status == 200) {
        if ($.fn.DataTable.isDataTable("#paymentTable")) {
          $("#paymentTable").DataTable().destroy();
        }
        Header.empty();
        response.result.forEach((element) => {
          let badge = StatusHandler(element.statusCode);
          body += ` <tr>
                        <td class="fw-semibold">${element.invoiceNo}</td>
                        <td>${element.vendorName}</td>
                        <td>${element.dueDate}</td>
                        <td>${element.totalAmount}</td>
                        <td>${element.paidAmount}</td>
                        <td>${element.remainingAmount}</td>
                        <td><label class='status-badge ${badge}'>${element.statusName}</label></td>
                       <td style="white-space: nowrap;">
                        <div class="buttons">
                        <a href="${BASE_URL}Payment/PaymentForm?invoiceNo=${element.invoiceNo}" class="btn btn-outline-primary btn-sm">
                              <i data-feather="edit"></i>
                          </a>
                          </div>
                        </td>
                    </tr>`;
        });
        Header.append(body);
        feather.replace();
        $("#paymentTable").DataTable();
      }
    },
    error: function (err) {
      alert("Error loading data");
    },
  });
}
