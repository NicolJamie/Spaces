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
        
        $this->checkConfig($config);
    }

    /**
     * checkConfig
     *
     * @param $config
     *
     * @return mixed
     * @throws \Exception
     */
    protected function checkConfig($config)
    {
        foreach ($this->details as $detail) {
            if (!isset($config[$detail])) {
                throw new \Exception($detail . ' is not set, plesae set it');
            }

            if (is_null($config[$detail])) {
                throw new \Exception($detail . ' value has not been set, please set it');
            }
        }

        return $this->config = $config;
    }
}