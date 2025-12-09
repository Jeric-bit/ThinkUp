<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Privacy Policy | ThinkUp</title>
  
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

    /* Animation Keyframes */
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .animate-load {
      animation: fadeUp 0.8s ease-out forwards;
    }

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
       PRIVACY PAGE CONTENT STYLES
       ========================================= */
    
    .privacy-hero {
      text-align: center;
      padding: 80px 0 40px;
    }

    .privacy-title {
      font-family: var(--font-heading);
      font-size: clamp(2rem, 5vw, 3.5rem);
      color: var(--navy);
      margin-bottom: 10px;
      line-height: 1.2;
    }

    .last-updated-text {
      color: var(--text-gray);
      font-weight: 500;
      font-size: 0.95rem;
      margin-bottom: 30px;
      display: block;
    }

    /* Search Bar */
    .search-container {
        max-width: 600px;
        margin: 0 auto;
        position: relative;
    }

    .search-input {
        width: 100%;
        padding: 15px 20px 15px 50px;
        border-radius: 50px;
        border: 2px solid var(--white);
        font-size: 1rem;
        outline: none;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        transition: all 0.3s;
        font-family: var(--font-body);
    }

    .search-input:focus {
        border-color: var(--teal);
        box-shadow: 0 5px 20px rgba(33, 140, 166, 0.15);
    }

    .search-icon {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-gray);
        font-size: 1.2rem;
    }

    .center-wrapper {
      display: flex;
      justify-content: center;
      padding-bottom: 100px;
    }

    /* Content Card */
    .content-card {
      background: var(--white);
      padding: 60px;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(2, 48, 71, 0.05);
      width: 100%;
      max-width: 900px; 
      position: relative;
    }

    /* Print Button */
    .print-btn {
        position: absolute;
        top: 30px;
        right: 30px;
        background-color: var(--light-azure);
        color: var(--teal);
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        font-size: 0.9rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .print-btn:hover {
        background-color: var(--teal);
        color: var(--white);
    }

    /* Quick Nav Tabs */
    .category-tabs {
        display: flex;
        justify-content: center;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 40px;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 20px;
        margin-top: 20px;
    }

    .tab-link {
        text-decoration: none;
        color: var(--text-gray);
        font-weight: 600;
        padding: 8px 20px;
        border-radius: 30px;
        background-color: var(--light-blue);
        transition: all 0.3s;
        cursor: pointer;
    }

    .tab-link:hover {
        background-color: var(--navy);
        color: var(--white);
        transform: translateY(-2px);
    }

    /* Accordion Styles */
    .policy-section {
      margin-bottom: 40px;
      scroll-margin-top: 40px;
    }

    .accordion-item {
        border-bottom: 1px solid #eee;
        margin-bottom: 10px;
    }

    .accordion-header {
        width: 100%;
        background: none;
        border: none;
        outline: none;
        padding: 20px 10px;
        text-align: left;
        font-family: var(--font-body);
        font-weight: 600;
        font-size: 1.1rem;
        color: var(--navy);
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: color 0.3s;
    }

    .accordion-header:hover {
        color: var(--teal);
    }

    .accordion-header i {
        transition: transform 0.3s ease;
        font-size: 0.9rem;
        color: var(--teal);
    }

    .accordion-header.active i {
        transform: rotate(180deg);
    }

    .accordion-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
        padding: 0 10px;
    }

    .text-block {
        color: var(--text-gray);
        margin-bottom: 20px;
        text-align: justify; 
        line-height: 1.8;
    }

    .custom-list {
        list-style: none;
        margin: 10px 0 20px;
        padding-left: 10px;
    }

    .custom-list li {
        position: relative;
        padding-left: 35px;
        margin-bottom: 15px;
        color: var(--text-gray);
    }

    .custom-list li::before {
        content: "\F26A";
        font-family: "bootstrap-icons";
        position: absolute;
        left: 0;
        top: 4px;
        color: var(--teal);
        font-size: 1.1rem;
        font-weight: bold;
    }

    .email-link {
        color: var(--teal);
        font-weight: 700;
        text-decoration: none;
        border-bottom: 2px solid var(--yellow2);
        transition: background-color 0.2s;
        word-break: break-all;
    }
    
    .email-link:hover {
        background-color: var(--yellow2);
        color: var(--navy);
    }

    .no-results { display: none; text-align: center; color: var(--text-gray); margin-top: 20px; }

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
    .footer-brand .footer-logo-img { height: 160px; width: 160px; object-fit: contain; margin: -40px 0 -30px -10px; }
    .footer-brand p { font-size: 0.95rem; line-height: 1.6; color: var(--text-gray); margin-bottom: 20px; }

    /* Headings */
    .footer-heading { font-family: var(--font-heading); font-size: 1.1rem; color: var(--navy); margin-bottom: 20px; }

    /* Footer Navigation */
    .footer-nav { display: flex; flex-direction: column; gap: 12px; }
    .footer-nav a { text-decoration: none; color: var(--text-gray); font-size: 0.95rem; transition: color 0.3s ease, transform 0.3s ease; position: relative; display: inline-block; width: fit-content; }
    .footer-nav a::after { content: ''; position: absolute; width: 0; height: 2px; bottom: -2px; left: 0; background-color: var(--teal); transition: width 0.3s ease; }
    .footer-nav a:hover { color: var(--teal); transform: translateX(5px); }
    .footer-nav a:hover::after { width: 100%; }

    /* Newsletter */
    .newsletter-text { font-size: 0.9rem; color: var(--text-gray); margin-bottom: 15px; }
    .newsletter-form { display: flex; gap: 10px; }
    .newsletter-input { padding: 10px 15px; border: 2px solid white; border-radius: 50px; flex: 1; outline: none; transition: box-shadow 0.3s; }
    .newsletter-input:focus { box-shadow: 0 0 0 3px rgba(33, 140, 166, 0.2); }
    .newsletter-btn { background-color: var(--teal); color: white; border: none; border-radius: 50px; padding: 0 20px; cursor: pointer; transition: transform 0.2s, background-color 0.3s; }
    .newsletter-btn:hover { background-color: var(--navy); transform: scale(1.1); }

    /* Social Icons */
    .social-icons { display: flex; gap: 15px; margin-top: 25px; }
    .social-icons img { width: 32px; height: 32px; transition: transform 0.3s ease, filter 0.3s ease; opacity: 0.85; }
    .social-icons img:hover { transform: translateY(-5px); opacity: 1; filter: drop-shadow(0 5px 5px rgba(0,0,0,0.2)); }

    /* Footer Bottom */
    .footer-bottom { background-color: var(--yellow2); padding: 15px 0; font-size: 0.9rem; font-weight: 600; color: var(--navy); text-align: center; }

    /* RESPONSIVE */
    @media (max-width: 1024px) { .footer-content { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 992px) { .content-card { padding: 40px; } .privacy-hero { padding: 40px 0 30px; } }
    @media (max-width: 768px) {
      .container { padding: 0 20px; }
      .header { padding-top: 15px; padding-bottom: 15px; }
      .nav-links { display: none; }
      .hamburger-btn { display: block; }
      .logo { margin: 0 0 0 -15px; margin-top: -40px; margin-bottom: -40px; height: 150px; width: 150px; }
      
      .category-tabs { overflow-x: auto; flex-wrap: nowrap; justify-content: flex-start; padding-bottom: 10px; }
      .tab-link { white-space: nowrap; }
      .print-btn { position: relative; top: 0; right: 0; margin-bottom: 20px; width: 100%; justify-content: center; }

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
    <section class="privacy-hero">
      <div class="container">
        <h1 class="privacy-title">Privacy Policy</h1>
        <p class="last-updated-text">Last Updated: March 10, 2025</p>
        
        <div class="search-container">
            <i class="bi bi-search search-icon"></i>
            <input type="text" class="search-input" id="searchInput" placeholder="Search policy details...">
        </div>
      </div>
    </section>

    <div class="container center-wrapper">
      <div class="content-card">
        
        <button class="print-btn" onclick="window.print()"><i class="bi bi-printer"></i> Print Policy</button>

        <p class="text-block" style="font-size: 1.1rem; text-align: center; margin-top: 40px;">
          Welcome to ThinkUp. Your privacy is paramount to us. This policy outlines exactly how we collect, use, and protect your personal information while you learn and grow on our platform.
        </p>

        <nav class="category-tabs">
          <a href="#collection" class="tab-link">Collection</a>
          <a href="#usage" class="tab-link">Usage</a>
          <a href="#sharing" class="tab-link">Sharing</a>
          <a href="#security" class="tab-link">Security</a>
          <a href="#contact" class="tab-link">Contact</a>
        </nav>

        <div id="policyContainer">
            
            <div id="collection" class="accordion-item">
                <button class="accordion-header">
                    1. Information We Collect <i class="bi bi-chevron-down"></i>
                </button>
                <div class="accordion-content">
                    <p class="text-block">To provide the best learning experience, we collect specific types of data when you interact with our services:</p>
                    <ul class="custom-list">
                        <li><strong>Personal Information:</strong> Name, email address, and account preferences provided during sign-up.</li>
                        <li><strong>Activity Data:</strong> Quiz scores, completion times, and topics you create or follow.</li>
                        <li><strong>Technical Data:</strong> Device type, browser version, and IP address for security auditing.</li>
                    </ul>
                </div>
            </div>

            <div id="usage" class="accordion-item">
                <button class="accordion-header">
                    2. How We Use Your Data <i class="bi bi-chevron-down"></i>
                </button>
                <div class="accordion-content">
                    <p class="text-block">We don't hoard data; we use it to make ThinkUp work for you. Specifically, we use it to:</p>
                    <ul class="custom-list">
                        <li>Create and maintain your personal user dashboard.</li>
                        <li>Calculate your progress and generate performance insights.</li>
                        <li>Facilitate the sharing of quizzes between teachers and students.</li>
                        <li>Detect and prevent fraudulent activity to keep the community safe.</li>
                    </ul>
                </div>
            </div>

            <div id="sharing" class="accordion-item">
                <button class="accordion-header">
                    3. Data Sharing <i class="bi bi-chevron-down"></i>
                </button>
                <div class="accordion-content">
                    <p class="text-block">We respect your trust. <strong>We do not sell your personal data</strong> to third-party advertisers. We may share data only under these strict conditions:</p>
                    <ul class="custom-list">
                        <li>With your explicit consent (e.g., connecting a social media account).</li>
                        <li>To comply with valid legal processes or government requests.</li>
                        <li>With trusted service providers who help host our platform (bound by confidentiality).</li>
                    </ul>
                </div>
            </div>

            <div id="security" class="accordion-item">
                <button class="accordion-header">
                    4. Security Measures <i class="bi bi-chevron-down"></i>
                </button>
                <div class="accordion-content">
                    <p class="text-block">
                        We implement industry-standard security measures, including encryption and secure server infrastructure, to maintain the safety of your personal information. However, please remember that no method of transmission over the Internet is 100% secure.
                    </p>
                </div>
            </div>

            <div id="contact" class="accordion-item">
                <button class="accordion-header">
                    5. Contact Us <i class="bi bi-chevron-down"></i>
                </button>
                <div class="accordion-content">
                    <p class="text-block">
                        Have questions about your data? We are here to help. Reach out to our Data Privacy Officer directly at:
                    </p>
                    <a href="mailto:privacy@thinkup.com" class="email-link">privacy@thinkup.com</a>
                </div>
            </div>

        </div>
        
        <p id="noResults" class="no-results">No sections found matching your search.</p>

      </div>
    </div>
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

    // Accordion Logic
    const acc = document.getElementsByClassName("accordion-header");
    for (let i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function() {
            this.classList.toggle("active");
            const panel = this.nextElementSibling;
            if (panel.style.maxHeight) {
                panel.style.maxHeight = null;
            } else {
                panel.style.maxHeight = panel.scrollHeight + "px";
            }
        });
    }

    // Search Logic (Real-time Filtering)
    const searchInput = document.getElementById('searchInput');
    const items = document.querySelectorAll('.accordion-item');
    const noResultsMsg = document.getElementById('noResults');

    searchInput.addEventListener('keyup', function(e) {
        const term = e.target.value.toLowerCase();
        let matches = 0;

        items.forEach(item => {
            const text = item.innerText.toLowerCase();
            if(text.includes(term)) {
                item.style.display = 'block';
                matches++;
            } else {
                item.style.display = 'none';
            }
        });

        if (matches === 0) {
            noResultsMsg.style.display = 'block';
        } else {
            noResultsMsg.style.display = 'none';
        }
    });
  </script>
</body>
</html>