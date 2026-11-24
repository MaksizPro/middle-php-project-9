<div>
    <div class="w-full">
        <section>
            <h1 class="text-4xl mb-4">Сайты</h1>
            <table class="table-auto w-full rounded-lg border border-gray-200 shadow-md bg-white text-left text-gray-600 divide-y divide-gray-200" data-test="urls">
                <thead>
                    <tr>
                        <th class="px-3 py-2">ID</th>
                        <th class="px-3 py-2">Имя сайта</th>
                        <th class="px-3 py-2">Последняя проверка</th>
                        <th class="px-3 py-2">Код ответа</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($urls as $url): ?>
                    <tr>
                        <td class="px-3 py-2"><?=htmlspecialchars($url['id'])?></td>
                        <td class="px-3 py-2">
                            <a href="<?=htmlspecialchars($routeParser->urlFor('urls.show', ['id' => $url['id']]))?>" class="underline"><?=htmlspecialchars($url['name'])?></a>
                        </td>
                        <td class="px-3 py-2"><?=htmlspecialchars($url['lastCheck']['created_at'] ?? '')?></td>
                        <td class="px-3 py-2"><?=htmlspecialchars($url['lastCheck']['status_code'] ?? '')?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>
</div>