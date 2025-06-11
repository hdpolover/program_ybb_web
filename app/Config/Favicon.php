<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Favicon extends BaseConfig
{
    /**
     * Path to the logo file relative to the public directory
     * Change this value to update the logo used for favicons
     */
    public string $logoPath = 'assets/logo/logo.png';

    /**
     * Cache directory for generated favicons
     */
    public string $cacheDir = 'assets/favicon';

    /**
     * Application name for manifest and meta tags
     */
    public string $appName = 'Youth Break the Boundaries';

    /**
     * Short name for PWA
     */
    public string $shortName = 'YBB';

    /**
     * Theme color for browser and PWA
     */
    public string $themeColor = '#ffffff';

    /**
     * Background color for PWA
     */
    public string $backgroundColor = '#ffffff';
}
