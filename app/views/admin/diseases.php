<?php
$title = "Disease Management";
ob_start();
?>

<div class="admin-diseases-container">

    <form method="GET" action="index.php" class="search-form">
        <input type="hidden" name="route" value="admin-diseases">
        <input
            type="text"
            name="keyword"
            class="search-input"
            placeholder="Search by disease name"
            value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
        <button type="submit" class="search-button">Sreach</button>
    </form>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Disease Name</th>
                    <th>Description</th>
                    <th>Symptoms (Weight)</th>
                    <th>Diet</th>
                    <th>Medication</th>
                    <th>Prevention</th>
                    <th>Workout</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($diseases)): ?>
                    <?php foreach ($diseases as $d): ?>
                        <tr>
                            <td><?= $d['id'] ?></td>
                            <td><?= htmlspecialchars($d['disease']) ?></td>
                            <td><?= htmlspecialchars($d['description']) ?></td>
                            <td>
                                <?php foreach ($d['symptoms'] as $s): ?>
                                    <?= htmlspecialchars($s['symptom']) ?> (<?= $s['weight'] ?>)<br>
                                <?php endforeach; ?>
                            </td>
                            <td>
                                <?php foreach ($d['diets'] as $diet): ?>
                                    <?= htmlspecialchars($diet['diet']) ?><br>
                                <?php endforeach; ?>
                            </td>
                            <td>
                                <?php foreach ($d['medications'] as $m): ?>
                                    <?= htmlspecialchars($m['medication']) ?><br>
                                <?php endforeach; ?>
                            </td>
                            <td>
                                <?php foreach ($d['precautions'] as $p): ?>
                                    <?= htmlspecialchars($p['precaution']) ?><br>
                                <?php endforeach; ?>
                            </td>
                            <td>
                                <?php foreach ($d['workouts'] as $w): ?>
                                    <?= htmlspecialchars($w['workout']) ?><br>
                                <?php endforeach; ?>
                            </td>
                            <td>
                                <a href="index.php?route=admin-edit-disease&id=<?= $d['id'] ?>" class="btn btn-edit">
                                    <i class="fas fa-edit"></i> Edit
                                </a>

                                <?php if ($_SESSION['admin']['role'] === 'superadmin'): ?>
                                    <a href="javascript:void(0);" onclick="confirmDeleteDisease(<?= $d['id'] ?>, '<?= htmlspecialchars($d['disease'], ENT_QUOTES) ?>')" class="btn btn-delete">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </a>
                                <?php endif; ?>

                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align: center;">No diseases found.</td>
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

<?php
$content = ob_get_clean();
include '../app/views/admin/home.php';
?>

<style>
    .search-form {
        display: flex;
        align-items: center;
        gap: 10px;
        max-width: 400px;
        margin-bottom: 20px;
    }

    .search-input {
        flex: 1;
        padding: 8px 12px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 6px;
        box-sizing: border-box;
    }

    .search-button {
        padding: 8px 16px;
        background-color: #007bff;
        color: white;
        font-size: 14px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .search-button:hover {
        background-color: #0056b3;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
        font-size: 14px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 500;
        transition: background-color 0.3s ease;
    }

    .btn-edit {
        background-color: #28a745;
        color: white;
        border: none;
    }

    .btn-edit:hover {
        background-color: #218838;
    }

    .btn-delete {
        background-color: #dc3545;
        color: white;
        border: none;
        margin-top: 10px;
    }

    .btn-delete:hover {
        background-color: #c82333;
    }
</style>
<script>
    function confirmDeleteDisease(diseaseId, diseaseName) {
        Swal.fire({
            title: 'Are you sure?',
            html: `You are about to delete <b style="color:#d33;">${diseaseName}</b>. This action cannot be undone.`,
            imageUrl: 'assets/images/question_mask.png', // Hoặc để trống nếu không có ảnh
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
                window.location.href = `index.php?route=admin-delete-disease&id=${diseaseId}`;
            }
        });
    }
</script>