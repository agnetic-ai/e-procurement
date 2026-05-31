<div id="app">
    <!-- SIDEBAR -->
    <div id="sidebar" class='active'>
        <div class="sidebar-wrapper active">
            <div class="sidebar-header">
                <!-- <img src="<?php echo BASE_URL; ?>public/voler/assets/images/shopping-cart.png" alt="Logo"> -->
                <img src="<?php echo BASE_URL; ?>public/voler/assets/images/logo.svg" alt="" srcset="">
            </div>
            <div class="sidebar-menu">
                <ul class="menu">
                    <li class='sidebar-title'>Main Menu</li>
                    <?php
                    $menus = $this->GenerateMenus();
                    foreach ($menus as $menu):
                        $hasChildren = isset($menu['children']) && !empty($menu['children']);
                        $isActive = ($current_page == $menu['url']);
                        $childActive = false;
                        if ($hasChildren) {
                            foreach ($menu['children'] as $child) {
                                if ($current_page == $child['url']) {
                                    $isActive = true;
                                    $childActive = true;
                                    break;
                                }
                            }
                        }
                        $menuClass = 'sidebar-item';
                        if ($isActive) {
                            $menuClass .= ' active';
                        }
                        if ($hasChildren) {
                            $menuClass .= ' has-sub';
                            if ($childActive) {
                                $menuClass .= ' open';
                            }
                        }
                    ?>
                        <li class="<?php echo $menuClass; ?>">
                            <a href="<?php echo $hasChildren ? '#' : BASE_URL . $menu['url']; ?>" class='sidebar-link'>
                                <i data-feather="<?php echo htmlspecialchars($menu['icon']); ?>"></i>
                                <span><?php echo htmlspecialchars($menu['title']); ?></span>
                                <?php if ($hasChildren): ?>
                                    <i class="bi bi-chevron-down"></i>
                                <?php endif; ?>
                            </a>

                            <?php if ($hasChildren): ?>
                                <ul class="submenu" <?php echo $childActive ? 'style="display: block;"' : ''; ?>>
                                    <?php foreach ($menu['children'] as $child):
                                        $isChildActive = ($current_page == $child['url']);
                                    ?>
                                        <li class="<?php echo $isChildActive ? 'active' : ''; ?>">
                                            <a href="<?php echo BASE_URL . $child['url']; ?>">

                                                <span><?php echo htmlspecialchars($child['title']); ?></span>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- MAIN CONTENT WRAPPER -->
    <div id="main" class="d-flex flex-column min-vh-100">
        <nav class="navbar navbar-header navbar-expand navbar-light">
            <a class="sidebar-toggler d-flex align-items-center" href="#" style="z-index:1060;">
                <i data-feather="menu" width="24" height="24"></i>
            </a>

            <div class="navbar-collapse">
                <ul class="navbar-nav d-flex align-items-center navbar-light ms-auto">
                    <li class="dropdown">
                        <button id="userDropdown"
                            class="nav-link dropdown-toggle nav-link-lg nav-link-user bg-transparent border-0">
                            <div class="avatar me-1">
                                <img src="<?= BASE_URL ?>public/voler/assets/images/avatar/avatar-s-1.png">
                            </div>
                            <div class="d-none d-md-block d-lg-inline-block">
                                Hi, <?= htmlspecialchars($session->get("full_name")) ?>
                            </div>
                        </button>

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

        <div class="main-content container-fluid flex-grow-1">