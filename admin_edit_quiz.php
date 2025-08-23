<?php
session_start();
require 'connection_db.php';

function fetchQuizzes() {
    global $conn;
    $quizzes = [];
    $sql = "SELECT id, article_id FROM quizzes ORDER BY id DESC";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $quizzes[] = $row;
    }
    return $quizzes;
}

function fetchQuestionsByQuiz($quiz_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM questions WHERE quiz_id = ?");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $questions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $questions;
}

function fetchOptionsByQuestion($question_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM options WHERE question_id = ?");
    $stmt->bind_param("i", $question_id);
    $stmt->execute();
    $options = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $options;
}

function deleteQuestionsNotInList($quiz_id, $idsToKeep) {
    global $conn;
    if(empty($idsToKeep)) {
        // Delete all questions for quiz
        $stmt = $conn->prepare("DELETE FROM questions WHERE quiz_id = ?");
        $stmt->bind_param("i", $quiz_id);
        $stmt->execute();
        $stmt->close();
        return;
    }
    $placeholders = implode(',', array_fill(0, count($idsToKeep), '?'));
    $types = str_repeat('i', count($idsToKeep));
    $sql = "DELETE FROM questions WHERE quiz_id = ? AND id NOT IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $params = array_merge([$quiz_id], $idsToKeep);
    $stmt->bind_param("i".$types, ...$params);
    $stmt->execute();
    $stmt->close();
}

function deleteOptionsNotInList($question_id, $idsToKeep) {
    global $conn;
    if(empty($idsToKeep)) {
        // Delete all options for question
        $stmt = $conn->prepare("DELETE FROM options WHERE question_id = ?");
        $stmt->bind_param("i", $question_id);
        $stmt->execute();
        $stmt->close();
        return;
    }
    $placeholders = implode(',', array_fill(0, count($idsToKeep), '?'));
    $types = str_repeat('i', count($idsToKeep));
    $sql = "DELETE FROM options WHERE question_id = ? AND id NOT IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $params = array_merge([$question_id], $idsToKeep);
    $stmt->bind_param("i".$types, ...$params);
    $stmt->execute();
    $stmt->close();
}

$msg = "";
$quiz_id = $_GET['quiz_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $quiz_id) {
    // Handle update quiz questions and options

    // We'll receive arrays for questions and options like:

    // questions: question_id[], question_text[], explanation[]
    // options: for each question: option_id[], option_text[], is_correct[] checkbox values

    $question_ids = $_POST['question_id'] ?? [];
    $question_texts = $_POST['question_text'] ?? [];
    $explanations = $_POST['explanation'] ?? [];

    // We'll track which question IDs to keep (not deleted)
    $question_ids_to_keep = [];

    // For each question submitted
    foreach ($question_ids as $index => $qid) {
        $qid = (int)$qid;
        $qtext = trim($question_texts[$index] ?? '');
        $exp = trim($explanations[$index] ?? '');

        if ($qid > 0) {
            // Existing question - update
            $stmt = $conn->prepare("UPDATE questions SET question_text = ?, explanation = ? WHERE id = ? AND quiz_id = ?");
            $stmt->bind_param("ssii", $qtext, $exp, $qid, $quiz_id);
            $stmt->execute();
            $stmt->close();
        } else {
            // New question - insert
            $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text, explanation) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $quiz_id, $qtext, $exp);
            $stmt->execute();
            $qid = $stmt->insert_id;
            $stmt->close();
        }
        $question_ids_to_keep[] = $qid;

        // Handle options for this question
        // option_ids[questionIndex][] etc.
        $option_ids = $_POST['option_id'][$index] ?? [];
        $option_texts = $_POST['option_text'][$index] ?? [];
        $option_is_corrects = $_POST['option_is_correct'][$index] ?? [];

        $option_ids_to_keep = [];

        foreach ($option_texts as $optIndex => $opt_text) {
            $opt_text = trim($opt_text);
            $opt_id = isset($option_ids[$optIndex]) ? (int)$option_ids[$optIndex] : 0;
            $is_correct = in_array($optIndex, $option_is_corrects) ? 1 : 0;

            if ($opt_id > 0) {
                // Update existing option
                $stmt = $conn->prepare("UPDATE options SET option_text = ?, is_correct = ? WHERE id = ? AND question_id = ?");
                $stmt->bind_param("siii", $opt_text, $is_correct, $opt_id, $qid);
                $stmt->execute();
                $stmt->close();
            } else {
                // Insert new option
                $stmt = $conn->prepare("INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)");
                $stmt->bind_param("isi", $qid, $opt_text, $is_correct);
                $stmt->execute();
                $opt_id = $stmt->insert_id;
                $stmt->close();
            }

            $option_ids_to_keep[] = $opt_id;
        }

        // Delete options that were removed
        deleteOptionsNotInList($qid, $option_ids_to_keep);
    }

    // Delete questions that were removed
    deleteQuestionsNotInList($quiz_id, $question_ids_to_keep);

    $msg = "Quiz questions and options updated successfully.";
}

