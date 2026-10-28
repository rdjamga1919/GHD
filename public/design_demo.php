
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>GHD - Design System</title>

    <link rel="stylesheet" href="assets/css/variables.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/components.css">
</head>

<body>

<main class="container">

    <h1 class="page-title">Design System GHD</h1>

    <p>Bibliothèque des composants visuels du projet.</p>

    <section class="card fade-in">
        <h2>Boutons</h2>

        <button class="btn">Bouton principal</button>

        <button class="btn btn-secondary">
            Bouton secondaire
        </button>
    </section>

    <section class="card">
        <h2>Exemple de profil</h2>

        <p><strong>Nom :</strong> Marie Dupont</p>
        <p><strong>Rôle :</strong> Étudiante</p>

        <span class="tag">Compte validé</span>
    </section>

    <section class="card">
        <h2>Formulaire</h2>

        <div class="form-group">
            <label for="nom">Nom</label>
            <input
                type="text"
                id="nom"
                class="form-control"
                placeholder="Votre nom"
            >
        </div>

        <button class="btn">Enregistrer</button>
    </section>

    <section class="card">
        <h2>Messages</h2>

        <div class="alert alert-success">
            Modification enregistrée avec succès.
        </div>

        <div class="alert alert-error">
            Une erreur est survenue.
        </div>
    </section>

    <section class="card">
        <h2>Tableau</h2>

        <table class="table">
            <thead>
            <tr>
                <th>Nom</th>
                <th>Rôle</th>
            </tr>
            </thead>

            <tbody>
            <tr>
                <td>Marie Dupont</td>
                <td>Étudiant</td>
            </tr>

            <tr>
                <td>Jean Martin</td>
                <td>Médecin</td>
            </tr>
            </tbody>
        </table>
    </section>

</main>

</body>
</html>
