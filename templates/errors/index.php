<div class="max-w-4xl m-auto flex flex-col min-h-[80vh] justify-center items-center">
    <h1 class="text-3xl font-medium text-gray-600 mb-2"><?=htmlspecialchars($title ?? '')?></h1>
    <p class="font-medium text-gray-600"><?=htmlspecialchars($message ?? '')?></p>
    <p><?=htmlspecialchars($details ?? '')?></p>
</div>