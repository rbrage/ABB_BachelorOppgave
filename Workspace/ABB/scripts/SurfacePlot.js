
/*
 * SurfacePlot.js
 *
 *
 * Written by Greg Ross
 *
 * Copyright 2012 ngmoco, LLC.  Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.  You may obtain a copy of
 * the License at http://www.apache.org/licenses/LICENSE-2.0.  Unless required by applicable
 * law or agreed to in writing, software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and limitations under the
 * License.
 *
 */
/*
 * This is the main class and entry point of the tool
 * and represents the Google viz API.
 * ***************************************************
 */
SurfacePlot = function(container){
	this.containerElement = container;

	this.redraw = function(){
		this.surfacePlot.init();
		this.surfacePlot.redraw();
	}
};



SurfacePlot.prototype.draw = function(data, options, basicPlotOptions, glOptions, points){
	var xPos = options.xPos;
	var yPos = options.yPos;
	var w = options.width;
	var h = options.height;
	var colourGradient = options.colourGradient;

	var fillPolygons = basicPlotOptions.fillPolygons;
	var tooltips = basicPlotOptions.tooltips;
	var renderPoints = basicPlotOptions.renderPoints;

	var xTitle = options.xTitle;
	var yTitle = options.yTitle;
	var zTitle = options.zTitle;
	var backColour = options.backColour;
	var axisTextColour = options.axisTextColour;
	var hideFlatMinPolygons = options.hideFlatMinPolygons;
	var tooltipColour = options.tooltipColour;
	var origin = options.origin;
	var startXAngle = options.startXAngle;
	var startZAngle = options.startZAngle;
	var zAxisTextPosition = options.zAxisTextPosition;



	if (this.surfacePlot == undefined) 
		this.surfacePlot = new JSSurfacePlot(xPos, yPos, w, h, colourGradient, this.containerElement, fillPolygons, tooltips, xTitle, yTitle, zTitle, renderPoints, backColour, axisTextColour, hideFlatMinPolygons, tooltipColour, origin, startXAngle, startZAngle, zAxisTextPosition, glOptions, data, points);

	this.surfacePlot.redraw();
};

SurfacePlot.prototype.getChart = function(){
	return this.surfacePlot;
}

SurfacePlot.prototype.cleanUp = function(){
	if (this.surfacePlot == null) 
		return;

	this.surfacePlot.cleanUp();
	this.surfacePlot = null;
}

/*
 * This class does most of the work.
 * *********************************
 */
