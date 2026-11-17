
</div>
</main>

<footer class="site-footer">
    <div class="footer-container">

        <div class="footer-content">

            <div class="footer-brand">
                <h2>GHD-HSP</h2>
                <p>
                    Plateforme de mise en relation
                    des étudiants, médecins et partenaires
                    du réseau hospitalo-universitaire.
                </p>
            </div>

            <div class="footer-links">
                <h3>Navigation</h3>
                <a href="/GHD/public/index.php">Accueil</a>
                <a href="/GHD/public/a_propos.php">À propos</a>
                <?php if (isset($_SESSION['utilisateur_id'])): ?>
                    <a href="/GHD/public/annuaire.php">Annuaire</a>
                    <?php if (in_array($_SESSION['role'] ?? '', ['etudiant', 'medecin', 'partenaire'], true)): ?>
                        <a href="/GHD/public/profil.php">Mon profil</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

        </div>

        <div class="footer-bottom">
            <span>&copy; <?= date('Y') ?> GHD-HSP</span>
            <span>Projet BTS SIO SLAM</span>
        </div>

    </div>
</footer>

</body>
</html>
