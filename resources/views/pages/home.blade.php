@extends('layouts.app')

@section('content')
    @if(!auth()->user()->hasanyrole('Repair', 'Warehouse Administrator', 'Viewer', 'Viewer PLSI', 'Viewer IDSI'))
        @if(!auth()->user()->hasanyrole('Main Warehouse Manager'))
            @if (auth()->user()->branch->branch != "Conversion")
                <div class="container pt-5">
                    <div class="container-fluid">
                        <div class="row">
                            @if (!auth()->user()->hasrole('Manager', 'Editor'))
                                <div class="col-sm-2">
                                    <a href="{{ route('stock.index')}}">
                                        <div class="card bg-card">
                                            <div class="card-body text-center">
                                                <p class="card-text" style="font-size: 12px">STOCK REQUEST</p>
                                                <p class="card-text">{{ $stockreq }}</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                @if (!auth()->user()->hasanyrole('Manager', 'Editor'))
                                <div class="col-sm-2">
                                    <a href="{{ route('stocks.index')}}">
                                        <div class="card bg-card">
                                            <div class="card-body text-center">
                                                <p class="card-text" style="font-size: 12px">STOCKS</p>
                                                <p class="card-text">{{ $units }}</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                @endif
                                <div class="col-sm-2">
                                    <a href="{{ route('return.index')}}">
                                        <div class="card bg-card">
                                            <div class="card-body text-center">
                                                <p class="card-text" style="font-size: 12px">@if (auth()->user()->hasanyrole('Warehouse Manager', 'Encoder')) REPAIRED @else RETURNS @endif</p>
                                                <p class="card-text">{{ $returns }}</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                @if(auth()->user()->branch->branch != 'Warehouse' && auth()->user()->branch->branch != 'Main-Office')
                                <div class="col-sm-2">
                                    <a href="{{ route('index.service-unit')}}">
                                        <div class="card bg-card">
                                            <div class="card-body text-center">
                                                <p class="card-text" style="font-size: 12px">SERVICE OUT</p>
                                                <p class="card-text">{{ $sunits }}</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-sm-2">
                                    <a href="{{ url('loans') }}">
                                        <div class="card @if($loans > 0)bg-card-red @else bg-card @endif">
                                            <div class="card-body text-center">
                                                <p class="card-text" style="font-size: 12px">LOAN REQUEST</p>
                                                <p class="card-text">{{ $loans }}</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                @endif
                            @endif
                            @if(auth()->user()->hasanyrole('Warehouse Manager', 'Manager', 'Editor'))
                            <div class="col-sm-2">
                                <a href="{{ url('resolved') }}">
                                    <div class="card bg-card">
                                        <div class="card-body text-center" style="font-size: 12px">
                                            <p class="card-text">RESOLVED</p>
                                            <p class="card-text">@if($resolved > 0){{ $resolved }} @else {{ $resolved }} @endif</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-sm-2">
                                <a href="{{ url('request') }}">
                                    <div class="card @if($unresolved > 0)bg-card-red @else bg-card @endif"">
                                        <div class="card-body text-center">
                                            <p class="card-text" style="font-size: 12px">UNRESOLVED</p>
                                            <p class="card-text">@if($unresolved > 0){{ $unresolved }} @else {{ $unresolved }} @endif</p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @endif
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <p>USER ACTIVITIES</p>
            </li>
        </ul>
    @endif

<div class="table-responsive">
    <div style="float: right;" class="pt-3" hidden>
        <b>SEARCH&nbsp;&nbsp;</b><a href="#" id="search-ic"><i class="fa fa-lg fa-search" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </div>
    <table class="table-hover table activityTable" id="activityTable" style="font-size:80%">
        <thead class="thead-dark">
            <tr class="tbsearch" style="display:none">
                <td>
                    <input type="text" class="form-control filter-input fl-0" placeholder="id" data-column="0" style="border: 1px solid black;"/>
                </td>
                <td>
                    <input type="text" class="form-control filter-input fl-1" placeholder="Date" data-column="1" style="border: 1px solid black;" />
                </td>
                <td>
                    <input type="text" class="form-control filter-input fl-2" placeholder="Fullname" data-column="2" style="border: 1px solid black;" />
                </td>
                <td>
                    <input type="text" class="form-control filter-input fl-3" placeholder="Branch" data-column="3" style="border: 1px solid black;" />
                </td>
                <td>
                    <input type="text" class="form-control filter-input fl-3" placeholder="Activity" data-column="4" style="border: 1px solid black;" />
                </td>
            </tr>
            <tr>
                <th>
                    id
                </th><th>
                    DATE & TIME
                </th>
                <th>
                    FULLNAME
                </th>
                <th>
                    BRANCH
                </th>
                <th>
                    ACTIVITY
                </th>
            </tr>
        </thead>
    </table>
</div>
@endsection