JSSurfacePlot = function(x, y, width, height, colourGradient, targetElement, fillRegions, tooltips, xTitle, yTitle, zTitle, renderPoints, backColour, axisTextColour, hideFlatMinPolygons, tooltipColour, origin, startXAngle, startZAngle, zAxisTextPosition, glOptions, data, points){
	this.xTitle = xTitle;
	this.yTitle = yTitle;
	this.zTitle = zTitle;
	this.backColour = backColour;
	this.axisTextColour = axisTextColour;
	this.glOptions = glOptions;
	var targetDiv;
	var id;
	var canvas;
	var canvasContext = null;
	this.context2D = null;
	var scale = JSSurfacePlot.DEFAULT_SCALE;
	var currentXAngle = JSSurfacePlot.DEFAULT_X_ANGLE;
	var currentZAngle = JSSurfacePlot.DEFAULT_Z_ANGLE;
	var zTextPosition = 1;



	if (startXAngle != null && startXAngle != void 0) 
		currentXAngle = startXAngle;

	if (startZAngle != null && startZAngle != void 0) 
		currentZAngle = startZAngle;

	if (zAxisTextPosition != null && zAxisTextPosition != void 0) 
		zTextPosition = zAxisTextPosition;

	this.points = points;
	this.data = data;
	var data3ds = null;
	var dataframes = null;
	var displayValues = null;
	this.numXPoints = 0;
	this.numYPoints = 0;
	var transformation;
	var cameraPosition;
	var colourGradient;

	var mouseDown1 = false;
	var mouseDown3 = false;
	var scalefactor = 100;
	var mousePosX = null;
	var mousePosY = null;
	var lastMousePos = new Point(0, 0);
	var mouseButton1Up = null;
	var mouseButton3Up = null;
	var mouseButton1Down = new Point(0, 0);
	var mouseButton3Down = new Point(0, 0);
	var closestPointToMouse = null;
	var xAxisHeader = "";
	var yAxisHeader = "";
	var zAxisHeader = "";
	var xAxisTitleLabel = new Tooltip(true);
	var yAxisTitleLabel = new Tooltip(true);
	var zAxisTitleLabel = new Tooltip(true);
	var tTip = new Tooltip(false, tooltipColour);

	this.glSurface = null;
	this.glAxes = null;
	this.useWebGL = false;
	this.gl = null;
	this.shaderProgram = null;
	this.shaderTextureProgram = null;
	this.mvMatrix = mat4.create();
	this.mvMatrixStack = [];
	this.pMatrix = mat4.create();
	var mouseDown = false;
	var mouseWheel = false;
	var lastMouseX = null;
	var lastMouseY = null;
	var rotationMatrix = mat4.create();
	var canvas_support_checked = false;
	var canvas_supported = true;

	function getInternetExplorerVersion() // Returns the version of Internet Explorer or a -1
	// (indicating the use of another browser).
	{
		var rv = -1; // Return value assumes failure.
		if (navigator.appName == 'Microsoft Internet Explorer') {
			var ua = navigator.userAgent;
			var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
			if (re.exec(ua) != null) 
				rv = parseFloat(RegExp.$1);
		}
		return rv;
	}

	function supports_canvas(){
		if (canvas_support_checked) 
			return canvas_supported;

		canvas_support_checked = true;
		canvas_supported = !!document.createElement('canvas').getContext;
		return canvas_supported;
	}

	this.init = function(){
		if (id) 
			targetElement.removeChild(targetDiv);

		id = this.allocateId();
		mat4.identity(rotationMatrix);
		mat4.rotate(rotationMatrix, degToRad(-70), [1, 0, 0]);
		mat4.rotate(rotationMatrix, degToRad(-42), [0, 0, 1]);
		transformation = new Th3dtran();

		this.createTargetDiv();

		if (!targetDiv) 
			return;

		this.dataToRender = this.data.formattedValues;
		this.frames = this.data.frames;
		this.determineMinMaxZValues();
		this.createCanvas();

		if (!this.useWebGL) {
			var maxAxisValue = this.nice_num(this.maxZValue);
			this.scaleAndNormalise(this.maxZValue / maxAxisValue);
		}
		else {
			if (this.glOptions.autoCalcZScale) 
				this.calculateZScale();

			this.glOptions.xTicksNum = this.glOptions.xLabels.length - 1;
			this.glOptions.yTicksNum = this.glOptions.yLabels.length - 1;
			this.glOptions.zTicksNum = this.glOptions.zLabels.length - 1;
		}
	};

	this.determineMinMaxZValues = function(){
		this.numXPoints = this.data.nRows;
		this.numYPoints = this.data.nCols;
		this.minZValue = Number.MAX_VALUE;
		this.maxZValue = Number.MIN_VALUE;

		if (this.frames) {
			this.numFrames = this.dataToRender.length;
			for (var k = 0; k < this.numFrames; k++) {
				for (var i = 0; i < this.numXPoints; i++) {
					for (var j = 0; j < this.numYPoints; j++) {
						var value = this.dataToRender[k][i][j];

						if (value < this.minZValue) 
							this.minZValue = value;

						if (value > this.maxZValue) 
							this.maxZValue = value;
					}
				}
			}
		}
		else {
			for (var i = 0; i < this.numXPoints; i++) {
				for (var j = 0; j < this.numYPoints; j++) {
					var value = this.dataToRender[i][j];

					if (value < this.minZValue) 
						this.minZValue = value;

					if (value > this.maxZValue) 
						this.maxZValue = value;
				}
			}
		}
	}

	this.cleanUp = function(){
		this.gl = null;

		canvas.onmousewheel = null;
		canvas.onmousedown = null;
		document.onmouseup = null;
		document.onmousemove = null;

		this.numXPoints = 0;
		this.numYPoints = 0;
		canvas = null;
		canvasContext = null;
		this.data = null;
		this.colourGradientObject = null;
		this.glSurface = null;
		this.glAxes = null;
		this.shaderProgram = null;
		this.shaderTextureProgram = null;
		this.shaderAxesProgram = null;
		this.mvMatrix = null;
		this.mvMatrixStack = null;
		this.pMatrix = null;
	}

	function hideTooltip(){
		tTip.hide();
	}

	function displayTooltip(e){
		var position = new Point(e.x, e.y);
		tTip.show(tooltips[closestPointToMouse], 200);
	}

	this.render = function(){

		if (this.useWebGL) // Render shiny WebGL surface plot.
		{
			var r = hexToR(this.backColour) / 255;
			var g = hexToG(this.backColour) / 255;
			var b = hexToB(this.backColour) / 255;

			this.initWorldObjects(data3ds, points);
			this.gl.clearColor(r, g, b, 0); // Set the background colour.
			this.gl.enable(this.gl.DEPTH_TEST);
			this.tick();

			return;
		}
		else // Render not quite so shiny non-GL surface-plot.
		{
			canvasContext.clearRect(0, 0, canvas.width, canvas.height);
			canvasContext.fillStyle = this.backColour;
			canvasContext.fillRect(0, 0, canvas.width, canvas.height);

			var canvasWidth = width;
			var canvasHeight = height;

			var minMargin = 20;
			var drawingDim = canvasWidth - minMargin * 2;
			var marginX = minMargin;
			var marginY = minMargin;

			transformation.init();
			transformation.rotate(currentXAngle, 0.0, currentZAngle);
			transformation.scale(scale);

			if (origin != null && origin != void 0) 
				transformation.translate(origin.x, origin.y, 0.0);
			else 
				transformation.translate(drawingDim / 2.0 + marginX, drawingDim / 2.0 + marginY, 0.0);

			cameraPosition = new Point3D(drawingDim / 2.0 + marginX, drawingDim / 2.0 + marginY, -1000.0);

			var axes = this.createAxes();
			var polygons = this.createPolygons(data3ds, points);

			for (i = 0; i < axes.length; i++) 
				polygons[polygons.length] = axes[i];

			// Sort the polygons so that the closest ones are rendered last
			// and therefore are not occluded by those behind them.
			// This is really Painter's algorithm.
			polygons.sort(PolygonComaparator);

			canvasContext.lineWidth = 1;
			canvasContext.strokeStyle = '#888';
			canvasContext.lineJoin = "round";

			for (i = 0; i < polygons.length; i++) {
				var polygon = polygons[i];

				if (polygon.isAnAxis()) {
					var p1 = polygon.getPoint(0);
					var p2 = polygon.getPoint(1);

					canvasContext.beginPath();
					canvasContext.moveTo(p1.ax, p1.ay);
					canvasContext.lineTo(p2.ax, p2.ay);
					canvasContext.stroke();
				}
				else {
					var p1 = polygon.getPoint(0);

					var colourValue = (p1.lz * 1.0) / 4.0;

					var rgbColour = this.colourGradientObject.getColour(colourValue);
					var colr = "rgb(" + rgbColour.red + "," + rgbColour.green + "," + rgbColour.blue + ")";
					canvasContext.fillStyle = colr;

					canvasContext.beginPath();
					canvasContext.arc(p1.ax,p1.ay,1,0,2*Math.PI);
					canvasContext.fill();

				}

			}


			if (supports_canvas()) 
				this.renderAxisText(axes);
		}
	};

	this.renderAxisText = function(axes){
		var xLabelPoint = new Point3D(25, 0, 0.0);
		var yLabelPoint = new Point3D(0, 25, 0.0);
		var zLabelPoint = new Point3D(0, 0, 25);

		var transformedxLabelPoint = transformation.ChangeObjectPoint(xLabelPoint);
		var transformedyLabelPoint = transformation.ChangeObjectPoint(yLabelPoint);
		var transformedzLabelPoint = transformation.ChangeObjectPoint(zLabelPoint);

		var xAxis = axes[0];
		var yAxis = axes[1];
		var zAxis = axes[2];

		canvasContext.fillStyle = this.axisTextColour;

		if (xAxis.distanceFromCamera > yAxis.distanceFromCamera) {
			var xAxisLabelPosX = transformedxLabelPoint.ax;
			var xAxisLabelPosY = transformedxLabelPoint.ay;
			canvasContext.fillText(xTitle, xAxisLabelPosX, xAxisLabelPosY);
		}

		if (xAxis.distanceFromCamera < yAxis.distanceFromCamera) {
			var yAxisLabelPosX = transformedyLabelPoint.ax;
			var yAxisLabelPosY = transformedyLabelPoint.ay;
			canvasContext.fillText(yTitle, yAxisLabelPosX, yAxisLabelPosY);
		}

		if (xAxis.distanceFromCamera < zAxis.distanceFromCamera) {
			var zAxisLabelPosX = transformedzLabelPoint.ax;
			var zAxisLabelPosY = transformedzLabelPoint.ay;
			canvasContext.fillText(zTitle, zAxisLabelPosX, zAxisLabelPosY);
		}
	};

	var sort = function(array){
		var len = array.length;

		if (len < 2) {
			return array;
		}

		var pivot = Math.ceil(len / 2);
		return merge(sort(array.slice(0, pivot)), sort(array.slice(pivot)));
	};

	var merge = function(left, right){
		var result = [];
		while ((left.length > 0) && (right.length > 0)) {
			if (left[0].distanceFromCamera < right[0].distanceFromCamera) {
				result.push(left.shift());
			}
			else {
				result.push(right.shift());
			}
		}

		result = result.concat(left, right);
		return result;
	};


	this.createAxes = function(){
		var axisOrigin = new Point3D(0, 0, 0);
		var xAxisEndPoint = new Point3D(50, 0, 0);
		var yAxisEndPoint = new Point3D(0, 50, 0);
		var zAxisEndPoint = new Point3D(0, 0, 50);

		var transformedAxisOrigin = transformation.ChangeObjectPoint(axisOrigin);
		var transformedXAxisEndPoint = transformation.ChangeObjectPoint(xAxisEndPoint);
		var transformedYAxisEndPoint = transformation.ChangeObjectPoint(yAxisEndPoint);
		var transformedZAxisEndPoint = transformation.ChangeObjectPoint(zAxisEndPoint);

		var axes = new Array();

		var xAxis = new Polygon(cameraPosition, true);
		xAxis.addPoint(transformedAxisOrigin);
		xAxis.addPoint(transformedXAxisEndPoint);
		xAxis.calculateCentroid();
		xAxis.calculateDistance();
		axes[axes.length] = xAxis;

		var yAxis = new Polygon(cameraPosition, true);
		yAxis.addPoint(transformedAxisOrigin);
		yAxis.addPoint(transformedYAxisEndPoint);
		yAxis.calculateCentroid();
		yAxis.calculateDistance();
		axes[axes.length] = yAxis;

		var zAxis = new Polygon(cameraPosition, true);
		zAxis.addPoint(transformedAxisOrigin);
		zAxis.addPoint(transformedZAxisEndPoint);
		zAxis.calculateCentroid();
		zAxis.calculateDistance();
		axes[axes.length] = zAxis;

		return axes;
	};

	this.createPolygons = function(data3D, pointsn){
		var i;
		var j;
		var points = new Array();
		var index = 0;


		for(i=0; i<pointsn.length;i++){
			var pointnew = new Point3D((pointsn[i][0]), (pointsn[i][1]), (pointsn[i][2]));
			var p1 = transformation.ChangeObjectPoint(pointnew);
			var point = new Polygon(cameraPosition, false);
			point.addPoint(p1);
			point.calculateCentroid();
			point.calculateDistance();
			points[index] = point;
			index++;
		}

		return points;
	};

	this.getDefaultColourRamp = function(){
		var colour1 = {
				red: 0,
				green: 0,
				blue: 255
		};
		var colour2 = {
				red: 0,
				green: 255,
				blue: 255
		};
		var colour3 = {
				red: 0,
				green: 255,
				blue: 0
		};
		var colour4 = {
				red: 255,
				green: 255,
				blue: 0
		};
		var colour5 = {
				red: 255,
				green: 0,
				blue: 0
		};
		return [colour1, colour2, colour3, colour4, colour5];
	};

	this.redraw = function(){
		var cGradient;

		if (colourGradient) 
			cGradient = colourGradient;
		else 
			cGradient = getDefaultColourRamp();

		this.colourGradientObject = new ColourGradient(this.minZValue, this.maxZValue, cGradient);

		var canvasWidth = width;
		var canvasHeight = height;

		var minMargin = 20;
		var drawingDim = canvasWidth - minMargin * 2;
		var marginX = minMargin;
		var marginY = minMargin;

		if (canvasWidth > canvasHeight) {
			drawingDim = canvasHeight - minMargin * 2;
			marginX = (canvasWidth - drawingDim) / 2;
		}
		else 
			if (canvasWidth < canvasHeight) {
				drawingDim = canvasWidth - minMargin * 2;
				marginY = (canvasHeight - drawingDim) / 2;
			}

		var xDivision = 1 / (this.numXPoints - 1);
		var yDivision = 1 / (this.numYPoints - 1);
		var xPos, yPos;
		var i, j;
		var numPoints = this.numXPoints * this.numYPoints;
		data3ds = new Array();
		dataframe = new Array();
		var index = 0;
		var colIndex;

		if (this.frames) {
			for (var k = 0; k < this.numFrames; k++) {
				index = 0;
				dataframe = new Array();
				for (i = 0, xPos = -1; i < this.numXPoints; i++, xPos += xDivision) {
					for (j = 0, yPos = 1; j < this.numYPoints; j++, yPos -= yDivision) {
						var x = xPos;
						var y = yPos;

						if (this.useWebGL) 
							colIndex = this.numYPoints - 1 - j;
						else 
							colIndex = j;

						dataframe[index] = new Point3D(x, y, this.dataToRender[k][i][colIndex]); // Reverse the y-axis to match the non-webGL surface.
						index++;
					}
				}
				data3ds.push(dataframe);
			}
		}
		else {
			for (i = 0, xPos = -1; i < this.numXPoints; i++, xPos += xDivision) {
				for (j = 0, yPos = 1; j < this.numYPoints; j++, yPos -= yDivision) {
					var x = xPos;
					var y = yPos;

					if (this.useWebGL) 
						colIndex = this.numYPoints - 1 - j;
					else 
						colIndex = j;

					data3ds[index] = new Point3D(x, y, this.dataToRender[i][colIndex]); // Reverse the y-axis to match the non-webGL surface.
					index++;
				}
			}
		}

		this.render();
	};

	this.allocateId = function(){
		var count = 0;
		var name = "surfacePlot";

		do {
			count++;
		}
		while (document.getElementById(name + count))
			return name + count;
	};

	this.createTargetDiv = function(){
		targetDiv = document.createElement("div");
		targetDiv.id = id;
		targetDiv.className = "surfaceplot";
		targetDiv.style.background = '#ffffff';
		targetDiv.style.position = 'absolute';
		targetDiv.style.border = '1px solid';
		

		if (!targetElement) 
			return;//document.body.appendChild(this.targetDiv);
		else {
			targetDiv.style.position = 'relative';
			targetElement.appendChild(targetDiv);
		}

		targetDiv.style.left = x + "px";
		targetDiv.style.top = y + "px";
	};

	this.getShader = function(id){
		var shaderScript = document.getElementById(id);

		if (!shaderScript) {
			return null;
		}

		var str = "";
		var k = shaderScript.firstChild;

		while (k) {
			if (k.nodeType == 3) {
				str += k.textContent;
			}

			k = k.nextSibling;
		}

		var shader;

		if (shaderScript.type == "x-shader/x-fragment") {
			shader = this.gl.createShader(this.gl.FRAGMENT_SHADER);
		}
		else 
			if (shaderScript.type == "x-shader/x-vertex") {
				shader = this.gl.createShader(this.gl.VERTEX_SHADER);
			}
			else {
				return null;
			}

		this.gl.shaderSource(shader, str);
		this.gl.compileShader(shader);

		if (!this.gl.getShaderParameter(shader, this.gl.COMPILE_STATUS)) {
			alert(this.gl.getShaderInfoLog(shader));
			return null;
		}

		return shader;
	};

	this.createProgram = function(fragmentShaderID, vertexShaderID){
		if (this.gl == null) 
			return null;

		var fragmentShader = this.getShader(fragmentShaderID);
		var vertexShader = this.getShader(vertexShaderID);

		if (fragmentShader == null || vertexShader == null) 
			return null;

		var program = this.gl.createProgram();
		this.gl.attachShader(program, vertexShader);
		this.gl.attachShader(program, fragmentShader);
		this.gl.linkProgram(program);

		program.pMatrixUniform = this.gl.getUniformLocation(program, "uPMatrix");
		program.mvMatrixUniform = this.gl.getUniformLocation(program, "uMVMatrix");

		program.nMatrixUniform = this.gl.getUniformLocation(program, "uNMatrix");
		program.axesColour = this.gl.getUniformLocation(program, "uAxesColour");
		program.ambientColorUniform = this.gl.getUniformLocation(program, "uAmbientColor");
		program.lightingDirectionUniform = this.gl.getUniformLocation(program, "uLightingDirection");
		program.directionalColorUniform = this.gl.getUniformLocation(program, "uDirectionalColor");

		return program;
	};


	this.initShaders = function(){
		if (this.gl == null) 
			return false;

		// Non-texture shaders
		this.shaderProgram = this.createProgram("shader-fs", "shader-vs");

		// Texture shaders
		this.shaderTextureProgram = this.createProgram("texture-shader-fs", "texture-shader-vs");

		// Axes shaders
		this.shaderAxesProgram = this.createProgram("axes-shader-fs", "axes-shader-vs");

		if (!this.gl.getProgramParameter(this.shaderProgram, this.gl.LINK_STATUS)) {
			return false;
		}

		return true;
	};

	this.mvPushMatrix = function(surfacePlot){
		var copy = mat4.create();
		mat4.set(surfacePlot.mvMatrix, copy);
		surfacePlot.mvMatrixStack.push(copy);
	};

	this.mvPopMatrix = function(surfacePlot){
		if (surfacePlot.mvMatrixStack.length == 0) {
			throw "Invalid popMatrix!";
		}

		surfacePlot.mvMatrix = surfacePlot.mvMatrixStack.pop();
	};

	this.setMatrixUniforms = function(program, pMatrix, mvMatrix){
		this.gl.uniformMatrix4fv(program.pMatrixUniform, false, pMatrix);
		this.gl.uniformMatrix4fv(program.mvMatrixUniform, false, mvMatrix);

		var normalMatrix = mat3.create();
		mat4.toInverseMat3(mvMatrix, normalMatrix);
		mat3.transpose(normalMatrix);
		this.gl.uniformMatrix3fv(program.nMatrixUniform, false, normalMatrix);
	};

	this.initWorldObjects = function(data3D, points){
		if (this.frames) {
			this.glSurface = new GLSurface(this, points);
			this.glAxes = new GLAxes( this, points);
		}
		else {
			this.glSurface = new GLSurface(this, points);
			this.glAxes = new GLAxes(this, points);
		}
	};

	// WebGL mouse handlers:
	var shiftPressed = false;
	var ctrlPressed = false;
	var mouseWheel = false;
	var leftmouseDown = false;
	var rigthmouseDown = false;
	

	this.handleMouseUp = function(event){
		mouseDown = false;
	};

	this.drawScene = function(){
		this.mvPushMatrix(this);

		this.gl.useProgram(this.shaderProgram);

		// Enable the vertex arrays for the current shader.
		this.shaderProgram.vertexPositionAttribute = this.gl.getAttribLocation(this.shaderProgram, "aVertexPosition");
		this.gl.enableVertexAttribArray(this.shaderProgram.vertexPositionAttribute);

		this.gl.viewport(0, 0, this.gl.viewportWidth, this.gl.viewportHeight);
		this.gl.clear(this.gl.COLOR_BUFFER_BIT | this.gl.DEPTH_BUFFER_BIT);
		mat4.perspective(5, this.gl.viewportWidth / this.gl.viewportHeight, 0.1, 100.0, this.pMatrix);
		mat4.identity(this.mvMatrix);

		mat4.translate(this.mvMatrix, [0.0, -0.3, -19.0]);

		mat4.multiply(this.mvMatrix, rotationMatrix);

		// Disable the vertex arrays for the current shader.
		this.gl.disableVertexAttribArray(this.shaderProgram.vertexPositionAttribute);

		this.glAxes.draw();

		this.glSurface.draw();

		this.mvPopMatrix(this);
	};


	this.tick = function(){
		var self = this;

		if (this.gl == null) 
			return;

		var animator = function(){
			if (self.gl == null) 
				return;

			self.drawScene();
			requestAnimFrame(animator);
		};

		requestAnimFrame(animator);
	};

	this.isWebGlEnabled = function(){
		var enabled = true;

		if (this.glOptions.chkControlId && document.getElementById(this.glOptions.chkControlId)) 
			enabled = document.getElementById(this.glOptions.chkControlId).checked;

		return enabled && this.initShaders();
	};

	this.rotate = function(deltaX, deltaY){
		var newRotationMatrix = mat4.create();
		mat4.identity(newRotationMatrix);

		mat4.rotate(newRotationMatrix, degToRad(deltaX / 2), [0, 1, 0]);
		mat4.rotate(newRotationMatrix, degToRad(deltaY / 2), [1, 0, 0]);
		mat4.multiply(newRotationMatrix, rotationMatrix, rotationMatrix);
	};

	this.handleMouseWheelMove = function(event){
		if (mouseDown) {
			return;
		}
		if(mouseWheel){ 
			var delta = 0;
			if (!event) 
				event = window.event;
			if (event.wheelDelta) {
				delta = event.wheelDelta/120;
			} else if (event.detail) { 
				delta = -event.detail/3;
			}
			var newRotationMatrix = mat4.create();
			mat4.identity(newRotationMatrix);
			var s = delta <  0 ? 0.95 : 1.05;
			scalefactor = scalefactor*s;
			mat4.scale(newRotationMatrix, [s, s, s]);
			mat4.multiply(newRotationMatrix, rotationMatrix, rotationMatrix);
			mouseWheel = false;
		}

	};

	this.handleMouseMove = function(event, context){

		if (!mouseDown) {
			return;
		}

		var newX = event.clientX;
		var newY = event.clientY;

		var deltaX = newX - lastMouseX;
		var deltaY = newY - lastMouseY;
		var newRotationMatrix = mat4.create();
		mat4.identity(newRotationMatrix);

		if (shiftPressed) // scale
		{
			
			var s = deltaY < 0 ? 1.05 : 0.95;
			
			mat4.scale(newRotationMatrix, [s, s, s]);
			mat4.multiply(newRotationMatrix, rotationMatrix, rotationMatrix);
		} 
		else if(ctrlPressed || rigthmouseDown){

			mat4.translate(newRotationMatrix, [deltaX/1000,-deltaY/1000,0]);
			mat4.multiply(newRotationMatrix, rotationMatrix, rotationMatrix);
		}


		else if(leftmouseDown)// rotate
		{
			mat4.rotate(newRotationMatrix, degToRad(deltaX / 2), [0, 1, 0]);
			mat4.rotate(newRotationMatrix, degToRad(deltaY / 2), [1, 0, 0]);
			mat4.multiply(newRotationMatrix, rotationMatrix, rotationMatrix);

			if (this.otherPlots) {
				var numPlots = this.otherPlots.length;
				for (var i = 0; i < numPlots; i++) {
					this.otherPlots[i].rotate(deltaX, deltaY);
				}
			}
		}

		lastMouseX = newX;
		lastMouseY = newY;
	};

	this.initGL = function(canvas){
		var canUseWebGL = false;

		try {
			this.gl = canvas.getContext("experimental-webgl", {
				alpha: false
			});
			this.gl.viewportWidth = canvas.width;
			this.gl.viewportHeight = canvas.height;
		} 
		catch (e) {
		}

		if (this.gl) {
			canUseWebGL = this.isWebGlEnabled();
			var self = this;

			var handleMouseDown = function(event){
				shiftPressed = isShiftPressed(event);
				ctrlPressed = isCtrlPressed(event);
				leftmouseDown = isleftmouseDown(event);
				rigthmouseDown = isrigthtmouseDown(event);

				mouseDown = true;
				mouseWheel = false;
				lastMouseX = event.clientX;
				lastMouseY = event.clientY;

				document.onmouseup = self.handleMouseUp;
				document.onmousemove = function(event){
					self.handleMouseMove(event, self)
				};//self.handleMouseMove;
			};

			var handleMouseWheel = function(event){
				mouseWheel = true;
				mouseDown = false;
				document.onmousemove = function(event){
					self.handleMouseWheelMove(event, self)
				};//self.handleMouseMove;

			};

			canvas.onmousewheel = handleMouseWheel;
			document.onmousewheel = this.handleMouseWheel;
			document.onmousewheel = function(event){
				self.handleMouseWheelMove(event, self);
			};
			canvas.onmousedown = handleMouseDown;
			document.onmouseup = this.handleMouseUp;
			document.onmousemove = function(event){
				self.handleMouseMove(event, self);
			};//this.handleMouseMove;
		}

		return canUseWebGL;
	};

	this.initCanvas = function(){
		canvas.className = "surfacePlotCanvas";
		canvas.setAttribute("width", width);
		canvas.setAttribute("height", height);
		canvas.style.left = '0px';
		canvas.style.top = '0px';

		targetDiv.appendChild(canvas);
	};

	this.scaleAndNormalise = function(scaleFactor){
		if (this.frames) {
			// Need to clone the data.
			var values = this.data.formattedValues.slice(0);
			var numRows = this.data.nRows;

			for (var i = 0; i < this.numFrames; i++) {
				values[i] = this.data.formattedValues[i].slice(0);

				for (var j = 0; j < numRows; j++) 
					values[i][j] = this.data.formattedValues[i][j].slice(0);
			}

			// Now, do the scaling.
			var numRows = values[0].length;
			var numCols = values[0][0].length;

			for (var k = 0; k < this.numFrames; k++) 
				for (var i = 0; i < numRows; i++) 
					for (var j = 0; j < numCols; j++) 
						values[k][i][j] = (values[k][i][j] / this.maxZValue) * scaleFactor;

			this.dataToRender = values;
		}
		else {
			// Need to clone the data.
			var values = this.data.formattedValues.slice(0);
			var numRows = this.data.nRows;

			for (var i = 0; i < numRows; i++) 
				values[i] = this.data.formattedValues[i].slice(0);

			// Now, do the scaling.
			var numRows = values.length;
			var numCols = values[0].length;

			for (var i = 0; i < numRows; i++) 
				for (var j = 0; j < numCols; j++) 
					values[i][j] = (values[i][j] / this.maxZValue) * scaleFactor;

			this.dataToRender = values;
		}

		// Recalculate the new min and max values.
		this.determineMinMaxZValues();
	}

	this.log = function(base, value){
		return Math.log(value) / Math.log(base);
	}

	this.nice_num = function(x, round){
		var exp = Math.floor(log(10, x));
		var f = x / Math.pow(10, exp);
		var nf;

		if (round) {
			if (f < 1.5) 
				nf = 1;
			else 
				if (f < 3) 
					nf = 2;
				else 
					if (f < 7) 
						nf = 5;
					else 
						nf = 10;
		}
		else {
			if (f <= 1) 
				nf = 1;
			else 
				if (f <= 2) 
					nf = 2;
				else 
					if (f <= 5) 
						nf = 5;
					else 
						nf = 10;
		}

		return nf * Math.pow(10, exp);
	}

	this.calculateZScale = function(){
		// Calculate the z-axis labels.
		var maxAxisValue = this.nice_num(this.maxZValue);
		var labels = [];
		var ticks = 10;
		var interval = maxAxisValue / ticks;
		var rounded2dp;

		for (var i = 0; i <= ticks; i++) {
			rounded2dp = Math.round(i * interval * 100) / 100;
			labels.push(rounded2dp);
		}

		this.glOptions.zLabels = labels;

		// Scale the values accordingly.
		var scaleFactor = this.maxZValue / maxAxisValue;
		this.scaleAndNormalise(scaleFactor);
	}

	this.createCanvas = function(){
		canvas = document.createElement("canvas");

		if (!supports_canvas()) {
			G_vmlCanvasManager.initElement(canvas);
			canvas.style.width = width;
			canvas.style.height = height;
		}
		else {
			this.initCanvas();
			this.useWebGL = this.initGL(canvas);
		}


		if (!this.useWebGL) {
			targetDiv.removeChild(canvas);
			canvas = document.createElement("canvas");

			this.initCanvas();

			canvasContext = canvas.getContext("2d");
			canvasContext.font = "bold 18px sans-serif";
			canvasContext.clearRect(0, 0, canvas.width, canvas.height);

			canvasContext.fillStyle = '#000';

			canvasContext.fillRect(0, 0, canvas.width, canvas.height);

			canvasContext.beginPath();
			canvasContext.rect(0, 0, canvas.width, canvas.height);
			canvasContext.strokeStyle = '#888';
			canvasContext.stroke();

			canvas.owner = this;
			canvas.onmousemove = this.mouseIsMoving;
			canvas.onmouseout = hideTooltip;
			canvas.onmousedown = this.mouseDownd;
			canvas.onmouseup = this.mouseUpd;
			


			canvas.addEventListener("touchstart", this.mouseDownd, false);
			canvas.addEventListener("touchmove", this.mouseIsMoving, false);
			canvas.addEventListener("touchend", this.mouseUpd, false);
			canvas.addEventListener("touchcancel", hideTooltip, false);
			
		}
		else 
			this.createHiddenCanvasForGLText();
	};

	this.createHiddenCanvasForGLText = function(){
		var hiddenCanvas = document.createElement("canvas");
		hiddenCanvas.setAttribute("width", 512);
		hiddenCanvas.setAttribute("height", 512);
		this.context2D = hiddenCanvas.getContext('2d');
		hiddenCanvas.style.display = 'none';
		targetDiv.appendChild(hiddenCanvas);
	};

	// Mouse events for the non-webGL version of the surface plot.
	this.mouseDownd = function(e){
		if (isShiftPressed(e)) {
			mouseDown3 = true;
			mouseButton3Down = getMousePositionFromEvent(e);
		}
		else {
			mouseDown1 = true;
			mouseButton1Down = getMousePositionFromEvent(e);
		}
	};

	this.mouseUpd = function(e){
		if (mouseDown1) {
			mouseButton1Up = lastMousePos;
		}
		else 
			if (mouseDown3) {
				mouseButton3Up = lastMousePos;
			}

		mouseDown1 = false;
		mouseDown3 = false;
	};

	
	this.mouseIsMoving = function(e){
		var self = e.target.owner;
		var currentPos = getMousePositionFromEvent(e);

		if (mouseDown1) {
			hideTooltip();
			self.calculateRotation(currentPos);
			
		}
		else 
			if (mouseDown3) {
				hideTooltip();
				self.calculateScale(currentPos);
			}
			else {
				closestPointToMouse = null;
				var closestDist = Number.MAX_VALUE;

				for (var i = 0; i < data3ds.length; i++) {
					var point = data3ds[i];
					var dist = distance({
						x: point.ax,
						y: point.ay
					}, currentPos);

					if (dist < closestDist) {
						closestDist = dist;
						closestPointToMouse = i;
					}
				}

				if (closestDist > 32) {
					hideTooltip();
					return;
				}

				displayTooltip(currentPos);
			}

		return false;
	};

	function isleftmouseDown(event){
		if (event.button==0){ 
			return true;
		}
	};
	function isrigthtmouseDown(event){
		if (event.button==2){ 
			return true;
		}
	};
	function isShiftPressed(e){
		var shiftPressed = 0;

		if (parseInt(navigator.appVersion) > 3) {
			var evt = navigator.appName == "Netscape" ? e : event;

			if (navigator.appName == "Netscape" && parseInt(navigator.appVersion) == 4) {
				// NETSCAPE 4 CODE
				var mString = (e.modifiers + 32).toString(2).substring(3, 6);
				shiftPressed = (mString.charAt(0) == "1");
			}
			else {
				// NEWER BROWSERS [CROSS-PLATFORM]
				shiftPressed = evt.shiftKey;
			}

			if (shiftPressed) 
				return true;
		}

		return false;
	};
	function isCtrlPressed(e){
		var ctrlPressed = 0;

		if (parseInt(navigator.appVersion) > 3) {
			var evt = navigator.appName == "Netscape" ? e : event;

			if (navigator.appName == "Netscape" && parseInt(navigator.appVersion) == 4) {
				// NETSCAPE 4 CODE
				var mString = (e.modifiers + 32).toString(2).substring(3, 6);
				ctrlPressed = (mString.charAt(0) == "1");
			}
			else {
				// NEWER BROWSERS [CROSS-PLATFORM]
				ctrlPressed = evt.ctrlKey;
			}

			if (ctrlPressed) 
				return true;
		}

		return false;
	};

	function getMousePositionFromEvent(e){
		if (getInternetExplorerVersion() > -1) {
			var e = window.event;

			if (e.srcElement.getAttribute('Stroked')) {
				if (mousePosX == null || mousePosY == null) 
					return;
			}
			else {
				mousePosX = e.offsetX;
				mousePosY = e.offsetY;
			}
		}
		else 
			if (e.layerX || e.layerX == 0) // Firefox
			{
				mousePosX = e.layerX;
				mousePosY = e.layerY;
			}
			else if (e.offsetX || e.offsetX == 0) // Opera
			{
				mousePosX = e.offsetX;
				mousePosY = e.offsetY;
			}
			else if (e.touches[0].pageX || e.touches[0].pageX == 0) //touch events
			{
				mousePosX = e.touches[0].pageX;
				mousePosY = e.touches[0].pageY;
			}

		var currentPos = new Point(mousePosX, mousePosY);

		return currentPos;
	}

	this.calculateRotation = function(e){
		lastMousePos = new Point(JSSurfacePlot.DEFAULT_Z_ANGLE, JSSurfacePlot.DEFAULT_X_ANGLE);

		if (mouseButton1Up == null) {
			mouseButton1Up = new Point(JSSurfacePlot.DEFAULT_Z_ANGLE, JSSurfacePlot.DEFAULT_X_ANGLE);
		}

		if (mouseButton1Down != null) {
			lastMousePos = new Point(mouseButton1Up.x + (mouseButton1Down.x - e.x),//
					mouseButton1Up.y + (mouseButton1Down.y - e.y));
		}

		currentZAngle = lastMousePos.x % 360;
		currentXAngle = lastMousePos.y % 360;

		closestPointToMouse = null;
		this.render();
	};

	this.calculateScale = function(e){
		lastMousePos = new Point(0, JSSurfacePlot.DEFAULT_SCALE / JSSurfacePlot.SCALE_FACTOR);

		if (mouseButton3Up == null) {
			mouseButton3Up = new Point(0, JSSurfacePlot.DEFAULT_SCALE / JSSurfacePlot.SCALE_FACTOR);
		}

		if (mouseButton3Down != null) {
			lastMousePos = new Point(mouseButton3Up.x + (mouseButton3Down.x - e.x),//
					mouseButton3Up.y + (mouseButton3Down.y - e.y));
		}

		scale = lastMousePos.y * JSSurfacePlot.SCALE_FACTOR;

		if (scale < JSSurfacePlot.MIN_SCALE) 
			scale = JSSurfacePlot.MIN_SCALE + 1;
		else 
			if (scale > JSSurfacePlot.MAX_SCALE) 
				scale = JSSurfacePlot.MAX_SCALE - 1;

		lastMousePos.y = scale / JSSurfacePlot.SCALE_FACTOR;
		closestPointToMouse = null;
		this.render();
	};

	this.init();
};

