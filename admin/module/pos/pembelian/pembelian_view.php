<?php     require_once("root.inc.php");     require_once($ROOT."library/auth.cls.php");     require_once($ROOT."library/textEncrypt.cls.php");     require_once($ROOT."library/datamodel.cls.php");     require_once($ROOT."library/currFunc.lib.php");     require_once($ROOT."library/dateFunc.lib.php");     require_once($APLICATION_ROOT."library/view.cls.php");             $view = new CView($_SERVER['PHP_SELF'],$_SERVER['QUERY_STRING']);     $dtaccess = new DataAccess();     $enc = new TextEncrypt();          $auth = new CAuth();     $table = new InoTable("table","100%","left");     $editPage = "pembelian_edit.php?";     $thisPage = "pembelian_view.php?";     if(!$auth->IsAllowed("pos_pembelian",PRIV_READ)){          die("access_denied");         exit(1);              } elseif($auth->IsAllowed("pos_pembelian",PRIV_READ)===1){         echo"<script>window.parent.document.location.href='".$GLOBAL_ROOT."login.php?msg=Session Expired'</script>";          exit(1);     }               $tipe["V"] = "Volume Based";     $tipe["N"] = "Non Valume Based";          $sql = "select * from pos_pembelian order by pembelian_tanggal desc";     $rs = $dtaccess->Execute($sql);     $dataTable = $dtaccess->FetchAll($rs);          //*-- config table ---*//     $tableHeader = "&nbsp;Pembelian Item Point of Sale";          $isAllowedDel = $auth->IsAllowed("pos_pembelian",PRIV_DELETE);     $isAllowedUpdate = $auth->IsAllowed("pos_pembelian",PRIV_UPDATE);     $isAllowedCreate = $auth->IsAllowed("pos_pembelian",PRIV_CREATE);          // --- construct new table ---- //     $counterHeader = 0;     if($isAllowedDel){          $tbHeader[0][$counterHeader][TABLE_ISI] = "<input type=\"checkbox\" onClick=\"EW_selectKey(this,'cbDelete[]');\">";          $tbHeader[0][$counterHeader][TABLE_WIDTH] = "3%";          $counterHeader++;     }          if($isAllowedUpdate){          $tbHeader[0][$counterHeader][TABLE_ISI] = "Edit";          $tbHeader[0][$counterHeader][TABLE_WIDTH] = "7%";          $counterHeader++;     }          $tbHeader[0][$counterHeader][TABLE_ISI] = "Tanggal";     $tbHeader[0][$counterHeader][TABLE_WIDTH] = "10%";          $counterHeader++;          $tbHeader[0][$counterHeader][TABLE_ISI] = "Nomer";     $tbHeader[0][$counterHeader][TABLE_WIDTH] = "20%";          $counterHeader++;          $tbHeader[0][$counterHeader][TABLE_ISI] = "Toko";     $tbHeader[0][$counterHeader][TABLE_WIDTH] = "30%";          $counterHeader++;               for($i=0,$counter=0,$n=count($dataTable);$i<$n;$i++,$counter=0){          if($isAllowedDel) {               $tbContent[$i][$counter][TABLE_ISI] = '<input type="checkbox" name="cbDeletePembelian[]" value="'.$dataTable[$i]["pembelian_id"].'">';                              $tbContent[$i][$counter][TABLE_ALIGN] = "center";               $counter++;          }                    if($isAllowedUpdate) {               $tbContent[$i][$counter][TABLE_ISI] = '<a href="'.$editPage.'&id='.$enc->Encode($dataTable[$i]["pembelian_id"]).'"><img hspace="2" width="16" height="16" src="'.$APLICATION_ROOT.'images/b_edit.png" alt="Edit" title="Edit" border="0"></a>';                              $tbContent[$i][$counter][TABLE_ALIGN] = "center";               $counter++;          }               $tbContent[$i][$counter][TABLE_ISI] = format_date($dataTable[$i]["pembelian_tanggal"]);           $tbContent[$i][$counter][TABLE_ALIGN] = "center";          $counter++;                    $tbContent[$i][$counter][TABLE_ISI] = $dataTable[$i]["pembelian_nomer"];          $tbContent[$i][$counter][TABLE_ALIGN] = "center";          $counter++;                    $tbContent[$i][$counter][TABLE_ISI] = $dataTable[$i]["pembelian_toko"];          $tbContent[$i][$counter][TABLE_ALIGN] = "center";          $counter++;                        }          $colspan = count($tbHeader[0]);          if($isAllowedDel) {          $tbBottom[0][0][TABLE_ISI] = '&nbsp;&nbsp;<input type="submit" name="btnDeletePembelian" value="Hapus" class="button">&nbsp;';     }          if($isAllowedCreate) {          $tbBottom[0][0][TABLE_ISI] .= '&nbsp;&nbsp;<input type="button" name="btnAdd" value="Tambah Baru" class="button" onClick="document.location.href=\''.$editPage.'\'">&nbsp;';     }          $tbBottom[0][0][TABLE_WIDTH] = "100%";     $tbBottom[0][0][TABLE_COLSPAN] = $colspan;?><?php echo $view->RenderBody("inventori.css",false); ?><?php if ($_GET["DelBatal"]) { ?><table width="100%" border="0" cellpadding="0" cellspacing="0"><tr>	  <td colspan="2" align="left"> 	     <font color="red">Tidak dapat dihapus karena masih terdapat transaksi. Hapus transaksi melalui fasilitas edit</font>	  </td>	 	</tr></table>	<? } ?><table width="100%" border="0" cellpadding="0" cellspacing="0">     <tr class="tableheader">          <td><?php echo $tableHeader;?></td>     </tr></table><form name="frmView" method="POST" action="<?php echo $editPage; ?>">     <?php echo $table->RenderView($tbHeader,$tbContent,$tbBottom); ?></form><?php echo $view->RenderBodyEnd(); ?>