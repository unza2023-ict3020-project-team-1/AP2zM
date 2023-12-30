<?php
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		session_start();
	}
?>

<!DOCTYPE html>
<html>
	<head>
		<!-- application title-->
		<title>Download Files</title>
		<!--<base href="/ap2zm/">-->
		<link href="style.css" rel="stylesheet" type="text/css">
		<script src="script.js"></script>
	</head>	
	<body>
		<div id="left_pane">
			<button id="hide_p" class="pane_btn" onclick="hide_pane()"><span class="pane-btn-icon">☰</span></button><br>
			<hr>
			<button class="choose" onclick="usage()">USAGE</button>
			<hr>
			<button class="choose" onclick="f_upload()">UPLOAD</button>
			<hr>
			<button class="choose" onclick="use_api()">USE API </button>
			<hr>
			<button class="choose" onclick="dwnld()">DOWNLOAD </button>
		</div>
		<div id="main">
			<button id="show_p" class="pane_btn" onclick="show_pane()" style="display: none;"><span class="pane-btn-icon">☰</span></button>
			<div id="dwnld_sect">
				<?php
					if ($_SERVER['REQUEST_METHOD'] == 'POST') {
						// The session ID is created and can be accessed via the session_id() function
						$userDir = "user_data/".session_id();
						$num = 0;
						$checkDir = $userDir;
						while(file_exists($checkDir)){
							$checkDir = $userDir.$num;
							$num++;
						}
						$userDir = $checkDir;
						mkdir($userDir, 0777, true);
						$pdfDir = $userDir."/pdfFiles";
						$metaDir = $userDir."/metaFiles";
						mkdir($pdfDir, 0777, true);
						mkdir($metaDir, 0777, true);

						// Handle file upload
						$pdfNames = [];
						for($i = 0; $i < count($_FILES['pdfFile']['name']); $i++){
							move_uploaded_file($_FILES['pdfFile']['tmp_name'][$i], $pdfDir."/".$_FILES['pdfFile']['name'][$i]);
							$pdfNames[] = $pdfDir."/".$_FILES['pdfFile']['name'][$i];
						}
						$metaNames = [];
						for($i = 0; $i < count($_FILES['metaFile']['name']); $i++){
							move_uploaded_file($_FILES['metaFile']['tmp_name'][$i], $metaDir."/".$_FILES['metaFile']['name'][$i]);
							$metaNames[] = $metaDir."/".$_FILES['metaFile']['name'][$i];
						}
						$zip = new ZipArchive();
						$zipName = $userDir."/ap2zm.zip";

						if ($zip->open($zipName, ZipArchive::CREATE) === TRUE) {
							for($i = 0; $i < count($pdfNames); $i++){
								$zip->addFile($pdfNames[$i], basename($pdfNames[$i]));
							}
							echo "<h1 class=\"choice\">FILES READY</h1>";
							echo "<a href=\"".$zipName."\" download>Click to Download ZIP file</a>";
							$zip->close();
						}
					//add metadata crosswalking code
					}
				?>
			</div>

			<div id="default" style="display: none;">
			    <h1 class="choice">USAGE</h1>
			    <h3>1. USING APIs</h3>
				To use API endpoints for the files and their metadata, the app give you a option to use those to fetch(by enterring the urls for document and metadata urls in the respective fields) and download your zipped files by clicking download.
				<br><br>
				
			    <h3>2. UPLOADING FILES</h3>
				<p>there are two icons, the left one with a PDF symbol is for documents and the other one is for the corresponding metadata.</p><br>
				i. Click on the document icon to select a file to upload from the local storage, make sure to also select the correspoding metadata
				<br><br>
				ii. To add more files, click the addmore button after selecting files.
				<br><br>
				iii. after selection of files is done, click uoload, the system will zip the documents, map the metadata to CSV format, and then put the CSV fomarted metadata into one CSV file, after which the user can download the ZIP and CSV files.
			</div>
			<div id="api_sect" class="sect" style="display:none;">
				<br>
				<h1 class="choice">USE API</h1>
				<!-- This form allows users to enter API-->
				<form onsubmit="fetchPDF(); return false;" method="post" action="api_form_process.php">
					<label for="docURL" class="apiLabel">Documents Url</label>
					<input type="text" id="docURL" name="docURL" class="apiURL" placeholder="Enter valid Documents API URL" required><br><br>
					<label for="metaURL" class="apiLabel" >Metadata Url</label>
					<input type="text" id="metaURL" name="metaURL" class="apiURL" placeholder="Enter valid Metadata API URL" required><br><br><br>
					<input type="submit" class="next-btn" value="fetch"><br><br><br>
				</form>
			</div>
			
			<!-- upload header 1 tag title -->
			<div id="file_sect" class="sect" style="display:none;">
				<h1 class="choice">UPLOAD FILES</h1>
				<!-- div to create listing table using javascript-->
				<div id="files-list">
					<h1 id="listMsg" style="font-size: 200%;"> No Files Selected</h1>
				</div><br>
				<!-- This form allows users to upload files and will be submitted to "upload.php" -->
				<form action="file_upload.php" method="post" id="fileInputForm" enctype="multipart/form-data">
					<label for="pdfI0" id="pdfL"><img  src="pdf.png" style="width:20%;" alt="upload pdf"></label>
					<input type="file" name="pdfFile[]" id="pdfI0" style="display: none;" multiple>
					<label for="metaI0" id="metaL" style="padding-left: 28%"><img src="meta.png" style="width:24%;" alt="upload metadata"></label>
					<input type="file" name="metaFile[]" id="metaI0" style="display: none;" multiple>
					<p style="margin-top: -1.3vw;"><span style="display: inline-block; text-align: center; width: 20%;  font-weight: 500;">document</span><span style="padding-left: 28.8%; text-align: center; display: inline-block; width: 25%; font-weight: 500;">metadata</span></p>
					<br><br>
					<button type="button" id="addmore" class="next-btn" onclick="addFiles()">Add Files</button>
					<br><br><br>
					<button type="submit" class="next-btn">Upload</button>
					<br><br>
				</form>
			</div>
		</div>
		<script>
			squareIt(".pane_btn");
		</script>
	</body>
</html>
