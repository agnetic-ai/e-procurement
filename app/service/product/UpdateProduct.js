$(function () {
  $("#updateForm").on("submit", function (e) {
    e.preventDefault();
    SubmitUpdateProduct();
  });
});

function SubmitUpdateProduct() {
  let formVendor = $("#updateForm");
  var dto = {
    productName: formVendor.find('input[name="name"]').val(),
    vendorId: parseInt(formVendor.find('select[name="vendor_id"]').val()),
    categoryId: parseInt(formVendor.find('select[name="category"]').val()),
    uof: formVendor.find('select[name="uof"]').val(),
    validFrom: formVendor.find('input[name="valid_from"]').val(),
    validTo: formVendor.find('input[name="valid_to"]').val(),
    unitPrice: unformatMoneyValue(
      formVendor.find('input[name="unit_price"]').val()
    ),
    statusCode: formVendor.find('select[name="status_code"]').val(),
    description: formVendor.find('textarea[name="description"]').val(),
    productId: parseInt(formVendor.find('label[name="product_id"]').text()),
    productVendorId: parseInt(
      formVendor.find('label[name="product_vendor_id"]').text()
    ),
  };

  $.ajax({
    type: "POST",
    url: BASE_URL + "product/SubmitUpdateProduct",
    contentType: "application/json",
    data: JSON.stringify(dto),
    dataType: "json",
    success: function (response) {
      Swal.fire({
        title: "Success!",
        text: "Product updated successfully.",
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
