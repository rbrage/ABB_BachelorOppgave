<?php
$this->viewmodel->templatemenu = array("total" => "Combined Statisktics");
$this->template("Shared");
?>

<section id="total">
	<div id="total" class="jqplot-target"></div>
	<script type="text/javascript">
	$(document).ready(function(){
        $.jqplot.config.enablePlugins = true;
        var s1 = [20, 58, 70, 88, 115, 103, 85, 74, 62, 35];
        var s2 = [15, 61, 74, 85, 110, 95, 83, 76, 60, 30];
        var s3 = [17.5, 65, 69, 86, 100, 100, 8, 75, 58, 20];
        var ticks = ['0-5', '5-10', '10-15', '15-20', '20-25', '25-30', '30-35', '35-40', '40-45', '45-50'];
         
        plot1 = $.jqplot('total', [s1, s2, s3], {
            // Only animate if we're not using excanvas (not in IE 7 or IE 8)..
            animate: !$.jqplot.use_excanvas,
            seriesDefaults:{
                renderer:$.jqplot.BarRenderer,
                pointLabels: { show: true }
            },
            axes: {
                xaxis: {
                    renderer: $.jqplot.CategoryAxisRenderer,
                    ticks: ticks
                }
            },
            highlighter: { show: false }
        });
    });
	</script>
</section>