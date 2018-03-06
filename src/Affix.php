<?php

namespace NicolJamie\Spaces;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

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

        $this->config = $config;
        $this->endPoint();

        return $this->config;
    }

    /**
     * setSpace
     * Override the space
     * @param null $space
     *
     * @return string
     */
    public function setSpace($space = null)
    {
        if (!is_null($space) && is_string($space)) {
            $this->config['space'] = $space;
            $this->endPoint();
        }
    }

    /**
     * setRegion
     * Override the region
     * @param null $region
     */
    public function setRegion($region = null)
    {
        if  (is_null($region) && is_string($region)) {
            $this->config['region'] = $region;
            $this->endPoint();
        }
    }

    /**
     * endPoint
     * Works out end point from config
     * @return string
     */
    protected function endPoint()
    {
        $this->config['endPoint'] = 'https://' . $this->config['space'] . '.' . $this->config['region'] . '.' . $this->config['host'];
    }

    /**
     * bootConnection
     * Boots the main connection to the space
     * @return S3Client|string
     */
    protected function bootConnection()
    {
        try {
            $s3 = new S3Client([
                'region' => $this->config['region'],
                'version' => 'latest',
                'endpoint' => $this->config['endPoint'],
                'credentials' => [
                    'key'    => $this->config['accessKey'],
                    'secret' => $this->config['secretKey'],
                ],
                'bucket_endpoint' => true
            ]);
        } catch (S3Exception $exception) {
            return $exception->getMessage();
        }

        return $s3;
    }
}