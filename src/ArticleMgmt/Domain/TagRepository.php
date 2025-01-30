<?php

namespace App\ArticleMgmt\Domain;

use App\Entity\Tag;
use Doctrine\Common\Collections\Collection;

interface TagRepository
{
    /**
     * @return Collection<Tag>
     */
    public function findOrCreateTags(string ...$tags): Collection;
}