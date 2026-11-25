<?php
$pdo = new PDO('mysql:dbname=jobs;host=mysql','student','student');
$hash = password_hash('letmein', PASSWORD_BCRYPT);
$stmt = $pdo->prepare('UPDATE users SET password = :password WHERE username = :username');
$stmt->execute(['password' => $hash, 'username' => 'admin']);
echo 'DONE';
