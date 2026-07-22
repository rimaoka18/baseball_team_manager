@if ($errors->any())
<div data-auto-dismiss class="bg-bf-cream text-bf-danger border border-bf-danger/40 p-4 rounded-xl mb-4 font-medium">
    <ul class="list-disc ml-5">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
