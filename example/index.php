<?php
session_start();

if (isset($_SESSION['authToken']) && isset($_SESSION['sid'])) {
    header('Location: dashboard.php');
    exit;
}

require_once 'platonusAPI.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];

    try {
        $platonus = new Platonus();
        $platonus->login($login, $password);

        // Сохраняем токены в сессии
        $_SESSION['authToken'] = $platonus->authToken;
        $_SESSION['sid'] = $platonus->sid;

        header('Location: dashboard.php');
        exit;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Platonus Login</title>
</head>
<body>
    <h1>Login to Platonus</h1>
    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST">
        <label for="login">Url:</label>
        <input type="text" id="url" name="url"><br><br>
        
        <label for="login">Login:</label>
        <input type="text" id="login" name="login" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>