// Fetch quizzes and questions/options for the selected quiz
$quizzes = fetchQuizzes();
$questions = [];
$options = []; // options indexed by question_id

if ($quiz_id) {
    $questions = fetchQuestionsByQuiz($quiz_id);
    foreach ($questions as $q) {
        $options[$q['id']] = fetchOptionsByQuestion($q['id']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Edit Quiz Questions and Options</title>
<style>
    body { font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px; }
    .container { max-width: 900px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);}
    select, input[type=text], textarea { width: 100%; padding: 8px; margin: 5px 0 15px; border-radius: 4px; border: 1px solid #ccc; }
    label { font-weight: bold; margin-top: 10px; display: block; }
    button { background: #4caf50; color: white; border: none; padding: 10px 16px; border-radius: 6px; cursor: pointer; }
    button:hover { background: #45a049; }
    .btn-danger { background: #e53935; }
    .btn-danger:hover { background: #b71c1c; }
    .msg { color: green; font-weight: bold; margin-bottom: 15px; }
    .question-block { background: #eef3f7; padding: 15px; border-radius: 6px; margin-bottom: 20px; }
    .option-block { background: #fff; padding: 8px; margin-bottom: 8px; border-radius: 4px; border: 1px solid #ccc; display: flex; align-items: center; }
    .option-block input[type=text] { flex-grow: 1; margin-right: 8px; }
    .option-block label { margin-right: 8px; }
    .remove-btn { background: #e53935; padding: 4px 8px; border-radius: 4px; border: none; color: white; cursor: pointer; }
    .remove-btn:hover { background: #b71c1c; }
    .add-btn { margin-top: 10px; }
    .inline-flex { display: flex; align-items: center; gap: 10px; }
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
<script>
function addQuestion() {
    const container = document.getElementById('questions-container');
    const qCount = container.children.length;
    const qId = 0; // new question, id=0

    const qDiv = document.createElement('div');
    qDiv.className = 'question-block';
    qDiv.dataset.questionIndex = qCount;

    qDiv.innerHTML = `
        <input type="hidden" name="question_id[]" value="0" />
        <label>Question Text:</label>
        <textarea name="question_text[]" rows="3" required></textarea>

        <label>Explanation:</label>
        <textarea name="explanation[]" rows="2"></textarea>

        <div class="options-container"></div>

        <button type="button" onclick="addOption(this)">Add Option</button>
        <button type="button" onclick="removeQuestion(this)" class="remove-btn" style="float:right;">Delete Question</button>
    `;
    container.appendChild(qDiv);
}

function addOption(btn) {
    const qDiv = btn.closest('.question-block');
    const optionsContainer = qDiv.querySelector('.options-container');
    const qIndex = Array.from(document.getElementById('questions-container').children).indexOf(qDiv);

    const optionCount = optionsContainer.children.length;
    const optionId = 0; // new option

    const optionDiv = document.createElement('div');
    optionDiv.className = 'option-block';

    optionDiv.innerHTML = `
        <input type="hidden" name="option_id[${qIndex}][]" value="0" />
        <input type="text" name="option_text[${qIndex}][]" placeholder="Option text" required />
        <label>
            Correct
            <input type="checkbox" name="option_is_correct[${qIndex}][]" value="${optionCount}" />
        </label>
        <button type="button" onclick="removeOption(this)" class="remove-btn">X</button>
    `;

    optionsContainer.appendChild(optionDiv);
}

function removeQuestion(btn) {
    if (confirm('Are you sure you want to delete this question?')) {
        btn.closest('.question-block').remove();
    }
}

function removeOption(btn) {
    btn.closest('.option-block').remove();
}

window.onload = function() {
    // Add "Add Option" buttons for existing questions
    document.querySelectorAll('.question-block').forEach(qDiv => {
        if (!qDiv.querySelector('.options-container').children.length) {
            // If no options, add one empty option by default
            const addBtn = qDiv.querySelector('button[onclick^="addOption"]');
            if (addBtn) addOption(addBtn);
        }
    });
};
</script>
</head>
<body>
<div class="navbar">
    <h2>Edit Quiz</h2>
    <div>
      <a href="admin_dashboard.php">Dashboard</a>
    </div>
  </div>
<div class="container">

<h1>Edit Quiz Questions and Options</h1>

<?php if ($msg): ?>
    <p class="msg"><?= htmlspecialchars($msg) ?></p>
<?php endif; ?>

<form method="GET" action="admin_edit_quiz.php">
    <label for="quiz_id">Select Quiz:</label>
    <select name="quiz_id" id="quiz_id" onchange="this.form.submit()">
        <option value="">-- Select a quiz --</option>
        <?php foreach ($quizzes as $qz): ?>
            <option value="<?= $qz['id'] ?>" <?= ($qz['id'] == $quiz_id) ? 'selected' : '' ?>>
                Quiz ID: <?= $qz['id'] ?> (Article ID: <?= htmlspecialchars($qz['article_id']) ?>)
            </option>
        <?php endforeach; ?>
    </select>
</form>

<?php if ($quiz_id): ?>
    <form method="POST" action="admin_edit_quiz.php?quiz_id=<?= $quiz_id ?>">
        <div id="questions-container">

            <?php foreach ($questions as $qIndex => $question): ?>
                <div class="question-block" data-question-index="<?= $qIndex ?>">
                    <input type="hidden" name="question_id[]" value="<?= $question['id'] ?>" />
                    <label>Question Text:</label>
                    <textarea name="question_text[]" rows="3" required><?= htmlspecialchars($question['question_text']) ?></textarea>

                    <label>Explanation:</label>
                    <textarea name="explanation[]" rows="2"><?= htmlspecialchars($question['explanation']) ?></textarea>

                    <div class="options-container">
                        <?php
                        $opts = $options[$question['id']] ?? [];
                        foreach ($opts as $optIndex => $opt): ?>
                            <div class="option-block">
                                <input type="hidden" name="option_id[<?= $qIndex ?>][]" value="<?= $opt['id'] ?>" />
                                <input type="text" name="option_text[<?= $qIndex ?>][]" value="<?= htmlspecialchars($opt['option_text']) ?>" placeholder="Option text" required />
                                <label>
                                    Correct
                                    <input type="checkbox" name="option_is_correct[<?= $qIndex ?>][]" value="<?= $optIndex ?>" <?= $opt['is_correct'] ? 'checked' : '' ?> />
                                </label>
                                <button type="button" onclick="removeOption(this)" class="remove-btn">X</button>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="button" onclick="addOption(this)">Add Option</button>
                    <button type="button" onclick="removeQuestion(this)" class="remove-btn" style="float:right;">Delete Question</button>
                </div>
            <?php endforeach; ?>

        </div>

        <button type="button" onclick="addQuestion()" class="add-btn">Add New Question</button>
        <br /><br />
        <button type="submit">Update Quiz Questions and Options</button>
    </form>
<?php else: ?>
    <p>Please select a quiz to edit questions.</p>
<?php endif; ?>

</div>
</body>
</html>
