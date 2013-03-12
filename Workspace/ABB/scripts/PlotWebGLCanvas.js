
var targetContainer, container;
var points;
var WIDTH, HEIGHT, axisSize;
var camera, scene, renderer;
var group;
var centroidSphere, line , pointsSystem;

var PARTICLE_SIZE = 1;

PlotWebGLCanvas = function(targetContainer, points, data){
	
	
	init(targetContainer, points, data);
	
};


	init = function(targetContainer, points, data){
		
			if ( ! Detector.webgl ) {Detector.addGetWebGLMessage();
	
			}else{
		
					drawWebGL(targetContainer, points, data);
				}
	};
	
	drawWebGL = function(targetContainer, points, data){
		this.WIDTH = data.width;
		this.HEIGHT = data.height;
		this.axisSize = data.axisSize;
		this.targetContainer = targetContainer;
		
		camera = new THREE.PerspectiveCamera(75, WIDTH / HEIGHT, 1, 10000);
	    camera.position.z = 250;
	    

	    renderer = new THREE.WebGLRenderer();
	    renderer.setSize(WIDTH, HEIGHT);
	    
	    
	    scene = new THREE.Scene();
	  
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
		var geometry = new THREE.Geometry();
		
		geometry.vertices.push(
			new THREE.Vector3(0,0,0), new THREE.Vector3( 10, 0, 0 ),
			new THREE.Vector3(0,0,0), new THREE.Vector3( 0, 10, 0 ),
			new THREE.Vector3(0,0,0), new THREE.Vector3( 0, 0, 10 )
		);

		geometry.colors.push(
				new THREE.Color( 0xff0000 ), new THREE.Color( 0xff0000 ),
				new THREE.Color( 0x000000 ), new THREE.Color( 0x00ff00 ),
				new THREE.Color( 0x000000 ), new THREE.Color( 0x0000ff )
			);
		
		var sphereMaterial = new THREE.MeshLambertMaterial({color: 0xFF6600});
		var material = new THREE.LineBasicMaterial({vertexColors: THREE.VertexColors, linewidth: 3});
		var material1 = new THREE.MeshBasicMaterial( {wireframe: true});
		
		line = new THREE.Line(geometry, material);
	    var mesh = new THREE.Mesh(geometry, material1);
	    var ball = new THREE.Mesh(new THREE.SphereGeometry(0.1, 16, 16), sphereMaterial);
	    centroidSphere = new THREE.Object3D();
	    centroidSphere.add(ball);
	    centroidSphere.add(line);
	    centroidSphere.add(mesh);
	   
	    scene.add(centroidSphere);
		centroidSphere.visible=true;
	};
	
	function addControls() {
	
	    controls = new THREE.TrackballControls(camera, renderer.domElement, centroidSphere, pointsSystem);
	    
	    controls.rotateSpeed = 0.7;
	    controls.zoomSpeed = .5;
	    controls.panSpeed = 1;

	    controls.noZoom = false;
	    controls.noPan = false;

	    controls.staticMoving = false;
	    controls.dynamicDampingFactor = 0.3;

	    controls.minDistance = 0.5;
	    controls.maxDistance = Infinity;
	   
	    controls.keys = [65, 83, 68]; // [ rotateKey, zoomKey, panKey ]
	}

	function addAxis(size) {
	    var material1 = new THREE.MeshBasicMaterial({
	        wireframe: true
	    });

	    var geometry = new THREE.Geometry();
		this.axisSize= size;
		
		geometry.vertices.push(
			new THREE.Vector3(), new THREE.Vector3( axisSize || 1, 0, 0 ),
			new THREE.Vector3(), new THREE.Vector3( 0, axisSize || 1, 0 ),
			new THREE.Vector3(), new THREE.Vector3( 0, 0, axisSize || 1 )
		);
		
		var colorBlue = (0x0000ff);
		var colorRed = (0xff0000);
		var colorGreen = (0x00ff00);
		geometry.colors.push(
				new THREE.Color( colorBlue ), 
				
				new THREE.Color( colorRed ),new THREE.Color( colorRed ),
				new THREE.Color( colorGreen ), new THREE.Color( colorGreen ), 
				new THREE.Color( colorBlue )
			);
		
		
		var material = new THREE.LineBasicMaterial({vertexColors: THREE.FaceColors, linewidth: 3});
		
	    var line = new THREE.Line(geometry, material);
	    var mesh = new THREE.Mesh(geometry, material1);
	    group = new THREE.Object3D();
	    group.add(line);
	    group.add(mesh);
	    
	    group.position.set(0,0,0);
	    scene.add(group);
	    
	    
	}
	
	function addPoints(points){
	   
		attributes = {

				size: { type: 'f', value: [] },
				ca: { type: 'c', value: [] }

			};

			uniforms = {

				color: { type: "c", value: new THREE.Color( 0xff0000 ) },
				
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
		pointsSystem.sortParticles = true;
			
		var vertices = pointsSystem.geometry.vertices;
		var values_size = attributes.size.value;
		var values_color = attributes.ca.value;
		
		for( var v = 0; v < vertices.length; v++ ) {

			values_size[ v ] = PARTICLE_SIZE * 0.5;
			values_color[ v ] = new THREE.Color( 0xff0000 );
			
		}
		
		
		scene.add( pointsSystem );
	    
	    
	}
	function animate() {

	    // note: three.js includes requestAnimationFrame shim
	    requestAnimationFrame(animate);
	    controls.update();
	    
	    renderer.render(scene, camera);
	     

	};
	
	


