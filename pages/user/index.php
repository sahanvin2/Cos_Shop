<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/db_functions.php';

// Get featured products
$featuredProducts = getAllProducts(8);

// Get categories
$categories = getAllCategories();

$pageTitle = 'Home';
include_once __DIR__ . '/../../components/header.php';
?>

<!-- Hero Section -->
<section class="relative bg-gray-100 py-12 md:py-24">
    <div class="container mx-auto px-4 flex flex-col md:flex-row items-center">
        <div class="md:w-1/2 md:pr-12 mb-8 md:mb-0">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">Discover Your Natural Beauty</h1>
            <p class="text-lg text-gray-600 mb-6">Experience premium beauty products that enhance your natural beauty and make you feel confident every day.</p>
            <div class="flex flex-wrap gap-4">
                <a href="<?php echo SITE_URL; ?>/pages/user/products.php" class="bg-primary hover:bg-primary-dark text-white font-bold py-3 px-6 rounded-full transition duration-300">
                    Shop Now
                </a>
                <a href="<?php echo SITE_URL; ?>/pages/user/about.php" class="bg-white hover:bg-gray-100 text-primary border border-primary font-bold py-3 px-6 rounded-full transition duration-300">
                    Learn More
                </a>
            </div>
        </div>
        <div class="md:w-1/2">
            <img src="../../assets/images/1st.jpg" alt="Beauty Products" class="rounded-lg shadow-xl">
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="py-12 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Shop by Category</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <?php foreach ($categories as $category): ?>
            <a href="<?php echo SITE_URL; ?>/pages/user/products.php?category=<?php echo $category['slug']; ?>" class="group">
                <div class="bg-gray-100 rounded-lg p-6 text-center transition-transform transform group-hover:scale-105">
                    <?php
                    $iconClass = '';
                    switch ($category['slug']) {
                        case 'skin-care':
                            $iconClass = 'fa-spa';
                            break;
                        case 'hair-care':
                            $iconClass = 'fa-cut';
                            break;
                        case 'body-care':
                            $iconClass = 'fa-shower';
                            break;
                        case 'makeup':
                            $iconClass = 'fa-magic';
                            break;
                        case 'fragrances':
                            $iconClass = 'fa-spray-can';
                            break;
                        default:
                            $iconClass = 'fa-pump-soap';
                    }
                    ?>
                    <i class="fas <?php echo $iconClass; ?> text-4xl text-primary mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-800"><?php echo $category['name']; ?></h3>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Featured Products</h2>
            <a href="<?php echo SITE_URL; ?>/pages/user/products.php" class="text-primary hover:text-primary-dark font-semibold transition">
                View All <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <?php if (empty($featuredProducts)): ?>
        <div class="text-center py-8">
            <p class="text-lg text-gray-600">No products available yet. Check back soon!</p>
        </div>
        <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($featuredProducts as $product): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform transform hover:scale-105 hover:shadow-lg">
                <a href="<?php echo SITE_URL; ?>/pages/user/product-details.php?slug=<?php echo $product['slug']; ?>">
                    <img src="<?php echo !empty($product['image']) ? $product['image'] : 'https://via.placeholder.com/300x300?text=Product+Image'; ?>" 
                         alt="<?php echo $product['name']; ?>" 
                         class="w-full h-64 object-cover">
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
</section>

<!-- Benefits Section -->
<section class="py-12 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">Why Choose Us</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="bg-primary-light w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-truck text-2xl text-primary-dark"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Free Shipping</h3>
                <p class="text-gray-600">Free shipping on all orders over $50</p>
            </div>
            
            <div class="text-center">
                <div class="bg-primary-light w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-undo text-2xl text-primary-dark"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Easy Returns</h3>
                <p class="text-gray-600">30-day money back guarantee</p>
            </div>
            
            <div class="text-center">
                <div class="bg-primary-light w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-shield-alt text-2xl text-primary-dark"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Secure Payment</h3>
                <p class="text-gray-600">100% secure payment processing</p>
            </div>
            
            <div class="text-center">
                <div class="bg-primary-light w-16 h-16 mx-auto rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-headset text-2xl text-primary-dark"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">24/7 Support</h3>
                <p class="text-gray-600">Dedicated support team</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">What Our Customers Say</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center mb-4">
                    <img src="../../assets/images/review3.png" alt="Customer" class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <h4 class="font-semibold text-gray-800">Sarah Johnson</h4>
                        <div class="text-yellow-400 flex">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
                <p class="text-gray-600 italic">"I've been using the Radiance Serum for a month now and my skin has never looked better! The customer service is also exceptional."</p>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center mb-4">
                    <img src="../../assets/images/review1.jpg" alt="Customer" class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <h4 class="font-semibold text-gray-800">Michael Chen</h4>
                        <div class="text-yellow-400 flex">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                    </div>
                </div>
                <p class="text-gray-600 italic">"The hair products here are amazing! My hair feels stronger and looks healthier after just a few weeks of use."</p>
            </div>
            
            <div class="bg-white p-6 rounded-lg shadow-md">
                <div class="flex items-center mb-4">
                    <img src="../../assets/images/review2.jpg" alt="Customer" class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <h4 class="font-semibold text-gray-800">Emily Rodriguez</h4>
                        <div class="text-yellow-400 flex">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                </div>
                <p class="text-gray-600 italic">"Fast shipping and the products are exactly as described. I love that they offer natural and cruelty-free options!"</p>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="py-12 bg-primary">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <div class="md:w-1/2 mb-6 md:mb-0">
                <h2 class="text-3xl font-bold text-white mb-2">Subscribe to Our Newsletter</h2>
                <p class="text-white opacity-90">Get the latest updates, offers and beauty tips delivered to your inbox.</p>
            </div>
            <div class="md:w-1/2">
                <form class="flex flex-col sm:flex-row">
                    <input type="email" placeholder="Your email address" class="flex-grow px-4 py-3 rounded-l-md focus:outline-none">
                    <button type="submit" class="bg-accent hover:bg-accent-dark text-white font-bold px-6 py-3 rounded-r-md transition duration-300 mt-2 sm:mt-0">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include_once __DIR__ . '/../../components/footer.php'; ?>
