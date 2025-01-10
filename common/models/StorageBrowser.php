<?php

namespace common\models;

use Symfony\Component\Finder\Finder;

/**
 * Storage browser model
 */
class StorageBrowser
{
    private $finder;
    private $path_url;

    public $args = array();
    public $config = array();

    /**
     * Configuration
     *
     * @param array $args
     */
    public function config($args = array())
    {
        $this->args = $args;
    }

    /**
     * Get files
     *
     * @param [type] $path
     * @param boolean $path_url
     * @return array
     */
    public function getFiles($path, $path_url = false)
    {
        $output = array();
        $path_name = $this->path_convert($path);

        if (is_dir($path_name)) {
            $this->path_url = $path_url;
            $this->run($path_name, 'files');

            if ($this->finder->hasResults()) {
                foreach ($this->finder as $file) {
                    if ($file) {
                        $output[] = $this->_item($file);
                    }
                }
            }
        }

        return $output;
    }

    /**
     * Get directories
     *
     * @param [type] $path
     * @param boolean $path_url
     * @return array
     */
    public function getFolders($path, $path_url = false)
    {
        $output = array();
        $path_name = $this->path_convert($path);

        if (is_dir($path_name)) {
            $this->path_url = $path_url;
            $this->run($path_name, 'directories');

            if ($this->finder->hasResults()) {
                foreach ($this->finder as $folder) {
                    if ($folder) {
                        $output[] = $this->_item($folder);
                    }
                }
            }
        }

        return $output;
    }

    /**
     * Init model
     */
    private function init()
    {
        $args = $this->args;
        $allowed = self::allowed_file_types();

        $exclude_types = array();
        $allowed_types = array_merge($allowed['docs'], $allowed['files'], $allowed['images'], $allowed['media'], $allowed['audio']);

        // Check allowed types
        if ($this->arrayValue($args, 'allowed_types')) {
            $alw_types = $args['allowed_types'];

            if (is_string($alw_types) && isset($allowed[$alw_types])) {
                $allowed_types = $allowed[$alw_types];
            } elseif (is_array($alw_types) && $alw_types) {
                $allowed_types = array();
                foreach ($alw_types as $value) {
                    if (isset($allowed[$value])) {
                        $allowed_types = array_merge($allowed[$value], $allowed_types);
                    } elseif (strpos($value, '*.') !== false) {
                        $allowed_types[] = $value;
                    }
                }
            }
        }

        // Check exclude types
        if ($this->arrayValue($args, 'exclude_types')) {
            $exc_types = $args['exclude_types'];

            if (is_string($exc_types) && isset($allowed[$exc_types])) {
                $exclude_types = $allowed[$exc_types];
            } elseif (is_array($exc_types) && $exc_types) {
                $exclude_types = array();
                foreach ($exc_types as $value) {
                    if (isset($allowed[$value])) {
                        $exclude_types = array_merge($allowed[$value], $exclude_types);
                    } elseif (strpos($value, '*.') !== false) {
                        $exclude_types[] = $value;
                    }
                }
            }
        }

        // Check ignore version control files
        $ignore_vcs = true;

        if ($this->arrayValue($args, 'ignore_vcs') === false) {
            $ignore_vcs = false;
        }

        // Check ignore unreadable dirs
        $ignore_unreadable_dirs = true;

        if ($this->arrayValue($args, 'ignore_unreadable_dirs') === false) {
            $ignore_unreadable_dirs = false;
        }

        // Check in path
        $in_path = false;

        if ($this->arrayValue($args, 'in_path')) {
            $in_path = $args['in_path'];
        }

        // Check not path
        $not_path = false;

        if ($this->arrayValue($args, 'not_path')) {
            $not_path = $args['not_path'];
        }

        // Check depth
        $depth = false;

        if ($this->arrayValue($args, 'depth')) {
            $depth = $args['depth'];
        }

        // Check size
        $size = false;

        if ($this->arrayValue($args, 'size')) {
            $size = $args['size'];
        }

        // Check date
        $date = false;

        if ($this->arrayValue($args, 'date')) {
            $date = $args['date'];
        }

        // Check sorting
        $sort_by = 'name';
        $sort_type = 'asc';

        if ($this->arrayValue($args, 'sort_by')) {
            $allowed_sort_by = ['name', 'type', 'accessed_time', 'changed_time', 'modified_time'];

            if (is_string($args['sort_by']) && in_array($args['sort_by'], $allowed_sort_by)) {
                $sort_by = $args['sort_by'];
            }
        }

        if ($this->arrayValue($args, 'sort_type')) {
            $allowed_sort_type = ['asc', 'desc'];

            if (is_string($args['sort_type']) && in_array($args['sort_type'], $allowed_sort_type)) {
                $sort_type = $args['sort_type'];
            }
        }

        $this->config = array(
            'allowed_types' => $allowed_types,
            'exclude_types' => $exclude_types,
            'depth' => $depth,
            'in_path' => $in_path,
            'not_path' => $not_path,
            'date' => $date,
            'size' => $size,
            'sort_by' => $sort_by,
            'sort_type' => $sort_type,
            'ignore_vcs' => $ignore_vcs,
            'ignore_unreadable_dirs' => $ignore_unreadable_dirs,
        );
    }

