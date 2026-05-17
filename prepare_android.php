#!/usr/bin/env php
<?php
/**
 * Script de préparation du bundle Android pour Lafia.
 *
 * Usage : php prepare_android.php
 *
 * Ce script remplace `php artisan native:run android` (qui exige un device connecté)
 * et crée le laravel_bundle.zip prêt pour `./gradlew assembleDebug`.
 */

$appDir    = __DIR__;
$tempDir   = $appDir . '/nativephp/android/laravel';
$destZip   = $appDir . '/nativephp/android/app/src/main/assets/laravel_bundle.zip';
$assetsDir = dirname($destZip);

// ── 1. Temp dir ────────────────────────────────────────────────────────────────
echo "[1/7] Nettoyage du dossier temporaire...\n";
if (is_dir($tempDir)) {
    exec('rm -rf ' . escapeshellarg($tempDir));
}
mkdir($tempDir, 0755, true);

// ── 2. Copie source (rsync, public/build INCLUS) ───────────────────────────────
echo "[2/7] Copie des fichiers source (rsync)...\n";
$excludes = [
    '.git',
    'node_modules',
    'nativephp/ios',
    'nativephp/android',
    'temp',
    'content',
    '*/tests',
    'vendor/*/vendor',
    'vendor/nativephp/mobile/vendor',
];
$excludeFlags = implode(' ', array_map(fn ($d) => "--exclude=" . escapeshellarg($d), $excludes));
$cmd = "rsync -aL $excludeFlags " . escapeshellarg($appDir . '/') . ' ' . escapeshellarg($tempDir . '/');
exec($cmd, $out, $ret);
if ($ret !== 0) {
    echo "ERREUR : rsync a échoué.\n";
    exit(1);
}

// ── 3. Composer install (--no-dev) ─────────────────────────────────────────────
echo "[3/7] composer install --no-dev...\n";
exec('cd ' . escapeshellarg($tempDir) . ' && composer install --no-dev --no-interaction 2>&1', $out, $ret);
foreach ($out as $line) { echo "  $line\n"; }
if ($ret !== 0) {
    echo "ERREUR : composer install a échoué.\n";
    exec('rm -rf ' . escapeshellarg($tempDir));
    exit(1);
}

// ── 4. Composer dump-autoload ──────────────────────────────────────────────────
echo "[4/7] composer dump-autoload --optimize...\n";
exec('cd ' . escapeshellarg($tempDir) . ' && composer dump-autoload --optimize --classmap-authoritative 2>&1', $out, $ret);

// ── 5. Fichiers de config supplémentaires ─────────────────────────────────────
echo "[5/7] Copie .env + ASSET_URL + .version + artisan.php...\n";

// .version
file_put_contents($tempDir . '/.version', 'DEBUG');

// .env avec ASSET_URL
if (file_exists($appDir . '/.env')) {
    copy($appDir . '/.env', $tempDir . '/.env');
    file_put_contents($tempDir . '/.env', PHP_EOL . 'ASSET_URL="/_assets"' . PHP_EOL, FILE_APPEND);
}

// artisan.php NativePHP bootstrap
$artisanSrc = $appDir . '/vendor/nativephp/mobile/bootstrap/android/artisan.php';
if (file_exists($artisanSrc)) {
    copy($artisanSrc, $tempDir . '/artisan.php');
}

// ── 6. Création du ZIP ────────────────────────────────────────────────────────
echo "[6/7] Création du bundle ZIP...\n";
if (file_exists($destZip)) {
    unlink($destZip);
}

$zip = new ZipArchive();
$result = $zip->open($destZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);
if ($result !== true) {
    echo "ERREUR : impossible de créer le ZIP ($destZip).\n";
    exec('rm -rf ' . escapeshellarg($tempDir));
    exit(1);
}

// Préfixes toujours exclus du zip
$alwaysExclude = [
    'vendor/nativephp/mobile/resources',
    'vendor/nativephp/mobile/vendor',
    'vendor/endroid',
    '.idea',
    'output',
    'storage/framework/views',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/app/native-build',
    'bootstrap/cache',
    'nativephp',
    'public/storage',
];

$source = rtrim(str_replace('\\', '/', $tempDir), '/') . '/';
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
    RecursiveIteratorIterator::LEAVES_ONLY
);

$added = 0;
foreach ($iterator as $file) {
    $filePath     = str_replace('\\', '/', $file->getRealPath());
    $relativePath = ltrim(str_replace($source, '', $filePath), '/');

    // Fichiers toujours exclus
    if (str_ends_with($relativePath, '.jks') || str_ends_with($relativePath, '.zip')) {
        continue;
    }

    // Préfixes exclus
    $skip = false;
    foreach ($alwaysExclude as $exc) {
        if (str_starts_with($relativePath, rtrim($exc, '/') . '/') || $relativePath === $exc) {
            $skip = true;
            break;
        }
    }
    if ($skip) {
        continue;
    }

    $zip->addFile($filePath, $relativePath);
    $added++;
}
$zip->close();

$sizeMB = round(filesize($destZip) / 1024 / 1024, 2);
echo "  $added fichiers ajoutés — taille : {$sizeMB} MB\n";

if (filesize($destZip) < 1024 * 100) {
    echo "ERREUR : bundle ZIP trop petit, quelque chose a mal tourné.\n";
    exec('rm -rf ' . escapeshellarg($tempDir));
    exit(1);
}

// ── 7. bundle_meta.json + cleanup ─────────────────────────────────────────────
echo "[7/7] Écriture de bundle_meta.json + nettoyage...\n";
$bundleMeta = json_encode([
    'version'        => 'DEBUG',
    'version_code'   => 1,
    'bifrost_app_id' => null,
    'runtime_mode'   => 'persistent',
], JSON_PRETTY_PRINT);
file_put_contents($assetsDir . '/bundle_meta.json', $bundleMeta);

exec('rm -rf ' . escapeshellarg($tempDir));

echo "\nBundle prêt : $destZip\n";
echo "\nLance maintenant :\n";
echo "  cd nativephp/android && ./gradlew assembleDebug\n\n";
