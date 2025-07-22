<?php if (!empty($_SESSION['error'])): ?>
    <p style="color:red"><?= $_SESSION['error'];
                            unset($_SESSION['error']); ?></p>
<?php endif; ?>

<form method="POST" action="index.php?route=admin-store-drug" class="add-drug-form">
    <label>Drug Name:</label><br>
    <input type="text" name="ten_thuoc" class="add-drug-input" required><br><br>

    <label>Dosage Form:</label><br>
    <input type="text" name="dang_bao_che" class="add-drug-input"><br><br>

    <label>Registration Number:</label><br>
    <input type="text" name="so_dang_ky" class="add-drug-input"><br><br>

    <label>Specification:</label><br>
    <input type="text" name="quy_cach" class="add-drug-input"><br><br>

    <label>Expiry Date:</label><br>
    <input type="text" name="han_su_dung" placeholder="e.g. 12/2026" class="add-drug-input"><br><br>

    <label>Drug Detail URL:</label><br>
    <input type="url" name="url" placeholder="https://..." class="add-drug-input"><br><br>

    <label>Active Ingredients:</label>
    <div id="active-ingredients">
        <div class="ingredient-group">
            <input type="text" name="hoat_chat[0][ten]" placeholder="Ingredient name (e.g. Paracetamol)" class="add-drug-input">
            <input type="text" name="hoat_chat[0][ham_luong]" placeholder="Strength (e.g. 500mg)" class="add-drug-input">
            <button type="button" class="delete-ingredient-btn" onclick="removeIngredient(this)">X</button>
        </div>
    </div>
    <button type="button" class="add-ingredient-btn" onclick="addIngredient()">+ Add Ingredient</button><br><br>

    <button type="submit" class="submit-drug-btn">Save Drug</button>
</form>

<style>
    .ingredient-group {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-bottom: 10px;
    }

    .delete-ingredient-btn {
        background-color: #e74c3c;
        color: white;
        border: none;
        padding: 5px 10px;
        cursor: pointer;
        border-radius: 5px;
    }

    .delete-ingredient-btn:hover {
        background-color: #c0392b;
    }
</style>

<script>
    let ingredientIndex = 1;

    function addIngredient() {
        const container = document.getElementById('active-ingredients');
        const div = document.createElement('div');
        div.className = 'ingredient-group';
        div.innerHTML = `
            <input type="text" name="hoat_chat[${ingredientIndex}][ten]" placeholder="Ingredient name (e.g. Paracetamol)" class="add-drug-input" required>
            <input type="text" name="hoat_chat[${ingredientIndex}][ham_luong]" placeholder="Strength (e.g. 500mg)" class="add-drug-input" required>
            <button type="button" class="delete-ingredient-btn" onclick="removeIngredient(this)">X</button>
        `;
        container.appendChild(div);
        ingredientIndex++;
    }

    function removeIngredient(button) {
        button.parentNode.remove();
    }
</script>