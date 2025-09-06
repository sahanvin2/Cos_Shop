<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_functions.php';

// Require admin privileges
requireAdmin();

// Get statistics
$db = getDB();

// Total products
$result = $db->query("SELECT COUNT(*) as total FROM products");
$totalProducts = $result->fetch_assoc()['total'];

// Total users
$result = $db->query("SELECT COUNT(*) as total FROM users WHERE user_type = 'user'");
$totalUsers = $result->fetch_assoc()['total'];

// Total orders
$result = $db->query("SELECT COUNT(*) as total FROM orders");
$totalOrders = $result->fetch_assoc()['total'];

// Total revenue
$result = $db->query("SELECT SUM(total_amount) as total FROM orders");
$totalRevenue = $result->fetch_assoc()['total'] ?? 0;

// Recent orders
$result = $db->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");
$recentOrders = [];
while ($row = $result->fetch_assoc()) {
    $recentOrders[] = $row;
}

// Low stock products
$result = $db->query("SELECT * FROM products WHERE stock < 10 ORDER BY stock ASC LIMIT 5");
$lowStockProducts = [];
while ($row = $result->fetch_assoc()) {
    $lowStockProducts[] = $row;
}

$pageTitle = 'Admin Dashboard';
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
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <li class="px-4 py-3 bg-gray-700">
                    <a href="<?php echo ADMIN_URL; ?>/dashboard.php" class="flex items-center">
                        <i class="fas fa-tachometer-alt w-6"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="px-4 py-3 hover:bg-gray-700 transition">
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
            <a href="<?php echo SITE_URL; ?>/logout.php" class="flex items-center text-gray-400 hover:text-white transition bg-gray-700 p-2 rounded-md">
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
        
        <!-- Dashboard Title -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-700">Overview</h2>
            <p class="text-gray-500">Here's what's happening with your store today.</p>
        </div>
        
        <!-- Header with Logout Button -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
            <div class="flex items-center space-x-4">
                <span class="text-gray-600">Welcome, <?php echo $_SESSION['username']; ?></span>
                <a href="<?php echo SITE_URL; ?>/logout.php" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-md transition flex items-center">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-primary-light p-3 rounded-full">
                        <i class="fas fa-box text-primary-dark text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm">Total Products</h3>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $totalProducts; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm">Total Customers</h3>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $totalUsers; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-shopping-cart text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm">Total Orders</h3>
                        <p class="text-2xl font-bold text-gray-800"><?php echo $totalOrders; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-dollar-sign text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm">Total Revenue</h3>
                        <p class="text-2xl font-bold text-gray-800">$<?php echo number_format($totalRevenue, 2); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Sales Overview</h3>
                <div style="height: 300px; position: relative;">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Top Categories</h3>
                <div style="height: 300px; position: relative;">
                    <canvas id="categoriesChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Recent Orders & Low Stock -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-gray-800">Recent Orders</h3>
                    <a href="<?php echo ADMIN_URL; ?>/orders.php" class="text-primary hover:text-primary-dark transition">
                        View All
                    </a>
                </div>
                
                <?php if (empty($recentOrders)): ?>
                <p class="text-gray-600">No orders yet.</p>
                <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($recentOrders as $order): ?>
                            <tr>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <a href="<?php echo ADMIN_URL; ?>/order-details.php?id=<?php echo $order['id']; ?>" class="text-primary hover:text-primary-dark">
                                        #<?php echo $order['id']; ?>
                                    </a>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap"><?php echo $order['username']; ?></td>
                                <td class="py-3 px-3 whitespace-nowrap">$<?php echo number_format($order['total_amount'], 2); ?></td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <?php
                                    $statusClass = '';
                                    switch ($order['status']) {
                                        case 'pending':
                                            $statusClass = 'bg-yellow-100 text-yellow-800';
                                            break;
                                        case 'processing':
                                            $statusClass = 'bg-blue-100 text-blue-800';
                                            break;
                                        case 'shipped':
                                            $statusClass = 'bg-purple-100 text-purple-800';
                                            break;
                                        case 'delivered':
                                            $statusClass = 'bg-green-100 text-green-800';
                                            break;
                                        case 'cancelled':
                                            $statusClass = 'bg-red-100 text-red-800';
                                            break;
                                    }
                                    ?>
                                    <span class="px-2 py-1 text-xs rounded-full <?php echo $statusClass; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap"><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-semibold text-gray-800">Low Stock Products</h3>
                    <a href="<?php echo ADMIN_URL; ?>/products.php" class="text-primary hover:text-primary-dark transition">
                        View All
                    </a>
                </div>
                
                <?php if (empty($lowStockProducts)): ?>
                <p class="text-gray-600">No low stock products.</p>
                <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                                <th class="py-2 px-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($lowStockProducts as $product): ?>
                            <tr>
                                <td class="py-3 px-3">
                                    <a href="<?php echo ADMIN_URL; ?>/edit-product.php?id=<?php echo $product['id']; ?>" class="text-primary hover:text-primary-dark">
                                        <?php echo $product['name']; ?>
                                    </a>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">$<?php echo number_format($product['price'], 2); ?></td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full <?php echo $product['stock'] <= 5 ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                        <?php echo $product['stock']; ?> left
                                    </span>
                                </td>
                                <td class="py-3 px-3 whitespace-nowrap">
                                    <a href="<?php echo ADMIN_URL; ?>/edit-product.php?id=<?php echo $product['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <!-- JavaScript for Charts -->
    <script>
        // Sample data for charts
        document.addEventListener('DOMContentLoaded', function() {
            // Sales Chart
            const salesCtx = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Sales',
                        data: [1200, 1900, 3000, 5000, 2000, 3000, 4500, 5500, 6500, 7000, 8000, 9000],
                        backgroundColor: 'rgba(236, 72, 153, 0.1)',
                        borderColor: 'rgba(236, 72, 153, 1)',
                        borderWidth: 2,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: 10,
                            right: 10,
                            bottom: 10,
                            left: 10
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            suggestedMax: 10000,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value;
                                }
                            },
                            grid: {
                                drawBorder: false
                            }
                        },
                        x: {
                            grid: {
                                drawBorder: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.7)',
                            padding: 10,
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgba(236, 72, 153, 1)',
                            borderWidth: 1,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return '$' + context.parsed.y;
                                }
                            }
                        }
                    }
                }
            });
            
            // Categories Chart
            const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
            const categoriesChart = new Chart(categoriesCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Skin Care', 'Hair Care', 'Body Care', 'Makeup', 'Fragrances'],
                    datasets: [{
                        data: [30, 25, 20, 15, 10],
                        backgroundColor: [
                            'rgba(236, 72, 153, 0.7)',
                            'rgba(59, 130, 246, 0.7)',
                            'rgba(245, 158, 11, 0.7)',
                            'rgba(16, 185, 129, 0.7)',
                            'rgba(139, 92, 246, 0.7)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '60%',
                    layout: {
                        padding: 20
                    },
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                padding: 15,
                                boxWidth: 15,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.7)',
                            padding: 10,
                            titleColor: '#fff',
                            bodyColor: '#fff',
                            borderColor: 'rgba(236, 72, 153, 1)',
                            borderWidth: 1,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.parsed + '%';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
