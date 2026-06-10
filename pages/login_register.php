<?php

require __DIR__ . '/../scripts/php/config/bootstrap.php';

$error = $_GET['error'] ?? '';
$success = $_GET['success'] ?? '';
$form = $_GET['form'] ?? 'login';

$messages = [
    'empty' => 'Please fill in all fields.',
    'invalid' => 'Invalid login credentials.',
    'exists' => 'This email address is already registered.',
    'email' => 'Please enter a valid email address.'
];

$successMessages = [
    'registered' => 'Account created. You can now log in.'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Register | The Luxury Exchange</title>
    <link rel="stylesheet" href="../css/fonts.css">
    <link rel="stylesheet" href="../css/login_register.css">
    <script src="../scripts/javascript/loginregister.js"></script>
</head>
<body>

<div class="container">

    <?php if ($success && isset($successMessages[$success])): ?>
        <div class="notice success"><?= escape($successMessages[$success]) ?></div>
    <?php endif; ?>

    <form action="../scripts/php/login.php" method="post" class="form-box <?= $form === 'login' ? 'active' : '' ?>" id="login">
        <h1>Login</h1>

        <?php if ($error && $form === 'login' && isset($messages[$error])): ?>
            <div class="notice error"><?= escape($messages[$error]) ?></div>
        <?php endif; ?>

        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
        <p>Don't have an account yet? <a href="#" onclick="showForm('register'); return false;">Click here</a></p>
    </form>

    <form action="../scripts/php/register.php" method="post" class="form-box <?= $form === 'register' ? 'active' : '' ?>" id="register">
        <h1>Register</h1>

        <?php if ($error && $form === 'register' && isset($messages[$error])): ?>
            <div class="notice error"><?= escape($messages[$error]) ?></div>
        <?php endif; ?>

        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required minlength="6">

        <h3>Choose an avatar</h3>
        <div class="avatar-choose">
            <label>
                <input type="radio" name="avatar" value="avatar1.png" checked> Avatar 1
            </label>
            <label>
                <input type="radio" name="avatar" value="avatar2.png"> Avatar 2
            </label>
            <label>
                <input type="radio" name="avatar" value="avatar3.png"> Avatar 3
            </label>
        </div>

        <button type="submit">Register</button>
        <p>Already have an account? <a href="#" onclick="showForm('login'); return false;">Click here</a></p>
    </form>

</div>

</body>
</html>
