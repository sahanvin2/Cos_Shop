<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/db_functions.php';

// Get product slug from URL
$productSlug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (empty($productSlug)) {
    setFlashMessage('error', 'Product not found');
    redirect(SITE_URL . '/pages/user/products.php');
}

// Get product details
$product = getProductBySlug($productSlug);

if (!$product) {
    setFlashMessage('error', 'Product not found');
    redirect(SITE_URL . '/pages/user/products.php');
}

// Get related products from the same category
$relatedProducts = getAllProducts(4, null);

$pageTitle = $product['name'];
include_once __DIR__ . '/../../components/header.php';
?>

<!-- Product Details Page -->
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
                    <a href="<?php echo SITE_URL; ?>/pages/user/products.php" class="text-gray-700 hover:text-primary transition">
                        Products
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="<?php echo SITE_URL; ?>/pages/user/products.php?category=<?php echo $product['category_slug'] ?? ''; ?>" class="text-gray-700 hover:text-primary transition">
                        <?php echo $product['category_name'] ?? 'Category'; ?>
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-primary"><?php echo $product['name']; ?></span>
                </div>
            </li>
        </ol>
    </nav>
    
    <!-- Product Details -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-12">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-6">
            <!-- Product Image -->
            <div class="flex flex-col">
                <div class="mb-4 bg-gray-100 rounded-lg overflow-hidden">
                    <img id="main-image" src="<?php echo !empty($product['image']) ? $product['image'] : 'https://via.placeholder.com/600x600?text=Product+Image'; ?>" 
                         alt="<?php echo $product['name']; ?>" 
                         class="w-full h-96 object-contain">
                </div>
                <div class="grid grid-cols-4 gap-2">
                    <div class="bg-gray-100 rounded-lg overflow-hidden cursor-pointer hover:opacity-80 transition border-2 border-primary">
                        <img src="<?php echo !empty($product['image']) ? $product['image'] : 'https://via.placeholder.com/150x150?text=Image+1'; ?>" 
                             alt="Thumbnail 1" 
                             class="w-full h-20 object-cover thumbnail-image">
                    </div>
                    <div class="bg-gray-100 rounded-lg overflow-hidden cursor-pointer hover:opacity-80 transition">
                        <img src="https://via.placeholder.com/150x150?text=Image+2" 
                             alt="Thumbnail 2" 
                             class="w-full h-20 object-cover thumbnail-image">
                    </div>
                    <div class="bg-gray-100 rounded-lg overflow-hidden cursor-pointer hover:opacity-80 transition">
                        <img src="https://via.placeholder.com/150x150?text=Image+3" 
                             alt="Thumbnail 3" 
                             class="w-full h-20 object-cover thumbnail-image">
                    </div>
                    <div class="bg-gray-100 rounded-lg overflow-hidden cursor-pointer hover:opacity-80 transition">
                        <img src="https://via.placeholder.com/150x150?text=Image+4" 
                             alt="Thumbnail 4" 
                             class="w-full h-20 object-cover thumbnail-image">
                    </div>
                </div>
            </div>
            
            <!-- Product Info -->
            <div>
                <span class="inline-block px-3 py-1 text-xs font-semibold text-primary bg-primary-light rounded-full mb-2">
                    <?php echo $product['category_name'] ?? 'Category'; ?>
                </span>
                
                <h1 class="text-3xl font-bold text-gray-800 mb-2"><?php echo $product['name']; ?></h1>
                
                <div class="flex items-center mb-4">
                    <div class="flex text-yellow-400">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <span class="text-gray-600 ml-2">4.5 (24 reviews)</span>
                </div>
                
                <div class="mb-6">
                    <?php if (!empty($product['sale_price'])): ?>
                    <div class="flex items-center">
                        <span class="text-gray-500 line-through text-xl">$<?php echo number_format($product['price'], 2); ?></span>
                        <span class="text-3xl font-bold text-primary ml-2">$<?php echo number_format($product['sale_price'], 2); ?></span>
                        <?php 
                        $discount = round(($product['price'] - $product['sale_price']) / $product['price'] * 100);
                        ?>
                        <span class="ml-2 px-2 py-1 text-xs font-semibold text-white bg-green-500 rounded-full">
                            <?php echo $discount; ?>% OFF
                        </span>
                    </div>
                    <?php else: ?>
                    <span class="text-3xl font-bold text-gray-800">$<?php echo number_format($product['price'], 2); ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="mb-6">
                    <p class="text-gray-600">
                        <?php echo nl2br($product['description']); ?>
                    </p>
                </div>
                
                <div class="mb-6">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        <span class="text-gray-700">In Stock: <?php echo $product['stock']; ?> available</span>
                    </div>
                    <div class="flex items-center mb-2">
                        <i class="fas fa-truck text-blue-500 mr-2"></i>
                        <span class="text-gray-700">Free shipping on orders over $50</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-undo text-orange-500 mr-2"></i>
                        <span class="text-gray-700">30-day money-back guarantee</span>
                    </div>
                </div>
                
                <form action="<?php echo SITE_URL; ?>/pages/user/cart-actions.php" method="POST">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    
                    <div class="mb-6">
                        <label for="quantity" class="block text-gray-700 font-medium mb-2">Quantity</label>
                        <div class="flex items-center">
                            <button type="button" id="decrease-qty" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-l-md transition duration-300">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" 
                                   class="w-20 py-2 px-3 border-y border-gray-300 text-center focus:outline-none">
                            <button type="button" id="increase-qty" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded-r-md transition duration-300">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex space-x-4">
                        <button type="submit" class="flex-grow bg-primary hover:bg-primary-dark text-white font-bold py-3 px-6 rounded-md transition duration-300">
                            <i class="fas fa-shopping-cart mr-2"></i> Add to Cart
                        </button>
                        <button type="button" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-4 rounded-md transition duration-300">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>
                </form>
                
                <div class="mt-8 border-t border-gray-200 pt-6">
                    <div class="flex items-center space-x-4">
                        <a href="#" class="text-gray-600 hover:text-primary transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-600 hover:text-primary transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-600 hover:text-primary transition">
                            <i class="fab fa-pinterest-p"></i>
                        </a>
                        <a href="#" class="text-gray-600 hover:text-primary transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Product Tabs -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-12">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button id="tab-description" class="tab-button active py-4 px-6 text-center border-b-2 border-primary font-medium text-primary">
                    Description
                </button>
                <button id="tab-additional" class="tab-button py-4 px-6 text-center border-b-2 border-transparent font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Additional Information
                </button>
                <button id="tab-reviews" class="tab-button py-4 px-6 text-center border-b-2 border-transparent font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Reviews (24)
                </button>
            </nav>
        </div>
        
        <div id="tab-content-description" class="tab-content p-6 block">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Product Description</h3>
            <div class="prose max-w-none">
                <p><?php echo nl2br($product['description']); ?></p>
                
                <p class="mt-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed euismod, urna eu tincidunt consectetur, nisi nunc pretium nisi, eget ultrices nisl nunc eu nisi. Sed euismod, urna eu tincidunt consectetur, nisi nunc pretium nisi, eget ultrices nisl nunc eu nisi.</p>
                
                <ul class="mt-4 list-disc list-inside">
                    <li>100% authentic products</li>
                    <li>Dermatologically tested</li>
                    <li>Cruelty-free and vegan</li>
                    <li>Free from harmful chemicals</li>
                    <li>Suitable for all skin types</li>
                </ul>
            </div>
        </div>
        
        <div id="tab-content-additional" class="tab-content p-6 hidden">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Additional Information</h3>
            <table class="min-w-full divide-y divide-gray-200">
                <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="py-3 px-4 text-gray-700 font-medium bg-gray-50">Weight</td>
                        <td class="py-3 px-4 text-gray-600">0.5 kg</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 text-gray-700 font-medium bg-gray-50">Dimensions</td>
                        <td class="py-3 px-4 text-gray-600">10 × 5 × 5 cm</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 text-gray-700 font-medium bg-gray-50">Ingredients</td>
                        <td class="py-3 px-4 text-gray-600">Aqua, Glycerin, Cetearyl Alcohol, Cetyl Alcohol, Butyrospermum Parkii Butter, Simmondsia Chinensis Seed Oil, Tocopherol</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 text-gray-700 font-medium bg-gray-50">Directions</td>
                        <td class="py-3 px-4 text-gray-600">Apply to clean skin morning and evening. Massage gently until absorbed.</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 text-gray-700 font-medium bg-gray-50">Warnings</td>
                        <td class="py-3 px-4 text-gray-600">For external use only. Avoid contact with eyes. Discontinue use if irritation occurs.</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div id="tab-content-reviews" class="tab-content p-6 hidden">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-800">Customer Reviews (24)</h3>
                <button class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-md transition duration-300">
                    Write a Review
                </button>
            </div>
            
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <div class="flex text-yellow-400 mr-2">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <span class="text-gray-600">Based on 24 reviews</span>
                </div>
                
                <div class="space-y-2">
                    <div class="flex items-center">
                        <span class="text-gray-600 w-16">5 stars</span>
                        <div class="flex-grow h-4 mx-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="bg-yellow-400 h-4 rounded-full" style="width: 75%"></div>
                        </div>
                        <span class="text-gray-600 w-16 text-right">18</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-gray-600 w-16">4 stars</span>
                        <div class="flex-grow h-4 mx-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="bg-yellow-400 h-4 rounded-full" style="width: 20%"></div>
                        </div>
                        <span class="text-gray-600 w-16 text-right">5</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-gray-600 w-16">3 stars</span>
                        <div class="flex-grow h-4 mx-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="bg-yellow-400 h-4 rounded-full" style="width: 5%"></div>
                        </div>
                        <span class="text-gray-600 w-16 text-right">1</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-gray-600 w-16">2 stars</span>
                        <div class="flex-grow h-4 mx-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="bg-yellow-400 h-4 rounded-full" style="width: 0%"></div>
                        </div>
                        <span class="text-gray-600 w-16 text-right">0</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-gray-600 w-16">1 star</span>
                        <div class="flex-grow h-4 mx-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="bg-yellow-400 h-4 rounded-full" style="width: 0%"></div>
                        </div>
                        <span class="text-gray-600 w-16 text-right">0</span>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-200 pt-6 space-y-6">
                <!-- Review 1 -->
                <div class="border-b border-gray-200 pb-6">
                    <div class="flex justify-between mb-2">
                        <div class="flex items-center">
                            <img src="https://via.placeholder.com/40x40?text=User" alt="User" class="w-10 h-10 rounded-full mr-3">
                            <div>
                                <h4 class="font-semibold text-gray-800">Sarah Johnson</h4>
                                <div class="flex text-yellow-400 text-sm">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                        <span class="text-gray-500 text-sm">2 weeks ago</span>
                    </div>
                    <h5 class="font-semibold text-gray-800 mb-2">Amazing product, exceeded my expectations!</h5>
                    <p class="text-gray-600">I've been using this product for two weeks now and I can already see a significant difference in my skin. It's much more hydrated and the fine lines around my eyes have diminished. Highly recommend!</p>
                </div>
                
                <!-- Review 2 -->
                <div class="border-b border-gray-200 pb-6">
                    <div class="flex justify-between mb-2">
                        <div class="flex items-center">
                            <img src="https://via.placeholder.com/40x40?text=User" alt="User" class="w-10 h-10 rounded-full mr-3">
                            <div>
                                <h4 class="font-semibold text-gray-800">Michael Chen</h4>
                                <div class="flex text-yellow-400 text-sm">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="far fa-star"></i>
                                </div>
                            </div>
                        </div>
                        <span class="text-gray-500 text-sm">1 month ago</span>
                    </div>
                    <h5 class="font-semibold text-gray-800 mb-2">Great product, but a bit pricey</h5>
                    <p class="text-gray-600">The product works really well and I love the scent. My only complaint is that it's a bit expensive for the size. Would still recommend though!</p>
                </div>
                
                <!-- Review 3 -->
                <div>
                    <div class="flex justify-between mb-2">
                        <div class="flex items-center">
                            <img src="https://via.placeholder.com/40x40?text=User" alt="User" class="w-10 h-10 rounded-full mr-3">
                            <div>
                                <h4 class="font-semibold text-gray-800">Emily Rodriguez</h4>
                                <div class="flex text-yellow-400 text-sm">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star-half-alt"></i>
                                </div>
                            </div>
                        </div>
                        <span class="text-gray-500 text-sm">2 months ago</span>
                    </div>
                    <h5 class="font-semibold text-gray-800 mb-2">Perfect for my sensitive skin</h5>
                    <p class="text-gray-600">I have very sensitive skin and have had reactions to many products in the past. This one is gentle and effective. No irritation at all and my skin looks so much better!</p>
                </div>
            </div>
            
            <div class="mt-6 text-center">
                <button class="text-primary hover:text-primary-dark transition">
                    Load More Reviews
                </button>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <div class="mb-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">You May Also Like</h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($relatedProducts as $relatedProduct): ?>
            <?php if ($relatedProduct['id'] !== $product['id']): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden transition-transform transform hover:scale-105 hover:shadow-lg border border-gray-100">
                <a href="<?php echo SITE_URL; ?>/pages/user/product-details.php?slug=<?php echo $relatedProduct['slug']; ?>">
                    <img src="<?php echo !empty($relatedProduct['image']) ? $relatedProduct['image'] : 'https://via.placeholder.com/300x300?text=Product+Image'; ?>" 
                         alt="<?php echo $relatedProduct['name']; ?>" 
                         class="w-full h-64 object-cover">
                </a>
                <div class="p-4">
                    <span class="text-xs font-semibold text-gray-500 uppercase"><?php echo $relatedProduct['category_name']; ?></span>
                    <a href="<?php echo SITE_URL; ?>/pages/user/product-details.php?slug=<?php echo $relatedProduct['slug']; ?>" class="block">
                        <h3 class="text-lg font-semibold text-gray-800 mt-1 hover:text-primary transition"><?php echo $relatedProduct['name']; ?></h3>
                    </a>
                    <div class="mt-2 flex justify-between items-center">
                        <div>
                            <?php if (!empty($relatedProduct['sale_price'])): ?>
                            <span class="text-gray-500 line-through">$<?php echo number_format($relatedProduct['price'], 2); ?></span>
                            <span class="text-lg font-bold text-primary ml-1">$<?php echo number_format($relatedProduct['sale_price'], 2); ?></span>
                            <?php else: ?>
                            <span class="text-lg font-bold text-gray-800">$<?php echo number_format($relatedProduct['price'], 2); ?></span>
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
                            <input type="hidden" name="product_id" value="<?php echo $relatedProduct['id']; ?>">
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
            <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
    // Quantity input functionality
    document.getElementById('decrease-qty').addEventListener('click', function() {
        const input = document.getElementById('quantity');
        const currentValue = parseInt(input.value);
        if (currentValue > 1) {
            input.value = currentValue - 1;
        }
    });
    
    document.getElementById('increase-qty').addEventListener('click', function() {
        const input = document.getElementById('quantity');
        const currentValue = parseInt(input.value);
        const maxValue = parseInt(input.getAttribute('max'));
        if (currentValue < maxValue) {
            input.value = currentValue + 1;
        }
    });
    
    // Tabs functionality
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            // Remove active class from all buttons
            tabButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.classList.remove('border-primary');
                btn.classList.remove('text-primary');
                btn.classList.add('border-transparent');
                btn.classList.add('text-gray-500');
            });
            
            // Add active class to clicked button
            button.classList.add('active');
            button.classList.add('border-primary');
            button.classList.add('text-primary');
            button.classList.remove('border-transparent');
            button.classList.remove('text-gray-500');
            
            // Hide all tab contents
            tabContents.forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('block');
            });
            
            // Show corresponding tab content
            const tabId = button.id.replace('tab-', 'tab-content-');
            document.getElementById(tabId).classList.remove('hidden');
            document.getElementById(tabId).classList.add('block');
        });
    });
    
    // Thumbnail image click
    const thumbnailImages = document.querySelectorAll('.thumbnail-image');
    const mainImage = document.getElementById('main-image');
    
    thumbnailImages.forEach(image => {
        image.addEventListener('click', () => {
            // Update main image src
            mainImage.src = image.src;
            
            // Update active thumbnail
            thumbnailImages.forEach(img => {
                img.parentElement.classList.remove('border-primary');
            });
            image.parentElement.classList.add('border-primary');
        });
    });
</script>

<?php include_once __DIR__ . '/../../components/footer.php'; ?>
