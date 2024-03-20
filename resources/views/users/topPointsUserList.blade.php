@extends('layouts.app')
@section('content')
    @php
        use App\Models\User;
        use App\Enums\UserRoleEnums;

        $students = User::students();

        // Find the authenticated user's rank position
        $authUserRank = $students->search(function ($student) {
            return $student->id == auth()->id();
        });

        // Increment by 1 because rank starts from 1 instead of 0
        $authUserRank += 1;
    @endphp

    <div class="container">
        <h3 class="text-center fw-bold py-5 fs-1 text-info">Leaderboards for Top Point Users</h3>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                @auth()
                    @if(auth()->user()->role->value === UserRoleEnums::STUDENT->value)
                        <tr class="sticky-top">
                            <td colspan="3" class="text-center fs-4 text-secondary">
                                Your Rank Position is <span class="text-forth">#{{$authUserRank}}</span>. {{-- Display authenticated user's rank position --}}
                            </td>
                        </tr>
                    @endif
                @endauth
                <tr>
                    <th class="text-center">Rank</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">Points</th>
                </tr>
                </thead>
                <tbody>
                @foreach($students as $index => $student)
                    <tr class="fs-4">
                        <td class="text-center">
                            @if($index == 0)
                                🥇 {{-- First place --}}
                            @elseif($index == 1)
                                🥈 {{-- Second place --}}
                            @elseif($index == 2)
                                🥉 {{-- Third place --}}
                            @else
                                {{$index + 1}} {{-- Other ranks --}}
                            @endif
                        </td>
                        <td>{{$student->name}}</td>
                        <td class="text-end text-warning pe-lg-5" >{{$student->points}}</td>
                    </tr>
                    @if ($index === 9)
                        <tr>
                            <td colspan="3" class="text-center fs-2 text-info">
                                <i class="bi bi-chevron-double-up me-4"></i> Top Ten List <i class="bi bi-chevron-double-up ms-4"></i>
                            </td>
                        </tr> {{-- After 10 records, add a horizontal line --}}
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
