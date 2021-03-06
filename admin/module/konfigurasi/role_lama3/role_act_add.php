<?php
     require_once("root.inc.php");
     require_once($ROOT."library/bitFunc.lib.php");
     require_once($ROOT."library/auth.cls.php");
     require_once($ROOT."library/textEncrypt.cls.php");
     require_once($ROOT."library/datamodel.cls.php");
     require_once($APLICATION_ROOT."library/view.cls.php");
     
     $dtaccess = new DataAccess();
     
     $enc = new textEncrypt();
     $auth = new CAuth();
     $err_code = 0;
     $view = new CView($_SERVER['PHP_SELF'],$_SERVER['QUERY_STRING']);
    // if(!$auth->IsAllowed("setup_role",PRIV_READ)){
          //die("access_denied");
          //exit(1);
   //  } elseif($auth->IsAllowed("setup_role",PRIV_READ)===1){
        //  echo"<script>window.parent.document.location.href='".$APLICATION_ROOT."login.php?msg=Session Expired'</script>";
         // exit(1);
    // }
 
     $priv["1"] = "checked";
     $priv["0"] = "";
     
     if($_GET["id_rol"]) { 
          $_POST["id_rol"] = $enc->Decode($_GET["id_rol"]);
          $idRolEnc = $_GET["id_rol"];
     }

	if ($_POST["id_rol_enc"]) $idRolEnc = $_POST["id_rol_enc"]; 
	
	   $backPage = "role_act_view.php?id=".$idRolEnc;
	  // $tbHeader[0][$counterHeader][TABLE_ISI] = "<input type=\"checkbox\" onClick=\"EW_selectKey(this,'cbDelete[]');\">";
     $tabletblHeader[0]["name"]  = "<input type=\"checkbox\" onClick=\"EW_selectKey(this,'cbDelete[]');\">";
     $tabletblHeader[0]["width"] = "1%";
     $tabletblHeader[0]["align"] = "center";
     
     $tabletblHeader[1]["name"]  = "Role Name";
     $tabletblHeader[1]["width"] = "20%";
     $tabletblHeader[1]["align"] = "center";
 
     $tabletblHeader[2]["name"]  = "Create";
     $tabletblHeader[2]["width"] = "10%";
     $tabletblHeader[2]["align"] = "center";
 
     $tabletblHeader[3]["name"]  = "Read";
     $tabletblHeader[3]["width"] = "10%";
     $tabletblHeader[3]["align"] = "center";
 
     $tabletblHeader[4]["name"]  = "Update";
     $tabletblHeader[4]["width"] = "10%";
     $tabletblHeader[4]["align"] = "center";
 
     $tabletblHeader[5]["name"]  = "Delete";
     $tabletblHeader[5]["width"] = "10%";
     $tabletblHeader[5]["align"] = "center";
 
 
     $tableContent[0]["name"]  = "priv_id";
     $tableContent[0]["wrap"] = "nowrap";
     $tableContent[0]["align"] = "left";
 
     $tableContent[1]["name"]  = "priv_name";
     $tableContent[1]["wrap"] = "nowrap";
     $tableContent[1]["align"] = "left";
 
     $jumContent = count($tabletblHeader);
     $startContent = 1;  // -- mulai bener2 listnya --
 
     $sql = "select a.* from global.global_auth_privilege a 
             left join global.global_auth_role_priv b 
             on a.priv_id = b.id_priv
             and b.id_rol = ".$_POST["id_rol"]." 
             where b.id_priv is null";
     $rs = $dtaccess->Execute($sql);
     $dataTable = $dtaccess->FetchAll($rs);
 
     $sql = "select rol_name from global.global_auth_role where rol_id = ".$_POST["id_rol"];
     $rs = $dtaccess->Execute($sql);
     $dataJabatan = $dtaccess->Fetch($rs);
 
     if ($_POST["btnSave"]) {
         
          $err_code = 0;
  
          if ($err_code == 0) {
               $dbTable = "global.global_auth_role_priv";
               
               $dbField[0] = "id_rol";   // PK
               $dbField[1] = "id_priv";
               $dbField[2] = "rol_priv_access";
   
               $actId = & $_POST["cbDelete"];
               for($i=0,$n=count($actId);$i<$n;$i++){
                    
                    if($_POST["rol_create_".$actId[$i]]) $actAkses = "1";
                    else $actAkses = "0";
                    
                    if($_POST["rol_read_".$actId[$i]]) $actAkses .= "1";
                    else $actAkses .= "0";
                    
                    if($_POST["rol_update_".$actId[$i]]) $actAkses .= "1";
                    else $actAkses .= "0";
                    
                    if($_POST["rol_delete_".$actId[$i]]) $actAkses .= "1";
                    else $actAkses .= "0";
                
                    $dbValue[0] = QuoteValue(DPE_NUMERIC,$_POST["id_rol"]);
                    $dbValue[1] = QuoteValue(DPE_CHAR,$actId[$i]);
                    $dbValue[2] = QuoteValue(DPE_CHAR,$actAkses);
    
                    $dbKey[0] = 0; // -- set key buat clause wherenya , valuenya = index array buat field / value
                    $dbKey[1] = 1; // -- set key buat clause wherenya , valuenya = index array buat field / value
    
                    $dtmodel = new DataModel($dbTable,$dbField,$dbValue,$dbKey);
                    $dtmodel->Insert() or die("insert  error");	
    
                    unset($dtmodel);
                    unset($dbValue);
                    unset($dbKey);
                    unset($actAkses);
               }
     
               header("location:role_act_view.php?id=".$enc->Encode($_POST["id_rol"]));
               exit();        
          }
     }
     $PageHeader = "&nbsp;Role Name : ".$dataJabatan["rol_name"];

