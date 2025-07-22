<!-- app/views/admin/edit_drug.php -->
<div class="admin-form-container">
    <form action="index.php?route=admin-update-drug" method="POST" class="drug-form">
        <input type="hidden" name="id" value="<?= $drug['id'] ?>">

        <div class="form-group">
            <label class="form-label">Drug Name:</label>
            <input type="text" class="form-input" name="ten_thuoc" value="<?= htmlspecialchars($drug['ten_thuoc']) ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Dosage Form:</label>
            <input type="text" class="form-input" name="dang_bao_che" value="<?= htmlspecialchars($drug['dang_bao_che']) ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Registration Number:</label>
            <input type="text" class="form-input" name="so_dang_ky" value="<?= htmlspecialchars($drug['so_dang_ky']) ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Specification:</label>
            <input type="text" class="form-input" name="quy_cach" value="<?= htmlspecialchars($drug['quy_cach']) ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Expiration Date:</label>
            <input type="date" class="form-input" name="han_su_dung" value="<?= htmlspecialchars($drug['han_su_dung']) ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Details URL:</label>
            <input type="text" class="form-input" name="url" value="<?= htmlspecialchars($drug['url']) ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Active Ingredients (Name - Strength):</label>
            <div id="ingredient-container">
                <?php foreach ($drug['hoat_chat'] as $index => $hc): ?>
                    <div class="ingredient-row">
                        <input type="text" class="form-input" name="active_ingredients[]" value="<?= htmlspecialchars($hc['ten_hoat_chat']) ?>" required placeholder="Name">
                        <input type="text" class="form-input" name="concentrations[]" value="<?= htmlspecialchars($hc['ham_luong']) ?>" required placeholder="Strength">
                        <button type="button" class="delete-ingredient-btn" onclick="removeIngredient(this)">X</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="add-ingredient-btn" onclick="addIngredient()">+ Thêm hoạt chất</button>
        </div>

        <div class="form-actions">
            <button type="submit" class="form-button">Update Drug</button>
        </div>
    </form>
</div>
<script>
    function addIngredient() {
        const container = document.getElementById('ingredient-container');
        const row = document.createElement('div');
        row.className = 'ingredient-row';
        row.innerHTML = `
        <input type="text" class="form-input" name="active_ingredients[]" required placeholder="Name">
        <input type="text" class="form-input" name="concentrations[]" required placeholder="Strength">
        <button type="button" class="delete-ingredient-btn" onclick="removeIngredient(this)">X</button>
    `;
        container.appendChild(row);
    }

    function removeIngredient(button) {
        const row = button.parentNode;
        row.remove();
    }
</script>

<style>
    .ingredient-row {
        display: flex;
        gap: 10px;
        margin-bottom: 8px;
        align-items: center;
    }

    .delete-ingredient-btn {
        background-color: #d33;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 6px;
        cursor: pointer;
    }

    .delete-ingredient-btn:hover {
        background-color: #b22;
    }

    .admin-form-container {
        max-width: 700px;
        margin: 40px auto;
        padding: 30px;
        background-color: #fefefe;
        border: 1px solid #ddd;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        font-family: 'Segoe UI', sans-serif;
    }

    .drug-form .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: #333;
    }

    .form-input {
        width: 100%;
        padding: 10px 12px;
        font-size: 15px;
        border: 1px solid #ccc;
        border-radius: 6px;
        transition: border 0.3s;
    }

    .form-input:focus {
        border-color: #007bff;
        outline: none;
    }

    .ingredient-row {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
    }

    .form-actions {
        text-align: center;
        margin-top: 30px;
    }

    .form-button {
        padding: 10px 24px;
        font-size: 16px;
        background-color: #007bff;
        border: none;
        color: white;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .form-button:hover {
        background-color: #0056b3;
    }
</style>