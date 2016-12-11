<?php

/**
* 	Plugin: Valoraciones linguisticas con HFLTS
*	Author: Rosana Montes Soldado
*			Universidad de Granada
*	Licence: 	CC-ByNCSA
*	Reference:	Microproyecto CEI BioTIC Ref. 11-2015
* 	Project coordinator: @rosanamontes
*	Website: http://lsi.ugr.es/rosana
*	
*	File: Developer settings
*/

$plugin = elgg_get_plugin_from_id('hflts');
$data = array(
/* ocultar
	'classic' => array(
		'type' => 'checkbox',
		'value' => 1,
		'checked' => elgg_get_plugin_setting('classic', 'hflts') == 1,
		'readonly' => false,
	),

	'todim' => array(
		'type' => 'checkbox',
		'value' => 1,
		'checked' => elgg_get_plugin_setting('todim', 'hflts') == 1,
		'readonly' => false,
	),

	'vikor' => array(
		'type' => 'checkbox',
		'value' => 1,
		'checked' => elgg_get_plugin_setting('vikor', 'hflts') == 1,
		'readonly' => false,
	),
	
	'topsis' => array(
		'type' => 'checkbox',
		'value' => 1,
		'checked' => elgg_get_plugin_setting('topsis', 'hflts') == 1,
		'readonly' => false,
	),

	'electre' => array(
		'type' => 'checkbox',
		'value' => 1,
		'checked' => elgg_get_plugin_setting('electre', 'hflts') == 1,
		'readonly' => false,
	),

//* no implementado
	'promethee' => array(
		'type' => 'checkbox',
		'value' => 1,
		'checked' => elgg_get_plugin_setting('promethee', 'hflts') == 1,
		'readonly' => false,
	),
*/

	'aggOperator' => array(
		'type' => 'dropdown',
		'value' => $plugin->aggOperator,
                'options_values' => array('0' => elgg_echo('hflts:aggOperator:minmax'), '1' => elgg_echo('hflts:aggOperator:HLWA')),
		'readonly' => false,
	),

	'termset' => array(
		'type' => 'dropdown',
		'value' => $plugin->termset,
                'options_values' => array('0' => elgg_echo('hflts:settings:s3'), '1' => elgg_echo('hflts:settings:s5'), '2' => elgg_echo('hflts:settings:s7')),
		'readonly' => false,
	),

	'profile_display' => array(
		'type' => 'dropdown',
		'value' => $plugin->profile_display,
                'options_values' => array('1' => elgg_echo('hflts:settings:yes'), '0' => elgg_echo('hflts:settings:no')),
		'readonly' => false,
	),
/*
	'debug' => array(
		'type' => 'dropdown',
		'value' => $plugin->debug,
                'options_values' => array('1' => elgg_echo('hflts:settings:yes'), '0' => elgg_echo('hflts:settings:no')),
		'readonly' => false,
	),

	'exportTex' => array(
		'type' => 'dropdown',
		'value' => $plugin->exportTex,
                'options_values' => array('1' => elgg_echo('hflts:settings:yes'), '0' => elgg_echo('hflts:settings:no')),
		'readonly' => false,
	),


	'allowMany' => array(
		'type' => 'dropdown',
		'value' => $plugin->allowMany,
                'options_values' => array('1' => elgg_echo('hflts:settings:yes'), '0' => elgg_echo('hflts:settings:no')),
		'readonly' => false,
	),
*/
	'auto_moderation' => array(
		'type' => 'dropdown',
		'value' => $plugin->auto_moderation,
                'options_values' => array('1' => elgg_echo('hflts:settings:yes'), '0' => elgg_echo('hflts:settings:no')),
		'readonly' => false,
	),

	'weight_experts' => array(
		'type' => 'dropdown',
		'value' => $plugin->weight_experts,
                'options_values' => array('1' => elgg_echo('hflts:settings:yes'), '0' => elgg_echo('hflts:settings:no')),
		'readonly' => false,
	),

	'base_expertise' => array(
		'type' => 'range',
		'value' => $plugin->base_expertise,
		'readonly' => false,
	),

	'weight_assessments' => array(
		'type' => 'dropdown',
		'value' => $plugin->weight_assessments,
                'options_values' => array('1' => elgg_echo('hflts:settings:yes'), '0' => elgg_echo('hflts:settings:no')),
		'readonly' => false,
	),

);

/*$form .= "<br><br><b>" . elgg_echo('elggx_userpoints:settings:profile_display') . "</b>";
$form .= elgg_view('input/dropdown', array(
                'name' => 'params[profile_display]',
                'options_values' => array('1' => elgg_echo('elggx_userpoints:settings:yes'), '0' => elgg_echo('elggx_userpoints:settings:no')),
                'value' => $plugin->profile_display
));
*/


$form_vars = array('id' => 'hflts-settings-form', 'class' => 'elgg-form-settings');
$body_vars = array('data' => $data);
echo elgg_view_form('hflts/settings', $form_vars, $body_vars);
