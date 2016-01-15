<?php

/**
* 	Plugin: Valoraciones linguisticas con HFLTS
*	Author: Rosana Montes Soldado
*			Universidad de Granada
*	Licence: 	CC-ByNCSA
*	Reference:	CEI BioTIC Micro.proyect Ref. 11-2015
* 	Project coordinator: @rosanamontes
*	Website: http://lsi.ugr.es/rosana
*	
*	File: A Hesitant Fuzzy Linguistic TODIM Method Based on a Score Function
*	Couping Wei, Zhilian Ren, Rosa Mª Rodiguez. IJCIS'15
*
* 	@package DecisionMaking
*
*/


class TodimHFL extends MCDM
{

	var $label;//shortname

	var $refC; //reference criterion
	var $W_r;  //relative weights
	var $T_Wr; //total relative weights
	var $chi; //risk attitudes in [0,1]. Optimistic =1, Pesimistic =0
	var $lambda; //to set the distance measure
	var $theta;  //attenuation factor

	var $hesitants;//parsed data
	var $score;//measure of the hesitant
	var $variance; //variance of the granularity
	var $dominance; //the dominance degree matrix
	var $overall;

	var $ranking; //array of ranked alternatives ranked

	public function	TodimHFL($username)
	{
		$this->N=1;
		$this->M=3;
		$this->P=$this->num=0;
		$this->label="todim";

		$this->alternatives = array($username);
		$this->W = array(1.0, 1.0, 1.0); //supose here the same importance. Allow the user to change it

		$this->lambda = 1.5;
		$this->theta = 1.0; //losses factor in case of >0
		$this->chi = 0.5; //extension factor

		$this->W_r = array();
		$this->hesitants = array();
		$this->score = array();
		$this->dominance = array();		
		$this->overall = array();
	}


	public function run()
	{
		parent::run();
		parent::electreCase();

		//Assuption: G is a normalized linguistic decision matrix, where criteria benefit is same and cost criteria es negated
		$this->variance = $this->variance();
		//parent::todimCase();//realEstateCase();vikorCase
		
		//step 1 find the most important factor and calculate the relative weights
		$this->relativeWeights();

		//step 2 calculate the dominance degree for each alternative concerning a criterion
		//step 3 calculate the dominance degree for each alternative
		$this->crossAlternativesWithCriteria();

		//step 4 calculate the overall dominance degree for each alternative
		$this->overallDominance();

		//step 5 rank alternatives
		$this->ranking();	

		return $this->ranking[0]['todim']['label'];
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
		if ($this->debug) system_message("G=" . $this->G . " variance=" . $var);
		return $var;
	}


	/**
	* Computes the relative weights in base of the main criteria
	*/
	private function relativeWeights()
	{
		$a = array_keys($this->W, max($this->W));
		$refC = $a[0];//index
		$this->T_Wr = 0;

		for ($i=0;$i<$this->M;$i++)
		{
			$this->W_r[$i] = $this->W[$i] / $this->W[$refC]; 
			$this->T_Wr += $this->W_r[$i];
		}
		
		if ($this->debug)
		{
			echo('W_r: <pre>');	print_r($this->W_r);	echo('</pre><br>');
			system_message("Total relative weight " . $this->T_Wr);
		}
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
			echo('<br>F <pre>');	print_r($hesitant);	echo('</pre>');
		}

		$sum=0;
		for ($l=0; $l<$L;$l++)
		{
			$sum += pow($hesitant[$l]-$D,2);
		}

		$var = $this->variance*$L;
		if ($this->debug) 
			echo "score = " .$D . " - (" . $sum." / " . $var . ") L=".$L."<br>" ;
		
