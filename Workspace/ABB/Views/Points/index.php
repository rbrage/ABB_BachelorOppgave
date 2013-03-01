<?php
$this->Template("sheard");
?>

<div class="row">
	<div class=span9>
		<div class="page-header">
			<h2>Table view</h2>
		</div>
		
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Number</th>
					<th>X</th>
					<th>Y</th>
					<th>Z</th>
					<th>Time</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				for($i = 0; $i < 20 && $i < $this->viewmodel->cachedarr->size(); $i++){
					$item = $this->viewmodel->cachedarr->get($i);
					echo "
				<tr>
				<td>".$i."</td>
				<td>".$item->x."</td>
				<td>".$item->y."</td>
				<td>".$item->z."</td>
				<td>".$item->timestamp."</td>
				</tr>";
				}
				?>
			</tbody>
		</table>
		<div class="span4 offset4">
			<button>Get more results</button>
			<script type="text/javascript">
				$.getJSON("/register/points/", function(data){;});
			</script>
		</div>
	</div>
</div>
