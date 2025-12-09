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
        </div> <!-- Close #main -->
        </div> <!-- Close #app -->

        <!-- Voler JavaScript -->
        <script src="<?php echo BASE_URL; ?>public/voler/assets/js/feather-icons/feather.min.js"></script>
        <script src="<?php echo BASE_URL; ?>public/voler/assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js"></script>
        <script src="<?php echo BASE_URL; ?>public/voler/assets/js/app.js"></script>
        <script src="<?php echo BASE_URL; ?>public/voler/assets/js/main.js"></script>

        <script>
            // Initialize Feather Icons
            feather.replace();

            // Sidebar toggle
            const sidebarToggle = document.querySelector('.sidebar-toggler');
            const sidebar = document.querySelector('#sidebar');

            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', () => {
                    sidebar.classList.toggle('active');
                });
            }

            // Sticky footer behavior
            function adjustFooter() {
                const main = document.querySelector('#main');
                const footer = document.querySelector('footer');

                if (main && footer) {
                    const windowHeight = window.innerHeight;
                    const mainHeight = main.offsetHeight;

                    if (mainHeight < windowHeight) {
                        main.style.minHeight = windowHeight + 'px';
                    }
                }
            }

            // Adjust on load and resize
            window.addEventListener('load', adjustFooter);
            window.addEventListener('resize', adjustFooter);
        </script>
        </body>

        </html>