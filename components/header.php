<?php
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            light: '#F9A8D4', // Pink-300
                            DEFAULT: '#EC4899', // Pink-500
                            dark: '#BE185D',   // Pink-700
                        },
                        secondary: {
                            light: '#93C5FD', // Blue-300
                            DEFAULT: '#3B82F6', // Blue-500
                            dark: '#1E40AF',   // Blue-700
                        },
                        accent: {
                            light: '#FCD34D', // Amber-300
                            DEFAULT: '#F59E0B', // Amber-500
                            dark: '#B45309',   // Amber-700
                        },
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        .flash-message {
            transition: opacity 0.5s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <a href="<?php echo SITE_URL; ?>" class="flex items-center">
                    <span class="text-2xl font-bold text-primary">Beauty<span class="text-accent">Shop</span></span>
                </a>
                
                <!-- Search Bar -->
                <div class="hidden md:block flex-1 max-w-md mx-8">
                    <form action="<?php echo SITE_URL; ?>/pages/user/search.php" method="GET" class="relative">
                        <input type="text" name="q" placeholder="Search products..." class="w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        <button type="submit" class="absolute right-0 top-0 mt-2 mr-4 text-gray-500">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                
                <!-- Navigation -->
                <nav class="hidden md:flex items-center space-x-6">
                    <a href="<?php echo SITE_URL; ?>" class="text-gray-700 hover:text-primary transition">Home</a>
                    <div class="relative">
                        <button id="products-menu-button" class="text-gray-700 hover:text-primary transition flex items-center bg-transparent border-none p-0 cursor-pointer font-normal">
                            Products
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>
                        <div id="products-dropdown" class="absolute left-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden">
                            <?php
                            // Get all categories
                            require_once __DIR__ . '/../includes/db_functions.php';
                            $categories = getAllCategories();
                            foreach ($categories as $category) {
                                echo '<a href="' . SITE_URL . '/pages/user/products.php?category=' . $category['slug'] . '" class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary hover:text-white">' . $category['name'] . '</a>';
                            }
                            ?>
                        </div>
                    </div>
                    <a href="<?php echo SITE_URL; ?>/pages/user/about.php" class="text-gray-700 hover:text-primary transition">About</a>
                    <a href="<?php echo SITE_URL; ?>/pages/user/contact.php" class="text-gray-700 hover:text-primary transition">Contact</a>
                </nav>
                
                <!-- User Menu & Cart -->
                <div class="flex items-center space-x-4">
                    <a href="<?php echo SITE_URL; ?>/pages/user/cart.php" class="text-gray-700 hover:text-primary transition relative">
                        <i class="fas fa-shopping-cart text-xl"></i>
                        <?php if (isLoggedIn()): ?>
                        <?php
                            $cartItems = getCartItems($_SESSION['user_id']);
                            $cartCount = count($cartItems);
                            if ($cartCount > 0):
                        ?>
                        <span class="absolute -top-2 -right-2 bg-primary text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                            <?php echo $cartCount; ?>
                        </span>
                        <?php endif; ?>
                        <?php endif; ?>
                    </a>
                    
                    <?php if (isLoggedIn()): ?>
                    <div class="relative">
                        <button id="user-menu-button" class="flex items-center text-gray-700 hover:text-primary transition">
                            <i class="fas fa-user-circle text-xl mr-1"></i>
                            <span class="hidden md:inline"><?php echo $_SESSION['username']; ?></span>
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>
                        <div id="user-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden">
                            <?php if (isAdmin()): ?>
                            <a href="<?php echo ADMIN_URL; ?>/dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary hover:text-white">
                                <i class="fas fa-tachometer-alt mr-2"></i> Admin Dashboard
                            </a>
                            <?php else: ?>
                            <a href="<?php echo USER_URL; ?>/profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary hover:text-white">
                                <i class="fas fa-user mr-2"></i> My Profile
                            </a>
                            <a href="<?php echo USER_URL; ?>/orders.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary hover:text-white">
                                <i class="fas fa-box mr-2"></i> My Orders
                            </a>
                            <?php endif; ?>
                            <div class="border-t border-gray-100"></div>
                            <a href="<?php echo SITE_URL; ?>/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-primary hover:text-white">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                        </div>
                    </div>
                    <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>/index.php" class="text-gray-700 hover:text-primary transition">
                        <i class="fas fa-sign-in-alt mr-1"></i>
                        <span class="hidden md:inline">Login</span>
                    </a>
                    <?php endif; ?>
                    
                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-button" class="md:hidden text-gray-700 hover:text-primary transition">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Search (Hidden by default) -->
            <div id="mobile-search" class="mt-4 md:hidden hidden">
                <form action="<?php echo SITE_URL; ?>/pages/user/search.php" method="GET" class="relative">
                    <input type="text" name="q" placeholder="Search products..." class="w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    <button type="submit" class="absolute right-0 top-0 mt-2 mr-4 text-gray-500">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
            
            <!-- Mobile Menu (Hidden by default) -->
            <nav id="mobile-menu" class="mt-4 md:hidden hidden">
                <div class="flex flex-col space-y-2 py-3">
                    <a href="<?php echo SITE_URL; ?>" class="text-gray-700 hover:text-primary transition py-2 px-3 hover:bg-gray-100 rounded">Home</a>
                    <a href="<?php echo SITE_URL; ?>/pages/user/products.php" class="text-gray-700 hover:text-primary transition py-2 px-3 hover:bg-gray-100 rounded">All Products</a>
                    
                    <!-- Mobile Categories -->
                    <div class="pl-3 space-y-1">
                        <?php foreach ($categories as $category): ?>
                        <a href="<?php echo SITE_URL; ?>/pages/user/products.php?category=<?php echo $category['slug']; ?>" class="text-gray-700 hover:text-primary transition py-1 px-3 hover:bg-gray-100 rounded text-sm block">
                            <?php echo $category['name']; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                    
                    <a href="<?php echo SITE_URL; ?>/pages/user/about.php" class="text-gray-700 hover:text-primary transition py-2 px-3 hover:bg-gray-100 rounded">About</a>
                    <a href="<?php echo SITE_URL; ?>/pages/user/contact.php" class="text-gray-700 hover:text-primary transition py-2 px-3 hover:bg-gray-100 rounded">Contact</a>
                </div>
            </nav>
        </div>
    </header>
    
    <!-- Dropdown Menus JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // User dropdown menu toggle
            const userMenuButton = document.getElementById('user-menu-button');
            const userDropdown = document.getElementById('user-dropdown');
            
            if (userMenuButton && userDropdown) {
                // Toggle dropdown when clicking the button
                userMenuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userDropdown.classList.toggle('hidden');
                    
                    // Close products dropdown if open
                    if (productsDropdown && !productsDropdown.classList.contains('hidden')) {
                        productsDropdown.classList.add('hidden');
                    }
                });
                
                // Ensure dropdown links are clickable
                const userDropdownLinks = userDropdown.querySelectorAll('a');
                userDropdownLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                });
            }
            
            // Products dropdown menu toggle
            const productsMenuButton = document.getElementById('products-menu-button');
            const productsDropdown = document.getElementById('products-dropdown');
            
            if (productsMenuButton && productsDropdown) {
                // Toggle dropdown when clicking the button
                productsMenuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    productsDropdown.classList.toggle('hidden');
                    
                    // Close user dropdown if open
                    if (userDropdown && !userDropdown.classList.contains('hidden')) {
                        userDropdown.classList.add('hidden');
                    }
                });
                
                // Add hover behavior with delay for better user experience
                productsMenuButton.addEventListener('mouseenter', function() {
                    productsDropdown.classList.remove('hidden');
                });
                
                // Ensure dropdown stays open when hovering over it
                productsDropdown.addEventListener('mouseenter', function() {
                    productsDropdown.classList.remove('hidden');
                });
                
                // Add a delay before closing the dropdown when mouse leaves
                let dropdownTimeout;
                
                productsMenuButton.addEventListener('mouseleave', function() {
                    dropdownTimeout = setTimeout(function() {
                        if (!productsDropdown.matches(':hover')) {
                            productsDropdown.classList.add('hidden');
                        }
                    }, 300); // 300ms delay before closing
                });
                
                productsDropdown.addEventListener('mouseleave', function() {
                    dropdownTimeout = setTimeout(function() {
                        if (!productsMenuButton.matches(':hover')) {
                            productsDropdown.classList.add('hidden');
                        }
                    }, 300); // 300ms delay before closing
                });
                
                // Cancel the timeout if user moves back to dropdown or button
                productsMenuButton.addEventListener('mouseenter', function() {
                    clearTimeout(dropdownTimeout);
                });
                
                productsDropdown.addEventListener('mouseenter', function() {
                    clearTimeout(dropdownTimeout);
                });
                
                // Ensure dropdown links are clickable
                const productDropdownLinks = productsDropdown.querySelectorAll('a');
                productDropdownLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                });
            }
            
            // Close dropdowns when clicking elsewhere on the page
            document.addEventListener('click', function(e) {
                if (userMenuButton && userDropdown && !userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) {
                    userDropdown.classList.add('hidden');
                }
                
                if (productsMenuButton && productsDropdown && !productsMenuButton.contains(e.target) && !productsDropdown.contains(e.target)) {
                    productsDropdown.classList.add('hidden');
                }
            });
        });
    </script>
    
    <!-- Flash Messages -->
    <?php
    $flashMessage = getFlashMessage();
    if ($flashMessage):
        $alertClass = '';
        switch ($flashMessage['type']) {
            case 'success':
                $alertClass = 'bg-green-100 border-green-400 text-green-700';
                $icon = 'fa-check-circle';
                break;
            case 'error':
                $alertClass = 'bg-red-100 border-red-400 text-red-700';
                $icon = 'fa-times-circle';
                break;
            case 'warning':
                $alertClass = 'bg-yellow-100 border-yellow-400 text-yellow-700';
                $icon = 'fa-exclamation-circle';
                break;
            default:
                $alertClass = 'bg-blue-100 border-blue-400 text-blue-700';
                $icon = 'fa-info-circle';
        }
    ?>
    <div id="flash-message" class="flash-message border px-4 py-3 rounded relative container mx-auto mt-4 <?php echo $alertClass; ?>">
        <div class="flex items-center">
            <i class="fas <?php echo $icon; ?> mr-2"></i>
            <span><?php echo $flashMessage['message']; ?></span>
        </div>
        <button class="absolute top-0 right-0 mt-3 mr-4" onclick="document.getElementById('flash-message').style.display='none'">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <script>
        setTimeout(function() {
            var flashMessage = document.getElementById('flash-message');
            if (flashMessage) {
                flashMessage.style.opacity = '0';
                setTimeout(function() {
                    flashMessage.style.display = 'none';
                }, 500);
            }
        }, 5000);
    </script>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 py-6">
