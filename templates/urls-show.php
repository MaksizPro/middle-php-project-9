<div>
    <div class="w-full">
        <section class="mb-8">
            <h1 class="text-4xl mb-4">Сайт: <?=$url?></h1>
            <table class="table-auto w-full rounded-lg border border-gray-200 shadow-md bg-white text-left text-gray-600 divide-y divide-gray-200" data-test="url">
                <tbody>
                    <tr>
                        <td class="px-3 py-2 font-bold">ID</td>
                        <td class="px-3 py-2"><?=$id?></td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 font-bold">Имя</td>
                        <td class="px-3 py-2"><a href="/urls/<?=$id?>" rel="noopener noreferrer"></a><?=$url?></td>
                    </tr>
                    <tr>
                        <td class="px-3 py-2 font-bold">Дата создания</td>
                        <td class="px-3 py-2"><?=$created_at?></td>
                    </tr>
                </tbody>
            </table>
        </section>
        <section class="mb-8">
            <h2 class="text-2xl mb-4">Проверки</h2>
            <form method="POST" action="/urls/<?=$id?>/checks">
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
                        <td class="px-3 py-2"><?=$check->id?></td>
                        <td class="px-3 py-2"><?=$check->status_code?></td>
                        <td class="px-3 py-2"><?=$check->h1?></td>
                        <td class="px-3 py-2"><?=$check->title?></td>
                        <td class="px-3 py-2"><?=$check->description?></td>
                        <td class="px-3 py-2"><?=$check->created_at?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>
</div>