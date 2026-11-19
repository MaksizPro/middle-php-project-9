<html>
  <head>
    <title><?=$title?></title>
    <link href="/assets/css/main.css" rel="stylesheet" />
  </head>
  <body>
    <div class="flex flex-col min-h-screen max-w-7xl m-auto">
        <header class="flex px-4 py-4 gap-x-5 bg-gray-800 mb-2">
            <div class="text-gray-200">
                <a href="/">Анализатор страниц</a>
            </div>
            <nav>
                <ul class="flex gap-x-3 text-gray-400">
                    <li><a href="/">Главная</a></li>
                    <li><a href="/urls">Сайты</a></li>
                </ul>
            </nav>
        </header>
        <main class="flex-grow">
            <?php if(isset($errors) && count($errors)): ?>
            <?php foreach($errors as $alert): ?>
                <div class="p-4 w-full bg-red-400 mt-1 mb-2" role="alert"><?=$alert?></div>
            <?php endforeach; ?>
            <?php endif; ?>
            <?php if(isset($success) && count($success)): ?>
            <?php foreach($success as $alert): ?>
                <div class="p-4 w-full bg-green-400 mt-1 mb-2" role="alert"><?=$alert?></div>
            <?php endforeach; ?>
            <?php endif; ?>
            <?=$content?>
        </main>
        <footer>
            Все права зарезервированны
        </footer>
    </div>
  </body>
</html>