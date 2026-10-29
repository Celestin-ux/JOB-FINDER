
<?php
session_start();
require_once __DIR__ . "/config/db.php";

if (
    !isset($_SESSION["user_id"]) ||
    $_SESSION["role"] !== "employer"
) {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST["title"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $location = trim($_POST["location"] ?? "");
    $jobType = trim($_POST["job_type"] ?? "");
    $salaryInput = trim($_POST["salary"] ?? "");

    if ($title === "" || $description === "" || $location === "") {
        $message = "Please complete all required fields.";
    } elseif (
        $salaryInput !== "" &&
        (!is_numeric($salaryInput) || (float)$salaryInput < 0)
    ) {
        $message = "Enter a valid, non-negative salary.";
    } else {
        $salary = $salaryInput === "" ? null : (float)$salaryInput;

        $stmt = $pdo->prepare(
            "INSERT INTO jobs
             (employer_id, title, description, location, salary, job_type)
             VALUES (?, ?, ?, ?, ?, ?)"
        );

        $stmt->execute([
            $_SESSION["user_id"],
            $title,
            $description,
            $location,
            $salary,
            $jobType !== "" ? $jobType : null
        ]);

        $message = "Job posted successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Post a Job | Job Finder</title>
</head>
<body>
    <h1>Post a Job Vacancy</h1>

    <?php if ($message !== ""): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="title">Job Title *</label><br>
        <input id="title" name="title" maxlength="150" required><br><br>

        <label for="description">Job Description *</label><br>
        <textarea id="description" name="description" required></textarea><br><br>

        <label for="location">Location *</label><br>
        <input id="location" name="location" maxlength="100" required><br><br>

        <label for="salary">Salary (optional)</label><br>
        <input id="salary" name="salary" type="number" min="0" step="0.01"><br><br>

        <label for="job_type">Job Type</label><br>
        <select id="job_type" name="job_type">
            <option value="">Choose a type</option>
            <option value="Full-time">Full-time</option>
            <option value="Part-time">Part-time</option>
            <option value="Internship">Internship</option>
            <option value="Contract">Contract</option>
        </select><br><br>

        <button type="submit">Publish Job</button>
    </form>

    <p><a href="jobs.php">View Available Jobs</a></p>
</body>
</html>
