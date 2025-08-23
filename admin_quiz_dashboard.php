<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php"); // Your login page filename
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Quiz Dashboard</title>
  <style>
    /* General Reset and fonts */
    body, html {
      margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f0f7f5;
      color: #2e7d32;
    }
    .navbar {
      background: #4caf50;
      color: white;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .navbar h1 {
      margin: 0;
      font-weight: 700;
      font-size: 26px;
      letter-spacing: 1.2px;
    }
    .navbar .nav-links a {
      color: white;
      margin-left: 25px;
      text-decoration: none;
      font-weight: 600;
      transition: color 0.3s;
      font-size: 16px;
    }
    .navbar .nav-links a:hover {
      color: #a5d6a7;
    }
    .dashboard-container {
      max-width: 1000px;
      margin: 40px auto;
      background: white;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.08);
      padding: 30px 40px;
      text-align: center;
    }
    h2 {
      margin-bottom: 30px;
      font-size: 28px;
      font-weight: 700;
      letter-spacing: 0.8px;
      color: #388e3c;
    }
    .cards {
      display: flex;
      justify-content: center;
      gap: 40px;
      flex-wrap: wrap;
    }
    .card {
      background: #e8f5e9;
      border-radius: 16px;
      width: 280px;
      padding: 40px 25px;
      box-shadow: 0 10px 20px rgba(56, 142, 60, 0.2);
      transition: box-shadow 0.3s ease, transform 0.3s ease;
      cursor: pointer;
      user-select: none;
    }
    .card:hover {
      box-shadow: 0 16px 32px rgba(56, 142, 60, 0.35);
      transform: translateY(-5px);
    }
    .card h3 {
      margin-top: 0;
      margin-bottom: 15px;
      font-size: 22px;
      font-weight: 700;
      color: #2e7d32;
    }
    .card p {
      font-size: 16px;
      color: #4a7c4a;
      line-height: 1.4;
    }
    .logout-btn {
      background: #c62828;
      color: white;
      padding: 10px 22px;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      font-size: 14px;
      transition: background-color 0.3s ease;
      user-select: none;
    }
    .logout-btn:hover {
      background: #8e1b1b;
    }
  </style>
</head>
<body>

  <div class="navbar">
    <h1>Quiz Dashboard</h1>
    <div class="nav-links">
      <span>Welcome, <?= htmlspecialchars($_SESSION['admin_name']) ?></span>
      <a href="admin_dashboard.php">Admin Dashboard</a>
    </div>
  </div>

  <div class="dashboard-container">
    <h2>Manage Quizzes & Responses</h2>
    <div class="cards">
      <div class="card" onclick="location.href='admin_quiz_responses.php'">
        <h3>View Quiz Responses</h3>
        <p>See detailed answers and results from all quiz takers.</p>
      </div>
      <div class="card" onclick="location.href='admin_edit_quiz.php'">
        <h3>Edit & Manage Quizzes</h3>
        <p>Create, update, or delete quizzes to keep your content fresh.</p>
      </div>
    </div>
  </div>

  <script>
    function logout() {
      if (confirm("Are you sure you want to logout?")) {
        window.location.href = "admin_logout.php"; // Make sure this file destroys session & redirects to login
      }
    }
  </script>

</body>
</html>
