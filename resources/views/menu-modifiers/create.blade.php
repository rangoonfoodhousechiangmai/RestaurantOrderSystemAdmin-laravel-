@extends('layouts.app', [
    'elementActive' => 'menu-modifiers',
])

@section('content')
    <div>
        <h2 class="text-center my-3">Create Menu Modifier</h2>
        <div class="row justify-content-center mt-3 mb-5">
            <div class="col-10">
                <form action="{{ route('menus.modifiers.store', $menu) }}" method="POST" class="form-submit">
                    @csrf

                    <div class="mb-3">
                        <label for="menu_id" class="form-label fs-5">Menu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ $menu->eng_name }} / {{ $menu->mm_name }}"
                            disabled>
                        <input type="hidden" name="menu_id" value="{{ $menu->id }}">
                        <div class="invalid-feedback" data-error-for="menu_id"></div>
                    </div>

                    <div class="row mb-3">
                        <label class="form-label fs-5 mb-3">Modifiers</label>
                        {{-- @dd($menu->modifiers) --}}
                        @php
                            $index = 1;
                        @endphp
                        @foreach ($menu->modifiers as $modifier)
                            <div class="col-6 col-lg-3">
                                {{ $index }}.{{ $modifier->name }}
                                @if (!is_null($modifier->pivot->price))
                                    ({{ $modifier->pivot->price }} )
                                @endif
                            </div>
                            @php
                                $index++;
                            @endphp
                        @endforeach
                        @php
                            $groupedModifiers = $modifiers->groupBy('type');
                        @endphp
                        @foreach ($groupedModifiers as $type => $mods)
                            <h4 class="text-capitalize" style="color: var(--bs-pink)">{{ $type }}</h4>
                            <div class="row">
                                @foreach ($mods as $modifier)
                                    <div class="col-6 col-lg-3 my-2">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input modifier-checkbox"
                                                name="modifier_ids[]" value="{{ $modifier->id }}"
                                                data-type="{{ $modifier->type }}" id="modifier_{{ $modifier->id }}"
                                                @checked(in_array($modifier->id, $selectedModifierIds))>
                                            <label class="form-check-label" for="modifier_{{ $modifier->id }}">
                                                {{ $modifier->name }}
                                            </label>
                                        </div>

                                        {{-- Price input shown only for certain types, hidden by default --}}
                                        {{-- @if (in_array($modifier->type, ['protein']))
                                            @php
                                                $existingPrice = $menu->modifiers->where('id', $modifier->id)->first()?->pivot->price ?? '';
                                            @endphp
                                            <input type="number" name="modifier_price[{{ $modifier->id }}]" min="0"
                                                class="form-control modifier-price mt-2" placeholder="Enter price" value="{{ $existingPrice }}"
                                                style="display: {{ in_array($modifier->id, $selectedModifierIds) ? 'block' : 'none' }};">
                                        @endif --}}
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                    </div>

                    <div class="text-end">
                        <a href="{{ route('menus.index') }}" class="btn btn-dark">Back</a>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('js')
    <script>
        // hide price field for modifier protein
        // document.querySelectorAll('.modifier-checkbox').forEach(function(checkbox) {
        //     checkbox.addEventListener('change', function() {
        //         const priceInput = this.closest('div.col-6').querySelector('.modifier-price');
        //         if (priceInput) {
        //             priceInput.style.display = this.checked ? 'block' : 'none';
        //         }
        //     });
        // });
    </script>
@endpush