GLText = function(text, pos, angle, surfacePlot, axis, align){
	this.shaderTextureProgram = surfacePlot.shaderTextureProgram;
	this.currenShader = null;
	this.gl = surfacePlot.gl;
	this.setMatrixUniforms = surfacePlot.setMatrixUniforms;

	this.vertexTextureCoordBuffer = null;
	this.textureVertexPositionBuffer = null;
	this.textureVertexIndexBuffer = null;
	this.context2D = surfacePlot.context2D;
	this.mvPushMatrix = surfacePlot.mvPushMatrix;
	this.mvPopMatrix = surfacePlot.mvPopMatrix;
	this.texture;
	this.text = text;
	this.angle = angle;
	this.pos = pos;
	this.surfacePlot = surfacePlot;
	this.textMetrics = null;
	this.axis = axis;
	this.align = align;

	this.setUpTextArea = function(){
		this.context2D.font = 'normal 28px Verdana';
		this.context2D.fillStyle = 'rgba(255,255,255,0)';
		this.context2D.fillRect(0, 0, 512, 512);
		this.context2D.lineWidth = 3;
		this.context2D.textAlign = 'left';
		this.context2D.textBaseline = 'top';
	};

	this.writeTextToCanvas = function(text, idx){
		this.context2D.save();
		this.context2D.clearRect(0, 0, 512, 512);
		this.context2D.fillStyle = 'rgba(255, 255, 255, 0)';
		this.context2D.fillRect(0, 0, 512, 512);

		var r = hexToR(this.surfacePlot.axisTextColour);
		var g = hexToG(this.surfacePlot.axisTextColour);
		var b = hexToB(this.surfacePlot.axisTextColour);

		this.context2D.fillStyle = 'rgba(' + r + ', ' + g + ', ' + b + ', 255)'; // Set the axis label colour.
		this.textMetrics = this.context2D.measureText(text);

		if (this.axis == "y" || this.align == "left") 
			this.context2D.fillText(text, 0, 0);
		else 
			if (!this.align) 
				this.context2D.fillText(text, 512 - this.textMetrics.width, 0);

		if (this.align == "centre") 
			this.context2D.fillText(text, 256 - this.textMetrics.width / 2, 0);
		if (this.align == "right") 
			this.context2D.fillText(text, 512 - this.textMetrics.width, 0);

		this.setTextureFromCanvas(this.context2D.canvas, this.texture, 0);

		this.context2D.restore();
	};

	this.setTextureFromCanvas = function(canvas, textTexture, idx){
		this.gl.activeTexture(this.gl.TEXTURE0 + idx);
		this.gl.bindTexture(this.gl.TEXTURE_2D, textTexture);
		this.gl.pixelStorei(this.gl.UNPACK_FLIP_Y_WEBGL, true);
		this.gl.texImage2D(this.gl.TEXTURE_2D, 0, this.gl.RGBA, this.gl.RGBA, this.gl.UNSIGNED_BYTE, canvas);

		if (isPowerOfTwo(canvas.width) && isPowerOfTwo(canvas.height)) {
			this.gl.texParameteri(this.gl.TEXTURE_2D, this.gl.TEXTURE_MAG_FILTER, this.gl.NEAREST);
			this.gl.texParameteri(this.gl.TEXTURE_2D, this.gl.TEXTURE_MIN_FILTER, this.gl.NEAREST);
		}
		else {
			this.gl.texParameteri(this.gl.TEXTURE_2D, this.gl.TEXTURE_MIN_FILTER, this.gl.LINEAR);
			this.gl.texParameteri(this.gl.TEXTURE_2D, this.gl.TEXTURE_WRAP_S, this.gl.CLAMP_TO_EDGE);
			this.gl.texParameteri(this.gl.TEXTURE_2D, this.gl.TEXTURE_WRAP_T, this.gl.CLAMP_TO_EDGE);
		}

		this.gl.bindTexture(this.gl.TEXTURE_2D, textTexture);
	};

	function isPowerOfTwo(value){
		return ((value & (value - 1)) == 0);
	}

	this.initTextBuffers = function(){

		// Text texture vertices
		this.textureVertexPositionBuffer = this.gl.createBuffer();
		this.gl.bindBuffer(this.gl.ARRAY_BUFFER, this.textureVertexPositionBuffer);
		this.textureVertexPositionBuffer.itemSize = 3;
		this.textureVertexPositionBuffer.numItems = 4;
		this.shaderTextureProgram.textureCoordAttribute = this.gl.getAttribLocation(this.shaderTextureProgram, "aTextureCoord");
		this.gl.vertexAttribPointer(this.shaderTextureProgram.textureCoordAttribute, this.textureVertexPositionBuffer.itemSize, this.gl.FLOAT, false, 0, 0);

		this.gl.bindBuffer(this.gl.ARRAY_BUFFER, this.textureVertexPositionBuffer);

		// Where we render the text.
		var texturePositionCoords = [-0.5, -0.5, 0.5, 0.5, -0.5, 0.5, 0.5, 0.5, 0.5, -0.5, 0.5, 0.5];

		this.gl.bufferData(this.gl.ARRAY_BUFFER, new Float32Array(texturePositionCoords), this.gl.STATIC_DRAW);

		// Texture index buffer.
		this.textureVertexIndexBuffer = this.gl.createBuffer();
		this.gl.bindBuffer(this.gl.ELEMENT_ARRAY_BUFFER, this.textureVertexIndexBuffer);

		var textureVertexIndices = [0, 1, 2, 0, 2, 3];

		this.gl.bufferData(this.gl.ELEMENT_ARRAY_BUFFER, new Uint16Array(textureVertexIndices), this.gl.STATIC_DRAW);
		this.textureVertexIndexBuffer.itemSize = 1;
		this.textureVertexIndexBuffer.numItems = 6;

		// Text textures
		this.vertexTextureCoordBuffer = this.gl.createBuffer();
		this.gl.bindBuffer(this.gl.ARRAY_BUFFER, this.vertexTextureCoordBuffer);
		this.vertexTextureCoordBuffer.itemSize = 2;
		this.vertexTextureCoordBuffer.numItems = 4;
		this.gl.vertexAttribPointer(this.shaderTextureProgram.textureCoordAttribute, this.vertexTextureCoordBuffer.itemSize, this.gl.FLOAT, false, 0, 0);

		this.gl.bindBuffer(this.gl.ARRAY_BUFFER, this.vertexTextureCoordBuffer);

		var textureCoords = [0.0, 0.0, 1.0, 0.0, 1.0, 1.0, 0.0, 1.0];

		this.gl.bufferData(this.gl.ARRAY_BUFFER, new Float32Array(textureCoords), this.gl.STATIC_DRAW);
	};

	this.initTextBuffers();
	this.setUpTextArea();

	this.texture = this.gl.createTexture();
	this.writeTextToCanvas(this.text, this.idx);
};

