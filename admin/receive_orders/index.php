<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline card-primary">
	<div class="card-header">
		<h3 class="card-title">List of Receive Orders</h3>
		<div class="card-tools">
			<a href="?page=receive_orders/manage_ro" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
        <div class="container-fluid">
			<table class="table table-hover table-striped">
				<colgroup>
					<col width="10%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr class="bg-navy disabled">
						<th>#</th>
						<th>Date Created</th>
						<th>RO #</th>
						<th>PO #</th>
						<th>Items</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$qry = $conn->query(
							"SELECT ro.*,po.po_no 
							FROM `ro_list` ro 
							inner join `po_list` po on ro.po_id = po.id 
							order by unix_timestamp(ro.date_updated) "
						);
						while($row = $qry->fetch_assoc()):
							$row['item_count'] = $conn->query("SELECT * FROM receive_order_items where ro_id = '{$row['id']}'")->num_rows;
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td class=""><?php echo date("M d,Y H:i",strtotime($row['date_created'])) ; ?></td>
							<td class=""><?php echo $row['ro_no'] ?></td>
							<td class=""><?php echo $row['po_no'] ?></td>
							<td><?php echo number_format($row['item_count']) ?></td>
							<td>
								<?php 
									switch ($row['status']) {
										case '1':
											echo '<span class="badge badge-warning">Partially received</span>';
											break;
										case '2':
											echo '<span class="badge badge-success">Fully received</span>';
											break;
										default:
											echo '<span class="badge badge-secondary">Not Received</span>';
											break;
									}
								?>
							</td>
							<td align="center">
								 <button type="button" class="btn btn-flat btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
								  	<a class="dropdown-item" href="?page=receive_orders/view_ro&id=<?php echo $row['id'] ?>"><span class="fa fa-eye text-primary"></span> View</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item" href="?page=receive_orders/manage_ro&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
				                  </div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this order permanently?","delete_ro",[$(this).attr('data-id')])
		})
		$('.table th,.table td').addClass('px-1 py-0 align-middle')
		$('.table').dataTable();
	})
	function delete_ro($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_ro",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>