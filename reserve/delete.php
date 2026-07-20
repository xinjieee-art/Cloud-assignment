<?php 
    if(is_post()){
    $id=req('reservation_id');

    $stm=$_db->prepare('SELECT * FROM reservation WHERE resevervation_id=?');
    $stm->execute([$id]);

    temp('info','Record have deleted');
    }

    redirect('/');
?>