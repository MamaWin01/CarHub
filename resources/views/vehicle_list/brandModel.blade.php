@foreach ($model as $key => $type)
    <div class="form-check">
        <input class="form-check-input other-model" type="checkbox" value="{{ $type->id }}" id="{{ $type->model }}" onclick="clickFunction('{{ $type->id }}','model',this)">
        <label class="form-check-label" for="{{ $type->model }}">{{ ucfirst($type->model) }}</label>
    </div>
@endforeach
