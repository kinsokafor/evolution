<?php

namespace EvoPhp\Api\FileHandling;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use EvoPhp\Api\Config;
use EvoPhp\Api\Operations;

class Files {

    /**
     * summary
     */
    private $fs;

    private $config;

    private const phpFileUploadErrors = array(
        0 => 'There is no error, the file uploaded with success',
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk.',
        8 => 'A PHP extension stopped the file upload.',
    );

    public function __construct()
    {
        $this->fs = new Filesystem();
        $this->config = new Config;
    }

    public function processFile(array $file) {
        if(!isset($file['data'])) return false;
        $default = [
            "processor" => "UploadFile",
            "path" => "Uploads",
            "saveAs" => ""
        ];
        $file = array_merge($default, $file);
        if(method_exists($this, $file['processor'])) {
            return $this->{$file['processor']}($file['data'], $file['path'], $file['saveAs']);
        } else return false;
    }

    public function uploadBase64Image($data, $path, $saveAs = "") {
        if($data != '') {
            $saveAs = $saveAs == "" ? Operations::randomString(4).time() : $saveAs;
            $dataArr = explode(';', $data);
            if(!isset($dataArr[1])) return false;
            list($type, $data) = $dataArr;
            list(, $data)      = explode(',', $data);
            list(, $type) = explode(":", $type);
            list($saveAs, ) = explode(".", $saveAs);
            $data = base64_decode($data);
            $this->fs->mkdir(Path::canonicalize($path));
            $path = Path::canonicalize($path ."/". $saveAs . "." . $this->mimeToExtension($type));
            file_put_contents($path, $data);
            return $this->getRoot()."/$path";
        } else return false;
    }

    function UploadFile($file, $path, $saveAs = "", $allowedTypes = null, $maxSize = null) {
        if (!isset($file['name'])) {
            http_response_code(400);
            return 'Upload failed';
        }

        if ($allowedTypes != null && !in_array($file['type'], $allowedTypes)) {
            http_response_code(400);
            return 'Wrong file type';
        }
        
        if ($maxSize != null && $file['size'] > $maxSize) {
            http_response_code(400);
            return 'File too large';
        }

        if ($file['error'] > 0) {
            http_response_code(400);
            return $this::phpFileUploadErrors[$file['error']];
        }

        $this->fs->mkdir(Path::canonicalize($path));
        
        $saveAs = $saveAs == "" ? $file['name'] : $saveAs.".".$this->mimeToExtension($file['type']);

        $path = Path::canonicalize($path ."/". $saveAs);

        if(move_uploaded_file($file['tmp_name'],$path)) {
            return $this->getRoot()."/$path";
        } else {
            http_response_code(400);
            return 'Failed to save file';
        }
    }

    public function unlink($file) {
        $file = str_replace($this->getRoot(), "", $file);
        unlink(Path::canonicalize(ABSPATH.$file));
    }

    private function getRoot() {
        return ($this->config->mode == "development" ? $this->config->devRoot : $this->config->root);
    }

    public function mimeToExtension($mime) {
        $mime_map = require("MimeTypes.php");
        return isset($mime_map[$mime]) ? $mime_map[$mime] : $mime;
    }

    public function deleteDir($dirPath) {
        $instance = new self;
        $dirPath = Path::canonicalize($dirPath);
        if (! is_dir($dirPath)) {
            return;
            // throw new \InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = \glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                $instance->deleteDir($file);
            } else {
                \unlink($file);
            }
        }
        \sleep(5);
        \rmdir($dirPath);
    }
}