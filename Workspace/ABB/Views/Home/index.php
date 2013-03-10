<?php 
$this->viewmodel->templatemenu = array("last" => "Last 10 Points", "plot3d" => "3D Plot");

$this->Template("sheard");
?>

<section id="last">
	<div class="page-header">
		<h2>Last 10 points</h2>
	</div>
	<?php 
	$list = $this->viewmodel->arr->getCachedArrayList();
	$size = $list->size();

	if($size != 0){
			?>
	<table class="table table-striped">
		<thead>
			<tr>
				<th>X</th>
				<th>Y</th>
				<th>Z</th>
				<th>Time</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			for ($i = $size -1; ($size - $i) <= 10 && $i >= 0 ;$i--){
						echo "
		<tr>
		<td>".$list->get($i)->x."</td>
		<td>".$list->get($i)->y."</td>
		<td>".$list->get($i)->z."</td>
			<td>".$list->get($i)->timestamp." ms</td>
			</tr>";
					}
					?>
		</tbody>
	</table>
	<?php 
			}
			else{
			?>
	<p>Can't find any points in the cache.</p>
	<?php 
			}
			?>
</section>

<section id="plot3d">
	<div class="page-header">
		<h2>3D plot</h2>
		<div id="3DPlotDiv" style="float: left; width: 950px; height: 700px; background:#edebeb;"
			oncontextmenu="return false"
			onmouseover="document.body.style.overflow='hidden';"
			onmouseout="document.body.style.overflow='auto';">
			
		</div>

		<div class="row span12">
			<input id="allowWebGL" type="checkbox" checked=""
				onclick="toggleChart(this)"> <span style="color: #000">Use webGL</span>
			<p>Hold down shift for zoom, and ctrl for move</p>
		</div>
	</div>

</section>


<script type="text/javascript">

			var point3DPlot;

			setUp();
			
			function setUp()
			{

			var points = new Array();

			<?php 
	      			$list = $this->viewmodel->arr->getCachedArrayList();
	      			$size = $list->size();
	      			for ($i = 0; $i<=$size-1 ;$i++){
	      				?>points[<?php echo $i ?>] = new point(<?php echo $list->get($i)->x ?>,<?php echo $list->get($i)->y?>,<?php echo $list->get($i)->z?>);
	      				<?php 
	      			}
	      			?>

	      		var data = {width: 950, height: 650, axisSize: 400};
	      		var container = document.getElementById("3DPlotDiv");
	      		 
	      		point3DPlot = new PlotWebGLCanvas(document.getElementById("3DPlotDiv"), points, data);
				


			};
			
				
				function point(x, y, z)
			      
			      {       
			        return [x, y, z]; 
			    };
				

			</script>




<!-- New script for 3D plot -->
<!--  
<script type="text/javascript">
		
		    var surfacePlot;
		    
			
			function setUp()
			{
				var numRows = 50;
				var numCols = 50;
				
				var tooltipStrings = new Array();
				var values = new Array();
				var data = {nRows: numRows, nCols: numCols, formattedValues: values};
				var points = new Array();
				
				var d = 360 / numRows;
				var idx = 0;

				 <?php 
// 			      			$list = $this->viewmodel->arr->getCachedArrayList();
// 			      			$size = $list->size();
// 			      			for ($i = 0; $i<=$size-1 ;$i++){
			      				?>points[<?php //echo $i ?>] = new point(<?php //echo $list->get($i)->x ?>,<?php //echo $list->get($i)->y?>,<?php //echo $list->get($i)->z?>);
			      				<?php 
			      			//}
			      			?>
			
				
				for (var i = 0; i < numRows; i++) 
				{
					values[i] = new Array();
					
					for (var j = 0; j < numCols; j++)
					{
						var value = (Math.cos(i * d * Math.PI / 180.0) * Math.cos(j * d * Math.PI / 180.0) + Math.sin(i * d * Math.PI / 180.0));
						
						values[i][j] = 1;
						
						tooltipStrings[idx] = "x:" + i + ", y:" + j + " = " + value;
						
						idx++;
					}
				}

				surfacePlot = new SurfacePlot(document.getElementById("surfacePlotDiv"));
				
				
				// Don't fill polygons in IE. It's too slow.
				var fillPly = true;
				
				// Define a colour gradient.
				var colour1 = {red:0, green:0, blue:255};
				var colour2 = {red:0, green:255, blue:255};
				var colour3 = {red:0, green:255, blue:0};
				var colour4 = {red:255, green:255, blue:0};
				var colour5 = {red:255, green:0, blue:0};
				var colours = [colour1, colour2, colour3, colour4, colour5];
				
				// Axis labels.
				var xAxisHeader	= "X-axis";
				var yAxisHeader	= "Y-axis";
				var zAxisHeader	= "Z-axis";
				
				var renderDataPoints = false;
				var background = '#ffffff';
				var axisForeColour = '#000000';
				var hideFloorPolygons = true;
				var chartOrigin = {x: 425, y:325};
				
				// Options for the basic canvas pliot.
				var basicPlotOptions = {fillPolygons: fillPly, tooltips: tooltipStrings, renderPoints: renderDataPoints };
				
				
				// Options for the webGL plot.
				var xLabels = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
				var yLabels = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
				var zLabels = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]; // These labels ar eused when autoCalcZScale is false;
				var glOptions = {xLabels: xLabels, yLabels: yLabels, zLabels: zLabels, chkControlId: "allowWebGL" ,autoCalcZScale: false};
				
				// Options common to both types of plot.
				var options = {xPos: 0, yPos: 0, width: 950, height: 650, colourGradient: colours, 
					xTitle: xAxisHeader, yTitle: yAxisHeader, zTitle: zAxisHeader, 
					backColour: background, axisTextColour: axisForeColour, hideFlatMinPolygons: hideFloorPolygons, origin: chartOrigin};
				
				surfacePlot.draw(data, options, basicPlotOptions, glOptions, points);
				
				
				// Link the two charts for rotation.
				var plot1 = surfacePlot.getChart();
				
				
				plot1.otherPlots = [plot1];  
				
			}
			
			setUp();
			
			function toggleChart(chkbox)
            { 
                surfacePlot.redraw();
                
            } 
			function point(x, y, z)
		      
		      {       
		        return [x, y, z]; 
		         }
			
		</script>-->
