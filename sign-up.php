<?php
// sign-up.php
session_start();
// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: home.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>ThinkUp | Sign Up</title>
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Archivo+Black&display=swap" rel="stylesheet" />
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <script src="https://accounts.google.com/gsi/client" async defer></script>

  <style>
    /* ========== CSS VARIABLES ========== */
    :root {
      /* Colors matched to your theme */
      --color-text: #000000;
      --color-text-dark: #023047; /* Navy Blue */
      --color-subtle: #A6A6A6;
      --color-bg: #e4f7fb;       /* Light Blue Background */
      --color-primary: #218ca6;  /* Teal */
      --color-primary-light: rgba(33, 140, 166, 0.1);
      --color-white: #FFFFFF;
      --color-border: #218ca6;
      --color-border-hover: #1a6d80;
      
      /* Typography */
      --font-primary: 'Poppins', sans-serif;
      --font-heading: 'Archivo Black', sans-serif;
      
      /* Spacing & Radius */
      --radius-lg: 100px; /* Pill Shape */
      
      /* Shadows */
      --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
      --shadow-md: 0 4px 8px rgba(0, 0, 0, 0.08);
    }
    
    /* ========== RESET & BASE ========== */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    body {
      font-family: var(--font-primary);
      background-color: var(--color-bg);
      color: var(--color-text);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      position: relative;
      /* Padding top accounts for fixed header */
      padding: 120px 20px 40px; 
    }

    /* =========================================
       HEADER / NAVBAR (Updated from aboutus.php)
       ========================================= */
    .header {
      background-color: var(--color-bg);
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

    /* Shrink State */
    .header.scrolled {
        padding-top: 5px;
        padding-bottom: 5px;
        background-color: rgba(228, 247, 251, 0.95);
        backdrop-filter: blur(10px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        border-radius: 0 0 20px 20px;
    }

    .container {
      max-width: 1300px;
      margin: 0 auto;
      padding: 0 40px;
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
      color: var(--color-text-dark);
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
        background-color: var(--color-primary);
        transition: width 0.3s ease;
        border-radius: 2px;
    }
    .nav-link:hover { color: var(--color-primary); }
    .nav-link:hover::after { width: 100%; }

    /* Nav Button Style - UPDATED FOR VERTICAL CENTERING */
    .signup-button {
      background-color: var(--color-text-dark);
      font-family: var(--font-heading);
      color: var(--color-white);
      
      /* Flexbox for centering */
      display: flex;
      align-items: center;     /* Vertical Center */
      justify-content: center; /* Horizontal Center */
      
      padding: 0 30px; 
      height: 47px; /* Fixed Height */
      
      border-radius: 50px;
      font-size: 14px;
      border: none;
      text-decoration: none;
      transition: all 0.3s;
    }
    
    .header.scrolled .signup-button { padding: 0 25px; } /* Adjusted padding for scrolled state if needed */
    
    .signup-button:hover {
      background-color: var(--color-primary);
      color: var(--color-white);
      box-shadow: 0 5px 15px rgba(33, 140, 166, 0.3);
    }

    /* Mobile Menu Icons */
    .hamburger-btn {
      display: none; 
      background: none;
      border: none;
      font-size: 2.5rem; 
      color: var(--color-text-dark);
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
      background-color: var(--color-white);
      z-index: 2000;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      gap: 30px; 
      transform: translateX(100%);
      transition: transform 0.4s cubic-bezier(0.77, 0, 0.175, 1);
    }
    .mobile-menu-overlay.active { transform: translateX(0); }

    .close-btn {
      position: absolute;
      top: 25px; 
      right: 25px;
      background: none;
      border: none;
      font-size: 2.5rem; 
      color: var(--color-primary); 
      cursor: pointer;
    }

    .mobile-nav-link {
      font-family: var(--font-heading);
      font-size: 1.8rem; 
      color: var(--color-text-dark);
      text-decoration: none;
      transition: color 0.3s;
      font-weight: 600;
    }
    .menu-divider { width: 60px; height: 2px; background-color: #e0e0e0; margin: 10px 0; }

    /* ========== SIGNUP CONTAINER ========== */
    .signup-container {
      text-align: center;
      width: 100%;
      max-width: 520px;
      animation: fadeInUp 0.4s ease;
    }
    
    @keyframes fadeInUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    
    /* ========== TYPOGRAPHY ========== */
    .heading-1 {
      font-family: var(--font-heading);
      font-size: 2rem;
      font-weight: 900;
      color: var(--color-text);
      margin-bottom: 8px;
      line-height: 1.2;
    }
    
    .heading-1__highlight { color: var(--color-text-dark); }
    .heading-1__accent { color: var(--color-primary); }
    
    .heading-2 {
      font-family: var(--font-heading);
      font-size: 1rem;
      color: var(--color-text);
      margin-bottom: 32px;
      font-weight: 500;
    }
    
    .body-text {
      font-size: 1rem;
      color: var(--color-text);
    }
    
    .body-text--link {
      color: var(--color-primary);
      text-decoration: none;
      font-weight: 800;
      position: relative;
    }
    .body-text--link:hover { text-decoration: underline; }

    /* ========== AUTH BUTTONS ========== */
    .auth-buttons {
      display: flex;
      flex-direction: column;
      gap: 16px;
      margin-bottom: 24px;
    }
    
    .auth-button {
      position: relative;
      width: 100%;
      max-width: 360px;
      margin: 0 auto;
      padding: 24px 32px; 
      border-radius: var(--radius-lg); 
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 1.1rem;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 16px;
      background-color: var(--color-white);
      color: var(--color-text-dark);
      border: 2px solid var(--color-border);
      font-weight: 500;
      overflow: hidden;
      box-shadow: var(--shadow-sm);
    }
    
    .auth-button:hover {
      background-color: var(--color-primary-light);
      border-color: var(--color-border-hover);
      border-width: 3px;
      font-weight: 600;
      transform: translateY(-2px);
      box-shadow: var(--shadow-md);
    }
    
    .auth-button:active {
      transform: translateY(0);
      transition: transform 0.1s ease;
    }
    
    .auth-button--email { margin-top: 8px; }
    
    .auth-button__icon { width: 22px; height: 22px; flex-shrink: 0; }
    .auth-button__icon--custom { font-size: 1.2rem; color: var(--color-primary); }
    
    /* ========== DIVIDER ========== */
    .divider {
      position: relative;
      margin: 24px auto;
      color: var(--color-text);
      text-transform: lowercase;
      font-family: var(--font-heading);
      font-size: 1rem;
      font-weight: 500;
      width: 100%;
      max-width: 360px;
      text-align: center;
    }
    
    .divider::before, .divider::after {
      content: '';
      position: absolute;
      height: 2px;
      width: 40%;
      background: var(--color-subtle);
      top: 50%;
      transform: translateY(-50%);
    }
    .divider::before { left: 0; }
    .divider::after { right: 0; }
    
    /* ========== RESPONSIVE STYLES ========== */
    @media (max-width: 768px) {
      .container { padding: 0 20px; }
      
      /* Header adjustments */
      .nav-links { display: none; }
      .hamburger-btn { display: block; }
      
      .logo {
        height: 150px; 
        width: 150px;
        margin: 0 0 0 -15px; 
        margin-top: -40px;
        margin-bottom: -40px;
      }
      
      .heading-1 { font-size: 1.75rem; }
      .auth-button { padding: 16px 24px; font-size: 1rem; }
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
    <a href="sign-up.php" class="mobile-nav-link" onclick="toggleMenu()" style="color:var(--color-primary);">Sign Up</a>
  </div>

  <div id="g_id_onload"
       data-client_id="YOUR_GOOGLE_CLIENT_ID" 
       data-callback="handleCredentialResponse"
       data-auto_select="false">
  </div>

  <main class="signup-container">
    <header class="text-center mb-xl">
      <h1 class="heading-1">
        Join the community at
        <span class="heading-1__highlight">Think</span><span class="heading-1__accent">Up</span>!
      </h1>
      <p class="heading-2">Choose a way to sign up</p>
    </header>
    
    <div class="auth-buttons">
      
      <button 
        type="button" 
        class="auth-button" 
        id="google-auth"
        aria-label="Continue with Google"
      >
        <img 
          src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" 
          alt="Google Icon" 
          class="auth-button__icon"
          loading="lazy"
        >
        <span class="auth-button__text">Continue with Google</span>
      </button>
      
      <button 
        type="button" 
        class="auth-button" 
        id="facebook-auth"
        aria-label="Continue with Facebook"
        onclick="fbLogin()"
      >
        <img 
          src="https://upload.wikimedia.org/wikipedia/commons/b/b8/2021_Facebook_icon.svg" 
          alt="Facebook Icon" 
          class="auth-button__icon"
          loading="lazy"
        >
        <span class="auth-button__text">Continue with Facebook</span>
      </button>
    </div>
    
    <p class="divider">or</p>
    
    <button 
      type="button" 
      class="auth-button auth-button--email" 
      id="email-auth"
      aria-label="Continue with Email"
      onclick="window.location.href='signup-email.php'"
    >
      <i class="fas fa-envelope auth-button__icon--custom" aria-hidden="true"></i>
      <span class="auth-button__text">Continue with Email</span>
    </button>
    
    <footer class="text-center mt-xl" style="margin-top: 32px;">
      <p class="body-text">
        Already have an account? 
        <a href="log-in.php" class="body-text--link">Log in instead</a>
      </p>
    </footer>
  </main>
  
  <script>
    // ========== 0. NAV MENU LOGIC ==========
    function toggleMenu() {
      const menu = document.getElementById("mobileMenu");
      menu.classList.toggle("active");
    }

    // Shrinking Header Logic (From aboutus.php)
    window.addEventListener("scroll", function() {
        const header = document.querySelector(".header");
        if (window.scrollY > 20) {
            header.classList.add("scrolled");
        } else {
            header.classList.remove("scrolled");
        }
    });

    // ========== 1. FACEBOOK SETUP ==========
    window.fbAsyncInit = function() {
      FB.init({
        appId      : "2494194494310948", // YOUR APP ID
        cookie     : true,
        xfbml      : true,
        version    : 'v18.0'
      });
    };

    (function(d, s, id){
       var js, fjs = d.getElementsByTagName(s)[0];
       if (d.getElementById(id)) {return;}
       js = d.createElement(s); js.id = id;
       js.src = "https://connect.facebook.net/en_US/sdk.js";
       fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

    function fbLogin() {
      FB.login(function(response) {
        if (response.authResponse) {
          FB.api('/me', {fields: 'name, email'}, function(userInfo) {
            socialLoginBackend({
              provider: 'facebook',
              email: userInfo.email,
              full_name: userInfo.name,
              oauth_uid: response.authResponse.userID
            });
          });
        }
      }, {scope: 'public_profile,email'});
    }

    // ========== 2. GOOGLE SETUP ==========
    function handleCredentialResponse(response) {
      const responsePayload = decodeJwtResponse(response.credential);
      socialLoginBackend({
        provider: 'google',
        email: responsePayload.email,
        full_name: responsePayload.name,
        oauth_uid: responsePayload.sub
      });
    }
    
    document.getElementById('google-auth').addEventListener('click', () => {
        google.accounts.id.prompt(); 
    });

    function decodeJwtResponse(token) {
      var base64Url = token.split('.')[1];
      var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
      var jsonPayload = decodeURIComponent(window.atob(base64).split('').map(function(c) {
        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
      }).join(''));
      return JSON.parse(jsonPayload);
    }

    // ========== 3. BACKEND SENDER ==========
    async function socialLoginBackend(data) {
      try {
        const response = await fetch('auth_social.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
          window.location.href = 'home.php';
        } else {
          alert('Login Error: ' + result.message);
        }
      } catch (error) {
        console.error('Error:', error);
        alert('Connection error.');
      }
    }
  </script>
</body>
</html>