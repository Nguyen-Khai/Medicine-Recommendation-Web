<?php
$title = "Diagnosis History";
ob_start();
?>

<div class="admin-diagnosis-container">
    <form method="GET" action="" class="filter-form">
        <input type="hidden" name="route" value="admin-diagnosis">

        <input type="text" name="keyword" placeholder="Search symptoms or disease name..." value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">

        <select name="user_id">
            <option value="">-- All users --</option>
            <?php foreach ($allUsers as $user) : ?>
                <option value="<?= $user['id'] ?>" <?= (($_GET['user_id'] ?? '') == $user['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($user['username']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <div class="date-range">
            <label for="from_date">From date:</label>
            <input type="date" id="from_date" name="from_date" value="<?= $_GET['from_date'] ?? '' ?>">

            <label for="to_date">To date:</label>
            <input type="date" id="to_date" name="to_date" value="<?= $_GET['to_date'] ?? '' ?>">
        </div>

        <button type="submit">Filter</button>
    </form>

    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Symptoms</th>
                    <th>Disease Name</th>
                    <th>Date & Time</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($diagnoses)) : ?>
                    <?php foreach ($diagnoses as $item) : ?>
                        <tr>
                            <td><?= $item['id'] ?></td>
                            <td><?= htmlspecialchars($item['username']) ?></td>
                            <td><?= htmlspecialchars($item['symptoms']) ?></td>
                            <td><?= htmlspecialchars($item['predicted_disease']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($item['created_at'])) ?></td>
                            <td><a href="?route=admin-diagnosis-detail&id=<?= $item['id'] ?>" class="view-link">View</a></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6" style="text-align:center;">No matching data found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"
                    class="<?= ($i == $currentPage) ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>
