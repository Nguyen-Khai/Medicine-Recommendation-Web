<h2>Phản hồi lại người dùng</h2>

<p><strong>Đến:</strong> <?= htmlspecialchars($feedback['email']) ?> (ID người dùng:<?= $feedback['user_id'] ?>)</p>
<p><strong>Nội dung gốc:</strong><br><?= nl2br(htmlspecialchars($feedback['message'])) ?></p>

<form method="POST" action="index.php?route=send-feedback-reply&id=<?= $feedback['id'] ?>" style="margin-top: 20px;">
  <textarea name="reply_message" rows="6" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc;" placeholder="Nhập nội dung phản hồi..."></textarea>

  <button type="submit" style="margin-top: 12px; padding: 10px 20px; background-color: #28a745; color: white; border: none; border-radius: 4px;">
    Gửi phản hồi
  </button>
</form>
