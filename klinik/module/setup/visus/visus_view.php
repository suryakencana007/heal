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
     $table = new InoTable("table","70%","left");
 
     $editPage = "visus_edit.php?";
     $thisPage = "visus_view.php?";

     if(!$auth->IsAllowed("setup_visus",PRIV_READ)){
          die("access_denied");
          exit(1);
          
     } elseif($auth->IsAllowed("setup_visus",PRIV_READ)===1){
          echo"<script>window.parent.document.location.href='".$ROOT."login.php?msg=Session Expired'</script>";
          exit(1);
     }
     
     
    
	$imgPub["y"] = '<img hspace="2" width="16" height="16" src="'.$APLICATION_ROOT.'images/on.gif" alt="Buta" title="Buta" border="0">';
	$imgPub["n"] = '<img hspace="2" width="16" height="16" src="'.$APLICATION_ROOT.'images/off.gif" alt="Normal" title="Normal" border="0">';

     $sql = "select a.* from klinik.klinik_visus a order by a.visus_id";
     $rs = $dtaccess->Execute($sql);
     $dataTable = $dtaccess->FetchAll($rs);
     
     //*-- config table ---*//
     $tableHeader = "&nbsp;Visus";
     
     $isAllowedDel = $auth->IsAllowed("setup_visus",PRIV_DELETE);
     $isAllowedUpdate = $auth->IsAllowed("setup_visus",PRIV_UPDATE);
     $isAllowedCreate = $auth->IsAllowed("setup_visus",PRIV_CREATE);
     
     // --- construct new table ---- //
     $counterHeader = 0;
     if($isAllowedDel){
          $tbHeader[0][$counterHeader][TABLE_ISI] = "<input type=\"checkbox\" onClick=\"EW_selectKey(this,'cbDelete[]');\">";
          $tbHeader[0][$counterHeader][TABLE_WIDTH] = "3%";
          $counterHeader++;
     }
     
     if($isAllowedUpdate){
          $tbHeader[0][$counterHeader][TABLE_ISI] = "Edit";
          $tbHeader[0][$counterHeader][TABLE_WIDTH] = "7%";
          $counterHeader++;
     }
          
     $tbHeader[0][$counterHeader][TABLE_ISI] = "Nama";
     $tbHeader[0][$counterHeader][TABLE_WIDTH] = "15%";
     $counterHeader++;

     $tbHeader[0][$counterHeader][TABLE_ISI] = "Buta";
     $tbHeader[0][$counterHeader][TABLE_WIDTH] = "15%";
     $counterHeader++;
     
     for($i=0,$counter=0,$n=count($dataTable);$i<$n;$i++,$counter=0){
          if($isAllowedDel) {
               $tbContent[$i][$counter][TABLE_ISI] = '<input type="checkbox" name="cbDelete[]" value="'.$dataTable[$i]["visus_id"].'">';               
               $tbContent[$i][$counter][TABLE_ALIGN] = "center";
               $counter++;
          }
          
          if($isAllowedUpdate) {
               $tbContent[$i][$counter][TABLE_ISI] = '<a href="'.$editPage.'&id='.$enc->Encode($dataTable[$i]["visus_id"]).'"><img hspace="2" width="16" height="16" src="'.$APLICATION_ROOT.'images/b_edit.png" alt="Edit" title="Edit" border="0"></a>';               
               $tbContent[$i][$counter][TABLE_ALIGN] = "center";
               $counter++;
          }
          
          $tbContent[$i][$counter][TABLE_ISI] = $dataTable[$i]["visus_nama"];
          $tbContent[$i][$counter][TABLE_ALIGN] = "left";
          $counter++;
          
          $tbContent[$i][$counter][TABLE_ISI] = $imgPub[$dataTable[$i]["visus_is_buta"]];
          $tbContent[$i][$counter][TABLE_ALIGN] = "center";          
     }
     
     $colspan = count($tbHeader[0]);

     
     if($isAllowedDel) $tbBottom[0][0][TABLE_ISI] = '&nbsp;&nbsp;<input type="submit" name="btnDelete" value="Hapus" class="button">&nbsp;';
     if($isAllowedCreate) $tbBottom[0][0][TABLE_ISI] .= '&nbsp;&nbsp;<input type="button" name="btnAdd" value="Tambah Baru" class="button" onClick="document.location.href=\''.$editPage.'\'">&nbsp;';
     $tbBottom[0][0][TABLE_WIDTH] = "100%";
     $tbBottom[0][0][TABLE_COLSPAN] = $colspan;
?>

<?php echo $view->RenderBody("inosoft.css",false); ?>

<table width="100%" border="1" cellpadding="0" cellspacing="0">
     <tr class="tableheader">
          <td><?php echo $tableHeader;?></td>
     </tr>
</table>

<form name="frmView" method="POST" action="<?php echo $editPage; ?>">
     <?php echo $table->RenderView($tbHeader,$tbContent,$tbBottom); ?>
</form>

<?php echo $view->RenderBodyEnd(); ?>
