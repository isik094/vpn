<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PaymentStatusEnum;
use App\helpers\StrHelper;
use App\Models\Payment;
use App\Models\Server;
use App\Models\Tariff;
use DefStudio\Telegraph\Models\TelegraphChat;
use Exception;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Платежный сервис
 *
 * @class PaymentService
 */
class PaymentService
{
    /** @var WataService Сервис платежного шлюза WATA */
    public WataService $wataService;

    public function __construct(
        public TelegraphChat $chat,
        public Tariff $tariff,
    ) {
        $this->wataService = new WataService();
    }

    /**
     * @return array
     * @throws GuzzleException
     * @throws Exception
     */
    public function create(): array
    {
        try {
            $model = new Payment();
            $model->chat_id = $this->chat->id;
            $model->tariff_id = $this->tariff->id;
            $model->setStatus(PaymentStatusEnum::OPENED);
            $model->server_id = Server::getServerId();

            if (!$model->save()) {
                throw new Exception('Unable to create payment');
            }

            $wataServiceData = $this->wataService->createLink(
                $this->tariff->amount,
                $this->getDescription(),
                (string) $model->id,
            );

            if (empty($wataServiceData)) {
                throw new Exception('Unable to create payment');
            }

            $model->terminal_name = $wataServiceData['terminalName'] ?? null;
            $model->terminal_public_id = $wataServiceData['terminalPublicId'] ?? null;
            $model->external_id = $wataServiceData['id'] ?? null;

            if (!$model->save()) {
                throw new Exception('Unable to create payment');
            }

            return $wataServiceData;
        } catch (Exception $e) {
            \Log::error("Error to create payment: {$e->getMessage()}");

            throw new Exception("Error to create payment: {$e->getMessage()}");
        }
    }

    /**
     * Описание платежа
     *
     * @return string
     */
    public function getDescription(): string
    {
        $monthName = StrHelper::declensionWord($this->tariff->count_month, __('messages.month'));
        $tariffName = "{$this->tariff->count_month} $monthName ({$this->tariff->amount} ₽)";

        return "Оплата тарифа $tariffName за ключ Outline VPN";
    }

    /**
     * Получить платежную ссылку WATA
     *
     * @param array $data
     * @return string
     * @throws Exception
     */
    public function getUrl(array $data): string
    {
        return $data['url'] ?? throw new Exception('При генерации платежной ссылки возникла ошибка.');
    }
}
