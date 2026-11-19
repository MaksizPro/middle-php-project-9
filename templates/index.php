<div>
    <div class="max-w-4xl w-full m-auto">
        <form class="bg-white shadow-md rounded px-8 pt-10 pb-8 mb-4" method="POST" action="/urls">
            <h1 class="text-5xl mb-2">Анализатор страниц</h1>
            <p class="text-xl">Бесплатно проверяйте сайты на SEO пригодность</p>
            <div class="mb-4 mt-4 flex">
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                    type="text" name="url" placeholder="https://www.example.com" value="<?= $url ?? '' ?>">
                <input class="bg-blue-500 ml-4 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit" value="Проверить" />
            </div>
        </form>
    </div>
</div>