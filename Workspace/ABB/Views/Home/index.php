<?php 
$this->Template("sheard");
?>

<div class="row">
	<div class=span9>

		<div id="result"></div>
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
						<td>".$list->get($i)->timestamp." ms - Date and time: " . date("r", round($list->get($i)->timestamp/1000)) . "</td>
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
				<canvas width="600" height="600" id="myCanvas"></canvas>
			</div>
		</section>

		<section id="point">
			<div class="page-header">
				<h2>Table view</h2>
			</div>
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

					for ($i = 0; $i<=$size-1 ;$i++){
						echo "
		<tr>
		<td>".$list->get($i)->x."</td>
				<td>".$list->get($i)->y."</td>
				<td>".$list->get($i)->z."</td>
					<td>".$list->get($i)->timestamp."</td>
					</tr>";
					}
					?>
				</tbody>
			</table>
		</section>
	</div>



	<div class="span3">
		<div data-spy="affix">
			<div class="alert alert-info">
				<p class="nav-header">Infomation</p>
				<table class="table table-condensed">
					<tbody>
						<tr>
							<td>Number of trigger points:</td>
							<td id="cachesize"><?php echo $this->viewmodel->listsize ?></td>
						</tr>
						<tr>
							<td>Used memory size:</td>
							<td id="memorysize"><?php echo $this->viewmodel->listmemory ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<script>
if(typeof(EventSource)!=="undefined")
  {
  var source=new EventSource("http://localhost:8888/SSE/BasicInfo");
  source.addEventListener("cachesize", function (event) {
      $("#cachesize").html(event.data);
  }, true);

  source.addEventListener("memorysize", function (event) {
      $("#memorysize").html(event.data + "k");
  }, true);
  
  }
else
  {
  	document.getElementById("result").innerHTML="Sorry, your browser does not support server-sent events...";
  }

</script>

