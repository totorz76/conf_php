<?php
include 'fonctions.php';
require 'connexion-bd.php';

$pdo = new PDO($dsn, $user, $pass, $options);
$sql = "SELECT * FROM participants ORDER BY id DESC";
$stm = $pdo->query($sql);
$conferences = $stm->fetchAll();
//dd($conferences);

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://bootswatch.com/5/solar/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/gh/tofsjonas/sortable@latest/sortable-base.min.css" rel="stylesheet" />
    <title>Inscription Conférence</title>
</head>

<body>
    <?php include 'nav.php'; ?>
    <div class="container mt-4">
        <?php if (count($conferences) === 0): ?>
            <div class="alert alert-warning">
                Aucune formation n'est disponible
            </div>

        <?php
        die();
        endif;
        ?>
        <h2>Liste des conférences</h2>
        <table class="table table-hover sortable">
            <thead>
                <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Nom</th>
                    <th scope="col">Email</th>
                    <th scope="col">Type de participant</th>
                    <th scope="col">Centres d’intérêt</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($conferences as $conference): ?>
                    <tr class="table-light">
                        <td><?= $conference['id'] ?></td>
                        <td><?= $conference['prenom'] . " " . $conference['nom'] ?></td>
                        <td><?= $conference['email'] ?></td>
                        <td><?= $conference['type_participant'] ?></td>
                        <td><?= $conference['centres_interet'] ?></td>
                        <td>
                            <a href="voir-conf.php?id=<?= $conference['id'] ?>" class="btn btn-info">Voir</a>
                            <a href="" class="btn btn-secondary">Editer</a>
                            <a href="supp-conf.php?id=<?= $conference['id'] ?>" class="btn btn-warning" onclick="return confirm('Etes-vous sur de vouloir supprimer cette conférence ?')">Supprimer

                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/gh/tofsjonas/sortable@latest/dist/sortable.auto.min.js"></script>
</body>

</html>