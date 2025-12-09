<?php
// quiz-editor.php
session_start();
require 'db_connect.php';

// 1. Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$quiz_id = $_GET['id'] ?? null;
if (!$quiz_id) {
    header("Location: home.php");
    exit();
}

try {
    // 2. Fetch Quiz Details
    $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ? AND user_id = ?");
    $stmt->execute([$quiz_id, $_SESSION['user_id']]);
    $quiz = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$quiz) {
        die("Error: Quiz not found or access denied.");
    }

    $quiz_title = htmlspecialchars($quiz['title']);
    $quiz_course = htmlspecialchars($quiz['course_name']);
    $quiz_code = htmlspecialchars($quiz['quiz_code']);
    $course_initial = strtoupper(substr($quiz_course, 0, 1));
    $share_link = "https://ThinkUp.com/" . $quiz_course . "/" . $quiz_code;

    // 3. Fetch Questions (For the Editor)
    $qStmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY order_index ASC, id ASC");
    $qStmt->execute([$quiz_id]);
    $db_questions = $qStmt->fetchAll(PDO::FETCH_ASSOC);

    $js_questions = [];

    if (count($db_questions) > 0) {
        foreach ($db_questions as $row) {
            $optStmt = $pdo->prepare("SELECT option_text, is_correct FROM question_options WHERE question_id = ?");
            $optStmt->execute([$row['id']]);
            $db_options = $optStmt->fetchAll(PDO::FETCH_ASSOC);

            $options = [];
            $correct = null;

            if ($row['type'] === 'multiple_choice') {
                for($i=0; $i<4; $i++) {
                    $val = $db_options[$i]['option_text'] ?? null;
                    $options[] = $val;
                    if (isset($db_options[$i]) && $db_options[$i]['is_correct']) {
                        $correct = $val;
                    }
                }
            } 
            elseif ($row['type'] === 'identification') {
                foreach($db_options as $opt) {
                    $options[] = $opt['option_text'];
                }
                $correct = $options[0] ?? null; 
            }
            elseif ($row['type'] === 'true_false') {
                $options = [null, null, null, null];
                foreach($db_options as $opt) {
                    if ($opt['is_correct']) $correct = $opt['option_text'];
                }
            }

            $js_questions[] = [
                'id' => $row['id'],
                'tempId' => uniqid(),
                'text' => $row['question_text'],
                'type' => $row['type'],
                'correct' => $correct,
                'options' => $options,
                'instructions' => $row['instructions'] ?? '',
                'randomize' => (bool)$row['is_randomized'],
                'required' => (bool)$row['is_required'],
                'showAnswers' => (bool)$row['show_answers']
            ];
        }
    } else {
        $js_questions[] = [
            'id' => null, 'tempId' => uniqid(), 'text' => '', 'type' => 'multiple_choice',
            'correct' => null, 'options' => [null, null, null, null],
            'instructions' => '', 'randomize' => true, 'required' => true, 'showAnswers' => true
        ];
    }

    // ==========================================
    // 4. FETCH STUDENT RESPONSES (Responses Tab)
    // ==========================================
    
    $resStmt = $pdo->prepare("
        SELECT qr.*, u.full_name AS username, u.email 
        FROM quiz_results qr
        JOIN users u ON qr.user_id = u.id
        WHERE qr.quiz_id = ?
        ORDER BY qr.created_at DESC
    ");
    $resStmt->execute([$quiz_id]);
    $student_results = $resStmt->fetchAll(PDO::FETCH_ASSOC);

    // ==========================================
    // 5. CALCULATE ANALYTICS (Results Tab)
    // ==========================================
    $stats = [
        'total_students' => count($student_results),
        'avg_score' => 0,
        'high_score' => 0,
        'pass_rate' => 0
    ];

    if ($stats['total_students'] > 0) {
        $scores = array_column($student_results, 'score');
        $stats['avg_score'] = round(array_sum($scores) / count($scores), 1);
        $stats['high_score'] = max($scores);
        
        $pass_count = 0;
        foreach($student_results as $s) {
            $percentage = ($s['total_questions'] > 0) ? ($s['score'] / $s['total_questions']) : 0;
            if ($percentage >= 0.5) $pass_count++;
        }
        $stats['pass_rate'] = round(($pass_count / $stats['total_students']) * 100);
    }

    // Question Analysis
    $itemAnalysis = [];
    if (count($db_questions) > 0) {
        $iaStmt = $pdo->prepare("
            SELECT question_id, SUM(is_correct) as correct_count 
            FROM quiz_responses 
            WHERE quiz_id = ? 
            GROUP BY question_id
        ");
        $iaStmt->execute([$quiz_id]);
        $iaRows = $iaStmt->fetchAll(PDO::FETCH_KEY_PAIR);

        foreach ($db_questions as $index => $q) {
            $qid = $q['id'];
            $correctCount = $iaRows[$qid] ?? 0;
            $total = $stats['total_students'];
            $percent = ($total > 0) ? round(($correctCount / $total) * 100) : 0;
            
            $itemAnalysis[] = [
                'index' => $index + 1,
                'text' => $q['question_text'],
                'percent' => $percent
            ];
        }
    }

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>ThinkUp | Editor - <?php echo $quiz_title; ?></title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
<style>
  /* ================= VARIABLES & CSS ================= */
  :root{
    --ink:#000000; --bg:#caf0f8; --dark:#023047;
    --side:#e4f7fb; --sideHover:#caf0f8; --sideActive:#caf0f8;
    --divider:#218ca6; --accent:#1b282c;
    --sideW:72px; --radius: 20px;
  }
  
  body.dark {
    --bg: #023047; --ink: #E9F8FF; 
    --side: #e4f7fb; --sideHover: #caf0f8; --sideActive: #caf0f8; 
    --divider: #218ca6; --accent: #1b282c; --dark: #E9F8FF;
  }

  *{box-sizing:border-box;margin:0}
  body, .sidebar, header, .nav-item, .btn { transition: background-color .25s ease, color .25s ease, border-color .25s ease; }
  body{font-family:Poppins,sans-serif;background:var(--bg);color:var(--ink); overflow-x: hidden;}
  body.nav-open{ --sideW:220px; }

  /* ───── Sidebar ───── */
  .sidebar{
    position: fixed; inset: 0 auto 0 0; width: var(--sideW); background: var(--side);
    border-right: 1px solid var(--divider); z-index: 100; padding: 10px 1px;
    display: flex; flex-direction: column; gap: 0px; 
    transition: width .25s cubic-bezier(0.4, 0, 0.2, 1), transform .25s cubic-bezier(0.4, 0, 0.2, 1);
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

  .sidebar-overlay{
    position:fixed; inset:0; background:rgba(2,48,71,0.35); backdrop-filter:blur(2px);
    opacity:0; pointer-events:none; transition:opacity .25s ease;
  }
  body.nav-open .sidebar-overlay{ opacity:1; pointer-events:auto; }
  @media (min-width: 769px) {
    body.nav-open .sidebar-overlay{ opacity:0; pointer-events:none; }
  }

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

  /* ───── Sub-Header ───── */
  .sub-header { 
    margin-left: var(--sideW); margin-top: 70px; transition: margin-left .25s cubic-bezier(0.4, 0, 0.2, 1);
    height: 64px; display: flex; align-items: center; justify-content: center; 
    background: #e4f7fb; border-bottom: 1px solid var(--divider); position: relative; z-index: 30; 
  }
  body.dark .sub-header { background: #e4f7fb; }
  .course-info { position: absolute; left: 24px; top: 50%; transform: translateY(-50%); display: flex; align-items: center; gap: 12px; }
  .back-arrow { font-size: 22px; cursor: pointer; color: #000; line-height: 1; font-weight: 900; }
  .course-badge { display: flex; align-items: center; gap: 8px; font-weight: 800; font-size: 1rem; color: #000; }
  .c-circle { width: 32px; height: 32px; background: #000; color: #fff; border-radius: 50%; display: grid; place-items: center; font-size: 14px; font-weight: 700; }
  .tabs { display: flex; height: 100%; align-items: flex-end; }
  .tab-btn { 
    background: transparent; border: none; height: 100%; padding: 0 32px; 
    font-family: inherit; font-size: 16px; font-weight: 500; color: #5a707a; 
    cursor: pointer; position: relative; display: grid; place-items: center; transition: all 0.2s ease;
  }
  .tab-btn:hover { color: #000; background: rgba(33,140,166,0.05); }
  .tab-btn.active { color: #0b2f42; font-weight: 800; background: #d1f1fa; box-shadow: 0 -4px 0 #218ca6 inset; }
  .course-title { font-weight: 700; }
  .course-divider { font-weight: 400; opacity: 0.8; margin-left: 8px; }

  /* ───── Main Layout ───── */
  main { margin-left: var(--sideW); transition: margin-left .25s cubic-bezier(0.4, 0, 0.2, 1); padding: 24px 30px; min-height: calc(100vh - 134px); position: relative; }
  .view-section { display: none; animation: fadeIn 0.3s ease; }
  .view-section.active { display: block; }
  @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

  /* ───── EDITOR STYLES ───── */
  .editor-card {
    width: 100%; max-width: 1280px; min-height: 520px; 
    background: #ffffff; border-radius: 18px; border: 1px solid var(--divider);
    display: grid; grid-template-columns: 260px 1fr 280px; overflow: visible;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03); margin: 0 auto;
  }
  body.dark .editor-card { background: #03283A; border-color: var(--divider); }

  /* Left Col */
  .col-list { border-right: 1px solid var(--divider); padding: 24px 16px; display: flex; flex-direction: column; height: 100%; }
  .col-head { font-weight: 700; color: #000; margin-bottom: 12px; font-size: 0.95rem; display: flex; justify-content: space-between; padding-left: 8px;}
  body.dark .col-head { color: #E9F8FF; }
  .q-list { list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 8px; overflow-y: auto; flex: 1; }
  .q-item { display: flex; align-items: center; gap: 8px; padding: 10px 14px; border-radius: 12px; background: transparent; color: #000; cursor: pointer; transition: all 0.2s; border: 2px solid transparent; }
  body.dark .q-item { color: #fff; }
  .q-item:hover { background: rgba(33,140,166,0.05); }
  .q-item.active { background: #fff; border-color: #218ca6; box-shadow: 0 2px 8px rgba(33,140,166, 0.15); }
  body.dark .q-item.active { background: #023047; border-color: #77D1F6; }

  .add-q-area { margin-top: 16px; display: flex; justify-content: flex-end; padding-right: 8px; }
  .btn-hover-expand {
    height: 40px; width: 40px; border-radius: 20px;
    background: #fff; border: 2px solid #218ca6; color: #218ca6;
    display: flex; align-items: center; justify-content: center; overflow: hidden;
    transition: width 0.3s ease, background 0.2s; cursor: pointer;
    position: relative; white-space: nowrap; box-shadow: 0 2px 5px rgba(0,0,0,0.05);
  }
  .btn-hover-expand:hover { width: 155px; background: #e4f7fb; justify-content: flex-start; }
  .btn-hover-expand .icon-plus { min-width: 36px; height: 36px; display: grid; place-items: center; font-size: 22px; font-weight: 600; flex-shrink: 0; margin-left: 2px; }
  .btn-hover-expand .text-label { opacity: 0; transform: translateX(10px); transition: all 0.3s ease; font-size: 13px; font-weight: 700; color: #0b2f42; pointer-events: none; }
  .btn-hover-expand:hover .text-label { opacity: 1; transform: translateX(0); }
  body.dark .btn-hover-expand { background: transparent; border-color: #77D1F6; color: #77D1F6; }
  body.dark .btn-hover-expand:hover { background: #0a3b51; }
  body.dark .btn-hover-expand .text-label { color: #fff; }

  /* Editor Col */
  .col-editor { padding: 40px 50px; display: flex; flex-direction: column; gap: 24px; background: #ffffff; height: 100%; border-radius: 0 0 18px 18px; overflow-y: auto; }
  body.dark .col-editor { background: #042434; }
  
  .input-box-lg, .input-box-instr { width: 100%; padding: 16px 20px; border: 1.8px solid #8ec9da; border-radius: 14px; font-family: inherit; font-size: 1.1rem; color: #0b2330; outline: none; transition: box-shadow .2s; }
  .input-box-instr { font-size: 0.95rem; color: #5a707a; padding: 12px 18px; }
  .input-box-lg:focus, .input-box-instr:focus { border-color: #218ca6; box-shadow: 0 0 0 3px rgba(33,140,166,0.15); }
  body.dark .input-box-lg, body.dark .input-box-instr { background: #023047; color: #fff; border-color: #0F6C8A; }

  /* Identification Specifics */
  .user-input-preview { width: 100%; padding: 16px 20px; border: 1.8px solid #8ec9da; border-radius: 14px; color: #aaa; background: #f9fcfe; font-style: italic; pointer-events: none; }
  body.dark .user-input-preview { background: #032030; border-color: #0F6C8A; color: #666; }
  .ident-answers-section { margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px; }
  .ident-title { font-weight: 800; font-size: 1.1rem; color: #0b2f42; margin-bottom: 16px; text-align: center; }
  body.dark .ident-title { color: #fff; }

  .answers-group { display: flex; flex-direction: column; gap: 14px; }
  .ans-row { display: flex; align-items: center; gap: 14px; animation: fadeInRow 0.2s ease; }
  .radio-circle { width: 22px; height: 22px; border-radius: 50%; border: 2px solid #218ca6; display: grid; place-items: center; flex-shrink: 0; cursor: pointer; }
  .radio-circle.checked { background: #218ca6; }
  .radio-circle.checked::after { content: ""; width: 8px; height: 8px; background: #fff; border-radius: 50%; }
  .input-box-md { flex: 1; padding: 12px 18px; border: 1.5px solid #8ec9da; border-radius: 12px; font-family: inherit; font-size: 1rem; color: #0b2330; outline: none; transition: border 0.2s; }
  .input-box-md:focus { border-color: #218ca6; box-shadow: 0 0 0 3px rgba(33,140,166,0.15); }
  body.dark .input-box-md { background: #023047; color: #fff; border-color: #0F6C8A; }

  .tf-group { display: flex; flex-direction: column; gap: 16px; margin-top: 10px; }
  .tf-btn { width: 100%; padding: 14px 20px; border: 2px solid #218ca6; border-radius: 14px; background: #fff; color: #0b2f42; font-weight: 800; font-size: 1.1rem; display: flex; justify-content: space-between; align-items: center; cursor: pointer; transition: all 0.2s ease; }
  .tf-btn .tf-icon { width: 24px; height: 24px; border: 2px solid #000; border-radius: 50%; display: grid; place-items: center; font-size: 14px; font-weight: 900; line-height: 1; }
  .tf-btn.selected { background: #0b2f42; color: #fff; border-color: #0b2f42; }
  .tf-btn.selected .tf-icon { border-color: #fff; background: #fff; color: #0b2f42; }
  body.dark .tf-btn { background: transparent; color: #fff; border-color: #77D1F6; }
  body.dark .tf-btn.selected { background: #77D1F6; color: #023047; }

  /* Settings Col */
  .col-settings { border-left: 1px solid var(--divider); padding: 30px 20px; display: flex; flex-direction: column; background: #fff; height: 100%; border-radius: 0 18px 18px 0; }
  body.dark .col-settings { background: #e4f7fb; }
  .type-dropdown { position: relative; width: 100%; z-index: 10; margin-bottom: 30px; }
  .td-btn { width: 100%; height: 48px; background: #fff; border: 2px solid #218ca6; border-radius: 14px; display: flex; align-items: center; justify-content: space-between; padding: 0 14px; cursor: pointer; font-weight: 700; color: #0b2f42; font-size: 0.9rem; }
  body.dark .td-btn { background: #023047; border-color: #0F6C8A; color: #fff; }
  .td-menu { position: absolute; top: calc(100% + 6px); left: 0; width: 100%; background: #fff; border: 2px solid #218ca6; border-radius: 12px; overflow: hidden; display: none; }
  .type-dropdown.open .td-menu { display: block; animation: fadeIn 0.2s ease; }
  .td-opt { padding: 12px 14px; cursor: pointer; font-weight: 500; font-size: 0.9rem; display: flex; align-items: center; gap: 8px; color: #000; }
  .td-opt:hover { background: #e4f7fb; }
  
  .toggles-area { display: flex; flex-direction: column; gap: 20px; flex: 1; }
  .toggle-row { display: flex; align-items: center; justify-content: space-between; font-weight: 600; font-size: 0.9rem; color: #0b2330; }
  .switch { position: relative; display: inline-block; width: 44px; height: 24px; }
  .switch input { opacity: 0; width: 0; height: 0; }
  .slider { position: absolute; cursor: pointer; inset: 0; background-color: #ccc; transition: .4s; border-radius: 34px; }
  .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 3px; bottom: 3px; background-color: white; transition: .4s; border-radius: 50%; }
  input:checked + .slider { background-color: #218ca6; }
  input:checked + .slider:before { transform: translateX(20px); }

  .actions-group { display: flex; gap: 10px; margin-top: auto; }
  .btn-outline, .btn-solid { flex: 1; height: 48px; border-radius: 12px; font-weight: 700; font-family: inherit; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 0.95rem; }
  .btn-outline { border: 2px solid #218ca6; background: transparent; color: #0b2f42; }
  .btn-outline:hover { background: #e4f7fb; }
  .btn-solid { border: none; background: #0b2f42; color: #fff; }
  .btn-solid:hover { background: #15455c; }
  body.dark .btn-outline { border-color: #0F6C8A; color: #000; }
  body.dark .btn-solid { background: #023047; color: #fff; border: 1px solid #77D1F6; }

  /* ================= RESPONSES VIEW ================= */
  .responses-wrapper{
    display:flex; gap:24px; align-items:flex-start; background:#d9eef5;
    padding:24px 28px; border-radius:24px; border:1px solid rgba(2,48,71,0.12);
    box-shadow:0 10px 26px rgba(2,48,71,0.08);
  }
  .responses-filter{
    width:210px; background:#f7fcff; border-radius:20px; border:2px solid #c7e6f0;
    padding:22px 20px; display:flex; flex-direction:column; gap:20px; position:sticky;
    top:140px;
  }
  .responses-filter h4{ font-size:0.95rem; font-weight:800; color:#023047; margin:0; }
  .filter-group{ display:flex; flex-direction:column; gap:12px; }
  .filter-option{
    display:flex; align-items:center; gap:10px; cursor:pointer; font-weight:600;
    color:#023047; font-size:0.9rem;
  }
  .filter-option input{ appearance:none; width:18px; height:18px; border-radius:50%;
    border:2px solid #218ca6; position:relative; cursor:pointer; transition:all .18s ease;
  }
  .filter-option input:checked{ background:#218ca6; box-shadow:0 0 0 3px rgba(33,140,166,0.25); }
  .filter-option input:checked::after{ content:""; position:absolute; inset:3px; background:#fff;
    border-radius:50%; }
  .filter-option input:checked + span{ color:#218ca6; }
  .responses-search{ position:relative; }
  .responses-search input{
    width:100%; padding:10px 14px 10px 40px; border-radius:12px; border:1.5px solid #b8dbe6;
    font-size:0.9rem; background:#fff; outline:none; transition:border-color .18s ease;
  }
  .responses-search input:focus{ border-color:#218ca6; box-shadow:0 0 0 3px rgba(33,140,166,0.2); }
  .responses-search .icon{ position:absolute; left:14px; top:50%; transform:translateY(-50%);
    font-size:0.95rem; color:#4e6b76; }
  .filter-divider{ height:1px; width:100%; background:#c7e6f0; border-radius:999px; }
  .filter-note{ font-size:0.78rem; color:#4e6b76; line-height:1.45; }
  .filter-note strong{ color:#023047; }

  .responses-columns{ flex:1; display:grid; grid-template-columns:repeat(3, minmax(200px, 1fr)); gap:18px; }
  .response-card{
    background:#f1faff; border-radius:22px; border:2px solid #b8dbe6; overflow:hidden;
    min-height:380px; display:flex; flex-direction:column;
    box-shadow:0 12px 28px rgba(2,48,71,0.09);
  }
  .response-card-header{
    position:relative; padding:18px 22px; background:#e0f3fb; display:flex;
    align-items:center; justify-content:space-between; border-bottom:1px solid #bad9e6;
  }
  .response-card-title{
    font-weight:800; font-size:1rem; color:#023047; letter-spacing:0.02em;
    text-transform:capitalize;
  }
  .responses-sort{ position:relative; display:flex; align-items:center; }
  .responses-sort-btn{
    display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:999px;
    border:2px solid #023047; background:#023047; color:#fff; font-size:0.75rem;
    font-weight:700; cursor:pointer; transition:transform .15s ease;
  }
  .responses-sort.is-active .responses-sort-btn{ background:#218ca6; border-color:#218ca6; }
  .responses-sort-btn:hover{ transform:translateY(-1px); }
  .responses-sort-btn .caret{ font-size:0.65rem; }
  .responses-sort-menu{
    position:absolute; right:0; top:calc(100% + 6px); background:#fff; border:1.5px solid #218ca6;
    border-radius:14px; padding:10px; display:flex; flex-direction:column; gap:6px;
    min-width:120px; box-shadow:0 16px 28px rgba(2,48,71,0.16); opacity:0; pointer-events:none;
    transform:translateY(-6px); transition:opacity .18s ease, transform .18s ease;
    z-index:20;
  }
  .responses-sort.is-open .responses-sort-menu{ opacity:1; pointer-events:auto; transform:translateY(0); }
  .responses-sort-menu button{
    background:#f1fbff; border:1px solid #cce5ef; border-radius:10px; padding:8px 10px;
    font-size:0.78rem; font-weight:700; color:#023047; cursor:pointer; transition:background .18s ease;
  }
  .responses-sort-menu button:hover{ background:#d0eef7; }

  .response-card-body{ flex:1; padding:18px 22px; display:flex; flex-direction:column; gap:12px; overflow:auto; }
  .response-item{
    display:flex; align-items:center; justify-content:space-between; font-weight:600;
    color:#023047; font-size:0.92rem; padding:10px 14px; border-radius:12px;
    background:#ffffff; border:1px solid transparent; transition:transform .15s ease, border-color .15s ease;
  }
  .response-item.stack{ flex-direction:column; align-items:flex-start; gap:6px; }
  .response-item.centered{ flex-direction:column; align-items:center; text-align:center; gap:10px; }
  .response-item.centered .status-chip{ align-self:center; }
  .response-item:hover{ transform:translateY(-1px); border-color:#9fd1e3; }
  .response-item small{ font-size:0.7rem; color:#5b7783; font-weight:500; }
  .status-chip{ display:inline-flex; align-items:center; gap:6px; padding:6px 12px; border-radius:999px;
    font-size:0.72rem; font-weight:700; letter-spacing:0.02em; }
  .status-chip.pass{ background:#c7f3dc; color:#146038; }
  .status-chip.fail{ background:#fde0e3; color:#9a2f3b; }
  .status-chip span{ font-size:0.7rem; }
  .response-empty{ padding:40px 20px; text-align:center; font-weight:600; color:#4a6570; font-size:0.95rem; }

  body.dark .responses-wrapper{ background:#0f3650; border-color:rgba(233,248,255,0.12); }
  body.dark .responses-filter{ background:#0d2a3a; border-color:#1a4c63; }
  body.dark .filter-option{ color:#e4f7fb; }
  body.dark .filter-option input{ border-color:#77D1F6; }
  body.dark .filter-option input:checked + span{ color:#77D1F6; }
  body.dark .responses-search input{ background:#0d2a3a; border-color:#1a4c63; color:#e4f7fb; }
  body.dark .responses-search .icon{ color:#77D1F6; }
  body.dark .filter-note{ color:#9ec9d8; }
  body.dark .filter-note strong{ color:#77D1F6; }
  body.dark .responses-columns .response-card{ background:#0d2a3a; border-color:#1a4c63; box-shadow:0 10px 24px rgba(1,18,26,0.55); }
  body.dark .response-card-header{ background:#12374d; border-bottom-color:#1a4c63; }
  body.dark .response-card-title{ color:#e4f7fb; }
  body.dark .responses-sort-btn{ border-color:#77D1F6; background:#77D1F6; color:#023047; }
  body.dark .responses-sort.is-active .responses-sort-btn{ background:#0F6C8A; border-color:#77D1F6; color:#fff; }
  body.dark .responses-sort-menu{ background:#0d2a3a; border-color:#77D1F6; }
  body.dark .responses-sort-menu button{ background:#12374d; border-color:#1a4c63; color:#e4f7fb; }
  body.dark .response-item{ background:#12374d; color:#e4f7fb; }
  body.dark .response-item small{ color:#a6cfe0; }
  body.dark .status-chip.pass{ background:#1b5a45; color:#9be9c1; }
  body.dark .status-chip.fail{ background:#5c2430; color:#ffb5bf; }

  /* ================= RESULTS VIEW ================= */
  .results-wrapper{ max-width:1050px; margin:0 auto; }
  .results-card{
    display:flex; gap:28px; background:#d9eef5; border-radius:32px; padding:32px;
    border:1px solid rgba(2,48,71,0.14); box-shadow:0 16px 36px rgba(2,48,71,0.12);
  }
  .results-side{
    width:260px; background:#f7fcff; border-radius:26px; border:2px solid #c7e6f0;
    padding:28px 24px; display:flex; flex-direction:column; gap:22px;
  }
  .results-side h3{ font-size:1.35rem; font-weight:800; color:#023047; margin:0; }
  .results-side p{ font-size:0.85rem; color:#54707d; line-height:1.5; margin:0; }
  .results-metric,
  .results-submissions{
    background:#fff; border-radius:20px; border:1.5px solid #b8dbe6; padding:20px 22px;
    display:flex; flex-direction:column; gap:10px;
  }
  .metric-label{ font-size:0.9rem; font-weight:700; color:#4a6570; text-transform:uppercase; letter-spacing:0.08em; }
  .metric-value{ font-size:2.4rem; font-weight:800; color:#023047; line-height:1; }
  .sub-labels{ display:flex; justify-content:space-between; gap:14px; font-size:0.8rem; color:#4a6570; font-weight:600; }
  .sub-labels span strong{ display:block; font-size:1.1rem; color:#023047; margin-bottom:4px; }

  .results-main{
    flex:1; background:#f1faff; border-radius:26px; border:2px solid #b8dbe6;
    padding:28px 32px; display:flex; flex-direction:column; gap:26px;
  }
  .results-main header{ display:flex; flex-direction:column; gap:6px; }
  .results-main h4{ font-size:1.2rem; font-weight:800; color:#023047; margin:0; }
  .results-main p{ margin:0; font-size:0.85rem; color:#4a6570; }

  .results-chart{
    flex:1; background:#ffffff; border-radius:22px; border:1.5px solid #cce5ef;
    padding:28px 26px; display:flex; align-items:flex-end; gap:20px; min-height:260px;
    overflow-x:auto;
  }
  .results-bar{ flex:0 0 86px; display:flex; flex-direction:column; align-items:center; gap:10px; }
  .results-bar-value{ font-size:0.9rem; font-weight:700; color:#023047; }
  .results-bar-column{
    width:100%; flex:1; background:linear-gradient(180deg,#8097a9 0%, #0b2f42 100%);
    border-radius:18px 18px 10px 10px; display:flex; align-items:flex-end; justify-content:center;
    min-height:20px; position:relative; overflow:hidden;
  }
  .results-bar-column::after{
    content:""; position:absolute; inset:0; border-radius:inherit;
    background:linear-gradient(180deg, rgba(255,255,255,0.35) 0%, rgba(255,255,255,0) 90%);
  }
  .results-bar-fill{
    width:100%; border-radius:inherit; background:linear-gradient(180deg,#4f6c7d 0%, #0b2f42 100%);
    transition:height .7s ease; height:0;
  }
  .results-bar-label{ font-size:0.85rem; font-weight:700; color:#4a6570; }
  .results-chart[data-ready="true"] .results-bar-fill{ height:var(--bar-height,0%); }

  .results-empty{
    flex:1; display:grid; place-items:center; font-weight:600; font-size:0.95rem;
    color:#4a6570; background:#ffffff; border-radius:22px; border:1.5px solid #cce5ef;
    padding:40px;
  }

  body.dark .results-card{ background:#0f3650; border-color:rgba(119,209,246,0.25); box-shadow:0 16px 36px rgba(1,18,26,0.55); }
  body.dark .results-side{ background:#0d2a3a; border-color:#1a4c63; }
  body.dark .results-side h3{ color:#e4f7fb; }
  body.dark .results-side p{ color:#9ec9d8; }
  body.dark .results-metric, body.dark .results-submissions{ background:#112f42; border-color:#1a4c63; }
  body.dark .metric-label{ color:#77D1F6; }
  body.dark .metric-value{ color:#e4f7fb; }
  body.dark .sub-labels{ color:#9ec9d8; }
  body.dark .sub-labels span strong{ color:#77D1F6; }
  body.dark .results-main{ background:#0d2a3a; border-color:#1a4c63; }
  body.dark .results-main h4{ color:#e4f7fb; }
  body.dark .results-main p{ color:#9ec9d8; }
  body.dark .results-chart{ background:#12374d; border-color:#1a4c63; }
  body.dark .results-bar-value{ color:#e4f7fb; }
  body.dark .results-bar-column{ background:linear-gradient(180deg,#3c5f71 0%, #021824 100%); }
  body.dark .results-bar-fill{ background:linear-gradient(180deg,#5a8da8 0%, #0F6C8A 100%); }
  body.dark .results-bar-label{ color:#9ec9d8; }
  body.dark .results-empty{ background:#12374d; border-color:#1a4c63; color:#9ec9d8; }

  /* SHARE */
  .share-new-card { background: #fff; border: 1px solid var(--divider); border-radius: 18px; max-width: 700px; margin: 0 auto; padding: 40px; box-shadow: 0 8px 30px rgba(0,0,0,0.05); text-align: center; }
  .share-heading { font-size: 1.8rem; font-weight: 800; color: #0b2f42; margin-bottom: 30px; }
  .share-row-1 { display: flex; align-items: center; gap: 12px; background: #f0f8fa; border: 1px solid #ccedee; border-radius: 14px; padding: 6px 6px 6px 16px; margin-bottom: 24px; }
  .share-icon-search { font-size: 1.2rem; color: #218ca6; }
  .share-link-text { flex: 1; text-align: left; font-family: monospace; font-size: 1rem; color: #555; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  .copy-pill { background: #0b2f42; color: #fff; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: background 0.2s; white-space: nowrap; }
  .copy-pill:hover { background: #15455c; }
  .share-row-2 { display: flex; flex-direction: column; align-items: center; gap: 10px; }
  .code-group { display: flex; align-items: center; gap: 10px; font-size: 1.1rem; color: #000; font-weight: 700; }
  .tooltip-dark { background: #0b2f42; color: #fff; padding: 6px 12px; border-radius: 6px; font-size: 0.75rem; position: absolute; pointer-events: none; opacity: 0; transition: opacity 0.2s; font-weight: 600; top: -40px; right: 0; }
  .tooltip-dark::after { content: ""; position: absolute; top: 100%; left: 50%; transform: translateX(-50%); border-width: 5px; border-style: solid; border-color: #0b2f42 transparent transparent transparent; }
  .copy-container { position: relative; }
  .copy-container.copied .tooltip-dark { opacity: 1; top: -45px; }
  .share-actions{ margin-top:32px; display:flex; justify-content:center; }
  .share-publish-btn{ padding:12px 36px; font-size:1rem; border-radius:14px; }

  @media (max-width: 1100px) {
    .editor-card { grid-template-columns: 220px 1fr 260px; }
    .col-editor { padding: 34px 30px; }
    .col-settings { padding: 26px 20px; }
    main { padding: 22px 24px; }
  }

  @media (max-width: 900px) {
    .editor-card { grid-template-columns: 1fr; display: flex; flex-direction: column; height: auto; }
    .col-list { border-right: none; border-bottom: 1px solid var(--divider); }
    .col-editor { padding: 28px 22px; border-radius: 0; }
    .col-settings { border-left: none; border-top: 1px solid var(--divider); border-radius: 0 0 18px 18px; }
    .responses-wrapper { flex-direction: column; padding: 20px; }
    .responses-filter { position: static; width: 100%; }
    .responses-columns { grid-template-columns: 1fr; }
    .results-card { flex-direction: column; padding: 24px; }
    .results-side { width: 100%; }
  }
  @media (max-width: 768px) {
    :root { --sideW: 0px; } body.nav-open { --sideW: 0px; }
    .sidebar { width: 240px; transform: translateX(-100%); box-shadow: none; }
    body.nav-open .sidebar { transform: translateX(0); box-shadow: 4px 0 25px rgba(0,0,0,0.3); }
    .nav-label { opacity: 1 !important; width: auto !important; transform: none !important; }
    #menuBtn { display: none; }
    header { left: 0; width: 100%; padding: 0 14px; }
    .mobile-toggle { display: block; }
    .brand img.logo { height: 80px; }
    .top-icons { gap: 14px; }
    main, .sub-header { margin-left: 0; }
    main { padding: 20px 18px; }
    .sub-header { height: auto; flex-direction: column; align-items: flex-start; justify-content: center; gap: 14px; padding: 16px 18px; }
    .course-info { position: static; transform: none; width: 100%; justify-content: space-between; }
    .tabs { width: 100%; justify-content: flex-start; overflow-x: auto; }
    .tabs::-webkit-scrollbar { display: none; }
    .tab-btn { flex: 0 0 auto; padding: 0 24px; font-size: 15px; }
  }
  @media (max-width: 600px) {
    main { padding: 18px 16px; }
    .course-info { flex-wrap: wrap; row-gap: 10px; }
    .responses-wrapper { padding: 18px; border-radius: 20px; }
    .responses-columns { gap: 14px; }
    .response-card { min-height: auto; }
    .response-card-header { flex-direction: column; align-items: flex-start; gap: 10px; }
    .responses-sort { width: 100%; justify-content: flex-start; }
    .responses-sort-btn { width: auto; }
    .results-card { padding: 20px; gap: 20px; border-radius: 28px; }
    .results-side { padding: 22px 20px; }
    .metric-value { font-size: 2rem; }
    .sub-labels { flex-direction: column; align-items: flex-start; gap: 8px; }
    .results-main { padding: 22px 20px; }
    .results-chart { padding: 22px 18px; gap: 14px; min-height: 220px; }
    .results-bar { flex: 0 0 72px; }
    .results-bar-label { font-size: 0.78rem; }
    .share-new-card { padding: 28px 20px; }
    .share-actions { margin-top: 26px; }
    .share-publish-btn { width: 100%; }
  }
  @media (max-width: 480px) {
    header { padding: 0 12px; }
    .tab-btn { padding: 0 18px; font-size: 14px; }
    .course-badge { flex-wrap: wrap; row-gap: 6px; }
    .responses-wrapper { padding: 16px; }
    .responses-search input { padding-left: 36px; }
    .results-card { padding: 18px; gap: 16px; }
    .results-side h3 { font-size: 1.2rem; }
    .results-side p { font-size: 0.78rem; }
    .metric-value { font-size: 1.8rem; }
    .results-main { padding: 20px 18px; }
    .results-chart { padding: 20px 16px; gap: 12px; }
    .results-bar { flex: 0 0 64px; }
    .share-row-1 { flex-direction: column; align-items: stretch; }
    .copy-pill { width: 100%; justify-content: center; }
  }
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

<div class="sub-header">
  <div class="course-info">
    <div class="back-arrow" onclick="window.location.href='folders.php'">←</div>
    <div class="course-badge">
      <div class="c-circle"><?php echo $course_initial; ?></div>
      <span style="font-weight:700"><?php echo $quiz_course; ?></span>
      <span id="quizTitleDisplay" style="font-weight:400; opacity:0.8; margin-left:8px;">| <?php echo $quiz_title; ?></span>
    </div>
  </div>
  <div class="tabs">
    <button class="tab-btn active" data-target="editor">Content</button>
    <button class="tab-btn" data-target="responses">Responses</button>
    <button class="tab-btn" data-target="results">Results</button>
    <button class="tab-btn" data-target="share">Share</button>
  </div>
</div>

<main>
  <div id="view-editor" class="view-section active">
    <div class="editor-card">
      <div class="col-list">
        <div class="col-head">Questions</div>
        <ul class="q-list" id="qList"></ul>
        <div class="add-q-area">
          <button class="btn-hover-expand" id="addQuestionBtn">
            <span class="icon-plus">+</span>
            <span class="text-label">Add question</span>
          </button>
        </div>
      </div>
      <div class="col-editor" id="editorPanel"></div>
      <div class="col-settings">
        <div class="type-dropdown" id="typeDropdown">
           <div class="td-btn" onclick="toggleDropdown()">
              <span id="tdSelected"><span class="td-icon">◎</span> Multiple Choice</span>
              <span class="td-arrow">▼</span>
           </div>
           <div class="td-menu">
              <div class="td-opt" onclick="changeType('multiple_choice')"><span class="td-icon">◎</span> Multiple Choice</div>
              <div class="td-opt" onclick="changeType('identification')"><span class="td-icon">=</span> Identification</div>
              <div class="td-opt" onclick="changeType('true_false')"><span class="td-icon">◐</span> True or False</div>
           </div>
        </div>
        <div class="toggles-area" id="togglesArea"></div>
        <div class="actions-group">
           <button class="btn-outline" id="draftBtn">Draft</button>
           <button class="btn-solid" id="saveBtn">Save</button>
        </div>
      </div>
    </div>
  </div>

  <div id="view-responses" class="view-section">
    <?php
      $totalResponses = count($student_results);
      $passCount = 0;
      $failCount = 0;
      foreach ($student_results as $summaryRow) {
        $summaryTotal = isset($summaryRow['total_questions']) ? (int)$summaryRow['total_questions'] : 0;
        $summaryScore = isset($summaryRow['score']) ? (int)$summaryRow['score'] : 0;
        $summaryPercent = $summaryTotal > 0 ? round(($summaryScore / $summaryTotal) * 100) : 0;
        if ($summaryPercent >= 50) {
          $passCount++;
        } else {
          $failCount++;
        }
      }
    ?>
    <div class="responses-wrapper">
      <aside class="responses-filter">
        <h4>Filter responses</h4>
        <div class="filter-group">
          <label class="filter-option">
            <input type="radio" name="responsesFilter" value="all" checked>
            <span>All responses (<?php echo $totalResponses; ?>)</span>
          </label>
          <label class="filter-option">
            <input type="radio" name="responsesFilter" value="passed">
            <span>Passed (<?php echo $passCount; ?>)</span>
          </label>
          <label class="filter-option">
            <input type="radio" name="responsesFilter" value="needs_review">
            <span>Needs review (<?php echo $failCount; ?>)</span>
          </label>
        </div>
        <div class="responses-search">
          <span class="icon">🔍</span>
          <input type="text" id="responsesSearch" placeholder="Search username">
        </div>
        <div class="filter-divider"></div>
        <p class="filter-note"><strong><?php echo $totalResponses; ?></strong> participant<?php echo $totalResponses === 1 ? '' : 's'; ?> submitted this quiz.</p>
        <p class="filter-note">Use the sort controls to align usernames, submission dates, or scores just like in the reference layout.</p>
      </aside>

      <section class="responses-columns">
        <article class="response-card">
          <div class="response-card-header">
            <span class="response-card-title">Usernames</span>
            <div class="responses-sort" data-column="username">
              <button type="button" class="responses-sort-btn">
                <span>Sort</span>
                <span class="caret">▼</span>
              </button>
              <div class="responses-sort-menu">
                <button type="button" data-sort="asc">A → Z</button>
                <button type="button" data-sort="desc">Z → A</button>
              </div>
            </div>
          </div>
          <div class="response-card-body" id="responsesUserList">
            <?php if ($totalResponses > 0): ?>
              <?php foreach ($student_results as $res): ?>
                <?php
                  $rawUsername = isset($res['username']) ? trim((string)$res['username']) : '';
                  $rawEmail = isset($res['email']) ? trim((string)$res['email']) : '';
                  $usernameSlug = strtolower($rawUsername);
                ?>
                <div class="response-item stack" data-username="<?php echo htmlspecialchars($usernameSlug, ENT_QUOTES); ?>">
                  <span>@<?php echo htmlspecialchars($rawUsername, ENT_QUOTES); ?></span>
                  <small><?php echo htmlspecialchars($rawEmail, ENT_QUOTES); ?></small>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="response-empty">No responses yet</div>
            <?php endif; ?>
          </div>
        </article>

        <article class="response-card">
          <div class="response-card-header">
            <span class="response-card-title">Submitted</span>
            <div class="responses-sort" data-column="date">
              <button type="button" class="responses-sort-btn">
                <span>Sort</span>
                <span class="caret">▼</span>
              </button>
              <div class="responses-sort-menu">
                <button type="button" data-sort="asc">Earliest</button>
                <button type="button" data-sort="desc">Latest</button>
              </div>
            </div>
          </div>
          <div class="response-card-body" id="responsesDateList">
            <?php if ($totalResponses > 0): ?>
              <?php foreach ($student_results as $res): ?>
                <?php
                  $timestamp = isset($res['created_at']) ? strtotime($res['created_at']) : false;
                  $timestamp = $timestamp ? $timestamp : 0;
                ?>
                <div class="response-item stack" data-date="<?php echo (int)$timestamp; ?>">
                  <span><?php echo $timestamp ? date('M d, Y', $timestamp) : '—'; ?></span>
                  <small><?php echo $timestamp ? date('g:i A', $timestamp) : 'Not recorded'; ?></small>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="response-empty">No responses yet</div>
            <?php endif; ?>
          </div>
        </article>

        <article class="response-card">
          <div class="response-card-header">
            <span class="response-card-title">Scores</span>
            <div class="responses-sort" data-column="score">
              <button type="button" class="responses-sort-btn">
                <span>Sort</span>
                <span class="caret">▼</span>
              </button>
              <div class="responses-sort-menu">
                <button type="button" data-sort="desc">Highest</button>
                <button type="button" data-sort="asc">Lowest</button>
              </div>
            </div>
          </div>
          <div class="response-card-body" id="responsesScoreList">
            <?php if ($totalResponses > 0): ?>
              <?php foreach ($student_results as $res): ?>
                <?php
                  $totalQuestions = isset($res['total_questions']) ? (int)$res['total_questions'] : 0;
                  $scoreValue = isset($res['score']) ? (int)$res['score'] : 0;
                  $percent = $totalQuestions > 0 ? round(($scoreValue / $totalQuestions) * 100) : 0;
                  $statusText = $percent >= 50 ? 'Passed' : 'Needs review';
                  $statusClass = $percent >= 50 ? 'pass' : 'fail';
                ?>
                <div class="response-item centered" data-score="<?php echo (int)$percent; ?>">
                  <span><?php echo $scoreValue; ?> / <?php echo $totalQuestions; ?></span>
                  <span class="status-chip <?php echo $statusClass; ?>">
                    <span><?php echo $percent; ?>%</span> <?php echo $statusText; ?>
                  </span>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="response-empty">No responses yet</div>
            <?php endif; ?>
          </div>
        </article>
      </section>
    </div>
  </div>

  <div id="view-results" class="view-section">
    <?php
      $viewsCount = isset($quiz['views']) ? (int)$quiz['views'] : $stats['total_students'];
      $finishedCount = $stats['total_students'];
      $unfinishedCount = 0;
    ?>
    <div class="results-wrapper">
      <div class="results-card">
        <aside class="results-side">
          <h3>Insights</h3>
          <p>Monitor engagement and submission performance for this quiz.</p>
          <div class="results-metric">
            <span class="metric-label">Views</span>
            <span class="metric-value"><?php echo $viewsCount; ?></span>
          </div>
          <div class="results-submissions">
            <span class="metric-label">Submissions</span>
            <span class="metric-value"><?php echo $finishedCount; ?></span>
            <div class="sub-labels">
              <span><strong><?php echo $finishedCount; ?></strong>Finished</span>
              <span><strong><?php echo $unfinishedCount; ?></strong>Unfinished</span>
            </div>
          </div>
        </aside>

        <section class="results-main">

          <?php if (empty($itemAnalysis)): ?>
            <div class="results-empty">No data available yet.</div>
          <?php else: ?>
            <div class="results-chart" id="resultsChart" data-ready="true">
              <?php foreach ($itemAnalysis as $item): ?>
                <?php
                  $percent = (int)$item['percent'];
                  $label = 'Item ' . $item['index'];
                ?>
                <div class="results-bar" style="--bar-height: <?php echo $percent; ?>%;">
                  <span class="results-bar-value"><?php echo $percent; ?>%</span>
                  <div class="results-bar-column" title="<?php echo htmlspecialchars($item['text']); ?>">
                    <div class="results-bar-fill"></div>
                  </div>
                  <span class="results-bar-label"><?php echo $label; ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </section>
      </div>
    </div>
  </div>

  <div id="view-share" class="view-section">
    <div class="share-new-card">
       <h2 class="share-heading">Share it and let them ThinkUp!</h2>
       <div class="share-row-1">
          <span class="share-icon-search">⚲</span>
          <span class="share-link-text" id="shareLinkText"><?php echo $share_link; ?></span>
          <div class="copy-container" id="copyLinkContainer">
             <button class="copy-pill" onclick="copyToClipboard('<?php echo $share_link; ?>', 'link')">
                <span>🔗</span> Copy link
             </button>
             <div class="tooltip-dark">Link copied!</div>
          </div>
       </div>
       <div class="share-row-2">
          <div class="code-group">
             <span class="code-label">Quiz Code:</span>
             <div class="copy-container" id="copyCodeContainer">
               <button class="copy-pill" onclick="copyToClipboard('<?php echo $quiz_code; ?>', 'code')">
                  <span>🔗</span> TU - <?php echo $quiz_code; ?>
               </button>
               <div class="tooltip-dark">Code copied!</div>
             </div>
          </div>
       </div>
       <div class="share-actions">
         <button class="btn-solid share-publish-btn" id="publishBtn">Publish Quiz</button>
       </div>
    </div>
  </div>
</main>

<script>
    // === DATA INJECTION ===
    let questions = <?php echo json_encode($js_questions); ?>;
    const quizId = <?php echo json_encode($quiz_id); ?>;
    const quizTitle = <?php echo json_encode($quiz_title); ?>;
    const studentResults = <?php echo json_encode($student_results); ?> || [];
    
    // === UI STATE ===
    let activeIndex = 0;
    
    // === DOM ELEMENTS ===
    const qList = document.getElementById('qList');
    const editorPanel = document.getElementById('editorPanel');
    const togglesArea = document.getElementById('togglesArea');
    const typeDropdown = document.getElementById('typeDropdown');
    const tdSelected = document.getElementById('tdSelected');

    function renderQuestionsList() {
        qList.innerHTML = '';
        questions.forEach((q, idx) => {
            const li = document.createElement('li');
            li.className = `q-item ${idx === activeIndex ? 'active' : ''}`;
            li.innerHTML = `<span style="font-weight:700; font-size:14px;">Q${idx+1}</span>
                            <span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; font-size:13px; color:#555;">
                              ${q.text || 'New Question'}
                            </span>
                            <button style="margin-left:auto; background:none; border:none; color:#888; font-size:16px; cursor:pointer;" onclick="deleteQuestion(event, ${idx})">&times;</button>`;
            li.onclick = (e) => {
                if(e.target.tagName === 'BUTTON') return;
                activeIndex = idx;
                loadQuestion(idx);
                renderQuestionsList();
            };
            qList.appendChild(li);
        });
    }

    function loadQuestion(idx) {
        if(questions.length === 0) return;
        const q = questions[idx];
        
        // Update Type Dropdown UI
        let label = 'Multiple Choice', icon = '◎';
        if(q.type === 'identification') { label = 'Identification'; icon = '='; }
        if(q.type === 'true_false') { label = 'True or False'; icon = '◐'; }
        tdSelected.innerHTML = `<span class="td-icon">${icon}</span> ${label}`;

        // Render Toggles
        togglesArea.innerHTML = `
           <div class="toggle-row">
             <span>Required</span>
             <label class="switch">
               <input type="checkbox" ${q.required ? 'checked' : ''} onchange="updateToggle('required', this.checked)">
               <span class="slider"></span>
             </label>
           </div>
           <div class="toggle-row">
             <span>Randomize Options</span>
             <label class="switch">
               <input type="checkbox" ${q.randomize ? 'checked' : ''} onchange="updateToggle('randomize', this.checked)">
               <span class="slider"></span>
             </label>
           </div>
           <div class="toggle-row">
             <span>Show Correct Answer</span>
             <label class="switch">
               <input type="checkbox" ${q.showAnswers ? 'checked' : ''} onchange="updateToggle('showAnswers', this.checked)">
               <span class="slider"></span>
             </label>
           </div>
        `;

        // Render Editor Inputs
        if (q.type === 'multiple_choice') {
            let optionsHTML = '';
            for(let i=0; i<4; i++) {
                // If it's the last 2 options and they are null, we might hide them or show empty.
                // For simplicity, we show 4 always or just show what is in array.
                // Let's stick to 4 max.
                const val = q.options[i] || '';
                const isCorr = (q.correct && q.correct === val && val !== '') ? 'checked' : '';
                optionsHTML += `
                <div class="ans-row">
                   <div class="radio-circle ${isCorr}" onclick="setCorrectMC(this, ${i})"></div>
                   <input type="text" class="input-box-md" value="${val}" placeholder="Option ${i+1}" oninput="updateOption(${i}, this.value)">
                </div>`;
            }

            editorPanel.innerHTML = `
              <input type="text" class="input-box-instr" id="qInstrInput" value="${q.instructions||''}" placeholder="Type your instructions here" style="margin-bottom:12px;">
              <input type="text" class="input-box-lg" id="qTextInput" value="${q.text||''}" placeholder="Type your question here" style="margin-bottom:24px">
              <div class="answers-group" id="answersGroup">
                 ${optionsHTML}
              </div>
            `;
        } 
        else if (q.type === 'identification') {
            let answersHTML = '';
            // q.options contains all valid answers
            const validList = (q.options && q.options.length) ? q.options : [null];
            validList.forEach((ans, i) => {
                // Filter out trailing nulls if we want, but let's keep one empty to type in
                answersHTML += `<div class="ans-row">
                                  <input type="text" class="input-box-md ident-opt-input" data-idx="${i}" value="${ans||''}" placeholder="Valid answer">
                                </div>`;
            });

            editorPanel.innerHTML = `
              <input type="text" class="input-box-instr" id="qInstrInput" value="${q.instructions||''}" placeholder="Type your instructions here" style="margin-bottom:12px;">
              <input type="text" class="input-box-lg" id="qTextInput" value="${q.text||''}" placeholder="Type your question here" style="margin-bottom:12px;">
              <input type="text" class="user-input-preview" value="answer (user input)" readonly>
              <div class="ident-answers-section">
                 <div class="ident-title">Correct Answer(s):</div>
                 <div class="answers-group" id="identGroup">${answersHTML}</div>
                 <div class="add-q-area">
                   <button class="btn-hover-expand" onclick="addIdentOption()">
                     <span class="icon-plus">+</span><span class="text-label">Add another variant</span>
                   </button>
                 </div>
              </div>
            `;
            
            // Attach event listeners for ident inputs
            document.querySelectorAll('.ident-opt-input').forEach(inp => {
                inp.addEventListener('input', (e) => {
                   const idx = e.target.getAttribute('data-idx');
                   questions[activeIndex].options[idx] = e.target.value;
                   // Also set first one as 'correct' for legacy reasons or display
                   if(questions[activeIndex].options.length > 0)
                      questions[activeIndex].correct = questions[activeIndex].options[0];
                });
            });
        }
        else if (q.type === 'true_false') {
            const isTrue = (q.correct === 'True');
            const isFalse = (q.correct === 'False');
            editorPanel.innerHTML = `
              <input type="text" class="input-box-lg" id="qTextInput" value="${q.text||''}" placeholder="Type your question here" style="margin-bottom:24px">
              <div class="tf-group">
                 <button class="tf-btn ${isTrue?'selected':''}" onclick="setTF('True')">
                   <span>True</span><span class="tf-icon"></span>
                 </button>
                 <button class="tf-btn ${isFalse?'selected':''}" onclick="setTF('False')">
                   <span>False</span><span class="tf-icon"></span>
                 </button>
              </div>
            `;
        }

        // Attach listeners for text inputs
        const txt = document.getElementById('qTextInput');
        if(txt) txt.addEventListener('input', (e) => { 
            questions[activeIndex].text = e.target.value; 
            renderQuestionsList(); 
        });
        
        const instr = document.getElementById('qInstrInput');
        if(instr) instr.addEventListener('input', (e) => {
            questions[activeIndex].instructions = e.target.value;
        });
    }

    // === EDITING LOGIC ===
    
    document.getElementById('addQuestionBtn').addEventListener('click', () => {
        questions.push({
            id: null, tempId: Date.now(), text: '', type: 'multiple_choice',
            correct: null, options: [null,null,null,null],
            instructions: '', randomize:true, required:true, showAnswers:true
        });
        activeIndex = questions.length - 1;
        renderQuestionsList();
        loadQuestion(activeIndex);
    });

    function deleteQuestion(e, idx) {
        e.stopPropagation();
        if(confirm('Delete this question?')) {
            questions.splice(idx, 1);
            if(questions.length === 0) {
                 // Always keep at least one
                 questions.push({id:null, tempId:Date.now(), text:'', type:'multiple_choice', options:[null,null,null,null], correct:null});
            }
            activeIndex = Math.max(0, activeIndex - 1);
            renderQuestionsList();
            loadQuestion(activeIndex);
        }
    }

    function changeType(newType) {
        questions[activeIndex].type = newType;
        questions[activeIndex].correct = null;
        if(newType !== 'multiple_choice') questions[activeIndex].options = [null]; 
        else questions[activeIndex].options = [null,null,null,null];
        
        toggleDropdown();
        loadQuestion(activeIndex);
    }

    function toggleDropdown() {
        typeDropdown.classList.toggle('open');
    }
    
    // Close dropdown if clicked outside
    document.addEventListener('click', (e) => {
        if(!e.target.closest('.type-dropdown')) typeDropdown.classList.remove('open');
    });

    function updateToggle(field, val) {
        questions[activeIndex][field] = val;
    }

    // MCQ Logic
    window.updateOption = function(idx, val) {
        questions[activeIndex].options[idx] = val;
        // If this option was marked correct, update correct value text too
        // But we store correct answer string, so if text changes, we must update correct string if it matches index
        // Simpler: find which index is checked visually? 
        // We will just rely on the click.
        const circles = document.querySelectorAll('.radio-circle');
        if(circles[idx].classList.contains('checked')) {
            questions[activeIndex].correct = val;
        }
    };
    window.setCorrectMC = function(el, idx) {
        document.querySelectorAll('.radio-circle').forEach(c => c.classList.remove('checked'));
        el.classList.add('checked');
        questions[activeIndex].correct = questions[activeIndex].options[idx];
    };

    // TF Logic
    window.setTF = function(val) {
        questions[activeIndex].correct = val;
        // Also force options to be True/False for saving
        questions[activeIndex].options = ['True', 'False']; 
        loadQuestion(activeIndex);
    };

    // Ident Logic
    window.addIdentOption = function() {
        questions[activeIndex].options.push(null);
        loadQuestion(activeIndex);
    }

    // === SAVING ===
    function saveQuizToDB(status) {
        let action = status;
        let btn = null;
        let payloadStatus = 'draft';
        let successMsg = 'Progress Saved';

        if (action === 'publish') {
          btn = document.getElementById('publishBtn');
          payloadStatus = 'published';
          successMsg = 'Quiz Published!';
        } else if (action === 'save') {
          btn = document.getElementById('saveBtn');
          successMsg = 'Changes Saved';
        } else {
          btn = document.getElementById('draftBtn');
          successMsg = 'Draft Saved';
        }

        if(btn) btn.disabled = true;

        fetch('save_quiz.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ 
            quiz_id: quizId, 
            title: quizTitle, // In a real app, bind this to a title input
            status: payloadStatus, 
            questions: questions 
          })
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert(successMsg);
            if(action === 'publish') window.location.href = 'folders.php';
          } else { 
            alert('Error: ' + data.message); 
          }
        })
        .catch(err => { console.error(err); alert('Connection error'); })
        .finally(() => { 
          if(btn) btn.disabled = false; 
        });
    }

      document.getElementById('draftBtn').addEventListener('click', () => saveQuizToDB('draft'));
      document.getElementById('saveBtn').addEventListener('click', () => saveQuizToDB('save'));
      const publishBtn = document.getElementById('publishBtn');
      if (publishBtn) publishBtn.addEventListener('click', () => saveQuizToDB('publish'));

    // === TABS & SHARED UI ===
    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.view-section').forEach(v => v.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById('view-' + btn.dataset.target).classList.add('active');
      });
    });

    // Mobile Sidebar
    const menuBtn = document.getElementById('menuBtn');
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const indicator = document.querySelector('.active-indicator');
    
    // --- Initialize Active Indicator Position ---
    document.addEventListener('DOMContentLoaded', () => {
        const activeItem = document.querySelector('.nav-item.active');
        if (activeItem && indicator) {
            indicator.style.top = activeItem.offsetTop + 'px';
        }
    });
    
    function toggleNav() { document.body.classList.toggle('nav-open'); }
    if(menuBtn) menuBtn.addEventListener('click', toggleNav);
    if(mobileMenuBtn) mobileMenuBtn.addEventListener('click', (e) => { e.stopPropagation(); toggleNav(); });
    if(sidebarOverlay) sidebarOverlay.addEventListener('click', () => document.body.classList.remove('nav-open'));

    // Theme & Notifs (Standard boilerplate)
    const notifBtn = document.getElementById('notifBtn');
    const notifPanel = document.getElementById('notifPanel');
    const accountBtn = document.getElementById('accountBtn');
    const accountMenu = document.getElementById('accountMenu');

    function closeNotif(){ notifPanel?.classList.remove('open'); notifBtn?.setAttribute('aria-expanded','false'); }
    function openNotif(){ notifPanel?.classList.add('open'); notifBtn?.setAttribute('aria-expanded','true'); }
    notifBtn?.addEventListener('click', (e) => { e.stopPropagation(); notifPanel.classList.contains('open') ? closeNotif() : openNotif(); accountMenu.classList.remove('open'); });
    accountBtn.addEventListener('click', (e) => { e.stopPropagation(); accountMenu.classList.toggle('open'); closeNotif(); });
    document.addEventListener('click', (e) => {
      if (!notifPanel.classList.contains('open') && !accountMenu.classList.contains('open')) return;
      const clickInNotif = e.target === notifPanel || notifPanel.contains(e.target) || e.target === notifBtn || notifBtn.contains(e.target);
      const clickInAcct = e.target === accountMenu || accountMenu.contains(e.target) || e.target === accountBtn || accountBtn.contains(e.target);
      if (!clickInNotif) closeNotif();
      if (!clickInAcct) accountMenu.classList.remove('open');
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

    // === RESPONSES VIEW ===
    const responsesUserList = document.getElementById('responsesUserList');
    const responsesDateList = document.getElementById('responsesDateList');
    const responsesScoreList = document.getElementById('responsesScoreList');
    const responsesFilterRadios = document.querySelectorAll('input[name="responsesFilter"]');
    const responsesSearchInput = document.getElementById('responsesSearch');
    const responsesSortBlocks = document.querySelectorAll('.responses-sort');
    const responsesSortDefaults = new Map();
    let responsesActiveFilter = 'all';
    let responsesSortState = null;

    const responsesDateFormatter = new Intl.DateTimeFormat('en-US', { month: 'short', day: '2-digit', year: 'numeric' });
    const responsesTimeFormatter = new Intl.DateTimeFormat('en-US', { hour: 'numeric', minute: '2-digit' });

    const escapeHTML = (value = '') => {
      const str = String(value ?? '');
      const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' };
      return str.replace(/[&<>"']/g, (char) => map[char]);
    };

    const computePercent = (row) => {
      const total = Number(row?.total_questions) || 0;
      const score = Number(row?.score) || 0;
      return total > 0 ? Math.round((score / total) * 100) : 0;
    };

    const deriveStatus = (row) => {
      const percent = computePercent(row);
      return {
        percent,
        statusText: percent >= 50 ? 'Passed' : 'Needs review',
        statusClass: percent >= 50 ? 'pass' : 'fail'
      };
    };

    function renderResponsesView(rows) {
      if (!responsesUserList || !responsesDateList || !responsesScoreList) return;
      if (!rows.length) {
        const emptyMarkup = '<div class="response-empty">No responses yet</div>';
        responsesUserList.innerHTML = emptyMarkup;
        responsesDateList.innerHTML = emptyMarkup;
        responsesScoreList.innerHTML = emptyMarkup;
        return;
      }

      responsesUserList.innerHTML = rows.map((row) => {
        const username = escapeHTML(row?.username || '');
        const email = escapeHTML(row?.email || '');
        const slug = escapeHTML((row?.username || '').toString().trim().toLowerCase());
        return `<div class="response-item stack" data-username="${slug}"><span>@${username}</span><small>${email}</small></div>`;
      }).join('');

      responsesDateList.innerHTML = rows.map((row) => {
        const parsed = row?.created_at ? Date.parse(row.created_at) : NaN;
        const isValid = !Number.isNaN(parsed);
        const dateObj = isValid ? new Date(parsed) : null;
        const dateLabel = dateObj ? responsesDateFormatter.format(dateObj) : '—';
        const timeLabel = dateObj ? responsesTimeFormatter.format(dateObj) : 'Not recorded';
        const stamp = isValid ? Math.floor(parsed / 1000) : 0;
        return `<div class="response-item stack" data-date="${stamp}"><span>${dateLabel}</span><small>${timeLabel}</small></div>`;
      }).join('');

      responsesScoreList.innerHTML = rows.map((row) => {
        const total = Number(row?.total_questions) || 0;
        const score = Number(row?.score) || 0;
        const { percent, statusText, statusClass } = deriveStatus(row);
        return `<div class="response-item centered" data-score="${percent}"><span>${score} / ${total}</span><span class="status-chip ${statusClass}"><span>${percent}%</span> ${statusText}</span></div>`;
      }).join('');
    }

    function applyResponsesState() {
      if (!Array.isArray(studentResults)) return;
      let working = studentResults.slice();

      if (responsesActiveFilter === 'passed') {
        working = working.filter((row) => computePercent(row) >= 50);
      } else if (responsesActiveFilter === 'needs_review') {
        working = working.filter((row) => computePercent(row) < 50);
      }

      const searchTerm = responsesSearchInput?.value.trim().toLowerCase() || '';
      if (searchTerm) {
        working = working.filter((row) => {
          const username = (row?.username || '').toString().toLowerCase();
          const email = (row?.email || '').toString().toLowerCase();
          return username.includes(searchTerm) || email.includes(searchTerm);
        });
      }

      if (responsesSortState) {
        const { column, direction } = responsesSortState;
        const modifier = direction === 'asc' ? 1 : -1;
        working.sort((a, b) => {
          if (column === 'username') {
            const aName = (a?.username || '').toString().toLowerCase();
            const bName = (b?.username || '').toString().toLowerCase();
            return aName.localeCompare(bName) * modifier;
          }
          if (column === 'date') {
            const aTime = a?.created_at ? Date.parse(a.created_at) || 0 : 0;
            const bTime = b?.created_at ? Date.parse(b.created_at) || 0 : 0;
            return (aTime - bTime) * modifier;
          }
          if (column === 'score') {
            return (computePercent(a) - computePercent(b)) * modifier;
          }
          return 0;
        });
      }

      renderResponsesView(working);
    }

    responsesFilterRadios.forEach((radio) => {
      radio.addEventListener('change', () => {
        responsesActiveFilter = radio.value;
        applyResponsesState();
      });
    });

    responsesSearchInput?.addEventListener('input', () => {
      applyResponsesState();
    });

    responsesSortBlocks.forEach((block) => {
      const trigger = block.querySelector('.responses-sort-btn');
      const menu = block.querySelector('.responses-sort-menu');
      const label = trigger?.querySelector('span');
      if (label) {
        responsesSortDefaults.set(block, label.textContent.trim());
      }

      trigger?.addEventListener('click', (event) => {
        event.stopPropagation();
        responsesSortBlocks.forEach((other) => {
          if (other !== block) other.classList.remove('is-open');
        });
        block.classList.toggle('is-open');
      });

      menu?.querySelectorAll('button').forEach((option) => {
        option.addEventListener('click', (event) => {
          event.stopPropagation();
          const direction = option.getAttribute('data-sort');
          const column = block.getAttribute('data-column');
          responsesSortState = { column, direction };

          responsesSortBlocks.forEach((other) => {
            const otherLabel = other.querySelector('.responses-sort-btn span');
            if (!otherLabel) return;
            if (other === block) {
              other.classList.add('is-active');
              otherLabel.textContent = option.textContent.trim();
            } else {
              other.classList.remove('is-active');
              const defaultValue = responsesSortDefaults.get(other);
              otherLabel.textContent = defaultValue || 'Sort';
              other.classList.remove('is-open');
            }
          });

          block.classList.remove('is-open');
          applyResponsesState();
        });
      });
    });

    document.addEventListener('click', (event) => {
      if (!event.target.closest('.responses-sort')) {
        responsesSortBlocks.forEach((block) => block.classList.remove('is-open'));
      }
    });

    applyResponsesState();

    // Copy Logic
    window.copyToClipboard = function(text, type) {
        navigator.clipboard.writeText(text).then(() => {
            const containerId = type === 'link' ? 'copyLinkContainer' : 'copyCodeContainer';
            const container = document.getElementById(containerId);
            container.classList.add('copied');
            setTimeout(() => { container.classList.remove('copied'); }, 2000);
        });
    }

    // === INIT ===
    renderQuestionsList();
    if(questions.length > 0) loadQuestion(0);

</script>
</body>
</html>