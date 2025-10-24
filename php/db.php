<?php
try {
 $conn = new PDO("mysql:host=localhost;port=3360", "root", "");
 $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
 $conn->exec('CREATE DATABASE IF NOT EXISTS user_management');
 $conn->exec('USE user_management');
 $conn->exec('CREATE TABLE IF NOT EXISTS users (
 nom_utilisateur VARCHAR(50) PRIMARY KEY,
 nom VARCHAR(50) NOT NULL,
 prenom VARCHAR(50) NOT NULL,
 _image VARCHAR(255),
 pass VARCHAR(255) NOT NULL,
 email VARCHAR(100),
 _role VARCHAR(50) NOT NULL,
 fuseau_horaire VARCHAR(50) NOT NULL,
 date_inscription DATE 
 )');/*
 //$conn->exec('DELETE FROM Notes where id=8');
 $hashed_pass = password_hash('2004', PASSWORD_BCRYPT);
 $sql = "INSERT INTO users (nom_utilisateur,nom, prenom, _image, pass, email,_role,fuseau_horaire, date_inscription) 
 VALUES (:nom_utilisateur,:nom, :prenom, :_image, :pass, :email,:_role,:fuseau_horaire, :date_inscription)";
 $stmt = $conn->prepare($sql);
 $stmt->execute([
 'nom_utilisateur' => 'h_oumaima',
 'nom' => 'hamza',
 'prenom' => 'oumaima',
 '_image' => '../src/user.jpeg',
 'pass' => $hashed_pass,
 'email' => 'oumaima@gmail.com',
 '_role' => 'admin',
 'fuseau_horaire' => 'Africa/Casablanca',
 'date_inscription' => '2025-05-12'
 ]);*/
} catch (PDOException $e) {
 echo "Erreur : " . $e->getMessage();
}
?>