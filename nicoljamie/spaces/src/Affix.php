<?php

namespace NicolJamie\Spaces;

class Affix
{
    /**
     * Affix constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        if (!class_exists('Aws\S3\S3Client')) {
            throw new \Exception('AWS SDK not found, please require');
        }

        $config = config('spaces');
        
        if (!is_array($config)) {
            throw new \Exception('Config File was not found, please create one.');
        }
        
        print_r($config);
    }
}