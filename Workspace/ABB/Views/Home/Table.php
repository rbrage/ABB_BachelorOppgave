<?php
$this->Template("Shared");
?>

<div class="row">
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
			<?php foreach ($viewmodel->arr as &$item){
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


