<?php
/**
 * Rugalmas nyelvkezelés
 *
 * @copyright Copyright (c) 2009, Takács Ákos
 * @author Takács Ákos <programmer@rimelek.hu>
 * @package LanguageHandler
 * @version 1.0
 */

/**
 * Egy nyelvet megvalósító osztály
 *
 * Ez az osztály valójában csak arra való, hogy egy
 * nyelvet kiválasszunk elsődleges nyelvnek. Ezek után
 * az osztály gondoskodik róla, hogy ha mégsem található meg az általa preferált
 * nyelvben az adott tartalom, akkor a {@link Languages} osztály
 *
 * @package LanguageHandler
 */
class Language implements ArrayAccess
{
	/**
	 * A kiválasztott nyelv kódja
	 *
	 * Pl.: hu, en, ro stb...
	 *
	 * @var string
	 */
	private $langcode;

	/**
	 * Egy nyelv létrehozása
	 *
	 * @param string $langcode Kiválasztott nyelv kódja
	 */
	public function  __construct($langcode)
	{
		$this->langcode = $langcode;
	}

	/**
	 * Létezik-e $offset indexű tartalom
	 *
	 * @param string $offset
	 * @return bool True, ha lézezik, false, ha nem létezik
	 */
	public function offsetExists($offset)
	{
		$langs = Languages::getInstance();
		return isset($langs[$this->langcode][$offset]);
	}

	/**
	 * Egy tartalom lekérdezése $offset index alapján
	 *
	 * @param string $offset
	 * @return string $offset indexű tartalom
	 */
	public function offsetGet($offset)
	{
		$langs = Languages::getInstance();
		return (isset($langs[$this->langcode][$offset])) ?
				$langs[$this->langcode][$offset] :
				$langs[$langs->getDefault()][$offset];
	}

	/**
	 * A nyelvek nem módosíthatóak
	 * 
	 * @param string $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value)
	{
		throw new Exception('Nem módosítható');
	}

	/**
	 * A nyelvek nem törölhetők dinamikusan
	 * 
	 * @param string $offset
	 */
	public function offsetUnset($offset)
	{
		throw new Exception('Nem módosítható');
	}
}


/**
 * A nyelvek kiválasztását végző osztály
 *
 * Itt dől el, hogy melyik nyelvet választjuk ki alapértelmezettnek és
 * hol kell keresni a nyelveket.<br />
 * Ez az osztály nem példányosítható. Kizárólag a {@link getInstance()}
 * metódusán keresztül érhető el a példánya.
 *
 * <b>languages/hu.php</b>
 * {@example ../languages/hu.php}
 *
 * <b>languages/en.php</b>
 * {@example ../languages/en.php}
 * 
 * <b>Használata:</b>
 * {@example ../index.php}
 *
 * @package LanguageHandler
 */
class Languages implements ArrayAccess
{
	/**
	 * Tárolja az osztály egyetlen példányát
	 * 
	 * @var Languages
	 */
	private static $instance = null;

	/**
	 * Nyelvek tömbje
	 *
	 * Index a nyelv kódja.
	 * Értéke egy asszociatív tömb
	 *
	 * @var array
	 */
	private $lang = array();

	/**
	 * Alapértelmezett nyelv kódja
	 * 
	 * @var string
	 */
	private $default = 'hu';

	/**
	 * Nyelvek mappája
	 * @var string
	 */
	private $languageDir = 'languages';


	/**
	 * A példány lekérdezése
	 *
	 * @return Languages
	 */
	public static function getInstance()
	{
		if (self::$instance === null)
		{
			$class = __CLASS__;
			self::$instance = new $class();
		}
		return self::$instance;
	}

	/**
	 * Konstruktor
	 *
	 * Az osztály nem példányosítható.
	 * A példány lekérését a {@link getInstance()} metódus végzi
	 */
	private function  __construct()
	{

	}

	/**
	 * Nyelvek mappájának lekérdezése
	 *
	 * @return string
	 */
	public function getLanguageDir()
	{
		return $this->languageDir;
	}

	/**
	 * Nyelvek mappájának beálítása
	 *
	 * @param string $dir Ebben a mappában keresi a nyelvi fájlokat
	 */
	public function setLanguageDir($dir)
	{
		$this->languageDir = $dir;
	}

	/**
	 * Alapértelmezett nyelv kódja
	 *
	 * @return string
	 */
	public function getDefault()
	{
		return $this->default;
	}

	/**
	 * Alapértelmezett nyelv kódjának beállítása
	 *
	 * @param string $langcode
	 */
	public function setDefault($langcode)
	{
		$this->default = $langcode;
	}

	/**
	 * Egy nyelv objektum lekérése
	 *
	 * Ezzel egy okos nyelvobjektumot kapunk,
	 * ami képes kezelni, ha egy nyelv még nem tartalmaz egy szöveget,
	 * de az alapértelmezett nyelv igen. 
	 *
	 * @param string $langcode A nyelv kódja
	 * @return Language 
	 */
	public function getLanguage($langcode)
	{
		 return new Language($langcode);
	}

	/**
	 * Létezik-e adott nyelven egy tartalom
	 *
	 * @param string $offset
	 * @return bool Ha létezik, True, egyébként false
	 */
	public function offsetExists($offset)
	{
		$offset = strtolower($offset);
		return file_exists(rtrim($this->languageDir,'/').'/'.$offset.'.php');
	}

	/**
	 * Egy bizonyos szöveg lekérdezése adott nyelven
	 *
	 * @param string $offset tartalom azonosítója
	 * @return mixed
	 */
	public function offsetGet($offset)
	{
		$offset = strtolower($offset);
		$dir = rtrim($this->languageDir,'/').'/';
		if (!isset($this->lang[$offset]))
		{
			if (!file_exists($dir.$offset.'.php'))
			{
				$offset = $this->default;
			}
			if (!isset($this->lang[$offset]))
			{
				require_once $dir.$offset.'.php';
			}
			else
			{
				$lang = $this->lang[$offset];
			}
			$this->lang[$offset] = $lang;
		}
		return $this->lang[$offset];
		
	}

	/**
	 * A nyelvlista nem módosítható
	 *
	 * @param string $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value)
	{
		throw new Exception('Nem módosítható');
	}

	/**
	 * A nyelv lista nem törölhető
	 *
	 * @param string $offset
	 */
	public function offsetUnset($offset)
	{
		throw new Exception('Nem módosítható');
	}
}
?>
