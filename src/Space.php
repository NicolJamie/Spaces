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
     * connection
     * @var \Aws\S3\S3Client|string
     */
    public $connection;

    /**
     * Space constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();

        $this->connection = $this->bootConnection();
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
            $list = $this->connection->listBuckets();
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

        try {
            $new = $this->connection->createBucket([
                'Bucket' => $args['space']
            ]);

            $this->connection->waitUntil('BucketExists', [
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
        SpaceException::inspect($args, 'upload');

        if ( ! isset($args['saveAs'])) {
            $args['saveAs'] = $args['pathToFile'];
        }

        try {
            $upload = $this->connection->upload($this->config['space'], $args['saveAs'],
                fopen($args['pathToFile'], 'r+'), $args['access'] ? 'public-read' : 'private');

            $this->connection->waitUntil('ObjectExists', [
                'Bucket' => $this->config['space'],
                'Key'    => $args['saveAs']
            ]);

            return $upload;

        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * @param array $args
     * @param bool $upload
     *
     * @return bool|\Exception
     * @throws \Exception
     */
    public function directory($args = [], $upload = true)
    {
        SpaceException::inspect($args, $upload ? 'uploadDirectory' : 'downloadDirectory');

        try {
            switch ($upload) {
                case true:
                    $params['before'] = function (\Aws\Command $command) {
                        $command['ACL'] = 'public-read';
                    };

                    return $this->connection->uploadDirectory(
                        $args['directory'],
                        $this->config['space'],
                        $args['prefix'],
                        $params
                    );
                    break;
                case false:
                    return $this->connection->downloadBucket(
                        $args['directory'],
                        $this->config['space'],
                        $args['prefix']
                    );
                    break;
                default:
                    return false;
            }
        } catch (\Exception $exception) {
            return $exception;
        }

        return true;
    }

    /**
     * fetch
     * Fetches a file from the space
     * fileName - Key located on CDN
     * saveAS - Path to download the file to
     *
     * @param array $args
     *
     * @param bool $saveAs
     *
     * @return string
     * @throws \Exception
     */
    public function fetch($args = [], $saveAs = false)
    {
        SpaceException::inspect($args, 'fetch');

        try {
            $result = $this->connection->getObject([
                'Bucket' => $this->config['space'],
                'Key'    => $args['fileName'],
                'SaveAs' => $args['saveAs']
            ]);

        } catch (\Exception $exception) {
            return $exception->getMessage();
        }

        return $saveAs ? $args['saveAs'] : $result;
    }

    /**
     * remove
     * Removes a file from the given space
     *
     * @param array $args
     *
     * @return \Aws\Result
     * @throws \Exception
     */
    public function remove($args = [])
    {
        SpaceException::inspect($args, 'remove');

        return $this->connection->deleteObject([
            'Bucket' => $this->config['space'],
            'Key'    => $args['file']
        ]);
    }

    /**
     * set
     * Sets a setting for the space
     *
     * @param array $args
     *
     * @throws \Exception
     */
    public function set($args = [])
    {
        SpaceException::inspect($args, 'set');

        if ( ! in_array($args['set'], [
            'space',
            'region',
        ])) {
            throw new \Exception('there is no setting for this.');
        }

        $set = ucfirst(strtolower($args['set']));
        $this->{'set' . $set}($args['value']);
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