<?php

namespace App\Enums;

class InviteStatus extends Enum {
    const PENDING = 'pending';
    const CONFIRMED = 'confirmed';
    const REJECTED = 'rejected';

    public static function label($value): string
    {
        switch ($value) {
            case self::PENDING:
                return __('Pendente');
            case self::CONFIRMED:
                return __('Confirmado');
            case self::REJECTED:
                return __('Recusado');
            default:
                return parent::label($value);
        }
    }
}