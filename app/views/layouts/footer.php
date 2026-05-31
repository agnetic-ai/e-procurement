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
<script>
    feather.replace();
</script>
<script src="<?php echo BASE_URL; ?>public/voler/assets/js/app.js"></script>
<script src="<?php echo BASE_URL; ?>public/voler/assets/js/main.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('userDropdown');
        if (!btn) return;
        const dropdown = new bootstrap.Dropdown(btn);
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            dropdown.toggle();
        });
    });
</script>

<script>
    window.BASE_URL = "<?php echo BASE_URL; ?>";
</script>
<script>
    $(document).ready(function() {
        $('.select2').select2();
    });

    function StatusHandler(status) {
        const statusLower = status.toLowerCase();
        console.log(statusLower);
        switch (true) {
            case statusLower.includes('inactive'):
                return 'status-inactive';
            case statusLower.includes('active'):
                return 'status-active';
            case statusLower.includes('pending'):
                return 'status-pending';
            case statusLower.includes('approved'):
                return 'status-approved';
            case statusLower.includes('rejected'):
                return 'status-rejected';
            case statusLower.includes('complete'):
                return 'status-complete';
            case statusLower.includes('draft'):
                return 'status-draft';

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

    function formatCurrency(amount) {
        return parseFloat(amount).toLocaleString("id-ID");
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
        if (!value) return 0;
        value = value.toString().trim();
        if (value.includes(',') && value.includes('.')) {
            value = value.split(',')[0].replace(/\./g, '');
        } else if (value.includes(',')) {
            value = value.replace(/,/g, '');
        } else {
            value = value.replace(/\./g, '');
        }

        return parseInt(value, 10) || 0;
    }


    function showLoading(
        message = 'Please wait',
        subtext = ''
    ) {
        if (document.getElementById('loadingOverlay')) return;

        const overlay = document.createElement('div');
        overlay.id = 'loadingOverlay';
        overlay.className = 'loading-overlay';

        overlay.innerHTML = `
    <div class="loading-content">
      <div class="spinner">
        <div class="spinner-circle"></div>
        <div class="spinner-circle"></div>
        <div class="spinner-circle"></div>
      </div>
      <div class="loading-text">
        ${message}
        <span class="loading-dots">
          <span>.</span><span>.</span><span>.</span>
        </span>
      </div>
      <div class="loading-subtext">${subtext}</div>
    </div>
  `;

        document.body.appendChild(overlay);
    }

    function hideLoading() {
        const overlay = document.getElementById('loadingOverlay');
        if (!overlay) return;

        overlay.style.opacity = '0';
        setTimeout(() => overlay.remove(), 300);
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

</body>

</html>