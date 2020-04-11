<?php

require_once $_SERVER['DOCUMENT_ROOT'] . "/lpgp-server/core/Core.php";

use Core\ClientsData;

if(isset($_GET['client'])){
	$cl_id = base64_decode($_GET['client']);
	$obj = new ClientsData("giulliano_php", "");
	$cl_dt = $obj->getClientData((int)$cl_id);
	$name_ip = '<input type="text" name="client-name" id="cl-nm" class="form-control" value="' . $cl_dt['nm_client'] .'">';
	$tk_ip = '<input type="password" name="client-tk" id="cl-tk" class="form-control" value="' . $cl_dt['tk_client'] . '" readonly>';
	$sel = '<select class="form-control" id="permissions-sel">';
	$opt_root = '<option value="1" ' . $cl_dt['vl_root'] == 1 ? "selected" : "" . '></option>';
	$opt_nor = '<option value="1" ' . $cl_dt['vl_root'] == 0 ? "selected" : "" . '></option>';
	$sel .= $opt_root . $opt_nor . "</select>";
	
}