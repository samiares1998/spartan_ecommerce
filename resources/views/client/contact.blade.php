<x-template.layout title="{{$title}}">
  <x-organisms.navbar cartCount=10 :path="$shop->path"/>
  <x-molecules.contact.hero :shop="$shop"/>

  <x-organisms.footer :shop="$shop"/>
</x-template.layout>