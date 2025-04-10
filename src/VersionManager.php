<?php

namespace RPurinton\Tagit;

class VersionManager {
    public function incrementVersion($version) {
        $parts = explode('.', $version);
        $parts[count($parts) - 1]++;
        return implode('.', $parts);
    }

    public function updateVersionFiles() {
        $composerFile = 'composer.json';
        $packageFile = 'package.json';
        $newVersion = null;

        if (file_exists($composerFile)) {
            $composerData = json_decode(file_get_contents($composerFile), true);
            if (!isset($composerData['version'])) {
                echo "Version not found in composer.json.\n";
                exit(1);
            }
            $newVersion = $this->incrementVersion($composerData['version']);
            $composerData['version'] = $newVersion;
            file_put_contents($composerFile, json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        if (file_exists($packageFile)) {
            $packageData = json_decode(file_get_contents($packageFile), true);
            if (!isset($packageData['version'])) {
                echo "Version not found in package.json.\n";
                exit(1);
            }
            // Sync with composer version or use package version
            if(!$newVersion) {
                $newVersion = $this->incrementVersion($packageData['version']);
            }
            $packageData['version'] = $newVersion;
            file_put_contents($packageFile, json_encode($packageData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }

        return $newVersion;
    }

    public function gitOperations($newVersion) {
        $date = date('Y-m-d');
        if (file_exists('composer.json')) {
            exec('COMPOSER_HOME="." COMPOSER_ALLOW_SUPERUSER=1 composer upgrade');
            exec('COMPOSER_HOME="." COMPOSER_ALLOW_SUPERUSER=1 composer bump');
        }

        if (file_exists('package.json')) {
            exec('npm upgrade');
        }

        exec('git add -A');
        exec("git commit -m 'Version $newVersion - $date'");
        exec("git tag $newVersion");
        exec('git push');
        exec('git push --tags');
    }
}
