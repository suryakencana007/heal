<?php
     require_once("root.inc.php");
     require_once($ROOT."library/auth.cls.php");
     require_once($ROOT."library/textEncrypt.cls.php");
     require_once($ROOT."library/datamodel.cls.php");
     require_once($ROOT."library/dateFunc.lib.php");
     require_once($APLICATION_ROOT."library/view.cls.php");
     
     $view = new CView($_SERVER['PHP_SELF'],$_SERVER['QUERY_STRING']);
     $dtaccess = new DataAccess();
     $enc = new TextEncrypt();     
     $auth = new CAuth();
     $table = new InoTable("table","100%","left");
 
     $thisPage = "report_pasien.php";

     if(!$auth->IsAllowed("dinas_luar",PRIV_READ)){
          die("access_denied");
          exit(1);
          
     } elseif($auth->IsAllowed("dinas_luar",PRIV_READ)===1){
          echo"<script>window.parent.document.location.href='".$ROOT."login.php?msg=Session Expired'</script>";
          exit(1);
     }

     if(!$_POST["tgl_awal"]) $_POST["tgl_awal"] = date("d-m-Y");
     $sql_where[] = "a.reg_jenis_pasien = ".QuoteValue(DPE_CHAR,PASIEN_DINASLUAR);
     $sql_where[] = "a.reg_dinasluar_tanggal = ".QuoteValue(DPE_DATE,date_db($_POST["tgl_awal"]));
     if($_POST["kota_nya"]) $sql_where[] = "a.reg_dinasluar_kota = ".QuoteValue(DPE_NUMERIC,$_POST["kota_nya"]);
     
     $sql = "select distinct(b.cust_usr_kode), b.cust_usr_nama, b.cust_usr_alamat, b.cust_usr_tanggal_lahir, b.cust_usr_jenis_kelamin, 
               a.reg_jenis_pasien, a.reg_status_pasien, a.reg_keterangan, a.reg_waktu,a.reg_dinasluar_tanggal,c.kota_nama,c.kota_id
               from klinik.klinik_registrasi a 
               join global.global_customer_user b on a.id_cust_usr = b.cust_usr_id
               right join global.global_kota c on a.reg_dinasluar_kota = c.kota_id";
     $sql.= " where ".implode(" and ",$sql_where);
     $sql.= "order by c.kota_nama, b.cust_usr_nama, a.reg_waktu";
     
     $rs = $dtaccess->Execute($sql);
     $dataTable = $dtaccess->FetchAll($rs);
     
     //*-- config table ---*//
     $tableHeader = "&nbsp;Report Pasien Dinas Luar";

     
     // --- construct new table ---- //
     $counterHeader = 0;
          
     $tbHeader[0][$counterHeader][TABLE_ISI] = "No";
     $tbHeader[0][$counterHeader][TABLE_WIDTH] = "5%";
     $counterHeader++;
          
     $tbHeader[0][$counterHeader][TABLE_ISI] = "Kode";
     $tbHeader[0][$counterHeader][TABLE_WIDTH] = "10%";
     $counterHeader++;
     
     $tbHeader[0][$counterHeader][TABLE_ISI] = "Nama";
     $tbHeader[0][$counterHeader][TABLE_WIDTH] = "15%";     
     $counterHeader++;
     
     $tbHeader[0][$counterHeader][TABLE_ISI] = "Alamat";
     $tbHeader[0][$counterHeader][TABLE_WIDTH] = "30%";     
     $counterHeader++;
     
     $tbHeader[0][$counterHeader][TABLE_ISI] = "Umur";
     $tbHeader[0][$counterHeader][TABLE_WIDTH] = "5%";     
     $counterHeader++;
     
     $tbHeader[0][$counterHeader][TABLE_ISI] = "Jenis Kelamin";
     $tbHeader[0][$counterHeader][TABLE_WIDTH] = "5%";     
     $counterHeader++;
     
     $tbHeader[0][$counterHeader][TABLE_ISI] = "Status Pasien";
     $tbHeader[0][$counterHeader][TABLE_WIDTH] = "5%";     
     $counterHeader++;
     
     $tbHeader[0][$counterHeader][TABLE_ISI] = "Keterangan";
     $tbHeader[0][$counterHeader][TABLE_WIDTH] = "15%";     
     $counterHeader++;
     
     $tbHeader[0][$counterHeader][TABLE_ISI] = "Waktu";
     $tbHeader[0][$counterHeader][TABLE_WIDTH] = "10%";     
     $counterHeader++;
     $k=0;
     for($i=0,$j=0,$counter=0,$n=count($dataTable);$i<$n;$i++,$j++,$counter=0){
          if($dataTable[$i]["kota_id"]!=$dataTable[$i-1]["kota_id"]){
            $tbContent[$j][$counter][TABLE_ISI] = ($k+1)."&nbsp;";
            $tbContent[$j][$counter][TABLE_ALIGN] = "right";
            $counter++;
          
            $tbContent[$j][$counter][TABLE_ISI] = "&nbsp;&nbsp;".$dataTable[$i]["kota_nama"];
            $tbContent[$j][$counter][TABLE_ALIGN] = "left";
            $tbContent[$j][$counter][TABLE_CLASS] = "tablesmallheader";
            $tbContent[$j][$counter][TABLE_COLSPAN] = count($tbHeader[0])-1;
            $counter=0; $j++;
          
          }
          
          $tbContent[$j][$counter][TABLE_ISI] = "&nbsp;";
          $tbContent[$j][$counter][TABLE_ALIGN] = "right";
          $counter++;
          
          $tbContent[$j][$counter][TABLE_ISI] = $dataTable[$i]["cust_usr_kode"];
          $tbContent[$j][$counter][TABLE_ALIGN] = "left";
          $counter++;
          
          $tbContent[$j][$counter][TABLE_ISI] = $dataTable[$i]["cust_usr_nama"];
          $tbContent[$j][$counter][TABLE_ALIGN] = "left";          
          $counter++;
          
          $tbContent[$j][$counter][TABLE_ISI] = nl2br($dataTable[$i]["cust_usr_alamat"]);
          $tbContent[$j][$counter][TABLE_ALIGN] = "left";          
          $counter++;
          
          $tbContent[$j][$counter][TABLE_ISI] = HitungUmur($dataTable[$i]["cust_usr_tanggal_lahir"]);
          $tbContent[$j][$counter][TABLE_ALIGN] = "left";          
          $counter++;
          
          $tbContent[$j][$counter][TABLE_ISI] = $dataTable[$i]["cust_usr_jenis_kelamin"];
          $tbContent[$j][$counter][TABLE_ALIGN] = "left";          
          $counter++;
          
          $tbContent[$j][$counter][TABLE_ISI] = $statusPasien[$dataTable[$i]["reg_status_pasien"]];
          $tbContent[$j][$counter][TABLE_ALIGN] = "left";          
          $counter++;

          $tbContent[$j][$counter][TABLE_ISI] = nl2br($dataTable[$i]["reg_keterangan"]);
          $tbContent[$j][$counter][TABLE_ALIGN] = "left";          
          $counter++;

          $tbContent[$j][$counter][TABLE_ISI] = $dataTable[$i]["reg_waktu"];
          $tbContent[$j][$counter][TABLE_ALIGN] = "left";          
          $counter++;
          
     }
     
     $colspan = count($tbHeader[0]);
     
     if(!$_POST["btnExcel"]){
          $tbBottom[0][0][TABLE_ISI] .= '&nbsp;&nbsp;<input type="submit" name="btnExcel" value="Export Excel" class="button" onClick="document.location.href=\''.$editPage.'\'">&nbsp;';
          $tbBottom[0][0][TABLE_WIDTH] = "100%";
          $tbBottom[0][0][TABLE_COLSPAN] = $colspan;
          $tbBottom[0][0][TABLE_ALIGN] = "center";
     }
     
	if($_POST["btnExcel"]){
          header('Content-Type: application/vnd.ms-excel');
          header('Content-Disposition: attachment; filename=report_pasien_'.$_POST["tgl_awal"].'.xls');
     }

    $sql = "select * from global_kota where id_prop>=10 and id_prop<=15 order by id_prop DESC, kota_id";
    $rs_kota = $dtaccess->Execute($sql,DB_SCHEMA_GLOBAL);
    $data_kota = $dtaccess->FetchAll($rs_kota);
