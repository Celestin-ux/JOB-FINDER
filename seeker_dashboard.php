
<?php
session_start();

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "job_seeker") {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Job Seeker Dashboard</title>
</head>
<body>
    <h1>
        Welcome, <?= htmlspecialchars($_SESSION["full_name"]) ?>!
    </h1>
    <p>You are logged in to your Job Finder account.</p>
    <a href="logout.php">Log Out</a>
</body>
</html>
