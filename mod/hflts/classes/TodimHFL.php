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
*	File: A Hesitant Fuzzy Linguistic TODIM Method Based on a Score Function
*   Couping Wei, Zhilian Ren, Rosa Mª Rodiguez. IJCIS'15
*
* 	@package DecisionMaking
*
*/


class TodimHFL extends MCDM
{

	var $label;//shortname
	//var $envelopes;
	var $hesitants;
	var $lengths;
	var $deltas;

	var $CSi; //array with lower interval values (for all criteria)
	var $CSj; //array with upper interval values (for all criteria)
	var $beta; //2-tuples
	var $avg; //average aggregation array
	var $ranking; //alternatives ranked array

	public function	TodimHFL($username)
	{
		$this->N=1;
		$this->M=3;
		$this->P=$this->num=0;
		$this->label="todim";

		$this->alternatives = array($username);
		$this->W = array(1.0, 1.0, 1.0); //same importance

	}

	
	public function run()
	{
		parent::run();

		//Assuption: G is a normalized linguistic decision matrix, where criteria benefit is same and cost criteria es negated
		$envelopes = array(['inf'=>3, 'sup'=>3],['inf'=>3,'sup'=>4],['inf'=>1,'sup'=>6]);

    	$n = sizeof($envelopes); //system_message("n " . $n);
    	$this->hesitants = array();
    	$this->lengths = array();
    	$this->deltas = array();

    	for ($i=0;$i<$n;$i++)
    	{
	 
	        echo "[".$envelopes[$i]['inf'].",".$envelopes[$i]['sup']."] ";
	        $this->hesitants[$i] = toHesitant($envelopes[$i],$this->lengths[$i],$this->deltas[$i]);
	        if ($this->hesitants[$i] != -1)
	            echo "score=".$this->scoreFunction($this->hesitants[$i],$this->lengths[$i],$this->deltas[$i])."<br>";
	    }


		//step 1 calculate the relative weights
		//step 2 calculate the dominance degree for each alternative concerning a criterion
		//step 3 calculate the dominance degree for each alternative
		//step 4 calculate the overall dominance degree for each alternative
		//step 5 rank alternatives
		$this->ranking();	

		return $this->ranking[0]['todim']['label'];
	}


	/**
	* Computes the averaged linguistic terms. If Average increases the score increases.
	* The hesitant degree increases when score decreases 
	* F(H_S) = deltaAvg - (1/#H_S \sum(delta-deltaAvg) / var(tau))
	* var(tau)
	*/
	private function scoreFunction($hesitant, $L, $D)
	{
    	if ($this->debug)
    	{
    		echo('<br>scoreFunction <pre>');	print_r($hesitant);	echo('</pre>');
    	}

		$sum=0;
		for ($l=0; $l<=$L;$l++)
		{
			echo $hesitant[$l]."_";
			$sum += pow($hesitant[$l]-$D,2);
		}
		$var = $this->variance()*$L;
		echo "score = " .$D . " - " . $sum." / variance #<br>" ;
		return ($D - $sum) / $var;
	}

	/**
	* Computes the var(tau) = {(0 - tau/2)^2 +..+ (tau - tau/2)^2}  / tau+1
	*/
	private function variance()
	{
		$taumed = $this->G * 0.5; 
		$sum = 0;
		for ($i=0; $i<=$this->G; $i++)
		{
			$sum += pow($i - $taumed,2);
		}
		$var = $sum / ($this->G + 1);
		//system_message("variance " . $var);
		return $var;
	}


	/**
	 * Returns the title of the method
	 *
	 * @return string
	 */
	public function getTitle() 
	{
		// make title for Teranga
		$header = $this->label;
		$header = elgg_echo("hflts:label:{$this->label}");
		return $header;
	}
		
	/**
	 * Returns the method full name
	 *
	 * @return string
	 */
	public function getDescription() 
	{
		// Make name for Teranga
		$result = $this->label;
		$result = elgg_echo("hflts:help:{$this->label}");
		return $result;
	}

    private function ranking()
    {
    	if ($this->debug)
    	{
    		echo('<br>Ranking <pre>');	print_r($this->ranking);	echo('</pre>');
    	}
    	return $this->ranking;
    }

	/*public function realEstateCase()
	{
		$this->N=5; //numero de alternatives
		$this->M=9; //numero de criterios
		$this->P=5; //numero de expertos
		
	    $this->alternatives = array('C-1','C-2','C-3','C-4','C-5');
		$this->W = array(1.0, 1.0, 0.5,0.8, 0.7, 0.7, 1.0, 0.8, 0.4); //9 pesos del usuario 1
		
		$this->parse_csv();		
		$this->num = $this->N*$this->P;
		
		$this->translation();
		$this->envelope();

		$this->average();
		$this->ranking();
	}*/

}
