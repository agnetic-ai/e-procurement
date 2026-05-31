$(document).ready(function () {
  loadUsers();
});

function loadUsers() {
  $.ajax({
    type: "POST",
    url: BASE_URL + "users/GetUserList",
    data: $("#searchForm").serialize(),
    success: function (response) {
      const Header = $("#usersTableBody");
      let body = "";
      if (response.status == 200) {
        if ($.fn.DataTable.isDataTable("#usersTable")) {
          $("#usersTable").DataTable().destroy();
        }
        Header.empty();

        if (response.result && response.result.length > 0) {
          response.result.forEach((el) => {
            let statusBadge = el.isActive
              ? '<span class="badge bg-success">Active</span>'
              : '<span class="badge bg-danger">Inactive</span>';

            let roleBadge = getRoleBadge(el.roleCode);

            let attemptsBadge = el.loginAttempts > 0
              ? `<span class="badge bg-warning">${el.loginAttempts}</span>`
              : `<span class="text-muted">0</span>`;

            body += `<tr>
              <td class="fw-semibold">${el.username}</td>
              <td>${el.fullName}</td>
              <td><small>${el.email}</small></td>
              <td>${roleBadge}</td>
              <td>${statusBadge}</td>
              <td class="text-center">${attemptsBadge}</td>
              <td><small class="text-muted">${el.createdAt}</small></td>
              <td style="white-space: nowrap;">
                <div class="buttons">
                  <button class="btn btn-outline-primary btn-sm" onclick="openEditModal(${el.userId})" title="Edit">
                    <i data-feather="edit"></i>
                  </button>
                  ${el.loginAttempts > 0 ? `<button class="btn btn-outline-warning btn-sm" onclick="resetLogin(${el.userId})" title="Reset Login Attempts">
                    <i data-feather="unlock"></i>
                  </button>` : ''}
                  <button class="btn btn-outline-danger btn-sm" onclick="openDeleteModal(${el.userId}, '${el.fullName}')" title="Delete">
                    <i data-feather="trash-2"></i>
                  </button>
                </div>
              </td>
            </tr>`;
          });
        } else {
          body = `<tr><td colspan="8" class="text-center text-muted py-4">
            <i data-feather="users" style="width:48px;height:48px;opacity:0.3;"></i>
            <p class="mt-2">No users found</p>
          </td></tr>`;
        }

        Header.append(body);
        feather.replace();
        $("#usersTable").DataTable({ ordering: false });
      }
    },
    error: function () {
      Swal.fire("Error!", "Failed to load users.", "error");
    },
  });
}

function clearFilter() {
  $("#filterName").val("");
  loadUsers();
}

function getRoleBadge(roleCode) {
  const badges = {
    admin: "bg-danger",
    procurement: "bg-info",
    requestor: "bg-secondary",
    manager: "bg-primary",
    finance: "bg-success",
    director: "bg-dark",
    head: "bg-warning",
  };
  let cls = badges[roleCode] || "bg-secondary";
  return `<span class="badge ${cls}">${roleCode || "N/A"}</span>`;
}

// ========== CREATE ==========
function openCreateModal() {
  $("#userModalLabel").text("Add User");
  $("#modalUserId").val("");
  $("#modalUsername").val("").prop("disabled", false);
  $("#modalPassword").val("");
  $("#modalNewPassword").val("");
  $("#modalFullName").val("");
  $("#modalEmail").val("");
  $("#modalRole").val("");
  $("#modalIsActive").prop("checked", true);
  $("#passwordGroup").show();
  $("#newPasswordGroup").hide();
  $("#modalUsername").prop("disabled", false);
  new bootstrap.Modal(document.getElementById("userModal")).show();
  feather.replace();
}

