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
*	File: main entry 
*/


$valorationlist = $vars['valorations'];//system_message(" Size\n " . sizeof($valorationlist));
	
if (sizeof($valorationlist) > 0) 
{

?>

<div class="evaluation-content evaluation-content-archived">
	<div class="clearfix">

<?php
	$count=1;
	foreach ($valorationlist as $evaluation) 
	{
		$person_link = elgg_view('output/text', array(
				'text' => $evaluation->name,
				'href' => $evaluation->url,
				'is_trusted' => true,
				'class' => 'elgg-evaluation-content-address elgg-lightbox',
				'data-colorbox-opts' => json_encode([
					'width' => '85%',
					'height' => '85%',
					'iframe' => true,
				]),
		));
		//system_message("empty " . $person_link);
		$hesitant = "#".$count++." => G=" .  $evaluation->granularity;
		if ($evaluation->criterio1) $hesitant .= " H1=".$evaluation->criterio1;
		if ($evaluation->criterio2) $hesitant .= " H2=".$evaluation->criterio2;
		if ($evaluation->criterio3) $hesitant .= " H3=".$evaluation->criterio3;

		?>
		<h3 class="mbm"><?php echo $person_link; ?></h3>
		<p><?php echo $hesitant; ?></p>
		<?php
	}	

?>	
	</div>
</div>

<?php

} else {
	echo elgg_echo('hflts:evaluation:not:found');
}

$result = euclideanDistance(null, $evaluation->granularity);
?>	

<p><?php echo $result; ?></p>

