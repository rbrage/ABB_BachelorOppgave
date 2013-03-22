/**
 * @author Eberhard Graether / http://egraether.com/
 */

THREE.TrackballControls = function ( object, domElement, centroidSphere, pointsSystem, plotWebGL ) {

	THREE.EventDispatcher.call( this );

	var _this = this;
	var STATE = { NONE: -1, ROTATE: 0, ZOOM: 1, PAN: 2, TOUCH_ROTATE: 3, TOUCH_ZOOM: 4, TOUCH_PAN: 5, CTRL: 6, ZOOMEIN: 7, ZOOMEOUT:8 };

	this.object = object;
	this.domElement = ( domElement !== undefined ) ? domElement : document;
	this.centroidSphere = centroidSphere;
	this.line = line;
	this.camera = object;
	this.pointsSystem = pointsSystem;
	this.plotWebGL = plotWebGL;
	// API

	this.enabled = true;

	this.screen = { width: 0, height: 0, offsetLeft: 0, offsetTop: 0 };
	this.radius = ( this.screen.width + this.screen.height ) / 4;

	this.rotateSpeed = 1.0;
	this.zoomSpeed = 1.2;
	this.panSpeed = 0.3;

	this.noRotate = false;
	this.noZoom = false;
	this.noPan = false;

	this.staticMoving = false;
	this.dynamicDampingFactor = 0.2;

	this.minDistance = 0;
	this.maxDistance = Infinity;

	this.keys = [ 65 /*A*/, 83 /*S*/, 68 /*D*/ ,17 /*CTRL*/ ];

	// internals

	this.target = new THREE.Vector3();

	var lastPosition = new THREE.Vector3();

	var _state = STATE.NONE,
	_prevState = STATE.NONE,

	_eye = new THREE.Vector3(),

	_rotateStart = new THREE.Vector3(),
	_rotateEnd = new THREE.Vector3(),

	_zoomStart = new THREE.Vector2(),
	_zoomEnd = new THREE.Vector2(),

	_touchZoomDistanceStart = 0,
	_touchZoomDistanceEnd = 0,

	_panStart = new THREE.Vector2(),
	_panEnd = new THREE.Vector2();
	
	//Zoome
	var scale = 1;
	var mousex;
	var mousey;
	var mouseDown = false;
	var INTERSECTED;
	
	
	// for reset

	this.target0 = this.target.clone();
	this.position0 = this.object.position.clone();
	this.up0 = this.object.up.clone();

	// events

	var changeEvent = { type: 'change' };


	// methods

	this.handleResize = function () {

		this.screen.width = window.innerWidth;
		this.screen.height = window.innerHeight;

		this.screen.offsetLeft = 0;
		this.screen.offsetTop = 0;

		this.radius = ( this.screen.width + this.screen.height ) / 4;

	};

	this.handleEvent = function ( event ) {

		if ( typeof this[ event.type ] == 'function' ) {

			this[ event.type ]( event );

		}

	};

	this.getMouseOnScreen = function ( clientX, clientY ) {

		return new THREE.Vector2(
			( clientX - _this.screen.offsetLeft ) / _this.radius * 0.5,
			( clientY - _this.screen.offsetTop ) / _this.radius * 0.5
		);

	};

	this.getMouseProjectionOnBall = function ( clientX, clientY ) {

		var mouseOnBall = new THREE.Vector3(
			( clientX - _this.screen.width * 0.5 - _this.screen.offsetLeft ) / _this.radius,
			( _this.screen.height * 0.5 + _this.screen.offsetTop - clientY ) / _this.radius,
			0.0
		);

		var length = mouseOnBall.length();

		if ( length > 1.0 ) {

			mouseOnBall.normalize();

		} else {

			mouseOnBall.z = Math.sqrt( 1.0 - length * length );

		}

		_eye.copy( _this.object.position ).sub( _this.target );

		var projection = _this.object.up.clone().setLength( mouseOnBall.y );
		projection.add( _this.object.up.clone().cross( _eye ).setLength( mouseOnBall.x ) );
		projection.add( _eye.setLength( mouseOnBall.z ) );

		return projection;

	};

	this.rotateCamera = function () {

		var angle = Math.acos( _rotateStart.dot( _rotateEnd ) / _rotateStart.length() / _rotateEnd.length() );

		if ( angle ) {

			var axis = ( new THREE.Vector3() ).crossVectors( _rotateStart, _rotateEnd ).normalize(),
				quaternion = new THREE.Quaternion();

			angle *= _this.rotateSpeed;

			quaternion.setFromAxisAngle( axis, -angle );

			_eye.applyQuaternion( quaternion );
			_this.object.up.applyQuaternion( quaternion );

			_rotateEnd.applyQuaternion( quaternion );

			if ( _this.staticMoving ) {

				_rotateStart.copy( _rotateEnd );

			} else {

				quaternion.setFromAxisAngle( axis, angle * ( _this.dynamicDampingFactor - 1.0 ) );
				_rotateStart.applyQuaternion( quaternion );

			}

		}

	};

	this.zoomCamera = function () {

		if ( _state === STATE.TOUCH_ZOOM ) {

			var factor = _touchZoomDistanceStart / _touchZoomDistanceEnd;
			_touchZoomDistanceStart = _touchZoomDistanceEnd;
			_eye.multiplyScalar( factor );

		} else {

			var factor = 1.0 + ( _zoomEnd.y - _zoomStart.y ) * _this.zoomSpeed;
			
			
			if ( factor !== 1.0 && factor > 0.0 ) {

				_eye.multiplyScalar( factor );

				if ( _this.staticMoving ) {

					_zoomStart.copy( _zoomEnd );

				} else {

					_zoomStart.y += ( _zoomEnd.y - _zoomStart.y ) * this.dynamicDampingFactor;
				

				}

			}

		}

	};

	this.panCamera = function () {

		var mouseChange = _panEnd.clone().sub( _panStart );

		if ( mouseChange.lengthSq() ) {

			mouseChange.multiplyScalar( _eye.length() * _this.panSpeed );

			var pan = _eye.clone().cross( _this.object.up ).setLength( mouseChange.x );
			pan.add( _this.object.up.clone().setLength( mouseChange.y ) );

			_this.object.position.add( pan );
			_this.target.add( pan );

			if ( _this.staticMoving ) {

				_panStart = _panEnd;

			} else {

				_panStart.add( mouseChange.subVectors( _panEnd, _panStart ).multiplyScalar( _this.dynamicDampingFactor ) );

			}

		}

	};

	this.checkDistances = function () {

		if ( !_this.noZoom || !_this.noPan ) {

			if ( _this.object.position.lengthSq() > _this.maxDistance * _this.maxDistance ) {

				_this.object.position.setLength( _this.maxDistance );

			}

			if ( _eye.lengthSq() < _this.minDistance * _this.minDistance ) {

				_this.object.position.addVectors( _this.target, _eye.setLength( _this.minDistance ) );

			}

		}

	};
	
	this.checkSelect = function(){
	
		if(_state === STATE.CTRL && mouseDown){
			var projector = new THREE.Projector();
			var ray = new THREE.Ray();
			ray.setPrecision( 1e-4);
			ray.setThreshold(5);
			
			var vector = new THREE.Vector3( mousex, mousey, 0.5 );
			projector.unprojectVector ( vector, _this.object );
			ray.setOrigin( _this.object.position ).setDirection( vector.sub( _this.object.position ).normalize() );
			
			if(pointsSystem.children[0] instanceof THREE.Particle){
				var intersects = ray.intersectParticle(pointsSystem);
				
				if(intersects != null){	
				
					plotWebGL.getSelectedPoint(intersects[ 0 ].point.x ,intersects[ 0 ].point.y ,intersects[ 0 ].point.z );
					
					if ( intersects.length > 0 ) {
						INTERSECTED = intersects[ 0 ].vertex
				
						pointsSystem.children[INTERSECTED].material.color = new THREE.Color(0x00ff00);
						pointsSystem.children[INTERSECTED].material.needsUpdate = true;
					}
				}
				
			}else{
			
				var intersects = ray.intersectObject(pointsSystem);
				if(intersects != null){	
			
				plotWebGL.getSelectedPoint(intersects[ 0 ].point.x ,intersects[ 0 ].point.y ,intersects[ 0 ].point.z );
				
				
				if ( intersects.length > 0 ) {
					
					
					attributes.size.value[ INTERSECTED ] = PARTICLE_SIZE;
					attributes.ca.value[ INTERSECTED ] = PARTICLE_COLOR;
					
					INTERSECTED = intersects[ 0 ].vertex;
					PARTICLE_COLOR = intersects[0].object.material.attributes.ca.value[INTERSECTED];
					attributes.ca.value[ INTERSECTED ] = 0x000000;
					attributes.ca.needsUpdate = true;
					
					attributes.size.value[ INTERSECTED ] = PARTICLE_SIZE *1.5;
					attributes.size.needsUpdate = true;
				
				}else {

					attributes.size.value[ INTERSECTED ] = PARTICLE_SIZE;
					attributes.size.needsUpdate = true;
					
					attributes.ca.value[ INTERSECTED ] = PARTICLE_COLOR;
					attributes.ca.needsUpdate = true;
			
					INTERSECTED = null;
					}
				}
			}
			
		}
	};

	this.update = function () {

		_eye.subVectors( _this.object.position, _this.target );

		if ( !_this.noRotate ) {

			_this.rotateCamera();

		}

		if ( !_this.noZoom ) {

			_this.zoomCamera();

		}

		if ( !_this.noPan ) {

			_this.panCamera();

		}
		
		_this.checkSelect();

		_this.object.position.addVectors( _this.target, _eye );

		_this.checkDistances();

		_this.object.lookAt( _this.target );

		if ( lastPosition.distanceToSquared( _this.object.position ) > 0 ) {

			_this.dispatchEvent( changeEvent );

			lastPosition.copy( _this.object.position );

		}
		
		if(_eye.lengthSq()<500)
			scale = 0.5;
		if(_eye.lengthSq()<1000&& (_eye.lengthSq()>500))
			scale = 1;
		if((_eye.lengthSq()<10000) && (_eye.lengthSq()>1000))
			scale = 2;
		if((_eye.lengthSq()<15000) && (_eye.lengthSq()>10000))
			scale = 4;
		if((_eye.lengthSq()<100000) && (_eye.lengthSq()>15000))
			scale = 6;
		if((_eye.lengthSq()<1000000) && (_eye.lengthSq()>100000))
			scale = 10;
		
   	 	
   	 	this.centroidSphere.position.x = _this.target.x;
   	 	this.centroidSphere.position.y = _this.target.y;
   	 	this.centroidSphere.position.z = _this.target.z;
		
   	 	this.centroidSphere.scale.x = scale;
	 	this.centroidSphere.scale.y = scale;
	 	this.centroidSphere.scale.z = scale;
		
		
	};

	this.reset = function () {

		_state = STATE.NONE;
		_prevState = STATE.NONE;

		_this.target.copy( _this.target0 );
		_this.object.position.copy( _this.position0 );
		_this.object.up.copy( _this.up0 );

		_eye.subVectors( _this.object.position, _this.target );

		_this.object.lookAt( _this.target );

		_this.dispatchEvent( changeEvent );

		lastPosition.copy( _this.object.position );

	};

	// listeners

	function keydown( event ) {
		
		if ( _this.enabled === false ) return;

		window.removeEventListener( 'keydown', keydown );

		_prevState = _state;

		if ( _state !== STATE.NONE ) {

			return;

		} else if ( event.keyCode === _this.keys[ STATE.ROTATE ] && !_this.noRotate ) {

			_state = STATE.ROTATE;

		} else if ( event.keyCode === _this.keys[ STATE.ZOOM ] && !_this.noZoom ) {

			_state = STATE.ZOOM;

		} else if ( event.keyCode === _this.keys[ STATE.PAN ] && !_this.noPan ) {
			
			_state = STATE.PAN;
			
		} else if ( event.keyCode === 17 ) {
		
			_state = STATE.CTRL;
			
		
		} else if ( event.keyCode === 107 ) {
		
			_state = STATE.ZOOMEIN;
			_zoomStart.y += ( 1 / 3 ) * 0.5;
			
		
		} else if ( event.keyCode === 109 ) {
		
			_state = STATE.ZOOMEOUT;
			_zoomStart.y += ( 1 / -3 ) * 0.5;
		}
		

	}

	function keyup( event ) {

		if ( _this.enabled === false ) return;

		_state = _prevState;

		window.addEventListener( 'keydown', keydown, false );

	}

	function mousedown( event ) {
			
		if( _state === STATE.CTRL){
			mouseDown = true;
			mousex = ( event.clientX / window.innerWidth ) * 2 - 1;
			mousey = - ( event.clientY / window.innerHeight ) * 2 + 1;
			document.addEventListener( 'mouseup', mouseup, false );
		} 
		else {
		if ( _this.enabled === false ) return;

		event.preventDefault();
		event.stopPropagation();

		if ( _state === STATE.NONE ) {

			_state = event.button;

		}

		if ( _state === STATE.ROTATE && !_this.noRotate ) {

			_rotateStart = _rotateEnd = _this.getMouseProjectionOnBall( event.clientX, event.clientY );
			
			

		} else if ( _state === STATE.ZOOM && !_this.noZoom ) {

			_zoomStart = _zoomEnd = _this.getMouseOnScreen( event.clientX, event.clientY );

		} else if ( _state === STATE.PAN && !_this.noPan ) {

			_panStart = _panEnd = _this.getMouseOnScreen( event.clientX, event.clientY );

		}
		
	   	 
		document.addEventListener( 'mousemove', mousemove, false );
		document.addEventListener( 'mouseup', mouseup, false );
		}

	}
	
	

	function mousemove( event ) {

		if ( _this.enabled === false ) return;

		event.preventDefault();
		event.stopPropagation();

		if ( _state === STATE.ROTATE && !_this.noRotate ) {

			_rotateEnd = _this.getMouseProjectionOnBall( event.clientX, event.clientY );
			
		} else if ( _state === STATE.ZOOM && !_this.noZoom ) {

			_zoomEnd = _this.getMouseOnScreen( event.clientX, event.clientY );
			

		} else if ( _state === STATE.PAN && !_this.noPan ) {

			_panEnd = _this.getMouseOnScreen( event.clientX, event.clientY );
			
		}
		

	}

	function mouseup( event ) {
		
		mouseDown = false;
		
		if ( _this.enabled === false ) return;

		event.preventDefault();
		event.stopPropagation();

		_state = STATE.NONE;

		document.removeEventListener( 'mousemove', mousemove );
		document.removeEventListener( 'mouseup', mouseup );
		
	}
	
	function mousewheel( event ) {

		if ( _this.enabled === false ) return;

		event.preventDefault();
		event.stopPropagation();

		var delta = 0;

		if ( event.wheelDelta ) { // WebKit / Opera / Explorer 9

			delta = event.wheelDelta / 40;

		} else if ( event.detail ) { // Firefox

			delta = - event.detail / 3;

		}

		_zoomStart.y += ( 1 / delta ) * 0.05;
		
		
	}

	function touchstart( event ) {

		if ( _this.enabled === false ) return;

		switch ( event.touches.length ) {

			case 1:
				_state = STATE.TOUCH_ROTATE;
				_rotateStart = _rotateEnd = _this.getMouseProjectionOnBall( event.touches[ 0 ].pageX, event.touches[ 0 ].pageY );
				break;

			case 2:
				_state = STATE.TOUCH_ZOOM;
				var dx = event.touches[ 0 ].pageX - event.touches[ 1 ].pageX;
				var dy = event.touches[ 0 ].pageY - event.touches[ 1 ].pageY;
				_touchZoomDistanceEnd = _touchZoomDistanceStart = Math.sqrt( dx * dx + dy * dy );
				break;

			case 3:
				_state = STATE.TOUCH_PAN;
				_panStart = _panEnd = _this.getMouseOnScreen( event.touches[ 0 ].pageX, event.touches[ 0 ].pageY );
				break;

			default:
				_state = STATE.NONE;

		}

	}

	function touchmove( event ) {

		if ( _this.enabled === false ) return;

		event.preventDefault();
		event.stopPropagation();

		switch ( event.touches.length ) {

			case 1:
				_rotateEnd = _this.getMouseProjectionOnBall( event.touches[ 0 ].pageX, event.touches[ 0 ].pageY );
				break;

			case 2:
				var dx = event.touches[ 0 ].pageX - event.touches[ 1 ].pageX;
				var dy = event.touches[ 0 ].pageY - event.touches[ 1 ].pageY;
				_touchZoomDistanceEnd = Math.sqrt( dx * dx + dy * dy )
				break;

			case 3:
				_panEnd = _this.getMouseOnScreen( event.touches[ 0 ].pageX, event.touches[ 0 ].pageY );
				break;

			default:
				_state = STATE.NONE;

		}

	}

	function touchend( event ) {

		if ( _this.enabled === false ) return;

		switch ( event.touches.length ) {

			case 1:
				_rotateStart = _rotateEnd = _this.getMouseProjectionOnBall( event.touches[ 0 ].pageX, event.touches[ 0 ].pageY );
				break;

			case 2:
				_touchZoomDistanceStart = _touchZoomDistanceEnd = 0;
				break;

			case 3:
				_panStart = _panEnd = _this.getMouseOnScreen( event.touches[ 0 ].pageX, event.touches[ 0 ].pageY );
				break;

		}

		_state = STATE.NONE;

	}

	this.domElement.addEventListener( 'contextmenu', function ( event ) { event.preventDefault(); }, false );

	this.domElement.addEventListener( 'mousedown', mousedown, false );

	this.domElement.addEventListener( 'mousewheel', mousewheel, false );
	this.domElement.addEventListener( 'DOMMouseScroll', mousewheel, false ); // firefox

	this.domElement.addEventListener( 'touchstart', touchstart, false );
	this.domElement.addEventListener( 'touchend', touchend, false );
	this.domElement.addEventListener( 'touchmove', touchmove, false );

	window.addEventListener( 'keydown', keydown, false );
	window.addEventListener( 'keyup', keyup, false );

	this.handleResize();

};
