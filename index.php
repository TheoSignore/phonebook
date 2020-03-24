<?php
	# This is the index page, for those who haven't already figured it out.

	# This redirect the user to a page where he will be forced to enter a default country
	if ((!isset($_COOKIE["defaultCountry"]))||($_COOKIE["defaultCountry"] == '0')) header("location: chooseYourCountry.php");

	# connection to Phonebook MySQL database
	$log = fopen("logs/MySQLconnection.txt","a+");
	$msg = "\r\n>".date('Y-m-d G:i:s')." - MySQLconnection.php - script to connect \"Phonebook MySQL database\".\r\n";
	fputs($log,$msg);
	try {
		$usr="phonebook";
		$psswd="Movianto95";
		$connectionMySQL = new PDO("mysql:host=localhost;dbname=phonebook;charset=utf8", $usr, $psswd);
		$connectionMySQL->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$connectionMySQL->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
		fputs($log,"Connected to MySql database.\r\n");
		fclose($log);
	}
	catch(Exception $e){
	    fputs($log,"Connection to MySql database failed.\r\n".$e->getMessage().".\r\n");
	    fclose($log);
	    exit;
	}
	$sql = "SELECT idRegion FROM Country WHERE idCountry = '".$_COOKIE["defaultCountry"]."'";
	$stmt = $connectionMySQL->prepare($sql);
	$stmt->execute();
	$defCty = $stmt->fetch();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>O&M Movianto - Phonebook</title>
		<link rel="stylesheet" type="text/css" href="css/index.css">
		<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
	</head>
	<body>
		<!-- Top banner-->
		<header>
			<table class="table_header">
				<tr>
					<td style="width: 10vw"><img class="header_logo" src="images/OMMovianto-logo.png"></td>
					<td><h1>O&M Movianto&nbsp;&nbsp;&nbsp;Phonebook</h1></td>
					<td><a href="administration.php"><img title="Access to the phonebook administration" class="administration_logo" src="images/administration.png"></a></td>
				</tr>
				<tr>
					<td>
						<div style="color: white; font-family: 'Stencil'; background-color: rgb(152,1,46); border-radius: 5px; font-size: 1.3vw; padding: 0.02vw">
							THESE INFORMATIONS ARE FOR <br/><u>INTERNAL USE ONLY</u>
						</div>
					</td>
					<td>
						<!--This search form is included in the header, for responsive design-->
						<div class="search_form">
							<table style="width: 30vw;">
								<tr>
									<td style="width: 9.5vw;"><strong><img src='images/compass.svg' style='height: 1vw;'/>&nbsp;&nbsp;&nbsp;Region</strong></td>
									<td class="button_region">
										<select id="reg" autocomplete="off" onchange="dispCountry(this.value)">
											<option value=''>Select a region</option>

				<?php
					# displays the default region based on the default country cookie
					switch ($defCty["idRegion"]) {
						case 'S':
							echo "<option value='N'>North</option><option value='S' selected='selected'>South</option><option value='E'>East</option><option value='W'>West</option>";
							break;
						case 'E':
							echo "<option value='N'>North</option><option value='S'>South</option><option value='E' selected='selected'>East</option><option value='W'>West</option>";
							break;
						case 'W':
							echo "<option value='N'>North</option><option value='S'>South</option><option value='E'>East</option><option value='W' selected='selected'>West</option>";
							break;
						default:
							echo "<option value='N' selected='selected'>North</option><option value='S'>South</option><option value='E'>East</option><option value='W'>West</option>";
							break;
					}
				?>
										</select>
									</td>
								</tr>
							</table>
							<table style="width: auto;">
								<tr>
									<td style="width: 100px;"><strong>Country</strong></td>
									<td style="width: 100px;">
										<select id="cty" autocomplete="off" onchange="dispSite(this.value)">
				<?php
					# displays the default country based on the default country cookie
					$sql = "SELECT idCountry,nameCountry FROM Country WHERE idRegion = '".$defCty["idRegion"]."'";
					$stmt = $connectionMySQL->prepare($sql);
					$stmt->execute();
					while ($defCty = $stmt->fetch()) {
						echo "<option value='".$defCty["idCountry"]."'";
						if ($defCty["idCountry"] == $_COOKIE["defaultCountry"]) echo " selected='selected'";
						echo " >".$defCty["nameCountry"]."</option>";
					}
				?>
										</select>
									</td>
									<td style="width: 40px;"><strong>Site</strong></td>
									<td style="width:150px;">
										<select id="sit" onchange="Thing(this.value)" autocomplete="off">
				<?php
					# displays the default sites based on the default country cookie
					$sql = "SELECT idGroup,nameSite FROM Site WHERE idCountry = '".$_COOKIE["defaultCountry"]."'";
					$stmt = $connectionMySQL->prepare($sql);
					$stmt->execute();
					echo "<option value=''>Select a site</option>";
					while ($sitDef = $stmt->fetch()) echo "<option value='".$sitDef["idGroup"]."'>".$sitDef["nameSite"]."</option>";
				?>
										</select>
									</td>
									<td><strong> Department</strong></td>
									<td>
										<select id="dep" autocomplete="off" onchange="displayList(document.getElementById('cty').value,document.getElementById('sit').value,this.value,'')">
											<option value=''>Select a department</option>
										</select>
									</td>
								</tr>
							</table>
							<table style="width: auto;">
								<tr>
									<td style="width: 300px; text-align: right;"><strong><img src='images/loupe-icon.png' style='height: 1vw;'/>&nbsp;&nbsp;&nbsp;Search by name and firstname</strong></td>
									<td>
										<input id="srch" type="text" onkeyup="rech()" autocomplete="off"/>
									</td>
								</tr>
							</table>
						</div>
					</td>
					<td>
						<form><button onclick="resetCountryDefault()">Reset default country</button></form></a>
					</td>
				</tr>
			</table>
		</header>
		<!--Search form-->

		<!--Search results are displayed here-->
		<div id="Main">
			<table class="main" id="disp">
