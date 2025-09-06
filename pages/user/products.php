<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/db_functions.php';

// Get category filter
$categorySlug = isset($_GET['category']) ? $_GET['category'] : '';
$category = null;

if (!empty($categorySlug)) {
    $category = getCategoryBySlug($categorySlug);
    if ($category) {
        $pageTitle = $category['name'] . ' Products';
    } else {
        // Invalid category, redirect to all products
        redirect(SITE_URL . '/pages/user/products.php');
    }
} else {
    $pageTitle = 'All Products';
}

// Get all categories for sidebar
$categories = getAllCategories();

// Get products based on category
$products = getAllProducts(null, $categorySlug);

include_once __DIR__ . '/../../components/header.php';
?>

<!-- Products Page -->
<div class="container mx-auto px-4 py-8">
    <!-- Breadcrumbs -->
    <nav class="flex mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="<?php echo SITE_URL; ?>" class="text-gray-700 hover:text-primary transition">
                    <i class="fas fa-home mr-2"></i> Home
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <?php if ($category): ?>
                    <a href="<?php echo SITE_URL; ?>/pages/user/products.php" class="text-gray-700 hover:text-primary transition">
                        Products
                    </a>
                    <?php else: ?>
                    <span class="text-primary">Products</span>
                    <?php endif; ?>
                </div>
            </li>
            <?php if ($category): ?>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-primary"><?php echo $category['name']; ?></span>
                </div>
            </li>
            <?php endif; ?>
        </ol>
    </nav>
    
    <div class="flex flex-col md:flex-row">
        <!-- Sidebar -->
        <div class="w-full md:w-1/4 lg:w-1/5 md:pr-8 mb-6 md:mb-0">
            <!-- Categories -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Categories</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="<?php echo SITE_URL; ?>/pages/user/products.php" 
                           class="block py-2 px-3 rounded <?php echo empty($categorySlug) ? 'bg-primary-light text-primary font-semibold' : 'text-gray-700 hover:bg-gray-100'; ?>">
                            All Products
                        </a>
                    </li>
                    <?php foreach ($categories as $cat): ?>
                    <li>
                        <a href="<?php echo SITE_URL; ?>/pages/user/products.php?category=<?php echo $cat['slug']; ?>" 
                           class="block py-2 px-3 rounded <?php echo $categorySlug === $cat['slug'] ? 'bg-primary-light text-primary font-semibold' : 'text-gray-700 hover:bg-gray-100'; ?>">
                            <?php echo $cat['name']; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <!-- Price Filter -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Filter by Price</h3>
                <form action="" method="GET">
                    <?php if ($categorySlug): ?>
                    <input type="hidden" name="category" value="<?php echo $categorySlug; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-4">
                        <label for="min_price" class="block text-gray-700 text-sm font-medium mb-2">Min Price ($)</label>
                        <input type="number" id="min_price" name="min_price" min="0" step="0.01" 
                               value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    
                    <div class="mb-4">
                        <label for="max_price" class="block text-gray-700 text-sm font-medium mb-2">Max Price ($)</label>
                        <input type="number" id="max_price" name="max_price" min="0" step="0.01" 
                               value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    </div>
                    
                    <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-md transition duration-300">
                        Apply Filter
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="w-full md:w-3/4 lg:w-4/5">
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-800 mb-3 sm:mb-0">
                        <?php echo $category ? $category['name'] . ' Products' : 'All Products'; ?>
                    </h1>
                    
                    <div class="flex items-center">
                        <span class="text-gray-600 mr-2">Sort by:</span>
                        <select id="sort-products" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                            <option value="latest">Latest</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                            <option value="name-asc">Name: A to Z</option>
                            <option value="name-desc">Name: Z to A</option>
                        </select>
                    </div>
                </div>
                
                <?php if (empty($products)): ?>
                <div class="text-center py-8">
                    <p class="text-lg text-gray-600 mb-4">No products found in this category.</p>
                    <a href="<?php echo SITE_URL; ?>/pages/user/products.php" class="text-primary hover:text-primary-dark transition">
                        <i class="fas fa-arrow-left mr-1"></i> View all products
                    </a>
                </div>
                <?php else: ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php foreach ($products as $product): ?>
                    <div class="bg-white rounded-lg shadow overflow-hidden transition-transform transform hover:scale-105 hover:shadow-lg border border-gray-100">
                        <a href="<?php echo SITE_URL; ?>/pages/user/product-details.php?slug=<?php echo $product['slug']; ?>">
                        <img src="<?php echo !empty($product['image']) ? htmlspecialchars($product['image']) : 'https://via.placeholder.com/300x300?text=Product+Image'; ?>" 
                            alt="<?php echo htmlspecialchars($product['name']); ?>" 
                            class="w-full h-64 object-cover"
                            onerror="this.src='https://via.placeholder.com/300x300?text=Product+Image';">  
                        </a>
                        <div class="p-4">
                            <span class="text-xs font-semibold text-gray-500 uppercase"><?php echo $product['category_name']; ?></span>
                            <a href="<?php echo SITE_URL; ?>/pages/user/product-details.php?slug=<?php echo $product['slug']; ?>" class="block">
                                <h3 class="text-lg font-semibold text-gray-800 mt-1 hover:text-primary transition"><?php echo $product['name']; ?></h3>
                            </a>
                            <div class="mt-2 flex justify-between items-center">
                                <div>
                                    <?php if (!empty($product['sale_price'])): ?>
                                    <span class="text-gray-500 line-through">$<?php echo number_format($product['price'], 2); ?></span>
                                    <span class="text-lg font-bold text-primary ml-1">$<?php echo number_format($product['sale_price'], 2); ?></span>
                                    <?php else: ?>
                                    <span class="text-lg font-bold text-gray-800">$<?php echo number_format($product['price'], 2); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex items-center">
                                    <div class="text-yellow-400 flex">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                    <span class="text-xs text-gray-500 ml-1">(4.5)</span>
                                </div>
                            </div>
                            <div class="mt-4 flex space-x-2">
                                <form action="<?php echo SITE_URL; ?>/pages/user/cart-actions.php" method="POST" class="flex-grow">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-md transition duration-300">
                                        <i class="fas fa-shopping-cart mr-1"></i> Add to Cart
                                    </button>
                                </form>
                                <button class="bg-gray-200 hover:bg-gray-300 text-gray-800 p-2 rounded-md transition duration-300">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    // Sort products functionality
    document.getElementById('sort-products').addEventListener('change', function() {
        const sortValue = this.value;
        const productCards = document.querySelectorAll('.grid > div');
        const productGrid = document.querySelector('.grid');
        
        const productArray = Array.from(productCards);
        
        productArray.sort((a, b) => {
            if (sortValue === 'price-low') {
                const priceA = parseFloat(a.querySelector('.font-bold').innerText.replace('$', ''));
                const priceB = parseFloat(b.querySelector('.font-bold').innerText.replace('$', ''));
                return priceA - priceB;
            } else if (sortValue === 'price-high') {
                const priceA = parseFloat(a.querySelector('.font-bold').innerText.replace('$', ''));
                const priceB = parseFloat(b.querySelector('.font-bold').innerText.replace('$', ''));
                return priceB - priceA;
            } else if (sortValue === 'name-asc') {
                const nameA = a.querySelector('h3').innerText;
                const nameB = b.querySelector('h3').innerText;
                return nameA.localeCompare(nameB);
            } else if (sortValue === 'name-desc') {
                const nameA = a.querySelector('h3').innerText;
                const nameB = b.querySelector('h3').innerText;
                return nameB.localeCompare(nameA);
            }
            return 0;
        });
        
        // Clear grid and append sorted items
        productGrid.innerHTML = '';
        productArray.forEach(product => {
            productGrid.appendChild(product);
        });
    });
</script>

<?php include_once __DIR__ . '/../../components/footer.php'; ?>
