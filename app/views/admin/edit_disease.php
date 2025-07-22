<form action="index.php?route=admin-update-disease" method="POST" class="edit-disease-form">
    <input type="hidden" name="id" value="<?= htmlspecialchars($disease['id']) ?>">

    <div class="form-group">
        <label for="disease">Disease Name:</label>
        <input type="text" id="disease" name="disease" value="<?= htmlspecialchars($disease['disease']) ?>" required>
    </div>

    <div class="form-group">
        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($disease['description']) ?></textarea>
    </div>

    <div class="form-group">
        <label for="symptoms">Related Symptoms (one per line):</label>
        <textarea id="symptoms" name="symptoms" rows="4"><?php
                                                            if (!empty($symptoms)) {
                                                                foreach ($symptoms as $symptom) {
                                                                    echo htmlspecialchars($symptom['symptom']) . "\n";
                                                                }
                                                            }
                                                            ?></textarea>
    </div>

    <div class="form-group">
        <label for="diets">Diets:</label>
        <textarea id="diets" name="diets" rows="2"><?= implode("\n", array_column($diets, 'diet')) ?></textarea>
    </div>

    <div class="form-group">
        <label for="medications">Medications:</label>
        <textarea id="medications" name="medications" rows="2"><?= implode("\n", array_column($medications, 'medication')) ?></textarea>
    </div>

    <div class="form-group">
        <label for="precautions">Precautions:</label>
        <textarea id="precautions" name="precautions" rows="2"><?= implode("\n", array_column($precautions, 'precaution')) ?></textarea>
    </div>

    <div class="form-group">
        <label for="workouts">Workouts:</label>
        <textarea id="workouts" name="workouts" rows="2"><?= implode("\n", array_column($workouts, 'workout')) ?></textarea>
    </div>

    <div class="form-actions">
        <button type="submit">Update Disease</button>
    </div>
</form>

<style>
    .edit-disease-form {
        max-width: 720px;
        margin: 40px auto;
        padding: 30px;
        background: #fefefe;
        border-radius: 12px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
        font-family: "Segoe UI", Tahoma, sans-serif;
    }

    .edit-disease-form h2 {
        text-align: center;
        font-size: 24px;
        margin-bottom: 20px;
        color: #1f2937;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        font-weight: 600;
        display: block;
        margin-bottom: 6px;
        color: #374151;
    }

    .form-group input[type="text"],
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 15px;
        background-color: #f9fafb;
        transition: 0.2s border-color ease-in-out;
    }

    .form-group textarea {
        resize: vertical;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        border-color: #3b82f6;
        outline: none;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }

    .form-actions {
        text-align: center;
        margin-top: 30px;
    }

    button[type="submit"] {
        padding: 10px 24px;
        font-size: 16px;
        background-color: #007bff;
        border: none;
        color: white;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button[type="submit"]:hover {
        background: #2563eb;
    }
</style>