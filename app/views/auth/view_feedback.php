<?php
$createdAtFormatted = date('H:i d/m/Y', strtotime($feedback['created_at']));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Phản hồi của bạn - HEALMATE</title>
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Merriweather&display=swap">
    <style>
        body {
            background: linear-gradient(270deg, #863333ff, #F5C45E, #FE7743, #328E6E, #4DA8DA, #AA60C8);
            background-size: 1000% 1000%;
            animation: waveGradient 20s ease infinite;
            font-family: 'Merriweather', serif;
            margin: 0;
            padding: 0;
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
            max-width: 720px;
            margin: 60px auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
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

        .feedback-title {
            text-align: center;
            font-size: 30px;
            margin-bottom: 30px;
            color: #333;
            background: linear-gradient(90deg, #F95454, #FFAF51, #6AC57E, #4DA8DA);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .feedback-box {
            background-color: #fafafa;
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 25px 30px;
        }

        .feedback-meta {
            font-size: 16px;
            color: #444;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .feedback-message h3 {
            margin: 0 0 10px 0;
            font-size: 18px;
            color: #222;
        }

        .feedback-message p {
            background-color: #fff;
            border-left: 4px solid #4DA8DA;
            padding: 12px 16px;
            font-size: 16px;
            line-height: 1.6;
            color: #333;
            white-space: pre-line;
        }

        .back-link {
            display: inline-block;
            margin-top: 30px;
            font-size: 16px;
            color: #007BFF;
            text-decoration: none;
            transition: all 0.2s;
        }

        .back-link:hover {
            text-decoration: underline;
            color: #0056b3;
        }
    </style>
</head>

<body>
    <?php include('header.php'); ?>

    <div class="result-container">
        <h1 class="feedback-title">Your Feedback</h1>

        <div class="feedback-box">
            <div class="feedback-meta">
                <div><strong>🕒 Sent at:</strong> <?= $createdAtFormatted ?></div>

                <?php if (!empty($feedback['email'])): ?>
                    <div><strong>📧 Email:</strong> <?= htmlspecialchars($feedback['email']) ?></div>
                <?php endif; ?>
            </div>

            <div class="feedback-message">
                <h3>Feedback Content</h3>
                <p><?= nl2br(htmlspecialchars($feedback['message'])) ?></p>
            </div>
        </div>

        <a href="index.php?route=profile#advice-history" class="back-link">← Back to Consultation History</a>
    </div>
</body>

</html>