$(document).ready(function () {
  loadProduct();
});

function loadProduct() {
  $.ajax({
    type: "POST",
    url: BASE_URL + "product/GetProductList",
    data: $("#searchForm").serialize(),
    success: function (response) {
      const Header = $("#procutTableBody");
      let body = "";
      if (response.status == 200) {
        if ($.fn.DataTable.isDataTable("#productTable")) {
          $("#productTable").DataTable().destroy();
        }
        Header.empty();
        response.result.forEach((element) => {
          let badge = StatusHandler(element.statusCode);
          body += ` <tr>
                        <td class="fw-semibold">${element.productName}</td>
                        <td>${element.categoryName}</td>
                        <td>${element.vendorName}</td>
                        <td>${element.unitPrice}</td>
                        <td>${element.uof}</td>
                        <td>${element.validFrom}</td>
                        <td>${element.validTo}</td>
                        <td><label class='status-badge ${badge}'>${element.statusName}</label></td>
                        <td>
                        <div class="buttons">
                           <a href="${BASE_URL}vendor/UpdateVendor?vendorCode=${element.productId}" class="btn btn-outline-primary btn-sm">
                              <i data-feather="edit"></i>
                          </a>
                          <a href="#" class="btn btn-outline-danger btn-sm">
                              <i data-feather="trash-2"></i>
                          </a>
                          </div>
                        </td>
                    </tr>`;
        });
        Header.append(body);
        feather.replace();
        $("#productTable").DataTable();
      }
    },
    error: function (err) {
      alert("Error loading data");
    },
  });
}
