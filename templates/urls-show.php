<div>
    <div class="w-full">
        <section class="mb-8">
            <h1 class="text-4xl mb-4">Сайт: <?=htmlspecialchars($url)?></h1>
            <table class="table-auto w-full rounded-lg border border-gray-200 shadow-md bg-white text-left text-gray-600 divide-y divide-gray-200" data-test="url">
                <tbody>
                    <tr>
                        <td class="px-3 py-2 font-bold">ID</td>
                        <td class="px-3 py-2"><?=htmlspecialchars($id)?></td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 font-bold">Имя</td>
                        <td class="px-3 py-2"><a href="<?=htmlspecialchars($routeParser->urlFor('urls.index', ['id' => $id]))?>" rel="noopener noreferrer"></a><?=htmlspecialchars($url)?></td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 font-bold">Дата создания</td>
                        <td class="px-3 py-2"><?=htmlspecialchars($created_at)?></td>
                    </tr>
                </tbody>
            </table>
        </section>
        <section class="mb-8">
            <h2 class="text-2xl mb-4">Проверки</h2>
            <form method="POST" action="<?=htmlspecialchars($routeParser->urlFor('urls.checks.store', ['id'=> $id]))?>">
                <input class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mb-2" type="submit" value="Запустить проверку" />
            </form>
            <table class="table-auto w-full rounded-lg border border-gray-200 shadow-md bg-white text-left text-gray-600 divide-y divide-gray-200" data-test="checks">
                <thead>
                    <tr>
                        <th class="px-3 py-2">ID</th>
                        <th class="px-3 py-2">Код ответа</th>
                        <th class="px-3 py-2">h1</th>
                        <th class="px-3 py-2">title</th>
                        <th class="px-3 py-2">description</th>
                        <th class="px-3 py-2">Дата создания</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($checks as $check): ?>
                    <tr>
                        <td class="px-3 py-2"><?=htmlspecialchars($check->id)?></td>
                        <td class="px-3 py-2"><?=htmlspecialchars($check->status_code)?></td>
                        <td class="px-3 py-2"><?=htmlspecialchars($check->h1)?></td>
                        <td class="px-3 py-2"><?=htmlspecialchars($check->title)?></td>
                        <td class="px-3 py-2"><?=htmlspecialchars($check->description)?></td>
                        <td class="px-3 py-2"><?=htmlspecialchars($check->created_at)?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>
</div>