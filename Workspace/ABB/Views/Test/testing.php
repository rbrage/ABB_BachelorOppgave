<html>
<head>
    <title>Rotate example</title>
<script>   
      var constants = {
        canvasWidth: 950, // In pixels.
        canvasHeight: 650, // In pixels.
        leftArrow: 37,//in use for orbit
        upArrow: 38,//in use for orbit
        rightArrow: 39,//in use for orbit
        downArrow: 40,//in use for orbit
        };
      
      // These are constants too but I've removed them from the above constants literal to ease typing and improve clarity.
      var X = 0;
      var Y = 1;
      var Z = 2;

      // -----------------------------------------------------------------------------------------------------  

      var controlKeyPressed = false; // Shared between processKeyDown() and processKeyUp().
     
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

        canvasElement.getContext('2d').translate(constants.canvasWidth/2, constants.canvasHeight/2); 
        // Translate the surface's origin to the center of the canvas.
        
        document.body.appendChild(canvasElement); // Make the canvas element a child of the body element.
      }

        
      // -----------------------------------------------------------------------------------------------------
            
      Surface.prototype.draw = function()
      {
        var myCanvas = document.getElementById("myCanvas"); // Required for Firefox.
        var ctx = myCanvas.getContext("2d");

       
			
        
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
      
      window.addEventListener('load', onloadInit, false); // Perform processing that must occur after the page has fully loaded.
    </script>
</head>
<body onload="draw();">        
    <div>
        <h2>3D plot</h2>
				<canvas width="950" height="650" id="myCanvas" style="border:1px solid #000000;"></canvas>
    </div>
</body>
</html>