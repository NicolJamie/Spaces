<?php

namespace NicolJamie\Spaces;

use Aws\S3\Exception\S3Exception;

class Space extends Affix
{
    /**
     * instance
     * @var null
     */
    public static $instance = null;

    /**
     * space
     * @var \Aws\S3\S3Client|string
     */
    public $space;

    /**
     * call
     * @var
     */
    public $call;

    /**
     * Space constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * list
     * List all Spaces within the given region
     *
     * @param bool $clean
     *
     * @return \Aws\Result|null|string
     */
    public function list($clean = false)
    {
        $list = null;

        try {
            $list = $this->bootConnection()->listBuckets();
        } catch (S3Exception $exception) {
            return $exception->getMessage();
        }

        return $list->toArray();
    }

    /**
     * create
     * Create a new space within the region
     *
     * @param array $args
     *
     * @return array|string
     * @throws \Exception
     */
    public function create($args = [])
    {
        SpaceException::inspect($args, 'create');

        $this->set('space', $args['space']);

        $connection = $this->bootConnection();

        try {
            $new = $connection->createBucket([
                'Bucket' => $args['space']
            ]);

            $connection->waitUntil('BucketExists', [
                'Bucket' => $args['space']
            ]);

        } catch (S3Exception $exception) {
            return $exception->getMessage();
        }

        return $new;
    }

    /**
     * upload
     * pathToFile - Path of tmp/file
     * access - public (true) or private (false) {boolean}
     * saveAs - custom name of file after upload
     *
     * @param array $args
     *
     * @return string
     * @throws \Exception
     */
    public function upload($args = [])
    {
        if (empty($args)) {
            throw new  \Exception('Arguments array cannot be emtpy');
        }

        if (!isset($args['pathToFile']) || empty($args['pathToFile'])) {
            throw new \Exception('you have to supply the path to the file in order to upload');
        }

        if (!is_bool($args['access'])) {
            throw new \Exception('this can only be a boolean ');
        }

        if (!isset($args['saveAs'])) {
            $args['saveAs'] = $args['pathToFile'];
        }

        $connection = $this->bootConnection();

        try {

            $upload = $connection->upload(
                $this->config['space'],
                $args['saveAs'],
                fopen($args['pathToFile'], 'r+'),
                $args['access'] ? 'public-read' : 'private'
            );

            $connection->waitUntil('ObjectExists', [
               'Bucket' => $this->config['space'],
               'Key' => $args['saveAs']
            ]);

            return $upload;

        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function fetch($args = [])
    {
        if (!isset($args['key'])) {
            throw new \Exception('No key has been specified');
        }



    }

    /**
     * remove
     * Removes a file from the given space
     * @param null $file
     *
     * @return \Aws\Result
     * @throws \Exception
     */
    public function remove($file = null)
    {
        if (is_null($file)) {
            throw new \Exception('File cannot be null');
        }

        return $this->bootConnection()->deleteObject([
            'Bucket' => $this->config['space'],
            'Key' => $file
        ]);
    }

    /**
     * set
     * Sets a setting for the space
     * @param null $set
     * @param null $value
     *
     * @throws \Exception
     */
    public function set($set = null, $value = null)
    {
        if (is_null($set)) {
            throw new \Exception('set cannot be null, please try again');
        }

        if (is_null($value)) {
            throw new \Exception('value cannot be null, please try again');
        }

        if (!in_array($set, [
            'space',
            'region'
        ])) {
            throw new \Exception('there is no setting for this.');
        }

        $set = ucfirst(strtolower($set));
        $this->{'set' . $set}($value);
    }

    /**
     * boot
     * @return Space
     * @throws \Exception
     */
    public static function boot()
    {
        $instance = self::$instance;

        if (is_null($instance)) {
            $instance = self::$instance = new self();
        }

        return $instance;
    }
}