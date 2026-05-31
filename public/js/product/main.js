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
                        <td><span class="badge bg-light text-dark">${element.productCode || '-'}</span></td>
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
                           <a href="${BASE_URL}product/UpdateProduct?productId=${element.productId}&vendorId=${element.vendorId}" class="btn btn-outline-primary btn-sm">
                              <i data-feather="edit"></i>
                          </a>
                          <a type="button" class="btn btn-outline-danger btn-sm" onclick="ConfirmDelete(${element.productId});">
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

function ConfirmDelete(productId) {
  Swal.fire({
    title: "Confirm Deletion",
    text: "Are you sure you want to delete this product? This action cannot be undone.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes",
    cancelButtonText: "No",
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      DeleteProduct(productId);
    }
  });
}

function DeleteProduct(productId) {
  let dto = {
    statusCode: "PRODUCT_INACTIVE",
    productId: parseInt(productId),
  };

  $.ajax({
    type: "POST",
    url: BASE_URL + "product/RemoveProduct",
    contentType: "application/json",
    data: JSON.stringify(dto),
    dataType: "json",
    success: function (response) {
      console.log(JSON.stringify(response), " response");
      Swal.fire({
        title: "Success!",
        text: "Delete Product successfully.",
        icon: "success",
      }).then(() => {
        loadProduct();
      });
    },
    error: function (err) {
      console.log(JSON.stringify(err), "error");
      Swal.fire({
        title: "Failed!",
        text: err.responseJSON.message,
        icon: "error",
      });
    },
  });
}
