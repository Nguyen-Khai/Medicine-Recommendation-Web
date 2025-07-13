<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Forgot password</title>
  <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Merriweather&display=swap">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter';
      background: linear-gradient(270deg,
          rgba(119, 205, 255, 1) 0%,
          rgba(174, 225, 254, 1) 33%,
          rgba(191, 231, 254, 1) 63%,
          rgba(226, 243, 253, 1) 100%);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .main-header .logo {
      font-size: 24px;
      font-weight: bold;
      color: #ffffff;
      position: relative;
      float: left;
    }

    img.logo {
      width: 60px;
      position: absolute;
      top: -158px;
      left: 20px;
    }

    h1.logo {
      position: absolute;
      left: 84px;
      top: -148px;
      color: #F95454;
    }

    .ng-nhp {
      text-align: center;
      position: relative;
      width: 100%;
    }

    .container-forms {
      display: flex;
      justify-content: center;
      align-items: flex-start;
      position: relative;
      width: 100%;
    }

    .form-section {
      display: none;
      justify-content: center;
      align-items: center;
      min-height: 300px;
      width: 100%;
    }

    .form-section form {
      border: 1px solid black;
      border-radius: 5px;
      padding: 60px;
      background-color: rgba(255, 255, 255, 0.9);
    }

    .login {
      margin-bottom: 30px;
      position: relative;
    }

    input {
      height: 70px;
      width: 400px;
      outline: none;
      border: 1px solid black;
      padding: 10px;
      border-radius: 5px;
      font-size: 18px;
      padding-top: 20px;
    }

    label {
      position: absolute;
      padding: 0px 5px;
      left: 10px;
      top: 50%;
      pointer-events: none;
      transform: translateY(-50%);
      font-size: 16px;
      background-color: transparent;
      transition: all 0.3s ease-in-out;
    }

    .login input:focus {
      border: 2px solid #1e90ff;
    }

    .login input:focus+label,
    .login input:valid+label {
      top: 13px;
      font-size: 15px;
      font-weight: 500;
      color: #1e90ff;
    }

    button {
      width: 180px;
      height: 50px;
      background-color: #c62e2e;
      border: none;
      border-radius: 30px;
      color: white;
      font-size: 18px;
      cursor: pointer;
      position: relative;
      top: 15px;
    }

    button:hover {
      background-color: #F95454;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
      transform: scale(1.02);
      transition: all 0.3s ease;
    }

    h1 {
      position: absolute;
      top: -194px;
    }

    h2 {
      position: absolute;
      font-size: 44px;
      color: #F95454;
      top: -126px;
    }

    p {
      position: absolute;
      top: -48px;
      font-weight: bolder;
    }

    img.img_re {
      width: 300px;
      height: 300px;
      left: 129px;
      bottom: -15px;
      position: absolute;
      object-fit: cover;
    }
  </style>
</head>

<body>
  <div class="ng-nhp">
    <div class="container-forms">
      <div class="logo">
        <img class="logo" src="assets/images/logo.png" alt="">
        <h1 class="logo">HealMate</h1>
      </div>
      <h2>Forgot password?</h2>
      <p>Please enter your username and email associated with your account. We will send a confirmation code to your email!</p>
      <div id="register-form">
        <?php if (!empty($_SESSION['error'])): ?>
          <p style="color: #F95454; margin-bottom: 10px; font-weight: bold; position: relative; top: -13px;"><?= $_SESSION['error'];
                                                                                                                            unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <?php if (!empty($_SESSION['success'])): ?>
          <p style="color:green; margin-bottom: 10px; font-weight: bold; position: relative; top: -13px;"><?= $_SESSION['success'];
                                                                                                                        unset($_SESSION['success']); ?></p>
        <?php endif; ?>
        <form action="index.php?route=handle-forgot-password" method="POST">
          <div class="login">
            <input type="text" name="username" required />
            <label>Username</label>
          </div>
          <div class="login">
            <input type="email" name="email" required />
            <label class="email">Email</label>
          </div>
          <button>Send code</button>
        </form>
      </div>
      <img src="assets/images/forgot password.png" class="img_re" />
    </div>
  </div>
</body>

</html>