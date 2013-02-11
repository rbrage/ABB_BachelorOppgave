<html>
<head>
    <title>Rotate example</title>
  <script type="text/javascript">
      function draw() {
          var canvas = document.getElementById("MyCanvas");
          if (canvas.getContext) {
              var ctx = canvas.getContext("2d");
              ctx.beginPath();
              ctx.lineWidth = "3";
              ctx.strokeStyle = "blue";
              ctx.moveTo(0, 0);
              ctx.lineTo(200, 50);
              ctx.moveTo(0, 0);
              ctx.lineTo(250, 50);
              ctx.moveTo(0, 0);
              ctx.lineTo(200, 125);
              ctx.rect(200, 50, 50, 75);
              ctx.stroke();
          }
      }
      function rotateRect() {
          var canvas = document.getElementById("MyCanvas");
          if (canvas.getContext) {
              var ctx = canvas.getContext("2d");
              ctx.clearRect(0, 0, canvas.width, canvas.height);    // Clear the rectangle before scaling.
              ctx.rotate(5 * Math.PI / 180); // Rotate 5 degrees.
              draw();
          }
      }
      function refresh() {
          window.location.reload(false);           // Reload the page.
      }
  </script>
</head>
<body onload="draw();">   
    <button onclick="rotateRect()">Rotate by 5 degrees</button>            
    <button onclick="refresh()">refresh page</button>
    </div>        
    <div>
        <canvas id="MyCanvas" width="300" height="300">This browser or document mode doesn't support canvas</canvas> 
    </div>
</body>
</html>