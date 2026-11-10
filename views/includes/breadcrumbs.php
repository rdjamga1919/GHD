<?php
/** $breadcrumbs = [['label' => 'Accueil', 'url' => '/GHD/public/index.php'], ...]. */
$breadcrumbEscape = static fn($value): string => htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$breadcrumbSafeUrl = static function ($url): ?string {
    if (!is_string($url) || preg_match('/[\\\\\x00-\x20]/', $url)) return null;
    $parts = parse_url($url);
    if ($parts === false || isset($parts['scheme']) || isset($parts['host'])) return null;
    $path = rawurldecode($parts['path'] ?? '');
    if (!str_starts_with($path, '/GHD/public/')) return null;
    $root = realpath(__DIR__ . '/../../public');
    $file = realpath(__DIR__ . '/../..' . substr($path, strlen('/GHD')));
    if ($root === false || $file === false || !str_starts_with($file, $root . DIRECTORY_SEPARATOR)
        || !is_file($file) || pathinfo($file, PATHINFO_EXTENSION) !== 'php') return null;
    return $url;
};
$breadcrumbItems = array_values($breadcrumbs ?? []);
?>
<?php if ($breadcrumbItems !== []): ?>
<nav class="breadcrumb" aria-label="Fil d’Ariane">
    <ol>
        <?php foreach ($breadcrumbItems as $breadcrumbIndex => $breadcrumbItem): ?>
            <?php $breadcrumbLast = $breadcrumbIndex === count($breadcrumbItems) - 1;
            $breadcrumbUrl = $breadcrumbSafeUrl($breadcrumbItem['url'] ?? null); ?>
            <li>
                <?php if (!$breadcrumbLast && $breadcrumbUrl !== null): ?>
                    <a href="<?= $breadcrumbEscape($breadcrumbUrl) ?>"><?= $breadcrumbEscape($breadcrumbItem['label'] ?? '') ?></a>
                <?php else: ?>
                    <span <?= $breadcrumbLast ? 'aria-current="page"' : '' ?>><?= $breadcrumbEscape($breadcrumbItem['label'] ?? '') ?></span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>
<?php endif; ?>
