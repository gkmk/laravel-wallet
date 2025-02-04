<?php

declare(strict_types=1);

namespace Bavix\Wallet\Internal\Assembler;

use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Internal\Dto\TransactionDtoInterface;
use Bavix\Wallet\Internal\Dto\TransferLazyDto;
use Bavix\Wallet\Internal\Dto\TransferLazyDtoInterface;

final class TransferLazyDtoAssembler implements TransferLazyDtoAssemblerInterface
{
    /** @param array<mixed>|null $extra */
    public function create(
        Wallet $fromWallet,
        Wallet $toWallet,
        int $discount,
        string $fee,
        TransactionDtoInterface $withdrawDto,
        TransactionDtoInterface $depositDto,
        string $status,
        ?string $uuid,
        ?array $extra,
    ): TransferLazyDtoInterface {
        return new TransferLazyDto(
            $fromWallet,
            $toWallet,
            $discount,
            $fee,
            $withdrawDto,
            $depositDto,
            $status,
            $uuid,
            $extra,
        );
    }
}
