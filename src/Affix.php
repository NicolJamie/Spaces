<?php

namespace NicolJamie\Spaces;

class Affix
{
    /**
     * details
     * connection details
     * @var array
     */
    protected $details = [
        'accessKey',
        'secretKey',
        'space',
        'region',
        'host'
    ];

    /**
     * config
     * Config of details
     * @var array
     */
    public $config = [];

    /**
     * Affix constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        if (!class_exists('Aws\S3\S3Client')) {
            throw new \Exception('AWS SDK not found, please require');
        }

        $this->checkConfig();
    }

    /**
     * checkConfig
     * @throws \Exception
     */
    protected function checkConfig()
    {
        $config = config('spaces');

        if (!is_array($config)) {
            throw new \Exception('Config File was not found, please create one.');
        }

        foreach ($this->details as $detail) {
            if (is_null($config[$detail])) {
                throw new \Exception($detail . ' value has not been set, please set it');
            }
        }

        return $this->config = $config;
    }
}