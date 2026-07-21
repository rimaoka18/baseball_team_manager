@extends('layouts.app')

@section('content')
<div class="space-y-8">

    {{-- Header --}}
    <div class="text-center">
        <h1 class="text-lg md:text-2xl font-bold mb-3">
            {{ $game->game_date }}｜{{ $game->location }}
        </h1>
        <h2 class="text-2xl md:text-4xl font-semibold">
            @if (is_null($game->team_score) || is_null($game->opponent_score))
                vs {{ $game->opponent }}
            @else
                Blitz Fang {{ $game->team_score }}
                <span class="text-sm text-gray-400">FINAL</span>
                {{ $game->opponent_score }} {{ $game->opponent }}
            @endif
        </h2>

        <div class="flex justify-center gap-2 mt-4">
            <a href="{{ route('games.edit', $game) }}"
                class="border border-bf-navy text-bf-navy bg-bf-cream text-sm px-4 py-1.5 rounded-lg hover:bg-bf-gold/20 transition">
                編集
            </a>
            <form action="{{ route('games.destroy', $game) }}" method="POST"
                onsubmit="return confirm('本当に削除しますか？');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="bg-red-50 text-red-600 border border-red-200 text-sm px-4 py-1.5 rounded-lg hover:bg-red-100 transition">
                    削除
                </button>
            </form>
        </div>
    </div>

    {{-- Starting Lineup (game not played yet, no box score entered) --}}
    @if ($lineups->isNotEmpty())
    <div>
        <h3 class="text-base md:text-lg font-semibold mb-2">スターティングラインナップ</h3>
        <ul class="bg-bf-cream border rounded shadow-sm divide-y text-gray-800">
            @foreach ($lineups as $lineup)
                <li class="flex items-center gap-2 px-3 py-2 text-sm">
                    <span class="inline-block w-6 text-gray-500">{{ $lineup->batting_order }}</span>
                    <span class="font-medium">{{ $lineup->player->name }}</span>
                    @if ($lineup->position)
                        <span class="text-gray-500">{{ $lineup->position }}</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Batting Stats --}}
    @if ($hitting->isNotEmpty())
    <div>
        <h3 class="text-base md:text-lg font-semibold mb-2">バッティング成績</h3>
        <p class="text-xs text-gray-500 mb-2 md:hidden">表は横にスクロールできます</p>

        <div class="overflow-x-auto w-full">
            <table class="min-w-[700px] w-full text-xs md:text-sm border shadow-sm rounded bg-bf-cream">
                <thead class="bg-bf-navy text-white">
                    <tr>
                        <th class="px-2 md:px-3 py-1 md:py-2 border whitespace-nowrap">選手名</th>
                        <th class="px-2 md:px-3 py-1 md:py-2 border whitespace-nowrap">AB</th>
                        <th class="px-2 md:px-3 py-1 md:py-2 border whitespace-nowrap">R</th>
                        <th class="px-2 md:px-3 py-1 md:py-2 border whitespace-nowrap">H</th>
                        <th class="px-2 md:px-3 py-1 md:py-2 border whitespace-nowrap">RBI</th>
                        <th class="px-2 md:px-3 py-1 md:py-2 border whitespace-nowrap">HR</th>
                        <th class="px-2 md:px-3 py-1 md:py-2 border whitespace-nowrap">BB</th>
                        <th class="px-2 md:px-3 py-1 md:py-2 border whitespace-nowrap">K</th>
                        <th class="px-2 md:px-3 py-1 md:py-2 border whitespace-nowrap">AVG</th>
                    </tr>
                </thead>
                <tbody class="text-gray-800">
                    @foreach ($hitting as $stat)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-2 md:px-3 py-1 md:py-2 border whitespace-nowrap">
                                <span class="font-medium">{{ $stat->player->name }}</span>
                                @if ($stat->position)
                                    <span class="text-gray-500 ml-1">{{ $stat->position }}</span>
                                @endif
                            </td>
                            <td class="text-center px-2 md:px-3 py-1 md:py-2 border">{{ $stat->at_bats }}</td>
                            <td class="text-center px-2 md:px-3 py-1 md:py-2 border">{{ $stat->runs }}</td>
                            <td class="text-center px-2 md:px-3 py-1 md:py-2 border">{{ $stat->hits }}</td>
                            <td class="text-center px-2 md:px-3 py-1 md:py-2 border">{{ $stat->rbi }}</td>
                            <td class="text-center px-2 md:px-3 py-1 md:py-2 border">{{ $stat->home_runs }}</td>
                            <td class="text-center px-2 md:px-3 py-1 md:py-2 border">{{ $stat->walks }}</td>
                            <td class="text-center px-2 md:px-3 py-1 md:py-2 border">{{ $stat->strikeouts }}</td>
                            <td class="text-center px-2 md:px-3 py-1 md:py-2 border">
                                @if ($stat->at_bats > 0)
                                    {{ number_format($stat->hits / $stat->at_bats, 3) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Pitching Stats --}}
    @php
        $pitchingWithIP = $pitching->filter(fn($stat) => $stat->innings_pitched > 0);
    @endphp

    @if ($pitchingWithIP->count() > 0)
    <div>
        <h3 class="text-base md:text-lg font-semibold mb-2">ピッチング成績</h3>
        <p class="text-xs text-gray-500 mb-2 md:hidden">表は横にスクロールできます</p>

        <div class="overflow-x-auto w-full">
            <table class="min-w-[700px] w-full text-xs md:text-sm border shadow-sm rounded bg-bf-cream">
                <thead class="bg-bf-navy text-white">
                    <tr>
                        <th class="px-2 md:px-3 py-1 md:py-2 border whitespace-nowrap">投手名</th>
                        <th class="px-2 md:px-3 py-1 md:py-2 border whitespace-nowrap">IP</th>
                        <th class="px-2 md:px-3 py-1 md:py-2 border whitespace-nowrap">H</th>
                        <th class="px-2 md:px-3 py-1 md:py-2 border whitespace-nowrap">R</th>
                        <th class="px-2 md:px-3 py-1 md:py-2 border whitespace-nowrap">ER</th>
                        <th class="px-2 md:px-3 py-1 md:py-2 border whitespace-nowrap">BB</th>
                        <th class="px-2 md:px-3 py-1 md:py-2 border whitespace-nowrap">K</th>
                        <th class="px-2 md:px-3 py-1 md:py-2 border whitespace-nowrap">ERA</th>
                    </tr>
                </thead>
                <tbody class="text-gray-800">
                    @foreach ($pitchingWithIP as $stat)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-2 md:px-3 py-1 md:py-2 border whitespace-nowrap">
                                <span class="font-medium">{{ $stat->player->name }}</span>
                                @if ($stat->position)
                                    <span class="text-gray-500 ml-1">{{ $stat->position }}</span>
                                @endif
                            </td>
                            <td class="text-center px-2 md:px-3 py-1 md:py-2 border">{{ $stat->innings_pitched }}</td>
                            <td class="text-center px-2 md:px-3 py-1 md:py-2 border">{{ $stat->hits_allowed }}</td>
                            <td class="text-center px-2 md:px-3 py-1 md:py-2 border">{{ ($stat->earned_runs + ($stat->pr ?? 0)) }}</td>
                            <td class="text-center px-2 md:px-3 py-1 md:py-2 border">{{ $stat->earned_runs }}</td>
                            <td class="text-center px-2 md:px-3 py-1 md:py-2 border">{{ $stat->pitching_walks }}</td>
                            <td class="text-center px-2 md:px-3 py-1 md:py-2 border">{{ $stat->pitching_strikeouts }}</td>
                            <td class="text-center px-2 md:px-3 py-1 md:py-2 border">
                                @if ($stat->innings_pitched > 0)
                                    {{ number_format(($stat->earned_runs * 9) / $stat->innings_pitched, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
