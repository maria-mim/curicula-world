<?php
session_start();
require 'connection_db.php';

// Check if user logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: user_portal.php");
    exit;
}

$userName = $_SESSION['user_name'] ?? 'User';

// Fetch articles
$articles = [];
$articleStmt = $conn->prepare("SELECT id, title FROM articles ORDER BY id DESC");
$articleStmt->execute();
$res = $articleStmt->get_result();
while ($row = $res->fetch_assoc()) {
    $articles[] = $row;
}
$articleStmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>User Dashboard</title>
<style>
* { box-sizing: border-box; margin:0; padding:0; }

body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #E6F2FF;
  color: #333333;
  line-height: 1.5;
  margin: 0;
}

/* Header with integrated logout */
header {
  background-color: #A2D9A0;
  color: #1B3B2B;
  padding: 20px 40px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 1.8rem;
  font-weight: 700;
  letter-spacing: 1px;
  border-bottom-left-radius: 15px;
  border-bottom-right-radius: 15px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

header .logout {
  background-color: #929db7ff; /* same as header */
  color: #1B3B2B;
  text-decoration: none;
  font-size: 1rem;
  font-weight: 600;
  padding: 14px 18px;
  border-radius: 12px;
  margin-top: 8px;
  border: 2px solid #88C28D;
  transition: background-color 0.3s ease, transform 0.2s ease;
}
header .logout:hover {
  background-color: #88C28D;
  transform: translateY(-2px);
}

/* Main content */
main {
  max-width: 1100px;
  margin: 30px auto 60px auto;
  padding: 0 20px;
  display: flex;
  gap: 30px;
  flex-wrap: wrap;
  justify-content: center;
}

section {
  background-color: #FFFDF5;
  flex: 1 1 400px;
  border-radius: 14px;
  padding: 25px 30px;
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
  display: flex;
  flex-direction: column;
}

section h2 {
  margin-top: 0;
  color: #1B3B2B;
  border-bottom: 3px solid #A2D9A0;
  padding-bottom: 8px;
  margin-bottom: 20px;
  font-weight: 700;
  font-size: 1.6rem;
}
.fun-section-box {
  background: linear-gradient(45deg, #a8dadc, #b5e48c);
  color: #003049;
  font-family: 'Comic Sans MS', Arial, sans-serif;
  font-size: 26px;
  font-weight: bold;
  text-decoration: none;
  border-radius: 15px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  letter-spacing: 1.2px;
  text-shadow: 1px 1px 3px rgba(255, 255, 255, 0.6);
  transition: transform 0.2s, box-shadow 0.2s, background 0.3s;
}

.fun-section-box:hover {
  transform: scale(1.05);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
  background: linear-gradient(45deg, #b5e48c, #a8dadc);
}



ul {
  list-style: none;
  padding: 0;
  margin: 0;
  flex-grow: 1;
}

li {
  background: #E0F7FA;
  padding: 14px 18px;
  margin-bottom: 12px;
  border-radius: 10px;
  box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
  transition: box-shadow 0.3s ease, transform 0.3s ease;
}

li:hover {
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
  transform: translateY(-2px);
}

a.article-link, a.quiz-link {
  color: #056D77;
  font-weight: 600;
  font-size: 1.1rem;
  text-decoration: none;
}

a.article-link:hover, a.quiz-link:hover {
  text-decoration: underline;
  color: #034D57;
}

.quiz-desc {
  font-size: 0.9rem;
  color: #555;
  margin-top: 6px;
}

@media (max-width: 768px) {
  main {
    flex-direction: column;
    max-width: 95%;
    margin: 20px auto;
  }
  header {
    flex-direction: column;
    gap: 10px;
    text-align: center;
  }
}
</style>
</head>
<body>

<header>
  <span>Welcome, <?= htmlspecialchars($userName) ?></span>
  <a class="logout" href="logout.php">Logout</a>
</header>

<main>
  <section>
    <h2>Articles</h2>
    <?php if (count($articles) === 0): ?>
      <p>No articles available.</p>
    <?php else: ?>
      <ul>
        <?php foreach ($articles as $article): ?>
          <li>
            <a class="article-link" href="view_article.php?id=<?= $article['id'] ?>">
              <?= htmlspecialchars($article['title']) ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </section>

  <section>
    <h2>Quizzes</h2>
    <a class="quiz-link" href="user_quiz_dashboard.php">Go to Quiz Dashboard</a>
     </a>
  </section>
<section>
 <a href="game_practice.php" class="section-box fun-section-box" title="Fungames">
    সংখ্যা ধরো
</a> <br>
<a href="shape_game.php" class="section-box fun-section-box" title="Fungames">
    আকার খেলা
</a> <br>
<a href="close_numbergame.php" class="section-box fun-section-box" title="Fungames">
    চিন্তা করো, পছন্দ করো!
</a> 
</section>

</main>

</body>
</html>
