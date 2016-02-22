<?php
     require_once("root.inc.php");
     require_once($ROOT."library/auth.cls.php");
     require_once($ROOT."library/textEncrypt.cls.php");
     require_once($ROOT."library/datamodel.cls.php");
     require_once($ROOT."library/bitFunc.lib.php");
     require_once($ROOT."library/currFunc.lib.php");
     require_once($APLICATION_ROOT."library/view.cls.php");

     $dtaccess = new DataAccess();
     $enc = new textEncrypt();
     $auth = new CAuth();
     $userData = $auth->GetUserData();     
     $view = new CView($_SERVER["PHP_SELF"],$_SERVER['QUERY_STRING']);
     
          
     if(!$auth->IsAllowed("setup_biaya",PRIV_READ)){
          die("access_denied");
          exit(1);
     } else if($auth->IsAllowed("setup_biaya",PRIV_READ)===1){
          echo"<script>window.parent.document.location.href='".$APLICATION_ROOT."login.php?msg=Login First'</script>";
          exit(1);
     }

	$isAllowedCreate = $auth->IsAllowed("setup_biaya",PRIV_CREATE);
	$isAllowedUpdate = $auth->IsAllowed("setup_biaya",PRIV_UPDATE);
	$isAllowedDel = $auth->IsAllowed("setup_biaya",PRIV_DELETE);

     function StripArr($num){
          return StripCurrency($num);
     }

	if($_POST["btnUpdate"]) {
		$sql = "delete from klinik.klinik_biaya_split";
		$dtaccess->Execute($sql);
		
		$dbTable = "klinik.klinik_biaya_split";
		
		$dbField[0] = "bea_split_id";   // PK
		$dbField[1] = "id_biaya";
		$dbField[2] = "bea_split_nominal";
		$dbField[3] = "id_split";
		
		foreach($_POST["txtNom"] as $biaya => $dataSplit) {
			
			$beaNominal = array_map("StripArr",$dataSplit);
			
			$sql = "update klinik.klinik_biaya set biaya_total = ".QuoteValue(DPE_NUMERIC,array_sum($beaNominal))." where biaya_id = ".QuoteValue(DPE_CHAR,$biaya);
			$dtaccess->Execute($sql);
			
			foreach($dataSplit as $split => $value) {
			
				$dbValue[0] = QuoteValue(DPE_CHAR,$dtaccess->GetTransID());
				$dbValue[1] = QuoteValue(DPE_CHAR,$biaya);
				$dbValue[2] = QuoteValue(DPE_NUMERIC,StripCurrency($value));
				$dbValue[3] = QuoteValue(DPE_CHAR,$split);
				
				$dbKey[0] = 0; // -- set key buat clause wherenya , valuenya = index array buat field / value
				$dtmodel = new DataModel($dbTable,$dbField,$dbValue,$dbKey);
		
				$dtmodel->Insert() or die("insert  error");	
			
				unset($dtmodel);
				unset($dbValue);
				unset($dbKey);
			}
		}
	}


	$sql = "select * from klinik.klinik_biaya order by biaya_id";
     $rs = $dtaccess->Execute($sql,DB_SCHEMA);
     $dataBiaya = $dtaccess->FetchAll($rs);

	$sql = "select * from klinik.klinik_split order by split_id";
     $rs = $dtaccess->Execute($sql,DB_SCHEMA);
     $dataSplit = $dtaccess->FetchAll($rs);
	
     $sql = "select * from klinik.klinik_biaya_split"; 
     $rs = $dtaccess->Execute($sql,DB_SCHEMA);
     while($row = $dtaccess->Fetch($rs)) {
		$_POST["txtNom"][$row["id_biaya"]][$row["id_split"]] = $row["bea_split_nominal"];
	}
     
	$table = new InoTable("table1","100%","left",null,1,2,1,null);     
     $PageHeader = "Tabel Biaya";

     // --- construct new table ---- //
	$counter=0;
		
     $tbHeader[0][0][TABLE_ISI] = "Layanan";
     $tbHeader[0][0][TABLE_WIDTH] = "20%";
	
     $tbHeader[0][1][TABLE_ISI] = "Jenis";
     $tbHeader[0][1][TABLE_WIDTH] = "10%";
	
	for($i=0,$n=count($dataSplit);$i<$n;$i++){
		$tbHeader[0][$i+2][TABLE_ISI] = $dataSplit[$i]["split_nama"];
		$tbHeader[0][$i+2][TABLE_WIDTH] = "10%";
	}

     for($i=0,$counter=0,$n=count($dataBiaya);$i<$n;$i++,$counter=0){

          $tbContent[$i][$counter][TABLE_ISI] = $dataBiaya[$i]["biaya_nama"];
          $tbContent[$i][$counter][TABLE_ALIGN] = "left";
          $counter++;
		
          $tbContent[$i][$counter][TABLE_ISI] = $rawatStatus[$dataBiaya[$i]["biaya_jenis"]];
          $tbContent[$i][$counter][TABLE_ALIGN] = "left";
          $counter++;
	
		for($j=0,$k=count($dataSplit);$j<$k;$j++){
			if($isAllowedUpdate) $tbContent[$i][$counter][TABLE_ISI] = $view->RenderTextBox("txtNom[".$dataBiaya[$i]["biaya_id"]."][".$dataSplit[$j]["split_id"]."]","txtNom_".$dataBiaya[$i]["biaya_id"]."_".$dataSplit[$j]["split_id"],"10","10",currency_format($_POST["txtNom"][$dataBiaya[$i]["biaya_id"]][$dataSplit[$j]["split_id"]]),"curedit", null,true);
			else $tbContent[$i][$counter][TABLE_ISI] = currency_format($_POST["txtNom"][$dataBiaya[$i]["biaya_id"]][$dataSplit[$j]["split_id"]]);
	
			$tbContent[$i][$counter][TABLE_ALIGN] = "center";
			$counter++;
		}
     }


     if($isAllowedUpdate) $tbBottom[0][0][TABLE_ISI] = '&nbsp;&nbsp;<input type="submit" name="btnUpdate" value="Simpan" class="button">&nbsp;';
     
	$tbBottom[0][0][TABLE_WIDTH] = "100%";
     $tbBottom[0][0][TABLE_COLSPAN] = count($tbHeader[0]);
     $tbBottom[0][0][TABLE_ALIGN] = "center";
	$counter++;
	
?>

<?php echo $view->RenderBody("inosoft.css",false); ?>


<table width="100%" border="1" cellpadding="0" cellspacing="0">
     <tr class="tableheader">
          <td>&nbsp;<?php echo $PageHeader;?></td>
     </tr>
</table>

<BR/>


<form name="frmEdit" method="POST" action="<?php echo $_SERVER["PHP_SELF"]?>">

<?php echo $table->RenderView($tbHeader,$tbContent,$tbBottom); ?>

</form>
</body>
</html>
 
