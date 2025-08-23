<?php
session_start();
require 'connection_db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: user_login.php");
    exit;
}
$userId = $_SESSION['user_id'];

// Fetch all quizzes with article titles
$quizzes = [];
$stmt = $conn->prepare("
    SELECT q.id AS quiz_id, q.article_id, a.title AS article_title, q.created_at
    FROM quizzes q
    JOIN articles a ON q.article_id = a.id
    ORDER BY q.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $quizzes[$row['quiz_id']] = $row;
}
$stmt->close();

// Fetch user’s responses and calculate scores
$correctOptions = [];
$quizIds = array_keys($quizzes);
if (count($quizIds) > 0) {
    $in = implode(',', array_map('intval', $quizIds));
    $questionsRes = $conn->query("
        SELECT q.id AS question_id, q.quiz_id, o.id AS correct_option_id
        FROM questions q
        JOIN options o ON q.id = o.question_id AND o.is_correct = 1
        WHERE q.quiz_id IN ($in)
    ");
    while ($row = $questionsRes->fetch_assoc()) {
        $correctOptions[$row['quiz_id']][$row['question_id']] = $row['correct_option_id'];
    }

    $responsesRes = $conn->query("
        SELECT r.quiz_id, r.question_id, r.selected_option_id
        FROM responses r
        WHERE r.user_id = $userId AND r.quiz_id IN ($in)
    ");

    $userResponses = [];
    while ($resp = $responsesRes->fetch_assoc()) {
        $qid = $resp['quiz_id'];
        $questionId = $resp['question_id'];
        $selOpt = $resp['selected_option_id'];

        if (!isset($userResponses[$qid])) {
            $userResponses[$qid] = ['answered' => 0, 'correct' => 0];
        }
        $userResponses[$qid]['answered']++;
        if (isset($correctOptions[$qid][$questionId]) && $selOpt == $correctOptions[$qid][$questionId]) {
            $userResponses[$qid]['correct']++;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>User Quiz Dashboard</title>
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #E6F2FF; /* dashboard theme background */
    padding: 30px;
    color: #1B3B2B;
}
h1 {
    color: #2e7d32;
    margin-bottom: 20px;
}
table {
    width: 100%;
    max-width: 900px;
    border-collapse: collapse;
    margin-top: 20px;
    background: #FFFDF5; /* pastel cream for learner-friendly */
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    border-radius: 12px;
    overflow: hidden;
}
th, td {
    padding: 14px 18px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}
th {
    background-color: #a8e6a3; /* navbar green shade */
    color: #1B3B2B;
    font-weight: 600;
}
tr:hover {
    background-color: #e0f7e9; /* soft highlight */
}
a.button {
    background-color: #66bb6a; /* pastel green button */
    color: white;
    padding: 8px 14px;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: background-color 0.3s;
}
a.button:hover {
    background-color: #4caf50;
}
.status-taken {
    color: #2e7d32;
    font-weight: 700;
}
.status-not-taken {
    color: #d32f2f;
    font-weight: 700;
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
    <h2>Quiz Dashboard</h2>
    <div>
      <a href="user_dashboard.php">User Dashboard</a>
    </div>
</div>

<h1>Your Quizzes</h1>

<?php if (empty($quizzes)): ?>
    <p>No quizzes are currently available.</p>
<?php else: ?>
<table>
  <thead>
    <tr>
      <th>Article Title</th>
      <th>Quiz Created On</th>
      <th>Status</th>
      <th>Score</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($quizzes as $quiz): 
      $quizId = $quiz['quiz_id'];
      $taken = isset($userResponses[$quizId]);
      $score = $taken ? ($userResponses[$quizId]['correct'] . ' / ' . $userResponses[$quizId]['answered']) : '-';
      $statusText = $taken ? "Taken" : "Not Taken";
      $statusClass = $taken ? "status-taken" : "status-not-taken";
      $actionLabel = $taken ? "Review" : "Take Quiz";
    ?>
    <tr>
      <td><?= htmlspecialchars($quiz['article_title']) ?></td>
      <td><?= date("M d, Y", strtotime($quiz['created_at'])) ?></td>
      <td class="<?= $statusClass ?>"><?= $statusText ?></td>
      <td><?= $score ?></td>
      <td>
        <a class="button" href="take_quiz.php?quiz_id=<?= $quizId ?>"><?= $actionLabel ?></a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>

<p><a href="user_dashboard.php">Back to User Dashboard</a></p>

</body>
</html>
