    </main>
    
    <!-- Footer -->
    <footer class="bg-gray-800 text-white pt-12 pb-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- About -->
                <div>
                    <h3 class="text-xl font-semibold mb-4">About BeautyShop</h3>
                    <p class="text-gray-300 mb-4">
                        Your one-stop destination for premium beauty products. We offer a wide range of skincare, haircare, and makeup products to enhance your natural beauty.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-300 hover:text-primary transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-primary transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-primary transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-gray-300 hover:text-primary transition">
                            <i class="fab fa-pinterest-p"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Categories -->
                <div>
                    <h3 class="text-xl font-semibold mb-4">Categories</h3>
                    <ul class="space-y-2">
                        <?php
                        foreach ($categories as $category) {
                            echo '<li><a href="' . SITE_URL . '/pages/user/products.php?category=' . $category['slug'] . '" class="text-gray-300 hover:text-primary transition">' . $category['name'] . '</a></li>';
                        }
                        ?>
                    </ul>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-xl font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="<?php echo SITE_URL; ?>" class="text-gray-300 hover:text-primary transition">Home</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/user/about.php" class="text-gray-300 hover:text-primary transition">About Us</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/user/products.php" class="text-gray-300 hover:text-primary transition">Shop</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/user/contact.php" class="text-gray-300 hover:text-primary transition">Contact Us</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/user/faq.php" class="text-gray-300 hover:text-primary transition">FAQ</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/user/shipping-policy.php" class="text-gray-300 hover:text-primary transition">Shipping Policy</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/user/privacy-policy.php" class="text-gray-300 hover:text-primary transition">Privacy Policy</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/user/terms.php" class="text-gray-300 hover:text-primary transition">Terms & Conditions</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <h3 class="text-xl font-semibold mb-4">Contact Us</h3>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3 text-primary"></i>
                            <span class="text-gray-300">No. 123, Galle Road, Colombo 07</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt mr-3 text-primary"></i>
                            <span class="text-gray-300">+94 (123) 456-7890</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3 text-primary"></i>
                            <span class="text-gray-300">info@beautyshop.com</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-clock mr-3 text-primary"></i>
                            <span class="text-gray-300">Mon-Fri: 6AM - 11PM</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <hr class="border-gray-700 my-8">
            
            <!-- Bottom Footer -->
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-gray-400 text-sm">
                    &copy; <?php echo date('Y'); ?> BeautyShop. All rights reserved.
                </p>
                <div class="mt-4 md:mt-0">
                    <img src="../../assets/images/pm.jpg" alt="Payment Methods" class="h-6">
                </div>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileSearch = document.getElementById('mobile-search');
            
            if (mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.remove('hidden');
                mobileSearch.classList.remove('hidden');
            } else {
                mobileMenu.classList.add('hidden');
                mobileSearch.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
