<?php
// Данные для подключения к базе данных
$host = "wtechfiit.postgres.database.azure.com";
$port = "5432";
$dbname = "wteh";
$user = "WTECH2025";
$password = "DimaMaks@"; // Замените на ваш настоящий пароль

// Строка подключения
$connStr = "host=$host port=$port dbname=$dbname user=$user password=$password";

// Подключение к базе данных
$conn = pg_connect($connStr);

if (!$conn) {
    die("Ошибка подключения к базе данных");
}

// Получение данных из формы
$name = $_POST['name'];
$surname = $_POST['surname'];
$email = $_POST['email'];
$gender = $_POST['gender'];
$password_plain = $_POST['password'];
$confirm_password = $_POST['confirm-password'];

// Проверка совпадения паролей
if ($password_plain !== $confirm_password) {
    die("Пароли не совпадают!");
}

// Хэшируем пароль перед вставкой
$password_hashed = password_hash($password_plain, PASSWORD_BCRYPT);

// SQL-запрос на вставку
$query = "INSERT INTO users (email, gender, name, password, surname) 
          VALUES ($1, $2, $3, $4, $5)";

// Выполнение запроса с параметрами
$result = pg_query_params($conn, $query, array($email, $gender, $name, $password_hashed, $surname));

if ($result) {
    echo "Пользователь успешно добавлен!";
} else {
    echo "Ошибка при добавлении пользователя: " . pg_last_error($conn);
}

// Закрытие соединения
pg_close($conn);
?>
