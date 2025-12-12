<?php
// Vérification basique : on doit recevoir ?status=ok
if (!isset($_GET['status']) || $_GET['status'] !== 'ok') {
    header('Location: index.php');
    exit;
}

$nom = htmlspecialchars($_GET['nom'] ?? '');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://bootswatch.com/5/solar/bootstrap.min.css">
    <title>Inscription réussie</title>
</head>
<body>

<div class="container mt-5">
    <div class="alert alert-success">
        <h2>🎉 Inscription réussie !</h2>
        <p>Merci <strong><?= $nom ?></strong>, votre inscription a bien été enregistrée.</p>
    </div>

    <a href="index.php" class="btn btn-primary mt-3">Retour au formulaire</a>
</div>

</body>
</html>