<!-- 
<script>   
      var constants = {
        canvasWidth:  950, // In pixels.
        canvasHeight: 650, // In pixels.
        leftArrow: 37,//in use for orbit
        upArrow: 38,//in use for orbit
        rightArrow: 39,//in use for orbit
        downArrow: 40,//in use for orbit
        
        colorMap: ["#060", "#090", "#0C0", "#0F0", "#9F0", "#9C0", "#990", "#960", "#930", "#900", "#C00"], // There are eleven possible "vertical" color values for the surface, based on the last row of http://www.cs.siena.edu/~lederman/truck/AdvanceDesignTrucks/html_color_chart.gif
        pointWidth: 2, // The size of a rendered surface point (i.e., rectangle width and height) in pixels.
        dTheta: 0.02, // The angle delta, in radians, by which to rotate the surface per key press.
        surfaceScale: 3 // An empirically derived constant that makes the surface a good size for the given canvas size.
      };
      
      // These are constants too but I've removed them from the above constants literal to ease typing and improve clarity.
      var X = 0;
      var Y = 1;
      var Z = 2;
	  var T = 3;
      // -----------------------------------------------------------------------------------------------------  

      var controlKeyPressed = false; // Shared between processKeyDown() and processKeyUp().
      var surface = new Surface(); // A set of points (in vector format) representing the surface.

      // -----------------------------------------------------------------------------------------------------

      function point(x, y, z, t)
      /*
        Given a (x, y, z) surface point, returns the 3 x 1 vector form of the point.
      */
      {       
        return [x, y, z, t]; // Return a 3 x 1 vector representing a traditional (x, y, z) surface point. This vector form eases matrix multiplication.
      }

      function line(x, y, z)
      
      {       
        return [x, y, z]; 
      }
      
      // -----------------------------------------------------------------------------------------------------
      
      function Surface()
      /*
        A surface is a list of (x, y, z) points, in 3 x 1 vector format. This is a constructor function.
      */
      {
        this.points = []; // An array of surface points in vector format. That is, each element of this array is a 3 x 1 array, as in [ [x1, y1, z1], [x2, y2, z2], [x3, y3, z3], ... ]
		this.CoorLines = [];
        }
      
      // -----------------------------------------------------------------------------------------------------  
      
      Surface.prototype.generate = function()
      /*
        Creates a list of (x, y, z) points (in 3 x 1 vector format) representing the surface.
      */
      {

    	 
      			
      			this.CoorLines[0] = line(100, 0, 0);
      			this.CoorLines[1] = line(0, 100, 0);
      			this.CoorLines[2] = line(0, 0, 100);	
      }

      // -----------------------------------------------------------------------------------------------------
            
      Surface.prototype.color = function()
      /*
        The color of a surface point is a function of its z-coordinate height.
      */
      {
        var t; // The z-coordinate for a given surface point (x, y, z).

        this.tMin = this.tMax = this.points[0][T]; // A starting value. Note that zMin and zMax are custom properties that could possibly be useful if this code is extended later.

        for (var i = 0; i < this.points.length; i++)
        {            
          t = this.points[i][T];
          if (t < this.tMin) { this.tMin = t; }
          if (t > this.tMax) { this.tMax = t; }
        }   
              
        var tDelta = Math.abs(this.tMax - this.tMin) / constants.colorMap.length; 

        for (var i = 0; i < this.points.length; i++)
        {
          this.points[i].color = constants.colorMap[ Math.floor( (this.points[i][T]-this.tMin)/tDelta ) ];
        }
                
        /* Note that the prior FOR loop is functionally equivalent to the follow (much less elegant) loop:       
        for (var i = 0; i < this.points.length; i++)
        {
          if (this.points[i][Z] <= this.zMin + zDelta) {this.points[i].color = "#060";}
          else if (this.points[i][Z] <= this.zMin + 2*zDelta) {this.points[i].color = "#090";}
          else if (this.points[i][Z] <= this.zMin + 3*zDelta) {this.points[i].color = "#0C0";}
          else if (this.points[i][Z] <= this.zMin + 4*zDelta) {this.points[i].color = "#0F0";}
          else if (this.points[i][Z] <= this.zMin + 5*zDelta) {this.points[i].color = "#9F0";}
          else if (this.points[i][Z] <= this.zMin + 6*zDelta) {this.points[i].color = "#9C0";}
          else if (this.points[i][Z] <= this.zMin + 7*zDelta) {this.points[i].color = "#990";}
          else if (this.points[i][Z] <= this.zMin + 8*zDelta) {this.points[i].color = "#960";}
          else if (this.points[i][Z] <= this.zMin + 9*zDelta) {this.points[i].color = "#930";}
          else if (this.points[i][Z] <= this.zMin + 10*zDelta) {this.points[i].color = "#900";}          
          else {this.points[i].color = "#C00";}
        }
        */
      }
      
      // -----------------------------------------------------------------------------------------------------
      
      function appendCanvasElement()
      /*
        Creates and then appends the "myCanvas" canvas element to the DOM.
      */
      {
    	  var canvasElement = document.createElement('canvas');
          var ctx = canvasElement.getContext('2d');
          canvasElement.width = constants.canvasWidth;
          canvasElement.height = constants.canvasHeight;
          canvasElement.id = "myCanvas";

          
          ctx.translate(constants.canvasWidth/2, constants.canvasHeight/2); // Translate the surface's origin to the center of the canvas.
        
          document.getElementById('canvas').appendChild(canvasElement);
        //document.body.appendChild(canvasElement); // Make the canvas element a child of the body element.
      }

      //------------------------------------------------------------------------------------------------------

      Surface.prototype.sortByTimeIndex = function(A, B) 
      {
        return A[T] - B[T]; // Determines if point A is behind, in front of, or at the same level as point B (with respect to the z-axis).
      }
            
      // -----------------------------------------------------------------------------------------------------
            
      Surface.prototype.draw = function()
      {
        var myCanvas = document.getElementById("myCanvas"); // Required for Firefox.
        var ctx = myCanvas.getContext("2d");

        this.points = surface.points.sort(surface.sortByTimeIndex);// Sort the set of points based on relative z-axis position. If the points are visibly small, you can sort of get away with removing this step.

        
        
        for (var i = 0; i < this.points.length; i++)
        {
          ctx.fillStyle = this.points[i].color; 
          ctx.fillRect(this.points[i][X] * constants.surfaceScale, this.points[i][Y] * constants.surfaceScale, constants.pointWidth, constants.pointWidth);  
        } 

        
        ctx.beginPath();
        for(var i=0; i<this.CoorLines.length; i++){
        	ctx.moveTo(0,0);
            ctx.lineTo(this.CoorLines[i][X] * constants.surfaceScale, this.CoorLines[i][Y] * constants.surfaceScale);
            }
        ctx.stroke();
           
      }
      
      // -----------------------------------------------------------------------------------------------------
      
      Surface.prototype.multi = function(R)
      /*
        Assumes that R is a 3 x 3 matrix and that this.points (i.e., P) is a 3 x n matrix. This method performs P = R * P.
      */
      {
        var Px = 0, Py = 0, Pz = 0; // Variables to hold temporary results.
        var P = this.points; // P is a pointer to the set of surface points (i.e., the set of 3 x 1 vectors).
        var CL = this.CoorLines;
        var sum; // The sum for each row/column matrix product.
  
        for (var V = 0; V < P.length; V++) // For all 3 x 1 vectors in the point list.
        {
          Px = P[V][X], Py = P[V][Y], Pz = P[V][Z];
          for (var Rrow = 0; Rrow < 3; Rrow++) // For each row in the R matrix.
          {
            sum = (R[Rrow][X] * Px) + (R[Rrow][Y] * Py) + (R[Rrow][Z] * Pz);
            P[V][Rrow] = sum;
          }
        }     
        for (var V = 0; V < CL.length; V++) // For all 3 x 1 vectors in the point list.
        {
          Px = CL[V][X], Py = CL[V][Y], Pz = CL[V][Z];
          for (var Rrow = 0; Rrow < 3; Rrow++) // For each row in the R matrix.
          {
            sum = (R[Rrow][X] * Px) + (R[Rrow][Y] * Py) + (R[Rrow][Z] * Pz);
            CL[V][Rrow] = sum;
          }
        }     
      }
      
      // -----------------------------------------------------------------------------------------------------

      Surface.prototype.erase = function()
      {
        var myCanvas = document.getElementById("myCanvas"); // Required for Firefox.
        var ctx = myCanvas.getContext("2d");

        ctx.clearRect(-constants.canvasWidth/2, -constants.canvasHeight/2, myCanvas.width, myCanvas.height);
      }

      // -----------------------------------------------------------------------------------------------------

      Surface.prototype.xRotate = function(sign)
      /*
        Assumes "sign" is either 1 or -1, which is used to rotate the surface "clockwise" or "counterclockwise".
      */
      {
        var Rx = [ [0, 0, 0],
                   [0, 0, 0],
                   [0, 0, 0] ]; // Create an initialized 3 x 3 rotation matrix.
                           
        Rx[0][0] = 1;
        Rx[0][1] = 0; // Redundant but helps with clarity.
        Rx[0][2] = 0; 
        Rx[1][0] = 0; 
        Rx[1][1] = Math.cos( sign*constants.dTheta );
        Rx[1][2] = -Math.sin( sign*constants.dTheta );
        Rx[2][0] = 0; 
        Rx[2][1] = Math.sin( sign*constants.dTheta );
        Rx[2][2] = Math.cos( sign*constants.dTheta );
        
        this.multi(Rx); // If P is the set of surface points, then this method performs the matrix multiplcation: Rx * P
        this.erase(); // Note that one could use two canvases to speed things up, which also eliminates the need to erase.
        this.draw();
      }
         
      // -----------------------------------------------------------------------------------------------------
         
      Surface.prototype.yRotate = function(sign)
      /*
        Assumes "sign" is either 1 or -1, which is used to rotate the surface "clockwise" or "counterclockwise".
      */      
      {
        var Ry = [ [0, 0, 0],
                   [0, 0, 0],
                   [0, 0, 0] ]; // Create an initialized 3 x 3 rotation matrix.
                           
        Ry[0][0] = Math.cos( sign*constants.dTheta );
        Ry[0][1] = 0; // Redundant but helps with clarity.
        Ry[0][2] = Math.sin( sign*constants.dTheta );
        Ry[1][0] = 0; 
        Ry[1][1] = 1;
        Ry[1][2] = 0; 
        Ry[2][0] = -Math.sin( sign*constants.dTheta );
        Ry[2][1] = 0; 
        Ry[2][2] = Math.cos( sign*constants.dTheta );
        
        this.multi(Ry); // If P is the set of surface points, then this method performs the matrix multiplcation: Rx * P
        this.erase(); // Note that one could use two canvases to speed things up, which also eliminates the need to erase.
        this.draw();
      }
 
      // -----------------------------------------------------------------------------------------------------
         
      Surface.prototype.zRotate = function(sign)
      /*
        Assumes "sign" is either 1 or -1, which is used to rotate the surface "clockwise" or "counterclockwise".
      */      
      {
        var Rz = [ [0, 0, 0],
                   [0, 0, 0],
                   [0, 0, 0] ]; // Create an initialized 3 x 3 rotation matrix.
                           
        Rz[0][0] = Math.cos( sign*constants.dTheta );
        Rz[0][1] = -Math.sin( sign*constants.dTheta );        
        Rz[0][2] = 0; // Redundant but helps with clarity.
        Rz[1][0] = Math.sin( sign*constants.dTheta );
        Rz[1][1] = Math.cos( sign*constants.dTheta );
        Rz[1][2] = 0;
        Rz[2][0] = 0
        Rz[2][1] = 0;
        Rz[2][2] = 1;
        
        this.multi(Rz); // If P is the set of surface points, then this method performs the matrix multiplcation: Rx * P
        this.erase(); // Note that one could use two canvases to speed things up, which also eliminates the need to erase.
        this.draw();
      }
     
      // -----------------------------------------------------------------------------------------------------
			var downX = 0;            // mouse starting positions
			var downY = 0;
			var moveX = 0;           // current element offset
			var moveY = 0;
			var pressed = false;
			
      function processMouseDown(e){
    	  downX=e.clientX;
    	  downY=e.clientY;
    	  var coor="Coordinates: (" + downX + "," + downY + ")";
    	  document.getElementById("demo1").innerHTML=coor;
    	  pressed = true;

    	  

      }
      function processMouseUp(e){
		var coor="Coordinates: (" + downX + "," + downY + ")";
  	  	document.getElementById("demo1").innerHTML=coor;
    	  pressed=false;
          }
      
      function processMouseMoved(e){
    	 moveX=e.clientX;
    	 moveY=e.clientY;
    	 var coor1="Coordinates: (" + moveX + "," + moveY + ")";
    	 document.getElementById("demo").innerHTML=coor1;
    	 if(pressed){
        	 
    		 if(downX>moveX){
        		 surface.yRotate(-1);
    	         e.preventDefault(); 
            	 }
        	 	if(downX<moveX){
        		 surface.yRotate(1);
    	         e.preventDefault(); 
            	 }
    	 	 if(downY>moveY){
        		 surface.xRotate(1);
    	         e.preventDefault(); 
            	 }
        		 if(downY<moveY){
        		 surface.xRotate(-1);
    	         e.preventDefault(); 
            	 }
    	 	
		}
      }
     function processZoom(e) {
         if(e.AltKey){
    	var test = e.detail? e.detail*(-120) : e.wheelDelta;
    	document.getElementById("demo1").innerHTML=test;
    	}
	} 
      
      
      
      function processKeyDown(evt)
      {                    
        if (evt.ctrlKey)
        {
          switch (evt.keyCode)
          {
            case constants.upArrow: 
              // No operation other than preventing the default behavior of the arrow key.
              evt.preventDefault(); // This prevents the default behavior of the arrow keys, which is to scroll the browser window when scroll bars are present. The user can still scroll the window with the mouse.              
              break;
            case constants.downArrow:
              // No operation other than preventing the default behavior of the arrow key.
              evt.preventDefault();
              break;
            case constants.leftArrow:
              // console.log("ctrl+leftArrow");
              surface.zRotate(-1); // The sign determines if the surface rotates "clockwise" or "counterclockwise". 
              evt.preventDefault(); 
              break;
            case constants.rightArrow:
              // console.log("ctrl+rightArrow");
              surface.zRotate(1);
              evt.preventDefault(); 
              break;
          }
          return; // When the control key is pressed, only the left and right arrows have meaning, no need to process any other key strokes (i.e., bail now).
        }
        
        // Assert: The control key is not pressed.

        switch (evt.keyCode)
        {
          case constants.upArrow:
            // console.log("upArrow");
            surface.xRotate(1);
            evt.preventDefault(); 
            break;
          case constants.downArrow:
            // console.log("downArrow");
            surface.xRotate(-1); 
            evt.preventDefault(); 
            break;
          case constants.leftArrow:
            // console.log("leftArrow");
            surface.yRotate(-1);  
            evt.preventDefault(); 
            break;
          case constants.rightArrow:
            // console.log("rightArrow");
            surface.yRotate(1);   
            evt.preventDefault(); 
            break;
        }
      }
               
      // -----------------------------------------------------------------------------------------------------
      
      function onloadInit()
      {
        appendCanvasElement(); // Create and append the canvas element to the DOM.
        surface.draw(); // Draw the surface on the canvas.
        document.addEventListener('onmousedown', processMouseDown, false); 
        document.addEventListener('onmouseup', processMouseUp, false);
        document.addEventListener('onmousemove', processMouseMove, false);
        document.addEventListener('onmousewheel', processZoom, false);
        
        
        
        document.addEventListener('keydown', processKeyDown, false); // Used to detect if the control key has been pressed. 
      }

      // -----------------------------------------------------------------------------------------------------
      
      surface.generate(); // Creates the set of points reprsenting the surface. Must be called before color().
      surface.color(); // Based on the min and max z-coordinate values, chooses colors for each point based on the point's z-ccordinate value (i.e., height).
      window.addEventListener('load', onloadInit, false); // Perform processing that must occur after the page has fully loaded.
    </script>
 -->
