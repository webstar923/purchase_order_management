<?php
require_once('../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `project_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=stripslashes($v);
        }

        $contact_ids = join("','", explode(',', $contact));
        $contact_qry = $conn->query("SELECT * from `users` where id in ('{$contact_ids}') ");
        $contacts = "";
        while($crow = $contact_qry->fetch_assoc()):
            $contacts .= '<p class="m-0">' . $crow['firstname'] . ' ' . $crow['lastname'] . '</p>';
        endwhile;
    }
}
?>
<style>
    #uni_modal .modal-footer{
        display:none
    }
</style>
<div class="container fluid">
    <callout class="callout-primary">
        <dl class="row">
            <dt class="col-md-4">Project Name</dt>
            <dd class="col-md-8">: <?php echo $name ?></dd>
            <dt class="col-md-4">Description</dt>
            <dd class="col-md-8">: <?php echo $description ?></dd>
            <dt class="col-md-4">Address</dt>
            <dd class="col-md-8">: <?php echo $address ?></dd>
            <dt class="col-md-4">Contact</dt>
            <dd class="col-md-8"><?php echo $contacts ?></dd>
        </dl>
    </callout>
    <div class="row px-2 justify-content-end">
        <div class="col-1">
            <button class="btn btn-dark btn-flat btn-sm" type="button" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>