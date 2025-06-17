<?php
/**
 * ddGetFileInfo
 * @version 2.5.1 (2025-06-17)
 * 
 * @see README.md
 * 
 * @copyright 2010–2025 Ronef {@link https://Ronef.me }
 */

// Include (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path')
	. 'assets/libs/ddTools/modx.ddtools.class.php'
);

return \DDTools\Snippet::runSnippet([
	'name' => 'ddGetFileInfo',
	'params' => $params,
]);
?>