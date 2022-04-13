<?php
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `po_list` where id = '{$_GET['id']}' ");
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

	.item-select span.select2-selection.select2-selection--single {
        text-align: center;
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
		select.taxcode {
			width:8vw;
			display: inline-block;
			color: #000;
		}
		[name="tax_percentage"],[name="discount_percentage"]{
			width:5vw;
		}
</style>
<div class="card card-outline card-info">
	<div class="card-header">
		<h3 class="card-title"><?php echo isset($id) ? "Update Purchase Order Details": "New Purchase Order" ?> </h3>
	</div>
	<div class="card-body">
		<form action="" id="po-form">
			<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
			<div class="row">
				<div class="col-md-4 form-group">
					<label for="project_id">Project</label>
					<select name="project_id" id="project_id" class="custom-select custom-select-sm rounded-0 select2" onchange="selectProject(this)">
						<option value="" disabled <?php echo !isset($project_id) ? "selected" :'' ?>></option>
						<?php 
							$project_qry = $conn->query("SELECT * FROM `project_list` order by `name` asc");
							$projects = [];
							while($row = $project_qry->fetch_assoc()):
								$projects[] = $row;
						?>
						<option value="<?php echo $row['id'] ?>" <?php echo isset($project_id) && $project_id == $row['id'] ? 'selected' : '' ?>><?php echo $row['name'] ?></option>
						<?php endwhile; ?>
					</select>
				</div>
				<div class="col-md-4 form-group">
					<label for="supplier_id">Supplier</label>
					<select name="supplier_id" id="supplier_id" class="custom-select custom-select-sm rounded-0 select2">
						<option value="" disabled <?php echo !isset($supplier_id) ? "selected" :'' ?>></option>
						<?php 
							$supplier_qry = $conn->query("SELECT * FROM `supplier_list` order by `name` asc");
							while($row = $supplier_qry->fetch_assoc()):
						?>
						<option value="<?php echo $row['id'] ?>" <?php echo isset($supplier_id) && $supplier_id == $row['id'] ? 'selected' : '' ?> <?php echo $row['status'] == 0? 'disabled' : '' ?>><?php echo $row['name'] ?></option>
						<?php endwhile; ?>
					</select>
				</div>
				<div class="col-md-4 form-group">
					<label for="po_no">PO # <span class="po_err_msg text-danger"></span></label>
					<input type="text" class="form-control form-control-sm rounded-0" id="po_no" name="po_no" value="<?php echo isset($po_no) ? $po_no : '' ?>">
					<small><i>Leave this blank to Automatically Generate upon saving.</i></small>
				</div>
				<div class="col-md-8 form-group">
					<label for="delivery_address" class="control-label">Delivery Address</label>
					<textarea name="delivery_address" id="delivery_address" cols="10" rows="3" class="form-control form-control-sm rounded-0"><?php echo isset($delivery_address) ? $delivery_address : '' ?></textarea>
				</div>
				<div class="col-md-4 form-group">
					<label for="delivery_date" class="control-label">Delivery Date</label>
					<input type="date" name="delivery_date" id="delivery_date" class="form-control form-control-sm rounded-0" value="<?php echo isset($delivery_date) ? date("Y-m-d",strtotime($delivery_date)) :"" ?>" required>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<table class="table table-striped table-bordered" id="item-list">
						<colgroup>
							<col width="5%">
							<col width="5%">
							<col width="10%">
							<col width="20%">
							<col width="30%">
							<col width="30%">
						</colgroup>
						<thead>
							<tr class="bg-navy disabled">
								<th class="px-1 py-1 text-center"></th>
								<th class="px-1 py-1 text-center">Qty</th>
								<th class="px-1 py-1 text-center">Unit</th>
								<th class="px-1 py-1 text-center">Item</th>
								<th class="px-1 py-1 text-center">Price</th>
								<th class="px-1 py-1 text-center">Total</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							if(isset($id)):
							$order_items_qry = $conn->query("SELECT o.*,i.name FROM `order_items` o inner join item_list i on o.item_id = i.id where o.`po_id` = '$id' ");
							echo $conn->error;
							$i = 1;
							while($row = $order_items_qry->fetch_assoc()):
							?>
							<tr class="po-item" data-id="">
								<td class="align-middle p-1 text-center">
									<button class="btn btn-sm btn-danger py-0" type="button" onclick="rem_item($(this))"><i class="fa fa-times"></i></button>
								</td>
								<td class="align-middle p-0 text-center">
									<input type="number" class="text-center w-100 border-0" step="any" name="qty[]" value="<?php echo $row['quantity'] ?>"/>
								</td>
								<td class="align-middle p-1 item-unit">
									<input type="text" class="text-center w-100 border-0" name="unit[]" value="<?php echo $row['unit'] ?>"/>
								</td>
								<td class="align-middle p-1 item-select">
									<select name="item_id[]" id="<?php echo "itemselect".$i++ ?>" class="custom-select custom-select-sm rounded-0 select2" onchange="selectItem(this)">
										<option value="" disabled <?php echo !isset($row['name']) ? "selected" :'' ?>></option>
										<?php 
											$item_qry = $conn->query("SELECT * FROM `item_list` order by `name` asc");
											while($irow = $item_qry->fetch_assoc()):
										?>
										<option value="<?php echo $irow['id'] ?>" <?php echo isset($row['item_id']) && $row['item_id'] === $irow['id'] ? "selected" :'' ?>>
											<?php echo $irow['name'] ?>
										</option>
										<?php endwhile; ?>
									</select>
								</td>
								<td class="align-middle p-1 unit-price">
									<input type="number" step="any" class="text-right w-100 border-0" name="unit_price[]"  value="<?php echo ($row['unit_price']) ?>"/>
								</td>
								<td class="align-middle p-1 text-right total-price"><?php echo number_format($row['quantity'] * $row['unit_price']) ?></td>
							</tr>
							<?php endwhile;endif; ?>
						</tbody>
						<tfoot>
							<tr class="bg-lightblue">
								<tr>
									<th class="p-1 text-right" colspan="5"><span><button class="btn btn btn-sm btn-flat btn-primary py-0 mx-1" type="button" id="add_row">Add Row</button></span> Sub Total</th>
									<th class="p-1 text-right" id="sub_total">0</th>
								</tr>
								<tr>
									<th class="p-1 text-right" colspan="5">Discount (%)
									<input type="number" step="any" name="discount_percentage" class="border-light text-right" value="<?php echo isset($discount_percentage) ? $discount_percentage : 0 ?>">
									</th>
									<th class="p-1"><input type="text" class="w-100 border-0 text-right" readonly value="<?php echo isset($discount_amount) ? $discount_amount : 0 ?>" name="discount_amount"></th>
								</tr>
								<tr>
									<th class="p-1 text-right" colspan="5">Tax Inclusive (%)
										<select class="form-control form-control-sm rounded-0 taxcode" onchange="selectTaxcode(this)">
											<option value="" disabled selected>Select Taxcode</option>
											<?php 
												$taxcode_qry = $conn->query("SELECT * FROM `taxcode_list` order by `code` asc");
												while($row = $taxcode_qry->fetch_assoc()):
											?>
											<option value="<?php echo $row['percentage'] ?>"><?php echo $row['code'] ?></option>
											<?php endwhile; ?>
										</select>
										<input type="number" step="any" name="tax_percentage" class="border-light text-right" value="<?php echo isset($tax_percentage) ? $tax_percentage : 0 ?>">
									</th>
									<th class="p-1"><input type="text" class="w-100 border-0 text-right" readonly value="<?php echo isset($tax_amount) ? $tax_amount : 0 ?>" name="tax_amount"></th>
								</tr>
								<tr>
									<th class="p-1 text-right" colspan="5">Total</th>
									<th class="p-1 text-right" id="total">0</th>
								</tr>
							</tr>
						</tfoot>
					</table>
					<div class="row">
						<div class="col-md-6">
							<label for="notes" class="control-label">Notes</label>
							<textarea name="notes" id="notes" cols="10" rows="4" class="form-control rounded-0"><?php echo isset($notes) ? $notes : '' ?></textarea>
						</div>
						<div class="col-md-6">
							<label for="status" class="control-label">Status</label>
							<select name="status" id="status" class="form-control form-control-sm rounded-0">
								<option value="0" <?php echo isset($status) && $status == 0 ? 'selected': '' ?>>Pending</option>
								<option value="1" <?php echo isset($status) && $status == 1 ? 'selected': '' ?>>Approved</option>
								<option value="2" <?php echo isset($status) && $status == 2 ? 'selected': '' ?>>Denied</option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
	<div class="card-footer">
		<button class="btn btn-flat btn-primary" form="po-form">Save</button>
		<a class="btn btn-flat btn-default" href="?page=purchase_orders">Cancel</a>
	</div>
</div>
<table class="d-none" id="item-clone">
	<tr class="po-item" data-id="">
		<td class="align-middle p-1 text-center">
			<button class="btn btn-sm btn-danger py-0" type="button" onclick="rem_item($(this))"><i class="fa fa-times"></i></button>
		</td>
		<td class="align-middle p-0 text-center">
			<input type="number" class="text-center w-100 border-0" value="1" step="any" name="qty[]"/>
		</td>
		<td class="align-middle p-1 item-unit">
			<input type="text" class="text-center w-100 border-0" name="unit[]"/>
		</td>
		<td class="align-middle p-1 item-select">
			<select name="item_id[]" class="custom-select custom-select-sm rounded-0 item-select2" onchange="selectItem(this)">
                <option value="" disabled selected></option>
                <?php 
					$item_qry = $conn->query("SELECT * FROM `item_list` order by `name` asc");
                    while($row = $item_qry->fetch_assoc()):
                ?>
                <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
                <?php endwhile; ?>
            </select>
		</td>
		<td class="align-middle p-1 unit-price">
			<input type="number" step="any" class="text-right w-100 border-0" name="unit_price[]" value="0"/>
		</td>
		<td class="align-middle p-1 text-right total-price">0</td>
	</tr>
</table>
<script>
	var projects = <?php echo json_encode($projects); ?>;

	var items = <?php
		$item_qry = $conn->query("SELECT * FROM `item_list` order by `name` asc");
		$items = [];
		while($row = $item_qry->fetch_assoc()) {
			$items[] = $row;
		}
		echo json_encode($items); 
	?>;


	var units = <?php
		$unit_qry = $conn->query("SELECT * FROM `uom_list` order by `name` asc");
		$units = [];
		while($row = $unit_qry->fetch_assoc()) {
			$units[] = $row;
		}
		echo json_encode($units);
	?>;

	var catalogs = <?php
		$catalog_qry = $conn->query("SELECT * FROM `sc_list`");
		$catalogs = [];
		while($row = $catalog_qry->fetch_assoc()) {
			$catalogs[] = $row;
		}
		echo json_encode($catalogs);
	?>;

	function rem_item(_this){
		_this.closest('tr').remove()
	}
	function calculate(){
		var _total = 0
		$('.po-item').each(function(){
			var qty = $(this).find("[name='qty[]']").val()
			var unit_price = $(this).find("[name='unit_price[]']").val()
			var row_total = 0;
			if(qty > 0 && unit_price > 0){
				row_total = parseFloat(qty) * parseFloat(unit_price)
			}
			$(this).find('.total-price').text(parseFloat(row_total).toLocaleString('en-US'))
		})
		$('.total-price').each(function(){
			var _price = $(this).text()
				_price = _price.replace(/\,/gi,'')
				_total += parseFloat(_price)
		})
		var discount_perc = 0
		if($('[name="discount_percentage"]').val() > 0){
			discount_perc = $('[name="discount_percentage"]').val()
		}
		var discount_amount = _total * (discount_perc/100);
		$('[name="discount_amount"]').val(parseFloat(discount_amount).toLocaleString("en-US"))
		var tax_perc = 0
		if($('[name="tax_percentage"]').val() > 0){
			tax_perc = $('[name="tax_percentage"]').val()
		}
		var tax_amount = _total * (tax_perc/100);
		$('[name="tax_amount"]').val(parseFloat(tax_amount).toLocaleString("en-US"))
		$('#sub_total').text(parseFloat(_total).toLocaleString("en-US"))
		$('#total').text(parseFloat(_total-discount_amount).toLocaleString("en-US"))
	}

	$(document).ready(function(){
		$('#add_row').click(function(){
			var tr = $('#item-clone tr').clone()
			$('#item-list tbody').append(tr)
			tr.find('[name="qty[]"],[name="unit_price[]"]').on('input keypress',function(e){
				calculate()
			})
			$('#item-list tfoot').find('[name="discount_percentage"],[name="tax_percentage"]').on('input keypress',function(e){
				calculate()
			})

			setTimeout(() => {
				tr.find('.item-select2').select2({placeholder:"Please Select here",width:"relative"})
			}, 500);
		})

		if($('#item-list .po-item').length > 0){
			$('#item-list .po-item').each(function(){
				var tr = $(this)
				tr.find('[name="qty[]"],[name="unit_price[]"]').on('input keypress',function(e){
					calculate()
				})
				$('#item-list tfoot').find('[name="discount_percentage"],[name="tax_percentage"]').on('input keypress',function(e){
					calculate()
				})
				tr.find('[name="qty[]"],[name="unit_price[]"]').trigger('keypress')
			})
		}else{
			$('#add_row').trigger('click')
		}

        $('.select2').select2({placeholder:"Please Select here",width:"relative"})

		$('#po-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			$('.err-msg').remove();
			$('[name="po_no"]').removeClass('border-danger')
			if($('#item-list .po-item').length <= 0){
				alert_toast(" Please add atleast 1 item on the list.",'warning')
				return false;
			}
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_po",
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
						location.href = "./?page=purchase_orders/view_po&id="+resp.id;
					}else if((resp.status == 'failed' || resp.status == 'po_failed') && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            $("html, body").animate({ scrollTop: 0 }, "fast");
                            end_loader()
							if(resp.status == 'po_failed'){
								$('[name="po_no"]').addClass('border-danger').focus()
							}
                    }else{
						alert_toast("An error occured",'error');
						end_loader();
                        console.log(resp)
					}
				}
			})
		})
	})

	function selectProject(el) {
		let project = projects.find(p => p.id === $(el).val())
		$('#delivery_address').val(project.address);
		$('#notes').val(project.description);
	}

	function selectItem(el) {
		var item = items.find(i => i.id === $(el).val())
		var unit = units.find(u => u.id === item.uom_id)
		$(el).closest('tr').find('.item-unit input').val(unit.name)

		var catalog = catalogs.find(c => c.item_id === item.id && c.supplier_id === $('#supplier_id').val());
		if (catalog) {
			$(el).closest('tr').find('.unit-price input').val(catalog.price)
			calculate()
		} else {
			$(el).closest('tr').find('.unit-price input').val(0)
			calculate()
		}
	}

	function selectTaxcode(el) {
		$(el).closest('tr').find('input[name=tax_percentage]').val($(el).val());
		calculate()
	}
</script>