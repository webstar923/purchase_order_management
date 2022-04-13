<?php
require_once('../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query(
        "SELECT i.*, c.name as category, cc.name as costcode, u.name as uom 
        from `item_list` i 
        left join `category_list` c on c.id = i.category_id 
        left join `costcode_list` cc on cc.id = i.costcode_id 
        left join `uom_list` u on u.id = i.uom_id 
        where i.id = '{$_GET['id']}' "
    );
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=stripslashes($v);
        }
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
            <dt class="col-md-4">Item Code</dt>
            <dd class="col-md-8">: <?php echo $code ?></dd>
            <dt class="col-md-4">Item Name</dt>
            <dd class="col-md-8">: <?php echo $name ?></dd>
            <dt class="col-md-4">Category</dt>
            <dd class="col-md-8">: <?php echo $category ?></dd>
            <dt class="col-md-4">Costcode</dt>
            <dd class="col-md-8">: <?php echo $costcode ?></dd>
            <dt class="col-md-4">Unit of Measure</dt>
            <dd class="col-md-8">: <?php echo $uom ?></dd>
        </dl>
    </callout>
    <div class="row px-2 justify-content-end">
        <div class="col-1">
            <button class="btn btn-dark btn-flat btn-sm" type="button" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>