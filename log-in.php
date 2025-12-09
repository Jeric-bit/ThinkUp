<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ThinkUp | Log In</title>

  <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

  <style>
    /* =========================================
       SHARED THEME (Exact match to Index)
       ========================================= */
    * { margin: 0; padding: 0; box-sizing: border-box; }

    :root {
      --light-blue: #e4f7fb;
      --teal: #218ca6;
      --navy-blue: #023047;
      --white: #ffffff;
      --black: #000000;
      --text-gray: #555b66;
      
      --navy: var(--navy-blue);
      --blue-bg: var(--light-blue);

      --font-heading: "Archivo Black", sans-serif;
      --font-body: "Poppins", sans-serif;
    }

    /* =========================================
       HEADER STYLES (Exact match to Index)
       ========================================= */
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
      text-decoration: none;
    }

    .header.scrolled .signup-button { padding: 10px 25px; }

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
    .mobile-menu-overlay.active { transform: translateX(0); }

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
    .menu-divider { width: 60px; height: 2px; background-color: #e0e0e0; margin: 10px 0; }

    /* =========================================
       BODY & LOGIN LAYOUT
       ========================================= */
    body {
      font-family: var(--font-body);
      background-color: var(--blue-bg);
      color: var(--navy);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      position: relative;
      /* Padding to account for fixed header + visual balance */
      padding: 140px 20px 60px; 
    }

    .login-container {
       width: 100%; 
       max-width: 480px; 
       text-align: center; 
       animation: fadeUp 0.6s ease-out forwards;
       opacity: 0;
       transform: translateY(20px);
    }

    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    h1 {
      font-size: 2rem; 
      font-weight: 900; 
      font-family: var(--font-heading);
      color: var(--black); 
      text-align: center; 
      margin-bottom: 8px; 
      letter-spacing: .2px;
    }
    h1 .think { color: var(--navy-blue); font-weight: 900; }
    h1 .up { color: var(--teal); font-weight: 900; }

    .login-divider, .or-divider {
      position: relative; 
      margin: 18px 0; 
      font-family: var(--font-heading);
      font-weight: 500; 
      font-size: .95rem; color: var(--black);
    }
    .login-divider::before, .login-divider::after,
    .or-divider::before,  .or-divider::after {
      content: ""; 
      position: absolute; 
      height: 2px; background: #a6a6a6; 
      top: 52%;
    }
    .login-divider::before, .or-divider::before { left: 0; }
    .login-divider::after,  .or-divider::after  { right: 0; }
    .login-divider::before, .login-divider::after { width: 38%; }
    .or-divider::before, .or-divider::after { width: 46%; }

    .social-buttons { 
      display: flex; 
      justify-content: center; 
      gap: 16px; margin-bottom: 16px; 
    }
    .social-btn {
      background: var(--white); 
      border: 1px solid var(--teal); 
      border-radius: 14px; 
      padding: 10px 12px;
      cursor: pointer; 
      transition: all .1s ease;
      display: block; 
    }
    .social-btn img { 
      width: 36px; 
      height: 36px; 
      transition: transform .3s ease; 
      display: block;
    }
    .social-btn:hover { 
      background-color: rgba(33,140,166,.2); 
      border: 3px solid var(--teal); 
      box-shadow: 0 0 8px var(--teal); 
    }

    .login-form { 
      display: flex; 
      flex-direction: column; 
      gap: 12px; 
      background-color: var(--blue-bg); 
    }
    
    .input-group { position: relative; }
    
    .input-group input {
      width: 100%; 
      padding: 14px 16px; 
      border: 2px solid var(--teal); 
      border-radius: 14px;
      background-color: var(--blue-bg); 
      font-size: .95rem; transition: all .3s ease; 
      color: var(--black);
      font-family: var(--font-body);
      outline: none;
    }
    .input-group label {
      position: absolute; 
      left: 16px; 
      top: 14px; 
      color: var(--navy-blue); 
      transition:.3s; 
      pointer-events:none;
      background: var(--blue-bg); 
      font-size:.95rem;
    }
    .input-group input:focus { 
      border-width: 3px; 
      border-color: var(--teal); 
      box-shadow: 0 0 0px var(--teal); 
      color: var(--navy-blue); 
    }
    .input-group input:focus + label, .input-group input:not(:placeholder-shown) + label {
      top:-10px; 
      left:18px; 
      font-size:.85rem; 
      color: var(--navy-blue); 
      padding:0 6px; 
      font-weight:600;
    }

    .toggle-password {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      user-select: none;
      color: var(--navy-blue);
      font-size: 1.1rem;
      transition: color .3s ease;
      padding: 5px;
    }

    .extra-links { text-align:left; margin-top:2px; }
    .forgot { 
      font-size:.9rem; color: var(--navy-blue); 
      font-weight:800; text-decoration:none; margin-left:15px; 
    }
    .forgot:hover { text-decoration:underline; }

    .login-btn {
      background: var(--navy-blue); 
      color: var(--white); 
      font-weight:800; border:none; border-radius:14px;
      padding:14px 20px; cursor:pointer; 
      transition:all .3s ease; font-size:1rem; margin-top:18px;
    }
    .login-btn:hover { 
      background: var(--teal); 
      box-shadow: 0 0 10px var(--teal); 
    }
    .login-btn:disabled { opacity: 0.7; cursor: not-allowed; }

    .signup-text { 
      font-size:1rem; margin-top:14px; color: var(--black); 
    }
    .signup-text a { 
      color: var(--teal); 
      text-decoration:none; 
      font-weight:800; 
    }
    .signup-text a:hover { text-decoration:underline; }

    .login-error-msg {
        color: #c73a3a; font-size: 0.9rem; text-align: center;
        margin-bottom: 10px; display: none;
        background: rgba(199, 58, 58, 0.1); padding: 8px; border-radius: 8px;
    }

    /* ===== Modals ===== */
    .backdrop {
      position:fixed; inset:0;
      background:rgba(0,0,0,.55);
      backdrop-filter:blur(1px);
      opacity:0; pointer-events:none;
      transition:opacity .2s; z-index:2050;
    }
    .backdrop.open { opacity:1; pointer-events:auto; }

    .modal {
      position:fixed; inset:0; display:grid; place-items:center;
      opacity:0; pointer-events:none;
      transition:opacity .2s; z-index:2060;
    }
    .modal.open { opacity:1; pointer-events:auto; }

    .modal-card {
      width:min(620px,92vw); 
      background: var(--blue-bg); 
      border-radius:22px; 
      box-shadow:0 12px 40px rgba(0,0,0,.25);
      border:1.5px solid #c5e8f1; 
      padding:50px 30px; 
      position:relative;
    }
    .modal-close { 
      position:absolute; top:14px; right:14px; 
      border:0; background:transparent; 
      cursor:pointer; font-size:1.1rem; color:#0b2330; 
    }
    .modal-title { 
      font-family: var(--font-heading); 
      font-size:1.6rem; color:#0b2330; 
      margin-bottom:8px; 
    }
    .modal-desc { color:#123; font-size:.98rem; margin-bottom:14px; }
    .modal .input-group input { background: var(--blue-bg); }
    .modal-actions { display:flex; justify-content:flex-end; margin-top:16px; }
    
    .btn-proceed, .btn-submit, .btn-update {
      background: var(--navy-blue); color: var(--white); border:0; 
      border-radius:22px; height:44px; min-width:120px;
      padding:0 18px; font-weight:800; 
      cursor:pointer; transition:background .2s ease, opacity .2s ease, transform .05s ease;
    }
    .btn-proceed:hover, .btn-submit:hover, .btn-update:hover { background: var(--teal); }
    .btn-proceed:active, .btn-submit:active, .btn-update:active { transform:translateY(1px); }
    .btn-proceed:disabled { opacity:.45; cursor:not-allowed; }

    .input-invalid { 
      border-color:#c73a3a !important; 
      box-shadow:0 0 0 2px rgba(199,58,58,.15); 
    }
    .help-error { margin-top:6px; color:#c73a3a; font-size:.88rem; }
    body.modal-open { overflow:hidden; }
    .resend { font-size:.9rem; color: var(--navy-blue); font-weight:700; text-decoration:none; }
    .resend:hover { text-decoration:underline; }
    .resend.disabled { pointer-events:none; opacity:.55; text-decoration:none; }
    .hint { font-size:.85rem; color: var(--navy-blue); margin-top:6px; }

    @media (max-width: 768px) {
      .container { padding: 0 20px; }
      .header { padding-top: 15px; padding-bottom: 15px; }
      .nav-links { display: none; }
      .hamburger-btn { display: block; }
      
      /* Updated Mobile Logo Sizing to match Index */
      .logo {
        height: 150px; 
        width: 150px;
        margin: 0 0 0 -15px; 
        margin-top: -40px;
        margin-bottom: -40px;
      }
      
      h1 { font-size: 1.4rem; }
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
        <a href="log-in.php" class="nav-link" style="color:var(--teal);">Log In</a>
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
    <a href="log-in.php" class="mobile-nav-link" onclick="toggleMenu()" style="color:var(--teal);">Log In</a>
    <a href="sign-up.php" class="mobile-nav-link" onclick="toggleMenu()">Sign Up</a>
  </div>

  <div class="login-container">
    <h1>Welcome back to <span class="think">Think</span><span class="up">Up</span>!</h1>

    <div class="social-login">
      <p class="login-divider"><span>Log in with</span></p>
      <div class="social-buttons">
        <button class="social-btn google" type="button" id="google-login">
          <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Google_%22G%22_logo.svg/1200px-Google_%22G%22_logo.svg.png" alt="Google" />
        </button>
        <button class="social-btn facebook" type="button" id="facebook-login">
          <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b8/2021_Facebook_icon.svg/2048px-2021_Facebook_icon.svg.png" alt="Facebook" />
        </button>
      </div>
    </div>

    <p class="or-divider"><span>or</span></p>

    <form id="loginForm" class="login-form" novalidate>
      <div class="input-group">
        <input type="text" name="username" id="username" required placeholder=" " autocomplete="username" inputmode="email" />
        <label for="username">Email or Username</label>
      </div>

      <div class="input-group">
        <input type="password" name="password" id="password" required placeholder=" " autocomplete="current-password" />
        <label for="password">Password</label>
        <i class="bi bi-eye-slash-fill toggle-password" id="togglePassword"></i>
      </div>

      <div id="loginError" class="login-error-msg"></div>

      <div class="extra-links">
        <a href="#" class="forgot" id="openForgot">Forgot password?</a>
      </div>

      <button type="submit" class="login-btn" id="loginBtn">Log in</button>

      <p class="signup-text">Don’t have an account? <a href="sign-up.php">Sign up</a></p>
    </form>
  </div>

  <div class="backdrop" id="fpBackdrop" aria-hidden="true"></div>

  <div class="modal" id="fpModalEmail" role="dialog" aria-modal="true" aria-labelledby="fpTitle1">
    <div class="modal-card">
      <button class="modal-close" id="closeEmail"><i class="bi bi-x-lg"></i></button>
      <h2 class="modal-title" id="fpTitle1">Verifying Email</h2>
      <p class="modal-desc">Enter your registered user account’s email.</p>
      <div class="input-group">
        <input type="email" id="fpEmail" placeholder=" " autocomplete="email" />
        <label for="fpEmail">Email</label>
      </div>
      <div id="fpEmailErr" class="help-error" style="display:none;">Please enter a valid email ending in .com</div>
      <div class="modal-actions">
        <button type="button" class="btn-proceed" id="proceedBtn" disabled>Proceed</button>
      </div>
    </div>
  </div>

  <div class="modal" id="fpModalCode" role="dialog" aria-modal="true" aria-labelledby="fpTitle2">
    <div class="modal-card">
      <button class="modal-close" id="closeCode"><i class="bi bi-x-lg"></i></button>
      <h2 class="modal-title" id="fpTitle2">Confirming your Email</h2>
      <p class="modal-desc">We’ve sent a confirmation code to your email.</p>
      <div class="input-group">
        <input type="text" id="fpCode" inputmode="numeric" placeholder=" " />
        <label for="fpCode">Verification code</label>
      </div>
      <div style="margin-top:10px;text-align:left">
        <a href="#" class="resend" id="resendLink">Resend code?</a>
        <span class="hint" id="cooldownHint" style="display:none; margin-left:8px;"></span>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn-submit" id="submitCodeBtn">Submit</button>
      </div>
    </div>
  </div>

  <div class="modal" id="fpModalNewPw" role="dialog" aria-modal="true" aria-labelledby="fpTitle3">
    <div class="modal-card">
      <button class="modal-close" id="closeNewPw"><i class="bi bi-x-lg"></i></button>
      <h2 class="modal-title" id="fpTitle3">New password</h2>
      <p class="modal-desc">Create a strong password to secure your account.</p>
      <div class="input-group">
        <input type="password" id="newPw" placeholder=" " autocomplete="new-password" />
        <label for="newPw">Password</label>
        <i class="bi bi-eye-slash-fill toggle-password" id="toggleNewPw"></i>
      </div>
      <div class="input-group" style="margin-top:10px;">
        <input type="password" id="confirmPw" placeholder=" " autocomplete="new-password" />
        <label for="confirmPw">Confirm new password</label>
        <i class="bi bi-eye-slash-fill toggle-password" id="toggleConfirmPw"></i>
      </div>
      <div id="pwErr" class="help-error" style="display:none;">Password must be at least 8 characters...</div>
      <div class="modal-actions">
        <button type="button" class="btn-update" id="updatePwBtn">Update</button>
      </div>
    </div>
  </div>

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

    /* ===== Login & Modal Logic ===== */
    const loginForm = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');
    const loginError = document.getElementById('loginError');

    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        loginBtn.disabled = true;
        const originalText = loginBtn.textContent;
        loginBtn.innerHTML = 'Logging in...';
        loginError.style.display = 'none';
        const formData = {
            username: document.getElementById('username').value,
            password: document.getElementById('password').value
        };
        try {
            const response = await fetch('auth_login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });
            const result = await response.json().catch(() => ({ success: false, message: 'Server error.' }));
            if (result.success) {
                window.location.href = 'home.php';
            } else {
                loginError.textContent = result.message || 'Login failed.';
                loginError.style.display = 'block';
                loginBtn.disabled = false;
                loginBtn.textContent = originalText;
            }
        } catch (err) {
            loginError.textContent = 'Connection error. Try again.';
            loginError.style.display = 'block';
            loginBtn.disabled = false;
            loginBtn.textContent = originalText;
        }
    });

    /* Social Login */
    async function handleSocialLogin(provider) {
        const mockSocialUser = {
            provider: provider,
            id: `mock_${provider}_${Date.now()}`,
            name: `${provider} User`,
            email: `user@${provider}.com`
        };
        try {
            const response = await fetch('auth_social.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(mockSocialUser)
            });
            const res = await response.json();
            if(res.success) window.location.href='home.php';
            else alert('Social fail: '+res.message);
        } catch(e) { alert('Connection error'); }
    }
    document.getElementById('google-login').addEventListener('click', () => handleSocialLogin('google'));
    document.getElementById('facebook-login').addEventListener('click', () => handleSocialLogin('facebook'));

    /* Password Eyes */
    function attachEye(iconId, inputId){
        const icon = document.getElementById(iconId);
        const input = document.getElementById(inputId);
        icon.addEventListener('click', () => {
            input.type = input.type==='password'?'text':'password';
            icon.classList.toggle('bi-eye');
            icon.classList.toggle('bi-eye-slash-fill');
        });
    }
    attachEye('togglePassword', 'password');
    attachEye('toggleNewPw', 'newPw');
    attachEye('toggleConfirmPw', 'confirmPw');

    /* Modals */
    const openForgot = document.getElementById('openForgot');
    const backdrop = document.getElementById('fpBackdrop');
    const modalEmail = document.getElementById('fpModalEmail');
    const modalCode = document.getElementById('fpModalCode');
    const modalNewPw = document.getElementById('fpModalNewPw');
    const emailInput = document.getElementById('fpEmail');
    const emailErr = document.getElementById('fpEmailErr');
    const proceedBtn = document.getElementById('proceedBtn');
    const codeInput = document.getElementById('fpCode');
    const submitCodeBtn = document.getElementById('submitCodeBtn');
    const newPw = document.getElementById('newPw');
    const confirmPw = document.getElementById('confirmPw');
    const pwErr = document.getElementById('pwErr');
    const updatePwBtn = document.getElementById('updatePwBtn');

    function openModal(m){ m.classList.add('open'); backdrop.classList.add('open'); document.body.classList.add('modal-open'); }
    function closeAll(){ [modalEmail, modalCode, modalNewPw].forEach(m=>m.classList.remove('open')); backdrop.classList.remove('open'); document.body.classList.remove('modal-open'); }
    openForgot.addEventListener('click', (e)=>{ e.preventDefault(); openModal(modalEmail); setTimeout(()=>emailInput.focus(),100); });
    document.querySelectorAll('.modal-close').forEach(b=>b.addEventListener('click', closeAll));
    backdrop.addEventListener('click', closeAll);

    /* Validations */
    const emailRegex = /^[^\s@]+@[^\s@]+\.com$/i;
    function validateEmail(){
        const v = emailInput.value.trim();
        const valid = emailRegex.test(v);
        proceedBtn.disabled = !valid;
        if(!valid && v.length) { emailInput.classList.add('input-invalid'); emailErr.style.display='block'; }
        else { emailInput.classList.remove('input-invalid'); emailErr.style.display='none'; }
        return valid;
    }
    emailInput.addEventListener('input', validateEmail);
    proceedBtn.addEventListener('click', ()=>{ if(!validateEmail()) return; modalEmail.classList.remove('open'); openModal(modalCode); setTimeout(()=>codeInput.focus(),100); startCooldown(60); });
    submitCodeBtn.addEventListener('click', ()=>{ if(!codeInput.value.trim()){ codeInput.focus(); return; } modalCode.classList.remove('open'); openModal(modalNewPw); setTimeout(()=>newPw.focus(),100); });
    function validatePw(){
        const p = newPw.value.trim(); const c = confirmPw.value.trim();
        const strong = /^(?=.*[A-Za-z])(?=.*\d).{8,}$/.test(p);
        const match = p===c && p!=='';
        [newPw, confirmPw].forEach(x=>x.classList.remove('input-invalid'));
        pwErr.style.display='none';
        if(!strong || !match){
            if(!strong) newPw.classList.add('input-invalid');
            if(!match) confirmPw.classList.add('input-invalid');
            pwErr.style.display='block';
            return false;
        }
        return true;
    }
    updatePwBtn.addEventListener('click', ()=>{ if(!validatePw()) return; alert('Password updated successfully!'); closeAll(); });

    /* Cooldown */
    const resendLink = document.getElementById('resendLink');
    const cooldownHint = document.getElementById('cooldownHint');
    let timer = null;
    function startCooldown(sec){
        let until = Date.now() + sec*1000;
        resendLink.classList.add('disabled');
        if(timer) clearInterval(timer);
        timer = setInterval(()=>{
            const left = Math.ceil((until - Date.now())/1000);
            if(left<=0){ clearInterval(timer); resendLink.classList.remove('disabled'); cooldownHint.style.display='none'; } 
            else { cooldownHint.style.display='inline'; cooldownHint.textContent = `Wait ${left}s`; }
        }, 1000);
    }
    resendLink.addEventListener('click', (e)=>{ e.preventDefault(); if(resendLink.classList.contains('disabled')) return; alert('Code resent!'); startCooldown(60); });
  </script>
</body>
</html>