GLText.prototype.draw = function(){
	this.mvPushMatrix(this.surfacePlot);

	var rotationMatrix = mat4.create();
	mat4.identity(rotationMatrix);

	if (this.axis == "y") {
		mat4.translate(rotationMatrix, [0.0, 1, 0.0]);
		mat4.translate(rotationMatrix, [this.pos.x + 0.53, this.pos.y + 0.6, this.pos.z - 0.5]);
		mat4.rotate(rotationMatrix, degToRad(this.angle), [1, 0, 0]);
		mat4.translate(rotationMatrix, [0.0, -1, 0]);
	}
	else 
		if (this.axis == "x") {
			mat4.translate(rotationMatrix, [1, 0, 0]);
			mat4.translate(rotationMatrix, [this.pos.x - 0.5, this.pos.y + 0.47, this.pos.z - 0.5]);
			mat4.rotate(rotationMatrix, degToRad(this.angle), [0, 0, 1]);
			mat4.translate(rotationMatrix, [1, 0, 0]);
		}
		else 
			if (this.axis == "z" && this.align == "centre") // Main Z-axis label.
			{
				mat4.translate(rotationMatrix, [0.0, 0, 1]);
				mat4.translate(rotationMatrix, [this.pos.x - 0.3, this.pos.y + 0.5, this.pos.z - 0.5]);
				mat4.rotate(rotationMatrix, degToRad(this.angle), [1, 0, 0]);
				mat4.rotate(rotationMatrix, degToRad(this.angle), [0, 0, 1]);
				mat4.translate(rotationMatrix, [0.0, 0, 1]);
			}
			else 
				if (this.axis == "z" && !this.align) {
					mat4.translate(rotationMatrix, [0.0, 0.5, 0.5]);
					mat4.translate(rotationMatrix, [this.pos.x - 0.53, this.pos.y + 0.5, this.pos.z - 0.5]);
					mat4.rotate(rotationMatrix, degToRad(this.angle), [1, 0, 0]);
					mat4.translate(rotationMatrix, [0.0, -0.5, -0.5]);
				}

	mat4.multiply(this.surfacePlot.mvMatrix, rotationMatrix);

	// Enable blending for transparency.
	this.gl.blendFunc(this.gl.SRC_ALPHA, this.gl.ONE_MINUS_SRC_ALPHA);
	this.gl.enable(this.gl.BLEND);
	this.gl.disable(this.gl.DEPTH_TEST);

	// Text
	this.currentShader = this.shaderTextureProgram;
	this.gl.useProgram(this.currentShader);

	// Enable the vertex arrays for the current shader.
	this.currentShader.vertexPositionAttribute = this.gl.getAttribLocation(this.currentShader, "aVertexPosition");
	this.gl.enableVertexAttribArray(this.currentShader.vertexPositionAttribute);
	this.currentShader.textureCoordAttribute = this.gl.getAttribLocation(this.currentShader, "aTextureCoord");
	this.gl.enableVertexAttribArray(this.currentShader.textureCoordAttribute);

	this.shaderTextureProgram.samplerUniform = this.gl.getUniformLocation(this.shaderTextureProgram, "uSampler");

	this.gl.bindBuffer(this.gl.ARRAY_BUFFER, this.textureVertexPositionBuffer);
	this.gl.vertexAttribPointer(this.currentShader.vertexPositionAttribute, this.textureVertexPositionBuffer.itemSize, this.gl.FLOAT, false, 0, 0);

	this.gl.bindBuffer(this.gl.ARRAY_BUFFER, this.vertexTextureCoordBuffer);
	this.gl.vertexAttribPointer(this.currentShader.textureCoordAttribute, this.vertexTextureCoordBuffer.itemSize, this.gl.FLOAT, false, 0, 0);

	this.gl.bindTexture(this.gl.TEXTURE_2D, this.texture);
	this.gl.uniform1i(this.currentShader.samplerUniform, 0);

	this.gl.bindBuffer(this.gl.ELEMENT_ARRAY_BUFFER, this.textureVertexIndexBuffer);

	this.setMatrixUniforms(this.currentShader, this.surfacePlot.pMatrix, this.surfacePlot.mvMatrix);

	this.gl.drawElements(this.gl.TRIANGLES, this.textureVertexIndexBuffer.numItems, this.gl.UNSIGNED_SHORT, 0);

	// Disable blending for transparency.
	this.gl.disable(this.gl.BLEND);
	this.gl.enable(this.gl.DEPTH_TEST);

	// Disable the vertex arrays for the current shader.
	this.gl.disableVertexAttribArray(this.currentShader.vertexPositionAttribute);
	this.gl.disableVertexAttribArray(this.currentShader.textureCoordAttribute);

	this.mvPopMatrix(this.surfacePlot);
};

