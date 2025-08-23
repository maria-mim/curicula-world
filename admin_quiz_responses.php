<?php
session_start();
require 'connection_db.php';

// Check admin auth
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_portal.php");
    exit;
}

// Fetch all users who took quizzes
$users = [];
$userStmt = $conn->prepare("SELECT DISTINCT u.id, u.first_name, u.last_name FROM users u JOIN responses r ON u.id = r.user_id ORDER BY u.first_name, u.last_name");
$userStmt->execute();
$userRes = $userStmt->get_result();
while ($row = $userRes->fetch_assoc()) {
    $users[] = $row;
}
$userStmt->close();

// Fetch all quizzes
$quizzes = [];
$quizStmt = $conn->prepare("SELECT id, article_id FROM quizzes ORDER BY article_id");
$quizStmt->execute();
$quizRes = $quizStmt->get_result();
while ($row = $quizRes->fetch_assoc()) {
    $quizzes[] = $row;
}
$quizStmt->close();

// Initialize variables
$selectedUserId = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
$selectedQuizId = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
$responses = [];
$questions = [];
$correctAnswers = [];

if ($selectedUserId > 0 && $selectedQuizId > 0) {
    // Fetch questions for quiz
    $stmt = $conn->prepare("SELECT id, question_text, explanation FROM questions WHERE quiz_id = ? ORDER BY id");
    $stmt->bind_param("i", $selectedQuizId);
    $stmt->execute();
    $qRes = $stmt->get_result();
    while ($row = $qRes->fetch_assoc()) {
        $questions[$row['id']] = $row;
    }
    $stmt->close();

    if (!empty($questions)) {
        // Fetch correct options
        $inQuestionIds = implode(',', array_keys($questions));
        $correctOptResult = $conn->query("SELECT question_id, id AS correct_option_id FROM options WHERE question_id IN ($inQuestionIds) AND is_correct = 1");
        while ($row = $correctOptResult->fetch_assoc()) {
            $correctAnswers[$row['question_id']] = $row['correct_option_id'];
        }

        // Fetch user responses for the quiz
        $stmt = $conn->prepare("SELECT question_id, selected_option_id FROM responses WHERE user_id = ? AND quiz_id = ?");
        $stmt->bind_param("ii", $selectedUserId, $selectedQuizId);
        $stmt->execute();
        $respRes = $stmt->get_result();
        while ($row = $respRes->fetch_assoc()) {
            $responses[$row['question_id']] = $row['selected_option_id'];
        }
        $stmt->close();

        // Fetch options for questions to show option text
        $options = [];
        $optResult = $conn->query("SELECT id, question_id, option_text FROM options WHERE question_id IN ($inQuestionIds) ORDER BY id");
        while ($row = $optResult->fetch_assoc()) {
            $options[$row['question_id']][$row['id']] = $row['option_text'];
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin: View User Quiz Responses</title>
<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f7faf7;
    padding: 30px;
    max-width: 900px;
    margin: auto;
    color: #2e7d32;
  }
  h1 {
    font-weight: 700;
    margin-bottom: 15px;
  }
  select, button {
    padding: 8px 12px;
    font-size: 1rem;
    margin-right: 12px;
    border-radius: 8px;
    border: 2px solid #4caf50;
  }
  button {
    background-color: #2e7d32;
    color: white;
    font-weight: 700;
    cursor: pointer;
  }
  button:hover {
    background-color: #1b4d20;
  }
  .question-block {
    margin-top: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid #d7e7d7;
  }
  .question-text {
    font-weight: 600;
    margin-bottom: 10px;
    font-size: 1.1rem;
  }
  .option {
    padding: 8px 15px;
    border-radius: 8px;
    margin-bottom: 8px;
    user-select: none;
  }
  .correct {
    background-color: #a5d6a7;
    border: 2px solid #4caf50;
    font-weight: 700;
  }
  .user-selected {
    background-color: #ffccbc;
    border: 2px solid #d32f2f;
  }
  .explanation {
    margin-top: 8px;
    font-size: 0.9rem;
    color: #4b6b44;
    background-color: #e9f5e9;
    padding: 10px 15px;
    border-left: 4px solid #4caf50;
    border-radius: 6px;
  }
  .no-responses {
    margin-top: 20px;
    font-weight: 600;
    color: #d32f2f;
  }
  .navbar {
      background-color: #a8e6a3;
      padding: 15px 30px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .navbar h2 {
      color: #2e7d32;
      font-size: 22px;
      font-weight: 600;
      margin: 0;
    }

    .navbar a {
      color: #2e7d32;
      text-decoration: none;
      font-weight: 500;
      margin-left: 20px;
      transition: color 0.3s;
    }

    .navbar a:hover {
      color: #1b5e20;
    }
</style>
</head>
<body>
<div class="navbar">
    <h2>Quiz Responses</h2>
    <div>
      <a href="admin_dashboard.php">Dashboard</a>
    </div>
  </div>
<h1>View User Quiz Responses</h1>

<form method="get">
  <label for="user_id">Select User:</label>
  <select name="user_id" id="user_id" required>
    <option value="">-- Choose User --</option>
    <?php foreach ($users as $user): ?>
      <option value="<?= $user['id'] ?>" <?= ($user['id'] == $selectedUserId) ? 'selected' : '' ?>>
        <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
      </option>
    <?php endforeach; ?>
  </select>

  <label for="quiz_id">Select Quiz:</label>
  <select name="quiz_id" id="quiz_id" required>
    <option value="">-- Choose Quiz --</option>
    <?php foreach ($quizzes as $quiz): ?>
      <option value="<?= $quiz['id'] ?>" <?= ($quiz['id'] == $selectedQuizId) ? 'selected' : '' ?>>
        <?= htmlspecialchars($quiz['article_id']) ?>
      </option>
    <?php endforeach; ?>
  </select>

  <button type="submit">View Responses</button>
</form>

<?php if ($selectedUserId > 0 && $selectedQuizId > 0): ?>
  <?php if (empty($responses)): ?>
    <div class="no-responses">No responses found for this user on this quiz.</div>
  <?php else: ?>
    <?php $score = 0; ?>
    <?php foreach ($questions as $qid => $question): ?>
      <div class="question-block">
        <div class="question-text"><?= htmlspecialchars($question['question_text']) ?></div>
        <?php foreach ($options[$qid] as $optId => $optText): 
          $isCorrect = isset($correctAnswers[$qid]) && $correctAnswers[$qid] == $optId;
          $isUserSelected = isset($responses[$qid]) && $responses[$qid] == $optId;
          $class = "";
          if ($isCorrect) $class = "correct";
          if ($isUserSelected && !$isCorrect) $class = "user-selected";
        ?>
          <div class="option <?= $class ?>">
            <?= htmlspecialchars($optText) ?>
            <?php if ($isCorrect): ?>
              <strong> (Correct Answer)</strong>
            <?php elseif ($isUserSelected): ?>
              <strong> (User's Answer)</strong>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>

        <div class="explanation"><strong>Explanation:</strong> <?= nl2br(htmlspecialchars($question['explanation'])) ?></div>
      </div>
      <?php
        // Calculate score
        if (isset($correctAnswers[$qid]) && isset($responses[$qid]) && $responses[$qid] == $correctAnswers[$qid]) {
            $score++;
        }
      ?>
    <?php endforeach; ?>

    <h3>User Score: <?= $score ?> / <?= count($questions) ?></h3>
  <?php endif; ?>
<?php endif; ?>

</body>
</html>
