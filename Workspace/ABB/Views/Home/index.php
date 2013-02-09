<?php 
$this->Template("sheard");
?>

<div class="row">
	<div class=span9>
		<h2>Table view</h2>
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
				<?php foreach ($this->viewmodel->arr as $item){
					echo "<tr>
		<td>".$item->x."</td>
			<td>".$item->y."</td>
				<td>".$item->z."</td>
				<td>".$item->timestamp."</td>
							</tr>";
				}
				?>

			</tbody>

		</table>

	</div>
	<div class="span3">
		<div data-spy="affix">
			<div class="alert alert-info">
				<p class="nav-header">Infomation</p>
				<table class="table table-condensed">
					<tbody>
						<tr>
							<td>Number og trigger points:</td>
							<td><?php echo $this->viewmodel->listsize ?></td>
						</tr>
						<tr>
							<td>Used memory size:</td>
							<td><?php echo $this->viewmodel->listmemory ?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>