/*
 * This class represents the axes for the webGL plot.
 */
GLAxes = function(surfacePlot, points){
	this.shaderProgram = surfacePlot.shaderAxesProgram;
	this.currenShader = null;
	this.gl = surfacePlot.gl;
	this.points = points;
	this.setMatrixUniforms = surfacePlot.setMatrixUniforms;
	this.axesVertexPositionBuffer = null;
	this.surfaceVertexColorBuffer = null;
	this.surfacePlot = surfacePlot;
	var scalefactor = surfacePlot.scalefactor;
	

	this.labels = [];

	this.initAxesBuffers = function(){
		var vertices = [];
		var axisExtent = 100;

		var axisOrigin = [0, 0, 0];
		var xAxisEndPoint = [axisExtent, 0, 0];
		var yAxisEndPoint = [0, axisExtent, 0];
		var zAxisEndPoint = [0, 0, axisExtent];

		// X
		vertices = vertices.concat(axisOrigin);
		vertices = vertices.concat(xAxisEndPoint);

		// Y
		vertices = vertices.concat(axisOrigin);
		vertices = vertices.concat(yAxisEndPoint);

		// Z
		vertices = vertices.concat(axisOrigin);
		vertices = vertices.concat(zAxisEndPoint);

		// Major axis lines.
		this.axesVertexPositionBuffer = this.gl.createBuffer();
		this.gl.bindBuffer(this.gl.ARRAY_BUFFER, this.axesVertexPositionBuffer);

		this.gl.bufferData(this.gl.ARRAY_BUFFER, new Float32Array(vertices), this.gl.DYNAMIC_DRAW);
		this.axesVertexPositionBuffer.itemSize = 3;
		this.axesVertexPositionBuffer.numItems = vertices.length / 3;



		// Set up the main X-axis label.
		var labelPos = {
				x: 10,
				y: 0,
				z: 0
		};
		var glText = new GLText(this.surfacePlot.xTitle, labelPos, 0, surfacePlot, "x", "centre");
		this.labels.push(glText);

		// Set up the main Y-axis label.
		labelPos = {
				x: 0,
				y: 10,
				z: 0
		};
		glText = new GLText(this.surfacePlot.yTitle, labelPos, 90, surfacePlot, "x", "centre");
		this.labels.push(glText);

		// Set up the main Z-axis label.
		labelPos = {
				x: 0,
				y: 0,
				z: 10
		};
		glText = new GLText(this.surfacePlot.zTitle, labelPos, 90, surfacePlot, "z", "centre");
		this.labels.push(glText);

	};

	this.initAxesBuffers();
};

