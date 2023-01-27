<?php
	// include '../koneksi.php';
	// include '../session.php';

	//Memanggil class aes
  include 'class/aes.class.php';
  include 'class/aesctr.class.php';

  //Memanggil class huffman
	// include 'class/huffmancoding.php';

  //Fungsi Proses Dekripsi RC4
	$pass = $_POST["katakunci"];
	$namafile = $_FILES["file"]["name"];
	function setupkey(){
		error_reporting(E_ALL ^ (E_NOTICE));
		$pass = $_POST["katakunci"];
		//echo "<br>";
		for($i=0;$i<256;$i++){
			$key[$i]=ord($pass[$i % strlen($pass)]); /*rubah ASCII ke desimal*/
		}
		global $mm;
		$mm=array();
		/*buat decrypt*/
		for($i=0;$i<256;$i++){
			$mm[$i] = $i;
		}
		$j = 0;
		$i = 0;
		for($i=0;$i<256;$i++){
			$a = $mm[$i];
			$j = ($j + $a + $key[$i]) % 256;
			$mm[$i] = $mm[$j];
			$mm[$j] = $a;
		}
	} /*akhir function*/

	function decrypt2($chipertext){
		global $mm;
		$xx=0;$yy=0;
		$plain='';
		for($n=1;$n<= strlen($chipertext);$n++){
			$xx = ($xx+1) % 256;
			$a = $mm[$xx];
			$yy = ($yy+$a) % 256;
			$mm[$xx] = $b = $mm[$yy];
			$mm[$yy] = $a;
			/*proses XOR antara chipertext dengan kunci
			dengan $chipertext sebagai chipertext
			dan $mm sebagai kunci*/
			$plain = ($chipertext^$mm[($a+$b) % 256]) % 256;
			return $plain;
		}
	}

  ini_set('memory_limit', '-1');
	ini_set('max_execution_time', '-1');

  $timer = microtime(true);

  $pw = $_POST['kunci'];
  $pt = $_FILES['file']['name'];
  $kcf = $_POST["katakunci"];
	$uploaded_name = $_FILES['file']['name'];
  $uploaded_ext = substr($uploaded_name, strrpos($uploaded_name, '.') + 1);
	$uploaded_size = $_FILES["file"]["size"];
	$dta = $_FILES["file"]["type"];

  $plain = empty($_POST['plain']) ? '' : $_POST['plain'];
  $decr = empty($_POST['decr']) ? $plain : AesCtr::decrypt($cipher, $pw, 256);

  function microtime_float(){
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
  }
  $time_start = microtime_float();

  if ($_FILES['file']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['file']['tmp_name'])){
  	$pt = file_get_contents($_FILES['file']['tmp_name']);

  	// $dekomp = HuffmanCoding::decode($pt);

    setupkey();
  	$nmfile =  "hasil/$namafile";
  	/*ambil data dari file enkripsifile*/
  	$fp = fopen($nmfile, "r");
  	$isi = fread($fp,filesize($nmfile));
  	$go = $isi;
  	$key = $kcf;

  	// Algoritma Dekripsi RC4
  	for($i=0;$i<strlen($go);$i++){
  		$b[$i]=ord($go[$i]); /*rubah ASCII ke desimal*/
  		$d[$i]=decrypt2($b[$i]); /*proses dekripsi RC4*/
  		$s[$i]=chr($d[$i]); /*rubah desimal ke ASCII*/
  	}
  	$hsl='';
  	//Hasil Dekripsi
  	for($i=0;$i<strlen($go);$i++){
  		$hsl = $hsl . $s[$i];
  	}

  	$plain = AesCtr::decrypt($hsl, $pw, 256);

  	if(strlen($pw)<8){
  		echo "<script>alert('Password Kurang dari 8 Karakter');window.location='dekrip.php';</script>";
   			return;
  	}
    if($_FILES["file"]["error"] != 0){
  		echo "<script>alert('Tidak ada file enkrip yang diupload!')</script>";
  		echo "<a href=?hal=dekrip> <button class='tombol' name ='Kembali'>Kembali</button> </a>";
  		return;
  	}
  	if(substr($uploaded_name,0,7)!="Enkrip_"){
  		echo "<script>alert('File yang dimasukan bukan hasil enkripsi')</script>";
  		echo "<a href=?hal=dekrip> <button class='tombol' name ='Kembali'>Kembali</button> </a>";
  		return;
  	}
  	if(strlen($kcf)<8){
  		echo "<script>alert('Password Kurang dari 8 karakter atau Password Kosong!')</script>";
  		echo "<a href=?hal=dekrip> <button class='tombol' name ='Kembali'>Kembali</button> </a>";
  		return;
  	}

  	move_uploaded_file($_FILES["file"]["tmp_name"],"hasil/temp");
  	$nama_file = str_replace("Enkrip", "Dekrip", $_FILES["file"]["name"]);

  	$fp = fopen("hasil/".$nama_file,"w");
  	fwrite($fp, $plain);
  	fclose($fp);
  	$time_end = microtime_float();
  	$time = $time_end - $time_start;
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Enkripsi</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Sistem Keamanan Surat Dinas</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="index.html">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Tools</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="tools_enkripsi.php">Enkripsi</a>
                        <a class="collapse-item" href="tools_dekripsi.php">Dekripsi</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Petunjuk Penggunaan</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Custom Utilities:</h6>
                        <a class="collapse-item" href="help_enkripsi.php">Enkripsi</a>
                        <a class="collapse-item" href="help_dekripsi.php">Dekripsi</a>
                    </div>
                </div>
            </li>
        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->


                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Nav Item - Alerts -->


                        <!-- Nav Item - Messages -->

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
						<!-- 
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>
-->
                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dekripsi</h1>
                    </div>

                    <!-- Content Row -->
                    <div class="row">
                        <div class="col-lg-4">

                            <!-- Custom Text Color Utilities -->


                            <!-- Custom Font Size Utilities -->


                        </div>

                        <!-- Second Column -->
                        <div class="col-lg-4">

                            <!-- Background Gradient Utilities -->


                        </div>

                        <!-- Third Column -->
                        <div class="col-lg-4">

                            <!-- Grayscale Utilities -->

                        </div>

                    </div>
                    <div id="page-wrapper">
                        <div id="page-inner">
                            <hr />
                            <div class="row">
                                <div class="alert alert-info col-lg-12">
                                    <table border="0" width="600px">
                                        <tr>
                                            <td width="150">
                                                <font color="black"><b>Nama File</b></font>
                                            </td>
                                            <td width="30">
                                                <font color="black">:</font>
                                            </td>
                                            <td width="300">
                                                <font color="black"><?php echo $_FILES["file"]["name"];?></font>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <font color="black"><b>Type File</font></b>
                                            </td>
                                            <td>
                                                <font color="black">:</font>
                                            </td>
                                            <td>
                                                <font color="black"><?php echo $_FILES["file"]["type"];?></font>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <font color="black"><b>Ukuran File</b></font>
                                            </td>
                                            <td>
                                                <font color="black">:</font>
                                            </td>
                                            <td>
                                                <font color="black"><?php echo ($_FILES["file"]["size"] / 1024);?> Kb
                                                </font>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <font color="black"><b>File Hasil</b></font>
                                            </td>
                                            <td>
                                                <font color="black">:</font>
                                            </td>
                                            <td>
                                                <font color="black"><?php echo $nama_file;?></font>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <font color="black"><b>Waktu Proses</b></font>
                                            </td>
                                            <td>
                                                <font color="black">:</font>
                                            </td>
                                            <td>
                                                <font color="black"><?php echo "$time seconds\n";?></font>
                                            </td>
                                        </tr>
                                    </table>
                                    <br />
                                    <div class="col-lg-4">
                                            <a href="tools_dekripsi.php"><button class="btn btn-primary">Kembali</button></a>
                                            <a href="<?php echo 'download.php?download_file='.$nama_file ?>"><button
                                                    class='btn btn-warning'>Download</button></a>
                                            <a href="tools_enkripsi.php"><button class="btn btn-success">Enkripsi</button></a>
                                    </div>
                                    <?php
						}else{
							echo "<script>alert('File Gagal di Enkrip');window.location = 'enkrip.php';</script>";
						}
					?>
                                </div>
                        </div>
					</div>
					</div>
					</div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->

    <div class="row">

        <!-- Area Chart -->


        <!-- Pie Chart -->


        <!-- Content Row -->
        <div class="row">



        </div>

    </div>
    <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

    <!-- Footer -->
    <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>
</body>
</html>