    /**
     * Run finder
     *
     * @param string $path
     * @param boolean $type
     * @return mixed
     */
    private function run($path, $type = false)
    {
        $this->init();

        if (is_dir($path)) {
            $check_file_types = false;
            $this->finder = new Finder();

            if ($this->config['ignore_unreadable_dirs'] === true) {
                $this->finder->ignoreUnreadableDirs();
            }

            if ($type == 'files') {
                $check_file_types = true;
                $this->finder->files();
            } elseif ($type == 'directories') {
                $this->finder->directories();
            }

            $this->finder->in($path);

            if ($this->config['in_path']) {
                $this->finder->path($this->config['in_path']);
            }

            if ($this->config['not_path']) {
                $this->finder->notPath($this->config['not_path']);
            }

            if ($this->config['depth']) {
                $this->finder->depth($this->config['depth']);
            }

            if ($check_file_types) {
                if ($this->config['allowed_types']) {
                    $this->finder->name($this->config['allowed_types']);
                }

                if ($this->config['exclude_types']) {
                    $this->finder->notName($this->config['exclude_types']);
                }
            }

            if ($this->config['date']) {
                $this->finder->date($this->config['date']);
            }

            if ($this->config['size']) {
                $this->finder->size($this->config['size']);
            }

            if ($this->config['sort_by']) {
                switch ($this->config['sort_by']) {
                    case 'name':
                        $this->finder->sortByName();
                        break;
                    case 'type':
                        $this->finder->sortByType();
                        break;
                    case 'accessed_time':
                        $this->finder->sortByAccessedTime();
                        break;
                    case 'changed_time':
                        $this->finder->sortByChangedTime();
                        break;
                    case 'modified_time':
                        $this->finder->sortByModifiedTime();
                        break;
                }

                if ($this->config['sort_type'] == 'desc') {
                    $this->finder->reverseSorting();
                }
            }

            if ($this->config['ignore_vcs'] === true) {
                $this->finder->ignoreVCSIgnored(true);
            } else {
                $this->finder->ignoreVCS(false);
            }
        }
    }

    /**
     * Finder item
     *
     * @param [type] $value
     * @return array
     */
    private function _item($value)
    {
        $output = array(
            'name' => $value->getFilename(),
            'file_url' => $this->file_url($value->getRelativePathname()),
            'path' => $value->getRealPath(),
            'relative_path_name' => $value->getRelativePathname(),
            'size' => $this->fileSizeConvert($value->getSize()),
            'size_bytes' => $value->getSize(),
            'extension' => $value->getExtension(),
            'type' => $value->getType(),
            'permissions' => substr(sprintf('%o', $value->getPerms()), -4),
            'modified_time' => $value->getMTime(),
            'access_time' => $value->getATime(),
            'is_link' => $value->isLink(),
            'is_writable' => $value->isWritable(),
            'is_writable' => $value->isWritable(),
        );

        return $output;
    }

    /**
     * Array value
     *
     * @param boolean $array
     * @param boolean $key
     * @param boolean $default
     * @return mixed
     */
    private function arrayValue($array = false, $key = false, $default = false)
    {
        if (is_array($array) && isset($array[$key])) {
            return $array[$key];
        }

        return $default;
    }

    /**
     * Path string fix
     *
     * @param string $name
     * @return string
     */
    private function path_convert($name)
    {
        $output = str_replace('/', DIRECTORY_SEPARATOR, trim($name));

        return $output;
    }

    /**
     * Item url convert
     *
     * @param string $url
     * @return string
     */
    private function file_url($url)
    {
        $url = str_replace('\\', '/', trim($url));
        $path_url = str_replace('\\', '/', trim($this->path_url));
        $output = trim($path_url, '/') . '/' . trim($url, '/');

        return $output;
    }

