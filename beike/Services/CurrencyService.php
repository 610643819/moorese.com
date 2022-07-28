<?php
/**
 * CurrencyService.php
 *
 * @copyright  2022 opencart.cn - All Rights Reserved
 * @link       http://www.guangdawangluo.com
 * @author     TL <mengwb@opencart.cn>
 * @created    2022-07-28 16:29:54
 * @modified   2022-07-28 16:29:54
 */

namespace Beike\Services;

use Beike\Models\Address;
use Beike\Models\TaxRate;
use Beike\Models\TaxRule;
use Beike\Repositories\CurrencyRepo;

class CurrencyService
{
    private static $instance;
    private $currencies = array();

    public function __construct() {
        foreach (CurrencyRepo::enabled() as $result) {
            $this->currencies[$result->code] = $result;
        }
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function format($amount, $currency, $value = '', $format = true)
    {
        $symbol_left = $this->currencies[$currency]->symbol_left;
        $symbol_right = $this->currencies[$currency]->symbol_right;
        $decimal_place = $this->currencies[$currency]->decimal_place;

        if (!$value) {
            $value = $this->currencies[$currency]->value;
        }

        $amount = $value ? (float)$amount * $value : (float)$amount;

        $amount = round($amount, (int)$decimal_place);

        if (!$format) {
            return $amount;
        }

        $string = '';
        if ($amount < 0) {
            $string = '-';
        }

        if ($symbol_left) {
            $string .= $symbol_left;
        }

        $string .= number_format(abs($amount), (int)$decimal_place, __('currency.decimal_point'), __('currency.thousand_point'));

        if ($symbol_right) {
            $string .= $symbol_right;
        }

        return $string;
    }


    public function convert($value, $from, $to) {
        if (isset($this->currencies[$from])) {
            $from = $this->currencies[$from]->value;
        } else {
            $from = 1;
        }

        if (isset($this->currencies[$to])) {
            $to = $this->currencies[$to]->value;
        } else {
            $to = 1;
        }

        return $value * ($to / $from);
    }

}