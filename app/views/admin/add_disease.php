<?php if (!empty($_SESSION['error'])): ?>
    <p class="error-message"><?= $_SESSION['error'];
                                unset($_SESSION['error']); ?></p>
<?php endif; ?>

<div class="form-container">
    <form method="POST" action="index.php?route=add-disease">
        <label for="name">Disease Name:</label>
        <input type="text" name="name" id="name" required>

        <label for="description">Description:</label>
        <textarea name="description" id="description" rows="4" required></textarea>

        <label for="symptoms">Enter related symptoms:</label>
        <textarea name="symptoms" id="symptoms" rows="4" placeholder="One symptom per line..."></textarea>

        <label for="precautions">Precautions (Tips):</label>
        <textarea name="precautions" rows="3" placeholder="Each tip on a new line..."></textarea>

        <label for="medications">Medications:</label>
        <textarea name="medications" rows="3" placeholder="Each medication on a new line..."></textarea>

        <label for="diets">Diets:</label>
        <textarea name="diets" rows="3" placeholder="Each food/nutrient on a new line..."></textarea>

        <label for="workouts">Workouts:</label>
        <textarea name="workouts" rows="3" placeholder="Each workout on a new line..."></textarea>

        <button type="submit" class="btn-primary">Add Disease</button>
    </form>
</div>

<style>
    .form-container {
        max-width: 600px;
        margin: auto;
        background: #fff;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        font-family: Arial, sans-serif;
    }

    .form-container label {
        display: block;
        font-weight: bold;
        margin-top: 15px;
    }

    .form-container input,
    .form-container textarea,
    .form-container select {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
        box-sizing: border-box;
    }

    .form-container button.btn-primary {
        margin-top: 20px;
        width: 100%;
        padding: 12px;
        background: #007bff;
        color: #fff;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 16px;
    }

    .form-container button.btn-primary:hover {
        background-color: #0056b3;
    }

    .error-message {
        color: red;
        font-weight: bold;
        text-align: center;
        margin-bottom: 15px;
    }
</style>