<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class InstallImageLibrary extends BaseCommand
{
    protected $group       = 'Custom';
    protected $name        = 'install:image';
    protected $description = 'Installs the Intervention/Image library';

    public function run(array $params)
    {
        CLI::write('Installing Intervention/Image library...', 'yellow');
        
        // Check if composer is available
        exec('composer -v', $output, $returnVal);
        if ($returnVal !== 0) {
            CLI::error('Composer is not available. Please install Composer first.');
            return;
        }
        
        // Run the composer require command
        CLI::write('Running: composer require intervention/image', 'green');
        passthru('composer require intervention/image');
        
        // Create the cache directory
        $cacheDir = ROOTPATH . 'writable/cache/images/';
        if (!is_dir($cacheDir)) {
            if (mkdir($cacheDir, 0755, true)) {
                CLI::write("Created cache directory: {$cacheDir}", 'green');
            } else {
                CLI::error("Failed to create cache directory: {$cacheDir}");
            }
        } else {
            CLI::write("Cache directory already exists: {$cacheDir}", 'yellow');
        }
        
        CLI::write('Installation complete!', 'green');
    }
}
