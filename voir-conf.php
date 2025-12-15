<?php
include 'fonctions.php';
require 'connexion-bd.php';

$idVoir = $_GET['id'] ?? null;
if (!is_numeric($idVoir)) {
    dd("Cette conférence n'existe pas.");
}
$pdo = new PDO($dsn, $user, $pass, $options);
$stm = $pdo->prepare("SELECT * FROM participants WHERE id = :id");
$stm->bindParam(':id', $idVoir, PDO::PARAM_INT);
$stmConf = $stm->execute();
$conferences = $stm->fetch();

// dd($conferences);

function createCards($conferences) {
    $cards = '';
    foreach ($conferences as $conference) {
        $cards .= '<div class="card" style="width: 18rem;">
                    <div class="card-body">
                        <h5 class="card-title">' . $conference['prenom'] . " " . $conference['nom'] . '</h5>
                        <h6 class="card-subtitle mb-2 text-muted">' . $conference['email'] . '</h6>
                        <p class="card-text">' . $conference['type_participant'] . '</p>                        
                        <p class="card-text">' . $conference['centres_interet'] . '</p>
                    </div>
                </div>';
    }
    return $cards;
}

?>