    /**
     * Item size convert
     *
     * @param [type] $bytes
     * @return string
     */
    private function fileSizeConvert($bytes)
    {
        $result = '0 B';

        $bytes = floatval($bytes);
        $sizes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4),
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3),
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2),
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024,
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1,
            ),
        );

        foreach ($sizes as $size) {
            if ($bytes >= $size["VALUE"]) {
                $result = $bytes / $size["VALUE"];
                $result = strval(round($result, 2)) . " " . $size["UNIT"];
                break;
            }
        }

        return $result;
    }

    /**
     * Icon convert
     *
     * @param [type] $file
     * @return string
     */
    public static function iconConvert($file)
    {
        $output = 'ri-file-2-fill';

        $ppt = ['ppt', 'pptx'];
        $xls = ['xls', 'xlsx'];
        $files = ['csv', 'txt', '.xps'];
        $audio = ['mp3', 'ogg', 'wav', 'wma', 'wmv'];
        $doc = ['doc', 'docm', 'docx', 'ods', 'odt', 'rtf', 'wps'];
        $images = ['ai', 'bmp', 'indd', 'eps', 'ico', 'jpg', 'jpeg', 'gif', 'png', 'psd', 'tiff', 'svg'];
        $videos = ['mp4', 'avi', 'flv', 'mpg', 'mpeg', '3gp', 'swf', 'wmv', 'webm'];

        if ($file && $file['extension']) {
            $extension = $file['extension'];

            if (in_array($extension, $audio)) {
                $output = 'ri-music-2-fill';
            } elseif (in_array($extension, $doc)) {
                $output = 'ri-file-word-fill';
            } elseif (in_array($extension, $ppt)) {
                $output = 'ri-file-ppt-2-fill';
            } elseif (in_array($extension, $images)) {
                $output = 'ri-image-2-fill';
            } elseif (in_array($extension, $videos)) {
                $output = 'ri-movie-fill';
            } elseif (in_array($extension, $xls)) {
                $output = 'ri-file-excel-fill';
            } elseif (in_array($extension, $files)) {
                $output = 'ri-file-text-fill';
            } elseif ($extension == 'pdf') {
                $output = 'ri-file-ppt-fill';
            }
        }

        return $output;
    }

    /**
     * Check image preview
     *
     * @param [type] $file
     * @return string
     */
    public static function checkImagePreview($file)
    {
        $output = false;

        $images = ['ico', 'jpg', 'jpeg', 'gif', 'png', 'svg'];

        if ($file && $file['extension']) {
            $extension = $file['extension'];

            if (in_array($extension, $images)) {
                $output = $file['file_url'];
            }
        }

        return $output;
    }

    /**
     * Path breadcrumb
     *
     * @param string $path
     * @param string $path_name
     * @return string
     */
    public static function pathBreadcrumb($path, $path_name)
    {
        $output = array('/' => $path_name);

        if ($path && $path != '/') {
            $path = trim($path, '/');
            $array = explode('/', $path);

            if ($array) {
                $key = '';
                foreach ($array as $value) {
                    $key .= '/' . $value;
                    $output[$key] = _strtotitle($value);
                }
            }
        }

        return $output;
    }

    /**
     * Path name check and convert
     *
     * @param string $name
     * @return string
     */
    public static function path_name_convert($name)
    {
        $output = str_replace('/', DIRECTORY_SEPARATOR, trim($name));

        return $output;
    }

    /**
     * Allowed file types
     *
     * @param string $name
     * @return array
     */
    public static function allowed_file_types($type = false)
    {
        $allowed['audio'] = ['*.mp3', '*.ogg', '*.wav', '*.wma', '*.wmv'];
        $allowed['docs'] = ['*.csv', '*.pdf', '*.txt', '*.doc', '*.docm', '*.docx', '*.ods', '*.odt', '*.rtf', '*.ppt', '*.pptx', '*.xls', '*.xlsx', '*.wps', '.xps'];
        $allowed['files'] = ['*.css', '*.eot', '*.js', '*.json', '*.hss', '*.html', '*.htm', '*.jhtml', '*.less', '*.pptx', '*.sass', '*.ttf', '*.woff', '*.woff2', '*.xls', '*.xlsx', '*.xhtml'];
        $allowed['images'] = ['*.ai', '*.bmp', '*.indd', '*.eps', '*.ico', '*.jpg', '*.jpeg', '*.gif', '*.png', '*.psd', '*.tiff', '*.svg'];
        $allowed['media'] = ['*.mp4', '*.avi', '*.flv', '*.mpg', '*.mpeg', '*.3gp', '*.swf', '*.wmv', '*.webm'];

        if ($type == 'array') {
            $output = array();

            foreach ($allowed as $value) {
                $output = array_merge($value, $output);
            }

            return $output;
        } elseif (isset($allowed[$type])) {
            return $allowed[$type];
        }

        return $allowed;
    }

    /**
     * Delete
     *
     * @param string $item
     * @return void
     */
    public static function delete($item)
    {
        if (is_file($item)) {
            unlink($item);
        } elseif (is_dir($item)) {
            if (substr($item, strlen($item) - 1, 1) != '/') {
                $item .= '/';
            }

            $files = glob($item . '*', GLOB_MARK);

            foreach ($files as $file) {
                if (is_dir($file)) {
                    self::delete($file);
                } else {
                    unlink($file);
                }
            }

            rmdir($item);
        }
    }
}
