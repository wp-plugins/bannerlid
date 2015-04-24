/**
 * The core function to create the client side HTML5 charts.
 * These function talk directly to chart.js
*/

/**
 * This function creates a bar chart
 *
 * @param (string) canvas_id - The ID of the cnavas to draw the chart on
 * @param (string) labels - An array of labels to show on the chart
 * @param (string) datasets - The formatted chart values
*/
var barChart = function(canvas_id, labels, datasets){
	
	var id_str = '#' + canvas_id;
	var ctx = jQuery(id_str).get(0).getContext("2d");

	var myNewChart = new Chart(ctx);

	var data = {
	    labels: jQuery.parseJSON(labels),
	    datasets: jQuery.parseJSON(datasets)
	};
	
	var options = {
	    scaleBeginAtZero : true,
	    scaleShowGridLines : true,
	    scaleGridLineColor : "rgba(0,0,0,.05)",
	    scaleGridLineWidth : 1,
	    scaleShowHorizontalLines: true,
	    scaleShowVerticalLines: true,
	    barShowStroke : true,
	    barStrokeWidth : 2,
	    barValueSpacing : 5,
	    barDatasetSpacing : 1,
	    legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<datasets.length; i++){%><li><span style=\"background-color:<%=datasets[i].fillColor%>\">&nbsp;&nbsp;</span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>"
	}

	var BarChart = new Chart(ctx).Bar(data, options);
	return BarChart;
}

/**
 * This function creates a doughnut chart
 *
 * @param (string) canvas_id - The ID of the cnavas to draw the chart on
 * @param (string) pieces - The formatted chart values
*/
var doughnutChart = function(canvas_id, pieces){

	var id_str = '#' + canvas_id;
	var pieces_obj = jQuery.parseJSON(pieces);
	  	
	var ctx = jQuery(id_str).get(0).getContext("2d");

	var myNewChart = new Chart(ctx);

	var data = pieces_obj;
	
	var options = {
	    segmentShowStroke : true,
	    segmentStrokeColor : "#fff",
	    segmentStrokeWidth : 2,
	    percentageInnerCutout : 60, // This is 0 for Pie charts
	    animationSteps : 100,
	    animationEasing : "easeOutBounce",
	    animateRotate : true,
	    animateScale : false,
	    legendTemplate : "<ul class=\"<%=name.toLowerCase()%>-legend\"><% for (var i=0; i<segments.length; i++){%><li><span style=\"background-color:<%=segments[i].fillColor%>\">&nbsp;&nbsp;</span><%if(segments[i].label){%><%=segments[i].label%><%}%></li><%}%></ul>"
	}

	var DoughnutChart = new Chart(ctx).Doughnut(data,options);
	var legend = DoughnutChart.generateLegend();
  	jQuery('#legend').html(legend);

	return DoughnutChart;
}