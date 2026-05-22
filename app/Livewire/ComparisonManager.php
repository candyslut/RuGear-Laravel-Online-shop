<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class ComparisonManager extends Component
{
    public Product $product;
    public array $comparisonItems = [];
    public bool $isInComparison = false;
    public bool $isComparisonFull = false;

    public function mount()
    {
        $comparison = session('comparison', []);
        $this->comparisonItems = $comparison;
        $this->isInComparison = in_array($this->product->id, $comparison);
        $this->isComparisonFull = count($comparison) >= 2 && !$this->isInComparison;
    }

    public function addToComparison()
    {
        $comparison = session('comparison', []);

        if (count($comparison) >= 2) {
            $this->dispatch('notification', message: 'Максимум 2 устройства для сравнения', type: 'error');
            return;
        }

        if (!empty($comparison)) {
            $existingProduct = Product::find($comparison[0]);
            if (get_class($existingProduct->specification) !== get_class($this->product->specification)) {
                $this->dispatch('notification', message: 'Можно сравнивать только устройства одного типа', type: 'error');
                return;
            }
        }

        if (!in_array($this->product->id, $comparison)) {
            $comparison[] = $this->product->id;
            session(['comparison' => $comparison]);
        }

        $this->comparisonItems = $comparison;
        $this->isInComparison = true;
        $this->isComparisonFull = count($comparison) >= 2;

        $this->dispatch('comparison-updated', items: $comparison);
        $this->dispatch('notification', message: 'Добавлено в сравнение', type: 'success');
    }

    public function removeFromComparison()
    {
        $comparison = session('comparison', []);
        $comparison = array_values(array_diff($comparison, [$this->product->id]));
        session(['comparison' => $comparison]);

        $this->comparisonItems = $comparison;
        $this->isInComparison = false;
        $this->isComparisonFull = count($comparison) >= 2;

        $this->dispatch('comparison-updated', items: $comparison);
        $this->dispatch('notification', message: 'Удалено из сравнения', type: 'success');
    }

    public function render()
    {
        return view('livewire.comparison-manager');
    }
}
