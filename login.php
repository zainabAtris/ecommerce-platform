<?php
session_start();
try {
    $db = new PDO('sqlite:users.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE,
        password TEXT
    )");
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['register'])) {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        if (!empty($username) && !empty($password)) {
            $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<script>alert('Username already exists. Please choose another.');</script>";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
                if ($stmt->execute([':username' => $username, ':password' => $hashed_password])) {
                    echo "<script>alert('Registration successful! You can now log in.');</script>";
                } else {
                    echo "<script>alert('Registration failed. Please try again later.');</script>";
                }
            }
        } else {
            echo "<script>alert('Please fill in all fields.');</script>";
        }
    } elseif (isset($_POST['login'])) {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        if (!empty($username) && !empty($password)) {
            $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user'] = $username;
                header("Location: index.php");
                exit();
            } else {
                echo "<script>alert('Invalid username or password.');</script>";
            }
        } else {
            echo "<script>alert('Please fill in all fields.');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login & Register</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .container { background-color: #fff; padding: 20px 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); width: 320px; text-align: center; }
        h2 { margin-bottom: 20px; color: #333; }
        input { width: calc(100% - 22px); padding: 10px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; }
        button { width: 100%; padding: 10px; border: none; border-radius: 5px; cursor: pointer; margin-top: 10px; font-weight: bold; }
        .login-btn { background-color: #4CAF50; color: white; }
        .register-btn { background-color: #008CBA; color: white; }
        hr { margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form method="post">
            <input type="text" name="username" placeholder="Enter Username" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <button type="submit" name="login" class="login-btn">Login</button>
        </form>
        <hr>
        <h2>Register</h2>
        <form method="post">
            <input type="text" name="username" placeholder="Enter Username" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <button type="submit" name="register" class="register-btn">Register</button>
        </form>
    </div>
</body>
</html>
