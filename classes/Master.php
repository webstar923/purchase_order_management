<?php
require_once('../config.php');
Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	function capture_err(){
		if(!$this->conn->error)
			return false;
		else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}
    function save_project(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id','description','address'))){
				if(!empty($data)) $data .=",";
				if ($k == 'contact') {
					$contact = implode(',', $v);
					$data .= " `{$k}`='{$contact}' ";
				} else {
					$data .= " `{$k}`='{$v}' ";
				}
			}
		}
		if(isset($_POST['description'])){
			if(!empty($data)) $data .=",";
				$data .= " `description`='".addslashes(htmlentities($description))."' ";
		}
		if(isset($_POST['address'])){
			if(!empty($data)) $data .=",";
				$data .= " `address`='".addslashes(htmlentities($address))."' ";
		}
		$check = $this->conn->query("SELECT * FROM `project_list` where `project_no` = '{$project_no}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Project ID already exist.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `project_list` set {$data} ";
		}else{
			$sql = "UPDATE `project_list` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';

			if ($this->conn->insert_id && $_POST['project_no'] === '') {
				$project_no = str_pad($this->conn->insert_id, 4, '0', STR_PAD_LEFT);
				$this->conn->query("UPDATE `project_list` set `project_no` = '{$project_no}' where id = '{$this->conn->insert_id}' ");
			}

			if(empty($id))
				$this->settings->set_flashdata('success',"New project successfully saved.");
			else
				$this->settings->set_flashdata('success',"project successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_project(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `project_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"project successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_supplier(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id'))){
				$v = addslashes(trim($v));
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `supplier_list` where `name` = '{$name}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Supplier already exist.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `supplier_list` set {$data} ";
			$save = $this->conn->query($sql);
		}else{
			$sql = "UPDATE `supplier_list` set {$data} where id = '{$id}' ";
			$save = $this->conn->query($sql);
		}
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New Supplier successfully saved.");
			else
				$this->settings->set_flashdata('success',"Supplier successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_supplier(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `supplier_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Supplier successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_costcode(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id','description'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(isset($_POST['description'])){
			if(!empty($data)) $data .=",";
				$data .= " `description`='".addslashes(htmlentities($description))."' ";
		}
		$check = $this->conn->query("SELECT * FROM `costcode_list` where `name` = '{$name}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Costcode Name already exist.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `costcode_list` set {$data} ";
		}else{
			$sql = "UPDATE `costcode_list` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New costcode successfully saved.");
			else
				$this->settings->set_flashdata('success',"costcode successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_costcode(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `costcode_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"costcode successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_category(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id','description'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `category_list` where `name` = '{$name}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Category Name already exist.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `category_list` set {$data} ";
		}else{
			$sql = "UPDATE `category_list` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New category successfully saved.");
			else
				$this->settings->set_flashdata('success',"category successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_category(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `category_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"category successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_uom(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id','description'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `uom_list` where `name` = '{$name}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Unit of measure already exist.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `uom_list` set {$data} ";
		}else{
			$sql = "UPDATE `uom_list` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New UOM successfully saved.");
			else
				$this->settings->set_flashdata('success',"UOM successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_uom(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `uom_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"UOM successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_item(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id','description'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(isset($_POST['description'])){
			if(!empty($data)) $data .=",";
				$data .= " `description`='".addslashes(htmlentities($description))."' ";
		}
		$check = $this->conn->query("SELECT * FROM `item_list` where `code` = '{$code}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Item Code already exist.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `item_list` set {$data} ";
		}else{
			$sql = "UPDATE `item_list` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New item successfully saved.");
			else
				$this->settings->set_flashdata('success',"item successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_item(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `item_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"item successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function search_items(){
		extract($_POST);
		$qry = $this->conn->query("SELECT * FROM item_list where `name` LIKE '%{$q}%'");
		$data = array();
		while($row = $qry->fetch_assoc()){
			$data[] = array("label"=>$row['name'],"id"=>$row['id'],"description"=>$row['description']);
		}
		return json_encode($data);
	}
	function save_supplier_catalog(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id','description'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `sc_list` set {$data} ";
		}else{
			$sql = "UPDATE `sc_list` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New supplier catalog successfully saved.");
			else
				$this->settings->set_flashdata('success',"supplier catalog successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_supplier_catalog(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `sc_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"supplier catalog successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
    function save_taxcode(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id','description'))){
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		$check = $this->conn->query("SELECT * FROM `taxcode_list` where `code` = '{$code}' ".(!empty($id) ? " and id != {$id} " : "")." ")->num_rows;
		if($this->capture_err())
			return $this->capture_err();
		if($check > 0){
			$resp['status'] = 'failed';
			$resp['msg'] = "Tax code already exist.";
			return json_encode($resp);
			exit;
		}
		if(empty($id)){
			$sql = "INSERT INTO `taxcode_list` set {$data} ";
		}else{
			$sql = "UPDATE `taxcode_list` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New taxcode successfully saved.");
			else
				$this->settings->set_flashdata('success',"taxcode successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_taxcode(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `taxcode_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"taxcode successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function save_po(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(in_array($k,array('discount_amount','tax_amount')))
				$v= str_replace(',','',$v);
			if(!in_array($k,array('id','po_no','project_no')) && !is_array($_POST[$k])){
				$v = addslashes(trim($v));
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}
		if(!empty($po_no)){
			$check = $this->conn->query("SELECT * FROM `po_list` where `po_no` = '{$po_no}' ".($id > 0 ? " and id != '{$id}' ":""))->num_rows;
			if($this->capture_err())
				return $this->capture_err();
			if($check > 0){
				$resp['status'] = 'po_failed';
				$resp['msg'] = "Purchase Order Number already exist.";
				return json_encode($resp);
				exit;
			}
		}else{
			$po_no = "";
			$project_no_like = $_POST['project_no']."-";
			$qry = $this->conn->query("SELECT * FROM `po_list` where `po_no` REGEXP '{$project_no_like}*[0-9]+' order by `date_created` desc limit 1");
			if ($qry->num_rows) {
				while($row = $qry->fetch_assoc()){
					$last_po_no = $row['po_no'];
					$project_po_no = explode('-', $last_po_no);
					$last_po_no = intval(end($project_po_no));
					$po_no = $project_no_like . str_pad($last_po_no + 1, 4, '0', STR_PAD_LEFT);
				}
			} else {
				$po_no = $project_no_like . '0001';
			}
		}
		$data .= ", po_no = '{$po_no}' ";

		if(empty($id)){
			$sql = "INSERT INTO `po_list` set {$data} ";
		}else{
			$sql = "UPDATE `po_list` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			$po_id = empty($id) ? $this->conn->insert_id : $id ;
			$resp['id'] = $po_id;
			$data = "";
			foreach($item_id as $k =>$v){
				if(!empty($data)) $data .=",";
				$data .= "('{$po_id}','{$v}','{$unit[$k]}','{$costcode[$k]}','{$unit_price[$k]}','{$qty[$k]}','{$taxcode_id[$k]}')";
			}
			if(!empty($data)){
				$this->conn->query("DELETE FROM `order_items` where po_id = '{$po_id}'");
				$save = $this->conn->query("INSERT INTO `order_items` (`po_id`,`item_id`,`unit`,`costcode`,`unit_price`,`quantity`,`taxcode_id`) VALUES {$data} ");
			}
			if(empty($id))
				$this->settings->set_flashdata('success',"Purchase Order successfully saved.");
			else
				$this->settings->set_flashdata('success',"Purchase Order successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_po(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `po_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Purchase Order successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function search_po(){
		extract($_POST);
		$qry = $this->conn->query("SELECT * FROM po_list where id = '{$id}'");
		$data = array();
		while($row = $qry->fetch_assoc()){
			$data = $row;
		}

		$order_items = [];
		$order_items_qry = $this->conn->query("SELECT o.*,i.name FROM `order_items` o inner join item_list i on o.item_id = i.id where o.`po_id` = '$id' ");
		while($row = $order_items_qry->fetch_assoc()){
			$order_items[] = $row;
		}
		$data['order_items'] = $order_items;
		return json_encode($data);
	}
	function save_ro(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(in_array($k,array('discount_amount','tax_amount')))
				$v= str_replace(',','',$v);
			if(!in_array($k,array('id','ro_no','po_no')) && !is_array($_POST[$k])){
				$v = addslashes(trim($v));
				if(!empty($data)) $data .=",";
				$data .= " `{$k}`='{$v}' ";
			}
		}		
		if(!empty($ro_no)){
			$check = $this->conn->query("SELECT * FROM `ro_list` where `ro_no` = '{$ro_no}' ".($id > 0 ? " and id != '{$id}' ":""))->num_rows;
			if($this->capture_err())
				return $this->capture_err();
			if($check > 0){
				$resp['status'] = 'ro_failed';
				$resp['msg'] = "Receive Order Number already exist.";
				return json_encode($resp);
				exit;
			}
		}else{
			$ro_no = "";
			$po_no_like = $_POST['po_no']."-";
			$qry = $this->conn->query("SELECT * FROM `ro_list` where `ro_no` REGEXP '{$po_no_like}*[0-9]+' order by `date_created` desc limit 1");
			if ($qry->num_rows) {
				while($row = $qry->fetch_assoc()){
					$last_ro_no = $row['ro_no'];
					$po_ro_no = explode('-', $last_ro_no);
					$last_ro_no = intval(end($po_ro_no));
					$ro_no = $po_no_like . str_pad($last_ro_no + 1, 4, '0', STR_PAD_LEFT);
				}
			} else {
				$ro_no = $po_no_like . '0001';
			}
		}
		$data .= ", ro_no = '{$ro_no}' ";
		
		if(empty($id)){
			$sql = "INSERT INTO `ro_list` set {$data} ";
		}else{
			$sql = "UPDATE `ro_list` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			$ro_id = empty($id) ? $this->conn->insert_id : $id ;
			$resp['id'] = $ro_id;
			$data = "";
			foreach($item_id as $k =>$v){
				if(!empty($data)) $data .=",";
				$data .= "('{$ro_id}','{$v}','{$qty[$k]}','{$received_qty[$k]}')";
			}
			if(!empty($data)){
				$this->conn->query("DELETE FROM `receive_order_items` where ro_id = '{$ro_id}'");
				$save = $this->conn->query("INSERT INTO `receive_order_items` (`ro_id`,`item_id`,`quantity`,`received_qty`) VALUES {$data} ");
			}
			if(empty($id))
				$this->settings->set_flashdata('success',"Receive Order successfully saved.");
			else
				$this->settings->set_flashdata('success',"Receive Order successfully updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_ro(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `ro_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Receive Order successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function get_price(){
		extract($_POST);
		 $qry = $this->conn->query("SELECT * FROM price_list where unit_id = '{$unit_id}'");
		 $this->capture_err();
		 if($qry->num_rows > 0){
			 $res = $qry->fetch_array();
			 switch($rent_type){
				 case '1':
					$resp['price'] = $res['monthly'];
					break;
				case '2':
					$resp['price'] = $res['quarterly'];
					break;
				case '3':
					$resp['price'] = $res['annually'];
					break;
			 }
		 }else{
			 $resp['price'] = "0";
		 }
		 return json_encode($resp);
	}
	function save_rent(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k =>$v){
			if(!in_array($k,array('id')) && !is_array($_POST[$k])){
				if(!empty($data)) $data .=",";
				$v = addslashes($v);
				$data .= " `{$k}`='{$v}' ";
			}
		}
		switch ($rent_type) {
			case 1:
				$data .= ", `date_end`='".date("Y-m-d",strtotime($date_rented.' +1 month'))."' ";
				break;
			
			case 2:
				$data .= ", `date_end`='".date("Y-m-d",strtotime($date_rented.' +3 month'))."' ";
				break;
			case 3:
				$data .= ", `date_end`='".date("Y-m-d",strtotime($date_rented.' +1 year'))."' ";
				break;
			default:
				# code...
				break;
		}
		if(empty($id)){
			$sql = "INSERT INTO `rent_list` set {$data} ";
		}else{
			$sql = "UPDATE `rent_list` set {$data} where id = '{$id}' ";
		}
		$save = $this->conn->query($sql);
		if($save){
			$resp['status'] = 'success';
			if(empty($id))
				$this->settings->set_flashdata('success',"New Rent successfully saved.");
			else
				$this->settings->set_flashdata('success',"Rent successfully updated.");
			$this->settings->conn->query("UPDATE `unit_list` set `status` = '{$status}' where id = '{$unit_id}'");
		}else{
			$resp['status'] = 'failed';
			$resp['err'] = $this->conn->error."[{$sql}]";
		}
		return json_encode($resp);
	}
	function delete_rent(){
		extract($_POST);
		$del = $this->conn->query("DELETE FROM `rent_list` where id = '{$id}'");
		if($del){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success',"Rent successfully deleted.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);

	}
	function delete_img(){
		extract($_POST);
		if(is_file($path)){
			if(unlink($path)){
				$resp['status'] = 'success';
			}else{
				$resp['status'] = 'failed';
				$resp['error'] = 'failed to delete '.$path;
			}
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = 'Unkown '.$path.' path';
		}
		return json_encode($resp);
	}
	function renew_rent(){
		extract($_POST);
		$qry = $this->conn->query("SELECT * FROM `rent_list` where id ='{$id}'");
		$res = $qry->fetch_array();
		switch ($res['rent_type']) {
			case 1:
				$date_end = " `date_end`='".date("Y-m-d",strtotime($res['date_end'].' +1 month'))."' ";
				break;
			case 2:
				$date_end = " `date_end`='".date("Y-m-d",strtotime($res['date_end'].' +3 month'))."' ";
				break;
			case 3:
				$date_end = " `date_end`='".date("Y-m-d",strtotime($res['date_end'].' +1 year'))."' ";
				break;
			default:
				# code...
				break;
		}
		$update = $this->conn->query("UPDATE `rent_list` set {$date_end}, date_rented = date_end where id = '{$id}' ");
		if($update){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Rent successfully renewed.");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'save_project':
		echo $Master->save_project();
	break;
	case 'delete_project':
		echo $Master->delete_project();
	break;
	case 'save_supplier':
		echo $Master->save_supplier();
	break;
	case 'delete_supplier':
		echo $Master->delete_supplier();
	break;
	case 'save_costcode':
		echo $Master->save_costcode();
	break;
	case 'delete_costcode':
		echo $Master->delete_costcode();
	break;
	case 'save_category':
		echo $Master->save_category();
	break;
	case 'delete_category':
		echo $Master->delete_category();
	break;
	case 'save_uom':
		echo $Master->save_uom();
	break;
	case 'delete_uom':
		echo $Master->delete_uom();
	break;
	case 'save_item':
		echo $Master->save_item();
	break;
	case 'delete_item':
		echo $Master->delete_item();
	break;
	case 'search_items':
		echo $Master->search_items();
	break;
	case 'save_supplier_catalog':
		echo $Master->save_supplier_catalog();
	break;
	case 'delete_supplier_catalog':
		echo $Master->delete_supplier_catalog();
	break;
	case 'save_taxcode':
		echo $Master->save_taxcode();
	break;
	case 'delete_taxcode':
		echo $Master->delete_taxcode();
	break;
	case 'save_po':
		echo $Master->save_po();
	break;
	case 'delete_po':
		echo $Master->delete_po();
	break;
	case 'search_po':
		echo $Master->search_po();
	break;
	case 'save_ro':
		echo $Master->save_ro();
	break;
	case 'delete_ro':
		echo $Master->delete_ro();
	break;
	case 'get_price':
		echo $Master->get_price();
		break;
	case 'save_rent':
		echo $Master->save_rent();
	break;
	case 'delete_rent':
		echo $Master->delete_rent();
	break;
	case 'renew_rent':
		echo $Master->renew_rent();
	break;
	
	default:
		// echo $sysset->index();
		break;
}