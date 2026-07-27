<?php 
require '../_base.php';

auth('user');

	$stm=$_db->prepare("SELECT * FROM room");
	
	$shows=$stm->fetchAll();
    

$_title = "Room Detail";
include '../_head.php';
?>

<?php if($shows == ''): 
	temp('info','No record yet');
	redirect('home.php');
	?>
	<?php else:?>
		<?php foreach($shows as $r):?>
	<table>
		<thead>
			<tr>
				<th>Room name:</th>
				<th>Room Status:</th>
				<th>Room Description:</th>
				<th>Room Capacity:</th>
			</tr>
		<thead>
			<tbody>
				<td><?= $r->name ?></td>
				<td><?= $r->status?></td>
				<td><?= $r->description?></td>
				<td><?= $r->capacity?></td>
			</tbody>
	
</table>
<?php endforeach ?>
<?php endif ?>
<?php include '../_foot.php'; ?>