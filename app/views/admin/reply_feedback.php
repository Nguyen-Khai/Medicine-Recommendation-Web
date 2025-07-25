<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reply to Feedback - HEALMATE Admin</title>
  <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', sans-serif;
      background-color: #f2f4f7;
      color: #333;
      margin: 0;
      padding: 0;
    }

    .card {
      background: #fff;
      border-radius: 12px;
      padding: 24px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
      max-width: 700px;
      margin: 40px auto;
    }

    .card-title {
      font-size: 24px;
      font-weight: 600;
      margin-bottom: 20px;
      color: #222;
    }

    .user-id {
      color: #666;
      font-size: 14px;
    }

    .original-message-box {
      background-color: #f9fafb;
      padding: 16px;
      border-left: 4px solid #007bff;
      border-radius: 8px;
      margin: 20px 0;
    }

    .original-message-content {
      white-space: pre-wrap;
      color: #333;
      margin-top: 8px;
    }

    .reply-form label {
      font-weight: 500;
      margin-bottom: 8px;
      display: block;
      font-size: 16px;
    }

    .reply-form textarea {
      width: 100%;
      padding: 12px;
      font-size: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      resize: vertical;
      transition: border-color 0.3s ease;
      background: #fff;
    }

    .reply-form textarea:focus {
      border-color: #007bff;
      outline: none;
    }

    .reply-form button {
      margin-top: 16px;
      padding: 10px 24px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      font-weight: 500;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .reply-form button:hover {
      background-color: #0056b3;
    }

    .back-button {
      display: inline-block;
      margin-bottom: 20px;
      color: #007bff;
      text-decoration: none;
      font-weight: 500;
      font-size: 15px;
      transition: color 0.3s ease;
    }

    .back-button:hover {
      color: #0056b3;
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="card feedback-reply-card">
    <a href="index.php?route=feedbacks" class="back-button">← Back to Feedbacks</a>

    <h2 class="card-title">📩 Reply to User</h2>

    <div class="card-content">
      <p><strong>To:</strong> <?= htmlspecialchars($feedback['email']) ?> 
        <span class="user-id">(User ID: <?= $feedback['user_id'] ?>)</span>
      </p>

      <div class="original-message-box">
        <p><strong>Original Message:</strong></p>
        <div class="original-message-content">
          <?= nl2br(htmlspecialchars($feedback['message'])) ?>
        </div>
      </div>

      <form method="POST" action="index.php?route=send-feedback-reply&id=<?= $feedback['id'] ?>" class="reply-form">
        <label for="reply_message">Your Reply</label>
        <textarea id="reply_message" name="reply_message" rows="6" placeholder="Write your reply here..."></textarea>

        <button type="submit">✉️ Send Reply</button>
      </form>
    </div>
  </div>

</body>
</html>
