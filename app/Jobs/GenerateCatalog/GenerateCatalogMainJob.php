<?php

namespace App\Jobs\GenerateCatalog;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateCatalogMainJob extends AbstractJob
{
    /**
     * @throws  \Psr\SimpleCache\InvalidArgumentException
     * @throws \Throwable
     */
    public function handle()
    {
        $this->debug('start');

        // Сначала кешируем продукты
        GenerateCatalogCacheJob::dispatchNow();

        // Затем создаем цепочку заданий формирование файлов с ценами
        $chainPrices = $this->getChainPrices();

        // Основные задачи
        $chainMain = [
            new GenerateCategoriesJob, // Генерация категорий
            new GenerateDeliveriesJob, // Генерация способов доставок
            new GeneratePointsJob,     // Генерация пунктов выдачи
        ];

        // Подзадачи которые должны выполнятся самыми последними
        $chainLast = [
            // Архивирование файлов и перенос архива в публичную папку
            new ArchiveUploadsJob,
            // Отправка уведомления сторонниму сервису о том что можно скачать
            // новый файл каталога товаров
            new SendPriceRequestJob,
        ];

        $chain = array_merge($chainPrices, $chainMain, $chainLast);

        GenerateGoodsFileJob::withChain($chain)->dispatch();
        //GenerateGoodsFileJob::dispatch()->chain($chain);

        $this->debug('finish');

    }

    /**
     * Формирование цепочек подзадач по генерации файлов с ценами
     *
     * @return array
     */
    private function getChainPrices()
    {
        $result = [];
        $products = collect([1, 2, 3, 4, 5]);
        $fileNum = 1;

        foreach ($products->chunk(1) as $chunk) {
            $result[] = new GeneratePricesFileChunkJob($chunk, $fileNum);
            $fileNum++;
        }

        return $result;
    }

}
