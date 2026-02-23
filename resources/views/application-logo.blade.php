<div {{ $attributes->merge(['class' => 'flex items-center']) }}>
    <img src="{{ asset('images/logo-dark.svg') }}"
         class="h-10 w-auto block dark:hidden"
         alt="Logo">

    <img src="{{ asset('images/logo-light.svg') }}"
         class="h-10 w-auto hidden dark:block"
         alt="Logo">
</div>
