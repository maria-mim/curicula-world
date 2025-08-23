<?php
session_start();
require 'connection_db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit;
}
$userId = $_SESSION['user_id'];

$quizId = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
if ($quizId <= 0) {
    die("Invalid quiz ID.");
}

// Fetch quiz and article info
$stmt = $conn->prepare("
    SELECT q.id, a.title AS article_title
    FROM quizzes q
    JOIN articles a ON q.article_id = a.id
    WHERE q.id = ?
");
$stmt->bind_param("i", $quizId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("Quiz not found.");
}
$quiz = $result->fetch_assoc();
$stmt->close();

// Fetch questions and options
$stmt = $conn->prepare("
    SELECT q.id AS question_id, q.question_text, q.explanation,
           o.id AS option_id, o.option_text
    FROM questions q
    JOIN options o ON q.id = o.question_id
    WHERE q.quiz_id = ?
    ORDER BY q.id, o.id
");
$stmt->bind_param("i", $quizId);
$stmt->execute();
$res = $stmt->get_result();

$questions = [];
while ($row = $res->fetch_assoc()) {
    $qid = $row['question_id'];
    if (!isset($questions[$qid])) {
        $questions[$qid] = [
            'question_text' => $row['question_text'],
            'explanation' => $row['explanation'],
            'options' => []
        ];
    }
    $questions[$qid]['options'][] = [
        'option_id' => $row['option_id'],
        'option_text' => $row['option_text']
    ];
}
$stmt->close();

if (empty($questions)) {
    die("No questions found for this quiz.");
}

// Check if user already submitted responses
$stmt = $conn->prepare("SELECT question_id, selected_option_id FROM responses WHERE user_id = ? AND quiz_id = ?");
$stmt->bind_param("ii", $userId, $quizId);
$stmt->execute();
$res = $stmt->get_result();
$userResponses = [];
while ($row = $res->fetch_assoc()) {
    $userResponses[$row['question_id']] = $row['selected_option_id'];
}
$stmt->close();

$alreadySubmitted = !empty($userResponses);
$errors = [];
$showResult = false;
$score = 0;
$totalQuestions = count($questions);
$correctAnswers = [];

// Get correct options
$correctOptResult = $conn->query("
    SELECT question_id, id AS correct_option_id
    FROM options
    WHERE question_id IN (SELECT id FROM questions WHERE quiz_id = $quizId)
    AND is_correct = 1
");
while ($row = $correctOptResult->fetch_assoc()) {
    $correctAnswers[$row['question_id']] = $row['correct_option_id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$alreadySubmitted) {
    $answers = $_POST['answers'] ?? [];
    if (count($answers) !== $totalQuestions) {
        $errors[] = "Please answer all questions.";
    } else {
        $conn->begin_transaction();
        try {
            $insertStmt = $conn->prepare("
                INSERT INTO responses (user_id, quiz_id, question_id, selected_option_id, responded_at)
                VALUES (?, ?, ?, ?, NOW())
            ");
            foreach ($questions as $questionId => $q) {
                $selectedOptionId = (int)$answers[$questionId];
                $insertStmt->bind_param("iiii", $userId, $quizId, $questionId, $selectedOptionId);
                $insertStmt->execute();
                if (isset($correctAnswers[$questionId]) && $selectedOptionId === (int)$correctAnswers[$questionId]) {
                    $score++;
                }
            }
            $conn->commit();
            $showResult = true;
            $alreadySubmitted = true;
            $userResponses = $answers;
        } catch (Exception $e) {
            $conn->rollback();
            $errors[] = "Error saving your responses. Please try again.";
        }
    }
} else if ($alreadySubmitted) {
    foreach ($userResponses as $qid => $selOption) {
        if (isset($correctAnswers[$qid]) && $selOption === (int)$correctAnswers[$qid]) {
            $score++;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Quiz: <?= htmlspecialchars($quiz['article_title']) ?></title>
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #E6F2FF; /* dashboard background */
    color: #1B3B2B;
    margin: 0;
    padding: 20px;
}
.navbar {
    background-color: #A2D9A0;
    padding: 15px 30px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 10px;
}
.navbar h2 {
    color: #1B3B2B;
    font-weight: 600;
}
.navbar a {
    color: #1B3B2B;
    text-decoration: none;
    font-weight: 500;
    margin-left: 15px;
}
.navbar a:hover {
    color: #034D57;
}

h1 {
    text-align: center;
    margin: 20px 0;
    color: #056D77;
}
form {
    background-color: #FFFDF5;
    padding: 25px;
    border-radius: 14px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.05);
}
.question-block {
    margin-bottom: 30px;
    padding-bottom: 15px;
    border-bottom: 1px solid #D7E7D7;
}
.question-text {
    font-weight: 600;
    margin-bottom: 10px;
    font-size: 1.1rem;
}
label.option-label {
    display: block;
    padding: 12px 18px;
    border-radius: 10px;
    border: 2px solid transparent;
    cursor: pointer;
    margin-bottom: 10px;
    transition: border-color 0.3s, background-color 0.2s;
}
input[type="radio"] { display: none; }
input[type="radio"]:checked + label.option-label {
    background-color: #A2D9A0;
    border-color: #1B3B2B;
    font-weight: 700;
}
.explanation {
    margin-top: 10px;
    font-size: 0.95rem;
    color: #1B3B2B;
    background-color: #D7F0D7;
    padding: 12px 18px;
    border-left: 4px solid #2e7d32;
    border-radius: 8px;
}
.correct-answer { color: #056D77; font-weight: 700; }
.incorrect-answer { color: #D32F2F; font-weight: 700; }
.error { color: #D32F2F; font-weight: 600; margin-bottom: 20px; }
button.submit-btn {
    background-color: #2e7d32;
    border: none;
    padding: 12px 25px;
    color: white;
    border-radius: 12px;
    font-weight: 700;
    cursor: pointer;
    transition: background-color 0.3s;
}
button.submit-btn:hover { background-color: #1b4d20; }
a.back-link {
    display: inline-block;
    margin-top: 20px;
    color: #056D77;
    font-weight: 600;
    text-decoration: none;
}
a.back-link:hover { text-decoration: underline; }
form[style] { opacity: 0.85; pointer-events: none; }
</style>
</head>
<body>

<div class="navbar">
    <h2>Quiz</h2>
    <div><a href="user_dashboard.php">Dashboard</a></div>
</div>

<h1><?= htmlspecialchars($quiz['article_title']) ?></h1>

<?php if ($errors): ?>
    <div class="error"><?= implode('<br>', $errors) ?></div>
<?php endif; ?>

<?php if ($alreadySubmitted && !$showResult): ?>
    <p>You have already taken this quiz. Review your answers below.</p>
<?php endif; ?>

<form method="post" action="take_quiz.php?quiz_id=<?= $quizId ?>" <?= $alreadySubmitted ? 'style="opacity:0.85;pointer-events:none;"' : '' ?>>
<?php foreach ($questions as $qid => $q): ?>
    <div class="question-block">
        <div class="question-text"><?= htmlspecialchars($q['question_text']) ?></div>
        <?php foreach ($q['options'] as $option):
            $checked = (isset($userResponses[$qid]) && $userResponses[$qid] == $option['option_id']);
            $isCorrect = (isset($correctAnswers[$qid]) && $correctAnswers[$qid] == $option['option_id']);
        ?>
        <input type="radio" id="q<?= $qid ?>o<?= $option['option_id'] ?>" name="answers[<?= $qid ?>]" value="<?= $option['option_id'] ?>" <?= $checked ? 'checked' : '' ?> />
        <label class="option-label" for="q<?= $qid ?>o<?= $option['option_id'] ?>">
            <?= htmlspecialchars($option['option_text']) ?>
            <?php if ($alreadySubmitted):
                if ($isCorrect) echo ' <span class="correct-answer">(Correct)</span>';
                elseif ($checked) echo ' <span class="incorrect-answer">(Your answer)</span>';
            endif; ?>
        </label>
        <?php endforeach; ?>

        <?php if ($alreadySubmitted): ?>
        <div class="explanation"><strong>Explanation:</strong> <?= nl2br(htmlspecialchars($q['explanation'])) ?></div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<?php if (!$alreadySubmitted): ?>
<button type="submit" class="submit-btn">Submit Quiz</button>
<?php else: ?>
<div><strong>Your score: <?= $score ?> / <?= $totalQuestions ?></strong></div>
<?php endif; ?>
</form>

<a href="user_quiz_dashboard.php" class="back-link">← Back to Dashboard</a>

</body>
</html>
