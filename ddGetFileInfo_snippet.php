<?php
/**
 * ddGetFileInfo
 * @version 2.5 (2021-04-25)
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