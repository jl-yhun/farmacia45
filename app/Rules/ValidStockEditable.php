<?php

namespace App\Rules;

use App\Helpers\LoggerBuilder;
use App\Producto;
use Illuminate\Contracts\Validation\InvokableRule;
use Illuminate\Support\Facades\Auth;

class ValidStockEditable implements InvokableRule
{
    private $_logger;
    public function __construct(LoggerBuilder $logger)
    {
        $this->_logger = $logger;
    }
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     * @return void
     */
    public function __invoke($attribute, $value, $fail)
    {
        $productoId = request()->route('producto');
        $product = Producto::find($productoId);

        $isValid = Auth::user()->hasRole('Admin') ||
            $value >= $product->stock;

        if (!$isValid) {
            $this->_logger
                ->info()
                ->description("Intento de actualización al stock")
                ->before($product->stock)
                ->after($value)
                ->user_id(Auth::user()->id)
                ->link_id($productoId)
                ->module($this::class)
                ->log();

            $fail('No se puede reducir el stock solo aumentar.');
        }
    }
}