GLAxes.prototype.draw = function(){
	this.currentShader = this.shaderProgram;
	this.gl.useProgram(this.currentShader);

	// Enable the vertex array for the current shader.
	this.currentShader.vertexPositionAttribute = this.gl.getAttribLocation(this.currentShader, "aVertexPosition");
	this.gl.enableVertexAttribArray(this.currentShader.vertexPositionAttribute);

	this.gl.uniform3f(this.currentShader.axesColour, 0.0, 0.0, 0.0); // Set the colour of the Major axis lines.

	// Major axis lines
	this.gl.bindBuffer(this.gl.ARRAY_BUFFER, this.axesVertexPositionBuffer);
	this.gl.vertexAttribPointer(this.currentShader.vertexPositionAttribute, this.axesVertexPositionBuffer.itemSize, this.gl.FLOAT, false, 0, 0);

	this.gl.lineWidth(2);
	this.setMatrixUniforms(this.currentShader, this.surfacePlot.pMatrix, this.surfacePlot.mvMatrix);
	this.gl.drawArrays(this.gl.LINES, 0, this.axesVertexPositionBuffer.numItems);


	// Render the axis labels.
	var numLabels = this.labels.length;

	// Enable the vertex array for the current shader.
	this.gl.disableVertexAttribArray(this.currentShader.vertexPositionAttribute);

	for (var i = 0; i < numLabels; i++) 
		this.labels[i].draw();
};

