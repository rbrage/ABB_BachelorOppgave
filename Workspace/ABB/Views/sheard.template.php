<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<style type="text/css">
body {
	padding-top: 60px;
	padding-bottom: 40px;
}
</style>
<link href="/scripts/bootstrap.min.css" rel="stylesheet" media="screen">
<link href="/scripts/bootstrap.css" rel="stylesheet" media="screen">
<script src="/scripts/jquery-1.9.0.js" type="text/javascript"></script>
<script src="/scripts/bootstrap.min.js" type="text/javascript"></script>
<script src="/scripts/SSESideInfo.js" type="text/javascript"></script>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<!--[if IE]><script type="text/javascript" src="../scripts/excanvas.js"></script><![endif]-->
<script type="text/javascript" src="../scripts/SurfacePlot.js"></script>
<script type="text/javascript" src="../scripts/ColourGradient.js"></script>
<script type="text/javascript" src="../scripts/glMatrix-0.9.5.min.js"></script>
<script type="text/javascript" src="../scripts/webgl-utils.js"></script>
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


<title>ABB Analyseprogram</title>
</head>
<body style="padding-top: 60px;">
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container-fluid">
			<div class="span11 offset3">
				
				<div class="nav-collapse">
					<ul class="nav">
						<li><a class="brand" style="padding-top: 5px; padding-bottom: 5px; margin-left: 5px;"	href="#"><img src="/img/abbLogo.gif"> </a></li>
						<li><a href="/">Home</a></li>
						<li><a href="/points/">All Points</a></li>
						<li><a href="/cluster/">Clusters</a></li>
						<li><a href="/stat/">Statistics</a></li>
					</ul>
					<ul class="nav pull-right">
						<li><a href="/settings/"><i class="icon-wrench"></i> Settings</a></li>
					</ul>
				</div>
				<!--/.nav-collapse -->
			</div>
			</div>
		</div>
	</div>
	<div class="container-fluid">
		<div class="row-fluid">

			<div class="span2">
				<div data-spy="affix">
					<ul class="nav nav-list">
						<li class="nav-header">Menu</li>
						<?php 
						if(is_array($this->viewmodel->templatemenu))
							foreach ($this->viewmodel->templatemenu as $section => $name)
								echo "<li><a href=\"#" . $section . "\">" . $name . "</a>";
						?>
					</ul>
				</div>
			</div>
			<div class="span7">
				<?php

				$this->ViewBody();

				?>
			</div>
			<div class="span2">
				<div data-spy="affix">
					<div class="alert alert-info">
						<p class="nav-header">Infomation</p>
						<table class="table table-condensed">
							<tbody>
								<tr>
									<td>Number of triggerpoints:</td>
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
	</div>
</body>
</html>

