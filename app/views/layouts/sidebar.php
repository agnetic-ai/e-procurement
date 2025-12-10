<div id="app">
    <!-- SIDEBAR -->
    <div id="sidebar" class='active'>
        <div class="sidebar-wrapper active">
            <div class="sidebar-header">
                <img src="<?php echo BASE_URL; ?>public/voler/assets/images/shopping-cart.png" alt="Logo">
            </div>
            <div class="sidebar-menu">
                <ul class="menu">
                    <li class='sidebar-title'>Main Menu</li>
                    <?php
                    $menus = $this->getVolerMenus();
                    foreach ($menus as $menu):
                        $isActive = ($current_page == $menu['url']);
                    ?>
                        <li class="sidebar-item <?php echo $isActive ? 'active' : ''; ?>">
                            <a href="<?php echo BASE_URL . $menu['url']; ?>" class='sidebar-link'>
                                <i data-feather="user"></i>
                                <span><?php echo $menu['title']; ?></span>
                            </a>
                        </li>
                    <?php endforeach; ?>

                    <li class='sidebar-title'>Pages</li>
                    <li class="sidebar-item  has-sub">
                        <a href="#" class='sidebar-link'>
                            <i data-feather="user" width="20"></i>
                            <span>Authentication</span>
                        </a>
                        <ul class="submenu ">
                            <li>
                                <a href="auth-login.html">Login</a>
                            </li>
                            <li>
                                <a href="auth-register.html">Register</a>
                            </li>
                            <li>
                                <a href="auth-forgot-password.html">Forgot Password</a>
                            </li>
                        </ul>
                    </li>

                </ul>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT WRAPPER -->
    <div id="main" class="d-flex flex-column min-vh-100">
        <!-- TOP NAVBAR (FIXED di atas) -->
        <nav class="navbar navbar-header navbar-expand navbar-light">
            <a class="sidebar-toggler" href="#"><span class="navbar-toggler-icon"></span></a>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav d-flex align-items-center navbar-light ms-auto">
                    <li class="dropdown">
                        <a href="#" data-bs-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                            <div class="avatar me-1">
                                <img src="<?php echo BASE_URL; ?>public/voler/assets/images/avatar/avatar-s-1.png" alt="Avatar">
                            </div>
                            <div class="d-none d-md-block d-lg-inline-block">
                                Hi, <?php echo htmlspecialchars($session->getUserName()['name'] ?? 'Admin'); ?>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>profile">
                                <i data-feather="user"></i> Profile
                            </a>
                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>settings">
                                <i data-feather="settings"></i> Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>auth/logout">
                                <i data-feather="log-out"></i> Logout
                            </a>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- CONTENT AREA (FLEXIBLE) -->
        <div class="main-content container-fluid flex-grow-1">
            <!-- Content akan dimasukkan di sini -->