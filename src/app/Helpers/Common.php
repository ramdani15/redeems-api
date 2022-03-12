<?php

if (!function_exists('format_money')) {
    /**
    * Format money
    * @param int $number
    * @param string $currency
    * @param bool $ceil
    * @return string
    */
    function format_money($number, $currency = 'Rp', $ceil = true, $comma = 2)
    {
        if ($ceil) {
            $number = ceil($number);
        }
        return "{$currency} " . number_format($number, $comma, ',', '.');
    }
}

if (!function_exists('canApi')) {
    /**
    * Check permission Api
    * @param string $permission
    * @return boolean
    */
    function canApi($permission)
    {
        return auth()->user()->can($permission);
    }
}

if (!function_exists('canApiOrAbort')) {
    /**
    * Check permission Api and abort if fail
    * @param string $permission
    * @return boolean
    */
    function canApiOrAbort($permission)
    {
        $can = canApi($permission);

        if (!$can) {
            // TODO : Fix Forbidden like abort function
            throw new Symfony\Component\HttpKernel\Exception\HttpException(403, 'You don\'t have permission', null, ['application/json'], 403);
        }
        return $can;
    }
}

if (!function_exists('calculateGiftRating')) {
    /**
    * Calculate Gift Average Rating
    * @param int $giftId
    * @return void
    */
    function calculateGiftRating($giftId)
    {
        $gift = \App\Models\Gift::find($giftId);
        if (!$gift) {
            return false;
        }
        $ratings = $gift->ratings()->sum('rating');
        $gift->rating = round($ratings * 2) / 2;
        $gift->save();
    }
}
