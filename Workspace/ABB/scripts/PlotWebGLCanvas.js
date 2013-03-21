
var targetContainer, container;
var points;
var WIDTH, HEIGHT, axisSize;
var camera, scene, renderer;
var group;
var centroidSphere, line , pointsSystem;
var _state;

var PARTICLE_SIZE = 2;
var PARTICLE_COLOR;
var webGL;

PlotWebGLCanvas = function(targetContainer, points, data){
	
	init(targetContainer, points, data);
	this.points = points;
	};
	
	init = function(targetContainer, points, data){
		
			if ( ! Detector.webgl ) {
				webGL = false;
				drawCanvas(targetContainer, points, data);
	
			}else{
				webGL = true;
				drawWebGL(targetContainer, points, data);
				}
	};
	
	drawWebGL = function(targetContainer, points, data){
		this.WIDTH = data.width;
		this.HEIGHT = data.height;
		this.axisSize = data.axisSize;
		this.targetContainer = targetContainer;
		
		camera = new THREE.PerspectiveCamera(75, WIDTH / HEIGHT, 1, 10000);
		camera.up = new THREE.Vector3( 0, 0, 1 );
	    camera.position.x = 500;
		
	   
		renderer = new THREE.WebGLRenderer( { clearColor: 0xffffff, clearAlpha: 1 } );
		renderer.setSize(WIDTH, HEIGHT);
	    
	    scene = new THREE.Scene();
		
		PARTICLE_COLOR = new THREE.Color(0xff0000);
	    addCameraRotationPoint();
	    addAxis(axisSize);
	    addPoints(points);
		addControls();
		
	    
		var container = document.createElement( 'div' );
		container.id ="canvas";
		container.appendChild(renderer.domElement);
		this.targetContainer.appendChild( container );
		
		
	    animate();
		
	};
	
	drawCanvas = function(targetContainer, points, data){
		this.WIDTH = data.width;
		this.HEIGHT = data.height;
		this.axisSize = data.axisSize;
		this.targetContainer = targetContainer;
		
		camera = new THREE.PerspectiveCamera(75, WIDTH / HEIGHT, 1, 10000);
		camera.up = new THREE.Vector3( 0, 0, 1 );
	    camera.position.x = 200;
		
	    renderer = new THREE.CanvasRenderer();
	    renderer.setSize(WIDTH, HEIGHT);
	    
	    scene = new THREE.Scene();
		
		PARTICLE_COLOR = new THREE.Color(0xff0000);
	    addCameraRotationPoint();
	    addAxis(axisSize);
	    addPoints(points);
		addControls();
		
	    
		var container = document.createElement( 'div' );
		container.appendChild(renderer.domElement);
		this.targetContainer.appendChild( container );
		
		
	    animate();
		
	};
	
	function addCameraRotationPoint(){
		
		centroidSphere = new THREE.Object3D();
		var lineGeometry = new THREE.Geometry();

			lineGeometry.vertices.push( new THREE.Vector3() );
			lineGeometry.vertices.push( new THREE.Vector3( 0, 10, 0 ) );
		
		// X-Axis
		var x_line = new THREE.Line( lineGeometry, new THREE.LineBasicMaterial( { color : 0xff0000 } ) );
		x_line.rotation.z = - Math.PI / 2;
		centroidSphere.add( x_line );
		
		// Y-Axis
		var y_line = new THREE.Line( lineGeometry, new THREE.LineBasicMaterial( { color : 0x00ff00 } ) );
		centroidSphere.add( y_line );
		
		// Z-Axis
		var z_line = new THREE.Line( lineGeometry, new THREE.LineBasicMaterial( { color : 0x0000ff } ) );
		z_line.rotation.x = Math.PI / 2;
		centroidSphere.add( z_line );
		
		var sphereMaterial = new THREE.MeshLambertMaterial({color: 0xFF6600});
	    var ball = new THREE.Mesh(new THREE.SphereGeometry(0.1, 16, 16), sphereMaterial);
	   
	    centroidSphere.add(ball);
	   
	    scene.add(centroidSphere);
		centroidSphere.visible=true;
	};
	
	function addControls() {
	
	    controls = new THREE.TrackballControls(camera, renderer.domElement, centroidSphere, pointsSystem, this);
	    
	    controls.rotateSpeed = 0.5;
	    controls.zoomSpeed = 0.5;
	    controls.panSpeed = 0.5;

	    controls.noZoom = false;
	    controls.noPan = false;

	    controls.staticMoving = false;
	    controls.dynamicDampingFactor = 0.5;

	    controls.minDistance = 0.1;
	    controls.maxDistance = 5000;
	   
	    controls.keys = [65, 83, 68]; // [ rotateKey, zoomKey, panKey ]
	}

	function addAxis(size) {
	    
		this.axisSize= size;
		group = new THREE.Object3D();
		
		var lineGeometry = new THREE.Geometry();

			lineGeometry.vertices.push( new THREE.Vector3() );
			lineGeometry.vertices.push( new THREE.Vector3( 0, size, 0 ) );
		
		// X-Axis
		var x_line = new THREE.Line( lineGeometry, new THREE.LineBasicMaterial( { color : 0xff0000 } ) );
		x_line.rotation.z = - Math.PI / 2;
		group.add( x_line );
		
		// Y-Axis
		var y_line = new THREE.Line( lineGeometry, new THREE.LineBasicMaterial( { color : 0x00ff00 } ) );
		group.add( y_line );
		
		// Z-Axis
		var z_line = new THREE.Line( lineGeometry, new THREE.LineBasicMaterial( { color : 0x0000ff } ) );
		z_line.rotation.x = Math.PI / 2;
		group.add( z_line );
		
	    
	    scene.add(group);
	    
	    
	}
	
	function addPoints(points){
	if(points==null) points=this.points;
		if(webGL){	  
			attributes = {

					size: { type: 'f', value: [] },
					ca: { type: 'c', value: [] }
					

				};

			uniforms = {

					color: { type: "c", value: new THREE.Color( 0xff0000 ) },
					texture: { type: "t", value: THREE.ImageUtils.loadTexture( "img/ball.png" ) }
					
				};

				

			var shaderMaterial = new THREE.ShaderMaterial( {

					uniforms: uniforms,
					attributes: attributes,
					vertexShader: document.getElementById( 'vertexshader' ).textContent,
					fragmentShader: document.getElementById( 'fragmentshader' ).textContent,
					transparent:	true
	  
				});
			
			var geometry = new THREE.Geometry();
			var color = [new THREE.Color(0x0000FF), new THREE.Color(0x008000), new THREE.Color(0xFFA000), new THREE.Color(0xFF0000), new THREE.Color(0xFFFF00), new THREE.Color(0x800080)];
			
			for ( var i = 0; i < points.length; i ++ ) {

				var vertex = new THREE.Vector3();
				vertex.x = points[i][0];
				vertex.y = points[i][1];
				vertex.z = points[i][2];
				geometry.vertices.push( vertex );
				
			}

			pointsSystem = new THREE.ParticleSystem( geometry, shaderMaterial );
			pointsSystem.dynamic = true;
			pointsSystem.sortParticles = true;
			
			var vertices = pointsSystem.geometry.vertices;
			var values_size = attributes.size.value;
			var values_color = attributes.ca.value;
			
			for( var v = 0; v < vertices.length; v++ ) {
				if((vertices[v].x==points[v][0])&&(vertices[v].y==points[v][1])&&(vertices[v].z==points[v][2])){
					if(points[v][4]==0){
						values_color[ v ] = color[0];
					}else if(points[v][4]==1){
						values_color[ v ] = color[1];
					}else if(points[v][4]==2){
						values_color[ v ] = color[2];
					}else if(points[v][4]==3){
						values_color[ v ] = color[3];
					}else if(points[v][4]==4){
						values_color[ v ] = color[4];
					}else if(points[v][4]==5){
						values_color[ v ] = color[5];
					}
					else{
						values_color[ v ] = 0xffffff;
					}
					
				};
				
				values_size[ v ] = PARTICLE_SIZE;
				
				
				
			}
			
		}else{
		
			var PI2 = Math.PI * 2;
					var program = function ( context ) {

						context.beginPath();
						context.arc( 0, 0, 0.1, 0, PI2, true );
						context.closePath();
						context.fill();

					}
			pointsSystem = new THREE.Object3D();
					
					for ( var i = 0; i < points.length; i++ ) {
						particle = new THREE.Particle( new THREE.ParticleCanvasMaterial( { color: PARTICLE_COLOR, program: program } ) );
						particle.position.x = points[i][0];
						particle.position.y = points[i][1];
						particle.position.z = points[i][2];
						particle.scale.x = particle.scale.y = 5;
						pointsSystem.add( particle );
					}

		}
		
		scene.add( pointsSystem );
	    
	    
	}
	
	function drawClusterCircle(x,y,z, size){
		var resolution = 100;
		var amplitude = size;
		var size = 360 / resolution;

		var geometry = new THREE.Geometry();
		var material = new THREE.LineBasicMaterial( { color: 0xaa00ff, opacity: 0.9} );
		for(var i = 0; i <= resolution; i++) {
			var segment = ( i * size ) * Math.PI / 180;
			geometry.vertices.push( new THREE.Vertex( new THREE.Vector3( Math.cos( segment ) * amplitude, 0, Math.sin( segment ) * amplitude ) ) );         
		}

		var line = new THREE.Line( geometry, material );
		line.position.set(x,y,z);
		line.rotation.z = - Math.PI / 2;
		scene.add(line);
		
	};
	
	function animate() {

	    requestAnimationFrame(animate);
	    controls.update();
	    renderer.render(scene, camera);
	
	};
	
	function getSelectedPoint(x,y,z){
		
		for(var i=0;i<this.points.length;i++){
			if((this.points[i][0] == x) && (this.points[i][1] == y) && (this.points[i][2] == z)) {
				
				var text="" +this.points[i][0]+" ," +this.points[i][1]+" ," +this.points[i][2]+"";
				document.getElementById("coordinates").innerHTML=text;
				
				var time="" +this.points[i][3];
				document.getElementById("timestamp").innerHTML=time;
				var clusternumber = this.points[i][4];
				var cluster="" +this.points[i][4];
				document.getElementById("cluster").innerHTML=cluster;
				
				
			}
		}
	}
	
	function reload(){
	addPoints();
	addControls();
	animate();
};