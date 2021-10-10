<?php

/**
 * This is a stub include that automatically configures the include path.
 */

set_include_path(dirname(__FILE__) . PATH_SEPARATOR . get_include_path() );
require_once 'HTMLPurifier/Bootstrap.php';
require_once 'HTMLPurifier.autoload.php';


//require_once 'HTMLPurifier.auto.php';
 
 
 
//crl additions

$config = HTMLPurifier_Config::createDefault();
//$config->set('HTML.Allowed', 'img[data-chem-obj]');
$config->set('HTML.DefinitionID', 'enduser-customize.html tutorial');
$config->set('HTML.DefinitionRev', 1);
$config->set('Cache.DefinitionImpl', null); // TODO: remove this later!
//$def = $config->getHTMLDefinition(true);
//$def->info_global_attr['data-chem-obj'] = new HTMLPurifier_AttrDef_Text;
$config->set('URI.AllowedSchemes', array('data' => true, 'type' => true));
$config->set('CSS.AllowTricky', true);
$config->set('Attr.EnableID', true);

$def = $config->maybeGetRawHTMLDefinition();

if ($def) {
$def->addAttribute('div', 'type', 'Enum#chemical/x-kekule-json');
$def->addAttribute('div', 'id', 'Text');

$def->addAttribute('img', 'style', 'CDATA');
$def->addAttribute('img', 'src', 'CDATA');
//$def->addAttribute('img', 'data-kekule-widget', 'Enum#"Kekule.ChemWidget.Viewer"');
$def->addAttribute('img', 'data-kekule-widget', 'CDATA');
$def->addAttribute('img', 'data-render-type', 'Number');
$def->addAttribute('img', 'data-chem-obj', 'Text');
$def->addAttribute('img', 'data-background-color', 'CDATA');
//$def->addAttribute('img', 'data-background-color', 'Enum#transparent');
$def->addAttribute('img', 'data-predefined-setting', 'Enum#basic');
$def->addAttribute('img', 'data-auto-size', 'Enum#true,false');
$def->addAttribute('img', 'data-uniqid', 'Text');

}
$purifier = new HTMLPurifier($config);
