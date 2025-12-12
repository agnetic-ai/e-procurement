$(function () {
  $("#productForm").on("submit", function (e) {
    e.preventDefault();
    SubmitNewProduct();
  });
});

function SubmitNewProduct() {
  let formVendor = $("#productForm");
  var dto = {
    productName: formVendor.find('input[name="name"]').val(),
    vendorId: formVendor.find('select[name="vendor_id"]').val(),
    categoryId: formVendor.find('select[name="category"]').val(),
    uof: formVendor.find('select[name="uof"]').val(),
    validFrom: formVendor.find('input[name="valid_from"]').val(),
    validTo: formVendor.find('input[name="valid_to"]').val(),
    unitPrice: unformatMoneyValue(
      formVendor.find('input[name="unit_price"]').val()
    ),
    statusCode: formVendor.find('select[name="status_code"]').val(),
    description: formVendor.find('textarea[name="description"]').val(),
  };
  console.log(JSON.stringify(dto));

  $.ajax({
    type: "POST",
    url: BASE_URL + "product/SubmitNewProduct",
    contentType: "application/json",
    data: JSON.stringify(dto),
    dataType: "json",
    success: function (response) {
      console.log(JSON.stringify(response));
      Swal.fire({
        title: "Success!",
        text: "Vendor created successfully.",
        icon: "success",
      }).then(() => {
        window.location = BASE_URL + "product/index";
      });
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
