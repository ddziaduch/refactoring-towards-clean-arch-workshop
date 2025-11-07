<?php

declare(strict_types=1);

namespace Clean\Adapter\In\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateCommentBodyDto
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly ?string $body = null,
    ) {
    }
}
