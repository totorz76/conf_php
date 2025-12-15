<?php
include 'fonctions.php';
require 'connexion-bd.php';

$idSupp = $_GET['id'] ?? null;
if (!is_numeric($idSupp)) {
    dd("Cette conférence n'existe pas.");
}

$pdo = new PDO($dsn, $user, $pass, $options);
$stm = $pdo->prepare("DELETE FROM participants WHERE id = :id");
$stm->bindParam(':id', $idSupp, PDO::PARAM_INT);
$suppResult = $stm->execute();

if ($suppResult) {
    header('Location: index.php');
}