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

    const CATEGORY_DEFAULT = 'default';
    const LANGVAR_DEFAULT = 'lang';

    /**
     * The instances of the class
     * 
     * @var static[]
     */
    private static $instances = array();

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
     * The category of translations
     *
     * @var string
     */
    private $category = null;

    /**
     * The name of the variable which contains the translations
     * 
     * E.g. $lang or $language or $locale withoit dollar
     *
     * @var string
     */
    private $nameOfLanguageVariable = self::LANGVAR_DEFAULT;

    /**
     * Get the instance
     *
     * @param string $category The category of translations
     * @return static
     */
    public static function getInstance($category = self::CATEGORY_DEFAULT)
    {
        if (!isset(self::$instances[$category])) {
            self::$instances[$category] = new static($category);
        }
        return self::$instances[$category];
    }

    /**
     * Constructor
     *
     * You can instantiate the class using the {@link getInstance()} method
     * 
     * @param string $category The category of translations
     */
    private function __construct($category)
    {
        $this->category = $category;
    }

    /**
     * Set the name of the variable which contains the translations
     * 
     * E.g. $lang or $language or $locale withoit dollar
     * 
     * $lang by default
     * 
     * @param string $nameOfLanguageVariable New name
     * @return static
     */
    public function setNameOfLanguageVariable($nameOfLanguageVariable)
    {
        $this->nameOfLanguageVariable = $nameOfLanguageVariable;
        return $this;
    }

    /**
     * Get the name of the variable which contains the translations
     * 
     * E.g. $lang or $language or $locale withoit dollar
     * 
     * $lang by default
     * 
     * @return string
     */
    public function getNameOfLanguageVariable()
    {
        return $this->nameOfLanguageVariable;
    }

    /**
     * Get the category of translations
     * 
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
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
        $this->path = rtrim($path ? : '.', '/') . '/';
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
     * This way you get an object which is able to handle the case when
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
     * Checking of existence of a language file in subdirectory by language code
     * 
     * @param string $langcode
     * @return boolean
     */
    private function isFileExistsInSubDir($langcode)
    {
        return file_exists($this->getFilePathInSubDir($langcode));
    }

    /**
     * Checking of existence of a language file with category suffix by language code
     * 
     * @param string $langcode
     * @return boolean
     */
    private function isFileExistsWithSuffix($langcode)
    {
        return file_exists($this->getFilePathWithSuffix($langcode));
    }

    /**
     * Checking of existence of a language file without suffix by language code
     * 
     * If there is no category suffix, then the file contains of texts of
     * the default category
     * 
     * @param string $langcode
     * @return boolean
     */
    private function isFileExistsWithoutSuffix($langcode)
    {
        return file_exists($this->getFilePathWithoutSuffix($langcode));
    }

    /**
     * Get the path of a language file in subdirectory by language code
     * 
     * @param string $langcode
     * @return string
     */
    public function getFilePathInSubDir($langcode)
    {
        return $this->getPath() . $langcode . '/' . $this->getCategory() . '.php';
    }

    /**
     * Get the path of a language file without suffix by language code
     * 
     * @param string $langcode
     * @return string
     */
    public function getFilePathWithoutSuffix($langcode)
    {
        return $this->getPath() . $langcode . '.php';
    }

    /**
     * Get the path of a language file with suffix by language code
     * 
     * @param string $langcode
     * @return string
     */
    public function getFilePathWithSuffix($langcode)
    {
        return $this->getPath() . $langcode . '-' . $this->getCategory() . '.php';
    }

    /**
     * Load the translations into the category from the language file in a subdirectory
     * 
     * @param string $langcode
     */
    private function loadFromFileInSubDir($langcode)
    {
        $this->loadIntoCategory($this, $this->getFilePathInSubDir($langcode), $langcode);
    }

    /**
     * Load the translations into the category from the language file with category suffix
     * 
     * @param string $langcode
     */
    private function loadFromFileWithSuffix($langcode)
    {
        $this->loadIntoCategory($this, $this->getFilePathWithSuffix($langcode), $langcode);
    }

    /**
     * Load the translations into the category from the language file without category suffix
     * 
     * @param string $langcode
     */
    private function loadFromFileWithoutSuffix($langcode)
    {
        $this->loadIntoCategory($this, $this->getFilePathWithoutSuffix($langcode), $langcode);
    }

    /**
     * Load the translations into the category from a file by the given path
     * 
     * Each argument of this method is accessed by func_get_arg() to avoid
     * using any variable in the context of the translations.
     * 
     * The method is static, because this way the variable '$this' is not
     * available in any language file.  
     * 
     * @param self $object An instance of this class
     * @param string $path The path where the language file can be found
     * @param string $langcode The language code of the choosen language
     */
    private static function loadIntoCategory()
    {
        //func_get_arg to avoid override any variable or access to $this from language file 
        require_once func_get_arg(1);
        if (isset(${func_get_arg(0)->getNameOfLanguageVariable()})) {
            func_get_arg(0)->lang[func_get_arg(2)][func_get_arg(0)->getCategory()] = ${func_get_arg(0)->getNameOfLanguageVariable()};
        }
    }

    /**
     * Checking of existence of a language by language code
     *
     * @param string $langcode
     * @return bool Ha létezik, True, egyébként false
     */
    public function offsetExists($langcode)
    {
        return (isset($this->lang[$langcode][$this->category])
            or $this->isFileExistsInSubDir($langcode)
            or $this->isFileExistsWithSuffix($langcode)
            or $this->isFileExistsWithoutSuffix($langcode));
    }

    /**
     * Get the array of texts by the given language code
     *
     * @param string $langcode Language code
     * @return array
     */
    public function offsetGet($langcode)
    {
        $noDefaultVersion = $this->getDefault() !== $langcode
            and ! isset($this->lang[$this->getDefault()][$this->getCategory()]);
        if (!isset($this->lang[$langcode][$this->getCategory()])) {
            if ($this->isFileExistsInSubDir($langcode)) {
                $this->loadFromFileInSubDir($langcode);
            } elseif ($this->isFileExistsWithSuffix($langcode)) {
                $this->loadFromFileWithSuffix($langcode);
            } elseif ($this->isFileExistsWithoutSuffix($langcode)) {
                $this->loadFromFileWithoutSuffix($langcode);
            } elseif ($noDefaultVersion and $this->isFileExistsInSubDir($this->getDefault())) {
                $langcode = $this->getDefault();
                $this->loadFromFileInSubDir($langcode);
            } elseif ($noDefaultVersion and $this->isFileExistsWithSuffix($this->getDefault())) {
                $langcode = $this->getDefault();
                $this->loadFromFileWithSuffix($langcode);
            } elseif ($noDefaultVersion and $this->isFileExistsWithoutSuffix($this->getDefault())) {
                $langcode = $this->getDefault();
                $this->loadFromFileWithoutSuffix($langcode);
            }
        }

        return $this->lang[$langcode][$this->getCategory()];
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
