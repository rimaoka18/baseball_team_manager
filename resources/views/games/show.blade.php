@extends('layouts.app')

@section('content')
<div class="space-y-8">
    {{-- Header --}}
    <div class="text-center">
        <h1 class="text-xl font-bold mb-3">{{ $game->game_date }}｜{{ $game->location }}</h1>
        <h2 class="text-4xl font-semibold">
            Blitz Fang {{ $game->team_score }} <span class="text-sm text-gray-600">FINAL</span> {{ $game->opponent_score }} {{ $game->opponent }}
        </h2>
    </div>

    {{-- Batting Stats --}}
    <div>
        <h3 class="text-lg font-semibold mb-2"> バッティング成績</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border shadow-sm rounded">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2 border">選手名</th>
                        <th class="px-2 py-1 border">AB</th>
                        <th class="px-2 py-1 border">R</th>
                        <th class="px-2 py-1 border">H</th>
                        <th class="px-2 py-1 border">RBI</th>
                        <th class="px-2 py-1 border">HR</th>
                        <th class="px-2 py-1 border">BB</th>
                        <th class="px-2 py-1 border">K</th>
                        <th class="px-2 py-1 border">AVG</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($hitting as $stat)
                        <tr class="border-t hover:bg-gray-50">
                            <td class="px-3 py-1 border">{{ $stat->player->name }}</td>
                            <td class="text-center px-2 py-1 border">{{ $stat->at_bats }}</td>
                            <td class="text-center px-2 py-1 border">{{ $stat->runs }}</td>
                            <td class="text-center px-2 py-1 border">{{ $stat->hits }}</td>
                            <td class="text-center px-2 py-1 border">{{ $stat->rbi }}</td>
                            <td class="text-center px-2 py-1 border">{{ $stat->home_runs }}</td>
                            <td class="text-center px-2 py-1 border">{{ $stat->walks }}</td>
                            <td class="text-center px-2 py-1 border">{{ $stat->strikeouts }}</td>
                            <td class="text-center px-2 py-1 border">
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
    <div>
        <h3 class="text-lg font-semibold mb-2"> ピッチング成績</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border shadow-sm rounded">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2 border">投手名</th>
                        <th class="px-2 py-1 border">IP</th>
                        <th class="px-2 py-1 border">H</th>
                        <th class="px-2 py-1 border">R</th>
                        <th class="px-2 py-1 border">ER</th>
                        <th class="px-2 py-1 border">BB</th>
                        <th class="px-2 py-1 border">K</th>
                        <th class="px-2 py-1 border">ERA</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pitching as $stat)
                        @if ($stat->innings_pitched > 0)
                            <tr class="border-t hover:bg-gray-50">
                                <td class="px-3 py-1 border">{{ $stat->player->name }}</td>
                                <td class="text-center px-2 py-1 border">{{ $stat->innings_pitched }}</td>
                                <td class="text-center px-2 py-1 border">{{ $stat->hits_allowed }}</td>
                                <td class="text-center px-2 py-1 border">{{ ($stat->earned_runs + ($stat->pr ?? 0)) }}</td>
                                <td class="text-center px-2 py-1 border">{{ $stat->earned_runs }}</td>
                                <td class="text-center px-2 py-1 border">{{ $stat->pitching_walks }}</td>
                                <td class="text-center px-2 py-1 border">{{ $stat->pitching_strikeouts }}</td>
                                <td class="text-center px-2 py-1 border">
                                    @if ($stat->innings_pitched > 0)
                                        {{ number_format(($stat->earned_runs * 9) / $stat->innings_pitched, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
