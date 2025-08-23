<?php
session_start();
require 'connection_db.php'; // $conn is your MySQLi connection

$msg = '';

// Function to upload files (images/videos)
function uploadFile($fileInputName) {
    if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] == 0) {
        $allowed = ['jpg','jpeg','png','gif','mp4','webm','ogg'];
        $fileName = $_FILES[$fileInputName]['name'];
        $fileTmp = $_FILES[$fileInputName]['tmp_name'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $newName = uniqid() . '.' . $ext;
            $dest = 'uploads/' . $newName;
            if (move_uploaded_file($fileTmp, $dest)) {
                return $dest;
            }
        }
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'] ?? '';
    $text1 = $_POST['text1'] ?? '';
    $text2 = $_POST['text2'] ?? '';

    $image1 = uploadFile('image1');
    $image2 = uploadFile('image2');
    $video = uploadFile('video');

    // Insert article
    $stmt = $conn->prepare("INSERT INTO articles (title, image1, text1, image2, text2, video) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $title, $image1, $text1, $image2, $text2, $video);
    if ($stmt->execute()) {
        $article_id = $stmt->insert_id;

        // Insert quiz only if questions exist
        if (!empty($_POST['questions'])) {
            // Create quiz for this article
            $quizStmt = $conn->prepare("INSERT INTO quizzes (article_id) VALUES (?)");
            $quizStmt->bind_param("i", $article_id);
            $quizStmt->execute();
            $quiz_id = $quizStmt->insert_id;
            $quizStmt->close();

            // Loop through questions
            foreach ($_POST['questions'] as $qIndex => $qData) {
                $questionText = $qData['question'] ?? '';
                $explanation = $qData['explanation'] ?? '';

                $questionStmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text, explanation) VALUES (?, ?, ?)");
                $questionStmt->bind_param("iss", $quiz_id, $questionText, $explanation);
                $questionStmt->execute();
                $question_id = $questionStmt->insert_id;
                $questionStmt->close();

                // Insert options for this question
                foreach ($qData['options'] as $optIndex => $optText) {
                    $isCorrect = (isset($qData['correct_answer']) && $qData['correct_answer'] == $optIndex) ? 1 : 0;
                    $optStmt = $conn->prepare("INSERT INTO options (question_id, option_text, is_correct) VALUES (?, ?, ?)");
                    $optStmt->bind_param("isi", $question_id, $optText, $isCorrect);
                    $optStmt->execute();
                    $optStmt->close();
                }
            }
        }

        $msg = "Article and quiz uploaded successfully!";
    } else {
        $msg = "Error uploading article.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin Upload Article & Quiz</title>
<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #eef4f8;
    padding: 20px;
    color: #333;
  }

  .navbar {
    background-color: #a8e6a3;
    padding: 15px 30px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
  }

  .navbar h2 {
    color: #2e7d32;
    font-size: 22px;
    font-weight: 600;
  }

  .navbar a {
    color: #2e7d32;
    text-decoration: none;
    font-weight: 500;
    margin-left: 20px;
    transition: color 0.3s;
  }

  .navbar a:hover { color: #1b5e20; }

  form {
    background: #fff;
    padding: 30px;
    border-radius: 15px;
    max-width: 900px;
    margin: auto;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
  }

  input[type=text], textarea, input[type=file] {
    width: 100%;
    padding: 12px;
    margin: 10px 0 20px 0;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 16px;
    outline: none;
    transition: border-color 0.3s;
  }

  input[type=text]:focus, textarea:focus {
    border-color: #4caf50;
  }

  button, .btn-remove, .add-option-btn {
    padding: 10px 15px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    border: none;
    transition: 0.3s ease;
  }

  button[type=submit] {
    background: linear-gradient(135deg, #66bb6a, #43a047);
    color: #fff;
    font-size: 16px;
    margin-top: 20px;
  }

  button[type=submit]:hover {
    background: #2e7d32;
  }

  .btn-remove {
    background: #d32f2f;
    color: #fff;
    float: right;
    margin-bottom: 10px;
  }

  .btn-remove:hover {
    background: #b71c1c;
  }

  .add-option-btn {
    background: #1976d2;
    color: #fff;
    margin-top: 10px;
    font-size: 14px;
  }

  .add-option-btn:hover {
    background: #0d47a1;
  }

  .msg {
    text-align:center;
    font-weight: 700;
    margin-bottom: 20px;
    color: #2e7d32;
  }

  .question-block {
    background: #eaf6ea;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 12px;
    position: relative;
  }

  .option-input { margin-left: 20px; margin-bottom: 8px; }
  .option-input input[type="text"] { width: 80%; }

  h2 { color: #2e7d32; margin-bottom: 15px; }
</style>
</head>
<body>

<div class="navbar">
    <h2>Upload Article & Quiz</h2>
    <div>
      <a href="admin_dashboard.php">Dashboard</a>
    </div>
</div>

<h2 style="text-align:center;">Upload Article</h2>
<?php if(!empty($msg)) echo "<p class='msg'>$msg</p>"; ?>

<form method="POST" enctype="multipart/form-data" id="articleForm">
    <label for="title">Title*</label>
    <input type="text" id="title" name="title" required>

    <label for="image1">First Image (optional)</label>
    <input type="file" id="image1" name="image1" accept="image/*">

    <label for="text1">First Text Block</label>
    <textarea id="text1" name="text1" rows="5" placeholder="Enter first paragraph or description"></textarea>

    <label for="image2">Second Image (optional)</label>
    <input type="file" id="image2" name="image2" accept="image/*">

    <label for="text2">Second Text Block</label>
    <textarea id="text2" name="text2" rows="5" placeholder="Enter second paragraph or description"></textarea>

    <label for="video">Video (optional)</label>
    <input type="file" id="video" name="video" accept="video/*">

    <hr style="margin: 30px 0;">

    <h2>Add MCQ Quiz (optional)</h2>
    <div id="questions-container"></div>
    <button type="button" class="add-option-btn" onclick="addQuestion()">+ Add Question</button>

    <button type="submit">Upload Article & Quiz</button>
</form>

<script>
let questionCount = 0;

function addQuestion() {
    questionCount++;
    const container = document.getElementById('questions-container');

    const questionDiv = document.createElement('div');
    questionDiv.className = 'question-block';
    questionDiv.setAttribute('data-qid', questionCount);

    questionDiv.innerHTML = `
      <button type="button" class="btn-remove" onclick="removeQuestion(${questionCount})">Remove Question</button>
      <label>Question*</label>
      <textarea name="questions[${questionCount}][question]" rows="3" required></textarea>

      <div class="options-container" id="options-${questionCount}">
        <label>Options* (Add at least 2)</label>
        <div class="option-input">
          <input type="radio" name="questions[${questionCount}][correct_answer]" value="0" required>
          <input type="text" name="questions[${questionCount}][options][]" placeholder="Option 1" required>
        </div>
        <div class="option-input">
          <input type="radio" name="questions[${questionCount}][correct_answer]" value="1" required>
          <input type="text" name="questions[${questionCount}][options][]" placeholder="Option 2" required>
        </div>
      </div>
      <button type="button" class="add-option-btn" onclick="addOption(${questionCount})">+ Add Option</button>

      <label>Explanation (optional)</label>
      <textarea name="questions[${questionCount}][explanation]" rows="3" placeholder="Explanation for the correct answer"></textarea>
    `;

    container.appendChild(questionDiv);
}

function removeQuestion(qid) {
    const container = document.getElementById('questions-container');
    const questionDiv = container.querySelector(`div[data-qid="${qid}"]`);
    if (questionDiv) container.removeChild(questionDiv);
}

function addOption(qid) {
    const optionsContainer = document.getElementById('options-' + qid);
    const optionCount = optionsContainer.querySelectorAll('.option-input').length;

    const optionDiv = document.createElement('div');
    optionDiv.className = 'option-input';
    optionDiv.innerHTML = `
        <input type="radio" name="questions[${qid}][correct_answer]" value="${optionCount}" required>
        <input type="text" name="questions[${qid}][options][]" placeholder="Option ${optionCount + 1}" required>
    `;
    optionsContainer.appendChild(optionDiv);
}
</script>

</body>
</html>
