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
                                    <a href="index.php?route=admin-edit-drug&id=<?= $drug['id'] ?>" class="btn btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                <?php endif; ?>

                                <?php if ($admin && $admin['role'] === 'superadmin'): ?>
                                    <a href="javascript:void(0);" class="btn btn-delete"
                                        onclick="confirmDelete(<?= $drug['id'] ?>, '<?= htmlspecialchars($drug['ten_thuoc'], ENT_QUOTES) ?>')">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align: center;">No matching results found.</td>
                    </tr>
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
<style>
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 12px;
        font-size: 14px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 500;
        transition: background-color 0.3s ease;
        border: none;
        cursor: pointer;
    }

    .btn-edit {
        background-color: #28a745;
        color: white;
    }

    .btn-edit:hover {
        background-color: #218838;
    }

    .btn-delete {
        background-color: #dc3545;
        color: white;
        margin-top: 10px;
    }

    .btn-delete:hover {
        background-color: #c82333;
    }
</style>
<script>
    function confirmDelete(drugId, drugName) {
        Swal.fire({
            title: 'Are you sure?',
            html: `You are about to delete <b style="color:#d33;">${drugName}</b>. This action cannot be undone.`,
            imageUrl: 'assets/images/question_mask.png',
            imageWidth: 100,
            imageHeight: 100,
            imageAlt: 'Warning Image',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `index.php?route=admin-delete-drug&id=${drugId}`;
            }
        });
    }
</script>