
<?php
session_start();

require_once __DIR__ . "/config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $message = "Please enter your email and password.";
    } else {
        $stmt = $pdo->prepare(
            "SELECT id, full_name, email, password, role
             FROM users
             WHERE email = ?"
        );

        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user["password"])) {
            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["full_name"] = $user["full_name"];
            $_SESSION["role"] = $user["role"];

            if ($user["role"] === "employer") {
                header("Location: employer_dashboard.php");
            } elseif ($user["role"] === "admin") {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: seeker_dashboard.php");
            }

            exit;
        } else {
            $message = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Job Finder</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 30px 15px;
        }

        .container {
            max-width: 420px;
            margin: 40px auto;
            background: white;
            padding: 28px;
            border-radius: 12px;
        }

        label {
            display: block;
            margin-top: 16px;
        }

        input, button {
            width: 100%;
            box-sizing: border-box;
            padding: 12px;
            margin-top: 7px;
        }

        button {
            background: #2563eb;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }

        .error {
            color: #b91c1c;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Log In</h1>

        <?php if ($message !== ""): ?>
            <p class="error">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <label for="email">Email Address</label>
            <input
                type="email"
                id="email"
                name="email"
                required
                value="<?= htmlspecialchars($_POST["email"] ?? "") ?>"
            >

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                required
            >

            <button type="submit">Log In</button>
        </form>

        <p>Don't have an account? <a href="register.php">Register</a></p>
    </div>
</body>
</html>
