<?php
/**
 * trip module (also called a trip widget)
 *
 * @uses $vars['title']    The title of the module
 * @uses $vars['content']  The module content
 * @uses $vars['all_link'] A link to list content
 * @uses $vars['add_link'] A link to create content
 *
* 	Plugin: mytripsTeranga from previous version of @package ElggGroup
*	Author: Rosana Montes Soldado 
*			Universidad de Granada
*	Licence: 	CC-ByNCSA
*	Reference:	Microproyecto CEI BioTIC Ref. 11-2015
* 	Project coordinator: @rosanamontes
*	Website: http://lsi.ugr.es/rosana
* 	Project colaborator: Antonio Moles 
*	
*   Project Derivative:
*	TFG: Desarrollo de un sistema de gestión de paquetería para Teranga Go
*   Advisor: Rosana Montes
*   Student: Ricardo Luzón Fernández
* 
*/


$trip = elgg_get_page_owner_entity();

$header = "<span class=\"mytrips-widget-viewall\">{$vars['all_link']}</span>";
$header .= '<h3>' . $vars['title'] . '</h3>';

if ($trip->canWriteToContainer() && isset($vars['add_link'])) {
	$vars['content'] .= "<span class='elgg-widget-more'>{$vars['add_link']}</span>";
}

echo '<li>';
echo elgg_view_module('info', '', $vars['content'], array(
	'header' => $header,
	'class' => 'elgg-module-trip',
));
echo '</li>';
