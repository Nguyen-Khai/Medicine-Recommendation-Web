<?php
$diseaseName = $_GET['disease'] ?? '';
$historyId = $_GET['id'] ?? '';
$createdAt = $_GET['time'] ?? '';
$createdAtFormatted = $createdAt ? date('H:i d/m/Y', strtotime($createdAt)) : '';
$userEmail = $_SESSION['user_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Feedback - HEALMATE</title>
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Merriweather&display=swap">
    <style>
        body {
            background: linear-gradient(270deg, #863333ff, #F5C45E, #FE7743, #328E6E, #4DA8DA, #AA60C8);
            background-size: 1000% 1000%;
            animation: waveGradient 20s ease infinite;
            font-family: 'Inter';
        }

        @keyframes waveGradient {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .result-container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h1.feedback {
            text-align: center;
            background: linear-gradient(270deg, #F95454, #F5C45E, #FE7743, #328E6E, #4DA8DA, #AA60C8);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .result-section {
            margin-bottom: 25px;
        }

        .result-section img {
            width: 30px;
            vertical-align: middle;
            margin-right: 10px;
        }

        form label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        form input[type="email"],
        form textarea {
            width: 100%;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #ccc;
            margin-top: 5px;
            font-family: 'Inter';
        }

        form button {
            margin-top: 20px;
            padding: 10px 20px;
            border: none;
            border-radius: 12px;
            background-color: #328E6E;
            color: white;
            cursor: pointer;
            font-size: 16px;
        }

        form button:hover {
            background-color: #2c6f57;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            text-decoration: none;
            color: #0D92F4;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .alert {
            margin: 10px 0;
            font-weight: bold;
        }

        .alert.success {
            color: green;
        }

        .alert.error {
            color: red;
        }
    </style>
</head>

<body>
    <?php include('header.php'); ?>

    <div class="result-container">
        <h1 class="feedback">Submit Feedback</h1>

        <?php if (!empty($_GET['success'])): ?>
            <p style="color: green;">Thank you for your feedback!</p>

            <?php if (!empty($_SESSION['feedback_info'])): ?>
                <div style="border-left: 4px solid green; background: #f0f0f0; padding: 10px; margin: 10px 0;">
                    <strong>Your submitted message:</strong>
                    <p><?= htmlspecialchars($_SESSION['feedback_info']['message']) ?></p>
                    <small>At: <?= htmlspecialchars($_SESSION['feedback_info']['created_at']) ?></small>
                </div>
                <?php unset($_SESSION['feedback_info']); ?>
            <?php endif; ?>
        <?php elseif (!empty($_GET['error'])): ?>
            <p style="color: red;">Please enter your feedback message.</p>
        <?php endif; ?>

        <div class="result-section">
            <?php if ($diseaseName && $createdAtFormatted): ?>
                <p>You are submitting feedback for the diagnosis of <strong><?= htmlspecialchars($diseaseName) ?></strong>
                    at <strong><?= $createdAtFormatted ?></strong>.
                </p>
            <?php else: ?>
                <p>Submit feedback to the HEALMATE system.</p>
            <?php endif; ?>
        </div>

        <form action="index.php?route=handle-feedback" method="POST">
            <input type="hidden" name="history_id" value="<?= htmlspecialchars($historyId) ?>">
            <input type="hidden" name="disease" value="<?= htmlspecialchars($diseaseName) ?>">
            <input type="hidden" name="created_at" value="<?= htmlspecialchars($createdAt) ?>">

            <label for="email">Email (optional)</label>
            <input type="email" name="email" id="email" placeholder="example@example.com" value="<?= htmlspecialchars($userInfo['email'] ?? '') ?>">

            <label for="message">Feedback Message</label>
            <textarea name="message" id="message" rows="6" required placeholder="Enter your feedback here..."></textarea>

            <button type="submit">Submit Feedback</button>
        </form>

        <a href="index.php?route=profile#advice-history" class="back-link">← Back to Consultation History</a>
    </div>
</body>

</html>