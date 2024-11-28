<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once 'platonusAPI.php';

// Проверяем авторизацию
if (!isset($_SESSION['authToken']) || !isset($_SESSION['sid'])) {
    header('Location: index.php');
    exit;
}

$platonus = new Platonus();
$platonus->authToken = $_SESSION['authToken'];
$platonus->sid = $_SESSION['sid'];

// Обработка действий
$result = null;
$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            $year = $_POST['year'] ?? date('Y');
            $semester = $_POST['semester'] ?? 1;

            if ($_POST['action'] === 'getJournal') {
                $language = $_POST['language'] ?? 'ru';
                $result = $platonus->getJournal($year, $semester, $language);
            } elseif ($_POST['action'] === 'getSubjects') {
                $result = $platonus->getSubjects($year, $semester);
            } elseif ($_POST['action'] === 'getSubject') {
                $subjectID = $_POST['subjectID'];
                $result = $platonus->getSubject($year, $semester, $subjectID);
            }
        }
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
    <title>Platonus Dashboard</title>
</head>
<body>
    <h1>Platonus Dashboard</h1>

    <form method="POST">
        <h3>Get Journal</h3>
        <label for="year">Year:</label>
        <input type="text" id="year" name="year" value="<?php echo date('Y'); ?>"><br><br>

        <label for="semester">Semester:</label>
        <input type="text" id="semester" name="semester" value="1"><br><br>

        <label for="language">Language (ru/kz):</label>
        <input type="text" id="language" name="language" value="ru"><br><br>

        <button type="submit" name="action" value="getJournal">Get Journal</button>
    </form>

    <form method="POST">
        <h3>Get Subjects</h3>
        <label for="year">Year:</label>
        <input type="text" id="year" name="year" value="<?php echo date('Y'); ?>"><br><br>

        <label for="semester">Semester:</label>
        <input type="text" id="semester" name="semester" value="1"><br><br>

        <button type="submit" name="action" value="getSubjects">Get Subjects</button>
    </form>

    <form method="POST">
        <h3>Get Specific Subject</h3>
        <label for="year">Year:</label>
        <input type="text" id="year" name="year" value="<?php echo date('Y'); ?>"><br><br>

        <label for="semester">Semester:</label>
        <input type="text" id="semester" name="semester" value="1"><br><br>

        <label for="subjectID">Subject ID:</label>
        <input type="text" id="subjectID" name="subjectID" required><br><br>

        <button type="submit" name="action" value="getSubject">Get Subject</button>
    </form>

    <?php if ($result): ?>
        <h3>Result:</h3>
        <pre><?php print_r($result); ?></pre>
    <?php endif; ?>

    <?php if ($error): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="logout.php">
        <button type="submit">Logout</button>
    </form>
</body>
</html>
