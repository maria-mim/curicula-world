<?php
session_start();
require 'connection_db.php'; // mysqli connection

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: user_articles.php");
    exit;
}

// Prepare and fetch article data
$stmt = $conn->prepare("SELECT title, image1, text1, image2, text2, video FROM articles WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows == 0) {
    // Article not found
    $stmt->close();
    echo "<p>Article not found.</p>";
    exit;
}

$stmt->bind_result($title, $image1, $text1, $image2, $text2, $video);
$stmt->fetch();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title><?= htmlspecialchars($title) ?></title>
<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    max-width: 850px;
    margin: 40px auto;
    background: #f0fff0; /* gentle pastel green background */
    padding: 25px 20px;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    color: #2e7d32; /* primary text color */
  }

  h1 {
    text-align: center;
    color: #2e7d32;
    font-size: 2rem;
    margin-bottom: 30px;
    font-weight: 700;
  }

  p {
    font-size: 1.1rem;
    line-height: 1.7;
    color: #334d33; /* softer dark green for readability */
    margin: 20px 0;
    white-space: pre-wrap;
  }

  img, video {
    display: block;
    max-width: 100%;
    margin: 20px auto;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
  }

  video {
    max-height: 400px;
  }

  a.back {
    display: inline-block;
    margin-bottom: 25px;
    color: #388e3c;
    text-decoration: none;
    font-weight: bold;
    font-size: 1rem;
    transition: color 0.3s;
  }
  a.back:hover {
    color: #1b5e20;
    text-decoration: underline;
  }

  /* Navbar with soft learner-friendly theme */
  .navbar {
    background-color: #a8e6a3;
    padding: 15px 30px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 8px;
    margin-bottom: 30px;
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
    padding: 6px 12px;
    border-radius: 8px;
    background: #e0f2e0; /* subtle button background for links */
    transition: background 0.3s, color 0.3s;
  }

  .navbar a:hover {
    color: #1b5e20;
    background: #c8e6c9;
  }
</style>
</head>
<body>

<div class="navbar">
    <h2>Articles</h2>
    <div>
      <a href="user_dashboard.php">Dashboard</a>
    </div>
</div>

<a href="user_articles.php" class="back">&larr; Back to Articles</a>

<h1><?= htmlspecialchars($title) ?></h1>

<?php if ($image1): ?>
  <img src="<?= htmlspecialchars($image1) ?>" alt="Image 1">
<?php endif; ?>

<?php if ($text1): ?>
  <p><?= nl2br(htmlspecialchars($text1)) ?></p>
<?php endif; ?>

<?php if ($image2): ?>
  <img src="<?= htmlspecialchars($image2) ?>" alt="Image 2">
<?php endif; ?>

<?php if ($text2): ?>
  <p><?= nl2br(htmlspecialchars($text2)) ?></p>
<?php endif; ?>

<?php if ($video): ?>
  <video controls>
    <source src="<?= htmlspecialchars($video) ?>" type="video/mp4">
    Your browser does not support the video tag.
  </video>
<?php endif; ?>

</body>
</html>
