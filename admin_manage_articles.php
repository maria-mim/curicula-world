<?php
session_start();
require 'connection_db.php';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Delete files from server
    $stmt = $conn->prepare("SELECT image1, image2, video FROM articles WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($img1, $img2, $vid);
    if ($stmt->fetch()) {
        foreach ([$img1, $img2, $vid] as $file) {
            if ($file && file_exists($file)) unlink($file);
        }
    }
    $stmt->close();

    // Delete article
    $stmt = $conn->prepare("DELETE FROM articles WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_manage_articles.php");
    exit;
}

// Fetch all articles
$result = $conn->query("SELECT id, title, created_at FROM articles ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Manage Articles</title>
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

  h2 { color: #2e7d32; text-align: center; margin-bottom: 20px; }

  table {
    border-collapse: collapse;
    width: 100%;
    max-width: 900px;
    margin: auto;
    background: #fff;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    border-radius: 10px;
    overflow: hidden;
  }

  th, td {
    padding: 12px 18px;
    border-bottom: 1px solid #ddd;
    text-align: left;
  }

  th {
    background: linear-gradient(135deg, #66bb6a, #43a047);
    color: #fff;
    font-weight: 600;
  }

  tr:hover { background-color: #dcedc8; }

  a.button {
    background: linear-gradient(135deg, #a5d6a7, #81c784);
    color: #1b5e20;
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    margin-right: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
  }

  a.button:hover {
    background: linear-gradient(135deg, #66bb6a, #43a047);
    color: #fff;
  }

  .delete-btn {
    background: linear-gradient(135deg, #ef9a9a, #e53935);
    color: #fff;
  }

  .delete-btn:hover {
    background: linear-gradient(135deg, #e53935, #b71c1c);
  }
</style>
</head>
<body>

<div class="navbar">
    <h2>Manage Articles</h2>
    <div>
      <a href="admin_dashboard.php">Dashboard</a>
    </div>
</div>

<h2>Manage Articles</h2>

<table>
    <thead>
        <tr>
            <th>Title</th>
            <th>Uploaded At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= $row['created_at'] ?></td>
            <td>
                <a class="button" href="admin_edit_article.php?id=<?= $row['id'] ?>">Edit</a>
                <a class="button delete-btn" href="admin_manage_articles.php?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this article?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
