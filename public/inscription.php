<?php

class Utilisateur
{
    private PDO $database;

    public function __construct(PDO $database)
    {
        $this->database = $database;
    }

    /**
     * Vérifie si l'adresse email existe déjà.
     */
    public function emailExiste(string $email): bool
    {
        $query = "
            SELECT id_utilisateur
            FROM Utilisateur
            WHERE email = :email
        ";

        $statement = $this->database->prepare($query);
        $statement->execute([
            'email' => $email
        ]);

        return $statement->fetch() !== false;
    }

    /**
     * Crée un nouvel utilisateur.
     */
    public function inscrire(
        string $nom,
        string $prenom,
        string $email,
        string $motDePasse,
        string $role
    ): bool {
        $motDePasseHash = password_hash(
            $motDePasse,
            PASSWORD_DEFAULT
        );

        $query = "
            INSERT INTO Utilisateur
                (nom, prenom, email, mot_de_passe, role)
            VALUES
                (:nom, :prenom, :email, :mot_de_passe, :role)
        ";

        $statement = $this->database->prepare($query);

        return $statement->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $email,
            'mot_de_passe' => $motDePasseHash,
            'role' => $role
        ]);
    }
}


/*
|--------------------------------------------------------------------------
| Connexion à la base de données
|--------------------------------------------------------------------------
*/

try {
    $database = new PDO(
        'mysql:host=localhost;dbname=gdh_hsp;charset=utf8mb4',
        'root',
        ''
    );

    $database->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $database->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

} catch (PDOException $exception) {
    die('Impossible de se connecter à la base de données.');
}


/*
|--------------------------------------------------------------------------
| Initialisation
|--------------------------------------------------------------------------
*/

$utilisateur = new Utilisateur($database);

$message = '';
$messageType = '';


/*
|--------------------------------------------------------------------------
| Traitement du formulaire
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = trim(isset($_POST['nom']) ? $_POST['nom'] : '');
    $prenom = trim(isset($_POST['prenom']) ? $_POST['prenom'] : '');
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $motDePasse = isset($_POST['mot_de_passe']) ? $_POST['mot_de_passe'] : '';
    $confirmation = isset($_POST['confirmation']) ? $_POST['confirmation'] : '';
    $role = isset($_POST['role']) ? $_POST['role'] : '';

    /*
     * Vérification des champs obligatoires.
     */
    if (
        $nom === '' ||
        $prenom === '' ||
        $email === '' ||
        $motDePasse === '' ||
        $confirmation === '' ||
        $role === ''
    ) {
        $message = 'Veuillez remplir tous les champs.';
        $messageType = 'error';
    }

    /*
     * Vérification de l'adresse email.
     */
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Veuillez saisir une adresse email valide.';
        $messageType = 'error';
    }

    /*
     * Vérification de la longueur du mot de passe.
     */
    elseif (strlen($motDePasse) < 8) {
        $message = 'Le mot de passe doit contenir au moins 8 caractères.';
        $messageType = 'error';
    }

    /*
     * Vérification de la confirmation du mot de passe.
     */
    elseif ($motDePasse !== $confirmation) {
        $message = 'Les mots de passe ne correspondent pas.';
        $messageType = 'error';
    }

    /*
     * Vérification du rôle.
     */
    elseif (
        !in_array(
            $role,
            ['etudiant', 'medecin', 'partenaire', 'gestionnaire'],
            true
        )
    ) {
        $message = 'Le rôle sélectionné est invalide.';
        $messageType = 'error';
    }

    /*
     * Vérification de l'email dans la BDD.
     */
    elseif ($utilisateur->emailExiste($email)) {
        $message = 'Cette adresse email est déjà utilisée.';
        $messageType = 'error';
    }

    /*
     * Création du compte.
     */
    else {
        try {
            $utilisateur->inscrire(
                $nom,
                $prenom,
                $email,
                $motDePasse,
                $role
            );

            $message = 'Votre compte a été créé avec succès.';
            $messageType = 'success';

            /*
             * On vide les champs après une inscription réussie.
             */
            $nom = '';
            $prenom = '';
            $email = '';

        } catch (PDOException $exception) {
            $message = 'Une erreur est survenue lors de la création du compte.';
            $messageType = 'error';
        }
    }
}

