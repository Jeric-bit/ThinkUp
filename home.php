<?php
// home.php
session_start();
require 'db_connect.php';

// 1. Security Check: Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html"); 
    exit();
}

// 2. Get User Name
$user_name = $_SESSION['user_name'] ?? 'Student';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>ThinkUp | Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
<style>
  /* [GLOBAL VARIABLES] */
  :root{
    --ink:#000000; --bg:#caf0f8; --dark:#023047;
    --side:#e4f7fb; --sideHover:#caf0f8; --sideActive:#caf0f8;
    --divider:#218ca6; --accent:#1b282c;
    --sideW:72px; --contentPad:28px;
  }
  
  /* HYBRID DARK MODE: Dark Main, Light Sidebar */
  body.dark {
    --bg: #023047;      /* Dark Blue Main Background */
    --ink: #E9F8FF;     /* Light Text */
    
    /* Keep Sidebar Light */
    --side: #e4f7fb;    
    --sideHover: #caf0f8;
    --sideActive: #caf0f8;
    --divider: #218ca6;
    --accent: #1b282c;
  }

  body, .sidebar, header, .nav-item, .btn {
    transition: background-color .25s ease, color .25s ease, border-color .25s ease;
  }
  *{box-sizing:border-box;margin:0}
  body{font-family:Poppins,system-ui,-apple-system,Segoe UI,Roboto,sans-serif;background:var(--bg);color:var(--ink)}
  
  /* Desktop Expanded State */
  body.nav-open{ --sideW:220px; }

  /* ───── Sidebar ───── */
  .sidebar{
    position: fixed; inset: 0 auto 0 0; width: var(--sideW); background: var(--side);
    border-right: 1px solid var(--divider); z-index: 100; padding: 10px 1px;
    display: flex; flex-direction: column; gap: 0px; 
    transition: width .25s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .sidebar::before {content:""; position:absolute; left:0; top:0; width:4px; height:100%;}
  .sidebar::after{content:none; position:absolute; right:-2px; top:0; width: 0.5px; height:100%; background:#023047;}

  /* Nav Item Base */
  .nav-item{
    position:relative; display:flex; align-items:center; gap:2px; height:56px; border-radius:0;
    padding:0; background:transparent; cursor:pointer; border:none; width:100%;
    justify-content:center; text-decoration: none; color: inherit;
  }
  .nav-item:hover{ background:var(--sideHover); }
  .nav-item img.icon{ width:70px; height:70px; object-fit:contain; }

  /* Active Indicator (The Black Line) */
  .active-indicator{ 
    position:absolute; right:0; width:6px; height:56px; background:var(--accent);
    top:0; transition: top .25s cubic-bezier(.22,.61,.36,1); pointer-events:none; z-index:2; 
    border-top-left-radius: 4px; border-bottom-left-radius: 4px;
  }

  /* Expanded Sidebar Internal Layout */
  body.nav-open .nav-item{ justify-content:flex-start; gap:12px; }
  .nav-label{ font-family:'Poppins',sans-serif; white-space:nowrap; overflow:hidden; opacity:0;
    transform:translateX(-4px); transition:opacity .15s ease, transform .15s ease, width .2s ease; width:0;}
  body.nav-open .nav-label{ opacity:1; transform:translateX(0); width:auto; }

  /* ───── Header (Glassmorphism) ───── */
  header{
    height:70px; 
    background: rgba(228, 247, 251, 0.85); /* Semi-transparent Light Blue */
    backdrop-filter: blur(12px); /* Blur effect */
    border-bottom: 1px solid rgba(33, 140, 166, 0.5);
    display:flex; align-items:center; justify-content:space-between; padding:0 20px;
    position: fixed; top: 0; right: 0; left: var(--sideW); 
    width: auto; z-index: 50;
    transition: left .25s cubic-bezier(0.4, 0, 0.2, 1);
  }
  
  /* Dark Mode Glass Header */
  body.dark header { 
    background: rgba(228, 247, 251, 0.9); /* Keep header light even in dark mode for contrast */
    border-color: #218ca6; 
  }

  /* Main Content Pushed Down */
  main{ 
    margin-left: var(--sideW); 
    padding: 100px var(--contentPad) 50px; 
    transition: margin-left .25s cubic-bezier(0.4, 0, 0.2, 1); 
    min-height: 100vh;
  }

  /* Header Mobile Toggle */
  .mobile-toggle { display: none; margin-right: 12px; cursor: pointer; background: none; border: none; padding: 0; }
  .mobile-toggle img { width: 32px; height: 32px; }

  /* Icons */
  .brand{display:flex;align-items:center;gap:10px;font-weight:800}
  .brand img.logo{height:50px;display:block;}
  @media(min-width: 769px) { .brand img.logo { height: 130px; } } 

  .top-icons{ display:flex; align-items:center; justify-content:center; gap:20px; }
  
  /* Standard Icon Size */
  .top-icons img{ width:40px; height:40px; object-fit:contain; }
  @media(min-width: 769px) { .top-icons img { width: 85px; height: 85px; } }

  /* --- SUN/MOON ICON SIZE (100px) --- */
  #themeToggle {
      transition: transform 0.2s ease;
      object-fit: contain;
  }
  @media(min-width: 769px) {
      #themeToggle {
          width: 100px;
          height: 100px;
      }
  }
  #themeToggle:hover { transform: scale(1.1); }

  /* =========================================================
     ACCOUNT DROPDOWN STYLES
     ========================================================= */
  .account-wrap{ position:relative; display:inline-flex; }
  
  /* Account Menu - Force Light BG */
  .account-menu{ 
    position:absolute; right:0; top:calc(100% + 15px); width:240px; 
    background:#e4f7fb; 
    color:#000; border:2px solid #218ca6; border-radius:18px; padding:12px; 
    opacity:0; pointer-events:none; transition: all .2s ease; z-index:99; 
    transform: translateY(10px);
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
  }
  .account-menu.open{ opacity:1; pointer-events:auto; transform: translateY(0); }
  
  .account-item{ 
    width:100%; display:flex; align-items:center; gap:10px; padding:14px 16px; 
    border-radius:14px; font-weight:800; cursor:pointer; margin-top:8px; 
  }
  .account-item:first-child{margin-top:0}
  
  /* Primary Item (My Account) - Dark Blue */
  .account-item.primary{ background:#0b2f42; color:#fff; border:none; }
  
  /* Card Item (Logout) - White with Border */
  .account-item.card{ background:#fff; color:#0b2f42; border: 1px solid #218ca6; }
  .account-item.card:hover{ background: #f0faff; border-color:#0b2f42; }

  /* SVG Icon Styles */
  .ai-icon { width: 20px; height: 20px; flex-shrink: 0; }
  .account-item.primary .ai-icon { fill: #fff; }
  .account-item.card .ai-icon { fill: #0b2f42; }

  /* Notif Popover */
  .top-icons .icon-btn{ appearance:none; border:0; background:transparent; padding:0; margin:0; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; }
  .notif-wrap{ position:relative; display:inline-flex; }
  
  .notif-popover{
    position:absolute; right:-6px; top:115%; width:280px; background:var(--side); color:#000;
    border:2px solid var(--divider); border-radius:14px; box-shadow:0 8px 22px rgba(0,0,0,.10);
    padding:14px 16px; z-index:999; opacity:0; transform: translateY(-6px) scale(.98);
    pointer-events:none; transition:opacity .18s ease, transform .18s ease;
  }
  .notif-popover.open{ opacity:1; transform:translateY(0) scale(1); pointer-events:auto; }
  .notif-head{ font-weight:700; color:#000; margin-bottom:10px; }
  .notif-head::after{ content:""; display:block; height:1px; width:100%; background:var(--divider); margin-top:6px; opacity:.8; }
  .notif-empty{ text-align:center; padding:14px 0 6px; color:#2b3b44; font-weight:500; }

  /* Mobile Responsive */
  @media (max-width: 768px) {
    :root { --sideW: 0px; }
    body.nav-open { --sideW: 0px; }

    .sidebar { width: 240px; transform: translateX(-100%); box-shadow: none; }
    body.nav-open .sidebar { transform: translateX(0); box-shadow: 4px 0 25px rgba(0,0,0,0.3); }
    
    .nav-label { opacity: 1 !important; width: auto !important; transform: none !important; }
    .nav-item { justify-content: flex-start; gap: 12px; }
    #menuBtn { display: none; }

    header { left: 0; width: 100%; padding: 0 16px; }
    .mobile-toggle { display: block; }
    
    main { margin-left: 0; padding: 90px 16px 40px; }
    .grid { grid-template-columns: repeat(2, 1fr) !important; gap: 16px; }
    .btn { padding: 14px 40px; font-size: 14px; width: 100%; }
    .cta { flex-direction: column; gap: 12px; }
    .pi-card { padding: 30px 20px; border-radius: 24px; }
    .pi-header { grid-template-columns: 1fr; justify-items: center; text-align: center; }
    .pi-name-group { width: 100%; }
  }

  .sidebar-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 90;
    opacity: 0; pointer-events: none; transition: opacity 0.3s ease;
    backdrop-filter: blur(2px);
  }
  body.nav-open .sidebar-overlay { opacity: 1; pointer-events: auto; }

  /* ====== VIEWS ====== */
  .view{ display:none; }
  .view.active{ display:block; animation: fadeInView 0.3s ease; }
  @keyframes fadeInView { from{opacity:0;transform:translateY(10px);} to{opacity:1;transform:translateY(0);} }

  .container { max-width: 1200px; margin: 0 auto; width: 100%; display: flex; flex-direction: column; align-items: center; }
  
  .rec { margin-top:60px; width: fit-content; max-width: 100%; display: flex; flex-direction: column; align-items: flex-start; }
  .rec h3{ margin:0 0 16px 0; font-weight: 1000px; text-align:left; width: 100%; font-size: 1rem; letter-spacing: 0.05em; color: var(--dark); opacity: 0.8; }
  .grid{ display:flex; flex-wrap: wrap; justify-content: flex-start; gap: 26px; width: 100%; }
  
  /* Original Simple Hero Style */
  h1{font-weight:500;font-size:clamp(22px,3.4vw,40px);text-align:center;margin-top:100px;margin-bottom:30px}
  h1 b{color:#0d4153}
  
  .btn{margin-bottom:30px; display:inline-flex;align-items:center;gap:10px;justify-content:center;padding:18px 70px;border-radius:999px;font-weight:800;cursor:pointer;border:2px solid var(--dark);background:var(--dark);color:#fff;transition:.2s}
  body.dark .btn { background:#e4f7fb; color:#023047; border-color:#e4f7fb;}
  body.dark .btn.secondary { background:transparent; color:#e4f7fb; border-color:#c5d1d7; box-shadow:0 0 0 2px #e4f7fb inset;}
  .btn.secondary{background:#fff;color:var(--dark);border-color:#69bfd3;box-shadow:0 0 0 2px #c8e9f3 inset}

  /* Original Bubble Cards Style */
  .dash-card{display:flex;flex-direction:column;align-items:center;text-align:center;gap:10px; cursor: pointer; transition: transform .2s ease;}
  .dash-card:hover { transform: translateY(-5px); }
  
  .bubble{width:160px;height:160px;border-radius:999px;display:grid;place-items:center;font-weight:900;font-size:48px;background:#ffd56b;color:#0b2330}
  @media(min-width: 769px){ .bubble { width:160px; height:160px; font-size: 72px; } }
  .bubble.pink{background:#ff78b5}.bubble.lav{background:#dfb7ff}.bubble.red{background:#ff6b6b}.bubble.yellow{background:#ffd56b}.bubble.green{background:#78d46f}.bubble.blue{background:#57b0ff}
  .subj{font-weight:800;margin-top:4px}.subtxt{font-size:12px;color:#30424b}

  /* ===== DARK MODE LOOK ===== */
  body.dark{ --bg:#023047; --ink:#ffffff; --side:#cfeff6; --sideHover:#bfe7f0; --sideActive:transparent; --divider:#1d3c4c; --accent:#0975aa; }
  body.dark h1{ color:#E9F8FF; } body.dark h1 b{ color:#fff; }
  body.dark .sidebar{ background:var(--side); }
  body.dark .sidebar::after{ content:""; position:absolute; right:0; top:0; width:1px; height:100%; background:#218ca6; }
  body.dark .nav-item{ background:transparent; }
  
  /* Active item style in Dark Mode */
  body.dark .nav-item.active{ background:transparent; }
  
  body.dark .nav-item:hover{ background:#0a3b51; }
  body.dark .active-indicator{ background:#e4f7fb; width:6px; }
  body.dark header{ background: rgba(228, 247, 251, 0.9); border-bottom:1px solid var(--divider); }
  body.dark main{ background:var(--bg); color:var(--ink); }
  
  /* Profile Styles */
  .profile-wrap{ max-width: 600px; padding: 30px 30px 52px; border-radius: 40px; background: #e4f7fb; box-shadow: 0 6px 24px rgba(0,0,0,.06); margin: 0 auto; }
  body.dark .profile-wrap{ background:#cfeff6; }
  .profile-title{ font-weight: 800; text-align: center; font-size: clamp(20px,2.4vw,28px); color:#0b2330; margin-bottom: 18px; }
  .pi-header{ display:grid; grid-template-columns: 120px 1fr; gap: 14px; align-items:start; margin-bottom: 12px; }
  .avatar-box{ position:relative; width:120px; }
  .avatar{ width:120px; height:120px; border-radius:999px; background:#e6eef2; display:grid; place-items:center; font-weight:700; color:#63707a; border:4px solid #cfdbe1; background-size:cover; background-position:center; }
  .avatar-edit { position: absolute; right: -8px; bottom: -10px; width: 38px; height: 38px; background: transparent; border: none; cursor: pointer; padding: 0; z-index: 5; }
  .avatar-edit::before { content: ""; width: 26px; height: 26px; display: block; background: url("https://cdn-icons-png.flaticon.com/512/1159/1159633.png") no-repeat center/contain; filter: brightness(0) saturate(100%) contrast(200%); }
  .pi-name-group{ display:grid; grid-template-rows: auto auto; gap: 12px; } .pi-field { position: relative; display: flex; align-items: center; }
  .profile-input { width: 100%; background: #f5fcff; border: 2px solid #7fc2d3; border-radius: 17px; height: 46px; padding: 0 14px; font-size: 14px; outline: none; color: #0b2330; transition: border-color 0.2s ease, box-shadow 0.2s ease; }
  .profile-input:focus { background: #e4f7fb; border-color: #218ca6; box-shadow: 0 0 0 2px rgba(33,140,166, 0.5); }
  .pi-rows{ margin-top: 8px; display:grid; gap:12px; } .pi-actions{ display:flex; justify-content:flex-end; margin-top:22px; }
  .save-btn{ min-width:150px; height:48px; border-radius:999px; background:#0b2f42; color:#fff; font-weight:800; border:none; cursor:pointer; box-shadow:0 8px 24px rgba(11,47,66,.18); transition:transform .15s ease, background .2s ease; }

  /* Modals & Drawer */
  .modal { position: fixed; inset: 0; background: rgba(0,0,0,.55); display: none; justify-content: center; align-items: center; z-index: 1000; backdrop-filter: blur(3px); }
  .modal.show { display: flex; }
  .modal-content { background: #e4f7fb; border-radius: 22px; padding: 30px; width: 90%; max-width: 420px; box-shadow: 0 8px 26px rgba(0,0,0,.25); position: relative; text-align: left; animation: modalPop .18s ease-out; }
  .modal-close{ position:absolute; top:10px; right:14px; border:0; background:transparent; font-size:24px; cursor:pointer; color:#023047; }
  @keyframes modalPop { from{opacity:0;transform:translateY(10px) scale(.98);} to{opacity:1;transform:translateY(0) scale(1);} }
  .input-group { position: relative; margin-bottom: 16px; } .input-group input { width: 100%; height: 46px; border-radius: 12px; border: 1.8px solid #218ca6; background: #e4f7fb; padding: 0 46px 0 16px; }
  
  .drawer { position: fixed; inset: 0; display: none; z-index: 3000; } .drawer.show { display:block; }
  .drawer-backdrop{ position:absolute; inset:0; background:rgba(0,0,0,.45); backdrop-filter: blur(2px); opacity:0; transition:opacity .2s ease; } .drawer.show .drawer-backdrop{ opacity:1; }
  .drawer-panel{ position:absolute; top:0; right:0; height:100%; width:min(520px, 92vw); background:#e4f7fb; border-left:1px solid #218ca6; transform:translateX(100%); transition: transform .22s ease-out; box-shadow: -8px 0 24px rgba(0,0,0,.18); }
  .drawer.show .drawer-panel{ transform:translateX(0); }
  .dq-head{ display:flex; align-items:center; gap:8px; padding:16px 18px; border-bottom:1px solid #218ca6; font-weight:800; color:#023047; }
  .dq-body{ padding:18px; display:grid; gap:16px; height:calc(100% - 65px); overflow:auto; }
  .dq-group{ background:#e4f7fb; border:2px solid #69bfd3; border-radius:16px; padding:12px; }
  .dq-input, .dq-select select{ width:100%; height:46px; border-radius:14px; border:1.8px solid #218ca6; background:#fff; padding:0 14px; outline:none; }
  .dq-actions{ margin-top:auto; display:flex; justify-content:flex-end; gap:10px; }
  .dq-btn{ min-width:110px; height:44px; border-radius:999px; font-weight:800; cursor:pointer; border:2px solid #0b2f42; background:#0b2f42; color:#fff; }
  .dq-btn.secondary{ background:#fff; color:#0b2f42; border-color:#69bfd3; }
  body.dark .drawer-panel{ background:#cfeff6; border-left-color:#1d3c4c; } body.dark .dq-group{ background:#cfeff6; border-color:#0975aa; }
  body.dark .dq-input, body.dark .dq-select select{ background:#e4f7fb; color:#023047; border-color:#0975aa; } body.dark .dq-btn{ background:#023047; border-color:#023047; }
  
  /* Toast */
  .toast-success { position: fixed; top: 80px; right: 22px; max-width: 320px; display:flex; align-items:center; gap:12px; background-color: #d0f0e3; color: #034d33; border: 2px solid #218ca6; border-radius: 12px; padding: 14px 20px; font-weight: 600; font-size: 1rem; box-shadow: 0 4px 10px rgba(0,0,0,0.12); opacity: 0; transform: translateY(-10px); pointer-events: none; transition: 0.25s; z-index: 1000; }
  body.dark .toast-success { background-color: #095440; color: #d0f0e3; border-color: #77D1F6; }
  .toast-success.show { opacity:1; transform:translateY(0); pointer-events:auto; }
  .toast-success-button{ margin-left:auto; background:transparent; border:none; color:inherit; font-weight:700; font-size:20px; cursor:pointer; }
</style>
</head>
<body>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <nav class="sidebar" aria-label="Sidebar">
    <button id="menuBtn" class="nav-item" aria-label="Toggle menu" aria-expanded="false">
      <img class="icon" src="https://images.unsplash.com/vector-1762015667575-6df5d9fc339e?auto=format&fit=crop&q=80&w=1160" alt="">
    </button>

    <a href="home.php" class="nav-item active">
      <img class="icon" src="https://images.unsplash.com/vector-1761990311135-822001b59e63?auto=format&fit=crop&q=80&w=1160" alt="">
      <span class="nav-label">Home</span>
    </a>
    <a href="folders.php" class="nav-item">
      <img class="icon" src="https://images.unsplash.com/vector-1761990768206-d168851041ee?auto=format&fit=crop&q=80&w=880" alt="">
      <span class="nav-label">Folders</span>
    </a>
    <a href="progress.php" class="nav-item">
      <img class="icon" src="https://images.unsplash.com/vector-1762015514967-f36e77ce5da8?auto=format&fit=crop&q=80&w=880" alt="">
      <span class="nav-label">Progress</span>
    </a>

    <div class="active-indicator"></div>
  </nav>

  <header>
    <div class="brand">
      <button class="mobile-toggle" id="mobileMenuBtn" aria-label="Open menu">
        <img src="https://images.unsplash.com/vector-1762015667575-6df5d9fc339e?auto=format&fit=crop&q=80&w=1160" alt="Menu">
      </button>
      <img src="https://images.unsplash.com/vector-1761420317266-eaf29b9ce275?auto=format&fit=crop&q=80&w=880" class="logo" alt="ThinkUp logo">
    </div>
    <div class="top-icons">
      <img id="themeToggle" src="https://images.unsplash.com/vector-1762021885090-5c631ddfa33e?auto=format&fit=crop&q=80&w=880" alt="Theme">

      <span class="notif-wrap">
        <button id="notifBtn" class="icon-btn" aria-haspopup="dialog" aria-expanded="false" aria-controls="notifPanel">
          <img src="https://images.unsplash.com/vector-1762021886143-5f780ef86f74?auto=format&fit=crop&q=80&w=880" alt="Notifications">
        </button>
        <div id="notifPanel" class="notif-popover" role="dialog" aria-label="Notifications">
          <div class="notif-head">Notifications</div>
          <div class="notif-empty">No Notifications</div>
        </div>
      </span>

      <span class="account-wrap">
        <button id="accountBtn" class="icon-btn" aria-haspopup="menu" aria-expanded="false" aria-controls="accountMenu" title="My account">
          <img src="https://images.unsplash.com/vector-1762021887302-22e2cffd4172?auto=format&fit=crop&q=80&w=880" alt="Account">
        </button>

        <div id="accountMenu" class="account-menu" role="menu">
          <button id="acctMy" class="account-item primary" role="menuitem">
            <svg class="ai-icon" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            <span>My account</span>
          </button>
          <button id="acctLogout" class="account-item card" role="menuitem" onclick="window.location.href='logout.php'">
            <svg class="ai-icon" viewBox="0 0 24 24"><path d="M17 7l-1.41 1.41L18.17 11H8v2h10.17l-2.58 2.58L17 17l5-5zM4 5h8V3H4c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h8v-2H4V5z"/></svg>
            <span>Logout</span>
          </button>
        </div>
      </span>
    </div>
  </header>

  <main>
    <section id="view-home" class="view active">
      <div class="container">
        <h1>Hi, <b id="who"><?php echo htmlspecialchars($user_name); ?></b>! Create a quiz to get started</h1>

        <div class="cta">
          <button class="btn" id="createBtn">+ Create Quiz</button>
          <button class="btn secondary" id="joinBtn">+ Join Quiz</button>
        </div>

        <section class="rec">
          <h3>RECOMMENDED FOR YOU</h3>
          <div class="grid">
            <div class="dash-card"><div class="bubble pink">W</div><div class="subj">WBDV</div><div class="subtxt">Cascading Style Sheets</div></div>
            <div class="dash-card"><div class="bubble lav">D</div><div class="subj">DSCR</div><div class="subtxt">Sets</div></div>
            <div class="dash-card"><div class="bubble red">E</div><div class="subj">ETIC</div><div class="subtxt">Theory of Justice</div></div>
            <div class="dash-card"><div class="bubble yellow">I</div><div class="subj">IMGT</div><div class="subtxt">SQL Commands</div></div>
            <div class="dash-card"><div class="bubble green">C</div><div class="subj">CENG</div><div class="subtxt">Figures of Speech</div></div>
            <div class="dash-card"><div class="bubble blue">M</div><div class="subj">MATH</div><div class="subtxt">Addition</div></div>
          </div>
        </section>
      </div>
    </section>

    <section id="view-profile" class="view">
      <div class="profile-wrap pi-card">
        <h2 class="profile-title">Personal Information</h2>
        <div class="pi-header">
          <div class="avatar-box">
            <div id="avatar" class="avatar">👤</div>
            <button id="avatarEdit" class="avatar-edit" title="Change avatar" type="button"></button>
            <input id="avatarFile" type="file" accept="image/*" hidden>
          </div>
          <div class="pi-name-group">
            <div class="pi-field"><input id="pi_first" class="profile-input" placeholder="First Name" value="<?php echo htmlspecialchars($user_name); ?>"></div>
            <div class="pi-field"><input id="pi_last" class="profile-input" placeholder="Last Name"></div>
          </div>
        </div>
        <div class="pi-rows">
          <input id="pi_user" class="profile-input" placeholder="Username" disabled title="Managed by Database">
          <input id="pi_email" type="email" class="profile-input" placeholder="Email" disabled title="Managed by Database">
        </div>
        <div class="pi-actions">
          <button id="saveProfile" class="save-btn" type="button">Update</button>
        </div>
      </div>
       <div id="avatarModal" class="modal" aria-hidden="true">
        <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="avatarTitle">
          <button class="modal-close" id="closeAvatarModal" aria-label="Close">×</button>
          <h3 id="avatarTitle">Upload image</h3>
          <div class="avup-grid">
            <div class="avup-preview"><div id="avupCircle" class="avup-circle"></div></div>
            <div class="avup-actions">
              <input id="avatarFileModal" type="file" accept="image/*" hidden>
              <button id="uploadAvatarBtn" class="avup-btn upload">Upload</button>
              <button id="saveAvatarBtn" class="avup-btn save">Save</button>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <div id="joinModal" class="modal" aria-hidden="true">
    <div class="modal-content" role="dialog">
      <button class="modal-close" id="closeJoinModal">×</button>
      <h3>Join a Quiz</h3>
      <div class="input-group">
        <input id="join_code" type="text" placeholder=" " maxlength="6" />
        <label for="join_code">Enter 6-digit Code</label>
      </div>
      <button id="btnJoinSubmit" class="save-btn">Enter</button>
    </div>
  </div>

  <div id="toast" class="toast-success" role="status" aria-live="polite">
    <svg viewBox="0 0 24 24" aria-hidden="true" width="22" height="22" style="flex:0 0 22px"><path fill="currentColor" d="M20 6L9 17l-5-5"/></svg>
    <span>Success</span>
    <button class="toast-success-button" aria-label="Close">×</button>
  </div>

  <div id="quizDrawer" class="drawer" aria-hidden="true">
    <div class="drawer-backdrop" data-close="drawer"></div>
    <aside class="drawer-panel" role="dialog" aria-modal="true" aria-labelledby="dqTitle">
      <div class="dq-head">
        <button id="dqClose" class="dq-btn secondary" style="min-width:auto;height:34px;padding:0 12px;border-radius:10px;border-width:1.6px">✕</button>
        <span id="dqTitle" style="font-weight:800;margin-left:6px;">Create quiz</span>
      </div>
      <div class="dq-body">
        <div class="dq-group">
          <label class="dq-label" for="dq_name">Give your quiz a name</label>
          <input id="dq_name" class="dq-input" placeholder="Enter quiz name" />
        </div>
        <div class="dq-group">
          <label class="dq-label" for="dq_course">Choose a course</label>
          <div class="dq-select">
            <select id="dq_course" aria-label="Courses">
              <option value="" selected disabled>Courses (select one)</option>
              <option value="WBDV – Web Dev">WBDV – Web Dev</option>
              <option value="DSCR – Discrete Math">DSCR – Discrete Math</option>
              <option value="ETIC – Ethics">ETIC – Ethics</option>
              <option value="IMGT – SQL">IMGT – SQL</option>
              <option value="CENG – English">CENG – English</option>
              <option value="MATH – Mathematics">MATH – Mathematics</option>
            </select>
          </div>
        </div>
        <div class="dq-group" style="background:transparent; border:none; padding:0;">
           <p style="font-size:0.9rem; color:#666; font-style:italic;">
             A unique 6-digit code will be generated automatically.
           </p>
        </div>
        <div class="dq-actions">
          <button id="dqCancel" class="dq-btn secondary" type="button">Cancel</button>
          <button id="dqSave" class="dq-btn" type="button">Save</button>
        </div>
      </div>
    </aside>
  </div>

<script>
  const views = { home: document.getElementById('view-home'), profile: document.getElementById('view-profile') };
  function showView(name) {
    Object.values(views).forEach(v => v?.classList.remove('active'));
    views[name]?.classList.add('active');
    closeNotif(); if(accountMenu) accountMenu.classList.remove('open');
  }

  const menuBtn = document.getElementById('menuBtn');
  const mobileMenuBtn = document.getElementById('mobileMenuBtn');
  const sidebarOverlay = document.getElementById('sidebarOverlay');
  const navItems = document.querySelectorAll('.nav-item[data-route]');
  const indicator = document.querySelector('.active-indicator');
  const accountBtn = document.getElementById('accountBtn');
  const accountMenu = document.getElementById('accountMenu');
  const notifBtn = document.getElementById('notifBtn');
  const notifPanel = document.getElementById('notifPanel');
  const acctMyBtn = document.getElementById('acctMy');

  // --- Initialize Active Indicator Position ---
  // This moves the black indicator to the "Home" icon immediately on load
  document.addEventListener('DOMContentLoaded', () => {
      const activeItem = document.querySelector('.nav-item.active');
      if (activeItem && indicator) {
          indicator.style.top = activeItem.offsetTop + 'px';
      }
  });

  function toggleSidebar() {
    const open = document.body.classList.toggle('nav-open');
    menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    mobileMenuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
  }
  menuBtn.addEventListener('click', toggleSidebar);
  mobileMenuBtn.addEventListener('click', (e) => { e.stopPropagation(); toggleSidebar(); });
  sidebarOverlay.addEventListener('click', () => { document.body.classList.remove('nav-open'); });

  function closeNotif(){ notifPanel?.classList.remove('open'); notifBtn?.setAttribute('aria-expanded','false'); }
  function openNotif(){ notifPanel?.classList.add('open'); notifBtn?.setAttribute('aria-expanded','true'); }
  notifBtn?.addEventListener('click', (e) => { e.stopPropagation(); notifPanel.classList.contains('open') ? closeNotif() : openNotif(); accountMenu.classList.remove('open'); });
  accountBtn.addEventListener('click', (e) => { e.stopPropagation(); accountMenu.classList.toggle('open'); closeNotif(); });
  acctMyBtn.addEventListener('click', (e) => { e.stopPropagation(); showView('profile'); });

  document.addEventListener('click', (e) => {
    if (!notifPanel.classList.contains('open') && !accountMenu.classList.contains('open')) return;
    const clickInNotif = e.target === notifPanel || notifPanel.contains(e.target) || e.target === notifBtn || notifBtn.contains(e.target);
    const clickInAcct = e.target === accountMenu || accountMenu.contains(e.target) || e.target === accountBtn || accountBtn.contains(e.target);
    if (!clickInNotif) closeNotif();
    if (!clickInAcct) accountMenu.classList.remove('open');
  });

  navItems.forEach(btn => {
    btn.addEventListener('click', () => {
      if (btn.dataset.route === 'folders') { window.location.href = 'folders.php'; return; }
      document.querySelectorAll('.nav-item').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      if(indicator) indicator.style.top = btn.offsetTop + 'px';
      if (btn.dataset.route === 'home') showView('home');
      if (btn.dataset.route === 'profile') showView('profile');
      if (window.innerWidth < 768) document.body.classList.remove('nav-open');
    });
  });

  // --- Theme Toggle Logic (Icons) ---
  const themeBtn = document.getElementById('themeToggle');
  const ICON_MOON = "https://images.unsplash.com/vector-1762021885090-5c631ddfa33e?auto=format&fit=crop&q=80&w=880";
  const ICON_SUN  = "https://images.unsplash.com/vector-1762027380971-51b98cfee95d?auto=format&fit=crop&q=80&w=880";

  function updateThemeIcon() {
      const isDark = document.body.classList.contains('dark');
      themeBtn.src = isDark ? ICON_SUN : ICON_MOON;
  }

  if (localStorage.getItem('thinkup_theme') === 'dark') {
      document.body.classList.add('dark');
  }
  updateThemeIcon(); // Init

  themeBtn.addEventListener('click', () => {
      document.body.classList.toggle('dark');
      localStorage.setItem('thinkup_theme', document.body.classList.contains('dark') ? 'dark' : 'light');
      updateThemeIcon();
  });

  const drawer = document.getElementById('quizDrawer');
  const dqSave = document.getElementById('dqSave');
  const createBtn = document.getElementById('createBtn');
  createBtn?.addEventListener('click', () => drawer.classList.add('show'));
  document.querySelectorAll('[data-close="drawer"], #dqClose, #dqCancel').forEach(el => { el.addEventListener('click', () => drawer.classList.remove('show')); });

  dqSave?.addEventListener('click', () => {
    const name = document.getElementById('dq_name').value.trim();
    const course = document.getElementById('dq_course').value;
    if (!name || !course) { alert('Please fill in the Name and Course.'); return; }
    
    fetch('create_quiz.php', {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ title: name, course: course })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) { window.location.href = 'quiz-editor.php?id=' + data.quiz_id; } 
        else { alert(data.message || "Error creating quiz"); }
    })
    .catch(err => { console.error(err); alert('Connection error'); });
  });

  const joinModal = document.getElementById('joinModal');
  const joinBtn = document.getElementById('joinBtn');
  const closeJoin = document.getElementById('closeJoinModal');
  const submitJoin = document.getElementById('btnJoinSubmit');
  joinBtn?.addEventListener('click', () => joinModal.classList.add('show'));
  closeJoin?.addEventListener('click', () => joinModal.classList.remove('show'));
  submitJoin?.addEventListener('click', () => {
      const code = document.getElementById('join_code').value.trim();
      if(code.length > 0) { window.location.href = 'quiz-taking.php?code=' + code; }
  });

  document.getElementById('saveProfile')?.addEventListener('click', () => {
      const toast = document.getElementById('toast');
      toast.classList.add('show');
      setTimeout(() => toast.classList.remove('show'), 2000);
  });
  
  const avatarEditBtn = document.getElementById('avatarEdit');
  const avatarModal = document.getElementById('avatarModal');
  const closeAvatar = document.getElementById('closeAvatarModal');
  const uploadBtn = document.getElementById('uploadAvatarBtn');
  const fileInput = document.getElementById('avatarFileModal');
  const preview = document.getElementById('avupCircle');
  const saveAvatarBtn = document.getElementById('saveAvatarBtn');
  let tempAvatar = null;
  avatarEditBtn?.addEventListener('click', () => avatarModal.classList.add('show'));
  closeAvatar?.addEventListener('click', () => avatarModal.classList.remove('show'));
  uploadBtn?.addEventListener('click', () => fileInput.click());
  fileInput?.addEventListener('change', () => {
      if(fileInput.files[0]){
          const reader = new FileReader();
          reader.onload = (e) => { tempAvatar = e.target.result; preview.style.backgroundImage = `url(${tempAvatar})`; };
          reader.readAsDataURL(fileInput.files[0]);
      }
  });
  saveAvatarBtn?.addEventListener('click', () => {
      if(tempAvatar) {
          document.getElementById('avatar').style.backgroundImage = `url(${tempAvatar})`;
          document.getElementById('avatar').textContent = '';
          avatarModal.classList.remove('show');
      }
  });
</script>
</body>
</html>