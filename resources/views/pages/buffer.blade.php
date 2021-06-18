@extends('layouts.app')

@section('content')
@if(auth()->user()->hasrole('Warehouse Manager'))
    <form class="search-form" action="#" style="margin:auto;max-width:300px">
    <input type="text" placeholder="Search.." id="searchall" size="50" autocomplete="off">
    </form>
@endif
<div class="table" id="requestdiv">
    <table class="table requestTable" id="requestTable" style="font-size:80%">
        <thead class="thead-dark">
            <tr>
                <th>
                    DATE
                </th>
                <th>
                    CATEGORY
                </th>
                <th>
                    ITEM DESCRIPTION
                </th>
                <th>
                    Quantity
                </th>
            </tr>
        </thead>
    </table>
</div>
@if(auth()->user()->hasrole('Warehouse ManageAr'))
    <div id="salltable" style="display: none">
        <table class="table searchtable" id="searchtable" style="display: none;font-size:80%;width: 100%">
            <thead class="thead-dark">
                <tr>
                    <th>
                        Date
                    </th>
                    <th>
                        Item Description
                    </th>
                    <th>
                        Serial
                    </th>
                    <th>
                        Branch
                    </th>
                    <th>
                        Prepared By
                    </th>
                </tr>
            </thead>
        </table>
    </div>
@endif
@if(auth()->user()->hasAnyRole('Warehouse Manager'))
    <div id="test" style="display:none;width:200px">
        <input type="button" id="reqBtn" class="btn btn-primary" value="SUMBIT REQUEST STOCKS">
    </div>
    <div class="d-flex">
        <input type="button" id="reqlistBtn" class="btn btn-primary ml-auto" value="VIEW REQUEST LIST">
    </div>
@endif
@endsection