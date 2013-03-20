
var targetContainer, container;
var points;
var WIDTH, HEIGHT, axisSize;
var camera, scene, renderer;
var group;
var centroidSphere, line , pointsSystem;
var _state;

var PARTICLE_SIZE = 1;
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
	    camera.position.x = 200;
		
	    renderer = new THREE.WebGLRenderer();
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

	    controls.minDistance = 10.0;
	    controls.maxDistance = Infinity;
	   
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
	  
		if(webGL){	  
		attributes = {

				size: { type: 'f', value: [] },
				ca: { type: 'c', value: [] }
				

			};

		uniforms = {

				color: { type: "c", value: new THREE.Color( 0xff0000 ) },
				texture: { type: "t", value: 0, texture: THREE.ImageUtils.loadTexture( "/img/point.png" ) }
				
			};

		var shaderMaterial = new THREE.ShaderMaterial( {

				uniforms: uniforms,
				attributes: attributes,
				vertexShader: document.getElementById( 'vertexshader' ).textContent,
				fragmentShader: document.getElementById( 'fragmentshader' ).textContent,
				depthTest: false,

			});
		
		var geometry = new THREE.Geometry();

		for ( var i = 0; i < points.length; i ++ ) {

			var vertex = new THREE.Vector3();
			vertex.x = points[i][0];
			vertex.y = points[i][1];
			vertex.z = points[i][2];
			geometry.vertices.push( vertex );
		}

		pointsSystem = new THREE.ParticleSystem( geometry, shaderMaterial );
		pointsSystem.dynamic = true;
		pointsSystem.sortParticles = false;
			
		var vertices = pointsSystem.geometry.vertices;
		var values_size = attributes.size.value;
		var values_color = attributes.ca.value;
		
		for( var v = 0; v < vertices.length; v++ ) {

			values_size[ v ] = PARTICLE_SIZE;
			values_color[ v ] = PARTICLE_COLOR;
			
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
	function animate() {

	    
	    requestAnimationFrame(animate);
	    controls.update();
	    renderer.render(scene, camera);
	     

	};
	
	function getSelectedPoint(x,y,z){
	console.log(x,y,z);
	
	
			//var text="intsersects: (" +intersects.length +")";
			//document.getElementById("demo").innerHTML=text;
			
				
	}
	