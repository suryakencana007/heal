<?php
     require_once("root.inc.php");
     require_once($ROOT."library/bitFunc.lib.php");
     require_once($ROOT."library/auth.cls.php");
     require_once($ROOT."library/textEncrypt.cls.php");
     require_once($ROOT."library/datamodel.cls.php");
     require_once($ROOT."library/currFunc.lib.php");
     require_once($ROOT."library/dateFunc.lib.php");
     require_once($ROOT."library/inoLiveX.php");
	   require_once($APLICATION_ROOT."library/view.cls.php");	
     
     $view = new CView($_SERVER['PHP_SELF'],$_SERVER['QUERY_STRING']);
     $dtaccess = new DataAccess();
     $enc = new textEncrypt();     
	$auth = new CAuth();
     $err_code = 0;
     
     $viewPage = "dokter_view.php";
     $editPage = "dokter_edit.php";
	
	$plx = new InoLiveX("CheckDataCustomerTipe");
	
     if(!$auth->IsAllowed("laboratorium",PRIV_READ)){
          die("access_denied");
          exit(1);
          
     } elseif($auth->IsAllowed("laboratorium",PRIV_READ)===1){
          echo"<script>window.parent.document.location.href='".$ROOT."login.php?msg=Session Expired'</script>";
          exit(1);
     }
	
	function CheckDataCustomerTipe($custTipeNama)
	{
          global $dtaccess;
          
          $sql = "SELECT a.dokter_id FROM lab_dokter a 
                    WHERE upper(a.dokter_nama) = ".QuoteValue(DPE_CHAR,strtoupper($custTipeNama));
          $rs = $dtaccess->Execute($sql,DB_SCHEMA_LAB);
          $datadokter = $dtaccess->Fetch($rs);
          
		return $datadokter["dokter_id"];
     }
	
	if($_POST["x_mode"]) $_x_mode = & $_POST["x_mode"];
	else $_x_mode = "New";
   
	if($_POST["dokter_id"])  $dokterId = & $_POST["dokter_id"];
 
     if ($_GET["id"]) {
          if ($_POST["btnDelete"]) { 
               $_x_mode = "Delete";
          } else { 
               $_x_mode = "Edit";
               $dokterId = $enc->Decode($_GET["id"]);
          }
         
          $sql = "select a.* from lab_dokter a 
				where dokter_id = ".QuoteValue(DPE_CHAR,$dokterId);
          $rs_edit = $dtaccess->Execute($sql,DB_SCHEMA_LAB);
          $row_edit = $dtaccess->Fetch($rs_edit);
          $dtaccess->Clear($rs_edit);
          
          $view->CreatePost($row_edit);
      }

	if($_x_mode=="New") $privMode = PRIV_CREATE;
	elseif($_x_mode=="Edit") $privMode = PRIV_UPDATE;
	else $privMode = PRIV_DELETE;    

     if ($_POST["btnNew"]) {
          header("location: ".$_SERVER["PHP_SELF"]);
          exit();
     }
   
     if ($_POST["btnSave"] || $_POST["btnUpdate"]) {          
          if($_POST["btnUpdate"]){
               $dokterId = & $_POST["dokter_id"];
               $_x_mode = "Edit";
          }
 
         
          if ($err_code == 0) {
               $dbTable = "lab_dokter";
               
               $dbField[0] = "dokter_id";   // PK
               $dbField[1] = "dokter_nama"; 
               $dbField[2] = "dokter_tempat_lahir"; 
               $dbField[3] = "dokter_tanggal_lahir"; //date
			         $dbField[4] = "dokter_alamat"; 
        			 $dbField[5] = "dokter_kota"; 
        			 $dbField[6] = "dokter_kodepos"; 
        			 $dbField[7] = "dokter_telp_rumah"; 
        			 $dbField[8] = "dokter_telp_ponsel"; 
        			 $dbField[9] = "dokter_email"; 
        			 $dbField[10] = "dokter_pekerjaan"; 
        			 $dbField[11] = "dokter_sex"; 
        			 $dbField[12] = "dokter_agama"; 
        			 $dbField[13] = "dokter_status_nikah"; 
        			 $dbField[14] = "dokter_warganegara"; 
        			 $dbField[15] = "dokter_goldarah"; 
        			 $dbField[16] = "dokter_jabatan"; 
        			 $dbField[17] = "dokter_pendidikan"; 
        			 $dbField[18] = "dokter_gajipokok"; //numerik
        			 $dbField[19] = "id_divisi"; 
        			 $dbField[20] = "dokter_nip"; 
        			 
               if(!$dokterId) $dokterId = $dtaccess->GetTransId();   
               $dbValue[0] = QuoteValue(DPE_CHAR,$dokterId);
               $dbValue[1] = QuoteValue(DPE_CHAR,$_POST["dokter_nama"]);
               $dbValue[2] = QuoteValue(DPE_CHAR,$_POST["dokter_tempat_lahir"]);
               $dbValue[3] = QuoteValue(DPE_DATE,date_db($_POST["dokter_tanggal_lahir"]));
               $dbValue[4] = QuoteValue(DPE_CHAR,$_POST["dokter_alamat"]);
               $dbValue[5] = QuoteValue(DPE_CHAR,$_POST["dokter_kota"]);
               $dbValue[6] = QuoteValue(DPE_CHAR,$_POST["dokter_kodepos"]);
               $dbValue[7] = QuoteValue(DPE_CHAR,$_POST["dokter_telp_rumah"]);
               $dbValue[8] = QuoteValue(DPE_CHAR,$_POST["dokter_telp_ponsel"]);
               $dbValue[9] = QuoteValue(DPE_CHAR,$_POST["dokter_email"]);
               $dbValue[10] = QuoteValue(DPE_CHAR,$_POST["dokter_pekerjaan"]);
               $dbValue[11] = QuoteValue(DPE_CHAR,$_POST["dokter_sex"]);
               $dbValue[12] = QuoteValue(DPE_CHAR,$_POST["dokter_agama"]);
               $dbValue[13] = QuoteValue(DPE_CHAR,$_POST["dokter_status_nikah"]);
               $dbValue[14] = QuoteValue(DPE_CHAR,$_POST["dokter_warganegara"]);
               $dbValue[15] = QuoteValue(DPE_CHAR,$_POST["dokter_goldarah"]);
               $dbValue[16] = QuoteValue(DPE_CHAR,$_POST["dokter_jabatan"]);
               $dbValue[17] = QuoteValue(DPE_CHAR,$_POST["dokter_pendidikan"]);
               $dbValue[18] = QuoteValue(DPE_NUMERIC,StripCurrency($_POST["dokter_gajipokok"]));
               $dbValue[19] = QuoteValue(DPE_CHAR,$_POST["id_divisi"]);
               $dbValue[20] = QuoteValue(DPE_CHAR,$_POST["dokter_nip"]);
               
               $dbKey[0] = 0; // -- set key buat clause wherenya , valuenya = index array buat field / value
               $dtmodel = new DataModel($dbTable,$dbField,$dbValue,$dbKey,DB_SCHEMA_LAB);
   
               if ($_POST["btnSave"]) {
                    $dtmodel->Insert() or die("insert  error");	
                  
               } else if ($_POST["btnUpdate"]) {
                    $dtmodel->Update() or die("update  error");	
               }
                  unset($dtmodel);
                  unset($dbField);
                  unset($dbValue);
                  unset($dbKey);
               
                  header("location:".$viewPage);
                  exit();
          }
     }
 
     if ($_POST["btnDelete"]) {
          $dokterId = & $_POST["cbDelete"];
          
          for($i=0,$n=count($dokterId);$i<$n;$i++){
               $sql = "delete from lab_dokter
                         where dokter_id = ".QuoteValue(DPE_CHAR,$dokterId[$i]);
               $dtaccess->Execute($sql,DB_SCHEMA_LAB);
          }
          
          header("location:".$viewPage);
          exit();    
     }
     //-- option untuk divisi dokternya --//
     $opt_divisi[0] = $view->RenderOption(DIVISI_DOKTER_DALAM,$divisi_dokter[DIVISI_DOKTER_DALAM],($_POST["id_divisi"]==DIVISI_DOKTER_DALAM)?"selected":"");
     $opt_divisi[1] = $view->RenderOption(DIVISI_DOKTER_LUAR,$divisi_dokter[DIVISI_DOKTER_LUAR],($_POST["id_divisi"]==DIVISI_DOKTER_LUAR)?"selected":"");
     
     //-- option untuk jenis kelamin dokternya --//
     $opt_sex[0] = $view->RenderOption("L","Laki-Laki",($_POST["dokter_sex"]=="L")?"selected":"");
     $opt_sex[1] = $view->RenderOption("P","Perempuan",($_POST["dokter_sex"]=="P")?"selected":"");
    
     //-- option untuk agama dokternya --//
     $opt_agama[0] = $view->RenderOption(AGAMA_ISLAM,$agamanya[AGAMA_ISLAM],($_POST["dokter_agama"]==AGAMA_ISLAM)?"selected":"");
     $opt_agama[1] = $view->RenderOption(AGAMA_KATOLIK,$agamanya[AGAMA_KATOLIK],($_POST["dokter_agama"]==AGAMA_KATOLIK)?"selected":"");
     $opt_agama[2] = $view->RenderOption(AGAMA_PROTESTAN,$agamanya[AGAMA_PROTESTAN],($_POST["dokter_agama"]==AGAMA_PROTESTAN)?"selected":"");
     $opt_agama[3] = $view->RenderOption(AGAMA_HINDU,$agamanya[AGAMA_HINDU],($_POST["dokter_agama"]==AGAMA_HINDU)?"selected":"");
     $opt_agama[4] = $view->RenderOption(AGAMA_BUDHA,$agamanya[AGAMA_BUDHA],($_POST["dokter_agama"]==AGAMA_BUDHA)?"selected":"");
          
     //-- option untuk status pernikahan dokternya --//
     $opt_status_nikah[0] = $view->RenderOption(MENIKAH_BELUM,$status_menikah[MENIKAH_BELUM],($_POST["dokter_status_nikah"]==MENIKAH_BELUM)?"selected":"");
     $opt_status_nikah[1] = $view->RenderOption(MENIKAH_SUDAH,$status_menikah[MENIKAH_SUDAH],($_POST["dokter_status_nikah"]==MENIKAH_SUDAH)?"selected":"");
     $opt_status_nikah[2] = $view->RenderOption(MENIKAH_DUDA,$status_menikah[MENIKAH_DUDA],($_POST["dokter_status_nikah"]==MENIKAH_DUDA)?"selected":"");
     $opt_status_nikah[3] = $view->RenderOption(MENIKAH_JANDA,$status_menikah[MENIKAH_JANDA],($_POST["dokter_status_nikah"]==MENIKAH_JANDA)?"selected":"");
     
     //-- option untuk warga negara dokternya --//
     $opt_warganegara[0] = $view->RenderOption("WNI","Warga Negara Indonesia",($_POST["dokter_warganegara"]=="WNI")?"selected":"");
     $opt_warganegara[1] = $view->RenderOption("WNA","Warga Negara Asing",($_POST["dokter_warganegara"]=="WNA")?"selected":"");
     
     //-- option untuk golongan darah dokternya --//
     $opt_goldarah[0] = $view->RenderOption(GOLDARAH_O,GOLDARAH_O,($_POST["dokter_goldarah"]==GOLDARAH_O)?"selected":"");
     $opt_goldarah[1] = $view->RenderOption(GOLDARAH_A,GOLDARAH_A,($_POST["dokter_goldarah"]==GOLDARAH_A)?"selected":"");
     $opt_goldarah[2] = $view->RenderOption(GOLDARAH_B,GOLDARAH_B,($_POST["dokter_goldarah"]==GOLDARAH_B)?"selected":"");
     $opt_goldarah[3] = $view->RenderOption(GOLDARAH_AB,GOLDARAH_AB,($_POST["dokter_goldarah"]==GOLDARAH_AB)?"selected":"");
     
