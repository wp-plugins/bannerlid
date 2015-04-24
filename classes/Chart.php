<?php
namespace Bannerlid;

/**
 * This class is the server side representation of a chart. It's the
 * middle-man between the raw stat data and the chart.js which is 
 * the javascript library that builds our charts. 
 *
 * @since      1.0.0
 * @package    Bannerlid
 * @subpackage Bannerlid/classes
 * @author     Weblid <barrywebla@googlemail.com>
 */
abstract class Chart {

	/**
	 * A pool of colours to choose from
	 *
	 * @since 1.0.0
	*/
	public $colours = array("#F7464A", "#46BFBD", "#FFC870");

	/**
	 * Type of chart
	 *
	 * @since 1.0.0
	*/
	public $chart_type;

	/**
	 * The data after it's been processed
	 *
	 * @since 1.0.0
	*/
	public $encoded_data;

	/**
	 * The raw data direct from the database
	 *
	 * @since 1.0.0
	*/
	public $rawdata;

	/**
	 * Must match the canvas ID the chat will be drawn on
	 *
	 * @since 1.0.0
	*/
	public $id;

	/**
	 * Name of the chart
	 *
	 * @since 1.0.0
	*/
	public $name;

	/**
	* Geters * Setters
	*/
	public function setRawdata($rawdata){
		$this->rawdata = $rawdata;
	}

	public function setLabels($labels){
		$this->labels = $labels;
	}

	public function setDatasets($datasets){
		$this->datasets = $datasets;
	}

	/**
	 * Returns a colour from the $this->colours pallette. 
	 *
	 * @param (int) $i - An incremental number to indicate the colour. If the number
	 * is higher than the total colours, the pointer simply goes back to the beginning.
	 * @since 1.0.0
	 * @return (string) Colour
	*/
	public function getColour($i){
		$total_colours = count($this->colours);
		if($i >= $total_colours){
			$remainder = $total_colours % $i;
			return $this->colours[$remainder];
		} else {
			return $this->colours[$i];
		}
	}


}

/**
 * Extends the core chart class and created charts as
 * bar charts.
 *
 * @since      1.0.0
 * @package    Bannerlid
 * @subpackage Bannerlid/classes
 * @author     Barry Mason <barrywebla@googlemail.com>
 */
class BarChart extends Chart {

	/**
	 * An array of chart labels
	 *
	 * @since 1.0.0
	*/
	public $labels = array();

	/**
	 * An array of datasets as objects
	 *
	 * @since 1.0.0
	*/
	public $datasets;

    /**
    * Intantiate the chart witht he data needed to 
    * build it.
    *
    * @access public
    * @param $name (string)
    * @param $id (string)
    * @return void
    */
	public function __construct($name, $id){

		$this->chart_type = 'Bar';
		$this->id = $id;
		$this->name = $name;
	}

	/**
	 * Convers raw db data to chart ready labels and datasets 
	 * according to the chart type.
	 *
	 * @param (array) Raw db data in an array
	 * @return void
	*/
	public function createDataFromRaw(array $raw){
		if(!empty($raw)){
			$this->setRawdata($raw);
			$labels = array();
			$data = array();

			foreach($raw as $row){
				$labels[] = $row->name;
				$data[] = $row->total;
			}

			$this->setLabels($labels);
			$this->setDatasets($this->parseDataset($data));
			$this->assignChartVars();
		}
	}

	/**
	* Parse dataset
	*/
	public function parseDataset($data){
		$colour= $this->colours[array_rand($this->colours)];

		$datasets = array(array(
			"label" => "Dataset 1",
			"fillColor" => $colour,
			"strokeColor" => $colour,
			"highlightFill" => $colour,
			"highlightStroke" => $colour,
			"data" => $data
		));
		return $datasets;
	}

