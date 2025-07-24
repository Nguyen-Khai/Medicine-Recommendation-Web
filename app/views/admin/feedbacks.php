<style>
  /* Bảng */
  table {
    width: 100%;
    border-collapse: collapse;
    background-color: #fff;
    font-family: 'Inter', sans-serif;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    border: 1px solid #e0e0e0;
  }

  thead {
    background-color: #f8f9fa;
  }

  th,
  td {
    padding: 12px 16px;
    text-align: left;
    border: 1px solid #e0e0e0;
    vertical-align: top;
    font-size: 14px;
    color: #333;
  }

  th {
    font-weight: 600;
    font-size: 15px;
    color: #333;
    letter-spacing: 0.3px;
  }

  tbody tr:hover {
    background-color: #f6f6f6;
  }

  td strong {
    color: #e74c3c;
  }

  /* Link hành động */
  td a {
    color: #007bff;
    text-decoration: none;
    font-weight: 500;
    margin-right: 8px;
  }

  td a:hover {
    text-decoration: underline;
  }

  /* Responsive nếu cần */
  @media (max-width: 768px) {
    table {
      font-size: 13px;
    }
  }
</style>

<!-- FORM TÌM KIẾM & LỌC -->
<form method="GET" action="index.php" style="margin-bottom: 20px; display: flex; gap: 10px;">
  <input type="hidden" name="route" value="feedbacks">

  <input type="text" name="search" placeholder="Search email / username..."
    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
    style="padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; width: 250px;">

  <select name="status" style="padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px;">
    <option value="">All read status</option>
    <option value="read" <?= ($_GET['status'] ?? '') === 'read' ? 'selected' : '' ?>>Read</option>
    <option value="unread" <?= ($_GET['status'] ?? '') === 'unread' ? 'selected' : '' ?>>Unread</option>
  </select>

  <select name="reply" style="padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px;">
    <option value="">All reply status</option>
    <option value="replied" <?= ($_GET['reply'] ?? '') === 'replied' ? 'selected' : '' ?>>Replied</option>
    <option value="not_replied" <?= ($_GET['reply'] ?? '') === 'not_replied' ? 'selected' : '' ?>>Not Replied</option>
  </select>

  <button type="submit" style="padding: 8px 16px; background-color: #007bff; color: white; border: none; border-radius: 4px;">
    Search
  </button>
</form>

<?php if (!empty($_GET['status']) && $_GET['status'] === 'success'): ?>
  <div style="padding: 12px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 6px; margin-bottom: 20px;">
    ✅ <?= htmlspecialchars($_GET['message']) ?>
  </div>
<?php endif; ?>


<!-- BẢNG PHẢN HỒI -->
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Email / User</th>
      <th>Message</th>
      <th>Time</th>
      <th>Status</th>
      <th>Reply</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($feedbacks as $f): ?>
      <tr>
        <td><?= $f['id'] ?></td>
        <td><?= htmlspecialchars($f['username'] ?? $f['email']) ?></td>
        <td><?= nl2br(htmlspecialchars($f['message'])) ?></td>
        <td><?= $f['created_at'] ?></td>
        <td>
          <?php if ($f['is_read']): ?>
            <span class="status-read">Read</span>
          <?php else: ?>
            <span class="status-unread">Unread</span>
          <?php endif; ?>
        </td>
        <td>
          <?php if ($f['has_replied']): ?>
            <span class="reply-status replied">Replied</span>
          <?php else: ?>
            <span class="reply-status not-replied">Not Replied</span>
          <?php endif; ?>
        </td>
        <td>
          <?php if (!$f['is_read']): ?>
            <a href="?route=mark-feedback&id=<?= $f['id'] ?>" class="action-link">Mark as Read</a>
          <?php endif; ?>

          <a href="?route=delete-feedback&id=<?= $f['id'] ?>"
            onclick="return confirm('Delete this feedback?')"
            class="action-link" style="color: #dc3545;">Delete</a>

          <?php if (!$f['has_replied']): ?>
            <a href="?route=reply-feedback&id=<?= $f['id'] ?>"
              class="action-link" style="color: #28a745;">Reply</a>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>