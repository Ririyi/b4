<?php
// Данные доступа к базе InfinityFree
$host = 'sql101.infinityfree.com';  // MySQL Hostname
$db   = 'if0_39759967_riyi';               // Database Name
$user = 'if0_39759967';             // Username
$pass = '5Qc1kmacyBr';         // Password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // показывать ошибки
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // возвращать ассоциативный массив
    PDO::ATTR_EMULATE_PREPARES   => false,                  // реальные prepared statements
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

$stmt = $pdo->query("SELECT DATABASE()");
$row = $stmt->fetch();
echo "Подключено к базе: " . $row['DATABASE()'];

// ===== Получаем данные формы =====
$fio = trim($_POST['fio'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$birth_date = $_POST['birth_date'] ?? '';
$gender = $_POST['gender'] ?? '';
$languages = $_POST['languages'] ?? [];
$biography = trim($_POST['biography'] ?? '');
$agreed = isset($_POST['agreed']) ? 1 : 0;

// ===== Массивы для ошибок и значений =====
$errors = [];
$values = [
    'fio' => $fio,
    'phone' => $phone,
    'email' => $email,
    'birth_date' => $birth_date,
    'gender' => $gender,
    'languages' => $languages,
    'biography' => $biography,
    'agreed' => $agreed
];

// ===== Валидация регулярками =====
if (!preg_match("/^[a-zA-Zа-яА-ЯёЁ\s]{1,150}$/u", $fio)) {
    $errors['fio'] = "ФИО: только буквы и пробелы, максимум 150 символов.";
}
if (!preg_match("/^\+?[0-9\-\(\)\s]{5,20}$/", $phone)) {
    $errors['phone'] = "Телефон: только цифры, +, -, (), длина 5-20 символов.";
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = "Некорректный email.";
}
if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $birth_date)) {
    $errors['birth_date'] = "Дата рождения должна быть в формате YYYY-MM-DD.";
}
if (!in_array($gender, ['male','female'])) {
    $errors['gender'] = "Выберите допустимый пол.";
}
if (empty($languages)) {
    $errors['languages'] = "Выберите хотя бы один язык программирования.";
}
if (!$agreed) {
    $errors['agreed'] = "Необходимо согласие с контрактом.";
}

// ===== Если есть ошибки, сохраняем их в cookies и редирект на форму =====
if (!empty($errors)) {
    setcookie('errors', json_encode($errors), 0, "/"); // до конца сессии
    setcookie('values', json_encode($values), 0, "/"); 
    header("Location: index.php"); // перезагрузка формы методом GET
    exit;
}

// ===== Если ошибок нет, сохраняем значения в cookies на 1 год =====
setcookie('values', json_encode($values), time() + 604800, "/"); // 1 неделя

// ===== Сохраняем заявку в БД =====
$stmt = $pdo->prepare("INSERT INTO application 
    (name, phone, email, birth_date, gender, biography, agreed) 
    VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([$fio, $phone, $email, $birth_date, $gender, $biography, $agreed]);

$appId = $pdo->lastInsertId();

// ===== Сохраняем выбранные языки =====
$stmtLang = $pdo->prepare("INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)");
foreach ($languages as $langId) {
    $stmtLang->execute([$appId, (int)$langId]);
}

// ===== Вывод успешного сообщения =====
echo "<h3>Данные успешно сохранены!</h3>";
echo "<p>Ваш ID заявки: " . htmlspecialchars($appId) . "</p>";
echo "<a href='index.php'>Заполнить снова</a>";
?>
