		<nav class="navbar navbar-inverse" role="navigation">
			<div class="container-fluid">
				<div class="navbar-header">
					<a class="navbar-brand" href="#">
					<img alt="Brand" src="../view/image/vm-logo.png">
					</a>
				</div>
				<div>
					<ul class="nav navbar-nav">
					<?= $_SERVER['PHP_SELF'] ?>
						<li class="<?php echo $_SERVER['PHP_SELF']=="/AI/view/index.php"?"active":""; ?>"><a href="index.php">AI Test</a></li>
						<li class="<?php echo $_SERVER['PHP_SELF']=="/AI/view/training.php"?"active":""; ?>"><a href="#">AI Training</a></li>
						<li class="<?php echo $_SERVER['PHP_SELF']=="/AI/view/report.php"?"active":""; ?>"><a href="report.php">Test Report</a></li>
						<li><a href="#">ToBeContinued</a></li>
					</ul>
						<ul class="nav navbar-nav navbar-right">
						<li class="<?php echo $_SERVER['PHP_SELF']=="/AI/view/model.php"?"active":""; ?>"><a href="model.php">AI For VMware</a></li>
					</ul>
				</div>
			</div>
		</nav>