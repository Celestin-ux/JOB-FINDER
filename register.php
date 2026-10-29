
<?php
require_once __DIR__ . "/config/db.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullName = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $role = $_POST["role"] ?? "job_seeker";

    if ($fullName === "" || $email === "" || $password === "") {
        $message = "Please fill in all required fields.";
        $messageType = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "error";
    } elseif (strlen($password) < 8) {
        $message = "Password must contain at least 8 characters.";
        $messageType = "error";
    } elseif (!in_array($role, ["job_seeker", "employer"], true)) {
        $message = "Please select a valid account type.";
        $messageType = "error";
    } else {
        try {
            $check = $pdo->prepare(
                "SELECT id FROM users WHERE email = ?"
            );
            $check->execute([$email]);

            if ($check->fetch()) {
                $message = "This email is already registered.";
                $messageType = "error";
            } else {
                $hashedPassword = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $sql = "INSERT INTO users
                        (full_name, email, password, role)
                        VALUES (?, ?, ?, ?)";

                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    $fullName,
                    $email,
                    $hashedPassword,
                    $role
                ]);

                $message = "Registration successful! You can now log in.";
                $messageType = "success";
            }
        } catch (PDOException $e) {
            error_log("Registration error: " . $e->getMessage());
            $message = "Registration failed. Please try again.";
            $messageType = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | Job Finder</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            margin: 0;
            padding: 30px 15px;
        }

        .container {
            max-width: 450px;
            margin: 30px auto;
            padding: 28px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(0,0,0,.08);
        }

        h1 {
            text-align: center;
            color: #1d4ed8;
        }

        label {
            display: block;
            margin-top: 16px;
            font-weight: bold;
        }

        input, select, button {
            width: 100%;
            padding: 12px;
            margin-top: 7px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 16px;
        }

        button {
            margin-top: 22px;
            background: #2563eb;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        .message {
            padding: 12px;
            border-radius: 6px;
            margin: 15px 0;
        }

        .success {
            background: #dcfce7;
            color: #166534;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create Account</h1>
        <p>Join Job Finder to find or post job opportunities.</p>

        <?php if ($message !== ""): ?>
            <div class="message <?= htmlspecialchars($messageType) ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="full_name">Full Name</label>
            <input
                type="text"
                id="full_name"
                name="full_name"
                maxlength="100"
                required
                value="<?= htmlspecialchars($_POST["full_name"] ?? "") ?>"
            >

            <label for="email">Email Address</label>
            <input
                type="email"
                id="email"
                name="email"
                maxlength="150"
                required
                value="<?= htmlspecialchars($_POST["email"] ?? "") ?>"
            >

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                minlength="8"
                required
            >

            <label for="role">Account Type</label>
            <select id="role" name="role" required>
                <option value="job_seeker">Job Seeker</option>
                <option value="employer">Employer</option>
            </select>

            <button type="submit">Register</button>
        </form>

        <div class="login-link">
            Already have an account?
            <a href="login.php">Log in</a>
        </div>
    </div>
</body>
</html>
