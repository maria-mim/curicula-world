<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_portal.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>
  <style>
    /* Reset and base */
    * {
      box-sizing: border-box;
      margin: 0; padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    body {
      background: #eef4f8; /* same as user dashboard */
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    /* Navbar */
    .navbar {
      background-color: #a8e6a3; /* user dashboard navbar */
      padding: 15px 30px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .navbar h2 {
      color: #2e7d32; /* user dashboard text */
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
    .navbar a:hover {
      color: #1b5e20;
    }

    /* Main dashboard container */
    .dashboard {
      max-width: 900px;
      margin: 40px auto;
      background-color: #fff; /* user dashboard card */
      border-radius: 15px;
      box-shadow: 0 6px 20px rgba(0,0,0,0.15);
      padding: 50px 40px;
      text-align: center;
    }

    .dashboard h1 {
      color: #2e7d32;
      font-size: 32px;
      margin-bottom: 40px;
    }

    /* Dashboard sections container */
    .sections {
      display: flex;
      justify-content: center;
      gap: 30px;
      flex-wrap: wrap;
    }

    /* Individual section boxes */
    .section-box {
      background: #a5d6a7; /* user dashboard cards */
      flex: 1 1 200px;
      max-width: 250px;
      padding: 25px 20px;
      border-radius: 15px;
      box-shadow: 0 7px 20px rgba(46, 125, 50, 0.15);
      transition: background-color 0.3s ease, transform 0.2s ease;
      cursor: pointer;
      color: #2e7d32;
      font-weight: 700;
      font-size: 18px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      user-select: none;
      text-decoration: none;
    }

    .section-box:hover {
      background-color: #1b4d20; /* hover same as user dashboard */
      color: #fff;
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(27, 77, 32, 0.5);
      text-decoration: none;
    }
  </style>
</head>
<body>

  <nav class="navbar">
    <h2>Admin Dashboard</h2>
    <div>
      <a href="logout.php">Logout</a>
    </div>
  </nav>

  <div class="dashboard">
    <h1>Welcome, Admin!</h1>

    <div class="sections">
      <a href="admin_upload_article.php" class="section-box" title="Upload New Articles">
        Upload Articles
      </a>

      <a href="admin_manage_articles.php" class="section-box" title="Edit and Delete Articles">
        Manage Articles
      </a>
      <a href="admin_quiz_dashboard.php" class="section-box" title="Edit and Delete Quizzes">
        Manage Quizzes</a>
     
    </div>
  </div>

</body>
</html>
