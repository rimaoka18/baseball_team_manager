@if ($previousGame)
    <button type="button" onclick="usePreviousLineup()"
        class="text-sm font-semibold text-bf-navy border border-bf-navy bg-bf-cream px-3 py-1 rounded-full hover:bg-bf-gold/20 transition">
        前回のスタメンを使う（{{ \Illuminate\Support\Carbon::parse($previousGame->game_date)->format('n/j') }} vs {{ $previousGame->opponent ?? '未定' }}）
    </button>
@endif
