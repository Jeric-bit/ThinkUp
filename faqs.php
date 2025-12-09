<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>FAQs | ThinkUp</title>
  
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
    }

    .container {
      max-width: 1300px;
      margin: 0 auto;
      padding: 0 40px;
    }

    /* ---------- HEADER ---------- */
    .header {
      background-color: var(--blue-bg);
      border-bottom-left-radius: 15px;
      border-bottom-right-radius: 15px;
      padding-top: 10px; 
      padding-bottom: 10px;
      position: relative;
      z-index: 100;
      box-shadow: 0 4px 20px rgba(0,0,0,0.03);
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
      border: 2px solid transparent;
    }

    .signup-button:hover {
      background-color: var(--white);
      color: var(--navy-blue);
      border: 2px solid var(--navy-blue);
      box-shadow: 0 5px 15px rgba(2, 48, 71, 0.2);
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
       FAQ PAGE STYLES
       ========================================= */
    
    .faq-hero {
      text-align: center;
      padding: 80px 0 40px;
    }

    .faq-title {
      font-family: var(--font-heading);
      font-size: clamp(2rem, 5vw, 3.5rem);
      color: var(--navy);
      margin-bottom: 20px;
      line-height: 1.2;
    }

    /* Search Bar */
    .faq-search-container {
        max-width: 600px;
        margin: 0 auto;
        position: relative;
    }

    .faq-search-input {
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

    .faq-search-input:focus {
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

    .content-card {
      background: var(--white);
      padding: 60px;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(2, 48, 71, 0.05);
      width: 100%;
      max-width: 900px; 
    }

    /* --- CATEGORY TABS --- */
    .category-tabs {
        display: flex;
        justify-content: center;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 40px;
        border-bottom: 1px solid #f0f0f0;
        padding-bottom: 20px;
    }

    .tab-link {
        text-decoration: none;
        color: var(--text-gray);
        font-weight: 600;
        padding: 8px 20px;
        border-radius: 30px;
        background-color: var(--light-blue);
        transition: all 0.3s;
    }

    .tab-link:hover, .tab-link.active {
        background-color: var(--navy);
        color: var(--white);
        transform: translateY(-2px);
    }

    /* --- ACCORDION STYLES --- */
    .faq-section {
      margin-bottom: 40px;
      scroll-margin-top: 20px;
    }

    .section-title {
      font-family: var(--font-heading);
      font-size: 1.3rem;
      color: var(--teal);
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 10px;
      text-transform: uppercase;
      letter-spacing: 1px;
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
        font-size: 1.05rem;
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

    /* Rotate icon when active */
    .accordion-header.active i {
        transform: rotate(180deg);
    }

    .accordion-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
        padding: 0 10px;
    }

    .accordion-content p {
        padding-bottom: 20px;
        color: var(--text-gray);
        font-size: 0.95rem;
        line-height: 1.6;
    }

    /* No Results Message */
    .no-results {
        display: none;
        text-align: center;
        padding: 20px;
        color: var(--text-gray);
        font-size: 1.1rem;
    }

    /* Support Box */
    .support-box {
        background-color: var(--light-blue);
        border-radius: 15px;
        padding: 30px;
        text-align: center;
        margin-top: 50px;
    }

    .support-box h3 {
        font-family: var(--font-heading);
        color: var(--navy);
        margin-bottom: 10px;
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
    .newsletter-input { padding: 10px 15px; border: 2px solid white; border-radius: 50px; flex: 1; font-family: var(--font-body); font-size: 0.9rem; outline: none; transition: box-shadow 0.3s; }
    .newsletter-input:focus { box-shadow: 0 0 0 3px rgba(33, 140, 166, 0.2); }
    .newsletter-btn { background-color: var(--teal); color: white; border: none; border-radius: 50px; padding: 0 20px; cursor: pointer; font-weight: 600; transition: transform 0.2s, background-color 0.3s; }
    .newsletter-btn:hover { background-color: var(--navy); transform: scale(1.1); }

    /* Social Icons */
    .social-icons { display: flex; gap: 15px; margin-top: 25px; }
    .social-icons img { width: 32px; height: 32px; transition: transform 0.3s ease, filter 0.3s ease; opacity: 0.85; }
    .social-icons img:hover { transform: translateY(-5px); opacity: 1; filter: drop-shadow(0 5px 5px rgba(0,0,0,0.2)); }

    /* Footer Bottom */
    .footer-bottom { background-color: var(--yellow2); padding: 15px 0; font-size: 0.9rem; font-weight: 600; color: var(--navy); text-align: center; }

    /* RESPONSIVE */
    @media (max-width: 1024px) { .footer-content { grid-template-columns: 1fr 1fr; } }
    @media (max-width: 992px) { .content-card { padding: 40px; } .faq-hero { padding: 40px 0 30px; } }
    @media (max-width: 768px) {
      .container { padding: 0 20px; }
      .header { padding-top: 15px; padding-bottom: 15px; }
      .nav-links { display: none; }
      .hamburger-btn { display: block; }
      .logo { margin: 0 0 0 -15px; margin-top: -40px; margin-bottom: -40px; height: 150px; width: 150px; }
      .faq-hero { padding: 30px 0 20px; }
      .center-wrapper { padding-bottom: 60px; }
      .content-card { padding: 30px 20px; border-radius: 15px; }
      .category-tabs { overflow-x: auto; flex-wrap: nowrap; justify-content: flex-start; padding-bottom: 10px; }
      .tab-link { white-space: nowrap; }
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

  <main>
    <section class="faq-hero">
      <div class="container">
        <h1 class="faq-title">How can we help?</h1>
        <div class="faq-search-container">
            <i class="bi bi-search search-icon"></i>
            <input type="text" class="faq-search-input" id="searchInput" placeholder="Search for questions...">
        </div>
      </div>
    </section>

    <div class="container center-wrapper">
      <div class="content-card">
        
        <div class="category-tabs">
            <a href="#general" class="tab-link active">General</a>
            <a href="#account" class="tab-link">Account</a>
            <a href="#quizzes" class="tab-link">Quizzes</a>
            <a href="#troubleshoot" class="tab-link">Troubleshooting</a>
        </div>

        <div id="faqContainer">
            <div id="general" class="faq-section">
              <h2 class="section-title"><i class="bi bi-info-circle-fill"></i> General Questions</h2>
              
              <div class="accordion-item">
                <button class="accordion-header">What is ThinkUp? <i class="bi bi-chevron-down"></i></button>
                <div class="accordion-content">
                    <p>ThinkUp is a smart and interactive quiz platform designed to help users learn, share knowledge, and challenge themselves. Whether you are a student, teacher, or just curious, ThinkUp makes learning fun and accessible.</p>
                </div>
              </div>

              <div class="accordion-item">
                <button class="accordion-header">Is ThinkUp free to use? <i class="bi bi-chevron-down"></i></button>
                <div class="accordion-content">
                    <p>Yes! ThinkUp is free for all users. You can create an account, take unlimited quizzes, and track your progress without any cost.</p>
                </div>
              </div>
            </div>

            <div id="account" class="faq-section">
              <h2 class="section-title"><i class="bi bi-person-fill-gear"></i> Account & Settings</h2>
              
              <div class="accordion-item">
                <button class="accordion-header">How do I create an account? <i class="bi bi-chevron-down"></i></button>
                <div class="accordion-content">
                    <p>Simply click the "Sign Up" button at the top right corner of the page. Fill in your details, and you're ready to start thinking up!</p>
                </div>
              </div>

              <div class="accordion-item">
                <button class="accordion-header">I forgot my password. What should I do? <i class="bi bi-chevron-down"></i></button>
                <div class="accordion-content">
                    <p>Don't worry. Go to the Log In page and click on "Forgot Password." Follow the instructions sent to your email to reset it securely.</p>
                </div>
              </div>
            </div>

            <div id="quizzes" class="faq-section">
              <h2 class="section-title"><i class="bi bi-lightbulb-fill"></i> Creating & Taking Quizzes</h2>
              
              <div class="accordion-item">
                <button class="accordion-header">How do I create a quiz? <i class="bi bi-chevron-down"></i></button>
                <div class="accordion-content">
                    <p>Once logged in, navigate to your dashboard and select "Create Quiz." You can add questions, set time limits, and choose categories. Once you're done, publish it for others to see!</p>
                </div>
              </div>

              <div class="accordion-item">
                <button class="accordion-header">Can I share my quiz with friends? <i class="bi bi-chevron-down"></i></button>
                <div class="accordion-content">
                    <p>Absolutely. After creating a quiz, you will get a unique link. Share that link with your friends, students, or colleagues so they can challenge your score.</p>
                </div>
              </div>
            </div>

            <div id="troubleshoot" class="faq-section">
              <h2 class="section-title"><i class="bi bi-tools"></i> Troubleshooting</h2>
              
              <div class="accordion-item">
                <button class="accordion-header">The quiz isn't loading. What's wrong? <i class="bi bi-chevron-down"></i></button>
                <div class="accordion-content">
                    <p>Please check your internet connection first. If the problem persists, try refreshing the page or clearing your browser cache. If it still doesn't work, contact support.</p>
                </div>
              </div>

              <div class="accordion-item">
                <button class="accordion-header">How do I report a bug or issue? <i class="bi bi-chevron-down"></i></button>
                <div class="accordion-content">
                    <p>We appreciate your help in making ThinkUp better! Please send an email to <a href="mailto:support@thinkup.com" class="help-link">support@thinkup.com</a> with details about the issue.</p>
                </div>
              </div>
            </div>
        </div>
        
        <p id="noResults" class="no-results">No results found matching your search.</p>

        <div class="support-box">
            <h3>Still have questions?</h3>
            <p style="color: var(--text-gray); margin-bottom: 20px;">We’re here to help you get the most out of ThinkUp.</p>
            <a href="mailto:support@thinkup.com" class="rounded-button" style="background-color: var(--navy); color: white; padding: 12px 30px; border-radius: 50px;">Contact Support</a>
        </div>

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

    // ACCORDION LOGIC
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

    // SEARCH LOGIC
    const searchInput = document.getElementById('searchInput');
    const sections = document.querySelectorAll('.faq-section');
    const noResultsMsg = document.getElementById('noResults');

    searchInput.addEventListener('keyup', function(e) {
        const term = e.target.value.toLowerCase();
        let totalMatches = 0;

        sections.forEach(section => {
            const items = section.querySelectorAll('.accordion-item');
            let sectionHasMatch = false;

            items.forEach(item => {
                const question = item.querySelector('.accordion-header').innerText.toLowerCase();
                const answer = item.querySelector('.accordion-content').innerText.toLowerCase();

                if(question.includes(term) || answer.includes(term)) {
                    item.style.display = 'block';
                    sectionHasMatch = true;
                    totalMatches++;
                } else {
                    item.style.display = 'none';
                }
            });

            // Hide/Show entire section based on matches
            if (sectionHasMatch) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        });

        // Toggle No Results Message
        if (totalMatches === 0) {
            noResultsMsg.style.display = 'block';
        } else {
            noResultsMsg.style.display = 'none';
        }
    });
  </script>
</body>
</html>