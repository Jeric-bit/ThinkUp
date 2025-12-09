<?php
// progress.php
session_start();
require 'db_connect.php';

// 1. Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$user_id = $_SESSION['user_id'];

// 2. Calculate Statistics
$total_xp = 0;
$quizzes_taken = 0;
$total_percent = 0;

try {
    // Fetch all results for this user
    $stmt = $pdo->prepare("SELECT * FROM quiz_results WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $results = $stmt->fetchAll();

    $quizzes_taken = count($results);

    foreach ($results as $row) {
        $total_xp += ($row['score'] * 10);
        if ($row['total_questions'] > 0) {
            $total_percent += ($row['score'] / $row['total_questions']);
        }
    }

    $avg_score = $quizzes_taken > 0 ? round(($total_percent / $quizzes_taken) * 100) : 0;
    $level = floor($total_xp / 100) + 1;
    $xp_progress = $total_xp % 100;

    // 3. Fetch Recommended Quizzes
    $recStmt = $pdo->prepare("SELECT * FROM quizzes WHERE user_id != ? ORDER BY RAND() LIMIT 4");
    $recStmt->execute([$user_id]);
    $recommendations = $recStmt->fetchAll();

} catch (PDOException $e) {
    // Silent fail
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>ThinkUp | Progress</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
<style>
  /* =========================================================
     1. GLOBAL STYLES (MATCHING HOME.PHP)
     ========================================================= */
  :root{
    --ink:#000000; --bg:#caf0f8; --dark:#023047;
    --side:#e4f7fb; --sideHover:#caf0f8; --sideActive:#caf0f8;
    --divider:#218ca6; --accent:#1b282c;
    --sideW:72px; --contentPad:28px;
    
    /* Progress Page Specific Variables */
    --card-bg: rgba(255, 255, 255, 0.4); 
    --border: #8ecae6;
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

    /* Progress Page Specific Dark Mode */
    --card-bg: rgba(0, 0, 0, 0.25);
    --border: #218ca6;
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

  /* --- ACTIVE STATE: No Dark Background, Just Indicator --- */
  .nav-item.active {
    background: transparent; 
  }
  /* Keep text/icon visible */
  .nav-item.active .nav-label { font-weight: 700; }

  .active-indicator{ position:absolute; right:0; width:6px; height:56px; background:var(--accent);
    top:0; transition: top .25s cubic-bezier(.22,.61,.36,1); pointer-events:none; z-index:2; }

  /* Expanded Sidebar Internal Layout */
  body.nav-open .nav-item{ justify-content:flex-start; gap:12px; }
  .nav-label{ font-family:'Poppins',sans-serif; white-space:nowrap; overflow:hidden; opacity:0;
    transform:translateX(-4px); transition:opacity .15s ease, transform .15s ease, width .2s ease; width:0;}
  body.nav-open .nav-label{ opacity:1; transform:translateX(0); width:auto; }

  /* ───── Header (Fixed) ───── */
  header{
    height:70px; background:#e4f7fb; border-bottom: 1px solid #218ca6;
    display:flex; align-items:center; justify-content:space-between; padding:0 20px;
    position: fixed; top: 0; right: 0; left: var(--sideW); 
    width: auto; z-index: 50;
    transition: left .25s cubic-bezier(0.4, 0, 0.2, 1);
  }
  
  /* Force Header to stay light in Dark Mode */
  body.dark header { background: #e4f7fb; border-color: #218ca6; }

  /* Main Content Pushed Down */
  main{ 
    margin-left: var(--sideW); 
    padding: 100px var(--contentPad) 50px; 
    transition: margin-left .25s cubic-bezier(0.4, 0, 0.2, 1); 
    min-height: 100vh;
    display: flex; flex-direction: column; align-items: center;
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
     ACCOUNT DROPDOWN STYLES (MATCHING HOME.PHP)
     ========================================================= */
  .account-wrap{ position:relative; display:inline-flex; }
  
  /* Account Menu - Force Light BG */
  .account-menu{ 
    position:absolute; right:0; top:calc(100% + 10px); width:240px; 
    background:#e4f7fb; /* Always light background */
    color:#000; border:2px solid #218ca6; border-radius:16px; padding:12px; 
    opacity:0; pointer-events:none; transition:opacity .18s; z-index:99; 
  }
  .account-menu.open{ opacity:1; pointer-events:auto; }
  
  .account-item{ 
    width:100%; display:flex; align-items:center; gap:10px; padding:12px; 
    border-radius:14px; font-weight:800; cursor:pointer; margin-top:10px; 
  }
  .account-item:first-child{margin-top:0}
  
  /* Primary Item (My Account) - Dark Blue */
  .account-item.primary{ background:#0b2f42; color:#fff; border:none; }
  
  /* Card Item (Logout) - White with Border */
  .account-item.card{ background:#fff; color:#0b2f42; border: 1px solid #218ca6; }

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
  }

  .sidebar-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 90;
    opacity: 0; pointer-events: none; transition: opacity 0.3s ease;
    backdrop-filter: blur(2px);
  }
  body.nav-open .sidebar-overlay { opacity: 1; pointer-events: auto; }

  /* =========================================================
     2. PROGRESS PAGE SPECIFIC STYLES
     ========================================================= */
  h1 { font-size: 38px; font-weight: 800; color: #023047; margin: 0 0 32px; text-align: center; }
  body.dark h1 { color: #fff; }
  h3 { font-size: 24px; font-weight: 600; color: #023047; margin: 0 0 20px; text-align: center; }
  body.dark h3 { color: #a0c4ff; }

  /* Chart */
  .chart-card { background: var(--card-bg); border: 2px solid var(--border); border-radius: 38px; padding: 50px; width: 100%; max-width: 500px; display: flex; justify-content: center; align-items: center; margin-bottom: 48px; }
  .donut { width: 220px; height: 220px; border-radius: 50%; display: grid; place-items: center; box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
  .donut-inner { width: 150px; height: 150px; background: #caf0f8; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 36px; color: #000; }
  body.dark .donut-inner { background: #023047; color: #fff; }

  /* XP Card */
  .xp-card { background: var(--card-bg); border: 2px solid var(--border); border-radius: 30px; padding: 36px 42px; width: 100%; max-width: 1100px; display: flex; align-items: center; gap: 28px; margin-bottom: 50px; }
  .user-avatar { width: 90px; height: 90px; border: 4px solid #000; border-radius: 50%; display: grid; place-items: center; background: #fff; font-size: 40px; }
  .xp-content { flex: 1; display: flex; flex-direction: column; gap: 14px; }
  .xp-header { display: flex; justify-content: space-between; align-items: center; }
  .xp-level { font-size: 22px; font-weight: 800; color: #000; } body.dark .xp-level { color: #fff; }
  .xp-badge { background: #6abccf; color: #000; font-size: 14px; font-weight: 800; padding: 6px 14px; border-radius: 12px; }
  .xp-bar-bg { width: 100%; height: 32px; background: #e1e8eb; border-radius: 999px; overflow: hidden; border: 1px solid rgba(0,0,0,0.05); }
  .xp-bar-fill { height: 100%; background: #023047; border-radius: 999px; transition: width 0.5s ease; }
  body.dark .xp-bar-fill { background: #4ecdc4; }

  /* Quiz List */
  .rec-title { text-align: left; width: 100%; max-width: 1100px; font-weight: 700; color: #000; margin-bottom: 20px; font-size: 20px; } body.dark .rec-title { color: #fff; }
  .quiz-list { width: 100%; max-width: 1100px; display: flex; flex-direction: column; gap: 20px; }
  .quiz-card { background: var(--card-bg); border: 2px solid var(--border); border-radius: 24px; padding: 28px 36px; display: flex; align-items: center; gap: 24px; transition: transform 0.2s ease; cursor: pointer; }
  .quiz-card:hover { transform: translateY(-4px); }
  .mini-chart { width: 70px; height: 70px; border-radius: 50%; border: 5px solid #6c757d; display: grid; place-items: center; font-size: 24px; font-weight: 800; color: #4a5a63; flex-shrink: 0; }
  body.dark .mini-chart { border-color: #888; color: #ddd; }
  .qc-info h4 { margin: 0 0 6px 0; font-size: 20px; font-weight: 800; color: #000; } body.dark .qc-info h4 { color: #fff; }
  .qc-info p { margin: 0; font-size: 15px; color: #4a5a63; } body.dark .qc-info p { color: #b8dce8; }
</style>
</head>
<body>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <nav class="sidebar" aria-label="Sidebar">
    <button id="menuBtn" class="nav-item" aria-label="Toggle menu" aria-expanded="false">
      <img class="icon" src="https://images.unsplash.com/vector-1762015667575-6df5d9fc339e?auto=format&fit=crop&q=80&w=1160" alt="">
    </button>

    <a href="home.php" class="nav-item">
      <img class="icon" src="https://images.unsplash.com/vector-1761990311135-822001b59e63?auto=format&fit=crop&q=80&w=1160" alt="">
      <span class="nav-label">Home</span>
    </a>
    
    <a href="folders.php" class="nav-item">
      <img class="icon" src="https://images.unsplash.com/vector-1761990768206-d168851041ee?auto=format&fit=crop&q=80&w=880" alt="">
      <span class="nav-label">Folders</span>
    </a>
    
    <a href="#" class="nav-item active">
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
    <h1>Overall Progress</h1>

    <h3>Progress Percentage</h3>
    <div class="chart-card">
      <div class="donut" style="background: conic-gradient(#023047 0% <?php echo $avg_score; ?>%, #ffffff <?php echo $avg_score; ?>% 100%);">
        <div class="donut-inner"><?php echo $avg_score; ?>%</div>
      </div>
    </div>

    <h3>Earned XP</h3>
    <div class="xp-card">
      <div class="user-avatar">👤</div>
      <div class="xp-content">
        <div class="xp-header">
          <span class="xp-level">Lvl <?php echo $level; ?>: Thinker</span>
          <span class="xp-badge"><?php echo $xp_progress; ?>/100 xp</span>
        </div>
        <div class="xp-bar-bg">
          <div class="xp-bar-fill" style="width: <?php echo $xp_progress; ?>%;"></div>
        </div>
      </div>
    </div>

    <div class="rec-title">Recommended Quizzes For You</div>
    
    <div class="quiz-list">
      <?php if(empty($recommendations)): ?>
        <p style="text-align:center; color:#5a6c78;">No recommendations available yet.</p>
      <?php else: ?>
        <?php foreach($recommendations as $rec): ?>
          <div class="quiz-card" onclick="window.location.href='quiz-taking.php?code=<?php echo htmlspecialchars($rec['quiz_code']); ?>'">
            <div class="mini-chart"><?php echo strtoupper(substr($rec['course_name'], 0, 1)); ?></div>
            <div class="qc-info">
              <h4><?php echo htmlspecialchars($rec['course_name']); ?></h4>
              <p><?php echo htmlspecialchars($rec['title']); ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </main>
  
<script>
  // UI Elements
  const menuBtn = document.getElementById('menuBtn');
  const mobileMenuBtn = document.getElementById('mobileMenuBtn');
  const sidebarOverlay = document.getElementById('sidebarOverlay');
  const accountBtn = document.getElementById('accountBtn');
  const accountMenu = document.getElementById('accountMenu');
  const notifBtn = document.getElementById('notifBtn');
  const notifPanel = document.getElementById('notifPanel');
  const acctMyBtn = document.getElementById('acctMy');
  
  // --- Initialize Active Indicator Position ---
  // This calculates where the "Progress" icon is and moves the black line there
  document.addEventListener('DOMContentLoaded', () => {
      const indicator = document.querySelector('.active-indicator');
      const activeItem = document.querySelector('.nav-item.active');
      
      if (activeItem && indicator) {
          indicator.style.top = activeItem.offsetTop + 'px';
      }
  });

  // Sidebar Logic
  function toggleSidebar() {
    const open = document.body.classList.toggle('nav-open');
    menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    mobileMenuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
  }
  menuBtn.addEventListener('click', toggleSidebar);
  mobileMenuBtn.addEventListener('click', (e) => { e.stopPropagation(); toggleSidebar(); });
  sidebarOverlay.addEventListener('click', () => { document.body.classList.remove('nav-open'); });

  // Popups (Notification & Account)
  function closeNotif(){ notifPanel?.classList.remove('open'); notifBtn?.setAttribute('aria-expanded','false'); }
  function openNotif(){ notifPanel?.classList.add('open'); notifBtn?.setAttribute('aria-expanded','true'); }
  notifBtn?.addEventListener('click', (e) => { e.stopPropagation(); notifPanel.classList.contains('open') ? closeNotif() : openNotif(); accountMenu.classList.remove('open'); });
  accountBtn.addEventListener('click', (e) => { e.stopPropagation(); accountMenu.classList.toggle('open'); closeNotif(); });
  
  // Close popups on outside click
  document.addEventListener('click', (e) => {
    if (!notifPanel.classList.contains('open') && !accountMenu.classList.contains('open')) return;
    const clickInNotif = e.target === notifPanel || notifPanel.contains(e.target) || e.target === notifBtn || notifBtn.contains(e.target);
    const clickInAcct = e.target === accountMenu || accountMenu.contains(e.target) || e.target === accountBtn || accountBtn.contains(e.target);
    if (!clickInNotif) closeNotif();
    if (!clickInAcct) accountMenu.classList.remove('open');
  });

  // My Account Navigation Logic (Stores flag to open profile on Home page)
  acctMyBtn.addEventListener('click', (e) => { 
       e.preventDefault();
       e.stopPropagation();
       localStorage.setItem('thinkup_open_profile', 'true');
       window.location.href = 'home.php';
  });

  // --- Theme Toggle Logic (Icons) ---
  const themeBtn = document.getElementById('themeToggle');
  const ICON_MOON = "https://images.unsplash.com/vector-1762021885090-5c631ddfa33e?auto=format&fit=crop&q=80&w=880";
  const ICON_SUN  = "https://images.unsplash.com/vector-1762027380971-51b98cfee95d?auto=format&fit=crop&q=80&w=880";

  function updateThemeIcon() {
      const isDark = document.body.classList.contains('dark');
      themeBtn.src = isDark ? ICON_SUN : ICON_MOON;
  }

  // Check saved theme
  if (localStorage.getItem('thinkup_theme') === 'dark') {
      document.body.classList.add('dark');
  }
  updateThemeIcon(); 

  themeBtn.addEventListener('click', () => {
      document.body.classList.toggle('dark');
      localStorage.setItem('thinkup_theme', document.body.classList.contains('dark') ? 'dark' : 'light');
      updateThemeIcon();
  });
</script>
</body>
</html> 