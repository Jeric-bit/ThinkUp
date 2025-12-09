<?php
// quiz-taking.php
session_start();
require 'db_connect.php';

// 1. Security Check
if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$code = $_GET['code'] ?? null;

// =========================================================
// HANDLE AJAX SUBMISSION (Save to Database)
// =========================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['answers']) || !isset($input['quiz_id'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid data']);
        exit;
    }

    $quiz_id_sub = $input['quiz_id'];
    $user_answers = $input['answers']; // Array: [question_id => "User Answer", ...]
    
try {
        $pdo->beginTransaction();

        // 1. Fetch correct answers map
        $stmt = $pdo->prepare("
            SELECT q.id, q.type, qo.option_text, qo.is_correct 
            FROM questions q 
            LEFT JOIN question_options qo ON q.id = qo.question_id 
            WHERE q.quiz_id = ?
        ");
        $stmt->execute([$quiz_id_sub]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $questions_map = [];
        foreach ($rows as $row) {
            $qid = $row['id'];
            if (!isset($questions_map[$qid])) {
                $questions_map[$qid] = ['type' => $row['type'], 'correct' => []];
            }
            if ($row['type'] === 'identification' || $row['is_correct']) {
                $questions_map[$qid]['correct'][] = strtolower(trim($row['option_text']));
            }
        }

        // 2. Calculate Score & Prepare Details
        $score = 0;
        $total_questions = count($questions_map);
        $results_detail = [];

        // Prepare statement to save INDIVIDUAL RESPONSES
        $respInsert = $pdo->prepare("INSERT INTO quiz_responses (user_id, quiz_id, question_id, user_answer, is_correct) VALUES (?, ?, ?, ?, ?)");

        foreach ($user_answers as $idx => $ansObj) {
            $qid = $ansObj['id'];
            $uAns = isset($ansObj['val']) ? trim($ansObj['val']) : '';
            $uAnsLower = strtolower($uAns);
            $isCorrect = false;

            if (isset($questions_map[$qid])) {
                if (in_array($uAnsLower, $questions_map[$qid]['correct'])) {
                    $isCorrect = true;
                    $score++;
                }
            }
            
            // Save this specific answer to DB
            $respInsert->execute([$user_id, $quiz_id_sub, $qid, $uAns, $isCorrect ? 1 : 0]);

            $displayCorrect = isset($questions_map[$qid]) ? array_map('ucfirst', $questions_map[$qid]['correct']) : [];
            
            $results_detail[] = [
                'q_index' => $idx + 1,
                'user_ans' => $uAns,
                'correct' => $isCorrect,
                'correct_answers' => $displayCorrect
            ];
        }

        // 3. Save Overall Result
        $xp_earned = $score * 10; 
        $ins = $pdo->prepare("INSERT INTO quiz_results (user_id, quiz_id, score, total_questions, created_at) VALUES (?, ?, ?, ?, NOW())");
        $ins->execute([$user_id, $quiz_id_sub, $score, $total_questions]);

        // 4. Update XP
        $updateXP = $pdo->prepare("UPDATE users SET total_xp = total_xp + ? WHERE id = ?");
        $updateXP->execute([$xp_earned, $user_id]);

        $pdo->commit();

        echo json_encode([
            'success' => true,
            'score' => $score,
            'total' => $total_questions,
            'xp' => $xp_earned,
            'details' => $results_detail
        ]);
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
        exit;
    }
}

// =========================================================
// PAGE LOAD: FETCH QUIZ DATA
// =========================================================
if (!$code) {
    echo "Error: No quiz code provided."; exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE quiz_code = ?");
    $stmt->execute([$code]);
    $quiz = $stmt->fetch();
    if (!$quiz) { echo "Error: Quiz not found."; exit(); }

    $qStmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY order_index ASC");
    $qStmt->execute([$quiz['id']]);
    $db_questions = $qStmt->fetchAll(PDO::FETCH_ASSOC);

    $optStmt = $pdo->prepare("SELECT question_id, option_text FROM question_options WHERE question_id IN (SELECT id FROM questions WHERE quiz_id = ?)");
    $optStmt->execute([$quiz['id']]);
    $all_options = $optStmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);

    $js_questions = [];
    foreach ($db_questions as $q) {
        $opts = [];
        if (isset($all_options[$q['id']])) {
            foreach($all_options[$q['id']] as $o) {
                $opts[] = $o['option_text'];
            }
        }
        $js_questions[] = [
            'id' => $q['id'],
            'text' => $q['question_text'],
            'type' => $q['type'],
            'options' => $opts
        ];
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
<title>ThinkUp | <?php echo htmlspecialchars($quiz['title']); ?></title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet" />
<style>
  /* =========================================================
     1. GLOBAL STYLES
     ========================================================= */
  :root{
    --ink:#000000; --bg:#caf0f8; --dark:#023047;
    --side:#e4f7fb; --sideHover:#caf0f8; --sideActive:#caf0f8;
    --divider:#218ca6; --accent:#1b282c;
    --sideW:72px; --contentPad:28px;
    --bulbOff: #b0c4ce; --bulbMid: #ffe082; --bulbOn: #ffb703;
    --trackColor: #9dcbd8; --arrowColor: #023047;
  }
  body.dark {
    --bg: #023047; --ink: #E9F8FF;
    --side: #e4f7fb; --sideHover: #caf0f8; --sideActive: #caf0f8;
    --divider: #218ca6; --accent: #1b282c;
    --trackColor: #1d3c4c; --arrowColor: #77D1F6;
  }
  body, .sidebar, header, .nav-item, .btn { transition: all .25s cubic-bezier(0.25, 0.46, 0.45, 0.94); }
  *{box-sizing:border-box;margin:0}
  body{font-family:Poppins,system-ui,-apple-system,Segoe UI,Roboto,sans-serif;background:var(--bg);color:var(--ink)}
  body.nav-open{ --sideW:220px; }

  /* ───── Sidebar ───── */
  .sidebar{ position: fixed; inset: 0 auto 0 0; width: var(--sideW); background: var(--side); border-right: 1px solid var(--divider); z-index: 100; padding: 10px 1px; display: flex; flex-direction: column; gap: 0px; transition: width .25s cubic-bezier(0.4, 0, 0.2, 1); }
  .sidebar::before {content:""; position:absolute; left:0; top:0; width:4px; height:100%;}
  .nav-item{ position:relative; display:flex; align-items:center; gap:2px; height:56px; border-radius:0; padding:0; background:transparent; cursor:pointer; border:none; width:100%; justify-content:center; text-decoration: none; color: inherit; }
  .nav-item:hover{ background:var(--sideHover); }
  .nav-item img.icon{ width:70px; height:70px; object-fit:contain; }
  body.nav-open .nav-item{ justify-content:flex-start; gap:12px; }
  .nav-label{ font-family:'Poppins',sans-serif; white-space:nowrap; overflow:hidden; opacity:0; transform:translateX(-4px); transition:opacity .15s ease, transform .15s ease, width .2s ease; width:0;}
  body.nav-open .nav-label{ opacity:1; transform:translateX(0); width:auto; }
  .active-indicator{ position:absolute; right:0; width:6px; height:56px; background:var(--accent); top:0; transition: top .25s cubic-bezier(.22,.61,.36,1); pointer-events:none; z-index:2; }

  /* ───── Header ───── */
  header{ height:70px; background: rgba(228, 247, 251, 0.85); backdrop-filter: blur(12px); border-bottom: 1px solid rgba(33, 140, 166, 0.5); display:flex; align-items:center; justify-content:space-between; padding:0 20px; position: fixed; top: 0; right: 0; left: var(--sideW); width: auto; z-index: 50; transition: left .25s cubic-bezier(0.4, 0, 0.2, 1); }
  body.dark header { background: rgba(228, 247, 251, 0.9); border-color: #218ca6; }
  main{ margin-left: var(--sideW); padding: 100px var(--contentPad) 50px; transition: margin-left .25s ease; min-height: 100vh; }
  .mobile-toggle { display: none; margin-right: 12px; cursor: pointer; background: none; border: none; padding: 0; }
  .mobile-toggle img { width: 32px; height: 32px; }
  .brand{display:flex;align-items:center;gap:10px;font-weight:800}
  .brand img.logo{height:50px;display:block;}
  @media(min-width: 769px) { .brand img.logo { height: 130px; } } 
  .top-icons{ display:flex; align-items:center; justify-content:center; gap:20px; }
  .top-icons img{ width:40px; height:40px; object-fit:contain; }
  @media(min-width: 769px) { .top-icons img { width: 85px; height: 85px; } }
  #themeToggle { transition: transform 0.2s ease; object-fit: contain; }
  @media(min-width: 769px) { #themeToggle { width: 100px; height: 100px; } }
  #themeToggle:hover { transform: scale(1.1); }
  
  /* Account/Notif Styles */
  .account-menu{ position:absolute; right:0; top:calc(100% + 15px); width:240px; background:#e4f7fb; color:#000; border:2px solid #218ca6; border-radius:18px; padding:12px; opacity:0; pointer-events:none; transition: all .2s ease; z-index:99; transform: translateY(10px); box-shadow: 0 10px 40px rgba(0,0,0,0.15); }
  .account-menu.open{ opacity:1; pointer-events:auto; transform: translateY(0); }
  .account-item{ width:100%; display:flex; align-items:center; gap:10px; padding:14px 16px; border-radius:14px; font-weight:800; cursor:pointer; margin-top:8px; }
  .account-item.primary{ background:#0b2f42; color:#fff; border:none; }
  .account-item.card{ background:#fff; color:#0b2f42; border: 1px solid #218ca6; }
  .ai-icon { width: 20px; height: 20px; flex-shrink: 0; }
  .account-item.primary .ai-icon { fill: #fff; }
  .account-item.card .ai-icon { fill: #0b2f42; }
  .top-icons .icon-btn{ appearance:none; border:0; background:transparent; padding:0; margin:0; display:inline-flex; align-items:center; justify-content:center; cursor:pointer; }
  .notif-wrap, .account-wrap { position:relative; display:inline-flex; }
  .notif-popover{ position:absolute; right:-6px; top:115%; width:280px; background:var(--side); color:#000; border:2px solid var(--divider); border-radius:14px; box-shadow:0 8px 22px rgba(0,0,0,.10); padding:14px 16px; z-index:999; opacity:0; transform: translateY(-6px) scale(.98); pointer-events:none; transition:opacity .18s ease, transform .18s ease; }
  .notif-popover.open{ opacity:1; transform:translateY(0) scale(1); pointer-events:auto; }
  .notif-head{ font-weight:700; color:#000; margin-bottom:10px; }
  .notif-head::after{ content:""; display:block; height:1px; width:100%; background:var(--divider); margin-top:6px; opacity:.8; }
  .notif-empty{ text-align:center; padding:14px 0 6px; color:#2b3b44; font-weight:500; }
  .sidebar-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 90; opacity: 0; pointer-events: none; transition: opacity 0.3s ease; backdrop-filter: blur(2px); }
  body.nav-open .sidebar-overlay { opacity: 1; pointer-events: auto; }
  
  /* Mobile Responsive */
  @media (max-width: 768px) {
    :root { --sideW: 0px; } body.nav-open { --sideW: 0px; }
    .sidebar { width: 240px; transform: translateX(-100%); box-shadow: none; }
    body.nav-open .sidebar { transform: translateX(0); box-shadow: 4px 0 25px rgba(0,0,0,0.3); }
    .nav-label { opacity: 1 !important; width: auto !important; transform: none !important; }
    .nav-item { justify-content: flex-start; gap: 12px; }
    #menuBtn { display: none; }
    header { left: 0; width: 100%; padding: 0 16px; }
    .mobile-toggle { display: block; }
    main { margin-left: 0; padding: 90px 16px 40px; display: flex; flex-direction: column; gap: 40px; }
    .quiz-layout { flex-direction: column-reverse; align-items: center; }
    .progress-section { width: 100%; height: auto; flex-direction: row; margin-bottom: 20px; }
    .track-wrapper { width: 100%; height: 40px; }
    .prog-track { width: 100%; height: 20px; top: 10px; }
    .growing-arrow-container { height: 20px; top: 10px; left: 0; flex-direction: row; transform: none; width: 0; }
    .arrow-head { transform: rotate(-90deg); margin-left: -5px; }
    .arrow-body { width: auto; height: 100%; flex: 1; border-radius: 10px 0 0 10px; }
  }

  /* =========================================================
     2. QUIZ TAKING STYLES
     ========================================================= */
  .quiz-layout { display: flex; gap: 60px; justify-content: center; align-items: flex-start; max-width: 1200px; margin: 0 auto; width: 100%; }
  .progress-section { display: flex; flex-direction: column; align-items: center; width: 100px; height: 500px; position: relative; }
  .bulb-container { width: 90px; height: 90px; position: relative; z-index: 10; display: grid; place-items: center; flex-shrink: 0; }
  .bulb-svg { width: 70px; height: auto; fill: var(--bulbOff); transition: fill 0.6s ease, filter 0.6s ease; }
  .bulb-rays { position: absolute; width: 140%; height: 140%; pointer-events: none; opacity: 0; transition: opacity 0.6s ease; }
  .bulb-rays line { stroke: var(--bulbOn); stroke-width: 6; stroke-linecap: round; }
  .bulb-container.dim .bulb-svg { fill: var(--bulbMid); }
  .bulb-container.lit .bulb-svg { fill: var(--bulbOn); filter: drop-shadow(0 0 18px rgba(255, 183, 3, 0.8)); }
  .bulb-container.lit .bulb-rays { opacity: 1; animation: spin 12s linear infinite; }
  @keyframes spin { from{transform:rotate(0deg);} to{transform:rotate(360deg);} }
  .track-wrapper { position: relative; width: 60px; flex: 1; display: flex; justify-content: center; z-index: 1; }
  .prog-track { width: 24px; height: 100%; background: var(--trackColor); border-radius: 20px; position: absolute; top: 0; bottom: 0; z-index: 1; }
  .growing-arrow-container { position: absolute; bottom: 0; left: 50%; transform: translateX(-50%); width: 60px; height: 0%; display: flex; flex-direction: column; align-items: center; transition: height 0.5s; z-index: 2; }
  .arrow-head { width: 50px; height: 35px; background: var(--arrowColor); clip-path: polygon(50% 0%, 0% 100%, 100% 100%); margin-bottom: -1px; flex-shrink: 0; }
  .arrow-body { width: 24px; flex: 1; background: var(--arrowColor); border-radius: 0 0 14px 14px; }
  
  .q-card { flex: 1; width: 100%; max-width: 700px; background: #fff; border-radius: 30px; box-shadow: 0 8px 30px rgba(0,0,0,0.04); padding: 50px 60px; border: 1px solid #cce3ea; min-height: 500px; display: flex; flex-direction: column; }
  body.dark .q-card { background: var(--side); border-color: var(--divider); }
  .q-meta h2 { font-size: 1.6rem; font-weight: 800; margin-bottom: 12px; color: #000; }
  .q-meta p { font-size: 0.9rem; color: #666; font-style: italic; line-height: 1.4; margin-bottom: 30px; border-bottom: 2px solid #8ec9da; padding-bottom: 20px; }
  body.dark .q-meta h2 { color: #fff; } body.dark .q-meta p { color: #ccc; }
  .q-text { font-size: 1.25rem; font-weight: 700; margin-bottom: 40px; line-height: 1.5; color: var(--ink); }

  /* Input/Options Styles */
  .q-input { width: 100%; height: 60px; border-radius: 16px; border: 2px solid #666; padding: 0 24px; font-size: 1.1rem; outline: none; background: transparent; color: inherit; transition: border 0.2s; }
  .q-input:focus { border-color: #218ca6; box-shadow: 0 0 0 4px rgba(33,140,166,0.1); }
  body.dark .q-input { border-color: #77D1F6; color: #fff; }
  .mc-option { display: flex; align-items: center; gap: 16px; width: 100%; padding: 18px 20px; border: 2px solid #8ec9da; border-radius: 16px; margin-bottom: 14px; cursor: pointer; transition: all 0.2s; font-weight: 500; color: #555; background: transparent; }
  .mc-radio { width: 22px; height: 22px; border: 2px solid #666; border-radius: 50%; display: grid; place-items: center; flex-shrink: 0; }
  .mc-option:hover { background: #e4f7fb; border-color: #218ca6; }
  .mc-option.selected { background: #d1f1fa; border-color: #0b2f42; color: #0b2f42; font-weight: 700; }
  .mc-option.selected .mc-radio { border-color: #0b2f42; background: #0b2f42; }
  .mc-option.selected .mc-radio::after { content: ""; width: 8px; height: 8px; background: #fff; border-radius: 50%; }
  .tf-group { display: flex; flex-direction: column; gap: 16px; }
  .tf-btn { width: 100%; padding: 20px; border: 2px solid #8ec9da; border-radius: 16px; font-size: 1.2rem; font-weight: 700; cursor: pointer; background: transparent; color: #555; transition: 0.2s; text-align: center; }
  .tf-btn:hover { background: #e4f7fb; border-color: #218ca6; color: #0b2f42; }
  .tf-btn.selected { background: #d1f1fa; border-color: #0b2f42; color: #0b2f42; box-shadow: 0 4px 15px rgba(11,47,66,0.1); }

  /* Dark Mode Overrides */
  body.dark .mc-option { border-color: #218ca6; color: #ccc; }
  body.dark .mc-radio { border-color: #77D1F6; }
  body.dark .mc-option:hover { background: rgba(33,140,166,0.2); }
  body.dark .mc-option.selected { background: #0b2f42; border-color: #77D1F6; color: #fff; }
  body.dark .mc-option.selected .mc-radio { background: #77D1F6; border-color: #77D1F6; }
  body.dark .mc-option.selected .mc-radio::after { background: #023047; }
  body.dark .tf-btn { border-color: #218ca6; color: #ccc; }
  body.dark .tf-btn.selected { background: #0b2f42; border-color: #77D1F6; color: #fff; }

  .next-btn { align-self: flex-end; margin-top: auto; background: #023047; color: #fff; padding: 16px 48px; border-radius: 14px; font-weight: 800; border: none; cursor: pointer; box-shadow: 0 6px 16px rgba(2,48,71,0.2); transition: transform 0.2s; font-size: 1rem; }
  .next-btn:hover { transform: translateY(-3px); }
  body.dark .next-btn { background: #E9F8FF; color: #023047; }

  /* Nav Panel */
  .nav-panel { width: 260px; display: flex; flex-direction: column; gap: 20px; }
  .nav-box { background: #fff; border-radius: 24px; border: 1px solid #cce3ea; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); }
  body.dark .nav-box { background: var(--side); border-color: var(--divider); }
  .nav-title { font-weight: 800; font-size: 0.95rem; margin-bottom: 16px; text-align: center; color: var(--ink); }
  .nav-list { list-style: none; display: flex; flex-direction: column; gap: 12px; max-height: 350px; overflow-y: auto; }
  .nav-li { display: flex; align-items: center; gap: 12px; font-size: 0.9rem; font-weight: 600; color: #666; cursor: pointer; padding: 8px; border-radius: 10px; transition: background 0.2s; }
  .nav-li:hover { background: rgba(0,0,0,0.03); }
  .nav-li.active { color: #000; font-weight: 800; background: #e4f7fb; }
  body.dark .nav-li.active { color: #fff; background: rgba(255,255,255,0.05); }
  .status-icon { width: 24px; height: 24px; border-radius: 50%; display: grid; place-items: center; font-size: 12px; font-weight: 900; border: 2px solid #ccc; color: #ccc; }
  .nav-li.completed .status-icon { background: #000; border-color: #000; color: #fff; }
  .nav-li.completed .status-icon::before { content: "✔"; }
  .submit-btn { width: 100%; padding: 18px; border-radius: 99px; font-weight: 800; font-size: 1.1rem; border: none; background: #a9c7d1; color: #fff; cursor: not-allowed; text-align: center; }
  .submit-btn.ready { background: #023047; cursor: pointer; box-shadow: 0 4px 12px rgba(2,48,71,0.2); }
  body.dark .submit-btn.ready { background: #E9F8FF; color: #023047; }
  
  /* =========================================================
     3. RESULTS OVERLAY (MATCHING IMAGE_930A7C)
     ========================================================= */
  .complete-overlay { position: fixed; inset: 0; z-index: 2000; background: var(--bg); display: none; align-items: flex-start; justify-content: center; overflow-y: auto; padding: 40px 20px; margin-left: var(--sideW); }
  .complete-main-card { background: #fff; border-radius: 30px; padding: 40px 50px; width: 100%; max-width: 800px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); border: 1px solid #cce3ea; text-align: center; margin: auto 0; position: relative; }
  body.dark .complete-main-card { background: var(--side); border-color: var(--divider); }
  
  .comp-head h2 { font-size: 2rem; font-weight: 900; color: #023047; margin-bottom: 30px; }
  body.dark .comp-head h2 { color: #fff; }

  /* Grid Layout for Score */
  .score-summary-grid { display: grid; grid-template-columns: 1fr 1fr; border: 1px solid #cce3ea; border-radius: 20px; overflow: hidden; margin-bottom: 30px; }
  .score-col { padding: 30px; display: flex; flex-direction: column; align-items: center; justify-content: center; }
  .score-col:first-child { border-right: 1px solid #cce3ea; }
  .score-label { font-weight: 800; font-size: 1.1rem; color: #0b2f42; margin-bottom: 16px; }
  body.dark .score-label { color: #fff; }

  /* Circle Chart */
  .circle-chart { width: 130px; height: 130px; border-radius: 50%; display: grid; place-items: center; position: relative; background: #e4f7fb; }
  .circle-inner { width: 85px; height: 85px; background: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.2rem; color: #0b2f42; z-index: 2; }
  body.dark .circle-inner { background: var(--side); color: #fff; }

  /* XP Section */
  .xp-val { font-size: 3rem; font-weight: 900; color: #000; display: flex; align-items: center; gap: 10px; margin: 6px 0; }
  .xp-coin { width: 32px; height: 32px; background: #f4b400; border-radius: 50%; color: #fff; font-size: 14px; display: grid; place-items: center; font-weight: bold; border: 2px solid #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
  body.dark .xp-val { color: #fff; }
  .total-score-text { font-size: 1.1rem; font-weight: 700; color: #555; }
  body.dark .total-score-text { color: #ccc; }

  /* Divider */
  .results-divider { position: relative; border-top: 1px solid #cce3ea; margin: 40px 0 30px; text-align: center; height: 0; }
  .results-label { background: #fff; border: 1px solid #cce3ea; border-radius: 99px; padding: 8px 30px; font-weight: 800; color: #0b2f42; position: relative; top: -22px; display: inline-block; font-size: 0.95rem; }
  body.dark .results-label { background: var(--side); color: #fff; border-color: #218ca6; }

  /* Result Items */
  .res-list { text-align: left; display: flex; flex-direction: column; gap: 30px; margin-bottom: 40px; }
  .res-q-head { font-size: 0.85rem; font-weight: 800; text-transform: uppercase; color: #000; margin-bottom: 10px; }
  body.dark .res-q-head { color: #fff; }
  
  .status-bar { display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-radius: 12px; font-weight: 800; margin-top: 10px; font-size: 0.95rem; }
  
  /* Match the image style: Clean/Light bars */
  .status-bar.correct { background: #f4fcfc; color: #000; }
  .status-bar.incorrect { background: #fdfdfd; color: #000; }
  body.dark .status-bar { background: #03283A; color: #fff; }

  .sb-left { display: flex; align-items: center; gap: 10px; }
  .sb-icon { font-size: 1.1rem; }
  
  .res-correct-ans { margin-top: 8px; font-size: 0.9rem; color: #666; padding-left: 4px; }
  .res-correct-ans em { font-style: italic; color: #000; font-weight: 600; }
  body.dark .res-correct-ans { color: #aaa; }
  body.dark .res-correct-ans em { color: #fff; }

  /* Buttons */
  .res-actions { display: flex; gap: 20px; justify-content: center; }
  .btn-act { padding: 16px 0; width: 100%; border-radius: 16px; font-weight: 800; cursor: pointer; font-size: 0.95rem; transition: transform 0.2s; }
  .btn-act:hover { transform: translateY(-2px); }
  .btn-solid { background: #023047; color: #fff; border: none; }
  .btn-outline { background: transparent; color: #023047; border: 2px solid #cce3ea; }
  body.dark .btn-solid { background: #E9F8FF; color: #023047; }
  body.dark .btn-outline { color: #fff; border-color: #fff; }
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
    <a href="folders.php" class="nav-item active">
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
          <button id="acctMy" class="account-item primary" role="menuitem" onclick="window.location.href='home.php#view-profile'">
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

  <main id="takingQuizView">
    <div class="quiz-layout">
        <section class="progress-section">
            <div class="bulb-container" id="bulbContainer">
                <svg class="bulb-rays" viewBox="0 0 100 100"><line x1="50" y1="10" x2="50" y2="0" /><line x1="50" y1="90" x2="50" y2="100" /><line x1="10" y1="50" x2="0" y2="50" /><line x1="90" y1="50" x2="100" y2="50" /></svg>
                <svg class="bulb-svg" viewBox="0 0 24 24"><path d="M9 21c0 .55.45 1 1 1h4c.55 0 1-.45 1-1v-1H9v1zm3-19C8.14 2 5 5.14 5 9c0 2.38 1.19 4.47 3 5.74V17c0 .55.45 1 1 1h6c.55 0 1-.45 1-1v-2.26c1.81-1.27 3-3.36 3-5.74 0-3.86-3.14-7-7-7z"/></svg>
            </div>
            <div class="track-wrapper">
                <div class="prog-track"></div>
                <div class="growing-arrow-container" id="progArrow"><div class="arrow-head"></div><div class="arrow-body"></div></div>
            </div>
        </section>

        <section class="q-card" id="questionCard">
            <div class="q-meta">
                <h2><?php echo htmlspecialchars($quiz['title']); ?></h2>
                <p>Read each statement carefully. Identify what is being asked.</p>
            </div>
            <div id="qContent"></div>
            <button class="next-btn" id="nextBtn">Next</button>
        </section>

        <aside class="nav-panel">
            <div class="nav-box">
                <div class="nav-title">Questions</div>
                <ul class="nav-list" id="navList"></ul>
            </div>
            <button class="submit-btn" id="submitBtn">Submit</button>
        </aside>
    </div>
  </main>

  <div class="complete-overlay" id="completeView">
    <div class="complete-main-card">
      <div class="comp-head"><h2>Quiz complete!</h2></div>
      
      <div class="score-summary-grid">
        <div class="score-col">
            <div class="score-label">Quiz progress</div>
            <div class="circle-chart" id="circleChart">
                <div class="circle-inner" id="circlePercent">0%</div>
            </div>
        </div>
        <div class="score-col">
            <div class="score-label">You Earned XP!</div>
            <div class="xp-val">+ <span id="xpDisplay">0</span> <div class="xp-coin">XP</div></div>
            <div class="total-score-text">Total score: <span id="scoreText">0 / 0</span></div>
        </div>
      </div>

      <div class="results-divider">
        <span class="results-label">Results</span>
      </div>

      <div class="res-list" id="resultsList"></div>
      
      <div class="res-actions">
        <button class="btn-act btn-outline" onclick="window.location.href='progress.php'">See Progress</button>
        <button class="btn-act btn-solid" onclick="window.location.href='home.php'">Back to Home</button>
      </div>
    </div>
  </div>

  <script>
    // 1. DATA FROM PHP
    const questions = <?php echo json_encode($js_questions); ?>;
    const quizId = <?php echo $quiz['id']; ?>;
    
    // 2. STATE
    let currentQIndex = 0;
    let answers = new Array(questions.length).fill(null);

    // 3. UI ELEMENTS
    const qContent = document.getElementById('qContent');
    const navList = document.getElementById('navList');
    const nextBtn = document.getElementById('nextBtn');
    const progArrow = document.getElementById('progArrow');
    const bulbContainer = document.getElementById('bulbContainer');
    const submitBtn = document.getElementById('submitBtn');
    const takingView = document.getElementById('takingQuizView');
    const completeView = document.getElementById('completeView');

    // === UI LOGIC ===
    const menuBtn = document.getElementById('menuBtn');
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const accountBtn = document.getElementById('accountBtn');
    const accountMenu = document.getElementById('accountMenu');
    const notifBtn = document.getElementById('notifBtn');
    const notifPanel = document.getElementById('notifPanel');
    const indicator = document.querySelector('.active-indicator');

    document.addEventListener('DOMContentLoaded', () => {
        const activeItem = document.querySelector('.nav-item.active');
        if (activeItem && indicator) indicator.style.top = activeItem.offsetTop + 'px';
    });

    function toggleSidebar() { document.body.classList.toggle('nav-open'); }
    menuBtn.addEventListener('click', toggleSidebar);
    mobileMenuBtn.addEventListener('click', (e) => { e.stopPropagation(); toggleSidebar(); });
    sidebarOverlay.addEventListener('click', () => { document.body.classList.remove('nav-open'); });

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

    const themeBtn = document.getElementById('themeToggle');
    const ICON_MOON = "https://images.unsplash.com/vector-1762021885090-5c631ddfa33e?auto=format&fit=crop&q=80&w=880";
    const ICON_SUN  = "https://images.unsplash.com/vector-1762027380971-51b98cfee95d?auto=format&fit=crop&q=80&w=880";
    function updateThemeIcon() { themeBtn.src = document.body.classList.contains('dark') ? ICON_SUN : ICON_MOON; }
    if (localStorage.getItem('thinkup_theme') === 'dark') document.body.classList.add('dark');
    updateThemeIcon();
    themeBtn.addEventListener('click', () => {
        document.body.classList.toggle('dark');
        localStorage.setItem('thinkup_theme', document.body.classList.contains('dark') ? 'dark' : 'light');
        updateThemeIcon();
    });

    // === QUIZ LOGIC ===

    function initQuiz() {
      if(questions.length === 0) return;
      renderNav();
      loadQuestion(0);
    }

    function renderNav() {
      navList.innerHTML = '';
      questions.forEach((q, idx) => {
        const hasAnswer = answers[idx] !== null && answers[idx] !== '';
        const li = document.createElement('li');
        li.className = `nav-li ${idx === currentQIndex ? 'active' : ''} ${hasAnswer ? 'completed' : ''}`;
        li.innerHTML = `<div class="status-icon"></div> Question ${idx + 1}`;
        li.onclick = () => loadQuestion(idx);
        navList.appendChild(li);
      });
      updateSubmitBtn();
    }

    function loadQuestion(index) {
      currentQIndex = index;
      const q = questions[index];
      const currentAnswer = answers[index] || '';
      
      let html = `<div class="q-text">${q.text}</div>`;

      // RENDER BASED ON TYPE
      if (q.type === 'multiple_choice') {
         if(q.options && q.options.length > 0) {
            q.options.forEach(opt => {
                const isSelected = currentAnswer === opt ? 'selected' : '';
                html += `
                <div class="mc-option ${isSelected}" onclick="selectMC(this, '${opt}')">
                   <div class="mc-radio"></div>
                   <span>${opt}</span>
                </div>`;
            });
         }
      } 
      else if (q.type === 'true_false') {
          const isTrue = currentAnswer === 'True' ? 'selected' : '';
          const isFalse = currentAnswer === 'False' ? 'selected' : '';
          html += `
          <div class="tf-group">
            <button class="tf-btn ${isTrue}" onclick="selectTF(this, 'True')">True</button>
            <button class="tf-btn ${isFalse}" onclick="selectTF(this, 'False')">False</button>
          </div>
          `;
      } 
      else {
          html += `<input class="q-input" type="text" placeholder="Type your answer here" value="${currentAnswer}" oninput="saveAnswer(this.value)">`;
      }

      qContent.innerHTML = html;
      renderNav();

      const totalQ = questions.length;
      const percent = index / (totalQ - 1 || 1); 
      const minPct = 15; const maxPct = 100;
      const currentPct = minPct + (percent * (maxPct - minPct));
      if(window.innerWidth > 768) {
         progArrow.style.height = `${currentPct}%`; progArrow.style.width = '60px';
      } else {
         progArrow.style.width = `${currentPct}%`; progArrow.style.height = '100%';
      }

      bulbContainer.classList.remove('dim', 'lit');
      if (index >= 2 && index < totalQ - 1) bulbContainer.classList.add('dim');
      else if (index === totalQ - 1) bulbContainer.classList.add('lit');

      nextBtn.textContent = (index === totalQ - 1) ? 'Review' : 'Next';
    }

    window.selectMC = function(el, val) {
        const siblings = document.querySelectorAll('.mc-option');
        siblings.forEach(s => s.classList.remove('selected'));
        el.classList.add('selected');
        saveAnswer(val);
    }

    window.selectTF = function(el, val) {
        const siblings = document.querySelectorAll('.tf-btn');
        siblings.forEach(s => s.classList.remove('selected'));
        el.classList.add('selected');
        saveAnswer(val);
    }

    window.saveAnswer = function(val) {
      answers[currentQIndex] = val.trim() === '' ? null : val;
      const li = navList.children[currentQIndex];
      if(answers[currentQIndex]) li.classList.add('completed');
      else li.classList.remove('completed');
      updateSubmitBtn();
    };

    nextBtn.addEventListener('click', () => {
      if (currentQIndex < questions.length - 1) loadQuestion(currentQIndex + 1);
    });

    function updateSubmitBtn() {
      const allAnswered = answers.every(a => a !== null && a !== '');
      if (allAnswered) {
        submitBtn.classList.add('ready');
        submitBtn.onclick = submitToDB;
      } else {
        submitBtn.classList.remove('ready');
        submitBtn.onclick = null;
      }
    }

    function submitToDB() {
        submitBtn.textContent = 'Submitting...';
        const payload = {
            quiz_id: quizId,
            answers: questions.map((q, idx) => ({ id: q.id, val: answers[idx] }))
        };

        fetch('quiz-taking.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                showResults(data);
            } else {
                alert("Error: " + data.message);
                submitBtn.textContent = 'Submit';
            }
        })
        .catch(err => {
            console.error(err);
            alert("Connection error occurred.");
            submitBtn.textContent = 'Submit';
        });
    }

    function showResults(data) {
        const percent = Math.round((data.score / data.total) * 100);
        document.getElementById('circlePercent').textContent = percent + '%';
        document.getElementById('circleChart').style.background = `conic-gradient(#0b2f42 ${percent}%, #e4f7fb ${percent}% 100%)`;
        
        document.getElementById('xpDisplay').textContent = data.xp;
        document.getElementById('scoreText').textContent = `${data.score} / ${data.total}`;
        
        let html = '';
        data.details.forEach(d => {
            const statusClass = d.correct ? 'correct' : 'incorrect';
            const icon = d.correct ? '✔ Correct' : '✖ Incorrect';
            const points = d.correct ? '+10' : '+0';
            
            html += `
            <div class="res-item">
               <div class="res-q-head">QUESTION ${d.q_index}</div>
               <div class="status-bar ${statusClass}">
                  <div class="sb-left"><span class="sb-icon">${icon}</span></div>
                  <div>${points}</div>
               </div>
               ${!d.correct ? `<div class="res-correct-ans">Correct Answer: <em>${d.correct_answers.join(' / ')}</em></div>` : ''}
            </div>`;
        });
        
        document.getElementById('resultsList').innerHTML = html;
        takingView.style.display = 'none';
        completeView.style.display = 'flex';
        window.scrollTo(0,0);
    }

    window.addEventListener('resize', () => loadQuestion(currentQIndex));
    initQuiz();
  </script>
</body>
</html>