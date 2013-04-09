<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * 
 */
class Bronto_Util_Retryer_FileRetryer implements Bronto_Util_Retryer_RetryerInterface
{
    /**
     * @var string
     */
    protected $_path;

    /**
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        $this->_path = sys_get_temp_dir();

        if (isset($options['path']) && !empty($options['path'])) {
            if (@is_dir($options['path'])) {
                $this->_path = $options['path'];
            } else {
                // Attempt to make it...
                if (@mkdir($options['path'], 0766, true)) {
                    $this->_path = $options['path'];
                }
            }
        }
    }

    /**
     * @param string $filename
     * @return string
     */
    public function getPath($filename = null)
    {
        return $this->_path . (!empty($filename) ? DIRECTORY_SEPARATOR . $filename : '');
    }

    /**
     * @param Bronto_Api_Object $object
     * @param int $attempts
     * @return string
     */
    public function store(Bronto_Api_Object $object, $attempts = 0)
    {
        if (!@is_dir($this->_path) && !@mkdir($this->_path, 0777, true)) {
            throw new Bronto_Util_RetryerException(sprintf('The Retryer path is not a directory: %s', $this->_path));
        } else {
            if (!@is_writable($this->_path)) {
                throw new Bronto_Util_RetryerException(sprintf('The Retryer path is not a writable: %s', $this->_path));
            }
        }

        $filename = sprintf(
            '%s_%s_%s_%d.php',
                strtolower($object->getName()),
                str_replace('.', '', microtime()),
                str_pad(rand(0, 1000), 4, ' ', STR_PAD_LEFT),
                (int) $attempts
        );
        $filename = str_replace(' ', '', $filename);
        $fh = @fopen($this->_path . DIRECTORY_SEPARATOR . $filename, 'w');
        $serialized = serialize($object);
        $result = @fwrite($fh, sprintf("<?php return unserialize('%s'); ?>", $serialized));
        @fclose($fh);

        return $result ? $filename : false;
    }

    /**
     * @param string $filePath
     * @return Bronto_Api_Object
     */
    protected function _loadObject($filePath)
    {
        if (!@file_exists($filePath)) {
            throw new Bronto_Util_RetryerException(sprintf('Failed to retry file path: %s', $filePath));
        }

        $parts = explode('_', $filePath);
        $parts = explode('.', $parts[count($parts) - 1]);

        return array(
            'object'   => include_once $filePath,
            'attempts' => (int) $parts[0],
        );
    }

    /**
     * @param string $filePath
     * @return Bronto_Api_Rowset
     */
    public function attempt($filePath)
    {
        $result   = $this->_loadObject($filePath);
        $object   = $result['object'];
        $attempts = $result['attempts'];
        $method   = $object->getLastRequestMethod();
        $data     = $object->getLastRequestData();

        try {
            $rowset = $object->doRequest($method, $data, true);
        } catch (Exception $e) {
            $this->store($object, $attempts++);
            return false;
        }

        $this->remove($filePath);

        return $rowset;
    }

    /**
     * @param string $filePath
     * @return bool
     */
    public function remove($filePath)
    {
        return @unlink($filePath);
    }
}