?>
<?php echo $view->RenderBody("inventori.css",false); ?>
<table width="100%" border="1" cellpadding="1" cellspacing="1">
	<tr class="tableheader">
	   <td colspan="<?php echo $jumContent;?>"><?php echo $PageHeader;?></td>
	</tr>
</table>

<form name="frmEdit" method="POST" action="<?php echo $_SERVER["PHP_SELF"]?>">
<table width="100%" border="1" cellpadding="1" cellspacing="1">
<tr>
    <td>
    <fieldset>
    <legend><strong>Role&nbsp;Setup</strong></legend>
    <table width="100%" border="1" cellpadding="1" cellspacing="1">
		<tr class="subheader">
		    <?php for($i=0;$i<$jumContent;$i++){ ?>
		        <td width="<?php echo $tabletblHeader[$i]["width"];?>" align="<?php echo $tabletblHeader[$i]["align"];?>"><?php echo $tabletblHeader[$i]["name"];?></td>
		    <?php } ?>
		</tr>
		<?php for($i=0,$n=count($dataTable);$i<$n;$i++){ ?>
	    <tr class="<?php if($i%2==0) echo "tablecontent-odd";else echo "tablecontent";?>">
	        <td align="center"><div align="center"><input type="checkbox" name="cbDelete[]" value="<?php echo $dataTable[$i][$tableContent[0]["name"]];?>"></div></td>
		        <?php for($j=$startContent;$j<($jumContent-4);$j++){ ?>
			<td <?php echo $tableContent[$j]["wrap"];?> align="<?php echo $tableContent[$j]["align"];?>">&nbsp;<?php echo $dataTable[$i][$tableContent[$j]["name"]];?>&nbsp;</td>
	        <?php } ?>
		    <td nowrap align="center">&nbsp;<input type="checkbox" name="rol_create_<?php echo $dataTable[$i][$tableContent[0]["name"]];?>" class="inputField" value="1" checked>&nbsp;</td>
	        <td nowrap align="center">&nbsp;<input type="checkbox" name="rol_read_<?php echo $dataTable[$i][$tableContent[0]["name"]];?>" class="inputField" value="1" checked>&nbsp;</td>
	        <td nowrap align="center">&nbsp;<input type="checkbox" name="rol_update_<?php echo $dataTable[$i][$tableContent[0]["name"]];?>" class="inputField" value="1" checked>&nbsp;</td>
	        <td nowrap align="center">&nbsp;<input type="checkbox" name="rol_delete_<?php echo $dataTable[$i][$tableContent[0]["name"]];?>" class="inputField" value="1" checked>&nbsp;</td>
	    </tr>
	<?php } ?>
	<tr class="tblCol"> 
	    <td colspan="<?php echo ($jumContent);?>"><div align="left">
	        <input type="submit" name="btnSave" value="S a v e" class="button">
			<input type="button" name="btnBack" value="B a c k" class="button" onClick="document.location.href='<?php echo $backPage;?>'"/>
	    <div></td>
	</tr>
	</table>
	</td>
</tr>
</table>
<input type="hidden" name="id_rol" value="<?php echo $_POST["id_rol"];?>" />
<input type="hidden" name="id_rol_enc" value="<?php echo $idRolEnc;?>" />
</form>

<?php echo $view->RenderBodyEnd(); ?>
<?
    $dtaccess->Close();
?>
