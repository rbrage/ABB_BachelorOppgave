
var targetContainer, container;
var points;
var WIDTH, HEIGHT, axisSize;
var camera, scene, renderer;

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
	    
	    projector = new THREE.Projector(); 
	    
	    scene = new THREE.Scene();
	    addControls();
	    addAxis(axisSize);
	    addPoints(points);
	    
	   
		var container = document.createElement( 'div' );
		container.appendChild(renderer.domElement);
		this.targetContainer.appendChild( container );
	   
	    animate();
		
	};
	
	function addControls() {
	    controls = new THREE.TrackballControls(camera, renderer.domElement);
	    var radius = 14; // scalar value used to determine relative zoom distances
	    controls.rotateSpeed = 1;
	    controls.zoomSpeed = .5;
	    controls.panSpeed = 1;

	    controls.noZoom = false;
	    controls.noPan = false;

	    controls.staticMoving = true;
	    controls.dynamicDampingFactor = 0.3;

	    controls.minDistance = radius * 0.5;
	    controls.maxDistance = radius * 200;

	    controls.keys = [65, 83, 68]; // [ rotateKey, zoomKey, panKey ]
	}

	function addAxis(size) {
	    var material1 = new THREE.MeshBasicMaterial({
	        color: 0xff0000,
	        wireframe: true
	    });

	    var geometry = new THREE.Geometry();
		this.axisSize= size;
		
		geometry.vertices.push(
			new THREE.Vector3(), new THREE.Vector3( axisSize || 1, 0, 0 ),
			new THREE.Vector3(), new THREE.Vector3( 0, axisSize || 1, 0 ),
			new THREE.Vector3(), new THREE.Vector3( 0, 0, axisSize || 1 )
		);

		geometry.colors.push(
				new THREE.Color( 0x000000 ), new THREE.Color( 0xff0000 ),
				new THREE.Color( 0x000000 ), new THREE.Color( 0x00ff00 ),
				new THREE.Color( 0x000000 ), new THREE.Color( 0x0000ff )
			);
		var material = new THREE.LineBasicMaterial({vertexColors: THREE.VertexColors});
	   

	    var line = new THREE.Line(geometry, material);
	    var mesh = new THREE.Mesh(geometry, material1);
	    var group = new THREE.Object3D();
	    group.add(line);
	    group.add(mesh);
	    
	    group.position.set(0,0,0);
	    scene.add(group);
	    
	    
	}
	
	function addPoints(points){
	    var geometry = new THREE.Geometry();

		for ( var i = 0; i < points.length; i ++ ) {

			var vertex = new THREE.Vector3();
			vertex.x = points[i][0];
			vertex.y = points[i][1];
			vertex.z = points[i][2];
			geometry.vertices.push( vertex );

			geometry.colors.push( new THREE.Color( 0xff0000 ) );

		}

		var material = new THREE.ParticleBasicMaterial( { size: 3, vertexColors: THREE. VertexColors, depthTest: false, opacity: 1, sizeAttenuation: false, transparent: false } );

		var mesh = new THREE.ParticleSystem( geometry, material );
		scene.position.set(0,0,0);
		scene.add( mesh );
	    
	    
	}
	function animate() {

	    // note: three.js includes requestAnimationFrame shim
	    requestAnimationFrame(animate);
	    controls.update();
	    renderer.render(scene, camera);

	};
	
	


