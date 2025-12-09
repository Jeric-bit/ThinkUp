<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ThinkUp | A Smart Quiz Platform</title>

  <link
    href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Poppins:wght@400;500;600;700&display=swap"
    rel="stylesheet"
  />
  
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
  />

  <style>
    /* =========================================
       SHARED THEME
       ========================================= */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    :root {
      --light-blue: #e4f7fb;
      --light-azure: #caf0f8;
      --teal: #218ca6;
      --navy-blue: #023047;
      --yellow: #ffb703;
      --yellow2: #ffde59;
      --lightyellow: #fffbd5;
      --white: #ffffff;
      --black: #000000;
      --text-gray: #555b66;

      --navy: var(--navy-blue);
      --blue-bg: var(--light-blue);

      --font-heading: "Archivo Black", sans-serif;
      --font-body: "Poppins", sans-serif;
    }

    html,
    body {
      height: 100%;
      scroll-behavior: smooth;
    }

    body {
      font-family: var(--font-body);
      color: var(--navy);
      background-color: var(--blue-bg);
      line-height: 1.7;
      /* Add padding to top so content doesn't hide behind fixed header */
      padding-top: 100px; 
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
      
      /* FIXED POSITIONING */
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 1000;
      
      /* Initial Large Size */
      padding-top: 15px; 
      padding-bottom: 15px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.03);
      
      /* Smooth Transition for Shrink Effect */
      transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    /* Shrink State (Applied via JS) */
    .header.scrolled {
        padding-top: 5px;
        padding-bottom: 5px;
        background-color: rgba(228, 247, 251, 0.95); /* Slight transparency */
        backdrop-filter: blur(10px); /* Glass effect */
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
    .logo-link:hover {
        transform: scale(1.05);
    }

    .logo {
      height: 200px;
      width: 200px;
      object-fit: contain;
      margin-bottom: -50px;
      margin-top: -50px;
      transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    /* Shrink Logo on Scroll */
    .header.scrolled .logo {
        height: 150px;
        width: 150px;
        margin-bottom: -40px;
        margin-top: -40px;
    }

    /* Desktop Navigation */
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

    /* Shrink Button slightly on scroll */
    .header.scrolled .signup-button {
        padding: 10px 25px;
    }

    .signup-button:hover {
      background-color: var(--teal);
      color: var(--white);
      box-shadow: 0 5px 15px rgba(33, 140, 166, 0.3);
    }

    /* Mobile Header Icons */
    .hamburger-btn {
      display: none; 
      background: none;
      border: none;
      font-size: 2.5rem; 
      color: var(--navy-blue);
      cursor: pointer;
      padding: 0;
      line-height: 1;
    }

    /* Mobile Menu Overlay */
    .mobile-menu-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: var(--white);
      z-index: 2000;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      gap: 30px; 
      transform: translateX(100%);
      transition: transform 0.4s cubic-bezier(0.77, 0, 0.175, 1);
    }

    .mobile-menu-overlay.active {
      transform: translateX(0);
    }

    .close-btn {
      position: absolute;
      top: 25px; 
      right: 25px;
      background: none;
      border: none;
      font-size: 2.5rem;
      color: var(--teal); 
      cursor: pointer;
    }

    .mobile-nav-link {
      font-family: var(--font-heading);
      font-size: 1.8rem; 
      color: var(--navy-blue);
      text-decoration: none;
      transition: color 0.3s;
      font-weight: 600;
    }
    
    .menu-divider {
        width: 60px; height: 2px; background-color: #e0e0e0; margin: 10px 0;
    }

    /* ---------- HERO SECTION ---------- */
    .hero-section {
      background-color: var(--blue-bg);
      background-image: url("https://images.unsplash.com/photo-1761264385457-f32b7a1d1e26?auto=format&fit=crop&q=80&w=1665");
      background-size: 100%; 
      background-position: center;
      background-repeat: no-repeat;
      text-align: center;
      padding: 160px 0; 
    }

    .hero-heading {
      font-family: var(--font-heading);
      font-size: clamp(2rem, 5vw, 3.6rem); 
      color: var(--black);
      line-height: 1.3;
      max-width: 800px;
      margin: 0 auto 50px;
      padding: 0 10px;
    }

    .highlight-yellow2 {
      background-color: var(--yellow2);
      color: var(--black);
      padding: 3px 10px;
      border-radius: 5px;
      white-space: nowrap;
      display: inline-block;
    }

    .hero-cta,
    .bottom-cta-button {
      background-color: var(--teal);
      color: var(--white);
      font-family: var(--font-heading);
      padding: 14px 40px; 
      border-radius: 50px;
      font-weight: 400;
      font-size: 1.1rem;
      text-shadow: 0 0 3px rgba(0, 0, 0, 0.2);
      box-shadow: 0 4px 0 0 #218ba675, 0 6px 12px rgba(0, 0, 0, 0.2);
    }

    .hero-cta:hover,
    .bottom-cta-button:hover {
      background-color: #1b8ea4;
      transform: translateY(-2px);
      box-shadow: 0 6px 0 0 #166c7d, 0 8px 16px rgba(0, 0, 0, 0.25);
    }

    /* ---------- FEATURES ---------- */
    .features-overview {
      padding: 100px 0; 
      background-color: var(--white);
    }

    .features-content {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 60px;
    }

    .divider {
      width: 2px;
      background-color: var(--light-azure);
      height: 250px;
      align-self: center;
    }

    .features-text-area {
      flex: 1;
    }

    .features-heading {
      font-family: var(--font-heading);
      font-size: clamp(2rem, 4vw, 3rem); 
      margin-bottom: 30px;
      line-height: 1.2;
      color: var(--navy);
    }
    
    .highlight-lightyellow {
      background-color: var(--lightyellow);
      color: var(--navy);
      padding: 3px 10px;
      border-radius: 5px;
      white-space: nowrap;
    }

    .key-points {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 20px; 
    }

    .point {
      display: flex;
      align-items: center;
      font-weight: 500;
      color: var(--navy);
      font-size: 1.15rem; 
      background-color: var(--blue-bg);
      padding: 15px 20px;
      border-radius: 12px;
      transition: transform 0.2s;
    }
    
    .point:hover {
        transform: translateX(10px);
        background-color: var(--light-azure);
    }

    .point::before {
      content: "💡";
      margin-right: 15px;
      font-size: 1.2em;
    }

    /* ---------- WHY THINKUP (CARDS) ---------- */
    .why-thinkup {
      padding: 100px 0 80px;
      background-color: var(--blue-bg);
      text-align: center;
    }

    .section-title {
      font-family: var(--font-heading);
      font-size: clamp(2rem, 4vw, 3rem);
      margin-bottom: 60px;
      color: var(--navy);
    }

    .highlight-up {
      color: var(--teal);
    }

    .cards-container {
      display: grid;
      grid-template-columns: repeat(4, 1fr); 
      gap: 30px; 
      text-align: center;
    }

    .card {
      background-color: var(--white);
      padding: 40px 20px; 
      border-radius: 24px;
      box-shadow: 0 12px 24px rgba(0, 48, 71, 0.08);
      transition: transform 0.3s, box-shadow 0.3s;
      min-height: 350px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: flex-start;
      height: 100%;
    }

    .card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px rgba(33, 140, 166, 0.15);
    }

    .card-icon {
      width: 170px; 
      height: 160px; 
      margin-bottom: 25px;
      display: block;
      object-fit: contain;
    }

    .card-title {
      font-weight: 800;
      font-size: 1.4rem; 
      margin-bottom: 15px;
      color: var(--navy-blue);
      font-family: var(--font-heading);
      line-height: 1.3;
    }

    .card-subtext {
      color: var(--text-gray);
      font-size: 1rem; 
      font-style: italic;
    }
    
    .card-link {
        text-decoration: none; 
        color: inherit; 
        display: block; 
        height: 100%;
    }

    /* ---------- CTA SECTION ---------- */
    .bottom-cta-section {
      background-color: var(--blue-bg);
      padding: 50px 0 100px;
      text-align: center;
    }

    .bottom-cta-heading {
      font-family: var(--font-heading);
      font-size: clamp(1.8rem, 4vw, 2.5rem);
      margin-bottom: 40px;
      color: var(--black);
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

    .footer-heading {
      font-family: var(--font-heading);
      font-size: 1.1rem;
      color: var(--navy);
      margin-bottom: 20px;
    }

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

    .footer-nav a:hover {
      color: var(--teal);
      transform: translateX(5px);
    }

    .footer-nav a:hover::after {
        width: 100%;
    }

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

    .footer-bottom {
      background-color: var(--yellow2);
      padding: 15px 0;
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--navy);
      text-align: center;
    }

    /* =========================================
       RESPONSIVE MEDIA QUERIES
       ========================================= */

    @media (max-width: 1024px) {
      .cards-container {
        grid-template-columns: repeat(2, 1fr);
        gap: 25px;
      }
      .card-icon {
        width: 150px;
        height: 150px;
      }
      .footer-content {
        grid-template-columns: 1fr 1fr;
      }
    }

    @media (max-width: 768px) {
      .container {
        padding: 0 20px;
      }

      .header {
        padding-top: 15px;
        padding-bottom: 15px;
      }

      .nav-links { display: none; }
      .hamburger-btn { display: block; }
      
      .logo {
        height: 150px; 
        width: 150px;
        margin: 0 0 0 -15px; 
        margin-top: -40px;
        margin-bottom: -40px;
      }

      .hero-section {
        padding: 100px 0 80px 0;
        background-size: 100% 100%; 
      }
      .hero-heading { margin-top: 20px; }
      
      .features-content { flex-direction: column; align-items: center; text-align: center; }
      .divider { display: none; }
      .point { justify-content: center; width: 100%; }

      .cards-container { grid-template-columns: 1fr; }
      .card { min-height: auto; }

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
    <div class="menu-divider"></div>
    <a href="log-in.php" class="mobile-nav-link" onclick="toggleMenu()">Log In</a>
    <a href="sign-up.php" class="mobile-nav-link" onclick="toggleMenu()">Sign Up</a>
  </div>

  <button id="backToTop" class="back-to-top" onclick="scrollToTop()">
    <i class="bi bi-arrow-up"></i>
  </button>

  <section class="hero-section animate-load">
    <div class="container">
      <h1 class="hero-heading">
        A Smart <span class="highlight-yellow2">Quiz Platform</span> Designed for Learning <br />and Sharing
      </h1>
      <a href="log-in.php" class="rounded-button hero-cta">Let’s ThinkUp!</a>
    </div>
  </section>

  <section class="features-overview">
    <div class="container features-content">
      <div class="features-text-area">
        <h2 class="features-heading">
          Not your ordinary <br />quiz site — it’s <br />where ideas <br /><span class="highlight-lightyellow" style="color: var(--yellow);">level up ↑</span>
        </h2>
      </div>

      <div class="divider"></div>

      <div class="key-points">
        <p class="point">Create and take quizzes easily</p>
        <p class="point">Track your scores and progress</p>
        <p class="point">Secure quiz environment</p>
        <p class="point">Teacher and student modes</p>
        <p class="point">Accessible anytime, anywhere</p>
      </div>
    </div>
  </section>

  <section class="why-thinkup">
    <div class="container">
      <h2 class="section-title">Why Think<span class="highlight-up">Up</span>?</h2>
      <div class="cards-container">
        
        <a href="feature-create.php" class="card-link">
            <div class="card">
              <img
                src="https://images.unsplash.com/photo-1761353672906-b63d3f26070c?auto=format&fit=crop&q=80&w=880"
                alt="Create Icon"
                class="card-icon"
              />
              <h3 class="card-title">Create quizzes <br />effortlessly</h3>
              <p class="card-subtext">— make them fun, fast, <br>and focused.</p>
            </div>
        </a>
        
        <a href="feature-track.php" class="card-link">
            <div class="card">
              <img
                src="https://i.pinimg.com/736x/a5/79/1b/a5791b55bad0a35f696bf61abaf5fcde.jpg"
                alt="Track Icon"
                class="card-icon"
              />
              <h3 class="card-title">Track your <br />growth</h3>
              <p class="card-subtext">— see how far you’ve <br>come, one quiz at a time.</p>
            </div>
        </a>

        <a href="feature-collaborate.php" class="card-link">
            <div class="card">
              <img
                src="https://i.pinimg.com/736x/2c/18/62/2c18624e81f7df08a9bd79af9d936b0e.jpg"
                alt="Collab Icon"
                class="card-icon"
              />
              <h3 class="card-title">Collaborate <br>and share</h3>
              <p class="card-subtext">— connect through <br>creativity.</p>
            </div>
        </a>

        <a href="feature-think.php" class="card-link">
            <div class="card">
              <img
                src="https://i.pinimg.com/736x/eb/a0/5b/eba05bcd4afcb6f183b92fa1fdcfc456.jpg"
                alt="Think Icon"
                class="card-icon"
              />
              <h3 class="card-title">Think deeper, <br>not harder</h3>
              <p class="card-subtext">— because smart learning <br>should never feel boring.</p>
            </div>
        </a>

      </div>
    </div>
  </section>

  <section class="bottom-cta-section">
    <div class="container">
      <h2 class="bottom-cta-heading">
        Don't just think — <span style="color: #023047;">Think</span
        ><span style="color: #218ca6;">Up</span>!
      </h2>
      <a href="sign-up.php" class="rounded-button bottom-cta-button">Join today!</a>
    </div>
  </section>

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
          <a href="footer/terms-conditions.php">Terms & Conditions</a>
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

    // Shrinking Header Logic
    window.addEventListener("scroll", function() {
        const header = document.querySelector(".header");
        if (window.scrollY > 20) {
            header.classList.add("scrolled");
        } else {
            header.classList.remove("scrolled");
        }
    });

    // Back to Top Logic
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