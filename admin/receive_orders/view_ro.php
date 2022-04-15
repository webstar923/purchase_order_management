<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<?php
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT ro.*, po.po_no from `ro_list` ro inner join `po_list` po on po.id = ro.po_id where ro.id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>
<style>
    span.select2-selection.select2-selection--single {
        border-radius: 0;
        padding: 0.25rem 0.5rem;
        padding-top: 0.25rem;
        padding-right: 0.5rem;
        padding-bottom: 0.25rem;
        padding-left: 0.5rem;
        height: auto;
    }
	/* Chrome, Safari, Edge, Opera */
		input::-webkit-outer-spin-button,
		input::-webkit-inner-spin-button {
		-webkit-appearance: none;
		margin: 0;
		}

		/* Firefox */
		input[type=number] {
		-moz-appearance: textfield;
		}
		[name="tax_percentage"],[name="discount_percentage"]{
			width:5vw;
		}
</style>
<div class="card card-outline card-info">
	<div class="card-header">
		<h3 class="card-title"><?php echo isset($id) ? "Update Receive Order Details": "New Receive Order" ?> </h3>
        <div class="card-tools">
            <button class="btn btn-sm btn-flat btn-success" id="print" type="button"><i class="fa fa-print"></i> Print</button>
		    <a class="btn btn-sm btn-flat btn-primary" href="?page=receive_orders/manage_ro&id=<?php echo $id ?>">Edit</a>
		    <a class="btn btn-sm btn-flat btn-default" href="?page=receive_orders">Back</a>
        </div>
	</div>
	<div class="card-body" id="out_print">
        <div class="row">
        <div class="col-8 d-flex align-items-center">
            <div>
                <p class="m-0"><?php echo $_settings->info('company_name') ?></p>
                <p class="m-0"><?php echo $_settings->info('company_email') ?></p>
                <p class="m-0"><?php echo $_settings->info('company_address') ?></p>
            </div>
        </div>
        <div class="col-4">
            <center><img src="<?php echo validate_image($_settings->info('logo')) ?>" alt="" height="200px"></center>
            <h2 class="text-center"><b>RECEIVE ORDER</b></h2>
        </div>
        </div>
        <div class="row mb-2">
            <div class="col-4">
                <p class="m-0"><b>Vendor</b></p>
                <?php 
                $sup_qry = $conn->query(
                    "SELECT s.*
                    FROM po_list po
                    inner join supplier_list s on s.id = po.supplier_id 
                    where po.id = '{$po_id}'"
                );
                $supplier = $sup_qry->fetch_array();
                ?>
                <div>
                    <p class="m-0"><?php echo $supplier['name'] ?></p>
                    <p class="m-0"><?php echo $supplier['address'] ?></p>
                    <p class="m-0"><?php echo $supplier['contact_person'] ?></p>
                    <p class="m-0"><?php echo $supplier['contact'] ?></p>
                    <p class="m-0"><?php echo $supplier['email'] ?></p>
                </div>
            </div>
            <div class="col-8 row">
                <div class="col-4">
                    <p  class="m-0"><b>R.O. #:</b></p>
                    <p><b><?php echo $ro_no ?></b></p>
                </div>
                <div class="col-4">
                    <p  class="m-0"><b>P.O. #:</b></p>
                    <p><b><?php echo $po_no ?></b></p>
                </div>
                <div class="col-4">
                    <p  class="m-0"><b>Date Created</b></p>
                    <p><b><?php echo date("Y-m-d",strtotime($date_created)) ?></b></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-bordered" id="item-list">
                    <colgroup>
                        <col width="10%">
                        <col width="10%">
                        <col width="20%">
                        <col width="30%">
                        <col width="30%">
                    </colgroup>
                    <thead>
                        <tr class="bg-navy disabled">
                            <th class="px-1 py-1 text-center">Qty</th>
                            <th class="px-1 py-1 text-center">Received Qty</th>
                            <th class="px-1 py-1 text-center">UOM</th>
                            <th class="px-1 py-1 text-center">Item Code</th>
                            <th class="px-1 py-1 text-center">Item Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if(isset($id)):
                        $order_items_qry = $conn->query(
                            "SELECT o.*,i.name,i.code,u.name as unit
                            FROM `receive_order_items` o 
                            inner join item_list i on o.item_id = i.id 
                            inner join uom_list u on i.uom_id = u.id 
                            where o.`ro_id` = '$id' "
                        );
                        echo $conn->error;
                        $i = 1;
                        while($row = $order_items_qry->fetch_assoc()):
                        ?>
                        <tr class="po-item" data-id="">
                            <td class="align-middle p-0 text-center">
                                <?php echo $row['quantity'] ?>
                            </td>
                            <td class="align-middle p-0 text-center">
                                <?php echo $row['received_qty'] ?>
                            </td>
                            <td class="align-middle p-1 text-center item-unit">
                                <?php echo $row['unit'] ?>
                            </td>
                            <td class="align-middle p-1 text-center item-select">
                                <?php echo $row['name'] ?>
                            </td>
                            <td class="align-middle p-1 text-center item-select">
                                <?php echo $row['code'] ?>
                            </td>
                        </tr>
                        <?php endwhile;endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
	</div>
</div>
<script>
	$(function(){
        $('#print').click(function(e){
            e.preventDefault();
            start_loader();
            var _h = $('head').clone()
            var _p = $('#out_print').clone()
            var _el = $('<div>')
                _p.find('thead th').attr('style','color:black !important')
                _el.append(_h)
                _el.append(_p)
                
            var nw = window.open("","","width=1200,height=950")
                nw.document.write(_el.html())
                nw.document.close()
                setTimeout(() => {
                    nw.print()
                    setTimeout(() => {
                        end_loader();
                        nw.close()
                    }, 300);
                }, 200);
        })
    })
</script>