?>

<?php echo $view->RenderBody("inosoft.css",true); ?>

<script language="javascript" type="text/javascript">

<? $plx->Run(); ?>

function CheckDataSave(frm)
{ 
     
     if(!frm.dokter_nama.value){
		alert('Nama dokter Optik Harus Diisi');
		frm.dokter_nama.focus();
          return false;
	}
	
	if(frm.x_mode.value=="New") {
		if(CheckDataCustomerTipe(frm.dokter_nama.value,'type=r')){
			alert('Nama dokter Optik Sudah Ada');
			frm.dokter_nama.focus();
			frm.dokter_nama.select();
			return false;
		}
	} 
     document.frmEdit.submit();     
}
</script>

<table width="100%" border="1" cellpadding="1" cellspacing="1">
    <tr class="tableheader">
        <td width="100%">&nbsp;Edit Dokter Laboratorium</td>
    </tr>
</table>

<form name="frmEdit" method="POST" action="<?php echo $_SERVER["PHP_SELF"]?>">
<table width="85%" border="1" cellpadding="1" cellspacing="1">
<tr>
     <td>
     <fieldset>
     <legend><strong>Setup Dokter</strong></legend>
     <table width="100%" border="1" cellpadding="1" cellspacing="1">
          <tr>
              <td align="right" class="tablecontent" width="30%">ID Pegawai</td>
              <td width="70%">
                    <?php echo $view->RenderTextBox("dokter_nip","dokter_nip","30","100",$_POST["dokter_nip"],"inputField", null,false);?>
               </td>
          </tr>
          <tr>
              <td align="right" class="tablecontent" width="30%">Divisi</td>
              <td width="70%">
                    <?php echo $view->RenderComboBox("id_divisi","id_divisi",$opt_divisi,"inputfield");?>
               </td>
          </tr>
          <tr>
               <td align="right" class="tablecontent" width="30%"><strong>Nama<?php if(readbit($err_code,1) || readbit($err_code,2)){?>&nbsp;<font color="red">(*)</font><?}?></strong>&nbsp;</td>
               <td width="70%">
                    <?php echo $view->RenderTextBox("dokter_nama","dokter_nama","50","100",$_POST["dokter_nama"],"inputField", null,false);?>
               </td>
          </tr> 
          <tr>
               <td align="right" class="tablecontent" width="30%"><strong>Tempat Lahir&nbsp;</strong></td>
               <td width="70%">
                    <?php echo $view->RenderTextBox("dokter_tempat_lahir","dokter_tempat_lahir","20","60",$_POST["dokter_tempat_lahir"],"inputField", null,false);?>
               </td>
          </tr>
          <tr>
               <td align="right" class="tablecontent" width="30%"><strong>Tanggal Lahir&nbsp;</td>
               <td width="70%">
                    <?php echo $view->RenderTextBox("dokter_tanggal_lahir","dokter_tanggal_lahir","20","100",format_date($_POST["dokter_tanggal_lahir"]),"inputField", "readonly",false);?>
                    <img src="<?php echo $APLICATION_ROOT;?>images/b_calendar.png" width="16" height="16" align="middle" id="img_tgl" style="cursor: pointer; border: 0px solid white;" title="Date selector" onMouseOver="this.style.background='red';" onMouseOut="this.style.background=''" />
               </td>
          </tr> 
          <tr>
               <td align="right" class="tablecontent" width="30%" valign="top"><strong>Alamat&nbsp;</strong></td>
               <td width="70%">
                    <?php echo $view->RenderTextarea("dokter_alamat","dokter_alamat","3","50",$_POST["dokter_alamat"],"inputField", null,false);?>
               </td>
          </tr>
          <tr>
               <td align="right" class="tablecontent" width="30%"><strong>Kota&nbsp;</strong></td>
               <td width="70%">
                    <?php echo $view->RenderTextBox("dokter_kota","dokter_kota","20","60",$_POST["dokter_kota"],"inputField", null,false);?>
               </td>
          </tr>
          <tr>
               <td align="right" class="tablecontent" width="30%"><strong>Kodepos&nbsp;</strong></td>
               <td width="70%">
                    <?php echo $view->RenderTextBox("dokter_kodepos","dokter_kodepos","10","60",$_POST["dokter_kodepos"],"inputField", null,false);?>
               </td>
          </tr>
          <tr>
               <td align="right" class="tablecontent" width="30%"><strong>Telp. Rumah&nbsp;</strong></td>
               <td width="70%">
                    <?php echo $view->RenderTextBox("dokter_telp_rumah","dokter_telp_rumah","20","60",$_POST["dokter_telp_rumah"],"inputField", null,false);?>
               </td>
          </tr>
          <tr>
               <td align="right" class="tablecontent" width="30%"><strong>Telp. Ponsel&nbsp;</strong></td>
               <td width="70%">
                    <?php echo $view->RenderTextBox("dokter_telp_ponsel","dokter_telp_ponsel","20","60",$_POST["dokter_telp_ponsel"],"inputField", null,false);?>
               </td>
          </tr>
          <tr>
               <td align="right" class="tablecontent" width="30%"><strong>Email&nbsp;</strong></td>
               <td width="70%">
                    <?php echo $view->RenderTextBox("dokter_email","dokter_email","25","60",$_POST["dokter_email"],"inputField", null,false);?>
               </td>
          </tr>
          <tr>
               <td align="right" class="tablecontent" width="30%"><strong>Pekerjaan&nbsp;</strong></td>
               <td width="70%">
                    <?php echo $view->RenderTextBox("dokter_pekerjaan","dokter_pekerjaan","25","60",$_POST["dokter_pekerjaan"],"inputField", null,false);?>
               </td>
          </tr>
          <tr>
               <td align="right" class="tablecontent" width="30%"><strong>Jenis Kelamin&nbsp;</strong></td>
               <td width="70%">
                    <?php echo $view->RenderComboBox("dokter_sex","dokter_sex",$opt_sex,"inputField");?>
               </td>
          </tr>
          <tr>
               <td align="right" class="tablecontent" width="30%"><strong>Agama&nbsp;</strong></td>
               <td width="70%">
                    <?php echo $view->RenderComboBox("dokter_agama","dokter_agama",$opt_agama,"inputField");?>
               </td>
          </tr>
          <tr>
               <td align="right" class="tablecontent" width="30%"><strong>Status&nbsp;</strong></td>
               <td width="70%">
                    <?php echo $view->RenderComboBox("dokter_status_nikah","dokter_status_nikah",$opt_status_nikah,"inputField");?>
               </td>
          </tr>
          <tr>
               <td align="right" class="tablecontent" width="30%"><strong>Warga Negara&nbsp;</strong></td>
               <td width="70%">
                    <?php echo $view->RenderComboBox("dokter_warganegara","dokter_warganegara",$opt_warganegara,"inputField");?>
               </td>
          </tr>
          <tr>
               <td align="right" class="tablecontent" width="30%"><strong>Golongan Darah&nbsp;</strong></td>
               <td width="70%">
                    <?php echo $view->RenderComboBox("dokter_goldarah","dokter_goldarah",$opt_goldarah,"inputField");?>
               </td>
          </tr>
          <tr>
               <td align="right" class="tablecontent" width="30%"><strong>Jabatan&nbsp;</strong></td>
               <td width="70%">
                    <?php echo $view->RenderTextBox("dokter_jabatan","dokter_jabatan","30","60",$_POST["dokter_jabatan"],"inputField", null,false);?>
               </td>
          </tr>
          <tr>
               <td align="right" class="tablecontent" width="30%"><strong>Pendidikan&nbsp;</strong></td>
               <td width="70%">
                    <?php echo $view->RenderTextBox("dokter_pendidikan","dokter_pendidikan","50","60",$_POST["dokter_pendidikan"],"inputField", null,false);?>
               </td>
          </tr>
          <tr>
               <td align="right" class="tablecontent" width="30%"><strong>Gaji Pokok&nbsp;</strong></td>
               <td width="70%">
                    <?php echo $view->RenderTextBox("dokter_gajipokok","dokter_gajipokok","50","60",currency_format($_POST["dokter_gajipokok"]),"inputField", null,true);?>
               </td>
          </tr>
          <tr>
               <td colspan="2" align="right">
                    <?php echo $view->RenderButton(BTN_SUBMIT,($_x_mode == "Edit")?"btnUpdate":"btnSave","btnSave","Simpan","button",false,"onClick=\"javascript:return CheckDataSave(document.frmEdit);\"");?>
                    <?php echo $view->RenderButton(BTN_BUTTON,"btnBack","btnBack","Kembali","button",false,"onClick=\"document.location.href='".$viewPage."';\"");?>                    
               </td>
          </tr>
     </table>
     </fieldset>
     </td>
</tr>
</table>

<script>document.frmEdit.dokter_nip.focus();</script>
<script>
    Calendar.setup({
        inputField     :    "dokter_tanggal_lahir",      // id of the input field
        ifFormat       :    "<?=$formatCal;?>",       // format of the input field
        showsTime      :    false,            // will display a time selector
        button         :    "img_tgl",   // trigger for the calendar (button ID)
        singleClick    :    true,           // double-click mode
        step           :    1                // show all years in drop-down boxes (instead of every other year as default)
    });
</script>
<? if (($_x_mode == "Edit") || ($_x_mode == "Delete")) { ?>
<?php echo $view->RenderHidden("dokter_id","dokter_id",$dokterId);?>
<? } ?>
<?php echo $view->RenderHidden("x_mode","x_mode",$_x_mode);?>
</form>

<?php echo $view->RenderBodyEnd(); ?>
