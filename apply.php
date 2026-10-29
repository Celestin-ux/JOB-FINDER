
<?php
session_start();
require_once __DIR__ . "/config/db.php";

if (
    !isset($_SESSION["user_id"]) ||
    $_SESSION["role"] !== "job_seeker"
) {
    header("Location: login.php");
    exit;
}

$jobId = filter_input(INPUT_GET, "job_id", FILTER_VALIDATE_INT);
$message = "";

if (!$jobId || $jobId < 1) {
    http_response_code(400);
    exit("Invalid job ID.");
}

$stmt = $pdo->prepare(
    "SELECT id, title FROM jobs WHERE id = ? AND status = 'open'"
);
$stmt->execute([$jobId]);
$job = $stmt->fetch();

if (!$job) {
    http_response_code(404);
    exit("Job not found or no longer open.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $coverLetter = trim($_POST["cover_letter"] ?? "");

    try {
        $stmt = $pdo->prepare(
            "INSERT INTO applications (job_id, applicant_id, cover_letter)
             VALUES (?, ?, ?)"
        );

        $stmt->execute([
            $jobId,
            $_SESSION["user_id"],
            $coverLetter !== "" ? $coverLetter : null
        ]);

        header("Location: my_applications.php?success=1");
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() === "23000") {
            $message = "You have already applied for this job.";
        } else {
            error_log("Job application error: " . $e->getMessage());
            $message = "Could not submit your application. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Apply for Job | Job Finder</title>
</head>
<body>
    <h1>Apply for: <?= htmlspecialchars($job["title"]) ?></h1>

    <?php if ($message !== ""): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label for="cover_letter">Cover Letter (optional)</label><br>
        <textarea id="cover_letter" name="cover_letter" rows="8"></textarea><br><br>

        <button type="submit">Submit Application</button>
    </form>

    <p><a href="jobs.php">Back to Jobs</a></p>
</body>
</html>
