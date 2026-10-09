</main>

<footer class="main-footer">
    <div class="footer-container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="<?= SITE_URL ?>/index.php" class="logo">
                    <span class="logo-icon"><i class="fas fa-store"></i></span>
                    <span class="logo-text"><?= SITE_NAME ?></span>
                </a>
                <p>Your one-stop marketplace connecting buyers with trusted vendors worldwide.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            
            <div class="footer-links">
                <h4>Shop</h4>
                <a href="<?= SITE_URL ?>/products.php">All Products</a>
                <a href="<?= SITE_URL ?>/vendors.php">Vendors</a>
                <a href="<?= SITE_URL ?>/register.php">Create Account</a>
                <a href="<?= SITE_URL ?>/register.php">Become a Seller</a>
            </div>
            
            <div class="footer-links">
                <h4>Support</h4>
                <a href="#">Help Center</a>
                <a href="#">Shipping Info</a>
                <a href="#">Returns Policy</a>
                <a href="#">Contact Us</a>
            </div>
            
            <div class="footer-newsletter">
                <h4>Stay Updated</h4>
                <p>Get exclusive deals and updates.</p>
                <form class="newsletter-form" onsubmit="event.preventDefault();">
                    <input type="email" placeholder="Your email">
                    <button type="submit"><i class="fas fa-paper-plane"></i></button>
                </form>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
            <div class="footer-bottom-links">
                <a href="#">Privacy</a>
                <a href="#">Terms</a>
                <a href="<?= SITE_URL ?>/login.php">Sign In</a>
            </div>
        </div>
    </div>
</footer>

<script src="<?= SITE_URL ?>/assets/js/main.js"></script>
</body>
</html>