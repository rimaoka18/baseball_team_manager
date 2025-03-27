@extends('layouts.app')

@section('content')
<div class="space-y-8">

    {{-- Header --}}
    <div class="text-center">
        <h1 class="text-lg md:text-2xl font-bold mb-3">
            {{ $game->game_date }}｜{{ $game->location }}
        </h1>
        <h2 class="text-2xl md:text-4xl font-semibold">
            Blitz Fang {{ $game->team_score }}
            <span class="text-sm text-gray-600">FINAL</span>
            {{ $game->opponent_score }} {{ $game->opponent }}
        </h2>
    </div>

    {{-- Batting Stats --}}
    <div>
        <h3 class="text-base md:text-lg font-semibold mb-2">バッティング成績</h3>
        <p class="text-xs text-gray-500 mb-2 md:hidden">👉 表は横にスクロールできます</p>

        <div class="overflow-x-auto w-full">
            <table class="min-w-[700px] w-full text-xs md:text-sm border shadow-sm rounded">
                <thead class="bg-gray-100">
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
                <tbody>
                    @foreach ($hitting as $stat)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-2 md:px-3 py-1 md:py-2 border whitespace-nowrap">{{ $stat->player->name }}</td>
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

    {{-- Pitching Stats --}}
    @php
        $pitchingWithIP = $pitching->filter(fn($stat) => $stat->innings_pitched > 0);
    @endphp

    @if ($pitchingWithIP->count() > 0)
    <div>
        <h3 class="text-base md:text-lg font-semibold mb-2">ピッチング成績</h3>
        <p class="text-xs text-gray-500 mb-2 md:hidden">👉 表は横にスクロールできます</p>

        <div class="overflow-x-auto w-full">
            <table class="min-w-[700px] w-full text-xs md:text-sm border shadow-sm rounded">
                <thead class="bg-gray-100">
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
                <tbody>
                    @foreach ($pitchingWithIP as $stat)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-2 md:px-3 py-1 md:py-2 border whitespace-nowrap">{{ $stat->player->name }}</td>
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
