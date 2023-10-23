<?php

namespace App\Enum;


enum AnswerStatus: string
{
    case NEEDS_APPROVAL = 'needs approval';
    case SPAM = 'spam';
    case APPROVED = 'archived';

    public function isApproved(): bool
    {
        return match ($this) {
            AnswerStatus::APPROVED => true,
            default => false
        };
    }
}
