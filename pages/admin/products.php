<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_functions.php';

// Require admin privileges
requireAdmin();

// Handle product deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $productId = (int)$_GET['id'];
    
    if (deleteProduct($productId)) {
        setFlashMessage('success', 'Product deleted successfully');
    } else {
        setFlashMessage('error', 'Failed to delete product');
    }
    
    redirect(ADMIN_URL . '/products.php');
}

// Get all products
$products = getAllProducts();

// Get all categories for filter
$categories = getAllCategories();

// Filter by category if provided
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';
if (!empty($categoryFilter)) {
    $filteredProducts = [];
    foreach ($products as $product) {
        if ($product['category_id'] == $categoryFilter) {
            $filteredProducts[] = $product;
        }
    }
    $products = $filteredProducts;
}

// Search functionality
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
if (!empty($searchQuery)) {
    $products = searchProducts($searchQuery);
}

$pageTitle = 'Manage Products';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle . ' - ' . SITE_NAME; ?></title>
    
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
</head>
<body class="bg-gray-100 min-h-screen flex">
    <!-- Sidebar -->
    <aside class="bg-gray-800 text-white w-64 min-h-screen flex flex-col">
        <div class="p-4 bg-gray-900">
            <h2 class="text-2xl font-bold">Beauty<span class="text-primary">Shop</span></h2>
            <p class="text-gray-400 text-sm">Admin Dashboard</p>
        </div>
        
        <nav class="flex-grow">
            <ul class="mt-6">
                <li class="px-4 py-3 hover:bg-gray-700 transition">
                    <a href="<?php echo ADMIN_URL; ?>/dashboard.php" class="flex items-center">
                        <i class="fas fa-tachometer-alt w-6"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="px-4 py-3 bg-gray-700">
                    <a href="<?php echo ADMIN_URL; ?>/products.php" class="flex items-center">
                        <i class="fas fa-box w-6"></i>
                        <span>Products</span>
                    </a>
                </li>
                <li class="px-4 py-3 hover:bg-gray-700 transition">
                    <a href="<?php echo ADMIN_URL; ?>/categories.php" class="flex items-center">
                        <i class="fas fa-tags w-6"></i>
                        <span>Categories</span>
                    </a>
                </li>
                <li class="px-4 py-3 hover:bg-gray-700 transition">
                    <a href="<?php echo ADMIN_URL; ?>/orders.php" class="flex items-center">
                        <i class="fas fa-shopping-cart w-6"></i>
                        <span>Orders</span>
                    </a>
                </li>
                <li class="px-4 py-3 hover:bg-gray-700 transition">
                    <a href="<?php echo ADMIN_URL; ?>/users.php" class="flex items-center">
                        <i class="fas fa-users w-6"></i>
                        <span>Users</span>
                    </a>
                </li>
                <li class="px-4 py-3 hover:bg-gray-700 transition">
                    <a href="<?php echo ADMIN_URL; ?>/settings.php" class="flex items-center">
                        <i class="fas fa-cog w-6"></i>
                        <span>Settings</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <div class="p-4 border-t border-gray-700">
            <a href="<?php echo SITE_URL; ?>/logout.php" class="flex items-center text-gray-400 hover:text-white transition">
                <i class="fas fa-sign-out-alt w-6"></i>
                <span>Logout</span>
            </a>
        </div>
    </aside>
    
    <!-- Main Content -->
    <main class="flex-grow p-6">
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
        <div id="flash-message" class="border px-4 py-3 rounded relative mb-6 <?php echo $alertClass; ?>">
            <div class="flex items-center">
                <i class="fas <?php echo $icon; ?> mr-2"></i>
                <span><?php echo $flashMessage['message']; ?></span>
            </div>
            <button class="absolute top-0 right-0 mt-3 mr-4" onclick="document.getElementById('flash-message').style.display='none'">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php endif; ?>
        
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Manage Products</h1>
            <a href="<?php echo ADMIN_URL; ?>/add-product.php" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-md transition duration-300">
                <i class="fas fa-plus mr-2"></i> Add New Product
            </a>
        </div>
        
        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="mb-4 md:mb-0">
                    <form action="" method="GET" class="flex items-center">
                        <label for="category" class="mr-2 text-gray-700">Filter by Category:</label>
                        <select id="category" name="category" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" <?php echo $categoryFilter == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo $category['name']; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="ml-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-md transition duration-300">
                            Filter
                        </button>
                    </form>
                </div>
                
                <div>
                    <form action="" method="GET" class="flex items-center">
                        <input type="text" name="search" placeholder="Search products..." 
                               value="<?php echo htmlspecialchars($searchQuery); ?>"
                               class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                        <button type="submit" class="ml-2 bg-secondary hover:bg-secondary-dark text-white font-bold py-2 px-4 rounded-md transition duration-300">
                            <i class="fas fa-search mr-1"></i> Search
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Products Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <?php if (empty($products)): ?>
            <div class="p-6 text-center">
                <p class="text-gray-600">No products found. <?php echo !empty($searchQuery) || !empty($categoryFilter) ? 'Try a different search or filter.' : ''; ?></p>
                <a href="<?php echo ADMIN_URL; ?>/add-product.php" class="inline-block mt-4 text-primary hover:text-primary-dark transition">
                    <i class="fas fa-plus mr-1"></i> Add your first product
                </a>
            </div>
            <?php else: ?>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-md object-cover" 
                                         src="<?php echo !empty($product['image']) ? $product['image'] : 'https://via.placeholder.com/100x100?text=No+Image'; ?>" 
                                         alt="<?php echo $product['name']; ?>">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900"><?php echo $product['name']; ?></div>
                                    <div class="text-sm text-gray-500"><?php echo substr($product['description'], 0, 50) . (strlen($product['description']) > 50 ? '...' : ''); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                <?php echo $product['category_name'] ?? 'Uncategorized'; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if (!empty($product['sale_price'])): ?>
                            <div class="text-sm text-gray-500 line-through">$<?php echo number_format($product['price'], 2); ?></div>
                            <div class="text-sm font-medium text-primary">$<?php echo number_format($product['sale_price'], 2); ?></div>
                            <?php else: ?>
                            <div class="text-sm font-medium text-gray-900">$<?php echo number_format($product['price'], 2); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($product['stock'] <= 5): ?>
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                <?php echo $product['stock']; ?> left
                            </span>
                            <?php elseif ($product['stock'] <= 20): ?>
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                <?php echo $product['stock']; ?> left
                            </span>
                            <?php else: ?>
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                <?php echo $product['stock']; ?> in stock
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <?php echo date('M d, Y', strtotime($product['created_at'])); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="<?php echo ADMIN_URL; ?>/edit-product.php?id=<?php echo $product['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="#" onclick="confirmDelete(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>')" class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash-alt"></i> Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
    </main>
    
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 max-w-md w-full">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Confirm Deletion</h3>
            <p class="text-gray-600 mb-6">Are you sure you want to delete <span id="productName" class="font-semibold"></span>? This action cannot be undone.</p>
            <div class="flex justify-end">
                <button onclick="closeDeleteModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-md mr-2 transition duration-300">
                    Cancel
                </button>
                <a id="deleteLink" href="#" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-md transition duration-300">
                    Delete
                </a>
            </div>
        </div>
    </div>
    
    <script>
        // Delete confirmation
        function confirmDelete(productId, productName) {
            document.getElementById('productName').textContent = productName;
            document.getElementById('deleteLink').href = '<?php echo ADMIN_URL; ?>/products.php?action=delete&id=' + productId;
            document.getElementById('deleteModal').classList.remove('hidden');
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
        
        // Close flash message after 5 seconds
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
</body>
</html>
