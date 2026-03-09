<?php
// index.php
session_start();
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get featured products
$featured_query = "SELECT * FROM products WHERE featured = TRUE LIMIT 4";
$featured_stmt = $db->prepare($featured_query);
$featured_stmt->execute();
$featured_products = $featured_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get reviews
$reviews_query = "SELECT * FROM reviews WHERE is_featured = TRUE ORDER BY id DESC LIMIT 6";
$reviews_stmt = $db->prepare($reviews_query);
$reviews_stmt->execute();
$reviews = $reviews_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get contact info
$contact_query = "SELECT * FROM contact_info LIMIT 1";
$contact_stmt = $db->prepare($contact_query);
$contact_stmt->execute();
$contact = $contact_stmt->fetch(PDO::FETCH_ASSOC);

// Get categories
$categories = [
    ['name' => 'HOME', 'icon' => '🏠'],
    ['name' => 'CLOTHING', 'icon' => '👕'],
    ['name' => 'ELECTRONICS', 'icon' => '📱'],
    ['name' => 'ACCESSORIES', 'icon' => '⌚']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maison - Modern Living Store</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        body {
            background-color: #fafafa;
            color: #1a1a1a;
        }

        /* Navigation */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 5%;
            background-color: white;
            border-bottom: 1px solid #eee;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 300;
            letter-spacing: 2px;
        }

        .nav-links {
            display: flex;
            gap: 2.5rem;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            font-size: 0.9rem;
            font-weight: 400;
            letter-spacing: 0.5px;
        }

        .nav-links a:hover {
            color: #000;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(105deg, #f8f8f8 0%, #f8f8f8 50%, #e8e8e8 50%);
            padding: 4rem 5%;
            display: flex;
            align-items: center;
            min-height: 500px;
        }

        .hero-content {
            max-width: 600px;
        }

        .hero-subtitle {
            font-size: 0.9rem;
            letter-spacing: 3px;
            color: #666;
            margin-bottom: 1rem;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 300;
            line-height: 1.2;
            margin-bottom: 1.5rem;
        }

        .hero-description {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 1rem 2rem;
            text-decoration: none;
            border: 1px solid #333;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: #333;
            color: white;
        }

        .btn-primary:hover {
            background-color: #000;
        }

        .btn-outline {
            background-color: transparent;
            color: #333;
        }

        .btn-outline:hover {
            background-color: #333;
            color: white;
        }

        /* Products Section */
        .products-section {
            padding: 4rem 5%;
            background-color: white;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.8rem;
            font-weight: 300;
        }

        .section-subtitle {
            color: #666;
            font-size: 0.9rem;
            letter-spacing: 2px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
        }

        .product-card {
            background: white;
            border: 1px solid #f0f0f0;
            padding: 1.5rem;
            transition: transform 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .product-category {
            color: #999;
            font-size: 0.8rem;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }

        .product-name {
            font-size: 1.1rem;
            font-weight: 400;
            margin-bottom: 0.5rem;
        }

        .product-price {
            font-size: 1.3rem;
            font-weight: 300;
            color: #333;
            margin-bottom: 1rem;
        }

        .product-rating {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            color: #f4b400;
        }

        .product-add {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            background-color: #333;
            color: white;
            text-decoration: none;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
        }

        /* Categories Section */
        .categories-section {
            padding: 4rem 5%;
            background-color: #f8f8f8;
        }

        .categories-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            margin-top: 2rem;
        }

        .category-card {
            background: white;
            padding: 2rem;
            text-align: center;
            border: 1px solid #eee;
        }

        .category-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .category-name {
            font-size: 1.2rem;
            font-weight: 400;
            margin-bottom: 0.5rem;
        }

        .category-link {
            color: #666;
            text-decoration: none;
            font-size: 0.9rem;
        }

        /* Features Section */
        .features-section {
            padding: 4rem 5%;
            background-color: white;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
        }

        .feature-card {
            text-align: center;
        }

        .feature-title {
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .feature-description {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        /* Testimonials Section */
        .testimonials-section {
            padding: 4rem 5%;
            background-color: #f8f8f8;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            margin-top: 2rem;
        }

        .testimonial-card {
            background: white;
            padding: 2rem;
            border: 1px solid #eee;
        }

        .testimonial-rating {
            color: #f4b400;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .testimonial-text {
            color: #555;
            line-height: 1.6;
            margin-bottom: 1.5rem;
            font-style: italic;
        }

        .testimonial-author {
            font-weight: 500;
            color: #333;
        }

        /* Gallery Section */
        .gallery-section {
            padding: 4rem 5%;
            background-color: white;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .gallery-item {
            aspect-ratio: 1;
            background-color: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
        }

        /* Contact Section */
        .contact-section {
            padding: 4rem 5%;
            background-color: #f8f8f8;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
        }

        .contact-info {
            padding-right: 2rem;
        }

        .contact-info h3 {
            font-size: 2rem;
            font-weight: 300;
            margin-bottom: 2rem;
        }

        .contact-detail {
            margin-bottom: 1.5rem;
        }

        .contact-detail-label {
            font-weight: 500;
            margin-bottom: 0.3rem;
        }

        .contact-detail-value {
            color: #666;
        }

        .contact-form input,
        .contact-form textarea {
            width: 100%;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #ddd;
            background: white;
        }

        .contact-form button {
            padding: 1rem 2rem;
            background-color: #333;
            color: white;
            border: none;
            cursor: pointer;
        }

        /* Footer */
        .footer {
            padding: 2rem 5%;
            text-align: center;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 0.9rem;
        }

        /* Admin Links */
        .admin-link {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #333;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="logo">MAISON</div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="shop.php">Shop</a>
            <a href="#about">About</a>
            <a href="#reviews">Reviews</a>
            <a href="#contact">Contact</a>
            <a href="https://wa.me/<?php echo $contact['whatsapp']; ?>" target="_blank">WhatsApp</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-subtitle">NEW COLLECTION 2026</div>
            <h1 class="hero-title">Curated for Modern Living</h1>
            <p class="hero-description">Discover thoughtfully designed products that blend simplicity with elegance for your everyday life.</p>
            <div class="hero-buttons">
                <a href="shop.php" class="btn btn-primary">Shop Now ➡️</a>
                <a href="https://wa.me/<?php echo $contact['whatsapp']; ?>" class="btn btn-outline">Order on WhatsApp</a>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products-section" id="shop">
        <div class="section-header">
            <div>
                <div class="section-subtitle">HANDPICKED</div>
                <h2 class="section-title">Featured Products</h2>
            </div>
        </div>
        <div class="products-grid">
            <?php foreach($featured_products as $product): ?>
            <div class="product-card">
                <div class="product-category"><?php echo $product['category']; ?></div>
                <h3 class="product-name"><?php echo $product['name']; ?></h3>
                <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                <div class="product-rating">★ <?php echo $product['rating']; ?></div>
                <button class="product-add" onclick="addToCart(<?php echo $product['id']; ?>)">Add</button>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section">
        <div class="section-header">
            <div>
                <div class="section-subtitle">BROWSE</div>
                <h2 class="section-title">Shop by Category</h2>
            </div>
        </div>
        <div class="categories-grid">
            <?php foreach($categories as $category): ?>
            <div class="category-card">
                <div class="category-icon"><?php echo $category['icon']; ?></div>
                <h3 class="category-name"><?php echo $category['name']; ?></h3>
                <a href="shop.php?category=<?php echo $category['name']; ?>" class="category-link">Explore →</a>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section" id="about">
        <div class="feature-card">
            <h3 class="feature-title">Quality Products</h3>
            <p class="feature-description">Every item is carefully curated and quality-checked before reaching you.</p>
        </div>
        <div class="feature-card">
            <h3 class="feature-title">Fast Delivery</h3>
            <p class="feature-description">Same-day dispatch and express shipping options available nationwide.</p>
        </div>
        <div class="feature-card">
            <h3 class="feature-title">Secure Payments</h3>
            <p class="feature-description">Multiple safe payment options including cards and WhatsApp ordering.</p>
        </div>
        <div class="feature-card">
            <h3 class="feature-title">Customer Support</h3>
            <p class="feature-description">Dedicated support team ready to help you 7 days a week via WhatsApp.</p>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section class="testimonials-section" id="reviews">
        <div class="section-header">
            <div>
                <div class="section-subtitle">TESTIMONIALS</div>
                <h2 class="section-title">What Our Customers Say</h2>
            </div>
        </div>
        <div class="testimonials-grid">
            <?php foreach($reviews as $review): ?>
            <div class="testimonial-card">
                <div class="testimonial-rating"><?php echo str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?></div>
                <p class="testimonial-text">"<?php echo htmlspecialchars($review['comment']); ?>"</p>
                <div class="testimonial-author">
                    <strong><?php echo $review['customer_initial']; ?></strong> <?php echo htmlspecialchars($review['customer_name']); ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="gallery-section">
        <div class="section-header">
            <div>
                <div class="section-subtitle">INSPIRATION</div>
                <h2 class="section-title">Our Collection</h2>
            </div>
        </div>
        <div class="gallery-grid">
            <?php 
            // Get gallery images
            $gallery_query = "SELECT * FROM gallery LIMIT 3";
            $gallery_stmt = $db->prepare($gallery_query);
            $gallery_stmt->execute();
            $gallery_items = $gallery_stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if(count($gallery_items) > 0): 
                foreach($gallery_items as $item): 
            ?>
            <div class="gallery-item">
                <img src="uploads/<?php echo $item['image']; ?>" alt="<?php echo $item['title']; ?>" style="width:100%; height:100%; object-fit:cover;">
            </div>
            <?php 
                endforeach; 
            else: 
                for($i = 1; $i <= 3; $i++): 
            ?>
            <div class="gallery-item">Gallery <?php echo $i; ?></div>
            <?php 
                endfor;
            endif; 
            ?>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section" id="contact">
        <div class="contact-info">
            <div class="section-subtitle">GET IN TOUCH</div>
            <h3>Contact Us</h3>
            <p style="color:#666; margin-bottom:2rem;">We'd love to hear from you</p>
            
            <div class="contact-detail">
                <div class="contact-detail-label">Phone</div>
                <div class="contact-detail-value"><?php echo $contact['phone']; ?></div>
            </div>
            <div class="contact-detail">
                <div class="contact-detail-label">Email</div>
                <div class="contact-detail-value"><?php echo $contact['email']; ?></div>
            </div>
            <div class="contact-detail">
                <div class="contact-detail-label">Address</div>
                <div class="contact-detail-value"><?php echo $contact['address']; ?></div>
            </div>
        </div>
        
        <div class="contact-form">
            <form action="send_message.php" method="POST">
                <input type="text" name="name" placeholder="Your Name" value="John Doe" required>
                <input type="email" name="email" placeholder="Email Address" value="john@example.com" required>
                <textarea name="message" placeholder="Your Message" rows="5"></textarea>
                <button type="submit">Send Message</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 MAISON. All rights reserved.</p>
    </footer>

    <!-- Admin Link -->
    <a href="admin/login.php" class="admin-link">Admin Panel</a>

    <script>
        function addToCart(productId) {
            alert('Product added to cart! (Demo functionality)');
        }
    </script>
</body>
</html>