?>
<?php if(!$_POST["btnExcel"]) { ?>
<?php echo $view->RenderBody("inosoft.css",true); ?>
<?php } ?>
<script language="JavaScript">
function CheckSimpan(frm) {
     
     if(!frm.tgl_awal.value) {
          alert("Tanggal Harus Diisi");
          return false;
     }

     if(!CheckDate(frm.tgl_awal.value)) {
          return false;
     }
}

</script>
<table width="100%" border="1" cellpadding="0" cellspacing="0">
     <tr class="tableheader">
          <td><?php echo $tableHeader;?></td>
     </tr>
</table>

<form name="frmView" method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>" onSubmit="return CheckSimpan(this);">
<?php if(!$_POST["btnExcel"]) { ?>
<table align="center" border=0 cellpadding=2 cellspacing=1 width="100%" class="tblForm" id="tblSearching">
     <tr>
          <td width="10%" class="tablecontent">&nbsp;Tanggal Pelaksanaan</td>
          <td width="20%" class="tablecontent-odd">
               <input type="text"  id="tgl_awal" name="tgl_awal" size="15" maxlength="10" value="<?php echo $_POST["tgl_awal"];?>"/>
               <img src="<?php echo $APLICATION_ROOT;?>images/b_calendar.png" width="16" height="16" align="middle" id="img_tgl_awal" style="cursor: pointer; border: 0px solid white;" title="Date selector" onMouseOver="this.style.background='red';" onMouseOut="this.style.background=''" />
          </td>
          <td width="15%" class="tablecontent">&nbsp;Lokasi</td>
          <td width="25%" class="tablecontent-odd">
			<select name="kota_nya" id="kota_nya" onKeyDown="return tabOnEnter(this, event);">
                    <option value="" >[ Pilih Lokasi ]</option>
                    <?php for($r=0;$r<count($data_kota);$r++) { ?>
                         <option value="<?php echo $data_kota[$r]["kota_id"];?>" <?php if($data_kota[$r]["kota_id"]==$_POST["kota_nya"]) echo "selected"; ?>><?php echo $data_kota[$r]["kota_nama"];?></option>
                    <?php } ?>
			</select>
          </td>
          <td class="tablecontent">
               <input type="submit" name="btnLanjut" value="Lanjut" class="button">
          </td>
     </tr>
</table>
<?php } ?>
<?php if(!$dataTable && $_POST["btnLanjut"]){?>
        <br /><br /><span style="font-family:sans-serif;color:#ff2200;font-size:14px">Data tidak ditemukan</span>
<?php }else {
        echo $table->RenderView($tbHeader,$tbContent,$tbBottom); 
}?>

</form>

<script type="text/javascript">
    Calendar.setup({
        inputField     :    "tgl_awal",      // id of the input field
        ifFormat       :    "<?php echo $formatCal;?>",       // format of the input field
        showsTime      :    false,            // will display a time selector
        button         :    "img_tgl_awal",   // trigger for the calendar (button ID)
        singleClick    :    true,           // double-click mode
        step           :    1                // show all years in drop-down boxes (instead of every other year as default)
    });
</script>

<?php echo $view->RenderBodyEnd(); ?>
