<?php
ini_set("display_errors", 1);
spl_autoload_register(function ($class_name) {
	include "./libs/" . $class_name . '.php';
});

	$tts = new TTS();
?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Tamriel Transport Service</title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
		<link rel="stylesheet" href="style.css">
	</head>
	<body>
		<div class="shade text-light">
			<div class="container py-4">
				<header class="pb-3 mb-4 border-bottom text-center">
					<h1>Tamriel Transport Services</h1>
					<h5>Jo'akh's traveling company</h5>
				</header>
				<div class="row mb-3">
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="autocomplete col-12">	
									<form autocomplete="off" method="GET">
										<div class="input-group">
											<input id="dest1" type="text" name="from" placeholder="Destination" class="form-control input-group-prepend" <?php echo(isset($_GET['from']) ? 'value="'.$_GET['from'].'"':null); ?>>
											<span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-ship"></i></span>
											<input id="dest2" type="text" name="dest" placeholder="Destination" class="form-control input-group-append" <?php echo(isset($_GET['dest']) ? 'value="'.$_GET['dest'].'"':null); ?>>
											<span class="input-group-text" id="basic-addon1"><i class="fa-solid fa-route"></i></span>
											<input type="submit" value="Search" class="form-control input-group-append">
										</div>
									</form>
								</div>
							</div>
							<div class="row mt-3">
								<div class="col-12">
									<div class="card bg-success">
										<div class="card-body">Zatím známé přístavy: <?=str_replace('"', '', $tts->FromDestinations());?></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php if(isset($_GET['from']) && isset($_GET['dest'])) {?>
				<div class="row">
					<div class="card">
						<div class="card-body text-dark">
							<?php foreach($tts->Route($_GET['from'], $_GET['dest']) as $path) { ?>
							<div class="card bg-warning mb-3">
								<div class="card-header"><?=str_replace("/", ' <i class="fa-solid fa-arrow-right-long"></i> ', $path->Trasa);?></div>
								<div class="card-body">
									<?php foreach($path->Useky as $u) { ?>
										<div class="card bg-primary mb-3 text-light">
											<div class="card-header">
												<?= $tts->Ico($u->Info->type);?>
												<?= str_replace("/", ' <i class="fa-solid fa-arrow-right-long"></i> ', $u->name);?>
											</div>
											<div class="card-body">
												<div class="row mb-3">
													<div class="col-3">Odjezdové místo: <?=$u->Info->from_dep;?></div>
													<div class="col-3">Příjezdové místo: <?=$u->Info->to_dep;?></div>
													<div class="col-3">Provozuje: <?=$u->Info->provozovatel;?></div>
													<div class="col-3">Cena: <?=$u->Cena;?></div>
												</div>
												<div class="row">
													<div class="col-12">
													<?=$u->Info->descr;?>
													</div>
												</div>
											</div>
										</div>
									<?php } ?>
								</div>
							</div>
							<?php } ?>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
			<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
			<script src="https://kit.fontawesome.com/d3f058f9ff.js" crossorigin="anonymous"></script>
			<script src="./scripts.js"></script>
			<script>
				var fdestinations = [<?=$tts->FromDestinations();?>];
				var tdestinations = [<?=$tts->ToDestinations();?>];
			</script>
			<script>
				autocomplete(document.getElementById("dest1"), fdestinations);
				autocomplete(document.getElementById("dest2"), tdestinations);
			</script>
		</div>  
	</body>
</html>