/*
 * A webGL surface without axes nor any other decoration.
 */
GLSurface = function(surfacePlot, points){
	this.shaderProgram = surfacePlot.shaderProgram;
	this.currentShader = null;
	this.gl = surfacePlot.gl;

	this.points = points;
	this.setMatrixUniforms = surfacePlot.setMatrixUniforms;

	this.surfaceVertexPositionBuffer = null;
	this.pointsVertexPositionBuffer = null;
	this.surfacePlot = surfacePlot;

	this.initSurfaceBuffers = function(){
		var i;
		var vertices = [];

		for(i=0; i<points.length;i++){
			var pointnew =[(points[i][0]),(points[i][1]),(points[i][2])];
			vertices = vertices.concat(pointnew);

		}
		this.pointsVertexPositionBuffer = this.gl.createBuffer();
		this.gl.bindBuffer(this.gl.ARRAY_BUFFER, this.pointsVertexPositionBuffer);

		this.gl.bufferData(this.gl.ARRAY_BUFFER, new Float32Array(vertices), this.gl.DYNAMIC_DRAW);
		this.pointsVertexPositionBuffer.itemSize = 3;
		this.pointsVertexPositionBuffer.numItems = vertices.length/3;


	};

	this.updateSurface = function(data){
		var i;
		var vertices = [];

		for(i=0; i<pointsn.length;i++){
			var pointnew =[(pointsn[i][0]),(pointsn[i][1]),(pointsn[i][2])];
			vertices = vertices.concat(pointnew);

		}
		this.pointsVertexPositionBuffer = this.gl.createBuffer();
		this.gl.bindBuffer(this.gl.ARRAY_BUFFER, this.pointsVertexPositionBuffer);

		this.gl.bufferData(this.gl.ARRAY_BUFFER, new Float32Array(vertices), this.gl.DYNAMIC_DRAW);
		this.pointsVertexPositionBuffer.itemSize = 3;
		this.pointsVertexPositionBuffer.numItems = vertices.length/3;

	};

	this.initSurfaceBuffers();
};

GLSurface.prototype.draw = function(){
	this.currentShader = this.shaderProgram;
	this.gl.useProgram(this.currentShader);

	// Enable the vertex arrays for the current shader.
	this.currentShader.vertexPositionAttribute = this.gl.getAttribLocation(this.currentShader, "aVertexPosition");
	this.gl.enableVertexAttribArray(this.currentShader.vertexPositionAttribute);

	this.gl.uniform3f(this.currentShader.axesColour, 0.0, 0.0, 0.0);

	this.gl.bindBuffer(this.gl.ARRAY_BUFFER, this.pointsVertexPositionBuffer);
	this.gl.vertexAttribPointer(this.currentShader.vertexPositionAttribute, this.pointsVertexPositionBuffer.itemSize, this.gl.FLOAT, false, 0, 0);

	this.setMatrixUniforms(this.currentShader, this.surfacePlot.pMatrix, this.surfacePlot.mvMatrix);
	this.gl.lineWidth(3);
	this.gl.drawArrays(this.gl.POINTS, 0 , this.pointsVertexPositionBuffer.numItems);

	// Disable the vertex arrays for the current shader.
	this.gl.disableVertexAttribArray(this.currentShader.vertexPositionAttribute);

};

/**
 * Given two coordinates, return the Euclidean distance
 * between them
 */
function distance(p1, p2){
	return Math.sqrt(((p1.x - p2.x) *
			(p1.x -
					p2.x)) +
					((p1.y - p2.y) * (p1.y - p2.y)));
}

/*
 * Matrix3d: This class represents a 3D matrix.
 * ********************************************
 */
Matrix3d = function(){
	this.matrix = new Array();
	this.numRows = 4;
	this.numCols = 4;

	this.init = function(){
		this.matrix = new Array();

		for (var i = 0; i < this.numRows; i++) {
			this.matrix[i] = new Array();
		}
	};

	this.getMatrix = function(){
		return this.matrix;
	};

	this.matrixReset = function(){
		for (var i = 0; i < this.numRows; i++) {
			for (var j = 0; j < this.numCols; j++) {
				this.matrix[i][j] = 0;
			}
		}
	};

	this.matrixIdentity = function(){
		this.matrixReset();
		this.matrix[0][0] = this.matrix[1][1] = this.matrix[2][2] = this.matrix[3][3] = 1;
	};

	this.matrixCopy = function(newM){
		var temp = new Matrix3d();
		var i, j;

		for (i = 0; i < this.numRows; i++) {
			for (j = 0; j < this.numCols; j++) {
				temp.getMatrix()[i][j] = (this.matrix[i][0] * newM.getMatrix()[0][j]) + (this.matrix[i][1] * newM.getMatrix()[1][j]) + (this.matrix[i][2] * newM.getMatrix()[2][j]) + (this.matrix[i][3] * newM.getMatrix()[3][j]);
			}
		}

		for (i = 0; i < this.numRows; i++) {
			this.matrix[i][0] = temp.getMatrix()[i][0];
			this.matrix[i][1] = temp.getMatrix()[i][1];
			this.matrix[i][2] = temp.getMatrix()[i][2];
			this.matrix[i][3] = temp.getMatrix()[i][3];
		}
	};

	this.matrixMult = function(m1, m2){
		var temp = new Matrix3d();
		var i, j;

		for (i = 0; i < this.numRows; i++) {
			for (j = 0; j < this.numCols; j++) {
				temp.getMatrix()[i][j] = (m2.getMatrix()[i][0] * m1.getMatrix()[0][j]) + (m2.getMatrix()[i][1] * m1.getMatrix()[1][j]) + (m2.getMatrix()[i][2] * m1.getMatrix()[2][j]) + (m2.getMatrix()[i][3] * m1.getMatrix()[3][j]);
			}
		}

		for (i = 0; i < this.numRows; i++) {
			m1.getMatrix()[i][0] = temp.getMatrix()[i][0];
			m1.getMatrix()[i][1] = temp.getMatrix()[i][1];
			m1.getMatrix()[i][2] = temp.getMatrix()[i][2];
			m1.getMatrix()[i][3] = temp.getMatrix()[i][3];
		}
	};

	this.toString = function(){
		return this.matrix.toString();
	}

	this.init();
};

/*
 * Point3D: This class represents a 3D point.
 * ******************************************
 */
Point3D = function(x, y, z){
	this.displayValue = "";

	this.lx;
	this.ly;
	this.lz;
	this.lt;

	this.wx;
	this.wy;
	this.wz;
	this.wt;

	this.ax;
	this.ay;
	this.az;
	this.at;

	this.dist;

	this.initPoint = function(){
		this.lx = this.ly = this.lz = this.ax = this.ay = this.az = this.at = this.wx = this.wy = this.wz = 0;
		this.lt = this.wt = 1;
	};

	this.init = function(x, y, z){
		this.initPoint();
		this.lx = x;
		this.ly = y;
		this.lz = z;

		this.ax = this.lx;
		this.ay = this.ly;
		this.az = this.lz;
	};

	this.init(x, y, z);
};

/*
 * Polygon: This class represents a polygon on the surface plot.
 * ************************************************************
 */
