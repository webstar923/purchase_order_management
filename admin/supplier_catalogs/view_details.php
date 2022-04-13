<?php
require_once('../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query(
        "SELECT sc.*, i.code as code, i.name as item_name, cc.name as costcode, u.uom as uom, s.name as supplier 
        from `sc_list` sc 
        left join `item_list` i on i.id = sc.item_id 
        left join `costcode_list` cc on cc.id = i.costcode_id 
        left join `uom_list` u on u.id = i.uom_id 
        left join `supplier_list` s on s.id = sc.supplier_id 
        where sc.id = '{$_GET['id']}' "
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
            <dd class="col-md-8">: <?php echo $item_name ?></dd>
            <dt class="col-md-4">Supplier</dt>
            <dd class="col-md-8">: <?php echo $supplier ?></dd>
            <dt class="col-md-4">Costcode</dt>
            <dd class="col-md-8">: <?php echo $costcode ?></dd>
            <dt class="col-md-4">Unit of Measure</dt>
            <dd class="col-md-8">: <?php echo $uom ?></dd>
            <dt class="col-md-4">SKU</dt>
            <dd class="col-md-8">: <?php echo $sku ?></dd>
            <dt class="col-md-4">Price</dt>
            <dd class="col-md-8">: <?php echo $price ?></dd>
            <dt class="col-md-4">Price Expiry Date</dt>
            <dd class="col-md-8">: <?php echo date("Y-m-d",strtotime($price_expiry_date)) ?></dd>
        </dl>
    </callout>
    <div class="row px-2 justify-content-end">
        <div class="col-1">
            <button class="btn btn-dark btn-flat btn-sm" type="button" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>