		return $D - ($sum / $var);
	}


	/**
	* Compute F(H) for all assessment regarding criteria and alternatives 
	* Use this value to compute the dominance degree
	*/
	private function crossAlternativesWithCriteria()
	{
		$length = $delta = 0;

		for ($i=0;$i<$this->N;$i++)//forall alternatives
		{
			for ($j=0;$j<$this->M;$j++)//forall criteria
			{
				$inf = "L".($j+1);
				$sup = "U".($j+1);
				$envelope = array ("inf" => $this->data[$i][$inf], "sup" => $this->data[$i][$sup]);
				if ($this->debug) echo "[".$this->data[$i][$inf].",".$this->data[$i][$sup]."] ";
				$this->hesitants[$i][$j] = toHesitant($envelope,$length,$delta);
				if ($this->hesitants[$i][$j] == -1)
					register_error("wrong hesitant in score function");
 
				$this->score[$i][$j] = $this->scoreFunction($this->hesitants[$i][$j], $length, $delta);
				if ($this->debug) echo $this->data[$i]["ref"] . " - C" . $j . " F=" . $this->score[$i][$j] ."<br>";
			}	
		}
		
		//check cases
		for ($j=0;$j<$this->M;$j++)//forall criteria
		{
			if ($this->debug) echo "C".($j+1);
			for ($i=0;$i<$this->N;$i++)//all alternatives
			{
				for ($k=0;$k<$this->N;$k++)//with all alternatives
				{   
					if ($i == $k)
					{
						$this->dominance[$i][$k][$j] = 0;
					}
					else
					{
						if ($this->score[$i][$j] == $this->score[$k][$j] ) 
							$this->dominance[$i][$k][$j] = 0;
						else if ($this->score[$i][$j] > $this->score[$k][$j] ) 
							$this->dominance[$i][$k][$j] = $this->dominanceDegreeCaseOver($i, $j, $k);
						else 
							$this->dominance[$i][$k][$j] = $this->dominanceDegreeCaseUnder($i, $j, $k);
					}
					if ($this->debug) 
						echo " (".$this->data[$i]["ref"] . "," . $this->data[$k]["ref"].") -> " .$this->dominance[$i][$k][$j] ;
				}
				if ($this->debug) echo "<br>&nbsp;&nbsp;&nbsp;";
			}
			if ($this->debug) echo "<br>";
		}

	}


	/**
	* Compute \Phi_j(Hi_,H_k) when F(H_ij) > F(H_kj)
	* as sqrt( w_jr d_(H_ij,H_kj) / sum w_jr)
	*/
	private function dominanceDegreeCaseOver($i, $j, $k)
	{
		$d = euclideanDistance($this->hesitants[$i][$j], $this->hesitants[$k][$j], $this->lambda, $this->G, $this->chi);
		if ($this->debug) 
			echo " (" .$d.") W_r " . $this->W_r[$j] . " / " . $this->T_Wr ;

		$dd = sqrt( ($d * $this->W_r[$j])/ $this->T_Wr );

		return $dd;
	}	

	/**
	* Compute \Phi_j(Hi_,H_k) when F(H_ij) < F(H_kj)
	* as -1/theta sqrt ( sum w_jr d_(H_kj,H_ij) / w_jr)
	*/
	private function dominanceDegreeCaseUnder($i, $j, $k)
	{
		$d = euclideanDistance($this->hesitants[$i][$j], $this->hesitants[$k][$j], $this->lambda, $this->G, $this->chi);
		$factor = -1.0/$this->theta;
		if ($this->debug) 
			echo " (" .$d.") W_r " . $this->W_r[$j] . " / " . $this->T_Wr ;

		$dd = $factor * sqrt( ($d *  $this->T_Wr) / $this->W_r[$j]);

		return $dd;

	}	

	/**
	* With the dominance degree for each alternative p_i over the remaining alternatives p_k
	* Calculate  compute the overal dominance det
	*/
	private function overallDominance()
	{
		$min = $max = 0;
		for ($i=0;$i<$this->N;$i++)
		{
			$this->overall[$i] = 0; 
			for ($k=0;$k<$this->N;$k++)
			{
				if ($i != $k)
				{
					$delta = 0;
					//echo " (".$this->data[$i]["ref"] . "," . $this->data[$k]["ref"].") -> "  ;
					for ($j=0;$j<$this->M;$j++)
						$delta += $this->dominance[$i][$k][$j];
					//echo " delta=". $delta . "<br>";
					$this->overall[$i] += $delta;
				}
			}
			//echo " overall = " . $this->overall[$i] . "<br>";
		}	
	}

	private function ranking()
	{
		$min = min($this->overall);
		$max = max($this->overall);
		$den = $max - $min;
		//echo "$min / $max / $den<br>";

		$values = array();
		for ($i=0;$i<$this->N;$i++)		
		{
			$a = $this->overall[$i] - $min;
			$b = $a / $den;
			//echo "$a / $b / $den<br>";
			$values[$i] = ($this->overall[$i] - $min)/$den;
			if ($this->debug) 
				echo $b . " X(".$this->data[$i]["ref"].") = " . $values[$i] ."<br>";
		}

		arsort($values);
		if ($this->debug) 
		{
			echo('<br><pre>');	print_r($values);	echo('</pre>');
		}

		//$p=0;  while ($candidato = current($values))   	

		for ($i=0;$i<$this->N;$i++)	
		{
			$index = key($values);
			$this->ranking[$i]['todim']['ref'] = $this->alternatives[$index] ;
			$this->ranking[$i]['todim']['value'] = current($values);
			$this->ranking[$i]['todim']['label'] = "--";
			//echo "<p>index ".$i." is ranked as ".$index." </p>";
			next($values);
		}  	
				
		if ($this->debug)
		{
			echo('<br>Ranking <pre>');	print_r($this->ranking);	echo('</pre>');
		}
		return $this->ranking;
	}


	private function testing()
	{
		/*
		* Example 1 in paper
		*/
		$envelopes = array(['inf'=>3, 'sup'=>3],['inf'=>3,'sup'=>4],['inf'=>1,'sup'=>6]);
		//$envelopes = array(['inf'=>2, 'sup'=>4],['inf'=>0,'sup'=>1],['inf'=>0,'sup'=>2],['inf'=>2,'sup'=>5]);
		$n = sizeof($envelopes); //system_message("n " . $n);
		$hesitants = array();
		$lengths = array();
		$deltas = array();

		for ($i=0;$i<$n;$i++)
		{
			echo "[".$envelopes[$i]['inf'].",".$envelopes[$i]['sup']."] ";
			$hesitants[$i] = toHesitant($envelopes[$i],$lengths[$i],$deltas[$i]);
			if ($hesitants[$i] != -1)
				echo "score=".$this->scoreFunction($hesitants[$i],$lengths[$i],$deltas[$i])."<br>";
		}

		/*
		* test intervalDominance. 
		* Example 2 in the paper
		*/
		$is1 = intervalDominance($envelopes[1]['inf'], $envelopes[1]['sup'], $envelopes[0]['inf'], $envelopes[0]['sup']);//must be 1 
		$is1ov2 = intervalDominance($envelopes[1]['inf'], $envelopes[1]['sup'], $envelopes[2]['inf'], $envelopes[2]['sup']);//must be 0.5
		$is3ov5 = intervalDominance($envelopes[2]['inf'], $envelopes[2]['sup'], $envelopes[0]['inf'], $envelopes[0]['sup']);//must be 0.6
		if ($this->debug)
			echo "intervalDominance " . $is1 . " " . $is1ov2 . " " . $is3ov5 . "<br>";
	}


}
