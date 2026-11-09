<html>
  <head>
    <title><?=$title?></title>
    <link href="/assets/css/main.css" rel="stylesheet" />
  </head>
  <body>
    <div class="flex flex-col min-h-screen">
        <header>
            <nav>...</nav>
        </header>
        <main class="flex-grow">
            <?php if(isset($allert)): ?>
                <div class="p-4 w-full bg-cyan-500" role="alert"><?=$allert?></div>
            <?php endif; ?>
            <?=$content?>
        </main>
        <footer>
            Все права зарезервированны
        </footer>
    </div>
  </body>
</html>