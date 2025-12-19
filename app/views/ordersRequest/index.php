<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><?= $data["title"] ?></h3>
                <p class="text-subtitle text-muted"><?= $data["subtitle"] ?></p>
            </div>
        </div>
    </div>
    <section class="section">
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
                                <input type="text" name="filterName" class="form-control"
                                    placeholder="Search by name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <select class="choices form-select">
                                    <option value="square">Square</option>
                                    <option value="rectangle">Rectangle</option>
                                    <option value="rombo">Rombo</option>
                                    <option value="romboid">Romboid</option>
                                    <option value="trapeze">Trapeze</option>
                                    <option value="traible">Triangle</option>
                                    <option value="polygon">Polygon</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-primary w-100" id="btnRefresh" onclick="loadProduct();">
                                <i data-feather="search" class="me-2"></i> Search
                            </button>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-sm btn-secondary w-100" type="button" onclick="cearFilter();">
                                <i data-feather="refresh-cw" class="me-2"></i> Refresh
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i data-feather="list" class="me-2"></i> Product List
                </h5>
                <div>
                    <a href="<?php echo BASE_URL; ?>product/NewProduct" class="btn btn-primary btn-sm">
                        <i data-feather="plus" class="me-1"></i> Add Product
                    </a>
                </div>
            </div>
            <div class="card-body card-over">
                <div class="responsive-container">
                    <table class='table-custom' id="orderRequestTable">
                        <thead>
                            <tr>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Vendor Name</th>
                                <th>Unit Price</th>
                                <th>Unit of Measure</th>
                                <th>Valid From</th>
                                <th>Valid To</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="procutTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- <script src="<?php echo BASE_URL; ?>app/service/product/main.js"></script> -->