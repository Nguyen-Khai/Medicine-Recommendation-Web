<div class="admin-users-container">
    <h2 class="admin-heading">Danh sách người dùng</h2>

    <div class="admin-users-controls">
        <input type="text" class="admin-search-input" placeholder="Tìm kiếm người dùng...">
        <button class="admin-add-btn">+ Thêm người dùng</button>
    </div>

    <table class="admin-users-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Tên đăng nhập</th>
                <th>Email</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $index => $user): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <button class="action-btn edit-btn">Sửa</button>
                            <button class="action-btn delete-btn">Xóa</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">Không có người dùng nào.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
