<?php
if (isset($_GET['id']) && $_GET['id'] > 0) {
	$qry = $conn->query(
		"SELECT ro.*, po.project_id, po.supplier_id, po.delivery_address, po.delivery_date, po.notes 
		from `ro_list` ro
		left join `po_list` po on po.id = ro.po_id 
		where ro.id = '{$_GET['id']}' "
	);
	if ($qry->num_rows > 0) {
		foreach ($qry->fetch_assoc() as $k => $v) {
			$$k = $v;
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
		width: 8vw;
		display: inline-block;
		color: #000;
	}

	[name="tax_percentage"],
	[name="discount_percentage"] {
		width: 5vw;
	}
</style>
<div class="card card-outline card-info">
	<div class="card-header">
		<h3 class="card-title"><?php echo isset($id) ? "Update Receive Order Details" : "New Receive Order" ?> </h3>
	</div>
	<div class="card-body">
		<form action="" id="ro-form">
			<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
			<div class="row">
				<div class="col-md-6 form-group">
					<label for="po_id">PO #</span></label>
					<select name="po_id" id="po_id" class="custom-select custom-select-sm rounded-0 select2" onchange="selectPurchaseOrder(this)">
						<option value="" disabled <?php echo !isset($po_id) ? "selected" : '' ?>></option>
						<?php
						$po_qry = $conn->query("SELECT * FROM `po_list` order by `date_created` asc");
						while ($row = $po_qry->fetch_assoc()) :
						?>
							<option value="<?php echo $row['id'] ?>" <?php echo isset($po_id) && $po_id == $row['id'] ? 'selected' : '' ?>><?php echo $row['po_no'] ?></option>
						<?php endwhile; ?>
					</select>
					<input type="hidden" name="po_no" id="po_no">
				</div>
				<div class="col-md-6 form-group">
					<label for="ro_no">RO # <span class="po_err_msg text-danger"></span></label>
					<input type="text" class="form-control form-control-sm rounded-0" id="ro_no" name="ro_no" placeholder="dddd-dddd-dddd" value="<?php echo isset($ro_no) ? $ro_no : '' ?>">
					<small><i>Leave this blank to Automatically Generate upon saving.</i></small>
					<div class="validation invalid-feedback"></div>
				</div>
				<div class="col-md-4 form-group">
					<label for="project_id">Project</label>
					<input type="text" class="form-control form-control-sm rounded-0" id="project_id" value="<?php echo isset($project_id) ? $project_id : '' ?>" readonly>
				</div>
				<div class="col-md-4 form-group">
					<label for="supplier_id">Supplier</label>
					<input type="text" class="form-control form-control-sm rounded-0" id="supplier_id" value="<?php echo isset($supplier_id) ? $supplier_id : '' ?>" readonly>
				</div>
				<div class="col-md-4 form-group">
					<label for="packing_slip_no">Packing Slip Number</label>
					<input type="text" class="form-control form-control-sm rounded-0" id="packing_slip_no" name="packing_slip_no" value="<?php echo isset($packing_slip_no) ? $packing_slip_no : '' ?>">
				</div>
				<div class="col-md-8 form-group">
					<label for="delivery_address" class="control-label">Delivery Address</label>
					<textarea id="delivery_address" cols="10" rows="3" class="form-control form-control-sm rounded-0" readonly><?php echo isset($delivery_address) ? $delivery_address : '' ?></textarea>
				</div>
				<div class="col-md-4 form-group">
					<label for="delivery_date" class="control-label">Delivery Date</label>
					<input type="date" id="delivery_date" class="form-control form-control-sm rounded-0" readonly value="<?php echo isset($delivery_date) ? date("Y-m-d", strtotime($delivery_date)) : "" ?>">
				</div>
				<div class="col-md-6 form-group">
					<label for="notes" class="control-label">Description</label>
					<textarea id="notes" cols="10" rows="4" class="form-control rounded-0" readonly><?php echo isset($notes) ? $notes : '' ?></textarea>
				</div>
				<div class="col-md-6 form-group">
					<label for="status" class="control-label">Status</label>
					<input type="text" class="form-control form-control-sm rounded-0" id="status" readonly>
					<input type="hidden" name="status" value="<?php echo isset($status) ? $status : ''; ?>">
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
							if (isset($id)) :
								$order_items_qry = $conn->query(
									"SELECT o.*,i.name,i.code,u.name as unit
								FROM `receive_order_items` o 
								inner join item_list i on o.item_id = i.id 
								inner join uom_list u on i.uom_id = u.id 
								where o.`ro_id` = '$id' "
								);
								echo $conn->error;
								$i = 1;
								while ($row = $order_items_qry->fetch_assoc()) :
							?>
									<tr class="po-item" data-id="">
										<td class="align-middle p-0 text-center">
											<input type="hidden" name="qty[]" value="<?php echo $row['quantity'] ?>" />
											<?php echo $row['quantity'] ?>
										</td>
										<td class="align-middle p-0 text-center">
											<input type="number" class="text-center w-100 border-0" step="1" min="0" max="<?php echo $row['quantity'] ?>" name="received_qty[]" value="<?php echo $row['received_qty'] ?>" />
										</td>
										<td class="align-middle p-1 text-center item-unit"><?php echo $row['unit'] ?></td>
										<td class="align-middle p-1 text-center item-select">
											<?php echo $row['name'] ?>
											<input type="hidden" name="item_id[]" value="<?php echo $row['item_id'] ?>" />
										</td>
										<td class="align-middle p-1 text-center item-select"><?php echo $row['code'] ?></td>
									</tr>
							<?php endwhile;
							endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</form>
	</div>
	<div class="card-footer">
		<button class="btn btn-flat btn-primary" form="ro-form">Save</button>
		<a class="btn btn-flat btn-default" href="?page=receive_orders">Cancel</a>
	</div>
</div>

<script>
	var purchase_orders = <?php
									$project_qry = $conn->query("SELECT * FROM `po_list`");
									$purchase_orders = [];
									while ($row = $project_qry->fetch_assoc()) {
										$purchase_orders[] = $row;
									}
									echo json_encode($purchase_orders);
									?>;

	var items = <?php
					$item_qry = $conn->query("SELECT * FROM `item_list` order by `name` asc");
					$items = [];
					while ($row = $item_qry->fetch_assoc()) {
						$items[] = $row;
					}
					echo json_encode($items);
					?>;

	var units = <?php
					$unit_qry = $conn->query("SELECT * FROM `uom_list` order by `name` asc");
					$units = [];
					while ($row = $unit_qry->fetch_assoc()) {
						$units[] = $row;
					}
					echo json_encode($units);
					?>;

	var catalogs = <?php
						$catalog_qry = $conn->query("SELECT * FROM `sc_list`");
						$catalogs = [];
						while ($row = $catalog_qry->fetch_assoc()) {
							$catalogs[] = $row;
						}
						echo json_encode($catalogs);
						?>;
	var is_valid_ro = true

	function initInputNumber() {
		$('input[type="number"]').on('keyup', function() {
			v = parseInt($(this).val());
			min = parseInt($(this).attr('min'));
			max = parseInt($(this).attr('max'));

			if (v < min) {
				$(this).val(min);
			} else if (v > max) {
				$(this).val(max);
			} else {
				$(this).val(v);
			}

			checkStatus();
		})
	}

	function checkStatus() {
		let status = 1;
		let qty_sum = 0;
		$('.po-item').each(function() {
			let td = $(this).find('input[type="number"]');
			v = parseInt(td.val());
			max = parseInt(td.attr('max'));
			if (v !== max) {
				status = 0;
			}
			qty_sum += v;
		});

		if (qty_sum == 0) {
			$('input[name="status"]').val(0);
			$('#status').val('Not received');
		} else if (status == 0) {
			$('input[name="status"]').val(1);
			$('#status').val('Partially received');
		} else {
			$('input[name="status"]').val(2);
			$('#status').val('Fully received');
		}
	}

	$(document).ready(function() {
		$('.select2').select2({
			placeholder: "Please Select here",
			width: "relative"
		})

		function validate_ro() {
			const vv = $("#ro_no").val()
			is_valid_ro = true
			let message = ''
			if (!vv) {
				is_valid_ro = true
			} else {
				const ro_arr = vv.split('-')
				if (ro_arr.length < 3) is_valid_ro = false
				else {
					const pn = ro_arr[0] + '-' + ro_arr[1]
					if ($('#po_no').val() != pn) {
						is_valid_ro = false
						message = pn + "- is different with #PO"
					} else if (!parseInt(ro_arr[2])) {
						message = ro_arr[2] + " is invalid. Last parts must be digits and greeter than 0"
						is_valid_ro = false
					}
				}
			}
			if (!is_valid_ro) {
				console.log(message, 'message');
				$("#ro_no").addClass("is-invalid")
				$("#ro_no~.validation").html(message)
			} else $("#ro_no").removeClass("is-invalid")
			return is_valid_ro
		}

		$("#ro_no").on("change", function() {
			validate_ro()
		});

		$('#ro-form').submit(function(e) {
			e.preventDefault();			
			if (!validate_ro()) return false
			var _this = $(this)
			$('.err-msg').remove();
			$('[name="po_no"]').removeClass('border-danger')
			if ($('#item-list .po-item').length <= 0) {
				alert_toast(" Please add atleast 1 item on the list.", 'warning')
				return false;
			}
			start_loader();
			$.ajax({
				url: _base_url_ + "classes/Master.php?f=save_ro",
				data: new FormData($(this)[0]),
				cache: false,
				contentType: false,
				processData: false,
				method: 'POST',
				type: 'POST',
				dataType: 'json',
				error: err => {
					console.log(err)
					alert_toast("An error occured", 'error');
					end_loader();
				},
				success: function(resp) {
					if (typeof resp == 'object' && resp.status == 'success') {
						location.href = "./?page=receive_orders/view_ro&id=" + resp.id;
					} else if ((resp.status == 'failed' || resp.status == 'po_failed') && !!resp.msg) {
						var el = $('<div>')
						el.addClass("alert alert-danger err-msg").text(resp.msg)
						_this.prepend(el)
						el.show('slow')
						$("html, body").animate({
							scrollTop: 0
						}, "fast");
						end_loader()
						if (resp.status == 'po_failed') {
							$('[name="po_no"]').addClass('border-danger').focus()
						}
					} else {
						alert_toast("An error occured", 'error');
						end_loader();
						console.log(resp)
					}
				}
			})
		})

		initInputNumber()
		checkStatus()
	})

	function selectPurchaseOrder(el) {
		const id = $(el).val()
		let purchase_order = purchase_orders.find(p => p.id === id);
		$('#po_no').val(purchase_order.po_no);
		$('#ro_no').attr("placeholder", purchase_order.po_no + "-dddd");


		$.ajax({
			url: _base_url_ + "classes/Master.php?f=search_po",
			method: 'POST',
			data: {
				id
			},
			dataType: 'json',
			error: err => {
				console.log(err)
			},
			success: function(resp) {
				updateOrderDetail(resp)
			}
		})
	}



	function updateOrderDetail(data) {
		const {
			project_id,
			supplier_id,
			delivery_address,
			delivery_date,
			order_items,
			discount_amount,
			discount_percentage,
			notes,
			tax_amount,
			tax_percentage
		} = data;

		$('#project_id').val(project_id);
		$('#supplier_id').val(supplier_id);
		$('#delivery_address').val(delivery_address);
		$('#delivery_date').val(moment(delivery_date).format("yyyy-MM-DD"));
		$('#notes').val(notes);
		$('#status').val('Not received');
		$('input[name=discount_percentage]').val(discount_percentage);
		$('input[name=discount_amount]').val(discount_amount);
		$('input[name=tax_percentage]').val(tax_percentage);
		$('input[name=tax_amount]').val(tax_amount);

		$('#item-list tbody').html('');

		order_items.forEach((order_item, index) => {
			let item = items.find(i => i.id === order_item.item_id);

			$('#item-list tbody').append(`
				<tr class="po-item" data-id="">
					<td class="align-middle p-0 text-center">
						<input type="hidden" name="qty[]" value="${order_item.quantity}"/>
						${order_item.quantity}
					</td>
					<td class="align-middle p-0 text-center">
						<input type="number" class="text-center w-100 border-0" step="1" min="0" max="${order_item.quantity}" name="received_qty[]" value="0"/>
					</td>
					<td class="align-middle p-1 text-center item-unit">${order_item.unit}</td>
					<td class="align-middle p-1 text-center item-select">
						${item.name}
						<input type="hidden" name="item_id[]" value="${item.id}"/>
					</td>
					<td class="align-middle p-1 text-center item-select">${item.code}</td>
				</tr>
			`)
		});

		initInputNumber()
	}
</script>