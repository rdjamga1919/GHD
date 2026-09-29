<?php

session_start();

if (
    !isset($_SESSION['id_utilisateur']) ||
    ($_SESSION['role'] ?? '') !== 'gestionnaire'
) {
    header('Location: ../connexion.php');
    exit;
}

try {
    $database = new PDO(
        'mysql:host=localhost;dbname=gdh_hsp;charset=utf8mb4',
        'root',
        ''
    );

    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $database->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $exception) {
    die('Impossible de se connecter à la base de données.');
}

$query = "
    SELECT id_utilisateur, nom, prenom, email, role, date_creation
    FROM Utilisateur
    WHERE est_valide = 0
    ORDER BY date_creation ASC
";

$statement = $database->query($query);
$comptesEnAttente = $statement->fetchAll();

$libellesRole = [
    'etudiant'     => 'Étudiant',
    'medecin'      => 'Médecin',
    'partenaire'   => 'Partenaire',
    'gestionnaire' => 'Gestionnaire',
];

$message = $_SESSION['message'] ?? '';
$messageType = $_SESSION['messageType'] ?? '';

unset($_SESSION['message'], $_SESSION['messageType']);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des utilisateurs - GDH</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 40px 20px; font-family: Arial, sans-serif; background-color: #f4f4f4; }
        main { max-width: 1000px; margin: 0 auto; padding: 30px; background-color: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        h1 { margin-top: 0; margin-bottom: 25px; }
        .message { margin-bottom: 20px; padding: 12px; border-radius: 5px; }
        .message.error { background-color: #fee2e2; color: #991b1b; }
        .message.success { background-color: #dcfce7; color: #166534; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 10px; text-align: left; border-bottom: 1px solid #e5e7eb; vertical-align: middle; }
        th { background-color: #f9fafb; font-size: 14px; text-transform: uppercase; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 13px; background-color: #e0e7ff; color: #3730a3; }
        .actions { display: flex; gap: 8px; }
        .actions form { margin: 0; }
        button { padding: 8px 14px; border: none; border-radius: 5px; font-size: 14px; font-weight: bold; cursor: pointer; }
        .btn-valider { background-color: #16a34a; color: white; }
        .btn-valider:hover { background-color: #15803d; }
        .btn-refuser { background-color: #dc2626; color: white; }
        .btn-refuser:hover { background-color: #b91c1c; }
        .vide { padding: 20px 0; color: #6b7280; text-align: center; }
    </style>
</head>
<body>
<main>

    <h1>Comptes en attente de validation</h1>

    <?php if ($message !== ''): ?>
        <div class="message <?= htmlspecialchars($messageType) ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if (empty($comptesEnAttente)): ?>

        <p class="vide">Aucun compte en attente de validation.</p>

    <?php else: ?>

        <table>
            <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Demande le</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($comptesEnAttente as $compte): ?>
                <tr>
                    <td><?= htmlspecialchars($compte['nom']) ?></td>
                    <td><?= htmlspecialchars($compte['prenom']) ?></td>
                    <td><?= htmlspecialchars($compte['email']) ?></td>
                    <td>
                            <span class="badge">
                                <?= htmlspecialchars($libellesRole[$compte['role']] ?? $compte['role']) ?>
                            </span>
                    </td>
                    <td>
                        <?= htmlspecialchars(date('d/m/Y à H:i', strtotime($compte['date_creation']))) ?>
                    </td>
                    <td class="actions">
                        <form method="POST" action="../../src/traitement/admin/traitementGestionUtilisateurs.php">
                            <input type="hidden" name="id_utilisateur" value="<?= (int) $compte['id_utilisateur'] ?>">
                            <input type="hidden" name="action" value="valider">
                            <button type="submit" class="btn-valider">Valider</button>
                        </form>
                        <form method="POST" action="../../src/traitement/admin/traitementGestionUtilisateurs.php"
                              onsubmit="return confirm('Refuser et supprimer définitivement ce compte ?');">
                            <input type="hidden" name="id_utilisateur" value="<?= (int) $compte['id_utilisateur'] ?>">
                            <input type="hidden" name="action" value="refuser">
                            <button type="submit" class="btn-refuser">Refuser</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    <?php endif; ?>

</main>

</body>
</html>
