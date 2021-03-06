<?php
     require_once("root.inc.php");
     require_once($ROOT."library/auth.cls.php");
     require_once($ROOT."library/textEncrypt.cls.php");
     require_once($ROOT."library/datamodel.cls.php");
     require_once($ROOT."library/currFunc.lib.php");
     require_once($ROOT."library/dateFunc.lib.php");
     require_once($APLICATION_ROOT."library/view.cls.php");
     
     $view = new CView($_SERVER['PHP_SELF'],$_SERVER['QUERY_STRING']);
     $dtaccess = new DataAccess();
     $enc = new TextEncrypt();     
     $auth = new CAuth();
     $table = new InoTable("table","100%","left");
 

     $viewPage = "paket_view.php"; 

     if(!$auth->IsAllowed("registrasi",PRIV_READ)){
          die("access_denied");
          exit(1);
          
     } elseif($auth->IsAllowed("registrasi",PRIV_READ)===1){
          echo"<script>window.parent.document.location.href='".$ROOT."login.php?msg=Session Expired'</script>";
          exit(1);
     }

     if ($_GET["id"]) {
     
     $_x_mode = "Edit";
     $paketId = $enc->Decode($_GET["id"]);
          
          
     $sql = "select a.paket_nama, a.paket_pemeriksaan from lab_paket a where paket_id = ". QuoteValue(DPE_CHAR,$paketId);
     $rs = $dtaccess->Execute($sql,DB_SCHEMA_LAB);
     $dataPaket = $dtaccess->Fetch($rs);
     
     $pieces = explode("~", $dataPaket["paket_pemeriksaan"]);

    }

     $sql = "select * from lab_kegiatan a 
             left join lab_kategori b on b.kategori_id = a.id_kategori
             left join lab_bonus c on c.bonus_id = a.id_bonus
             order by b.kategori_nama ";
     $rs = $dtaccess->Execute($sql,DB_SCHEMA_LAB);
     $dataTable = $dtaccess->FetchAll($rs);
     
     //*-- config table ---*//
     $tableHeader = "&nbsp;Paket Pemeriksaan ". $dataPaket["paket_nama"];
     
     $isAllowedDel = $auth->IsAllowed("registrasi",PRIV_DELETE);
     $isAllowedUpdate = $auth->IsAllowed("registrasi",PRIV_UPDATE);
     $isAllowedCreate = $auth->IsAllowed("registrasi",PRIV_CREATE);


     //proses simpan
    if($_POST["btnAdd"]){
    $Pemeriksaan = implode("~", $_POST["pemeriksaan"]);
     
    $sql = "update laboratorium.lab_paket set paket_pemeriksaan = ".QuoteValue(DPE_CHAR,$Pemeriksaan)."  where paket_id = ".QuoteValue(DPE_CHAR,$_POST["paket"]) ;
		$dtaccess->Execute($sql); 
    header("location:".$viewPage);
    exit();
    }

     
     // --- construct new table ---- //
     $counterHeader = 0;
     if($isAllowedDel){
          $tbHeader[0][$counterHeader][TABLE_ISI] = "<input type=\"checkbox\" onClick=\"EW_selectKey(this,'pemeriksaan[]');\">";
          $tbHeader[0][$counterHeader][TABLE_WIDTH] = "3%";
          $counterHeader++;
     }
     
     $tbHeader[0][$counterHeader][TABLE_ISI] = "Nama";
     $tbHeader[0][$counterHeader][TABLE_WIDTH] = "30%";
     $counterHeader++; 
     
     $tbHeader[0][$counterHeader][TABLE_ISI] = "Nilai Normal";
     $tbHeader[0][$counterHeader][TABLE_WIDTH] = "10%";
     $counterHeader++; 
     
     $tbHeader[0][$counterHeader][TABLE_ISI] = "Satuan";
     $tbHeader[0][$counterHeader][TABLE_WIDTH] = "15%";
     $counterHeader++; 
     
     $tbHeader[0][$counterHeader][TABLE_ISI] = "Kategori Bonus";
     $tbHeader[0][$counterHeader][TABLE_WIDTH] = "15%";
     $counterHeader++; 
     
     $tbHeader[0][$counterHeader][TABLE_ISI] = "Biaya";
     $tbHeader[0][$counterHeader][TABLE_WIDTH] = "20%";
     $counterHeader++; 
     
     for($i=0,$j=0,$counter=0,$n=count($dataTable);$i<$n;$i++,$j++,$counter=0){
       if($dataTable[$i]["kategori_nama"]!=$dataTable[$i-1]["kategori_nama"]){
         $tbContent[$j][$counter][TABLE_ISI] = "&nbsp;&nbsp;&nbsp;&nbsp;".$dataTable[$i]["kategori_nama"];            
         $tbContent[$j][$counter][TABLE_CLASS] = "tablesmallheader";
         $tbContent[$j][$counter][TABLE_ALIGN] = "left";
         $tbContent[$j][$counter][TABLE_COLSPAN] = count($tbHeader[0]);
         $counter=0; $j++;
       }
          if($isAllowedDel) {
          
         for($ii=0,$nn=count($pieces);$ii<$nn;$ii++){
          
          if($pieces[$ii] == $dataTable[$i]["kegiatan_id"]){
          $checkdatax = "checked";
          }
          } 
               $tbContent[$j][$counter][TABLE_ISI] = '<input type="checkbox" name="pemeriksaan[]" '.$checkdatax.' value="'.$dataTable[$i]["kegiatan_id"].'">';
               $tbContent[$j][$counter][TABLE_ALIGN] = "center";
               $counter++;
          }
          
          $tbContent[$j][$counter][TABLE_ISI] = "&nbsp;&nbsp;".$dataTable[$i]["kegiatan_nama"];
          $tbContent[$j][$counter][TABLE_ALIGN] = "center";
          $counter++; 
     
          $tbContent[$j][$counter][TABLE_ISI] = "&nbsp;&nbsp;".$dataTable[$i]["kegiatan_nilai_normal"];
          $tbContent[$j][$counter][TABLE_ALIGN] = "center";
          $counter++; 
     
          $tbContent[$j][$counter][TABLE_ISI] = "&nbsp;&nbsp;".$dataTable[$i]["kegiatan_satuan"];
          $tbContent[$j][$counter][TABLE_ALIGN] = "center";
          $counter++; 
     
          $tbContent[$j][$counter][TABLE_ISI] = "&nbsp;&nbsp;".$dataTable[$i]["bonus_nama"];
          $tbContent[$j][$counter][TABLE_ALIGN] = "left";
          $counter++; 
     
          $tbContent[$j][$counter][TABLE_ISI] = "Rp.&nbsp;".currency_format($dataTable[$i]["kegiatan_biaya"])."&nbsp;&nbsp;";
          $tbContent[$j][$counter][TABLE_ALIGN] = "right";
          $counter++; 
          
          unset($checkdatax);
     }
     
     $colspan = count($tbHeader[0]);

     

     $tbBottom[0][0][TABLE_ISI] .= '&nbsp;&nbsp;<input type="hidden" name="paket" value="'.$paketId.'"><input type="submit" name="btnAdd" value="Simpan" class="button" >&nbsp;';

     
     $tbBottom[0][0][TABLE_WIDTH] = "100%";
     $tbBottom[0][0][TABLE_COLSPAN] = $colspan;
?>

<?php echo $view->RenderBody("inosoft.css",false); ?>

<table width="100%" border="1" cellpadding="0" cellspacing="0">
     <tr class="tableheader">
          <td><?php echo $tableHeader;?></td>
     </tr>
     
</table>

<form name="frmView" method="POST" action="paket_detail.php">
     <?php echo $table->RenderView($tbHeader,$tbContent,$tbBottom); ?>
</form>

<?php echo $view->RenderBodyEnd(); ?>
