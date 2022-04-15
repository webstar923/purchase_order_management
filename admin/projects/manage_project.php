<?php
require_once('../../config.php');
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `project_list` where id = '{$_GET['id']}' ");
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
<form action="" id="project-form">
     <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
    <div class="container-fluid">
        <div class="form-group">
            <label for="project_no">Project ID <span class="po_err_msg text-danger"></span></label>
            <input type="text" class="form-control rounded-0" id="project_no" name="project_no" value="<?php echo isset($project_no) ? $project_no : '' ?>">
            <small><i>Leave this blank to Automatically Generate upon saving.</i></small>
        </div>
        <div class="form-group">
            <label for="name" class="control-label">Project Name</label>
            <input type="text" name="name" id="name" class="form-control rounded-0" value="<?php echo isset($name) ? $name :"" ?>" required>
        </div>
        <div class="form-group">
            <label for="description" class="control-label">Description</label>
            <textarea rows="3" name="description" id="description" class="form-control rounded-0" required><?php echo isset($description) ? $description :"" ?></textarea>
        </div>
        <div class="form-group">
            <label for="address" class="control-label">Address</label>
            <textarea rows="3" name="address" id="address" class="form-control rounded-0" required><?php echo isset($address) ? $address :"" ?></textarea>
        </div>
        <div class="form-group">
            <label for="status" class="control-label">Contact</label>
            <select name="contact[]" id="contact" class="form-control rounded-0 select2" multiple>
                <option disabled>Select Contact</option>
                <?php 
                    $user_qry = $conn->query("SELECT * FROM `users`");
                    while($row = $user_qry->fetch_assoc()):
                ?>
                <option value="<?php echo $row['id'] ?>" <?php echo isset($contact) && in_array($row['id'], explode(',', $contact)) ? 'selected' : '' ?>><?php echo $row['firstname'].$row['lastname'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
    </div>
</form>
<script>
    $(function(){
        $('.select2').select2({placeholder:"Please Select here",width:"relative"})

        $('#project-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_project",
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