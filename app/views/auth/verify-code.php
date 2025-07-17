<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Enter Verification Code</title>
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Merriweather', serif;
            background: linear-gradient(to right, #e0f7fa, #ffffff);
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login {
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
            width: 420px;
            max-width: 90%;
            position: relative;
        }

        .login h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #F95454;
        }

        .form-group {
            position: relative;
            margin-bottom: 28px;
        }

        .form-group input {
            height: 60px;
            width: 100%;
            outline: none;
            border: 1px solid #aaa;
            padding: 10px;
            border-radius: 5px;
            font-size: 15px;
            padding-top: 20px;
            font-family: 'Merriweather', serif;
        }

        .form-group input:focus {
            border: 2px solid #1e90ff;
        }

        .form-group label {
            position: absolute;
            padding: 0px 5px;
            left: 10px;
            top: 50%;
            pointer-events: none;
            transform: translateY(-50%);
            font-size: 16px;
            color: #777;
            background-color: white;
            transition: all 0.3s ease-in-out;
        }

        .form-group input:focus+label,
        .form-group input:valid+label {
            top: 13px;
            font-size: 15px;
            font-weight: 500;
            color: #1e90ff;
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: #1e90ff;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0d6efd;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            transform: scale(1.02);
        }

        .resend {
            text-align: center;
            margin-top: 18px;
        }

        .resend a {
            color: #1e90ff;
            text-decoration: underline;
            font-size: 0.95rem;
        }

        .message {
            margin-bottom: 15px;
            font-weight: bold;
            text-align: center;
            padding: 8px 12px;
            border-radius: 6px;
        }

        .message.error {
            background-color: #ffe5e5;
            color: #d8000c;
        }

        .message.success {
            background-color: #e0f9e0;
            color: #2e7d32;
        }
    </style>
</head>

<body>

    <div class="login">
        <?php if (!empty($_SESSION['error'])): ?>
            <p class="message error"><?= $_SESSION['error'];
                                        unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <?php if (!empty($_SESSION['success'])): ?>
            <p class="message success"><?= $_SESSION['success'];
                                        unset($_SESSION['success']); ?></p>
        <?php endif; ?>

        <h2>Enter Verification Code</h2>

        <form method="POST" action="index.php?route=verifyResetCode">
            <div class="form-group">
                <input type="text" name="code" id="code" required />
                <label for="code">Verification Code</label>
            </div>
            <button type="submit">Confirm</button>
        </form>

        <div class="resend">
            <a href="index.php?route=resend-verification-code">
                Didn't receive the code? Resend verification code
            </a>
        </div>
    </div>

</body>

</html>