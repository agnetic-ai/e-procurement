<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><?= $data["title"] ?></h3>
                <p class="text-subtitle text-muted"><?= $data["subtitle"] ?></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class='breadcrumb-header'>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active">User Management</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        <!-- Search Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i data-feather="search" class="me-2"></i> Search & Filter
                </h5>
            </div>
            <div class="card-body">
                <form id="searchForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i data-feather="search"></i>
                                </span>
                                <input type="text" name="filterName" id="filterName" class="form-control"
                                    placeholder="Search by username, name, or email">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-primary w-100" onclick="loadUsers();">
                                <i data-feather="search" class="me-2"></i> Search
                            </button>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-secondary w-100" onclick="clearFilter();">
                                <i data-feather="refresh-cw" class="me-2"></i> Refresh
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Users Table Card -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i data-feather="users" class="me-2"></i> User List
                </h5>
                <button class="btn btn-primary btn-sm" onclick="openCreateModal();">
                    <i data-feather="plus" class="me-1"></i> Add User
                </button>
            </div>
            <div class="card-body card-over">
                <div class="responsive-container">
                    <table class='table-custom' id="usersTable">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Login Attempts</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Create/Edit User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Add User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modalUserId" value="">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="modalUsername" placeholder="Enter username">
                </div>
                <div class="mb-3" id="passwordGroup">
                    <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="modalPassword" placeholder="Enter password (min 6 chars)">
                </div>
                <div class="mb-3" id="newPasswordGroup" style="display:none;">
                    <label class="form-label fw-semibold">New Password <small class="text-muted">(leave blank to keep current)</small></label>
                    <input type="password" class="form-control" id="modalNewPassword" placeholder="Enter new password">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="modalFullName" placeholder="Enter full name">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="modalEmail" placeholder="Enter email">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Role <span class="text-danger">*</span></label>
                    <select class="form-select" id="modalRole">
                        <option value="">-- Select Role --</option>
                        <?php foreach ($data['roles'] as $role): ?>
                            <option value="<?= $role['roleId'] ?>"><?= htmlspecialchars($role['roleName']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="modalIsActive" checked>
                        <label class="form-check-label" for="modalIsActive">Active</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="btnSaveUser" onclick="saveUser();">
                    <i data-feather="save" class="me-1"></i> Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <i data-feather="alert-triangle" style="width:48px;height:48px;color:#dc3545;"></i>
                <p class="mt-3">Yakin ingin menghapus user <strong id="deleteUserName"></strong>?</p>
                <input type="hidden" id="deleteUserId" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" onclick="confirmDelete();">
                    <i data-feather="trash-2" class="me-1"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>public/js/users/main.js"></script>
