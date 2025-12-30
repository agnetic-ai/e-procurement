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
                Information Goods Receipt</h5>
        </div>
        <div class="card-body">
            <form id="formGoodsReceipts" method="post" onsubmit="return false;">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Goods Receipt Number</label>
                            <input type="text" class="form-control" value="<?= $data["GrHeader"]["grNumber"] ?>" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Purchase Request Number</label>
                            <input type="text" class="form-control" value="<?= $data["GrHeader"]["prNumber"] ?>" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Department</label>
                            <input type="text" class="form-control" value="<?= $data["GrHeader"]["department"] ?>" disabled>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Received Date</label>
                            <input type="text" class="form-control" value="<?= date('d M Y') ?>" disabled>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Received By</label>
                            <input type="text" class="form-control" value="<?= $session->get("full_name") ?>" disabled>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="alert alert-secondary color-secondary"><i data-feather="info"></i>
        This form is used to record the receipt of goods after the Purchase Request has been approved. For items with the unit “Unit” (electronics), entering a Serial Number is mandatory upon submission. </div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title"><i data-feather="archive"></i>
                Information Detail Goods Receipt</h5>
        </div>
        <div class="card-body card-over">
            <div class="responsive-container">
                <table class='table-custom' id="goodsReceiptsDetailTable">
                    <thead>
                        <tr>
                            <th>Product Name</th>
                            <th>Quantity Order</th>
                            <th>Unit Of Meansure</th>
                            <th>Quantity Received</th>
                            <th>Serial Number</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data["GrDetails"] as $detail) : ?>
                            <tr data-id="<?= $detail["goodsDetailId"] ?>">
                                <td><?= $detail["productName"] ?></td>
                                <td><?= $detail["quantity"] ?></td>
                                <td><span class="unit-badge"><?= $detail["unit"] ?></span></td>
                                <td>
                                    <input type="number"
                                        class="form-control received-qty"
                                        name="receivedQty[<?= $detail['goodsDetailId'] ?>]"
                                        min="0"
                                        max="<?= $detail["quantity"] ?>"
                                        value="<?= $detail["qty_received"] ?>"
                                        <?= $detail['qty_received'] == $detail["quantity"] ? 'readonly' : '' ?>>
                                    <small class="form-text mt-1 qty-info
                                        <?= $detail['qty_received'] == $detail['quantity']
                                            ? 'text-success'
                                            : 'text-danger' ?>"
                                        data-qty-order="<?= $detail['quantity'] ?>"
                                        data-qty-received="<?= $detail['qty_received'] ?>">

                                        <?= $detail['qty_received'] == $detail['quantity']
                                            ? 'Complete : All items have been received.'
                                            : 'Remaining: ' . ($detail['quantity'] - $detail['qty_received']) . ' of ' . $detail['quantity'] ?>
                                    </small>

                                </td>
                                <td>
                                    <?php if ($detail["unit"] === "Unit") : ?>
                                        <?php
                                        $hasAsset = false;
                                        foreach ($data["AssetUnits"] as $asset) :
                                            if ($asset["goodsDetailId"] == $detail["goodsDetailId"]) :
                                                $hasAsset = true;
                                        ?>
                                                <input type="text"
                                                    class="form-control mb-2"
                                                    name="serialNumber[<?= $detail['goodsDetailId'] ?>][]"
                                                    value="<?= $asset['serialNumber'] ?? '' ?>"
                                                    placeholder="Enter Serial Number"
                                                    data-id="<?= $asset['assetId'] ?>"
                                                    <?= !empty($asset['serialNumber']) ? 'readonly' : 'required' ?>>
                                        <?php
                                            endif;
                                        endforeach;
                                        ?>

                                        <?php if (!$hasAsset) : ?>
                                            <input type="text"
                                                class="form-control"
                                                placeholder="No serial number data"
                                                readonly>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <input type="text" class="form-control" value="N/A" readonly>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <!-- <div class="col-md-12"> -->
                <div class="form-group">
                    <label>Goods Receipt Notes</label>
                    <textarea class="form-control"
                        <?= in_array($data["GrHeader"]["statusCode"], ["GR_DRAFT", "GR_PROCESS"])
                            ? 'required'
                            : 'readonly' ?>
                        name="notes" style="resize: none;" id="gr_notes"><?= $data["GrHeader"]["notes"] ?></textarea>
                </div>
            </div>
            <div class="col-12 d-flex justify-content-end">
                <a href="<?php echo BASE_URL; ?>goodsReceipts" class="btn me-1 mb-1 btn-secondary">
                    <i data-feather="chevron-left"></i>
                    Back</a> &nbsp;
                <?php if (
                    $data["GrHeader"]["statusCode"] === "GR_DRAFT" ||
                    $data["GrHeader"]["statusCode"] === "GR_PROCESS"
                ) : ?>
                    <button type="button" class="btn btn-primary me-1 mb-1" onclick="SubmitGoodsReceipts();">
                        Submit
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script>
    function SubmitGoodsReceipts() {
        const table = $('#goodsReceiptsDetailTable tbody tr');
        let goodsDetails = [];

        table.each(function() {
            const row = $(this);
            const goodsDetailId = row.data('id');
            const receivedQty = parseInt(row.find('.received-qty').val()) || 0;

            let serialNumbers = [];
            row.find('input[name="serialNumber[' + goodsDetailId + '][]"][required]').each(function() {
                const sn = $(this).val().trim();

                if (sn !== '') {
                    serialNumbers.push({
                        assetId: $(this).data('id'),
                        serialNumber: sn
                    });
                }
            });

            goodsDetails.push({
                goodsDetailId: goodsDetailId,
                receivedQty: receivedQty,
                serialNumbers: serialNumbers.length > 0 ? serialNumbers : null
            });
        });

        var dto = {
            grNumber: "<?= $data['GrHeader']['grNumber'] ?>",
            receiveBy: <?= (int) $session->get('user_id') ?>,
            receiveDate: "<?= date('Y-m-d') ?>",
            notes: $("#gr_notes").val().trim(),
            goodsDetails: goodsDetails
        };

        showLoading();
        $.ajax({
            type: "POST",
            url: BASE_URL + "GoodsReceipts/SubmitGoodsReceipt",
            contentType: "application/json",
            data: JSON.stringify(dto),
            dataType: "json",
            success: function(response) {
                hideLoading();
                if (response.status == 201) {
                    Swal.fire({
                        title: "Success!",
                        text: response.message,
                        icon: "success",
                    }).then(() => {
                        window.location = BASE_URL + "goodsReceipts";
                    });
                }
            },
            error: function(err) {
                hideLoading();
                Swal.fire({
                    title: "Failed!",
                    text: err.responseJSON.message,
                    icon: "error",
                });
            },
        });
    }
</script>