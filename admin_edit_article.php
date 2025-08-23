<?php
session_start();
require 'connection_db.php';

$id = $_GET['id'] ?? 0;
$id = (int)$id;

if ($id <= 0) {
    header("Location: admin_manage_articles.php");
    exit;
}

// Fetch article
$stmt = $conn->prepare("SELECT title, image1, text1, image2, text2, video FROM articles WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($title, $image1, $text1, $image2, $text2, $video);
$stmt->fetch();
$stmt->close();

$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titleNew = $_POST['title'] ?? '';
    $text1New = $_POST['text1'] ?? '';
    $text2New = $_POST['text2'] ?? '';

    // Handle file uploads with deletion of old files if replaced
    function uploadAndReplace($fileInputName, $oldFile) {
        global $msg;
        if (isset($_FILES[$fileInputName]) && $_FILES[$fileInputName]['error'] == 0) {
            $allowed = ['jpg','jpeg','png','gif','mp4','webm','ogg'];
            $fileName = $_FILES[$fileInputName]['name'];
            $fileTmp = $_FILES[$fileInputName]['tmp_name'];
            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $newName = 'uploads/' . uniqid() . '.' . $ext;
                if (move_uploaded_file($fileTmp, $newName)) {
                    // Delete old file
                    if ($oldFile && file_exists($oldFile)) unlink($oldFile);
                    return $newName;
                } else {
                    $msg = "Failed to upload $fileInputName.";
                }
            } else {
                $msg = "Invalid file type for $fileInputName.";
            }
        }
        return $oldFile; // keep old if no new upload
    }

    $image1New = uploadAndReplace('image1', $image1);
    $image2New = uploadAndReplace('image2', $image2);
    $videoNew = uploadAndReplace('video', $video);

    // Update DB
    $stmt = $conn->prepare("UPDATE articles SET title=?, image1=?, text1=?, image2=?, text2=?, video=? WHERE id=?");
    $stmt->bind_param("ssssssi", $titleNew, $image1New, $text1New, $image2New, $text2New, $videoNew, $id);

    if ($stmt->execute()) {
        $msg = "Article updated successfully.";
        // Refresh current data
        $title = $titleNew;
        $text1 = $text1New;
        $text2 = $text2New;
        $image1 = $image1New;
        $image2 = $image2New;
        $video = $videoNew;
    } else {
        $msg = "Error updating article.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Edit Article</title>
<style>
  body {font-family: Arial,sans-serif; background:#f0f7f0; padding: 20px;}
  form {background:#fff; padding: 20px; border-radius: 8px; max-width: 600px; margin: auto; box-shadow: 0 5px 15px rgba(0,0,0,0.1);}
  input[type=text], textarea {width: 100%; padding: 10px; margin: 8px 0 20px 0; border-radius: 5px; border: 1px solid #ccc;}
  label {font-weight: bold;}
  input[type=file] {margin-bottom: 15px;}
  button {background: #4caf50; color: white; padding: 12px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px;}
  button:hover {background: #45a049;}
  img, video {max-width: 100%; margin-bottom: 15px; border-radius: 8px;}
  .msg {text-align:center; margin-bottom: 20px; font-weight: bold; color: green;}
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
    <h2>Edit Article</h2>
    <div>
      <a href="admin_dashboard.php">Dashboard</a>
    </div>
  </div>

<h2 style="text-align:center; color:#2e7d32;">Edit Article</h2>
<?php if(!empty($msg)) echo "<p class='msg'>$msg</p>"; ?>

<form method="POST" enctype="multipart/form-data">
    <label for="title">Title*</label>
    <input type="text" id="title" name="title" value="<?= htmlspecialchars($title) ?>" required>

    <label for="image1">First Image (optional)</label>
    <?php if ($image1 && file_exists($image1)): ?>
      <img src="<?= htmlspecialchars($image1) ?>" alt="Image 1 preview">
    <?php endif; ?>
    <input type="file" id="image1" name="image1" accept="image/*">

    <label for="text1">First Text Block</label>
    <textarea id="text1" name="text1" rows="5"><?= htmlspecialchars($text1) ?></textarea>

    <label for="image2">Second Image (optional)</label>
    <?php if ($image2 && file_exists($image2)): ?>
      <img src="<?= htmlspecialchars($image2) ?>" alt="Image 2 preview">
    <?php endif; ?>
    <input type="file" id="image2" name="image2" accept="image/*">

    <label for="text2">Second Text Block</label>
    <textarea id="text2" name="text2" rows="5"><?= htmlspecialchars($text2) ?></textarea>

    <label for="video">Video (optional)</label>
    <?php if ($video && file_exists($video)): ?>
      <video controls>
        <source src="<?= htmlspecialchars($video) ?>" type="video/mp4">
        Your browser does not support the video tag.
      </video>
    <?php endif; ?>
    <input type="file" id="video" name="video" accept="video/*">

    <button type="submit">Update Article</button>
</form>

</body>
</html>
