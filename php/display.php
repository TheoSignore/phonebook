<?php
	# this script diplays the users informations and creates an xml files with containing them.

	# xlsx file setting: header and style
	require '../logs/vendor/autoload.php';

	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

	$spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$sheet->getStyle('B1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('C0C0C0');
	$sheet->getStyle('B1')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
	$sheet->mergeCells('A1:A2');
	$sheet->mergeCells('B1:I1');
	$sheet->mergeCells('B2:I2');
	$sheet->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
	$sheet->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
	$sheet->setCellValue('B1', 'Owens & Minor - Movianto          ');
	$sheet->setCellValue('B2', 'File generated automaticaly by O&M Phonebook        ');
	$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
	$drawing->setName('Logo');
	$drawing->setDescription('Logo');
	$drawing->setPath('../images/OMMovianto-logo.png');
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

	# used to define the column's width
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

	# This script gets the users based on the information defined, then displays it.
	include("MySQLconnection.php");
	$param = 1;
	$sql = "SELECT idUser,nameUser,firstNameUser,title,idDep,eMail,internCisco,phoneIntern,phoneExtern,phoneMobile FROM Users INNER JOIN Site ON Users.idGroup = Site.idGroup WHERE idCountry = ?";
	if ((isset($_GET["sit"]))&&(!empty($_GET["sit"]))&&($_GET["sit"] !='')) $sql .=" and Users.idGroup= ?";
	if ((isset($_GET["dep"]))&&(!empty($_GET["dep"]))&&($_GET["dep"] !='')) $sql .=" and idDep= ?";
	if ((isset($_GET["sr"]))&&(!empty($_GET["sr"]))&&($_GET["sr"] !='')) $sql .=" and (nameUser LIKE ? OR firstNameUser LIKE ?)";
	$sql .= ";";
	$stmt = $connectionMySQL->prepare($sql);
	$stmt->bindParam($param,$_GET["cty"]);
	if ((isset($_GET["sit"]))&&(!empty($_GET["sit"]))&&($_GET["sit"] !='')) {
		$param++;
		$stmt->bindParam($param,$_GET["sit"]);
	}
	if ((isset($_GET["dep"]))&&(!empty($_GET["dep"]))&&($_GET["dep"] !='')) {
		$param++;
		$stmt->bindParam($param,$_GET["dep"]);
	}
	if ((isset($_GET["sr"]))&&(!empty($_GET["sr"]))&&($_GET["sr"] !='')) {
		$sr = $_GET['sr'];
		$sr = "%".$sr."%";
		$param++;
		$stmt->bindParam($param,$sr, PDO::PARAM_STR);
		$param++;
		$stmt->bindParam($param,$sr, PDO::PARAM_STR);
	}

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
				<th rowspan='5000' valign='top'><a href='php/contact.xlsx'><button style='height: 3vw; font-size:0.6vw;'>EXPORT<br/>TO XLSX</button><br/><img src='images/excel.png' style='height: 3.4vw;'/></a></</th>
			</tr>";
		while ($row = $stmt->fetch()) {
			$stmt2 = $connectionMySQL->prepare("SELECT typeDep FROM Dep WHERE idDep=".$row[4].";");
			$stmt2->execute();
			$row2 = $stmt2->fetch();
			$dep = $row2[0];
			$counter++;

			# filling the cells in the xlsx file
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

			# displaying the information
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

		# setting xlsx file's printing format and style
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
		$writer->save('contact.xlsx');
	}
	else echo "<span style='color: white;'>No user found.</option>"
?>