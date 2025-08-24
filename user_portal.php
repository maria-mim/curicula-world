<?php
session_start();
include "connection_db.php";

if (!$conn) {
    die("Database connection failed.");
}

// Handle Registration
if (isset($_POST['register'])) {
    $first = trim($_POST['first_name']);
    $last = trim($_POST['last_name']);
    $address = trim($_POST['address']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $error = "Passwords do not match!";
    } else {
        // Check if email exists already
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, address, email, phone, password) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $first, $last, $address, $email, $phone, $hashed);

            if ($stmt->execute()) {
                $success = "Registration successful. Please login.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}

// Handle Login
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['first_name'];
        header("Location: user_dashboard.php"); // Create this page for logged-in users
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>User Login/Register</title>
<style>
  body {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: linear-gradient(135deg, #d0f0c0, #a8e6a3);
  margin: 0;
  padding: 0;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  min-height: 100vh;
}

.navbar {
  position: fixed;
  top: 0; left: 0; right: 0;
  background: #2e7d32;
  padding: 15px 40px;
  box-shadow: 0 6px 12px rgba(0,0,0,0.2);
  display: flex;
  justify-content: space-between;
  align-items: center;
  z-index: 1000;
}

.navbar h2 {
  color: #d0f0c0;
  font-size: 26px;
  font-weight: 700;
  letter-spacing: 1.2px;
  user-select: none;
}

.navbar a {
  color: #a8e6a3;
  text-decoration: none;
  font-weight: 600;
  margin-left: 25px;
  font-size: 16px;
  padding: 8px 14px;
  border-radius: 8px;
  transition: background-color 0.3s ease, color 0.3s ease;
  user-select: none;
}

.navbar a:hover {
  background-color: #d0f0c0;
  color: #2e7d32;
  box-shadow: 0 4px 8px rgba(208, 240, 192, 0.6);
}

.container {
  margin-top: 110px;
  width: 420px;
  background: #f7fff7;
  border-radius: 18px;
  box-shadow: 0 15px 30px rgba(46, 125, 50, 0.25);
  padding: 40px 35px 50px;
  transition: box-shadow 0.4s ease;
}

.container:hover {
  box-shadow: 0 25px 50px rgba(46, 125, 50, 0.35);
}

h2 {
  text-align: center;
  color: #2e7d32;
  font-weight: 800;
  font-size: 28px;
  margin-bottom: 30px;
  letter-spacing: 0.8px;
  user-select: none;
}

.form-group {
  margin-bottom: 20px;
}

label {
  display: block;
  font-weight: 700;
  margin-bottom: 6px;
  color: #3a913f;
  letter-spacing: 0.5px;
  user-select: none;
}

input[type="text"],
input[type="email"],
input[type="password"],
textarea {
  width: 100%;
  padding: 14px 15px;
  border: 2px solid #a8e6a3;
  border-radius: 12px;
  font-size: 16px;
  color: #2e7d32;
  font-weight: 600;
  background: #e6f5e6;
  box-sizing: border-box;
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
  resize: vertical;
}

input[type="text"]:focus,
input[type="email"]:focus,
input[type="password"]:focus,
textarea:focus {
  border-color: #2e7d32;
  box-shadow: 0 0 10px #2e7d32a6;
  outline: none;
  background: #f0fff0;
}

button {
  width: 100%;
  padding: 14px;
  background: #4caf50;
  color: white;
  font-weight: 700;
  font-size: 18px;
  border: none;
  border-radius: 14px;
  cursor: pointer;
  box-shadow: 0 6px 15px #388e3ca8;
  transition: background-color 0.4s ease, box-shadow 0.4s ease;
  user-select: none;
}

button:hover {
  background: #388e3c;
  box-shadow: 0 10px 25px #2e7d3280;
}

.toggle-btns {
  text-align: center;
  margin-bottom: 35px;
  user-select: none;
}

.toggle-btns button {
  margin: 5px 12px;
  width: 40%;
  padding: 12px 0;
  border-radius: 16px;
  border: 2px solid #4caf50;
  background-color: #c8facc;
  color: #2e7d32;
  font-weight: 700;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease;
  box-shadow: 0 4px 12px #a8e6a3aa;
}

.toggle-btns button.active {
  background-color: #4caf50;
  color: #fff;
  box-shadow: 0 8px 22px #2e7d32cc;
  border-color: #388e3c;
}

.toggle-btns button:hover:not(.active) {
  background-color: #a8e6a3;
  color: #1b5e20;
  box-shadow: 0 6px 18px #71b75caa;
}

.hidden {
  display: none;
}

.error, .success {
  text-align: center;
  padding: 12px 20px;
  margin-bottom: 25px;
  border-radius: 14px;
  font-weight: 700;
  font-size: 16px;
  user-select: none;
}

.error {
  background: #ffebee;
  color: #d32f2f;
  box-shadow: 0 4px 12px #d32f2f55;
}

.success {
  background: #e8f5e9;
  color: #2e7d32;
  box-shadow: 0 4px 12px #2e7d3280;
}

</style>
</head>
<body>
  <div class="navbar">
    <h2>User Portal</h2>
    <div>
      <a href="index.html">Home</a>
    </div>
  </div>

  <div class="container">
    <div class="toggle-btns">
      <button id="loginBtn" class="active" onclick="showForm('login')">Login</button>
      <button id="registerBtn" onclick="showForm('register')">Register</button>
    </div>

    <?php if (isset($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
      <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Login Form -->
    <form id="loginForm" method="POST" <?= isset($_POST['register']) ? 'class="hidden"' : '' ?>>
      <h2>Login</h2>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required />
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required />
      </div>
      <button type="submit" name="login">Login</button>
    </form>

    <!-- Registration Form -->
    <form id="registerForm" method="POST" <?= isset($_POST['register']) ? '' : 'class="hidden"' ?>>
      <h2>Register</h2>
      <div class="form-group">
        <label>First Name</label>
        <input type="text" name="first_name" required />
      </div>
      <div class="form-group">
        <label>Last Name</label>
        <input type="text" name="last_name" required />
      </div>
      <div class="form-group">
        <label>Address</label>
        <textarea name="address" required></textarea>
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required />
      </div>
      <div class="form-group">
        <label>Phone Number</label>
        <input type="text" name="phone" required />
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required />
      </div>
      <div class="form-group">
        <label>Re-enter Password</label>
        <input type="password" name="confirm_password" required />
      </div>
      <button type="submit" name="register">Register</button>
    </form>
  </div>

<script>
function showForm(form) {
  const loginForm = document.getElementById('loginForm');
  const registerForm = document.getElementById('registerForm');
  const loginBtn = document.getElementById('loginBtn');
  const registerBtn = document.getElementById('registerBtn');

  if (form === 'login') {
    loginForm.classList.remove('hidden');
    registerForm.classList.add('hidden');
    loginBtn.classList.add('active');
    registerBtn.classList.remove('active');
  } else {
    loginForm.classList.add('hidden');
    registerForm.classList.remove('hidden');
    loginBtn.classList.remove('active');
    registerBtn.classList.add('active');
  }
}
</script>

</body>
</html>
