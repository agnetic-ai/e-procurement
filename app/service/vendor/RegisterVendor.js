$(function () {
  $("#vendorForm").on("submit", function (e) {
    e.preventDefault();
    submitVendor();
  });
});

function submitVendor() {
  let formVendor = $("#vendorForm");
  let name = formVendor.find('input[name="name"]').val();
  let email = formVendor.find('input[name="email"]').val();
  let phone = formVendor.find('input[name="phone"]').val();
  let city_id = parseInt(formVendor.find('select[name="city_id"]').val());
  let business_type_id = formVendor
    .find('select[name="business_type_id"]')
    .val();
  let tax_number = formVendor.find('input[name="tax_number"]').val();
  let payment_terms = parseInt(
    formVendor.find('select[name="payment_terms"]').val()
  );
  let website = formVendor.find('input[name="website"]').val();
  let address = formVendor.find('textarea[name="address"]').val();

  let dto = {
    vendorName: name,
    vendorEmail: email,
    vendorPhone: phone,
    cityId: city_id,
    businessType: business_type_id,
    taxNumber: tax_number,
    paymentTerms: payment_terms,
    website: website,
    address: address,
  };

  $.ajax({
    type: "POST",
    url: BASE_URL + "vendor/SubmitNewVendor",
    contentType: "application/json",
    data: JSON.stringify(dto),
    dataType: "json",
    success: function (response) {
      Swal.fire({
        title: "Success!",
        text: "Vendor created successfully.",
        icon: "success",
      }).then(() => {
        window.location = BASE_URL + "vendor/index";
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
