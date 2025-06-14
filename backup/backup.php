<?php
$now = date('Y-m-d');
$backupDate = date('Y_m_d');

// --- Database Backup ---
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'db_warehouse_cpgc';

function backup_database($host, $user, $pass, $dbname, $backupDate) {
    $mysqli = new mysqli($host, $user, $pass, $dbname);
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    $tables = [];
    $result = $mysqli->query("SHOW TABLES");
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
    }

    $backup = '';
    foreach ($tables as $table) {
        $res = $mysqli->query("SELECT * FROM $table");
        $num_fields = $res->field_count;

        $row2 = $mysqli->query("SHOW CREATE TABLE $table")->fetch_row();
        $backup .= "\n\n" . $row2[1] . ";\n\n";

        while ($row = $res->fetch_row()) {
            $backup .= "INSERT INTO $table VALUES(";
            for ($j = 0; $j < $num_fields; $j++) {
                $row[$j] = addslashes($row[$j]);
                $row[$j] = str_replace("\n", "\\n", $row[$j]);
                $backup .= isset($row[$j]) ? '"' . $row[$j] . '"' : '""';
                if ($j < $num_fields - 1) $backup .= ',';
            }
            $backup .= ");\n";
        }
    }

    $filePath = "C:/backup/warehouse_cpgc/db_backup/$backupDate.sql";

    // Ensure output directory exists
    $dir = dirname($filePath);
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }

    file_put_contents($filePath, $backup);
    echo "✅ Database backup created: $filePath\n";

    // // Then copy the backup file to OneDrive
    $oneDrivePath = "C:/Users/WAREHOUSE-SERVER/OneDrive/warehouse_cpgc/db_backup/$backupDate.sql";

    // Ensure OneDrive directory exists
    $oneDriveDir = dirname($oneDrivePath);
    if (!file_exists($oneDriveDir)) {
        mkdir($oneDriveDir, 0777, true);
    }

    copy($filePath, $oneDrivePath);
}

// --- File & Image Backup ---
function zip_directories($folders, $zipPath) {
    $zip = new ZipArchive();
    $zipDir = dirname($zipPath);
    if (!file_exists($zipDir)) {
        mkdir($zipDir, 0777, true);
    }

    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
        exit("❌ Cannot open <$zipPath>\n");
    }

    foreach ($folders as $folder) {
        $rootPath = realpath($folder);
        if (!$rootPath) {
            echo "⚠️ Folder not found or empty: $folder\n";
            continue;
        }

        echo "🔍 Zipping from: $rootPath\n";

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);
                $zip->addFile($filePath, basename($folder) . '/' . $relativePath);
            }
        }
    }

    $zip->close();
    echo "✅ ZIP created: $zipPath\n";
}

// --- Recursive Copy Helper ---
function rcopy($src, $dst) {
    if (is_dir($src)) {
        if (!file_exists($dst)) mkdir($dst, 0777, true);
        $files = scandir($src);
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                rcopy("$src/$file", "$dst/$file");
            }
        }
    } else if (file_exists($src)) {
        copy($src, $dst);
    }
}

// === RUN BACKUP STEPS ===

// 1. Database Backup
backup_database($host, $user, $pass, $dbname, $backupDate);

// 2. Images Folder Backup
$ImagesBackup = [
    'C:/Systems/warehouse_management_cpgc/public/images',
];
$imagesZipPath = "C:/backup/warehouse_cpgc/uploads/images/$backupDate.zip";
zip_directories($ImagesBackup, $imagesZipPath);
rcopy($imagesZipPath, "C:/Users/WAREHOUSE-SERVER/OneDrive/warehouse_cpgc/uploads/images/$backupDate.zip");

// 3. Gatepass Items Folder Backup
$GatepassItemsBackup = [
    'C:/Systems/warehouse_management_cpgc/public/gatepass_items'
];
$gatepassZipPath = "C:/backup/warehouse_cpgc/uploads/gatepass/$backupDate.zip";
zip_directories($GatepassItemsBackup, $gatepassZipPath);
rcopy($gatepassZipPath, "C:/Users/WAREHOUSE-SERVER/OneDrive/warehouse_cpgc/uploads/gatepass/$backupDate.zip");

echo "🎉 All backups completed successfully.\n";
?>
