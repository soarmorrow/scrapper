<!DOCTYPE html>
<!-- 
Sample code for a simple google result scrapper.
Some core codes are trimmed.
-->
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>
		<?php

		$post = filter_input_array(INPUT_POST);
		if(isset($post['q']) && $post['q']){
			$q = trim($post['q']);
			echo "Scrapping result for '{$q}'";
		}else{
			echo "Google result scrapper!";
		}
		?>
	</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Google search result scrapper">
	<meta name="author" content="SoarMorrow Solutions Pvt. Ltd">
	
	<link href="http://bootswatch.com/readable/bootstrap.min.css" rel="stylesheet">
	<link href="//cdn.datatables.net/1.10.2/css/jquery.dataTables.min.css" rel="stylesheet">
	<link href="//cdn.datatables.net/plug-ins/725b2a2115b/integration/bootstrap/3/dataTables.bootstrap.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/chosen/1.1.0/chosen.min.css">
	<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
	<link href="css/style.css" rel="stylesheet">

	<link rel="shortcut icon" href="http://soarmorrow.com/img/logo-small.png" type="image/x-icon">

	<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
  <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <![endif]-->
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/chosen/1.1.0/chosen.jquery.js"></script>
    <script type="text/javascript" src="//cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="//cdn.datatables.net/plug-ins/725b2a2115b/integration/bootstrap/3/dataTables.bootstrap.js"></script>
    <script type="text/javascript" src="js/bootstrap.min.js"></script>
    <script type="text/javascript" src="js/scripts.js"></script>
</head>

<body>
	<div class="container-fluid">
		<?php
		include_once './includes/functions.php';
		$error = array();
		if(isset($post['submit']) && $post['submit']){
			if(isset($post['q']) && $post['q']){
				$q = trim($post['q']);
			}else{
				$error[] = "Search word is empty";
			}
			$preference = '';
			if(isset($post['preference']) && $post['preference'] && $post['preference'] != 'any'){
				$preference = "+site:".trim($post['preference']);
			}
			if(empty($error)){
				$generateUrl = "http://google.com/search?q=".urlencode($q).$preference;
				$googleData = file_get_contents($generateUrl);
				$dom = new DOMDocument();
				@$dom->loadHTML($googleData);
				$xpath = new DOMXpath($dom);
				$scrap = $xpath->query('//*[@id="ires"]/ol/li');
				if(!isset($scrap) && !$scrap){
					$error[] = "Google returned an empty result for <strong><i>{$q}</i></strong>";
				}
			}
		}

		?>
		<div class="row-fluid clearfix">
			<div class="col-md-12 column">
				<div class="page-header">
					<h1>
						Scrapper <span>Scrape through Google search result</span>
					</h1>
				</div>
				<form class="form-inline" role="form" method="post">
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-addon"><i class="fa fa-edit"></i></div>
							<input class="form-control" type="text" name="q" placeholder="Search word">
						</div>
					</div>
					<div class="form-group">
						<label class="sr-only" for="exampleInputPassword2">Preference</label>
						<select name="preference" class="form-control preference">
							<option value="any">Any domain</option>
							<option value=".com">.com</option>
							<option value=".co.in">.co.in</option>
							<option value=".in">.in</option>
							<option value=".co.us">.us</option>
							<option value=".co.uk">.uk</option>
						</select>
					</div>
					<button type="submit" name="submit" value="true" class="btn btn-default"><i class="fa fa-google"></i> search</button>
				</form>

				<div class="clearfix"></div><br />
				<?php
				if (!empty($error)) {
					?>
					<div class="alert alert-danger">
						<strong>Error !</strong> Please check following errors
						<ul>
							<?php
							foreach ($error as $err) {
								echo '<li>' . $err . '</li>';
							}
							?>
						</ul>
					</div>
					<?php
				}
				?>
				<div class="clearfix"></div>
				<br />
				<table class="table table-bordered table-hover google-links">
					<thead>
						<tr>
							<th>
								#
							</th>
							<th>
								title
							</th>
							<th>
								link
							</th>
							<th>
								Status
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
						if(isset($scrap) && $scrap){
							$i=0;
							foreach ($scrap as $liNode) {
								$anchor  = $liNode->getElementsByTagName('a');
								$title = trim(str_replace("...", '', $anchor->item(0)->nodeValue));
								$images = 'Images for '.$q.$preference;
								$news = 'News for '.$q.$preference;
								$noImage = (strcmp($title, $images) != 0);
								$noNews = (strcmp($title, $news) != 0);
								if($noImage && $noNews){
									echo "<tr>";
									echo "<td>".(++$i)."</td>";
									echo "<td>".utf8_encode($title)."</td>";
									preg_match_all('/q=(.*?)&/s', $anchor->item(0)->getAttribute("href"), $matches);
									$url = $matches[1];
									echo '<td><a href="'.urldecode($url).'" class="btn btn-link">'.urldecode($url[0]).'</a></td>';
									echo '<td><span class="label label-success"><i class="fa fa-link"></i> active</span></td>';
									echo "</tr>";
								}
							}
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="row-fluid">
			<div class="col-md-12 footer pull-right">
				<strong>
					Created by <a href="http://soarmorrow.com">SorMorrow Solutions</a>
				</strong>
			</div>
		</div>
	</div>
</body>
</html>