Polygon = function(cameraPosition, isAxis){
	this.points = new Array();
	this.cameraPosition = cameraPosition;
	this.isAxis = isAxis;
	this.centroid = null;
	this.distanceFromCamera = null;

	this.isAnAxis = function(){
		return this.isAxis;
	};

	this.addPoint = function(point){
		this.points[this.points.length] = point;
	};

	this.distance = function(){
		return this.distance2(this.cameraPosition, this.centroid);
	};

	this.calculateDistance = function(){
		this.distanceFromCamera = this.distance();
	};

	this.calculateCentroid = function(){
		var xCentre = 0;
		var yCentre = 0;
		var zCentre = 0;

		var numPoints = this.points.length * 1.0;

		for (var i = 0; i < numPoints; i++) {
			xCentre += this.points[i].ax;
			yCentre += this.points[i].ay;
			zCentre += this.points[i].az;
		}

		xCentre /= numPoints;
		yCentre /= numPoints;
		zCentre /= numPoints;

		this.centroid = new Point3D(xCentre, yCentre, zCentre);
	};

	this.distance2 = function(p1, p2){
		return ((p1.ax - p2.ax) * (p1.ax - p2.ax)) + ((p1.ay - p2.ay) * (p1.ay - p2.ay)) + ((p1.az - p2.az) * (p1.az - p2.az));
	};

	this.getPoint = function(i){
		return this.points[i];
	};
};

/*
 * PolygonComaparator: Class used to sort arrays of polygons.
 * ************************************************************
 */
PolygonComaparator = function(p1, p2){
	var diff = p1.distanceFromCamera - p2.distanceFromCamera;

	if (diff == 0) 
		return 0;
	else 
		if (diff < 0) 
			return -1;
		else 
			if (diff > 0) 
				return 1;

	return 0;
};

/*
 * Th3dtran: Class for matrix manipuation.
 * ************************************************************
 */
Th3dtran = function(){
	this.rMat;
	this.rMatrix;
	this.objectMatrix;

	this.init = function(){
		this.rMat = new Matrix3d();
		this.rMatrix = new Matrix3d();
		this.objectMatrix = new Matrix3d();

		this.initMatrix();
	};

	this.initMatrix = function(){
		this.objectMatrix.matrixIdentity();
	};

	this.translate = function(x, y, z){
		this.rMat.matrixIdentity();
		this.rMat.getMatrix()[3][0] = x;
		this.rMat.getMatrix()[3][1] = y;
		this.rMat.getMatrix()[3][2] = z;

		this.objectMatrix.matrixCopy(this.rMat);
	};

	this.rotate = function(x, y, z){
		var rx = x * (Math.PI / 180.0);
		var ry = y * (Math.PI / 180.0);
		var rz = z * (Math.PI / 180.0);

		this.rMatrix.matrixIdentity();
		this.rMat.matrixIdentity();
		this.rMat.getMatrix()[1][1] = Math.cos(rx);
		this.rMat.getMatrix()[1][2] = Math.sin(rx);
		this.rMat.getMatrix()[2][1] = -(Math.sin(rx));
		this.rMat.getMatrix()[2][2] = Math.cos(rx);
		this.rMatrix.matrixMult(this.rMatrix, this.rMat);

		this.rMat.matrixIdentity();
		this.rMat.getMatrix()[0][0] = Math.cos(ry);
		this.rMat.getMatrix()[0][2] = -(Math.sin(ry));
		this.rMat.getMatrix()[2][0] = Math.sin(ry);
		this.rMat.getMatrix()[2][2] = Math.cos(ry);
		this.rMat.matrixMult(this.rMatrix, this.rMat);

		this.rMat.matrixIdentity();
		this.rMat.getMatrix()[0][0] = Math.cos(rz);
		this.rMat.getMatrix()[0][1] = Math.sin(rz);
		this.rMat.getMatrix()[1][0] = -(Math.sin(rz));
		this.rMat.getMatrix()[1][1] = Math.cos(rz);
		this.rMat.matrixMult(this.rMatrix, this.rMat);

		this.objectMatrix.matrixCopy(this.rMatrix);
	};

	this.scale = function(scale){
		this.rMat.matrixIdentity();
		this.rMat.getMatrix()[0][0] = scale;
		this.rMat.getMatrix()[1][1] = scale;
		this.rMat.getMatrix()[2][2] = scale;

		this.objectMatrix.matrixCopy(this.rMat);
	};

	this.ChangeObjectPoint = function(p){
		p.ax = (p.lx * this.objectMatrix.getMatrix()[0][0] + p.ly * this.objectMatrix.getMatrix()[1][0] + p.lz * this.objectMatrix.getMatrix()[2][0] + this.objectMatrix.getMatrix()[3][0]);
		p.ay = (p.lx * this.objectMatrix.getMatrix()[0][1] + p.ly * this.objectMatrix.getMatrix()[1][1] + p.lz * this.objectMatrix.getMatrix()[2][1] + this.objectMatrix.getMatrix()[3][1]);
		p.az = (p.lx * this.objectMatrix.getMatrix()[0][2] + p.ly * this.objectMatrix.getMatrix()[1][2] + p.lz * this.objectMatrix.getMatrix()[2][2] + this.objectMatrix.getMatrix()[3][2]);

		return p;
	};

	this.init();
};

/*
 * Point: A simple 2D point.
 * ************************************************************
 */
Point = function(x, y){
	this.x = x;
	this.y = y;
};

/*
 * This function displays tooltips and was adapted from original code by Michael Leigeber.
 * See http://www.leigeber.com/
 */
Tooltip = function(useExplicitPositions, tooltipColour){
	var top = 3;
	var left = 3;
	var maxw = 300;
	var speed = 10;
	var timer = 20;
	var endalpha = 95;
	var alpha = 0;
	var tt, t, c, b, h;
	var ie = document.all ? true : false;

	this.show = function(v, w){
		if (tt == null) {
			tt = document.createElement('div');
			tt.style.color = tooltipColour;

			tt.style.position = 'absolute';
			tt.style.display = 'block';

			t = document.createElement('div');

			t.style.display = 'block';
			t.style.height = '5px';
			t.style.marginleft = '5px';
			t.style.overflow = 'hidden';

			c = document.createElement('div');

			b = document.createElement('div');

			tt.appendChild(t);
			tt.appendChild(c);
			tt.appendChild(b);
			document.body.appendChild(tt);

			if (!ie) {
				tt.style.opacity = 0;
				tt.style.filter = 'alpha(opacity=0)';
			}
			else 
				tt.style.opacity = 1;


		}

		if (!useExplicitPositions) 
			document.onmousemove = this.pos;

		tt.style.display = 'block';
		c.innerHTML = '<span style="font-weight:bold; font-family: arial;">' + v + '</span>';
		tt.style.width = w ? w + 'px' : 'auto';

		if (!w && ie) {
			t.style.display = 'none';
			b.style.display = 'none';
			tt.style.width = tt.offsetWidth;
			t.style.display = 'block';
			b.style.display = 'block';
		}

		if (tt.offsetWidth > maxw) {
			tt.style.width = maxw + 'px';
		}

		h = parseInt(tt.offsetHeight) + top;

		if (!ie) {
			clearInterval(tt.timer);
			tt.timer = setInterval(function(){
				fade(1)
			}, timer);
		}
	};

	this.setPos = function(e){
		tt.style.top = e.y + 'px';
		tt.style.left = e.x + 'px';
	};

	this.pos = function(e){
		var u = ie ? event.clientY + document.documentElement.scrollTop : e.pageY;
		var l = ie ? event.clientX + document.documentElement.scrollLeft : e.pageX;
		tt.style.top = (u - h) + 'px';
		tt.style.left = (l + left) + 'px';
		tt.style.zIndex = 999999999999;
	};

	function fade(d){
		var a = alpha;

		if ((a != endalpha && d == 1) || (a != 0 && d == -1)) {
			var i = speed;

			if (endalpha - a < speed && d == 1) {
				i = endalpha - a;
			}
			else 
				if (alpha < speed && d == -1) {
					i = a;
				}

			alpha = a + (i * d);
			tt.style.opacity = alpha * .01;
			tt.style.filter = 'alpha(opacity=' + alpha + ')';
		}
		else {
			clearInterval(tt.timer);

			if (d == -1) {
				tt.style.display = 'none';
			}
		}
	}

	this.hide = function(){
		if (tt == null) 
			return;

		if (!ie) {
			clearInterval(tt.timer);
			tt.timer = setInterval(function(){
				fade(-1)
			}, timer);
		}
		else {
			tt.style.display = 'none';
		}
	};
};

degToRad = function(degrees){
	return degrees * Math.PI / 180;
};

function hexToR(h){
	return parseInt((cutHex(h)).substring(0, 2), 16)
}

function hexToG(h){
	return parseInt((cutHex(h)).substring(2, 4), 16)
}

function hexToB(h){
	return parseInt((cutHex(h)).substring(4, 6), 16)
}

function cutHex(h){
	return (h.charAt(0) == "#") ? h.substring(1, 7) : h
}

log = function(base, value){
	return Math.log(value) / Math.log(base);
};

JSSurfacePlot.DEFAULT_X_ANGLE = 47;
JSSurfacePlot.DEFAULT_Z_ANGLE = 47;
JSSurfacePlot.DATA_DOT_SIZE = 1;
JSSurfacePlot.DEFAULT_SCALE = 300;
JSSurfacePlot.MIN_SCALE = 5;
JSSurfacePlot.MAX_SCALE = 5000;
JSSurfacePlot.SCALE_FACTOR = 1;

