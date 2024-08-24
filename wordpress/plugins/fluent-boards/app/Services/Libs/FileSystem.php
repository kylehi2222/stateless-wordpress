<?php

namespace FluentBoards\App\Services\Libs;

use FluentBoards\Framework\Support\Arr;

class FileSystem
{
    protected $subDir = '';

    public function _setSubDir($subDir)
    {
        $this->subDir = $subDir;
        return $this;
    }
    /**
     * Read file content from custom upload dir of this application
     * @return string [path]
     */
    public function _get($file)
    {
        $arr = explode('/', $file);
        $fileName = end($arr);
        if ($this->subDir) {
            $fileName = $this->subDir . DIRECTORY_SEPARATOR . $fileName;
        }
        return file_get_contents(
            $this->getDir() . DIRECTORY_SEPARATOR . $fileName
        );
    }

    /**
     * Get custom upload dir name of this application
     * @return string [directory path]
     */
    public function _getDir()
    {
        $uploadDir = wp_upload_dir();

        $fbsUploadDir = apply_filters('fluent_boards/upload_folder_name', FLUENT_BOARDS_UPLOAD_DIR);

        if ($this->subDir) {
            return $uploadDir['basedir'] . DIRECTORY_SEPARATOR . $fbsUploadDir. DIRECTORY_SEPARATOR. $this->subDir;
        }

        return $uploadDir['basedir'] . DIRECTORY_SEPARATOR . $fbsUploadDir;
    }

    /**
     * Get absolute path of file using custom upload dir name of this application
     * @return string [file path]
     */
    public function _getAbsolutePathOfFile($file)
    {
        return $this->_getDir() . DIRECTORY_SEPARATOR . $file;
    }

    /**
     * Upload files into custom upload dir of this application
     * @return array
     */
    public function _uploadFromRequest()
    {
        return $this->_put(FluentBoards('request')->files());
    }

    /**
     * Upload files into custom upload dir of this application
     * @param array $files
     * @return array
     */
    public function _put($files)
    {
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        $this->overrideUploadDir();

        $uploadOverrides = ['test_form' => false];

        foreach ((array)$files as $file) {
            $filesArray = $file->toArray();
            $extraData = Arr::only($filesArray, ['name', 'size']);
            $uploadsData = \wp_handle_upload($filesArray, $uploadOverrides);
            $uploadedFiles[] = array_merge($extraData, $uploadsData);
        }

        return $uploadedFiles;
    }

    /**
     * Delete a file from custom upload directory of this application
     * @param array $files
     * @return void
     */
    public function _delete($files)
    {
        $files = (array)$files;

        foreach ($files as $file) {
            $arr = explode('/', $file);
            $fileName = end($arr);
            @unlink($this->getDir() . '/' . $fileName);
        }
    }

    /**
     * Register filters for custom upload dir
     */
    public function _overrideUploadDir()
    {
        add_filter('wp_handle_upload_prefilter', function ($file) {
            add_filter('upload_dir', [$this, '_setCustomUploadDir']);

            add_filter('wp_handle_upload', function ($fileinfo) {
                remove_filter('upload_dir', [$this, '_setCustomUploadDir']);
                $fileinfo['file'] = basename($fileinfo['file']);
                return $fileinfo;
            });

            return $this->_renameFileName($file);
        });
    }

    /**
     * Set plugin's custom upload dir
     * @param array $param
     * @return array $param
     */
    public function _setCustomUploadDir($param)
    {
        $fbsUploadDir = apply_filters('fluent_boards/upload_folder_name', FLUENT_BOARDS_UPLOAD_DIR);

        if ($this->subDir) {
            $fbsUploadDir .= DIRECTORY_SEPARATOR . $this->subDir;
            if (!is_dir($param['basedir'] . $fbsUploadDir)) {
                @mkdir($param['basedir'] . $fbsUploadDir, 0755);
            }
        }

        $param['url'] = $param['baseurl'] . DIRECTORY_SEPARATOR . $fbsUploadDir;

        $param['path'] = $param['basedir'] . DIRECTORY_SEPARATOR . $fbsUploadDir;

        return $param;
    }

    /**
     * Rename the uploaded file name before saving
     * @param array $file
     * @return array $file
     */
    public function _renameFileName($file)
    {
        $currentTimeStamp = (new \DateTimeImmutable())->getTimestamp();
        $prefix = $currentTimeStamp . '-';
        $prefix = apply_filters('fluent_boards/uploaded_file_name_prefix', $prefix);
        $file['name'] = $prefix . $file['name'];

        return $file;
    }

    public function _deleteDir($dir)
    {
        $dir = $this->getDir() . DIRECTORY_SEPARATOR . $dir;
        if (is_dir($dir)) {
            $this->deleteContents($dir);
        }
    }

    /**
     * Delete a directory and its content
     * Lead Note: this method should be called via scheduler or queue
     * @param string $dir
     * @return bool
     */
    private function deleteContents($dir)
    {
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            if(is_dir("$dir/$file")) {
                // Recursively delete subdirectory
                $this->deleteContents("$dir/$file");
            } else {
                // Delete file
                unlink("$dir/$file");
            }
        }
        // Delete the directory itself. If the directory is not empty, it will not be deleted.
        return rmdir($dir);
    }

    public static function __callStatic($method, $params)
    {
        $instance = new static;

        return call_user_func_array([$instance, $method], $params);
    }

    public function __call($method, $params)
    {
        $hiddenMethod = "_" . $method;

        $method = method_exists($this, $hiddenMethod) ? $hiddenMethod : $method;

        return call_user_func_array([$this, $method], $params);
    }
}
