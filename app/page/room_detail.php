<?php 
require '../_base.php';

auth('user');

	$stm=$_db->prepare("SELECT * FROM room");
	$stm->execute();
	$shows=$stm->fetchAll();
    

$_title = "Room Detail";
include '../_head.php';
?>

<?php if($shows == ''): 
	temp('info','No record yet');
	redirect('home.php');
	?>
	<?php else:?>
	<table style="border: 3px solid black;">
		<thead>
			<tr>
				<th>Room name</th>
				<th>Room Status</th>
				<th>Room Description</th>
				<th>Room Capacity</th>
			</tr>
			
		</thead>

			<tbody>
				<tr>
					<?php foreach($shows as $r):?>
					<td><?= $r->name ?></td>
					<td><?= $r->status?></td>
					<td><?= $r->description?></td>
					<td><?= $r->capacity?></td>
				</tr>
				<?php endforeach; ?>

			</tbody>
	
</table>
<?php endif;?>
<?php include '../_foot.php'; ?>