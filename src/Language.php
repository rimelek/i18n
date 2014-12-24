<?php

namespace Rimelek\I18n;

use Exception;

/**
 * Class for accessing to translation flexible way
 *
 * This class allows you to get a default text when the text does not exists
 * in the requested language.
 *
 */
class Language implements \ArrayAccess
{

    /**
     * The code of the language
     *
     * e.g.: en, hu, ro
     *
     * @var string
     */
    private $code;

    /**
     * 
     *
     * @param string $code The code of the language
     */
    public function __construct($code = null)
    {
        $this->code = $code ? : Languages::getInstance()->getDefault();
    }

    /**
     * Checking of existence of a content by key
     *
     * @param string $key The key of the text
     * @return bool true, if it exists, otherwise false
     */
    public function offsetExists($key)
    {
        $langs = Languages::getInstance();
        return isset($langs[$this->code][$key]);
    }

    /**
     * Get a text by key
     *
     * @param string $key
     * @return mixed A content in the requested language or in the default 
     * language if the requested does not exist.
     */
    public function offsetGet($key)
    {
        $langs = Languages::getInstance();
        return (isset($langs[$this->code][$key])) ?
            $langs[$this->code][$key] :
            $langs[$langs->getDefault()][$key];
    }

    /**
     * Contents cannot be set directly
     * 
     * @param string $key
     * @param mixed $value
     */
    public function offsetSet($key, $value)
    {
        throw new Exception('Contents cannot be set directly');
    }

    /**
     * Contents cannot be unset
     * 
     * @param string $key
     */
    public function offsetUnset($key)
    {
        throw new Exception('Contents cannot be unset');
    }

    /**
     * Get the language code
     * 
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

}
