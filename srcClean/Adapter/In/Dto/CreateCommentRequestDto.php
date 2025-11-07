<?php

declare(strict_types=1);

namespace Clean\Adapter\In\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateCommentRequestDto
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\Valid]
        public ?CreateCommentBodyDto $comment = null,
    ) {
    }
}
