<?php
/**
 * ddGetFileInfo
 * @version 2.4 (2021-01-15)
 * 
 * @see README.md
 * 
 * @copyright 2010–2021 DD Group {@link https://DivanDesign.biz }
 */

//Include (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path') .
	'assets/libs/ddTools/modx.ddtools.class.php'
);

return \DDTools\Snippet::runSnippet([
	'name' => 'ddGetFileInfo',
	'params' => $params
]);
?>