?>

<!DOCTYPE html>

<html lang="fr">

<head>

    ```
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Inscription - GDH</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;

            display: flex;
            justify-content: center;
            align-items: center;

            font-family: Arial, sans-serif;

            background-color: #f4f4f4;
        }

        .formulaire {
            width: 100%;
            max-width: 450px;

            padding: 30px;

            background-color: white;

            border-radius: 10px;

            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        h1 {
            margin-top: 0;
            margin-bottom: 25px;

            text-align: center;
        }

        .champ {
            margin-bottom: 18px;
        }

        label {
            display: block;

            margin-bottom: 6px;

            font-weight: bold;
        }

        input,
        select {
            width: 100%;

            padding: 11px;

            border: 1px solid #ccc;
            border-radius: 5px;

            font-size: 15px;
        }

        input:focus,
        select:focus {
            outline: none;

            border-color: #2563eb;
        }

        button {
            width: 100%;

            padding: 12px;

            margin-top: 5px;

            border: none;
            border-radius: 5px;

            background-color: #2563eb;

            color: white;

            font-size: 16px;
            font-weight: bold;

            cursor: pointer;
        }

        button:hover {
            background-color: #1d4ed8;
        }

        .message {
            margin-bottom: 20px;

            padding: 12px;

            border-radius: 5px;

            text-align: center;
        }

        .message.error {
            background-color: #fee2e2;

            color: #991b1b;
        }

        .message.success {
            background-color: #dcfce7;

            color: #166534;
        }

    </style>
    ```

</head>

<body>

```
<main class="formulaire">

    <h1>Créer un compte</h1>

    <?php if ($message !== ''): ?>

        <div class="message <?= htmlspecialchars($messageType) ?>">
            <?= htmlspecialchars($message) ?>
        </div>

    <?php endif; ?>


    <form method="POST" action="">

        <!-- Nom -->
        <div class="champ">

            <label for="nom">
                Nom
            </label>

            <input
                type="text"
                id="nom"
                name="nom"
                maxlength="100"
                required
                value="<?= htmlspecialchars($nom ?? '') ?>"
            >

        </div>


        <!-- Prénom -->
        <div class="champ">

            <label for="prenom">
                Prénom
            </label>

            <input
                type="text"
                id="prenom"
                name="prenom"
                maxlength="100"
                required
                value="<?= htmlspecialchars($prenom ?? '') ?>"
            >

        </div>


        <!-- Email -->
        <div class="champ">

            <label for="email">
                Adresse email
            </label>

            <input
                type="email"
                id="email"
                name="email"
                maxlength="150"
                required
                value="<?= htmlspecialchars($email ?? '') ?>"
            >

        </div>


        <!-- Mot de passe -->
        <div class="champ">

            <label for="mot_de_passe">
                Mot de passe
            </label>

            <input
                type="password"
                id="mot_de_passe"
                name="mot_de_passe"
                minlength="8"
                required
            >

        </div>


        <!-- Confirmation -->
        <div class="champ">

            <label for="confirmation">
                Confirmer le mot de passe
            </label>

            <input
                type="password"
                id="confirmation"
                name="confirmation"
                minlength="8"
                required
            >

        </div>


        <!-- Rôle -->
        <div class="champ">

            <label for="role">
                Rôle
            </label>

            <select
                id="role"
                name="role"
                required
            >

                <option value="">
                    -- Choisir un rôle --
                </option>

                <option value="etudiant">
                    Étudiant
                </option>

                <option value="medecin">
                    Médecin
                </option>

                <option value="partenaire">
                    Partenaire
                </option>

                <option value="gestionnaire">
                    Gestionnaire
                </option>

            </select>

        </div>


        <button type="submit">
            S'inscrire
        </button>

    </form>

</main>
```

</body>

</html>
