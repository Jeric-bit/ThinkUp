<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Think Deeper | ThinkUp</title>
  
  <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

  <style>
    /* =========================================
       SHARED THEME
       ========================================= */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      scroll-behavior: smooth;
    }

    :root {
      --light-blue: #e4f7fb;
      --light-azure: #caf0f8;
      --teal: #218ca6;
      --navy-blue: #023047;
      --yellow: #ffb703;
      --yellow2: #ffde59;
      --white: #ffffff;
      --gray-border: #e0e0e0;
      --text-gray: #555b66;

      --navy: var(--navy-blue);
      --blue-bg: var(--light-blue);
      --font-heading: "Archivo Black", sans-serif;
      --font-body: "Poppins", sans-serif;
    }

    body {
      font-family: var(--font-body);
      color: var(--navy);
      background-color: var(--blue-bg);
      line-height: 1.7;
      padding-top: 100px; /* Space for fixed header */
    }

    .container {
      max-width: 1300px;
      margin: 0 auto;
      padding: 0 40px;
    }

    /* Animation */
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .animate-load { animation: fadeUp 0.8s ease-out forwards; }

    /* ---------- BUTTONS ---------- */
    .rounded-button {
      border: none;
      cursor: pointer;
      text-decoration: none;
      transition: background-color 0.3s, transform 0.2s, box-shadow 0.2s, opacity 0.2s;
      color: var(--light-blue);
      display: inline-block;
      text-align: center;
    }

    .rounded-button:hover {
      opacity: 0.95;
      transform: translateY(-1px);
    }

    /* ---------- BACK TO TOP BUTTON ---------- */
    .back-to-top {
      position: fixed;
      bottom: 30px;
      right: 30px;
      background-color: var(--teal);
      color: var(--white);
      width: 50px;
      height: 50px;
      border-radius: 50%;
      border: none;
      font-size: 1.5rem;
      cursor: pointer;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
      opacity: 0;
      visibility: hidden;
      transform: translateY(20px);
      transition: all 0.3s ease;
      z-index: 1000;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .back-to-top.show {
      opacity: 1;
      visibility: visible;
      transform: translateY(0);
    }

    .back-to-top:hover {
      background-color: var(--navy);
      transform: translateY(-5px);
    }

    /* ---------- HEADER (FIXED & SHRINKING) ---------- */
    .header {
      background-color: var(--blue-bg);
      border-bottom-left-radius: 15px;
      border-bottom-right-radius: 15px;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 1000;
      padding-top: 15px; 
      padding-bottom: 15px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.03);
      transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .header.scrolled {
        padding-top: 5px;
        padding-bottom: 5px;
        background-color: rgba(228, 247, 251, 0.95);
        backdrop-filter: blur(10px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        border-radius: 0 0 20px 20px;
    }

    .header-content {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo-link {
        display: block;
        transition: transform 0.2s;
    }
    .logo-link:hover { transform: scale(1.05); }

    .logo {
      height: 200px;
      width: 200px;
      object-fit: contain;
      margin-bottom: -50px;
      margin-top: -50px;
      transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .header.scrolled .logo {
        height: 150px;
        width: 150px;
        margin-bottom: -40px;
        margin-top: -40px;
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 30px;
    }

    .nav-link {
      text-decoration: none;
      font-family: var(--font-heading);
      color: var(--navy);
      font-weight: 500;
      font-size: 1rem; 
      padding: 5px 0;
      position: relative;
      transition: color 0.3s;
    }

    /* Animated Underline */
    .nav-link::after {
        content: '';
        position: absolute;
        width: 0;
        height: 3px;
        bottom: 0;
        left: 0;
        background-color: var(--teal);
        transition: width 0.3s ease;
        border-radius: 2px;
    }

    .nav-link:hover { color: var(--teal); }
    .nav-link:hover::after { width: 100%; }

    .signup-button {
      background-color: var(--navy-blue);
      font-family: var(--font-heading);
      color: var(--white);
      padding: 12px 30px; 
      border-radius: 50px;
      font-size: 14px;
      display: inline-block;
      border: none;
      transition: all 0.3s;
      text-decoration: none;
    }

    .header.scrolled .signup-button { padding: 10px 25px; }

    .signup-button:hover {
      background-color: var(--teal);
      color: var(--white);
      box-shadow: 0 5px 15px rgba(33, 140, 166, 0.3);
    }

    /* Mobile Header Icons */
    .hamburger-btn { display: none; background: none; border: none; font-size: 2.5rem; color: var(--navy-blue); cursor: pointer; padding: 0; line-height: 1; }

    /* Mobile Menu Overlay */
    .mobile-menu-overlay {
      position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: var(--white); z-index: 2000;
      display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 30px; 
      transform: translateX(100%); transition: transform 0.4s cubic-bezier(0.77, 0, 0.175, 1);
    }
    .mobile-menu-overlay.active { transform: translateX(0); }
    .close-btn { position: absolute; top: 25px; right: 25px; background: none; border: none; font-size: 2.5rem; color: var(--teal); cursor: pointer; }
    .mobile-nav-link { font-family: var(--font-heading); font-size: 1.8rem; color: var(--navy-blue); text-decoration: none; transition: color 0.3s; font-weight: 600; }
    .menu-divider { width: 60px; height: 2px; background-color: #e0e0e0; margin: 10px 0; }

    /* =========================================
       FEATURE PAGE STYLES
       ========================================= */
    
    .feature-hero {
      text-align: center;
      padding: 100px 0 60px;
      background: linear-gradient(180deg, var(--blue-bg) 0%, rgba(255,255,255,1) 100%);
    }

    .feature-title {
      font-family: var(--font-heading);
      font-size: clamp(2.5rem, 5vw, 4rem);
      color: var(--navy);
      margin-bottom: 20px;
      line-height: 1.2;
    }

    .feature-subtitle {
      font-size: 1.2rem;
      color: var(--text-gray);
      max-width: 700px;
      margin: 0 auto 40px;
    }

    /* Steps Section */
    .steps-section {
        padding: 80px 0;
        background-color: var(--white);
    }

    .steps-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 40px;
        margin-top: 50px;
    }

    .step-card {
        text-align: center;
        padding: 30px;
        background: var(--light-blue);
        border-radius: 20px;
        position: relative;
        transition: transform 0.3s;
    }
    
    .step-card:hover { transform: translateY(-10px); }

    .step-number {
        background-color: var(--navy); /* Unique accent color */
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: var(--font-heading);
        font-size: 1.5rem;
        color: var(--white);
        margin: 0 auto 20px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .step-title {
        font-family: var(--font-heading);
        font-size: 1.3rem;
        margin-bottom: 10px;
        color: var(--teal);
    }

    /* Tools Grid */
    .tools-section {
        padding: 80px 0;
        background-color: var(--navy);
        color: var(--white);
        text-align: center;
    }
    
    .tools-heading {
        font-family: var(--font-heading);
        font-size: 2.5rem;
        margin-bottom: 50px;
        color: var(--white);
    }

    .tools-grid {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .tool-pill {
        background-color: rgba(255,255,255,0.1);
        padding: 15px 30px;
        border-radius: 50px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        border: 1px solid rgba(255,255,255,0.2);
    }
    
    .tool-pill i { color: var(--yellow); font-size: 1.2rem; }

    /* Bottom CTA */
    .bottom-cta {
        text-align: center;
        padding: 100px 0;
        background-color: var(--white);
    }

    .cta-btn-lg {
        background-color: var(--teal);
        color: white;
        font-family: var(--font-heading);
        padding: 20px 50px;
        border-radius: 50px;
        font-size: 1.2rem;
        text-decoration: none;
        box-shadow: 0 10px 20px rgba(33, 140, 166, 0.3);
        transition: all 0.3s;
    }
    
    .cta-btn-lg:hover {
        background-color: var(--navy);
        transform: translateY(-3px);
    }

    /* ---------- FOOTER ---------- */
    .footer {
      background-color: var(--light-azure);
      padding-top: 60px;
      color: var(--navy);
      border-top: 1px solid rgba(0,0,0,0.05);
    }

    .footer-content {
      display: grid;
      grid-template-columns: 1.5fr 1fr 1fr 1.5fr; /* 4 Columns */
      gap: 40px;
      padding-bottom: 50px;
    }

    /* Brand Column */
    .footer-brand .footer-logo-img {
      height: 160px;
      width: 160px;
      object-fit: contain;
      margin: -40px 0 -30px -10px;
    }
    .footer-brand p {
      font-size: 0.95rem;
      line-height: 1.6;
      color: var(--text-gray);
      margin-bottom: 20px;
    }

    /* Headings */
    .footer-heading {
      font-family: var(--font-heading);
      font-size: 1.1rem;
      color: var(--navy);
      margin-bottom: 20px;
    }

    /* Footer Navigation */
    .footer-nav {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .footer-nav a {
      text-decoration: none;
      color: var(--text-gray);
      font-size: 0.95rem;
      transition: color 0.3s ease, transform 0.3s ease;
      position: relative;
      display: inline-block;
      width: fit-content;
    }

    /* Expanding Underline Effect for Footer Links */
    .footer-nav a::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        bottom: -2px;
        left: 0;
        background-color: var(--teal);
        transition: width 0.3s ease;
    }

    /* Hover Effect */
    .footer-nav a:hover {
      color: var(--teal);
      transform: translateX(5px);
    }

    .footer-nav a:hover::after {
        width: 100%;
    }

    /* Newsletter */
    .newsletter-text {
      font-size: 0.9rem;
      color: var(--text-gray);
      margin-bottom: 15px;
    }

    .newsletter-form {
      display: flex;
      gap: 10px;
    }

    .newsletter-input {
      padding: 10px 15px;
      border: 2px solid white;
      border-radius: 50px;
      flex: 1;
      font-family: var(--font-body);
      font-size: 0.9rem;
      outline: none;
      transition: box-shadow 0.3s;
    }
    
    .newsletter-input:focus {
        box-shadow: 0 0 0 3px rgba(33, 140, 166, 0.2);
    }

    .newsletter-btn {
      background-color: var(--teal);
      color: white;
      border: none;
      border-radius: 50px;
      padding: 0 20px;
      cursor: pointer;
      font-weight: 600;
      transition: transform 0.2s, background-color 0.3s;
    }
    
    .newsletter-btn:hover { 
        background-color: var(--navy); 
        transform: scale(1.1);
    }

    /* Social Icons */
    .social-icons {
      display: flex;
      gap: 15px;
      margin-top: 25px;
    }
    
    .social-icons img {
      width: 32px;
      height: 32px;
      transition: transform 0.3s ease, filter 0.3s ease;
      opacity: 0.85;
    }
    
    .social-icons img:hover {
      transform: translateY(-5px); 
      opacity: 1;
      filter: drop-shadow(0 5px 5px rgba(0,0,0,0.2));
    }

    /* Footer Bottom */
    .footer-bottom {
      background-color: var(--yellow2);
      padding: 15px 0;
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--navy);
      text-align: center;
    }

    /* RESPONSIVE */
    @media (max-width: 1024px) {
      .footer-content {
        grid-template-columns: 1fr 1fr;
      }
    }

    @media (max-width: 992px) {
        .steps-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 768px) {
      .container { padding: 0 20px; }
      
      /* Header */
      .header { padding-top: 15px; padding-bottom: 15px; }
      .nav-links { display: none; }
      .hamburger-btn { display: block; }
      .logo { margin: 0 0 0 -15px; margin-top: -40px; margin-bottom: -40px; height: 150px; width: 150px; }

      /* Feature Styles */
      .tools-grid { gap: 10px; }
      .tool-pill { width: 100%; justify-content: center; }
      
      /* Footer */
      .footer-content { grid-template-columns: 1fr; text-align: center; }
      .footer-brand .footer-logo-img { margin: 0 auto -20px; }
      .footer-nav a:hover { padding-left: 0; color: var(--teal); transform: none; }
      .footer-nav a { display: block; }
      .social-icons { justify-content: center; }
    }
  </style>
</head>
<body>

  <header class="header">
    <div class="container header-content">
      <a href="index.php" class="logo-link">
        <img
          src="https://images.unsplash.com/vector-1761327026877-26de041584b8?auto=format&fit=crop&q=80&w=880"
          alt="ThinkUp Logo"
          class="logo"
        />
      </a>
      
      <nav class="nav-links">
        <a href="index.php" class="nav-link">Home</a>
        <a href="aboutus.php" class="nav-link">About Us</a>
        <a href="log-in.php" class="nav-link">Log In</a>
        <a href="sign-up.php" class="signup-button">Sign Up</a>
      </nav>

      <button class="hamburger-btn" onclick="toggleMenu()">
        <i class="bi bi-list"></i>
      </button>
    </div>
  </header>

  <div class="mobile-menu-overlay" id="mobileMenu">
    <button class="close-btn" onclick="toggleMenu()">
      <i class="bi bi-x-lg"></i>
    </button>
    <a href="index.php" class="mobile-nav-link" onclick="toggleMenu()">Home</a>
    <a href="aboutus.php" class="mobile-nav-link" onclick="toggleMenu()">About Us</a>
    <div style="width:50px; height:2px; background:#ccc; margin: 10px 0;"></div>
    <a href="log-in.php" class="mobile-nav-link" onclick="toggleMenu()">Log In</a>
    <a href="sign-up.php" class="mobile-nav-link" onclick="toggleMenu()">Sign Up</a>
  </div>

  <button id="backToTop" class="back-to-top" onclick="scrollToTop()">
    <i class="bi bi-arrow-up"></i>
  </button>

  <main>
    <section class="feature-hero animate-load">
      <div class="container">
        <h1 class="feature-title">Think Deeper, <span style="color: var(--teal);">Not Harder</span></h1>
        <p class="feature-subtitle">
          Smart learning should never feel boring. Dive into topics that matter with innovative quiz formats designed to challenge your critical thinking and retention.
        </p>
        <img src="https://i.pinimg.com/736x/eb/a0/5b/eba05bcd4afcb6f183b92fa1fdcfc456.jpg" alt="Critical Thinking Icon" style="max-height: 300px; object-fit: contain; margin-top: 20px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
      </div>
    </section>

    <section class="steps-section">
      <div class="container">
        <h2 style="text-align: center; font-family: var(--font-heading); color: var(--navy); font-size: 2.5rem;">The Smart Way</h2>
        
        <div class="steps-grid">
          <div class="step-card">
            <div class="step-number">1</div>
            <h3 class="step-title">Focus</h3>
            <p>Our distraction-free interface keeps you in the zone, helping you absorb information faster.</p>
          </div>
          
          <div class="step-card">
            <div class="step-number">2</div>
            <h3 class="step-title">Analyze</h3>
            <p>Move beyond simple memorization with questions that ask you to apply logic and reasoning.</p>
          </div>
          
          <div class="step-card">
            <div class="step-number">3</div>
            <h3 class="step-title">Retain</h3>
            <p>Reinforce what you've learned through spaced repetition and instant feedback loops.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="tools-section">
      <div class="container">
        <h2 class="tools-heading">Tools for Thinking</h2>
        <div class="tools-grid">
          <div class="tool-pill"><i class="bi bi-puzzle-fill"></i> Logic Puzzles</div>
          <div class="tool-pill"><i class="bi bi-stopwatch-fill"></i> Timed Challenges</div>
          <div class="tool-pill"><i class="bi bi-card-text"></i> Flashcards</div>
          <div class="tool-pill"><i class="bi bi-book-half"></i> Study Modes</div>
          <div class="tool-pill"><i class="bi bi-lightbulb-fill"></i> Hint System</div>
          <div class="tool-pill"><i class="bi bi-check-circle-fill"></i> Explanations</div>
        </div>
      </div>
    </section>

    <section class="bottom-cta">
      <div class="container">
        <h2 style="font-family: var(--font-heading); font-size: 2.5rem; margin-bottom: 30px; color: var(--navy);">Unlock your potential</h2>
        <a href="sign-up.php" class="cta-btn-lg">Start Thinking Smarter</a>
      </div>
    </section>

  </main>

  <footer class="footer">
    <div class="container footer-content">
      
      <div class="footer-brand">
        <img src="https://images.unsplash.com/vector-1761361180979-ec71362bbcfb?auto=format&fit=crop&q=80&w=880" alt="ThinkUp Logo" class="footer-logo-img">
        <p>A smart quiz platform designed for learning, sharing, and connecting curious minds everywhere.</p>
        <div class="social-icons">
          <a href="#"><img src="https://upload.wikimedia.org/wikipedia/commons/4/4e/Gmail_Icon.png" alt="Email"></a>
          <a href="#"><img src="https://upload.wikimedia.org/wikipedia/commons/6/6c/Facebook_Logo_2023.png" alt="Facebook"></a>
          <a href="#"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/95/Instagram_logo_2022.svg/1200px-Instagram_logo_2022.svg.png?20220518162235" alt="Instagram"></a>
        </div>
      </div>

      <div class="footer-col">
        <h4 class="footer-heading">Company</h4>
        <nav class="footer-nav">
          <a href="aboutus.php">About Us</a>
          <a href="terms-conditions.php">Terms & Conditions</a>
          <a href="#">Careers</a>
        </nav>
      </div>

      <div class="footer-col">
        <h4 class="footer-heading">Support</h4>
        <nav class="footer-nav">
          <a href="faqs.php">FAQs</a>
          <a href="#">Contact Us</a>
          <a href="privacy-policy.php">Privacy Policy</a>
          <a href="terms-conditions.php">Terms & Conditions</a>
        </nav>
      </div>

      <div class="footer-col">
        <h4 class="footer-heading">Stay Updated</h4>
        <p class="newsletter-text">Join our newsletter to get the latest quiz challenges and updates.</p>
        <form class="newsletter-form" action="#" method="POST" onsubmit="event.preventDefault(); alert('Subscribed!');">
          <input type="email" placeholder="Enter email" class="newsletter-input" required>
          <button type="submit" class="newsletter-btn"><i class="bi bi-arrow-right"></i></button>
        </form>
      </div>

    </div>
    
    <div class="footer-bottom">
        <div class="container">
            © 2025 ThinkUp. All Rights Reserved.
        </div>
    </div>
  </footer>

  <script>
    // Toggle Mobile Menu
    function toggleMenu() {
      const menu = document.getElementById("mobileMenu");
      menu.classList.toggle("active");
    }

    // Shrinking Header
    window.addEventListener("scroll", function() {
        const header = document.querySelector(".header");
        if (window.scrollY > 20) {
            header.classList.add("scrolled");
        } else {
            header.classList.remove("scrolled");
        }
    });

    // Back to Top
    const backToTopBtn = document.getElementById("backToTop");
    window.addEventListener("scroll", function() {
      if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
        backToTopBtn.classList.add("show");
      } else {
        backToTopBtn.classList.remove("show");
      }
    });

    function scrollToTop() {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  </script>
</body>
</html>