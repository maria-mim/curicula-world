<?php
session_start();
include "connection_db.php";

// Handle Registration
if (isset($_POST['register'])) {
    $first = $_POST['first_name'];
    $last = $_POST['last_name'];
    $address = $_POST['address'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $error = "Passwords do not match!";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO admins (first_name, last_name, address, email, phone, password) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $first, $last, $address, $email, $phone, $hashed);

        if ($stmt->execute()) {
            $success = "Registration successful. Please login.";
        } else {
            $error = "Registration failed. Email might already exist.";
        }
    }
}

// Handle Login
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    $admin = $res->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['first_name'];
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $error = "Invalid email or password.";
    }
}

// Determine active form for button highlight and form display
$activeForm = "login"; // default
if (isset($_POST['register'])) {
    $activeForm = "register";
} elseif (isset($_POST['login'])) {
    $activeForm = "login";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Login/Register</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #eafaf1;
      margin: 0;
      padding: 0;
    }

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

    .container {
      width: 400px;
      margin: 50px auto;
      padding: 30px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.1);
      transition: background-color 0.5s ease;
    }

    /* Different bg colors for login and register forms */
    .container.login-active {
      background: #d0f0d6; /* light green */
    }

    .container.register-active {
      background: #d6e0f0; /* light blue */
    }

    h2 {
      text-align: center;
      color: #2e7d32;
      margin-bottom: 20px;
    }

    .toggle-btns {
      text-align: center;
      margin-bottom: 25px;
    }

    .toggle-btns button {
      margin: 0 10px;
      width: 140px;
      padding: 12px 0;
      font-weight: 600;
      font-size: 16px;
      border: none;
      border-radius: 30px;
      cursor: pointer;
      color: white;
      background: #7ccc7c;
      box-shadow: 0 4px 8px rgba(118, 203, 118, 0.4);
      transition: background-color 0.3s, box-shadow 0.3s;
      user-select: none;
    }

    .toggle-btns button:hover:not(.active) {
      background: #a0d3a0;
      box-shadow: 0 6px 12px rgba(160, 211, 160, 0.6);
    }

    .toggle-btns button.active {
      background: #2e7d32;
      box-shadow: 0 6px 15px rgba(46, 125, 50, 0.7);
    }

    .form-group {
      margin-bottom: 18px;
    }

    label {
      display: block;
      font-weight: 600;
      margin-bottom: 6px;
      color: #2e7d32;
    }

    input[type="text"],
    input[type="email"],
    input[type="password"],
    textarea {
      width: 100%;
      padding: 12px;
      border: 2px solid #a5d6a7;
      border-radius: 8px;
      box-sizing: border-box;
      font-size: 15px;
      transition: border-color 0.3s;
      font-family: inherit;
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="password"]:focus,
    textarea:focus {
      border-color: #2e7d32;
      outline: none;
      background: #f3fff3;
    }

    textarea {
      resize: vertical;
      min-height: 70px;
    }

    button[type="submit"] {
      width: 100%;
      padding: 14px;
      background: #388e3c;
      border: none;
      color: white;
      font-weight: 700;
      font-size: 16px;
      border-radius: 8px;
      cursor: pointer;
      box-shadow: 0 5px 15px rgba(56, 142, 60, 0.6);
      transition: background-color 0.3s, box-shadow 0.3s;
      user-select: none;
    }

    button[type="submit"]:hover {
      background: #27692a;
      box-shadow: 0 7px 18px rgba(39, 105, 42, 0.8);
    }

    .error {
      color: #b00020;
      background: #f8d7da;
      border: 1px solid #f5c2c7;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 18px;
      font-weight: 600;
      text-align: center;
    }

    .success {
      color: #155724;
      background: #d4edda;
      border: 1px solid #c3e6cb;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 18px;
      font-weight: 600;
      text-align: center;
    }

    .hidden {
      display: none;
    }
  </style>
</head>
<body>
  <div class="navbar">
    <h2>Admin</h2>
    <div>
      <a href="index.html">Home</a>
    </div>
  </div>

  <div class="container <?= $activeForm === 'register' ? 'register-active' : 'login-active' ?>">
    <div class="toggle-btns">
      <button id="btnLogin" class="<?= $activeForm === 'login' ? 'active' : '' ?>" onclick="showForm('login')">Login</button>
      <button id="btnRegister" class="<?= $activeForm === 'register' ? 'active' : '' ?>" onclick="showForm('register')">Register</button>
    </div>

    <?php if (isset($error)): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
      <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Login Form -->
    <form id="loginForm" method="POST" <?= $activeForm === 'register' ? 'class="hidden"' : '' ?>>
      <h2>Admin Login</h2>
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
    <form id="registerForm" method="POST" <?= $activeForm === 'register' ? '' : 'class="hidden"' ?>>
      <h2>Register Admin</h2>
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
      const btnLogin = document.getElementById('btnLogin');
      const btnRegister = document.getElementById('btnRegister');
      const container = document.querySelector('.container');

      if (form === 'login') {
        loginForm.classList.remove('hidden');
        registerForm.classList.add('hidden');
        btnLogin.classList.add('active');
        btnRegister.classList.remove('active');
        container.classList.remove('register-active');
        container.classList.add('login-active');
      } else {
        loginForm.classList.add('hidden');
        registerForm.classList.remove('hidden');
        btnLogin.classList.remove('active');
        btnRegister.classList.add('active');
        container.classList.remove('login-active');
        container.classList.add('register-active');
      }
    }
  </script>
</body>
</html>
