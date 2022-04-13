<?php
require_once('../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `item_list` where id = '{$_GET['id']}' ");
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
<form action="" id="item-form">
     <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
    <div class="container-fluid">
        <div class="form-group">
            <label for="code" class="control-label">Item Code</label>
            <input type="text" name="code" id="code" class="form-control rounded-0" value="<?php echo isset($code) ? $code :"" ?>" required>
        </div>
        <div class="form-group">
            <label for="name" class="control-label">Item Name</label>
            <input type="text" name="name" id="name" class="form-control rounded-0" value="<?php echo isset($name) ? $name :"" ?>" required>
        </div>
        <div class="form-group">
            <label for="category_id" class="control-label">Category</label>
            <select name="category_id" id="category_id" class="custom-select rounded-0 select2">
                <option value="" disabled <?php echo !isset($category_id) ? "selected" :'' ?>>Select Category</option>
                <?php 
                    $category_qry = $conn->query("SELECT * FROM `category_list` order by `name` asc");
                    while($row = $category_qry->fetch_assoc()):
                ?>
                <option value="<?php echo $row['id'] ?>" <?php echo isset($category_id) && $category_id == $row['id'] ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="costcode_id" class="control-label">Costcode</label>
            <select name="costcode_id" id="costcode_id" class="custom-select rounded-0 select2">
                <option value="" disabled <?php echo !isset($costcode_id) ? "selected" :'' ?>>Select Costcode</option>
                <?php 
                    $costcode_qry = $conn->query("SELECT * FROM `costcode_list` order by `name` asc");
                    while($row = $costcode_qry->fetch_assoc()):
                ?>
                <option value="<?php echo $row['id'] ?>" <?php echo isset($costcode_id) && $costcode_id == $row['id'] ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="uom_id" class="control-label">Unit of Measure</label>
            <select name="uom_id" id="uom_id" class="custom-select rounded-0 select2">
                <option value="" disabled <?php echo !isset($uom_id) ? "selected" :'' ?>>Select UOM</option>
                <?php 
                    $uom_qry = $conn->query("SELECT * FROM `uom_list` order by `name` asc");
                    while($row = $uom_qry->fetch_assoc()):
                ?>
                <option value="<?php echo $row['id'] ?>" <?php echo isset($uom_id) && $uom_id == $row['id'] ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
    </div>
</form>
<script>
    $(function(){
        $('#item-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_item",
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