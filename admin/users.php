<?php require 'check_admin.php'; require 'db.php'; ?>

<?php
// Добавление пользователя
if(isset($_POST['add_user'])){
    $name=$_POST['name'];
    $surname=$_POST['surname'];
    $email=$_POST['email'];
    $phone=$_POST['phone'];
    $password=$_POST['password'];
    $role=$_POST['role'];

    $stmt=$mysqli->prepare("INSERT INTO users (name,surname,email,phone,password,role,created_at) VALUES (?,?,?,?,?,?,NOW())");
    $stmt->bind_param("ssssss",$name,$surname,$email,$phone,$password,$role);
    $stmt->execute();
}

// Редактирование пользователя
if(isset($_POST['edit_user'])){
    $id=$_POST['id'];
    $name=$_POST['name'];
    $surname=$_POST['surname'];
    $email=$_POST['email'];
    $phone=$_POST['phone'];
    $role=$_POST['role'];

    $stmt=$mysqli->prepare("UPDATE users SET name=?, surname=?, email=?, phone=?, role=? WHERE id=?");
    $stmt->bind_param("sssssi",$name,$surname,$email,$phone,$role,$id);
    $stmt->execute();
}

// Удаление пользователя
if(isset($_GET['delete'])){
    $id=$_GET['delete'];
    $stmt=$mysqli->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i",$id);
    $stmt->execute();
}

// Получение пользователей
$result=$mysqli->query("SELECT * FROM users ORDER BY id DESC");
$users=$result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Пользователи</title>
<link rel="stylesheet" href="assets/style.css">
<style>
.user-card {border:1px solid #b30000;padding:10px;margin-bottom:15px;background:#fff0f0; display:flex; justify-content:space-between; align-items:center;}
.user-info {flex-grow:1;}
.user-actions input, .user-actions select {margin-right:5px;margin-bottom:5px;}
</style>
</head>
<body>
<h2>Пользователи</h2>

<h3>Добавить пользователя</h3>
<form method="POST" class="user-form">
<input type="text" name="name" placeholder="Имя" required>
<input type="text" name="surname" placeholder="Фамилия" required>
<input type="email" name="email" placeholder="Email" required>
<input type="text" name="phone" placeholder="Телефон" required>
<input type="text" name="password" placeholder="Пароль" required>
<select name="role">
<option value="user">Пользователь</option>
<option value="admin">Админ</option>
</select>
<button type="submit" name="add_user">Добавить</button>
</form>

<h3>Список пользователей</h3>
<?php foreach($users as $u): ?>
<div class="user-card">
    <div class="user-info">
        <strong><?= htmlspecialchars($u['name']) ?> <?= htmlspecialchars($u['surname']) ?></strong><br>
        Email: <?= htmlspecialchars($u['email']) ?>, Телефон: <?= htmlspecialchars($u['phone']) ?>, Роль: <?= htmlspecialchars($u['role']) ?>
    </div>
    <div class="user-actions">
        <form method="POST">
            <input type="hidden" name="id" value="<?= $u['id'] ?>">
            <input type="text" name="name" value="<?= htmlspecialchars($u['name']) ?>" required>
            <input type="text" name="surname" value="<?= htmlspecialchars($u['surname']) ?>" required>
            <input type="email" name="email" value="<?= htmlspecialchars($u['email']) ?>" required>
            <input type="text" name="phone" value="<?= htmlspecialchars($u['phone']) ?>" required>
            <select name="role">
                <option value="user" <?= $u['role']=='user'?'selected':'' ?>>Пользователь</option>
                <option value="admin" <?= $u['role']=='admin'?'selected':'' ?>>Админ</option>
            </select>
            <button type="submit" name="edit_user">Редактировать</button>
        </form>
        <a href="?delete=<?= $u['id'] ?>" onclick="return confirm('Удалить пользователя?')">Удалить</a>
    </div>
</div>
<?php endforeach; ?>
<a href="index.php">Назад</a>
<script src="assets/script.js"></script>
</body>
</html>
