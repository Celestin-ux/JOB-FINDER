
<?php
require_once __DIR__ . "/config/db.php";

$stmt = $pdo->query(
    "SELECT jobs.id, jobs.title, jobs.description,
            jobs.location, jobs.salary, jobs.job_type,
            jobs.created_at, users.full_name AS employer_name
     FROM jobs
     INNER JOIN users ON jobs.employer_id = users.id
     WHERE jobs.status = 'open'
     ORDER BY jobs.created_at DESC"
);

$jobs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Available Jobs | Job Finder</title>
</head>
<body>
    <h1>Available Jobs</h1>

    <?php if (!$jobs): ?>
        <p>No jobs have been posted yet.</p>
    <?php endif; ?>

    <?php foreach ($jobs as $job): ?>
        <article>
            <h2><?= htmlspecialchars($job["title"]) ?></h2>

            <p>
                <strong>Employer:</strong>
                <?= htmlspecialchars($job["employer_name"]) ?>
            </p>

            <p>
                <strong>Location:</strong>
                <?= htmlspecialchars($job["location"] ?? "Not specified") ?>
            </p>

            <p>
                <strong>Job Type:</strong>
                <?= htmlspecialchars($job["job_type"] ?? "Not specified") ?>
            </p>

            <p>
                <strong>Salary:</strong>
                <?= $job["salary"] !== null
                    ? htmlspecialchars(number_format((float)$job["salary"], 2))
                    : "Not specified" ?>
            </p>

            <p><?= nl2br(htmlspecialchars($job["description"])) ?></p>
            <hr>
        </article>
    <?php endforeach; ?>

    <p><a href="login.php">Log In</a></p>


    <p>
    <a href="apply.php?job_id=<?= (int)$job['id'] ?>">
        Apply for this job
    </a>
</p>
</body>
</html>
