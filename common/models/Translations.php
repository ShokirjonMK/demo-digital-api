<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "shops".
 *
 * @property int $id
 * @property path_key|null $path_key
 * @property string|null $lang_key
 * @property string|null $translations
 * @property string|null $logs
 * @property int|null $updated_on
 * @property int|null $updated_by
 */
class Translations extends \yii\db\ActiveRecord
{
    public $_logs = array();
    public $_translations = array();

    /**
     * Table name
     *
     * @return string
     */
    public static function tableName()
    {
        return 'translations';
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['translations', 'logs', 'changed_on'], 'safe'],
            [['updated_by'], 'integer'],
            [['path_key', 'lang_key'], 'string'],
            [['path_key', 'lang_key'], 'required'],
            [['updated_by'], 'default', 'value' => 0],
        ];
    }

    /**
     * Attribute labels
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'path_key' => _e('Path key'),
            'lang_key' =>  _e('Languages key'),
            'translations' =>  _e('Translations'),
            'logs' => _e('Logs'),
            'updated_on' => _e('Updated On'),
            'updated_by' => _e('Updated By'),
        ];
    }

    /**
     * Save action
     *
     * @param object $model
     * @param string $translations
     * @param string $path_key
     * @param object $language
     * @return void
     */
    public static function saveAction($model, $translations, $path_key, $language)
    {
        $output = json_output();
        $translation_path = self::getPathData($path_key);

        if (!$model) {
            $output['message'] = _e('No translation model was found.');
        } elseif (empty($translations)) {
            $output['message'] = _e('No translations found.');
        } elseif (!$language) {
            $output['message'] = _e('The target language key was not found.');
        } elseif (!$translation_path) {
            $output['message'] = _e('No translation data found.');
        } else {
            $locale = $language->locale;
            $save_to = array_value($translation_path, 'save_to');

            $model_translations = $model->translations;
            $translations = self::filterTransations($model_translations, $translations);
            $object = self::find()->where(['path_key' => $path_key, 'lang_key' => $language->lang_code])->one();

            if ($object) {
                $object->translations = $translations;
                $object->updated_on = date('Y-m-d H:i:s');
                $object->updated_by = current_user_id();
                $object->save(false);
            } else {
                $object = new self();
                $object->path_key = $path_key;
                $object->lang_key = $language->lang_code;
                $object->logs = array();
                $object->translations = $translations;
                $object->updated_on = date('Y-m-d H:i:s');
                $object->updated_by = current_user_id();
                $object->save(false);
            }

            // Save to path
            $save_dir = $save_to . DS . $locale . DS;

            if (!is_dir($save_to)) {
                mkdir($save_to, 0775);
            }

            if (!is_dir($save_dir)) {
                mkdir($save_dir, 0775);
            }

            if (is_dir($save_dir)) {
                $filename = $save_dir . 'app.php';
                $str = '<?php return ' . self::var_export($translations) . ';';
                file_put_contents($filename, $str);

                $output = json_output('success');
                $output['message'] = _e('The translations were saved successfully.');
            } else {
                $output['message'] = _e('Translation folder not created.');
            }
        }

        return $output;
    }

    /**
     * Filter translations
     *
     * @param array $model_translations
     * @param array $translations
     * @return array
     */
    private static function filterTransations($model_translations, $translations)
    {
        $output = array();

        if ($translations) {
            foreach ($translations as $translation_key => $translation_value) {
                if (isset($model_translations[$translation_key])) {
                    $_trs = $model_translations[$translation_key];

                    if ($_trs != $translation_value) {
                        $output[$translation_key] = $translation_value;
                    }
                }
            }
        }

        return $output;
    }

    /**
     * Get translation path data
     *
     * @param string $path_key
     * @return array
     */
    public static function getPathData($path_key)
    {
        return self::listPath($path_key);
    }

    /**
     * List translation path
     *
     * @return array
     */
    public static function listPath($key = null)
    {
        $array = array();
        $base_path = HOME_PATH . 'base' . DS;
        $common_path = HOME_PATH . 'common' . DS;
        $backend_path = BACKEND_PATH;
        $frontend_path = FRONTEND_PATH;

        $find_in = array(
            $base_path,
            $common_path . 'mail',
            $common_path . 'models',
            $common_path . 'widgets',
        );

        $array['backend-path'] = array(
            'name' => _e('Control panel'),
            'type' => 'system',
            'dir' => $backend_path,
            'save_to' => $backend_path . 'translations',
            'find_in' => array_merge(
                array(
                    $backend_path . 'controllers',
                    $backend_path . 'models',
                    $backend_path . 'views',
                    $backend_path . 'widgets',
                ),
                $find_in
            ),
            'exclude' => array(
                $base_path . 'helpers/frontend.php',
                $base_path . 'Frrontend.php',
                $base_path . 'FrrontendController.php',
            ),
        );

        // Get themes
        if (is_dir(THEMES_PATH)) {
            $theme_find_in = array(
                $base_path,
                $frontend_path . 'controllers',
                $frontend_path . 'mail',
                $frontend_path . 'models',
                $frontend_path . 'views',
            );

            foreach (glob(THEMES_PATH . '*', GLOB_ONLYDIR) as $dir) {
                $theme_dir = $dir . DS;
                $theme_config = $theme_dir . 'theme-config.php';
                $dirname = basename($dir);

                if (is_file($theme_config)) {
                    $theme_key = "theme-{$dirname}-path";
                    $theme_key = preg_replace('/\s+/', '-', $theme_key);
                    $theme_config = require $theme_config;
                    $theme_name = _e('Theme') . ': ' . array_value($theme_config, 'name', 'No theme name');

                    $array[$theme_key] = array(
                        'name' => $theme_name,
                        'type' => 'theme',
                        'dir' => $theme_dir,
                        'save_to' => $theme_dir . 'app/translations/',
                        'find_in' => array_merge(
                            array(
                                $theme_dir . 'app',
                                $theme_dir . 'layouts',
                                $theme_dir . 'partials',
                                $theme_dir . 'templates',
                                $theme_dir . 'views',
                            ),
                            $theme_find_in
                        ),
                        'exclude' => array(
                            $base_path . 'helpers/backend.php',
                            $base_path . 'Backend.php',
                            $base_path . 'BackendController.php',
                        ),
                    );
                }
            }
        }

        if (!is_null($key)) {
            $output = array();

            if (isset($array[$key])) {
                $output = $array[$key];
            }

            return $output;
        }

        return $array;
    }

    /**
     * Scan translation messages
     *
     * @param string $path_key
     * @return void
     */
    public function scanMessages($path_key)
    {
        $output = json_output();
        $translation_path = array();

        if (is_string($path_key)) {
            $translation_path = self::getPathData($path_key);
        }

        if ($translation_path) {
            $run = true;
            $dir = array_value($translation_path, 'dir');
            $find_in = array_value($translation_path, 'find_in');

            $excludes = array();
            $exclude = array_value($translation_path, 'exclude', array());

            if (is_array($exclude) && $exclude) {
                foreach ($exclude as $item) {
                    if ($item) {
                        $excludes[] = $this->pathNameFixer($item);
                    }
                }
            }

            if (!is_dir($dir)) {
                $run = false;
                $output['message'] = _e('The directory not found. Please refresh the page and try again.');
            }

            if ($run) {
                $output = json_output('success');
                $output['message'] = _e('Translations successfully reloaded.');

                if (is_array($find_in) && $find_in) {
                    $log_path_name = $this->logPathName($dir);
                    $this->_logs[] = "Start scanning the directory '{$log_path_name}'.";

                    foreach ($find_in as $find_path) {
                        $find_path = $this->pathNameFixer($find_path);

                        if (!in_array($find_path, $excludes)) {
                            $log_path_name = $this->logPathName($find_path);
                            $this->_logs[] = "List the directory '{$log_path_name}'.";
                            $this->findMessagesInDir($find_path, $excludes);
                        }
                    }
                }

                ksort($this->_translations);

                $date = date('Y-m-d H:i:s');
                $model = self::find()->where(['path_key' => $path_key, 'lang_key' => 'core'])->one();

                if ($model) {
                    $model->logs = $this->_logs;
                    $model->translations = $this->_translations;
                    $model->updated_on = date('Y-m-d H:i:s');
                    $model->updated_by = current_user_id();
                    $model->save(false);
                } else {
                    $model = new self();
                    $model->path_key = $path_key;
                    $model->lang_key = 'core';
                    $model->logs = $this->_logs;
                    $model->translations = $this->_translations;
                    $model->updated_on = date('Y-m-d H:i:s');
                    $model->updated_by = current_user_id();
                    $model->save(false);
                }

                $output['count'] = count($this->_translations);
                $output['date'] = Yii::$app->formatter->asDate($date, 'php:d/m/Y H:i:s');
            }
        }

        return $output;
    }

    /**
     * Find translation messages in dir
     *
     * @param string $dir
     * @param array $exclude
     * @return void
     */
    public function findMessagesInDir($dir, $exclude = array())
    {
        $files = array();
        $dir = trim($dir);

        if (is_string($dir) && is_dir($dir)) {
            $dir = rtrim($dir, '/');
            $dir = rtrim($dir, '\\');
            $dir = $dir . DS;

            foreach (glob($dir . "*", GLOB_ONLYDIR) as $dirname) {
                if (!in_array($dirname, $exclude)) {
                    $log_path_name = $this->logPathName($dirname);

                    if (is_readable($dirname)) {
                        $this->_logs[] = "List the directory '{$log_path_name}'.";
                        $this->findMessagesInDir($dirname, $exclude);
                    } else {
                        $this->_logs[] = "The directory '{$log_path_name}' is not readable.";
                    }
                }
            }

            $files = glob($dir . "{*.php, *.PHP}", GLOB_BRACE);
        } else {
            $log_path_name = $this->logPathName($dir);
            $this->_logs[] = "The directory '{$log_path_name}' not found.'";
        }

        if (count($files) > 0) {
            foreach ($files as $file) {
                $php = '';
                $file_content = '';

                if (!in_array($file, $exclude)) {
                    $log_path_name = $this->logPathName($file);

                    if (is_readable($file)) {
                        $this->_logs[] = "Find messages in '{$log_path_name}'.";
                        $file_content = file_get_contents($file);
                    } else {
                        $this->_logs[] = "The file '{$log_path_name}' is not readable.";
                    }
                }

                if (is_string($file_content) && $file_content) {
                    $write = false;
                    $tokens = token_get_all($file_content);

                    foreach ($tokens as $token) {
                        if (is_array($token)) {
                            list($id, $value) = $token;

                            switch ($id) {
                                case T_OPEN_TAG:
                                case T_OPEN_TAG_WITH_ECHO:
                                    $write = true;
                                    break;
                                case T_CLOSE_TAG:
                                    $write = false;
                                    $php .= $value;
                                    break;
                            }

                            if ($write) {
                                $php .= $value;
                            }
                        } elseif ($write) {
                            $php .= $token;
                        }
                    }
                }

                if (is_string($php) && $php) {
                    $write = false;
                    $trs = array();
                    $tokens = token_get_all($php);

                    foreach ($tokens as $token) {
                        if (is_array($token)) {
                            $name = token_name($token[0]);
                            $value = $token[1];

                            if ($name == 'T_STRING' && $value == '_e') {
                                $write = true;
                            }

                            if ($write && $name == 'T_CONSTANT_ENCAPSED_STRING') {
                                $write = false;

                                $value = trim($value);
                                $value = trim($value, '"\'');

                                if ($value) {
                                    $trs[] = $value;
                                    $this->_translations[$value] = $value;
                                }
                            }

                            // echo "Line {$token[2]}: ", token_name($token[0]), " ('{$token[1]}')", PHP_EOL;
                        }
                    }

                    if ($trs) {
                        $trs_count = count($trs);
                        $this->_logs[] = "{$trs_count} translation messages was found.";
                    } else {
                        $this->_logs[] = "Translation messages not found.";
                    }
                }
            }
        }
    }

    /**
     * Log path name
     *
     * @param string $name
     * @return array
     */
    public function logPathName($name)
    {
        return str_replace(HOME_PATH, '{HOME_PATH}\\', $name);
    }

    /**
     * Log path name
     *
     * @param string $name
     * @return array
     */
    public function pathNameFixer($name)
    {
        $name = str_replace('//', '/', $name);
        $name = str_replace('\\', DS, $name);
        $name = str_replace('/', DS, $name);

        return $name;
    }

    /**
     * Var export as array
     *
     * @param array $expression
     * @return string
     */
    private static function var_export($expression)
    {
        $export = var_export($expression, true);
        $patterns = [
            "/array \(/" => '[',
            "/^([ ]*)\)(,?)$/m" => '$1]$2',
            "/=>[ ]?\n[ ]+\[/" => '=> [',
            "/([ ]*)(\'[^\']+\') => ([\[\'])/" => '$1$2 => $3',
        ];

        $export = preg_replace(array_keys($patterns), array_values($patterns), $export);
        return $export;
    }
}
