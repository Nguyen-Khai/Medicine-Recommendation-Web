<?php
if (!isset($_SESSION['admin'])) {
  header("Location: index.php?route=login");
  exit();
}

require_once '../app/models/AdminModel.php';
$adminModel = new AdminModel();
$adminInfo = $adminModel->getAdminById($_SESSION['admin']['id']); // lấy avatar từ DB
$avatar = $adminInfo['avatar'] ?? null;
$avatarSrc = $avatar ? 'data:image/png;base64,' . base64_encode($avatar) : 'assets/images/default-avatar.png';
$logModel = new AdminModel();
$logs = $logModel->getLogsByAdminId($_SESSION['admin']['id']);
?>
<?php
$language = $_SESSION['admin']['language'] ?? 'en';
$langPath = __DIR__ . '/../../../lang/' . $language . '.php'; // cập nhật đường dẫn đúng

if (file_exists($langPath)) {
  $lang = include $langPath;
} else {
  $lang = include __DIR__ . '/../../../lang/en.php';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Admin Settings - HEALMATE</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Inter', sans-serif;
    }

    body {
      background: #f5f7fa;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 1200px;
      margin: 40px auto;
      background: white;
      border-radius: 16px;
      display: flex;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
      overflow: hidden;
    }

    .sidebar {
      width: 300px;
      padding: 30px;
      background: #ffffff;
      border-right: 1px solid #ddd;
    }

    .main-content {
      flex: 1;
      padding: 30px;
    }

    .back-link {
      display: inline-block;
      margin-bottom: 20px;
      color: #2e7dff;
      text-decoration: none;
      font-size: 14px;
    }

    .settings-header {
      display: flex;
      align-items: center;
      gap: 16px;
      margin-bottom: 30px;
    }

    .avatar {
      width: 70px;
      height: 70px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #ccc;
    }

    .admin-name {
      font-size: 22px;
      font-weight: 600;
      color: #333;
    }

    .section-title {
      font-size: 18px;
      font-weight: 600;
      color: #2c3e50;
      border-left: 4px solid #2e7dff;
      padding-left: 10px;
      margin: 30px 0 10px;
    }

    .settings-list {
      list-style: none;
      padding: 0;
    }

    .settings-list li {
      margin: 8px 0;
    }

    .settings-list a {
      text-decoration: none;
      color: #2e7dff;
      font-size: 16px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
      cursor: pointer;
    }

    .settings-list a:hover {
      color: #1c54b2;
    }

    .tab-content {
      display: none;
    }

    .tab-content.active {
      display: block;
    }

    .form-group {
      margin-bottom: 20px;
    }

    label {
      font-weight: 500;
      display: block;
      margin-bottom: 6px;
    }

    input,
    select {
      width: 100%;
      padding: 10px;
      font-size: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    button {
      padding: 10px 18px;
      background: #2e7dff;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 16px;
    }

    button:hover {
      background: #1c54b2;
    }

    @media screen and (max-width: 768px) {
      .container {
        flex-direction: column;
      }

      .sidebar {
        width: 100%;
        border-right: none;
        border-bottom: 1px solid #ddd;
      }
    }

    /* Edit Info */
    .edit-info-container {
      max-width: 600px;
      margin: 0 auto;
      padding: 32px;
      border-radius: 12px;
    }

    .edit-info-container h2 {
      font-size: 24px;
      margin-bottom: 20px;
      font-weight: bold;
      text-align: center;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
    }

    .form-input {
      width: 100%;
      padding: 10px 14px;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 8px;
      transition: border-color 0.3s;
    }

    .form-input:focus {
      border-color: #007bff;
      outline: none;
    }

    .btn-save {
      width: 100%;
      padding: 12px;
      font-size: 16px;
      background-color: #007bff;
      color: white;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .btn-save:hover {
      background-color: #0056b3;
    }

    .alert {
      padding: 12px 16px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-weight: 500;
    }

    .alert-success {
      background-color: #d4edda;
      color: #155724;
    }

    .alert-info {
      background-color: #d1ecf1;
      color: #0c5460;
    }

    /* Change password */
    .change-password-container {
      max-width: 600px;
      margin: 0 auto;
      padding: 32px;
      border-radius: 12px;
    }

    .change-password-container h2 {
      font-size: 24px;
      margin-bottom: 20px;
      font-weight: bold;
      text-align: center;
    }

    .alert-error {
      background-color: #f8d7da;
      color: #721c24;
      padding: 12px 16px;
      border-radius: 8px;
      margin-bottom: 20px;
      font-weight: 500;
    }

    /* Change avatar */
    .avatar-upload-container {
      max-width: 400px;
      margin: 30px auto;
      padding: 20px;
      border: 2px dashed #ccc;
      border-radius: 12px;
      background-color: #f9f9f9;
      text-align: center;
      font-family: 'Inter', sans-serif;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .avatar-upload-container h2 {
      font-size: 1.5rem;
      margin-bottom: 20px;
      color: #333;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      font-weight: 500;
      margin-bottom: 10px;
      color: #555;
    }

    .form-group input[type="file"] {
      border: 1px solid #ccc;
      padding: 8px;
      width: 100%;
      cursor: pointer;
      border-radius: 6px;
      background-color: #fff;
    }

    .upload-btn {
      padding: 10px 20px;
      background-color: #0d6efd;
      color: white;
      border: none;
      border-radius: 6px;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .upload-btn:hover {
      background-color: #0b5ed7;
    }

    #preview-container {
      margin-top: 20px;
      text-align: center;
    }

    #avatar-preview {
      max-width: 150px;
      max-height: 150px;
      border-radius: 50%;
      object-fit: cover;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      margin: 20px;
      position: relative;
      left: 79px;
    }

    /* Admin logs */
    .admin-logs-container {
      margin-top: 30px;
      padding: 20px;
      background-color: #fff;
      border-radius: 8px;
    }

    .admin-logs-table {
      width: 100%;
      border-collapse: collapse;
    }

    .admin-logs-table th,
    .admin-logs-table td {
      padding: 10px;
      border-bottom: 1px solid #ccc;
      text-align: left;
    }

    .admin-logs-table .truncate {
      max-width: 300px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .alert.alert-info {
      background-color: #eef5ff;
      color: #333;
      padding: 10px;
      margin-top: 10px;
      border-left: 4px solid #6495ed;
    }
  </style>
</head>

<body>

  <div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
      <a href="index.php?route=admin-dashboard" class="back-link"><i class="fa fa-arrow-left"></i> Back to Dashboard</a>

      <div class="settings-header">
        <img src="<?= $avatarSrc ?>" alt="Avatar" class="avatar">
        <div class="admin-name"><?= isset($_SESSION['admin']['name']) ? htmlspecialchars($_SESSION['admin']['name']) : 'Admin' ?></div>
      </div>

      <div class="section-title">Profile</div>
      <ul class="settings-list">
        <li><a data-section="personal-info"><i class="fa fa-user-edit"></i> Edit Info</a></li>
        <li><a data-section="password"><i class="fa fa-lock"></i> Change Password</a></li>
        <li><a data-section="avatar"><i class="fa fa-image"></i> Upload Avatar</a></li>
      </ul>

      <div class="section-title">Preferences</div>
      <ul class="settings-list">
        <li><a data-section="language"><i class="fa fa-globe"></i> Language</a></li>
      </ul>

      <div class="section-title">System</div>
      <ul class="settings-list">
        <li><a data-section="admin-logs"><i class="fa fa-history"></i> Admin Logs</a></li>
      </ul>
    </div>

    <!-- Content Area -->
    <div class="main-content">
      <!-- Personal Info -->
      <div class="tab-content active" id="personal-info">
        <div class="edit-info-container">
          <h2>Edit Personal Information</h2>

          <?php if (!empty($_SESSION['admin_success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['admin_success']) ?></div>
            <?php unset($_SESSION['admin_success']); ?>
          <?php endif; ?>

          <?php if (!empty($_SESSION['admin_info'])): ?>
            <div class="alert alert-info"><?= htmlspecialchars($_SESSION['admin_info']) ?></div>
            <?php unset($_SESSION['admin_info']); ?>
          <?php endif; ?>

          <form method="POST" action="index.php?route=admin-settings/edit-info">
            <div class="form-group">
              <label for="name">Full name:</label>
              <input type="text" id="name" name="name" class="form-input"
                value="<?= isset($_SESSION['admin']['name']) ? htmlspecialchars($_SESSION['admin']['name']) : 'Admin' ?>" required>
            </div>

            <div class="form-group">
              <label for="email">Email:</label>
              <input type="email" id="email" name="email" class="form-input"
                value="<?= htmlspecialchars($_SESSION['admin']['email']) ?>" required>
            </div>

            <button type="submit" class="btn-save">Save Changes</button>
          </form>
        </div>
      </div>

      <!-- Change Password -->
      <div class="tab-content" id="password">
        <div class="change-password-container">
          <h2>Change Password</h2>

          <?php if (!empty($_SESSION['password_error'])): ?>
            <div class="alert alert-error"><?= htmlspecialchars($_SESSION['password_error']) ?></div>
            <?php unset($_SESSION['password_error']); ?>
          <?php endif; ?>

          <?php if (!empty($_SESSION['password_success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['password_success']) ?></div>
            <?php unset($_SESSION['password_success']); ?>
          <?php endif; ?>

          <form method="POST" action="index.php?route=admin-settings/change-password">
            <div class="form-group">
              <label for="current_password">Current Password</label>
              <input type="password" id="current_password" name="current_password" class="form-input" required>
            </div>

            <div class="form-group">
              <label for="new_password">New Password</label>
              <input type="password" id="new_password" name="new_password" class="form-input" required>
            </div>

            <div class="form-group">
              <label for="confirm_password">Confirm New Password</label>
              <input type="password" id="confirm_password" name="confirm_password" class="form-input" required>
            </div>

            <button type="submit" class="btn-save">Update Password</button>
          </form>
        </div>
      </div>

      <!-- Avatar Upload -->
      <div class="tab-content" id="avatar">
        <div class="avatar-upload-container">
          <h2>Upload New Avatar</h2>

          <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
              <?= $_SESSION['success'];
              unset($_SESSION['success']); ?>
            </div>
          <?php endif; ?>

          <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
              <?= $_SESSION['error'];
              unset($_SESSION['error']); ?>
            </div>
          <?php endif; ?>
          <form method="POST" action="index.php?route=admin-settings/upload-avatar" enctype="multipart/form-data">
            <div class="form-group">
              <label for="avatar">Choose Image</label>
              <input type="file" name="avatar" id="avatarInput" accept="image/*">
            </div>
            <div id="preview-container">
              <img id="avatar-preview" src="#" alt="Preview" style="display:none;" />
            </div>
            <button type="submit" class="upload-btn">Upload</button>
          </form>
        </div>
      </div>

      <!-- Language -->
      <div class="tab-content" id="language">
        <h2><?= $lang['language_region'] ?></h2>
        <form method="POST" action="index.php?route=admin-settings/language">
          <div class="form-group">
            <label><?= $lang['choose_language'] ?></label>
            <select name="language">
              <option value="en" <?= ($_SESSION['admin']['language'] ?? '') === 'en' ? 'selected' : '' ?>>English</option>
              <option value="vi" <?= ($_SESSION['admin']['language'] ?? '') === 'vi' ? 'selected' : '' ?>>Tiếng Việt</option>
            </select>
          </div>
          <button type="submit"><?= $lang['save'] ?></button>
        </form>
      </div>

      <!-- Admin logs -->
      <div class="tab-content" id="admin-logs">
        <div class="admin-logs-container">
          <h2>Admin Activity Logs</h2>

          <?php if (!empty($logs)): ?>
            <table class="admin-logs-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Action</th>
                  <th>IP Address</th>
                  <th>User Agent</th>
                  <th>Time</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($logs as $index => $log): ?>
                  <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($log['action']) ?></td>
                    <td><?= htmlspecialchars($log['ip_address']) ?></td>
                    <td class="truncate"><?= htmlspecialchars($log['user_agent']) ?></td>
                    <td><?= date('Y-m-d H:i:s', strtotime($log['created_at'])) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <div class="alert alert-info">No logs available.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- JS -->
    <script>
      const links = document.querySelectorAll('[data-section]');
      const sections = document.querySelectorAll('.tab-content');

      links.forEach(link => {
        link.addEventListener('click', () => {
          const sectionId = link.dataset.section;

          // Hiển thị đúng tab nội dung
          sections.forEach(sec => sec.classList.remove('active'));
          document.getElementById(sectionId).classList.add('active');

          // Đổi trạng thái active cho tab menu
          links.forEach(l => l.classList.remove('active'));
          link.classList.add('active');

          // Cập nhật URL không reload
          const newUrl = new URL(window.location.href);
          newUrl.searchParams.set('tab', sectionId);
          window.history.replaceState(null, '', newUrl);
        });
      });

      // Khi tải trang, mở tab theo URL ?tab=
      window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tab = urlParams.get('tab');

        if (tab) {
          const targetSection = document.getElementById(tab);
          if (targetSection) {
            sections.forEach(sec => sec.classList.remove('active'));
            targetSection.classList.add('active');

            // Đặt active cho nút tab
            links.forEach(link => {
              link.classList.toggle('active', link.dataset.section === tab);
            });
          }
        }
      });

      const avatarInput = document.getElementById('avatarInput');
      const avatarPreview = document.getElementById('avatar-preview');

      avatarInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
          const reader = new FileReader();
          reader.onload = function(e) {
            avatarPreview.src = e.target.result;
            avatarPreview.style.display = 'block';
          };
          reader.readAsDataURL(file);
        } else {
          avatarPreview.src = '#';
          avatarPreview.style.display = 'none';
        }
      });
    </script>
</body>

</html>