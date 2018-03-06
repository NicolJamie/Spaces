<?php

namespace NicolJamie\Spaces;

use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;

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
        if ( ! class_exists('Aws\S3\S3Client')) {
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

        // Determine endPoint
        $this->config['endPoint'] = 'https://' . $config['space'] . '.' . $conig['region'] . '.' .$config['host'];

        dd($this->config);

        $this->config = $config;
    }

    /**
     * bootConnection
     * Boots the main connection to the space
     * @return string
     */
    protected function bootConnection()
    {
        try {
            $s3 = new S3Client([
                'region' => $this->config['regiion'],
                'version' => 'latest',
                'endpoint' => $endpoint,
                'credentials' => [
                    'key'    => $access_key,
                    'secret' => $secret_key,
                ],
                'bucket_endpoint' => true
            ]);
        } catch (S3Exception $exception) {
            return $exception->getMessage();
        }
    }
}