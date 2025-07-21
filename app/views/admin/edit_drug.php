<?php
if (!isset($drug) || !isset($ingredients)) {
    echo "Dữ liệu thuốc không tồn tại.";
    exit;
}
?>

<h2>Edit Drug</h2>

<form action="index.php?route=admin-update-drug&id=<?= $drug['id'] ?>" method="POST">
    <div>
        <label>Tên thuốc:</label>
        <input type="text" name="ten_thuoc" value="<?= htmlspecialchars($drug['ten_thuoc']) ?>" required>
    </div>

    <div>
        <label>Dạng bào chế:</label>
        <input type="text" name="dang_bao_che" value="<?= htmlspecialchars($drug['dang_bao_che']) ?>" required>
    </div>

    <div>
        <label>Số đăng ký:</label>
        <input type="text" name="so_dang_ky" value="<?= htmlspecialchars($drug['so_dang_ky']) ?>" required>
    </div>

    <div>
        <label>Quy cách:</label>
        <input type="text" name="quy_cach" value="<?= htmlspecialchars($drug['quy_cach']) ?>" required>
    </div>

    <div>
        <label>Hạn sử dụng:</label>
        <input type="text" name="han_su_dung" value="<?= htmlspecialchars($drug['han_su_dung']) ?>" required>
    </div>

    <div>
        <label>URL chi tiết:</label>
        <input type="text" name="url" value="<?= htmlspecialchars($drug['url']) ?>">
    </div>

    <hr>

    <h3>Hoạt chất</h3>
    <div id="hoat-chat-container">
        <?php foreach ($ingredients as $index => $ingredient): ?>
            <div class="hoat-chat-row">
                <input type="text" name="hoat_chat[<?= $index ?>][ten_hoat_chat]" value="<?= htmlspecialchars($ingredient['ten_hoat_chat']) ?>" placeholder="Tên hoạt chất" required>
                <input type="text" name="hoat_chat[<?= $index ?>][ham_luong]" value="<?= htmlspecialchars($ingredient['ham_luong']) ?>" placeholder="Hàm lượng" required>
                <button type="button" onclick="removeHoatChat(this)">X</button>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="button" onclick="addHoatChat()">+ Thêm hoạt chất</button>

    <br><br>
    <button type="submit">Cập nhật thuốc</button>
</form>

<script>
    function addHoatChat() {
        const container = document.getElementById('hoat-chat-container');
        const index = container.children.length;
        const div = document.createElement('div');
        div.className = 'hoat-chat-row';
        div.innerHTML = `
        <input type="text" name="hoat_chat[${index}][ten_hoat_chat]" placeholder="Tên hoạt chất" required>
        <input type="text" name="hoat_chat[${index}][ham_luong]" placeholder="Hàm lượng" required>
        <button type="button" onclick="removeHoatChat(this)">X</button>
    `;
        container.appendChild(div);
    }

    function removeHoatChat(button) {
        button.parentElement.remove();
    }
</script>

<style>
    form div {
        margin-bottom: 10px;
    }

    .hoat-chat-row {
        display: flex;
        gap: 10px;
        margin-bottom: 5px;
    }

    .hoat-chat-row input {
        flex: 1;
    }
</style>