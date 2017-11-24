<!DOCTYPE html>
<html>
	<head>
		<base href="{{ url.url }}/">
		<title>Tribal Trading</title>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<style>
			html, body {
				margin: 0;
				padding: 0;
			}

			header {
				z-index: 0;
				padding: 10px 20px;
				background-color: rgba(198, 57, 57, .8);
				border-bottom: 2px solid rgba(198, 57, 57, 1);
			}

			h1 {
				color: #fff;
				text-shadow: 2px 2px rgba(198, 57, 57, 1);
			}

			main {
				margin: 0 auto;
				margin-top: -20px;
				padding: 2em;
				width: 960px;
				background-color: #fff;
				border: 1px solid rgba(198, 57, 57, .8);
				border-bottom: 2px solid rgba(198, 57, 57, 1);
				z-index: 999;
			}
		</style>
	<body>
		<header>
			<h1>OOoops: <?php echo $exception->getMessage(); ?></h1>
		</header>
		<main>
			<h2>Thrown in:</h2>
			<h4><?php echo $exception->getFile() . ' (' . $exception->getLine() . ')'; ?></h4>
			<h2>Trace:</h2>
<?php foreach ($exception->getTrace() as $trace) : ?>
			<h4><?php echo $trace['file'] . ' (' . $trace['line'] . ')' ?></h4>
<?php endforeach; ?>
		</main>
	</body>
</html>