// ========== EDIT ==========
function openEditModal(userId) {
  $.ajax({
    type: "GET",
    url: BASE_URL + "users/GetUserById?id=" + userId,
    success: function (response) {
      if (response.status == 200 && response.result) {
        let u = response.result;
        $("#userModalLabel").text("Edit User");
        $("#modalUserId").val(u.userId);
        $("#modalUsername").val(u.username).prop("disabled", true);
        $("#modalFullName").val(u.fullName);
        $("#modalEmail").val(u.email);
        $("#modalRole").val(u.roleId);
        $("#modalIsActive").prop("checked", u.isActive == 1);
        $("#passwordGroup").hide();
        $("#newPasswordGroup").show();
        $("#modalNewPassword").val("");
        new bootstrap.Modal(document.getElementById("userModal")).show();
        feather.replace();
      } else {
        Swal.fire("Error", "User tidak ditemukan", "error");
      }
    },
    error: function () {
      Swal.fire("Error!", "Gagal memuat data user.", "error");
    },
  });
}

// ========== SAVE (Create/Update) ==========
function saveUser() {
  let userId = $("#modalUserId").val();
  let isEdit = userId !== "";

  let payload = {
    fullName: $("#modalFullName").val().trim(),
    email: $("#modalEmail").val().trim(),
    roleId: parseInt($("#modalRole").val()) || 0,
    isActive: $("#modalIsActive").is(":checked") ? 1 : 0,
  };

  if (isEdit) {
    payload.userId = parseInt(userId);
    let newPass = $("#modalNewPassword").val();
    if (newPass) payload.newPassword = newPass;
  } else {
    payload.username = $("#modalUsername").val().trim();
    payload.password = $("#modalPassword").val();
  }

  let url = isEdit ? BASE_URL + "users/UpdateUser" : BASE_URL + "users/CreateUser";

  $.ajax({
    type: "POST",
    url: url,
    contentType: "application/json",
    data: JSON.stringify(payload),
    success: function (response) {
      if (response.status >= 200 && response.status < 300) {
        bootstrap.Modal.getInstance(document.getElementById("userModal")).hide();
        Swal.fire({
          title: "Berhasil!",
          text: response.message,
          icon: "success",
          timer: 1500,
          showConfirmButton: false,
        });
        loadUsers();
      } else {
        Swal.fire("Gagal", response.message || "Terjadi kesalahan", "error");
      }
    },
    error: function (xhr) {
      let msg = "Terjadi kesalahan";
      try {
        let res = JSON.parse(xhr.responseText);
        msg = res.message || msg;
      } catch (e) {}
      Swal.fire("Error", msg, "error");
    },
  });
}

// ========== DELETE ==========
function openDeleteModal(userId, userName) {
  $("#deleteUserId").val(userId);
  $("#deleteUserName").text(userName);
  new bootstrap.Modal(document.getElementById("deleteModal")).show();
  feather.replace();
}

function confirmDelete() {
  let userId = parseInt($("#deleteUserId").val());

  $.ajax({
    type: "POST",
    url: BASE_URL + "users/DeleteUser",
    contentType: "application/json",
    data: JSON.stringify({ userId: userId }),
    success: function (response) {
      if (response.status >= 200 && response.status < 300) {
        bootstrap.Modal.getInstance(document.getElementById("deleteModal")).hide();
        Swal.fire({
          title: "Dihapus!",
          text: response.message,
          icon: "success",
          timer: 1500,
          showConfirmButton: false,
        });
        loadUsers();
      } else {
        Swal.fire("Gagal", response.message || "Terjadi kesalahan", "error");
      }
    },
    error: function (xhr) {
      let msg = "Terjadi kesalahan";
      try {
        let res = JSON.parse(xhr.responseText);
        msg = res.message || msg;
      } catch (e) {}
      Swal.fire("Error", msg, "error");
    },
  });
}

// ========== RESET LOGIN ATTEMPTS ==========
function resetLogin(userId) {
  Swal.fire({
    title: "Reset Login Attempts?",
    text: "Login attempts akan direset ke 0",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "Ya, Reset",
    cancelButtonText: "Batal",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        type: "POST",
        url: BASE_URL + "users/ResetLoginAttempts",
        contentType: "application/json",
        data: JSON.stringify({ userId: userId }),
        success: function (response) {
          if (response.status >= 200 && response.status < 300) {
            Swal.fire({ title: "Berhasil!", text: response.message, icon: "success", timer: 1200, showConfirmButton: false });
            loadUsers();
          } else {
            Swal.fire("Gagal", response.message, "error");
          }
        },
        error: function () {
          Swal.fire("Error", "Gagal reset login attempts", "error");
        },
      });
    }
  });
}
