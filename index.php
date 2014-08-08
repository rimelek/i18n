<?php
require_once 'class/Languages.class.php';
$languages = Languages::getInstance();
$languages->setLanguageDir('languages');
$languages->setDefault('hu');

print $languages['hu']['hello'].'<br />';
print $languages['en']['hello'].'<br />';
print $languages['ens']['magyar'].'<br />';
//kimenet:
//Szia
//Hello
//magyarban van csak

$lang = $languages->getLanguage('en');
print $lang['hello'].'<br />';
print $lang['magyar'].'<br />';
//kimenet:
//Hello
//magyarban van csak

$lang = $languages->getLanguage('ens');
print $lang['hello'].'<br />';
print $lang['magyar'].'<br />';
//kimenet:
//Hello
//magyarban van csak
?>
