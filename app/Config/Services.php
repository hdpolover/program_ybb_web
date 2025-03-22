<?php

namespace Config;

use CodeIgniter\Config\BaseService;

/**
 * Services Configuration file.
 *
 * Services are simply other classes/libraries that the system uses
 * to do its job. This is used by CodeIgniter to allow the core of the
 * framework to be swapped out easily without affecting the usage within
 * the rest of your application.
 *
 * This file holds any application-specific services, or service overrides
 * that you might need. An example has been included with the general
 * method format you should use for your service methods. For more examples,
 * see the core Services file at system/Config/Services.php.
 */
class Services extends BaseService
{
    /*
     * public static function example($getShared = true)
     * {
     *     if ($getShared) {
     *         return static::getSharedInstance('example');
     *     }
     *
     *     return new \CodeIgniter\Example();
     * }
     */

    /**
     * Create a new Image Compression service
     */
    public static function imageCompression($getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('imageCompression');
        }

        // Check if Intervention/Image is installed
        if (!class_exists('\Intervention\Image\ImageManager')) {
            // Try to autoload the class
            if (file_exists(ROOTPATH . 'vendor/autoload.php')) {
                require_once ROOTPATH . 'vendor/autoload.php';
            }
        }

        if (class_exists('\Intervention\Image\ImageManager')) {
            // Check if we're using Intervention/Image v3+
            if (method_exists('\Intervention\Image\ImageManager', 'withDriver')) {
                $manager = new \Intervention\Image\ImageManager('gd');
                return $manager->withDriver('gd');
            } else {
                // Fallback for older versions
                $manager = new \Intervention\Image\ImageManager('gd');
                return $manager;
            }
        }

        return new \stdClass(); // Placeholder for service
    }
}
