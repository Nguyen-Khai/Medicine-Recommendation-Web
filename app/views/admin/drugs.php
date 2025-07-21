<?php
$title = "Drugs Management";
ob_start();

// Khởi động session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$admin = $_SESSION['admin'] ?? null;
?>

<div class="admin-drugs-container">
    <form method="GET" action="" class="filter-form">
        <input type="hidden" name="route" value="admin-drugs">
        <input type="text" name="keyword" placeholder="Search for drug name..." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
        <button type="submit">Search</button>
    </form>

    <?php if ($admin && in_array($admin['role'], ['superadmin', 'manager'])): ?>
        <div style="text-align: right; margin: 10px 0;">
            <a href="index.php?route=admin-add-drug" class="add-drug-btn">+ Add New Drug</a>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Drug Name</th>
                    <th>Dosage Form</th>
                    <th>Registration Number</th>
                    <th>Specification</th>
                    <th>Expiration Date</th>
                    <th>Active Ingredients</th>
                    <th>Details</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($drugs)): ?>
                    <?php foreach ($drugs as $drug): ?>
                        <tr>
                            <td><?= $drug['id'] ?></td>
                            <td><?= htmlspecialchars($drug['ten_thuoc']) ?></td>
                            <td><?= htmlspecialchars($drug['dang_bao_che']) ?></td>
                            <td><?= htmlspecialchars($drug['so_dang_ky']) ?></td>
                            <td><?= htmlspecialchars($drug['quy_cach']) ?></td>
                            <td><?= htmlspecialchars($drug['han_su_dung']) ?></td>
                            <td>
                                <?php if (!empty($drug['hoat_chat'])): ?>
                                    <?php foreach ($drug['hoat_chat'] as $hc): ?>
                                        <?= htmlspecialchars($hc['ten_hoat_chat']) ?> (<?= htmlspecialchars($hc['ham_luong']) ?>)<br>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <i>None</i>
                                <?php endif; ?>
                            </td>
                            <td><a href="<?= htmlspecialchars($drug['url']) ?>" target="_blank" class="view-link">View</a></td>
                            <td>
                                <?php if ($admin && in_array($admin['role'], ['superadmin', 'manager'])): ?>
                                    <a href="index.php?route=admin-edit-drug&id=<?= $drug['id'] ?>" class="edit-btn">Edit</a>
                                <?php endif; ?>
                                <?php if ($admin && $admin['role'] === 'superadmin'): ?>
                                    <a href="index.php?route=admin-delete-drug&id=<?= $drug['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this drug?')">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="9" style="text-align: center;">No matching results found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php
            $maxPagesToShow = 2;
            $startPage = max(1, $currentPage - $maxPagesToShow);
            $endPage = min($totalPages, $currentPage + $maxPagesToShow);
            ?>

            <?php if ($currentPage > 1): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage - 1])) ?>">&laquo;</a>
            <?php endif; ?>

            <?php if ($startPage > 1): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => 1])) ?>">1</a>
                <?php if ($startPage > 2): ?><span class="dots">...</span><?php endif; ?>
            <?php endif; ?>

            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="<?= ($i == $currentPage) ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php if ($endPage < $totalPages): ?>
                <?php if ($endPage < $totalPages - 1): ?><span class="dots">...</span><?php endif; ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $totalPages])) ?>"><?= $totalPages ?></a>
            <?php endif; ?>

            <?php if ($currentPage < $totalPages): ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $currentPage + 1])) ?>">&raquo;</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>