<script>   
      var constants = {
        canvasWidth: 1000, // In pixels.
        canvasHeight: 600, // In pixels.
        leftArrow: 37,
        upArrow: 38,
        rightArrow: 39,
        downArrow: 40,
        xMin: 0, // These four max/min values define a square on the xy-plane that the surface will be plotted over.
        xMax: 100,
        yMin: 0,
        yMax: 100, 
        xDelta: 0.2, // Make smaller for more surface points. 
        yDelta: 0.2, // Make smaller for more surface points. 
        colorMap: ["#060", "#090", "#0C0", "#0F0", "#9F0", "#9C0", "#990", "#960", "#930", "#900", "#C00"], // There are eleven possible "vertical" color values for the surface, based on the last row of http://www.cs.siena.edu/~lederman/truck/AdvanceDesignTrucks/html_color_chart.gif
        pointWidth: 2, // The size of a rendered surface point (i.e., rectangle width and height) in pixels.
        dTheta: 0.05, // The angle delta, in radians, by which to rotate the surface per key press.
        surfaceScale: 6 // An empirically derived constant that makes the surface a good size for the given canvas size.
      };
      
      // These are constants too but I've removed them from the above constants literal to ease typing and improve clarity.
      var X = 0;
      var Y = 1;
      var Z = 2;

      // -----------------------------------------------------------------------------------------------------  

      var controlKeyPressed = false; // Shared between processKeyDown() and processKeyUp().
      var surface = new Surface(); // A set of points (in vector format) representing the surface.

      // -----------------------------------------------------------------------------------------------------

      function point(x, y, z)
      /*
        Given a (x, y, z) surface point, returns the 3 x 1 vector form of the point.
      */
      {       
        return [x, y, z]; // Return a 3 x 1 vector representing a traditional (x, y, z) surface point. This vector form eases matrix multiplication.
      }
      
      // -----------------------------------------------------------------------------------------------------
      
      function Surface()
      /*
        A surface is a list of (x, y, z) points, in 3 x 1 vector format. This is a constructor function.
      */
      {
        this.points = []; // An array of surface points in vector format. That is, each element of this array is a 3 x 1 array, as in [ [x1, y1, z1], [x2, y2, z2], [x3, y3, z3], ... ]
      }
      
      // -----------------------------------------------------------------------------------------------------  
      
      Surface.prototype.equation = function(x, y)
      /*
        Given the point (x, y), returns the associated z-coordinate based on the provided surface equation, of the form z = f(x, y).
      */
      {
        var d = Math.sqrt(x*x + y*y); // The distance d of the xy-point from the z-axis.
        
        return 4*(Math.sin(d) / d); // Return the z-coordinate for the point (x, y, z). 
      }
      
      // -----------------------------------------------------------------------------------------------------  
      
      Surface.prototype.generate = function()
      /*
        Creates a list of (x, y, z) points (in 3 x 1 vector format) representing the surface.
      */
      {

    	  <?php 
      			$list = $this->viewmodel->arr->getCachedArrayList();
      			$size = $list->size();
      			for ($i = 0; $i<=$size-1 ;$i++){
      				?>this.points[<?php echo $i ?>] = point(<?php echo $list->get($i)->x ?>,<?php echo $list->get($i)->y?>,<?php echo $list->get($i)->z?>);
      				<?php 
      			}
      			?>
  				
      }

      // -----------------------------------------------------------------------------------------------------
            
      Surface.prototype.color = function()
      /*
        The color of a surface point is a function of its z-coordinate height.
      */
      {
        var z; // The z-coordinate for a given surface point (x, y, z).
        
        this.zMin = this.zMax = this.points[0][Z]; // A starting value. Note that zMin and zMax are custom properties that could possibly be useful if this code is extended later.
        for (var i = 0; i < this.points.length; i++)
        {            
          z = this.points[i][Z];
          if (z < this.zMin) { this.zMin = z; }
          if (z > this.zMax) { this.zMax = z; }
        }   
              
        var zDelta = Math.abs(this.zMax - this.zMin) / constants.colorMap.length; 

        for (var i = 0; i < this.points.length; i++)
        {
          this.points[i].color = constants.colorMap[ Math.floor( (this.points[i][Z]-this.zMin)/zDelta ) ];
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
        
        canvasElement.width = constants.canvasWidth;
        canvasElement.height = constants.canvasHeight;
        canvasElement.id = "myCanvas";

        canvasElement.getContext('2d').translate(constants.canvasWidth/2, constants.canvasHeight/2); // Translate the surface's origin to the center of the canvas.
        
        document.body.appendChild(canvasElement); // Make the canvas element a child of the body element.
      }

      //------------------------------------------------------------------------------------------------------

      Surface.prototype.sortByZIndex = function(A, B) 
      {
        return A[Z] - B[Z]; // Determines if point A is behind, in front of, or at the same level as point B (with respect to the z-axis).
      }
            
      // -----------------------------------------------------------------------------------------------------
            
      Surface.prototype.draw = function()
      {
        var myCanvas = document.getElementById("myCanvas"); // Required for Firefox.
        var ctx = myCanvas.getContext("2d");

        //this.points = surface.points.sort(surface.sortByZIndex); // Sort the set of points based on relative z-axis position. If the points are visibly small, you can sort of get away with removing this step.

        for (var i = 0; i < this.points.length; i++)
        {
          //ctx.fillStyle = this.points[i].color; 
          ctx.fillRect(this.points[i][X] * constants.surfaceScale, this.points[i][Y] * constants.surfaceScale, constants.pointWidth, constants.pointWidth);  
        }    
      }
      
      // -----------------------------------------------------------------------------------------------------
      
      Surface.prototype.multi = function(R)
      /*
        Assumes that R is a 3 x 3 matrix and that this.points (i.e., P) is a 3 x n matrix. This method performs P = R * P.
      */
      {
        var Px = 0, Py = 0, Pz = 0; // Variables to hold temporary results.
        var P = this.points; // P is a pointer to the set of surface points (i.e., the set of 3 x 1 vectors).
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
        document.addEventListener('keydown', processKeyDown, false); // Used to detect if the control key has been pressed.
      }

      // -----------------------------------------------------------------------------------------------------
      
      surface.generate(); // Creates the set of points reprsenting the surface. Must be called before color().
     // surface.color(); // Based on the min and max z-coordinate values, chooses colors for each point based on the point's z-ccordinate value (i.e., height).
      window.addEventListener('load', onloadInit, false); // Perform processing that must occur after the page has fully loaded.
    </script>
    
