<?php
$title = "Disease Management";
ob_start();
?>

<div class="admin-diseases-container">

    <form method="GET" action="index.php">
        <input type="hidden" name="route" value="admin-diseases">
        <input type="text" name="keyword" placeholder="Tìm theo tên bệnh" value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
        <button type="submit">Tìm kiếm</button>
    </form>


    <a href="index.php?route=admin-add-disease" class="add-btn">+ Add New Disease</a>

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
                                <a href="index.php?route=admin-edit-disease&id=<?= $d['id'] ?>" class="edit-btn">Edit</a>
                                <?php if ($_SESSION['admin']['role'] === 'superadmin'): ?>
                                    <a href="index.php?route=admin-delete-disease&id=<?= $d['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
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