<?php
	# This generates the .xlsx sheet based on the default country.
	require 'logs/vendor/autoload.php';

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$sheet->getStyle('B1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('C0C0C0');
	$sheet->getStyle('B1')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
	$sheet->mergeCells('A1:A2');
	$sheet->mergeCells('B1:I1');
	$sheet->mergeCells('B2:I2');
	$sheet->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
	$sheet->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
	$sheet->setCellValue('B1', 'Owens & Minor - Movianto          ');
	$sheet->setCellValue('B2', 'File generated automaticaly by O&M Phonebook        ');
	$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
	$drawing->setName('Logo');
	$drawing->setDescription('Logo');
	$drawing->setPath('images/OMMovianto-logo.png');
	$drawing->setHeight(34);
	$drawing->setWorksheet($sheet);
	$drawing->setCoordinates('A1');
	$sheet->getStyle('A3:M3')->getFont()->getColor()->setARGB('98012E');
	$sheet->setCellValue('A3','Fam Name');
	$sheet->setCellValue('B3','Name');
	$sheet->setCellValue('C3','Title');
	$sheet->setCellValue('D3','Department');
	$sheet->setCellValue('E3','eMail');
	$sheet->setCellValue('F3','Cisco');
	$sheet->setCellValue('G3','Intern');
	$sheet->setCellValue('H3','Extern');
	$sheet->setCellValue('I3','Mobile');
	$styleArray = [
	    'borders' => [
	        'outline' => [
	            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
	            'color' => ['argb' => '000000'],
	        ],
	    ],
	];

	$sheet->getStyle('A1:I2')->applyFromArray($styleArray);
	$styleArray = [
	    'borders' => [
	        'allBorders' => [
	            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
	            'color' => ['argb' => '000000'],
	        ],
	    ],
	];

	$sheet->getStyle('A3:I3')->applyFromArray($styleArray);
	$sheet->getStyle('A3:I3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

	$counter = 3;
	$largestA = 8;
	$largestB = 5;
	$largestC = 5;
	$largestD = 10;
	$largestE = 5;
	$largestF = 5;
	$largestG = 6;
	$largestH = 6;
	$largestI = 6;

	$param = 1;
	$sql = "SELECT idUser,nameUser,firstNameUser,title,idDep,eMail,internCisco,phoneIntern,phoneExtern,phoneMobile FROM Users INNER JOIN Site ON Users.idGroup = Site.idGroup WHERE idCountry = ?;";
	$stmt = $connectionMySQL->prepare($sql);
	$stmt->bindParam($param,$_COOKIE["defaultCountry"]);
	$log = fopen("logs/general_log.txt","a+");
	fputs($log,$sql."\r\n");
	fclose($log);
	try {
		$stmt->execute();
	} catch (Exception $e) {
		echo "Failed ".$e->__toString();
	}
	if ($stmt->rowCount() != 0) {
		echo "
			<tr>
				<th>Name</th>
				<th>First Name</th>
				<th>Title</th>
				<th>Department</th>
				<th>eMail address</th>
				<th>Cisco phone</th>
				<th>Intern phone</th>
				<th>Extern phone</th>
				<th>Mobile Phone</th>
				<th rowspan='5000' valign='top'><a href='php/contact.xlsx'><button>EXPORT<br/>TO XLSX</button><br/><img src='images/excel.png' style='height: 3.4vw;'/></a></th>
			</tr>";
		while ($row = $stmt->fetch()) {
			$stmt2 = $connectionMySQL->prepare("SELECT typeDep FROM Dep WHERE idDep=".$row[4].";");
			$stmt2->execute();
			$row2 = $stmt2->fetch();
			$dep = $row2[0];
			$counter++;
			$cell = 'A'.$counter;
			if (strlen($row[1]) > $largestA) $largestA = strlen($row[1]);
			$sheet->setCellValue($cell,$row[1]);
			$cell = 'B'.$counter;
			if (strlen($row[2]) > $largestB) $largestB = strlen($row[2]);
			$sheet->setCellValue($cell,$row[2]);
			$cell = 'C'.$counter;
			if (strlen($row[3]) > $largestC) $largestC = strlen($row[3]);
			$sheet->setCellValue($cell,$row[3]);
			$cell = 'D'.$counter;
			if (strlen($dep) > $largestD) $largestD = strlen($dep);
			$sheet->setCellValue($cell,$dep);
			$cell = 'E'.$counter;
			if (strlen($row[5]) > $largestE) $largestE = strlen($row[5]);
			$sheet->setCellValue($cell,$row[5]);
			$cell = 'F'.$counter;
			if (strlen($row[6]) > $largestF) $largestF = strlen($row[6]);
			$sheet->setCellValue($cell,$row[6]);
			$cell = 'G'.$counter;
			if ($row[7] == -1) {
				$sheet->setCellValue($cell,'none');
				if (4 > $largestG) $largestG = 4;
			}
			else {
				$sheet->setCellValue($cell,$row[7]);
				if (strlen($row[7]) > $largestG) $largestG = strlen($row[7]);
			}
			$cell = 'H'.$counter;
			if (strlen($row[8]) > $largestH) $largestH = strlen($row[8]);
			$sheet->setCellValue($cell,$row[8]);
			$cell = 'I'.$counter;
			if (strlen($row[9]) > $largestI) $largestI = strlen($row[9]);
			$sheet->setCellValue($cell,$row[9]);
			echo "
					<tr>
						<td class='info'><strong>".$row[1]."</strong></td>
						<td class='info'>".$row[2]."</td>
						<td class='info2'>".$row[3]."</td>
						<td class='info2'>".$dep."</td>
						<td class='info2'>".$row[5]."</td>
						<td class='info2'>".$row[6]."</td>
						<td class='info2'>";
						if ($row[7] == -1) echo "none";
						else echo $row[7];
						echo "</td>
						<td class='info2'>".$row[8]."</td>
						<td class='info2'>".$row[9]."</td>
					</tr>";
		}
		$styleArray = [
		    'borders' => [
		        'allBorders' => [
		            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
		            'color' => ['argb' => '000000'],
		        ],
		    ],
		];
		$totalCells = 'A4:I'.$counter;
		$ttcells = "A1:I".$counter;
		$sheet->getStyle($totalCells)->applyFromArray($styleArray);
		$sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
		$sheet->getPageSetup()->setFitToPage(true);
		$sheet->getPageSetup()->setFitToWidth(1);
		$sheet->getPageMargins()->setTop(0);
		$sheet->getPageMargins()->setRight(0);
		$sheet->getPageMargins()->setLeft(0);
		$sheet->getPageMargins()->setBottom(0);
		$sheet->getStyle($ttcells)->getFont()->setSize(18);
		$largestA = $largestA*2;
		$largestB = $largestB*2;
		$largestC = $largestC*2;
		$largestD = $largestD*2;
		$largestE = $largestE*2;
		$largestF = $largestF*2;
		$largestG = $largestG*2;
		$largestH = $largestH*2;
		$largestI = $largestI*2;
		$sheet->getColumnDimension('A')->setWidth($largestA);
		$sheet->getColumnDimension('B')->setWidth($largestB);
		$sheet->getColumnDimension('C')->setWidth($largestC);
		$sheet->getColumnDimension('D')->setWidth($largestD);
		$sheet->getColumnDimension('E')->setWidth($largestE);
		$sheet->getColumnDimension('F')->setWidth($largestF);
		$sheet->getColumnDimension('G')->setWidth($largestG);
		$sheet->getColumnDimension('H')->setWidth($largestH);
		$sheet->getColumnDimension('I')->setWidth($largestI);
		$writer = new Xlsx($spreadsheet);
		$writer->save('php/contact.xlsx');
	}
	else echo "<span style='color: white;'>No user found.</option>"
?>
			</table>
		</div>
		<div id="fax">
		</div>
		<!--Including jQuey library-->
		<script type="text/javascript" src="js/jquery.js"></script>
		<footer>
			<h5>Developped by Théo Signore for O&M - Movianto</h5>
		</footer>
		<script>
			function rech(){
				displayList(document.getElementById('cty').value,document.getElementById('sit').value,document.getElementById('dep').value,document.getElementById('srch').value);
			}
			// Display country dropdown list
			function dispCountry(reg){
				if (reg == '') {
					document.getElementById("cty").innerHTML = "<option value=''>Region not defined</option>";
					document.getElementById("sit").innerHTML = "<option value=''>Region not defined</option>";
					document.getElementById("dep").innerHTML = "<option value=''>Region not defined</option>";
					document.getElementById("disp").innerHTML = "<span style='color: white;'>Region not defined.</span>";
				}
				else {
					document.getElementById("sit").innerHTML = "<option value=''>country not defined</option>";
					document.getElementById("dep").innerHTML = "<option value=''>country not defined</option>";
					document.getElementById("disp").innerHTML = "<span style='color: white;'>Country not defined.</span>";
					var xmlhttp = new XMLHttpRequest();
				    xmlhttp.onreadystatechange = function() {
				        if (this.readyState == 4 && this.status == 200) {
				            document.getElementById("cty").innerHTML = this.responseText;
				        }
				    };
				    xmlhttp.open("GET","php/modifForm.php?param=cty&&reg="+reg, true);
				    xmlhttp.send();
				}
			}

			// Display site dropdown list
			function dispSite(cty){
				document.getElementById("dep").innerHTML = "<option value=''>site not defined</option>";
				displayList(cty,'','','');
				var xmlhttp = new XMLHttpRequest();
			    xmlhttp.onreadystatechange = function() {
			        if (this.readyState == 4 && this.status == 200) {
			            document.getElementById("sit").innerHTML = this.responseText;
			        }
			    };
			    xmlhttp.open("GET","php/modifForm.php?param=sit&&cty="+cty, true);
			    xmlhttp.send();
			}

			// Display department dropdown list
			function dispDep(sit){
				dispFax(sit);
				var xmlhttp = new XMLHttpRequest();
			    xmlhttp.onreadystatechange = function() {
			        if (this.readyState == 4 && this.status == 200) {
			            document.getElementById("dep").innerHTML = this.responseText;
			        }
			    };
			    xmlhttp.open("GET","php/modifForm.php?param=dep&&sit="+sit, true);
			    xmlhttp.send();
			}
			// Display users using search form parameters.
			function displayList(cty,sit,dep,sr){
				var queryDispUsers = new XMLHttpRequest();
				queryDispUsers.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
			            document.getElementById("disp").innerHTML = this.responseText;
			        }
				};
				queryDispUsers.open("GET","php/display.php?cty="+cty+"&&sit="+sit+"&&dep="+dep+"&&sr="+sr, true);
				queryDispUsers.send();
			}
			function Thing(val){
				var cty = document.getElementById('cty').value;
				dispDep(val);
				displayList(cty,val,'','');
				dispFax(val);
			}
			function dispFax(group){
				var xmlhttp = new XMLHttpRequest();
			    xmlhttp.onreadystatechange = function() {
			        if (this.readyState == 4 && this.status == 200) {
			            document.getElementById("fax").innerHTML = this.responseText;
			        }
			    };
			    xmlhttp.open("GET","php/dispFax.php?sit="+group, true);
			    xmlhttp.send();
			}
			function resetCountryDefault(){
				var xmlhttp = new XMLHttpRequest();
			    xmlhttp.open("GET","resetCookie.php", false);
			    xmlhttp.send();
			}
		</script>
	</body>
</html>