	/**
	 * Send the variables we'll need to build our chart
	 *
	 * @see $this->localizeChartScript()
	 * @since 1.0.0
	*/
	public function assignChartVars(){

		$id = $this->id;
		$label_data = json_encode($this->labels);
		$chart_data = json_encode($this->datasets);

		$alldata = array(
			"type" => $this->chart_type,
			"id" => $id,
			"labels" => $label_data,
			"datasets" => $chart_data
		);

		$this->encoded_data = $alldata;
		//wp_localize_script('chart','alldata',$alldata);
	}

	/**
	 * Outputs the chart as html
	 *
	 * @see $this->localizeChartScript()
	 * @since 1.0.0
	 * @return void
	*/
	public function showChart(){
		echo '<canvas id="'.$this->id.'" class="chart_canvas"></canvas>';
		echo '<div id="'.$this->id.'-legend" class="chart-legend"></div>';

		echo '<script>';
		echo 'jQuery(document).ready(function(){ ';
		echo 'var bar_chart = barChart("'.$this->id.'", \''.$this->encoded_data['labels'].'\', \''.$this->encoded_data['datasets'].'\'); ';
		echo 'var legend = bar_chart.generateLegend(); ';
		echo 'jQuery("#'.$this->id.'-legend").html(legend); ';
		echo '});';
		echo '</script>';
		
	}
}

/**
 * Extends the core chart class and created charts as
 * doughnut charts.
 *
 * @since      1.0.0
 * @package    Bannerlid
 * @subpackage Bannerlid/classes
 * @author     Barry Mason <barrywebla@googlemail.com>
 */

class DoughnutChart extends Chart {

	/**
	 * An array of datasets as objects
	 *
	 * @since 1.0.0
	*/
	public $pieces;

	/*
	 * Setter for $pieces
	 * @access public
	*/
	public function setPieces($pieces){
		$this->pieces = $pieces;
	}

    /**
    * Intantiate the chart witht he data needed to 
    * build it.
    *
    * @access public
    * @param $name (string)
    * @param $id (string)
    * @return void
    */
	public function __construct($name, $id){

		$this->chart_type = 'Doughnut';
		$this->id = $id;
		$this->name = $name;
	}

	/**
	 * Convers raw db data to chart ready labels and datasets 
	 * according to the chart type.
	 *
	 * @param (array) Raw db data in an array
	 * @return void
	*/
	public function createDataFromRaw(array $raw){
		if(!empty($raw)){
			$this->setRawdata($raw);
			
			$pieces = array();
			
			$i = 0;
			foreach($raw as $row){
				$colour= $this->getColour($i);
				$pieces[] = array(
					"value" => $row->total, 
					"label" => $row->name,
					"color" => $colour,
					"highlight" => $colour,
				);
				$i++;
			}

			$this->setPieces($pieces);
			$this->assignChartVars();
		}
	}

	/**
	 * Send the variables we'll need to build our chart
	 *
	 * @see $this->localizeChartScript()
	 * @since 1.0.0
	*/
	public function assignChartVars(){

		$alldata = array(
			"type" => $this->chart_type,
			"id" => $this->id,
			"pieces" => json_encode($this->pieces),
		);

		$this->encoded_data = $alldata;
		//wp_localize_script('chart','alldata', $alldata);
	}

	/**
	 * Outputs the chart as html
	 *
	 * @see $this->localizeChartScript()
	 * @since 1.0.0
	 * @return void
	*/
	public function showChart(){

		echo '<canvas id="'.$this->id.'" class="chart_canvas"></canvas>';
		
		echo '<div id="'.$this->id.'-legend" class="chart-legend"></div>';

		echo '<script>';
		echo 'jQuery(document).ready(function(){';
		echo 'var doughnut_chart = doughnutChart("'.$this->id.'", \''.$this->encoded_data['pieces'].'\'); ';
		echo 'var legend = doughnut_chart.generateLegend(); ';
		echo 'jQuery("#'.$this->id.'-legend").html(legend); ';
		echo '});';
		echo '</script>';
		
	}
}