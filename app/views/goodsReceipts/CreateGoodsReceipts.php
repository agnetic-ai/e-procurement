<div class="main-content container-fluid">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3><?= $data['title'] ?></h3>
                <p class="text-subtitle text-muted"><?= $data['subtitle'] ?></p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class='breadcrumb-header'>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo BASE_URL; ?>goodsReceipts/index">Goods Receipts</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Manage Goods Receipts</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title"><i data-feather="file-text"></i>
                Select Purchase Order</h5>
        </div>
        <div class="card-body">
            <form id="formGoodsReceipts" method="post" onsubmit="return false;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="required">Choose Purchase Order Number</label>
                            <select class="form-control select2" name="selPurchaseOrder" id="selPurchaseOrder">
                                <option value="">-- Select Purchase Order --</option>
                                <?php if (!empty($data['PurchaseOrder'])): ?>
                                    <?php foreach ($data['PurchaseOrder'] as $obj): ?>
                                        <option value="<?php echo $obj['poNumber']; ?>">
                                            <?php echo $obj['poNumber']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">No Purchase Order available</option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12 d-flex justify-content-start">
                        <button type="button" class="btn btn-primary" onclick="ChoosePurchaseOrder();">Chooose Purchase Order Nmber</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i data-feather="file-text"></i>
                Purchase Order Information
            </h5>
        </div>
        <div class="card-body">
            <form id="formGoodsReceipts" method="post" onsubmit="return false;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Purchase Order Number</label>
                            <input type="text" class="form-control" id="txt_po" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Purchase Request Number</label>
                            <input type="text" class="form-control" id="txt_pr" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Department</label>
                            <input type="text" class="form-control" id="txt_dep" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Po Received Date</label>
                            <input type="text" class="form-control" id="po_date" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Po Received By</label>
                            <input type="text" class="form-control" id="po_by" disabled>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div id="formHistory" class="d-none">
        <div class="card card-history">
            <div class="card-header">
                <h5 class="card-title">
                    <i data-feather="clock"></i> Previous GR History for <small id="smPo"></small>
                </h5>
            </div>
            <div class="card-body">
                <div class="responsive-container">
                    <table class='table-custom'>
                        <thead>
                            <tr>
                                <th>GR Number</th>
                                <th>Receipt Date</th>
                                <th>Total Items Received</th>
                                <th>Status</th>
                                <th>Received By</th>
                            </tr>
                        </thead>
                        <tbody id="bodyHistoryGr">
                            <tr>
                                <td colspan="5" align="center">No History Found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="alert alert-info d-flex align-items-center">
        <i data-feather="info" class="me-2"></i>
        <div>
            <strong>Info:</strong> Items with unit <code>Unit</code> require serial numbers. Partial deliveries are allowed.
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i data-feather="archive"></i> Goods Receipt Details
            </h5>
        </div>
        <div class="card-body card-over">
            <div class="responsive-container">
                <table class='table-custom' id="goodsReceiptsDetailTable">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Unit Of Meansure</th>
                            <th>Quantity Order</th>
                            <th>Quantity Received</th>
                            <th>Serial Number</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="form-group">
                    <label>Goods Receipt Notes</label>
                    <textarea class="form-control" name="notes" style="resize: none;" id="gr_notes"></textarea>
                </div>
            </div>
            <div class="col-12 d-flex justify-content-end">
                <a href="<?php echo BASE_URL; ?>goodsReceipts" class="btn me-1 mb-1 btn-secondary">
                    <i data-feather="chevron-left"></i>
                    Back</a> &nbsp;

                <button type="button" class="btn btn-primary me-1 mb-1" onclick="SubmitGoodsReceipts();">
                    Submit
                </button>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo BASE_URL; ?>app/service/goodsReceipts/CreateGr.js"></script>