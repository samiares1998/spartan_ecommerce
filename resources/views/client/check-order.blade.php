<x-template.layout title="{{ $title }}" >  
  <x-organisms.navbar :path="$shop->path"/>
  <x-molecules.check-order.form />
  @if(!empty($order))
    <x-molecules.check-order.data :order="$order" :orderDetail="$orderDetail" :orderTotal="$orderTotal ?? 0"/>
  @endif
  <x-organisms.footer :shop="$shop"/>
</x-template.layout>
