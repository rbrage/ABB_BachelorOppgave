<?php 
$this->Template("sheard");
?>

<div class="row">
	<div class=span9>

		<div id="result"></div>
		<section id="last">
			<div class="page-header">
				<h2>Last 10 points</h2>
			</div>
			<?php 
			$list = $this->viewmodel->arr->getCachedArrayList();
			$size = $list->size();
			
			if($size != 0){
			?>
			<table class="table table-striped">
				<thead>
					<tr>
						<th>X</th>
						<th>Y</th>
						<th>Z</th>
						<th>Time</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					for ($i = $size -1; ($size - $i) <= 10 && $i >= 0 ;$i--){
						echo "
						<tr>
						<td>".$list->get($i)->x."</td>
					<td>".$list->get($i)->y."</td>
							<td>".$list->get($i)->z."</td>
								<td>".$list->get($i)->timestamp." ms - Date and time: " . date("r", round($list->get($i)->timestamp/1000)) . "</td>
							</tr>";
					}
					?>
				</tbody>
			</table>
			<?php 
			}
			else{
			?>
			<p>Can't find any points in the cache.</p>
			<?php 
			}
			?>
		</section>

		<section id="plot3d">
			<div class="page-header">
				<h2>3D plot</h2>
			</div>
		</section>

		<section id="point">
			<div class="page-header">
				<h2>Table view</h2>
			</div>
			<table class="table table-striped">
				<thead>
					<tr>
						<th>X</th>
						<th>Y</th>
						<th>Z</th>
						<th>Time</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					
// 					foreach ($this->viewmodel->arr as $item){
// 						echo "
// 		<tr>
// 		<td>".$item->x."</td>
// 				<td>".$item->y."</td>
// 					<td>".$item->z."</td>
// 						<td>".$item->timestamp."</td>
// 					</tr>";
// 					}
					?>
				</tbody>
			</table>
		</section>
	</div>
	
	
	
	<div class="span3">
		<div data-spy="affix">
			<div class="alert alert-info">
				<p class="nav-header">Infomation</p>
				<table class="table table-condensed">
					<tbody>
						<tr>
							<td>Number of trigger points:</td>
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

<script>
if(typeof(EventSource)!=="undefined")
  {
  var source=new EventSource("http://localhost:8888/SSE/BasicInfo");
  source.addEventListener("cachesize", function (event) {
      $("#cachesize").html(event.data);
  }, true);

  source.addEventListener("memorysize", function (event) {
      $("#memorysize").html(event.data + "k");
  }, true);
  
  }
else
  {
  	document.getElementById("result").innerHTML="Sorry, your browser does not support server-sent events...";
  }

</script>
