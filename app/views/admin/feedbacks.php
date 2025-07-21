<h2>Phản hồi từ người dùng</h2>

<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Email / Người dùng</th>
      <th>Nội dung</th>
      <th>Thời gian</th>
      <th>Trạng thái</th>
      <th>Hành động</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($feedbacks as $f): ?>
    <tr>
      <td><?= $f['id'] ?></td>
      <td><?= $f['username'] ?? $f['email'] ?></td>
      <td><?= nl2br(htmlspecialchars($f['message'])) ?></td>
      <td><?= $f['created_at'] ?></td>
      <td><?= $f['is_read'] ? 'Đã đọc' : '<strong>Chưa đọc</strong>' ?></td>
      <td>
        <?php if (!$f['is_read']): ?>
          <a href="?route=mark-feedback&id=<?= $f['id'] ?>">Đánh dấu đã đọc</a>
        <?php endif; ?>
        | <a href="?route=delete-feedback&id=<?= $f['id'] ?>" onclick="return confirm('Xoá phản hồi này?')">Xoá</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
