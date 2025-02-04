<?php

declare(strict_types=1);

namespace Bavix\Wallet\Internal\Repository;

use Bavix\Wallet\Internal\Dto\TransferDtoInterface;
use Bavix\Wallet\Internal\Query\TransferQueryInterface;
use Bavix\Wallet\Internal\Service\JsonServiceInterface;
use Bavix\Wallet\Internal\Transform\TransferDtoTransformerInterface;
use Bavix\Wallet\Models\Transfer;

final readonly class TransferRepository implements TransferRepositoryInterface
{
    public function __construct(
        private TransferDtoTransformerInterface $transformer,
        private JsonServiceInterface $jsonService,
        private Transfer $transfer
    ) {
    }

    /**
     * @param non-empty-array<int|string, TransferDtoInterface> $objects
     */
    public function insert(array $objects): void
    {
        $values = [];
        foreach ($objects as $object) {
            $values[] = array_map(
                fn ($value) => is_array($value) ? $this->jsonService->encode($value) : $value,
                $this->transformer->extract($object)
            );
        }

        $this->transfer->newQuery()
            ->insert($values)
        ;
    }

    public function insertOne(TransferDtoInterface $dto): Transfer
    {
        $attributes = $this->transformer->extract($dto);
        $instance = $this->transfer->newInstance($attributes);
        $instance->saveQuietly();

        return $instance;
    }

    /**
     * @return Transfer[]
     */
    public function findBy(TransferQueryInterface $query): array
    {
        return $this->transfer->newQuery()
            ->whereIn('uuid', $query->getUuids())
            ->get()
            ->all()
        ;
    }

    /**
     * @param non-empty-array<int> $ids
     */
    public function updateStatusByIds(string $status, array $ids): int
    {
        $connection = $this->transfer->getConnection();

        return $this->transfer->newQuery()
            ->toBase()
            ->whereIn($this->transfer->getKeyName(), $ids)
            ->update([
                'status_last' => $connection->raw('status'),
                'status' => $status,
            ])
        ;
    }
}
