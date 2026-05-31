function SubmitAssignAsset() {
  if ($("select[name='employee_id']").val() === "") {
    Swal.fire({
      title: "Validation Error",
      text: "Please select employee",
      icon: "warning",
    });
    return;
  }

  if ($("input[name='assigned_date']").val() === "") {
    Swal.fire({
      title: "Validation Error",
      text: "Please select assign date",
      icon: "warning",
    });
    return;
  }

  const dto = {
    assetId: parseInt($("input[name='asset_unit_id']").val()),
    employeeId: parseInt($("select[name='employee_id']").val()),
    assignedDate: $("input[name='assigned_date']").val(),
    notes: $("textarea[name='notes']").val(),
  };

  Swal.fire({
    title: "Assign Asset?",
    text: "Are you sure you want to assign this asset?",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Yes, Assign",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        type: "POST",
        url: BASE_URL + "Assets/SubmitAssignAsset",
        contentType: "application/json",
        data: JSON.stringify(dto),
        dataType: "json",
        beforeSend: function () {
          showLoading();
        },
        success: function (response) {
          hideLoading();

          if (response.status === 201 || response.status === 200) {
            Swal.fire({
              title: "Success",
              text: response.message,
              icon: "success",
            }).then(() => {
              window.location = BASE_URL + "assets/stock";
            });
          } else {
            Swal.fire({
              title: "Failed",
              text: response.message || "Failed to assign asset",
              icon: "error",
            });
          }
        },
        error: function (err) {
          hideLoading();
          Swal.fire({
            title: "Error",
            text: err.responseJSON?.message || "Unexpected error occurred",
            icon: "error",
          });
        },
      });
    }
  });
}
