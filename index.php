<?php
// ===============================================
// CONFIG PDO
// ===============================================
$host    = 'localhost';
$db      = 'conference';
$user    = 'root';
$pass    = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// ===============================================
// VARIABLES FORMULAIRE
// ===============================================
$nom = '';
$prenom = '';
$email = '';
$password = '';
$password_confirm = '';
$date_naissance = '';
$telephone = '';
$pays = '';
$type = '';
$centres_interet = [];
$errors = [];

$liste_pays = ["France", "Belgique", "Suisse", "Canada"];
$types_acceptes = ["Etudiant", "Professionnel", "Speaker"];
$interets_acceptes = ["PHP", "JavaScript", "DevOps", "IA"];

// ===============================================
// TRAITEMENT FORMULAIRE
// ===============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['envoyer'])) {

    // Nettoyage des données
    $nom = trim(strip_tags($_POST['nom'] ?? ''));
    $prenom = trim(strip_tags($_POST['prenom'] ?? ''));
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $date_naissance = $_POST['date_naissance'] ?? '';
    $telephone = trim($_POST['telephone'] ?? '');
    $pays = $_POST['pays'] ?? '';
    $type = $_POST['type_participant'] ?? '';
    $centres_interet = $_POST['centres_interet'] ?? [];

    // -------------------------- VALIDATIONS ------------------------------

    // NOM
    if ($nom === '') {
        $errors['nom'] = "Le nom ne doit pas être vide.";
    } elseif (strlen($nom) < 2 || strlen($nom) > 30) {
        $errors['nom'] = "Le nom doit contenir entre 2 et 30 caractères.";
    }

    // PRENOM
    if ($prenom === '') {
        $errors['prenom'] = "Le prénom ne doit pas être vide.";
    } elseif (strlen($prenom) < 2 || strlen($prenom) > 30) {
        $errors['prenom'] = "Le prénom doit contenir entre 2 et 30 caractères.";
    }

    // EMAIL
    if ($email === '') {
        $errors['email'] = "L'email est obligatoire.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Email invalide.";
    }

    // PASSWORD
    if ($password === '') {
        $errors['password'] = "Mot de passe obligatoire.";
    } elseif (strlen($password) < 8 ||
              !preg_match('/[A-Z]/', $password) ||
              !preg_match('/[a-z]/', $password) ||
              !preg_match('/[0-9]/', $password)) {
        $errors['password'] = "Min 8 caractères, 1 maj, 1 min, 1 chiffre.";
    }

    // CONFIRM PASSWORD
    if ($password_confirm === '') {
        $errors['password_confirm'] = "Confirmation obligatoire.";
    } elseif ($password !== $password_confirm) {
        $errors['password_confirm'] = "Les mots de passe ne correspondent pas.";
    }

    // DATE NAISSANCE
    if ($date_naissance === '') {
        $errors['date_naissance'] = "Date obligatoire.";
    } else {
        $d = DateTime::createFromFormat('Y-m-d', $date_naissance);
        if (!$d || $d->format('Y-m-d') !== $date_naissance) {
            $errors['date_naissance'] = "Date invalide.";
        } else {
            $age = $d->diff(new DateTime())->y;
            if ($age < 18) {
                $errors['date_naissance'] = "Vous devez avoir au moins 18 ans.";
            }
        }
    }

    // TELEPHONE
    if ($telephone === '') {
        $errors['telephone'] = "Téléphone obligatoire.";
    } elseif (!preg_match('/^\+?[0-9]{10,15}$/', $telephone)) {
        $errors['telephone'] = "Format téléphone invalide.";
    }

    // PAYS
    if ($pays === '') {
        $errors['pays'] = "Le pays est obligatoire.";
    } elseif (!in_array($pays, $liste_pays)) {
        $errors['pays'] = "Pays invalide.";
    }

    // TYPE PARTICIPANT
    if ($type === '') {
        $errors['type_participant'] = "Type obligatoire.";
    } elseif (!in_array($type, $types_acceptes)) {
        $errors['type_participant'] = "Type non autorisé.";
    }

    // CENTRES INTERET
    if (empty($centres_interet)) {
        $errors['centres_interet'] = "Choisir au moins un centre.";
    } else {
        foreach ($centres_interet as $ci) {
            if (!in_array($ci, $interets_acceptes)) {
                $errors['centres_interet'] = "Centre d’intérêt non autorisé.";
                break;
            }
        }
    }

    // CONDITIONS
    if (empty($_POST['conditions'])) {
        $errors['conditions'] = "Vous devez accepter les conditions.";
    }

     // ===================================================================
    // PREPARATION DES DONNEES AVANT L'INSERTION
    // ===================================================================

    // Transformation du tableau des centres d'intérêt en chaîne
    $centres_interet_str = implode(',', $centres_interet);

    // Vérification de la case des conditions
    $conditions_valide = isset($_POST['conditions']) ? 1 : 0;

    // Hash du mot de passe
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Date et heure actuelles pour l'inscription
    $date_inscription = date('Y-m-d H:i:s');

    // ===================================================================
    // INSERTION EN BASE SI AUCUNE ERREUR
    // ===================================================================
    if (empty($errors)) {

        try {

            // Connexion PDO au moment de l'insertion
            $pdo = new PDO($dsn, $user, $pass, $options);

            $sql = "INSERT INTO participants 
                (nom, prenom, email, password_hash, date_naissance, telephone, pays, type_participant, centres_interet, conditions_valide, date_inscription)
                VALUES
                (:nom, :prenom, :email, :password_hash, :date_naissance, :telephone, :pays, :type_participant, :centres_interet, :conditions_valide, :date_inscription)";


            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ':nom'              => $nom,
                ':prenom'           => $prenom,
                ':email'            => $email,
                ':password_hash'    => $password_hash,
                ':date_naissance'   => $date_naissance,
                ':telephone'        => $telephone,
                ':pays'             => $pays,
                ':type_participant' => $type,
                ':centres_interet'  => $centres_interet_str,
                ':conditions_valide'=> $conditions_valide,
                ':date_inscription' => $date_inscription,
            ]);

            header("Location: success.php?status=ok&nom=" . urlencode($nom));
            exit;

        } catch (PDOException $e) {
            $errors['pdo'] = "Erreur de connexion à la base ou d’insertion.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://bootswatch.com/5/solar/bootstrap.min.css">
    <title>Inscription Conférence</title>
</head>

<body>
<?php include 'header.php'; ?>
<div class="container mt-4">

    <h2>Inscription à la conférence</h2>

    <?php if (!empty($errors['pdo'])): ?>
        <div class="alert alert-danger"><?= $errors['pdo'] ?></div>
    <?php endif; ?>

    <form action="" method="post">

        <!-- NOM -->
        <div>
            <label class="form-label mt-4">Nom</label>
            <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($nom) ?>">
            <?php if (!empty($errors['nom'])): ?>
                <div class="alert alert-warning mt-1"><?= $errors['nom'] ?></div>
            <?php endif; ?>
        </div>

        <!-- PRENOM -->
        <div>
            <label class="form-label mt-4">Prénom</label>
            <input type="text" class="form-control" name="prenom" value="<?= htmlspecialchars($prenom) ?>">
            <?php if (!empty($errors['prenom'])): ?>
                <div class="alert alert-warning mt-1"><?= $errors['prenom'] ?></div>
            <?php endif; ?>
        </div>

        <!-- EMAIL -->
        <div>
            <label class="form-label mt-4">Email</label>
            <input type="text" class="form-control" name="email" value="<?= htmlspecialchars($email) ?>">
            <?php if (!empty($errors['email'])): ?>
                <div class="alert alert-warning mt-1"><?= $errors['email'] ?></div>
            <?php endif; ?>
        </div>

        <!-- PASSWORD -->
        <div>
            <label class="form-label mt-4">Mot de passe</label>
            <input type="password" class="form-control" name="password">
            <?php if (!empty($errors['password'])): ?>
                <div class="alert alert-warning mt-1"><?= $errors['password'] ?></div>
            <?php endif; ?>
        </div>

        <!-- PASSWORD CONFIRM -->
        <div>
            <label class="form-label mt-4">Confirmer mot de passe</label>
            <input type="password" class="form-control" name="password_confirm">
            <?php if (!empty($errors['password_confirm'])): ?>
                <div class="alert alert-warning mt-1"><?= $errors['password_confirm'] ?></div>
            <?php endif; ?>
        </div>

        <!-- DATE NAISSANCE -->
        <div>
            <label class="form-label mt-4">Date de naissance</label>
            <input type="date" class="form-control" name="date_naissance" value="<?= htmlspecialchars($date_naissance) ?>">
            <?php if (!empty($errors['date_naissance'])): ?>
                <div class="alert alert-warning mt-1"><?= $errors['date_naissance'] ?></div>
            <?php endif; ?>
        </div>

        <!-- TELEPHONE -->
        <div>
            <label class="form-label mt-4">Téléphone</label>
            <input type="text" class="form-control" name="telephone" value="<?= htmlspecialchars($telephone) ?>">
            <?php if (!empty($errors['telephone'])): ?>
                <div class="alert alert-warning mt-1"><?= $errors['telephone'] ?></div>
            <?php endif; ?>
        </div>

        <!-- PAYS -->
        <div>
            <label class="form-label mt-4">Pays</label>
            <select class="form-control" name="pays">
                <option value="">-- Sélectionner --</option>
                <?php foreach ($liste_pays as $p): ?>
                    <option value="<?= $p ?>" <?= ($pays === $p ? 'selected' : '') ?>><?= $p ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['pays'])): ?>
                <div class="alert alert-warning mt-1"><?= $errors['pays'] ?></div>
            <?php endif; ?>
        </div>

        <!-- TYPE PARTICIPANT -->
        <div>
            <label class="form-label mt-4">Type de participant</label>
            <select class="form-control" name="type_participant">
                <option value="">-- Sélectionner --</option>
                <?php foreach ($types_acceptes as $t): ?>
                    <option value="<?= $t ?>" <?= ($type === $t ? 'selected' : '') ?>><?= $t ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($errors['type_participant'])): ?>
                <div class="alert alert-warning mt-1"><?= $errors['type_participant'] ?></div>
            <?php endif; ?>
        </div>

        <!-- CENTRES D'INTERET -->
        <div class="mt-4">
            <label class="form-label">Centres d’intérêt</label><br>

            <?php foreach ($interets_acceptes as $ci): ?>
                <label>
                    <input type="checkbox" name="centres_interet[]" value="<?= $ci ?>"
                        <?= in_array($ci, $centres_interet) ? 'checked' : '' ?>>
                    <?= $ci ?>
                </label><br>
            <?php endforeach; ?>

            <?php if (!empty($errors['centres_interet'])): ?>
                <div class="alert alert-warning mt-1"><?= $errors['centres_interet'] ?></div>
            <?php endif; ?>
        </div>

        <!-- CONDITIONS -->
        <div class="mt-4">
            <label>
                <input type="checkbox" name="conditions" value="1"
                    <?= !empty($_POST['conditions']) ? 'checked' : '' ?>>
                J’accepte les conditions générales
            </label>
            <?php if (!empty($errors['conditions'])): ?>
                <div class="alert alert-warning mt-1"><?= $errors['conditions'] ?></div>
            <?php endif; ?>
        </div>

        <!-- SUBMIT -->
        <br>
        <div class="d-grid gap-2">
            <button class="btn btn-lg btn-success" type="submit" name="envoyer">Envoyer</button>
        </div>

    </form>
</div>

</body>
</html>
