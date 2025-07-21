<form method="POST" class="edit-form">
    <label for="username">Username:</label>
    <input type="text" name="username" value="<?= htmlspecialchars($admin['username']) ?>" required>

    <label for="email">Email:</label>
    <input type="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required>

    <label for="role">Role:</label>
    <select name="role" <?= $_SESSION['admin']['role'] !== 'superadmin' ? 'disabled' : '' ?>>
        <option value="superadmin" <?= $admin['role'] === 'superadmin' ? 'selected' : '' ?>>Superadmin</option>
        <option value="manager" <?= $admin['role'] === 'manager' ? 'selected' : '' ?>>Manager</option>
        <option value="staff" <?= $admin['role'] === 'staff' ? 'selected' : '' ?>>Staff</option>
    </select>

    <br><br>
    <button type="submit">Update</button>
    <a href="index.php?route=admins"><button type="button">Back</button></a>
</form>

<style>
    .edit-form {
        max-width: 500px;
        padding: 20px;
        background: #f9f9f9;
        border-radius: 12px;
    }

    .edit-form label {
        font-weight: bold;
        display: block;
        margin-top: 10px;
    }

    .edit-form input, .edit-form select {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 8px;
    }

    .edit-form button {
        padding: 10px 18px;
        border: none;
        border-radius: 8px;
        margin-top: 15px;
        background-color: #2563eb;
        color: white;
        cursor: pointer;
    }

    .edit-form button[type="button"] {
        background-color: gray;
        margin-left: 10px;
    }
</style>
