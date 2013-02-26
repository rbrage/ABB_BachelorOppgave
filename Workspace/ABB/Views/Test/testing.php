<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!--[if IE]><script type="text/javascript" src="../scripts/excanvas.js"></script><![endif]-->
<script type="text/javascript" src="../scripts/SurfacePlot.js"></script>
<script type="text/javascript" src="../scripts/ColourGradient.js"></script>
<script type="text/javascript" src="../scripts/glMatrix-0.9.5.min.js"></script>
<script type="text/javascript" src="../scripts/webgl-utils.js"></script>

<title>SurfacePlot test stub</title>

<script id="shader-fs" type="x-shader/x-fragment">
            #ifdef GL_ES
            precision highp float;
            #endif
            
            varying vec4 vColor;
            varying vec3 vLightWeighting;
            
            void main(void)
            {
            	gl_FragColor = vec4(vColor.rgb * vLightWeighting, vColor.a);
            }
        </script>

<script id="shader-vs" type="x-shader/x-vertex">
            attribute vec3 aVertexPosition;
            attribute vec3 aVertexNormal;
            attribute vec4 aVertexColor;

           
            uniform mat4 uMVMatrix;
            uniform mat4 uPMatrix;
            uniform mat3 uNMatrix;
            varying vec4 vColor;
            
            uniform vec3 uAmbientColor;
            uniform vec3 uLightingDirection;
            uniform vec3 uDirectionalColor;
            varying vec3 vLightWeighting;
            
            void main(void)
            {

				
				gl_PointSize = 2.0;
                gl_Position = uPMatrix * uMVMatrix * vec4(aVertexPosition, 1.0);
                
                vec3 transformedNormal = uNMatrix * aVertexNormal;
                float directionalLightWeighting = max(dot(transformedNormal, uLightingDirection), 0.0);
                vLightWeighting = uAmbientColor + uDirectionalColor * directionalLightWeighting; 

                vColor = aVertexColor;
            }
        </script>

<script id="axes-shader-fs" type="x-shader/x-fragment">
            precision mediump float;
			varying vec4 vColor;
			
			void main(void)
			{
				gl_FragColor = vColor;
			}
        </script>

<script id="axes-shader-vs" type="x-shader/x-vertex">
            attribute vec3 aVertexPosition;
			attribute vec4 aVertexColor;
			uniform mat4 uMVMatrix;
			uniform mat4 uPMatrix;
			varying vec4 vColor;
			uniform vec3 uAxesColour;
			
			void main(void)
			{
				gl_Position = uPMatrix * uMVMatrix * vec4(aVertexPosition, 1.0);
				vColor =  vec4(uAxesColour, 1.0);
			} 
        </script>

<script id="texture-shader-fs" type="x-shader/x-fragment">
            #ifdef GL_ES
            precision highp float;
            #endif
            
            varying vec2 vTextureCoord;
            
            uniform sampler2D uSampler;
            
            void main(void)
            {
                gl_FragColor = texture2D(uSampler, vTextureCoord);
            }
        </script>

<script id="texture-shader-vs" type="x-shader/x-vertex">
            attribute vec3 aVertexPosition;
            
            attribute vec2 aTextureCoord;
            varying vec2 vTextureCoord;
            
            uniform mat4 uMVMatrix;
            uniform mat4 uPMatrix;
            
            void main(void)
            {
                gl_Position = uPMatrix * uMVMatrix * vec4(aVertexPosition, 1.0);
                vTextureCoord = aTextureCoord; 
            }
        </script>


</head>
<body style="background: #fff">

	<div>
		<div id="surfacePlotDiv"
			style="float: left; width: 950px; height: 950px;">
			<!-- SurfacePlot goes here... -->
			<div id="surfacePlot1" class="surfaceplot"
				style="background-color: rgb(255, 255, 255); position: relative; left: 0px; top: 0px; background-position: initial initial; background-repeat: initial initial;">

			</div>
		</div>
	</div>
	<div>
		<input id="allowWebGL" type="checkbox" checked=""
			onclick="toggleChart(this)"> <span style="color: #000">Use webGL</span>
			<p>Hold down shift for zoom, and ctrl for move</p>
	</div>

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

				for(var i=0;i<100;i++){
					points[i] = new point(Math.floor((Math.random()*10)+1),Math.floor((Math.random()*10)+1),Math.floor((Math.random()*10)+1));
				}
				
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
			
		</script>
</body>
</html>
