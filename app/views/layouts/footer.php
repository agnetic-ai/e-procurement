</div>
<footer class="mt-auto">
    <div class="footer clearfix mb-0 text-muted py-3">
        <div class="container-fluid">
            <div class="float-start">
                <p><?php echo date('Y'); ?> &copy; eProcurement System</p>
            </div>
            <div class="float-end">
                <p>Powered by <a href="#">Voler Admin</a></p>
            </div>
        </div>
    </div>
</footer>
</div>
</div>

<!--Sweet alert -->
<script src="<?php echo BASE_URL; ?>public/select2/js/select2.min.js"></script>
<script src="<?php echo BASE_URL; ?>public/sweetalert/sweetalert2.all.min.js"></script>
<!-- Voler JavaScript -->
<script src="<?php echo BASE_URL; ?>public/voler/assets/vendors/simple-datatables/simple-datatables.js"></script>
<script src="<?php echo BASE_URL; ?>public/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo BASE_URL; ?>public/node_modules/datatables.net/js/dataTables.min.js"></script>
<script src="<?php echo BASE_URL; ?>public/node_modules/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
<script src="<?php echo BASE_URL; ?>public/voler/assets/js/feather-icons/feather.min.js"></script>
<script src="<?php echo BASE_URL; ?>public/voler/assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
<script src="<?php echo BASE_URL; ?>public/voler/assets/vendors/choices.js/choices.min.js"></script>
<script src="<?php echo BASE_URL; ?>public/voler/assets/js/app.js"></script>
<script src="<?php echo BASE_URL; ?>public/voler/assets/js/main.js"></script>
<script>
    window.BASE_URL = "<?php echo BASE_URL; ?>";
</script>
<script>
    $(document).ready(function() {
        $('.sidebar-toggler').click(function() {
            $('#sidebar').toggleClass('active');
        });
        $('.select2').select2();
    });

    function StatusHandler(status) {
        const statusLower = status.toLowerCase();

        switch (true) {
            case statusLower.includes('inactive'):
                return 'status-inactive';
            case statusLower.includes('active'):
                return 'status-active';
            case statusLower.includes('pending'):
                return 'status-pending';

            case statusLower.includes('review'):
                return 'status-pending';
            default:
                return 'status-unknown';
        }
    }

    function cearFilter() {
        $("#searchForm")[0].reset();
        location.reload();
    }

    function formatMoney(input) {
        let value = input.value;
        value = value.replace(/[^0-9]/g, '');
        if (value === '') {
            input.value = '';
            return;
        }
        let formatted = new Intl.NumberFormat('id-ID').format(value);
        input.value = formatted;
    }

    function unformatMoneyValue(value) {
        return parseInt(value.replace(/\./g, '')) || 0;
    }


    function showLoading(message = 'Loading...') {
        if ($('#loadingOverlay').length === 0) {
            $('body').append(`
                    <div class="loading-overlay" id="loadingOverlay">
                        <div class="loading-spinner"></div>
                        <p class="mt-3 text-muted">${message}</p>
                    </div>
                `);
        }
        $('#loadingOverlay').fadeIn(200);
    }

    function hideLoading() {
        $('#loadingOverlay').fadeOut(200, function() {
            $(this).remove();
        });
    }

    // Global Alert Function
    function showAlert(type, message, duration = 3000) {
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        };

        const alertId = 'global-alert-' + Date.now();
        const alertHtml = `
                <div id="${alertId}" class="alert ${alertClass[type]} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3" 
                     style="z-index: 9999; min-width: 300px; max-width: 500px;">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;

        $('body').append(alertHtml);

        // Auto remove after duration
        setTimeout(() => {
            $(`#${alertId}`).alert('close');
        }, duration);
    }
</script>

<!-- Page Specific Scripts -->
<?php
// Load page-specific scripts if needed
if (isset($pageScripts)) {
    foreach ($pageScripts as $script) {
        echo '<script src="' . BASE_URL . 'public/js/' . $script . '"></script>';
    }
}
?>

<script>
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
</script>
</body>

</html>