<html lang="en">
<head>
<title>three.js webgl - geometry - tessellation</title>
<meta charset="utf-8">
<meta name="viewport"
	content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<style>
body {
	color: #ffffff;
	font-family: Monospace;
	font-size: 13px;
	text-align: center;
	font-weight: bold;
	background-color: #ffffff;
	margin: 0px;
	overflow: hidden;
}

#info {
	color: #fff;
	position: absolute;
	top: 0px;
	width: 100%;
	padding: 5px;
	z-index: 100;
}

a {
	color: red
}

#stats {
	position: absolute;
	top: 0;
	left: 0
}

#stats #fps {
	background: transparent !important
}

#stats #fps #fpsText {
	color: #777 !important
}

#stats #fps #fpsGraph {
	display: none
}
</style>
<style type="text/css">
iframe.dealply-toast {
	right: -99999px !important;
}

iframe.dealply-toast.fastestext-revealed {
	right: 0px !important;
}
</style>

</head>
<body>

	<script src="/scripts/three.min.js"></script>
	<script src="/scripts/TrackballControls.js"></script>
	
	<div id="3DPlotDiv" width="1300" height="700">
	</div>
	

	
	<script>

			var container;

			var camera, scene, renderer;

			var targetDiv;
			
			var WIDTH = 1300,
			HEIGHT = 600;

			var light, axis;
			

			init();
			animate();
			
			function init() {

				camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 1, 10000);
			    camera.position.z = 250;
			    
			    renderer = new THREE.WebGLRenderer();
				renderer.setSize(WIDTH, HEIGHT);
			    
			    scene = new THREE.Scene();
			    addControls();
			    addShapes();

			   
				container = document.createElement( 'div' );
				container.appendChild(renderer.domElement);

                document.getElementById("3DPlotDiv").appendChild( container );
			   
			    

			}
		
			function addControls() {
			    controls = new THREE.TrackballControls(camera, renderer.domElement);
			    var radius = 14; // scalar value used to determine relative zoom distances
			    controls.rotateSpeed = 1;
			    controls.zoomSpeed = .5;
			    controls.panSpeed = 1;

			    controls.noZoom = false;
			    controls.noPan = false;

			    controls.staticMoving = false;
			    controls.dynamicDampingFactor = 0.3;

			    controls.minDistance = radius * 1.1;
			    controls.maxDistance = radius * 25;

			    controls.keys = [65, 83, 68]; // [ rotateKey, zoomKey, panKey ]
			}

			function addShapes() {
			    var material1 = new THREE.MeshBasicMaterial({
			        color: 0xff0000,
			        wireframe: true
			    });

			    var geometry = new THREE.Geometry();
				var size= 50;
				
				geometry.vertices.push(
					new THREE.Vector3(), new THREE.Vector3( size || 1, 0, 0 ),
					new THREE.Vector3(), new THREE.Vector3( 0, size || 1, 0 ),
					new THREE.Vector3(), new THREE.Vector3( 0, 0, size || 1 )
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

			    var geometry = new THREE.Geometry();

				for ( var i = 0; i < 2000; i ++ ) {

					var vertex = new THREE.Vector3();
					vertex.x = Math.random() * 40;
					vertex.y = Math.random() * 40;
					vertex.z = Math.random() * 40;
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

			}

		</script>
	

</body>
</html>
