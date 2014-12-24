<?php

namespace Rimelek\I18n;

use Exception;

/**
 * Class for get a translated text
 *
 * You can choose a default language by this class and give the path 
 * where the language files are located.
 * 
 * This is a singleton class. You cannot directly instantiate it.
 * Use the {@link getInstance()} method to get the instance.
 * 
 */
class Languages implements \ArrayAccess
{

    /**
     * The instance of the class
     * 
     * @var Languages
     */
    private static $instance = null;

    /**
     * Two dimensional array of translations
     *
     * The key is the language code.
     * The value is the array of translated strings
     *
     * @var array
     */
    private $lang = array();

    /**
     * The default language's code
     * 
     * @var string
     */
    private $default = 'en';

    /**
     * The path where the language files are located
     * 
     * @var string
     */
    private $path = 'languages';

    /**
     * Get the instance
     *
     * @return Languages
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            $class = __CLASS__;
            self::$instance = new $class();
        }
        return self::$instance;
    }

    /**
     * Constructor
     *
     * You can instantiate the class using the {@link getInstance()} method
     */
    private function __construct()
    {
        
    }

    /**
     * Get the path of the language files
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the path of the language files
     *
     * @param string $path The language files will be search in this folder
     * @return static
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Get the language code of the default language
     *
     * @return string
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Set the language code of the default language
     *
     * @param string $langcode
     * @return static
     */
    public function setDefault($langcode)
    {
        $this->default = $langcode;
        return $this;
    }

    /**
     * Get the instance of Language by a language code
     *
     * This way yue get an object which is able to handle the case when
     * a text has no translation in the choosen language, but it is
     * available in the default language.
     *
     * @param string $langcode A nyelv kódja
     * @return Language 
     */
    public function getLanguage($langcode)
    {
        return new Language($langcode);
    }

    /**
     * Checking of existence of a language by language code
     *
     * @param string $langcode
     * @return bool Ha létezik, True, egyébként false
     */
    public function offsetExists($langcode)
    {
        return file_exists(rtrim($this->path, '/') . '/' . strtolower($langcode) . '.php');
    }

    /**
     * Get the array of texts by the given language code
     *
     * @param string $langcode Language code
     * @return array
     */
    public function offsetGet($langcode)
    {
        $offset = strtolower($langcode);
        $dir = rtrim($this->path, '/') . '/';
        if (!isset($this->lang[$offset])) {
            if (!file_exists($dir . $offset . '.php')) {
                $offset = $this->default;
            }
            if (!isset($this->lang[$offset])) {
                require_once $dir . $offset . '.php';
            } else {
                $lang = $this->lang[$offset];
            }
            $this->lang[$offset] = $lang;
        }
        return $this->lang[$offset];
    }

    /**
     * Languages cannot be set directly
     *
     * @param string $langcode
     * @param mixed $value
     */
    public function offsetSet($langcode, $value)
    {
        throw new Exception('Texts cannot be set directly');
    }

    /**
     * Languages cannot be unset
     *
     * @param string $langcode
     */
    public function offsetUnset($langcode)
    {
        throw new Exception('Texts cannot be unset');
    }

}
