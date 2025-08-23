<?php
session_start();
require 'connection_db.php'; // mysqli connection

// Check connection
if (!isset($conn)) {
    die("Database connection not found!");
}

// Get search query if any
$search = $_GET['search'] ?? '';

if ($search) {
    $stmt = $conn->prepare("SELECT id, title FROM articles WHERE title LIKE CONCAT('%', ?, '%') ORDER BY id DESC");
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("SELECT id, title FROM articles ORDER BY id DESC");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>View Articles</title>
<style>
  /* Base */
  body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f0faf0; /* soft pastel green */
      margin: 0;
      padding: 0;
      color: #1b3b2b;
  }

  /* Navbar */
  .navbar {
      background-color: #a8e6a3;
      padding: 18px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
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
      border-radius: 10px;
      transition: background-color 0.3s ease, color 0.3s ease;
  }
  .navbar a:hover {
      background-color: #88c28d;
      color: #fff;
  }

  /* Container */
  .container {
      max-width: 750px;
      margin: 30px auto;
      padding: 0 20px;
  }
  h1 {
      text-align: center;
      color: #2e7d32;
      margin-bottom: 25px;
      font-size: 2rem;
  }

  /* Search form */
  form {
      display: flex;
      justify-content: center;
      margin-bottom: 25px;
      gap: 10px;
      flex-wrap: wrap;
  }
  input[type="text"] {
      width: 60%;
      min-width: 200px;
      padding: 12px 14px;
      border-radius: 12px;
      border: 2px solid #c8e6c9;
      font-size: 16px;
      transition: border-color 0.3s, box-shadow 0.3s;
  }
  input[type="text"]:focus {
      border-color: #66bb6a;
      box-shadow: 0 0 8px #66bb6a55;
      outline: none;
      background-color: #f0fff0;
  }
  button {
      padding: 12px 18px;
      background-color: #66bb6a;
      border: none;
      border-radius: 12px;
      color: white;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s, transform 0.2s;
  }
  button:hover {
      background-color: #4caf50;
      transform: translateY(-2px);
  }

  /* Articles list */
  ul {
      list-style: none;
      padding-left: 0;
      margin: 0;
  }
  li {
      background-color: #e8f5e9;
      padding: 16px 20px;
      margin-bottom: 12px;
      border-radius: 14px;
      box-shadow: 0 4px 12px rgba(46, 125, 50, 0.1);
      transition: transform 0.3s, box-shadow 0.3s;
  }
  li:hover {
      background-color: #d0f0c0;
      transform: translateY(-3px);
      box-shadow: 0 6px 18px rgba(46,125,50,0.2);
  }
  a {
      text-decoration: none;
      color: #2e7d32;
      font-size: 1.1rem;
      font-weight: 600;
  }
  a:hover {
      text-decoration: underline;
      color: #1b5e20;
  }

  /* Responsive */
  @media (max-width: 600px) {
      input[type="text"] {
          width: 100%;
      }
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

<div class="container">
    <h1>Articles</h1>

    <form method="GET" action="user_articles.php">
        <input type="text" name="search" placeholder="Search articles by title..." value="<?= htmlspecialchars($search) ?>">
        <button type="submit">Search</button>
    </form>

    <ul>
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<li><a href="view_article.php?id=' . $row['id'] . '">' . htmlspecialchars($row['title']) . '</a></li>';
            }
        } else {
            echo '<li>No articles found.</li>';
        }
        ?>
    </ul>
</div>

</body>
</html>
