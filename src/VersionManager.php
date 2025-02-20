<?php

namespace RPurinton\Tagit;

class VersionManager {
    public function incrementVersion($version) {
        $parts = explode('.', $version);
        $parts[count($parts) - 1]++;
        return implode('.', $parts);
    }

    public function updateComposerJson() {
        $composerFile = 'composer.json';
        if (!file_exists($composerFile)) {
            echo "composer.json not found in the current directory.\n";
            exit(1);
        }

        $composerData = json_decode(file_get_contents($composerFile), true);
        if (!isset($composerData['version'])) {
            echo "Version not found in composer.json.\n";
            exit(1);
        }

        $newVersion = $this->incrementVersion($composerData['version']);
        $composerData['version'] = $newVersion;
        file_put_contents($composerFile, json_encode($composerData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return $newVersion;
    }

    public function gitOperations($newVersion) {
        $date = date('Y-m-d');
        exec('git add -A');
        exec("git commit -m 'Version $newVersion - $date'");
        exec("git tag $newVersion");
        exec('git push');
        exec('git push --tags');
    }
}
