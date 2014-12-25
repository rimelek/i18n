<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Rimelek\I18n\Languages;

$languages = Languages::getInstance()->setNameOfLanguageVariable('locale');
$languages->setPath(__DIR__ . '/languages');
$languages->setDefault('en');

echo $languages['en']['welcome'] . "\n";
echo $languages['hu']['welcome'] . "\n";
echo $languages['not-available-language']['welcome'] . "\n\n";
//output:
//Welcome to this site
//Üdvözöllek ezen az oldalon
//Welcome to this site

$hu = $languages->getLanguage('hu');
echo $hu['welcome'] . "\n";
echo $hu['only-in-english'] . "\n\n";
//output:
//Üdvözöllek ezen az oldalon
//This text available only in english

$nal = $languages->getLanguage('not-available-language');
echo $nal['welcome'] . "\n";
//output:
//Welcome to this site

