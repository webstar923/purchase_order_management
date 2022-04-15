<?php
require_once('../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `sc_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=stripslashes($v);
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
</style>
<form action="" id="sc-form">
     <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
    <div class="container-fluid">
        <div class="form-group">
            <label for="item_id" class="control-label">Item</label>
            <select name="item_id" id="item_id" class="custom-select rounded-0 select2">
                <option value="" disabled <?php echo !isset($item_id) ? "selected" :'' ?>>Select Item</option>
                <?php 
                    $item_qry = $conn->query("SELECT * FROM `item_list` order by `name` asc");
                    while($row = $item_qry->fetch_assoc()):
                ?>
                <option value="<?php echo $row['id'] ?>" <?php echo isset($item_id) && $item_id == $row['id'] ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="supplier_id" class="control-label">Supplier</label>
            <select name="supplier_id" id="supplier_id" class="custom-select rounded-0 select2">
                <option value="" disabled <?php echo !isset($supplier_id) ? "selected" :'' ?>>Select Supplier</option>
                <?php 
                    $supplier_qry = $conn->query("SELECT * FROM `supplier_list` order by `name` asc");
                    while($row = $supplier_qry->fetch_assoc()):
                ?>
                <option value="<?php echo $row['id'] ?>" <?php echo isset($supplier_id) && $supplier_id == $row['id'] ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="sku" class="control-label">SKU</label>
            <input type="text" name="sku" id="sku" class="form-control rounded-0" value="<?php echo isset($sku) ? $sku :"" ?>" required>
        </div>
        <div class="form-group">
            <label for="price" class="control-label">Price</label>
            <input type="number" name="price" step="0.01" id="price" class="form-control rounded-0" value="<?php echo isset($price) ? $price :"0" ?>" required>
        </div>
        <div class="form-group">
            <label for="price_expiry_date" class="control-label">Price Expiry Date</label>
            <input type="date" name="price_expiry_date" id="price_expiry_date" class="form-control rounded-0" value="<?php echo isset($price_expiry_date) ? date("Y-m-d",strtotime($price_expiry_date)) :"" ?>" required>
        </div>
    </div>
</form>
<script>
    $(function(){
        $('#sc-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_supplier_catalog",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					alert_toast("An error occured",'error');
					end_loader();
				},
				success:function(resp){
					if(typeof resp =='object' && resp.status == 'success'){
						location.reload();
					}else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            $("html, body").animate({ scrollTop: 0 }, "fast");
                    }else{
						alert_toast("An error occured",'error');
                        console.log(resp)
					}
                    end_loader()
				}
			})
		})
	})
</script>