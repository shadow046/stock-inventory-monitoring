@extends('layouts.app')

@section('content')
  <div>
    
    <div style="display: flex; justify-content: flex-end" class="pt-3">
      <a href="#" id="search-ic"><i class="fa fa-lg fa-search" aria-hidden="true"></i></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
      <a href="#" id="filter" data-placement="bottom" data-toggle="popover" data-title="Filter" data-content='@include("inc.cfilter")'><i class="fa fa-lg fa-filter" aria-hidden="true"></i></a> 
    </div>
    <table class="table" id="userTable">
      <thead class="thead-dark">
        <tr class="tbsearch">
          <td>
              <input type="text" class="form-control filter-input" placeholder="Search for name...." data-column="0" />
          </td>
          <td>
            <input type="text" class="form-control filter-input" placeholder="Search for email...." data-column="1" />
          </td>
          <td>
            <input type="text" class="form-control filter-input" placeholder="Search for area...." data-column="2" />
          </td>
          <td>
            <input type="text" class="form-control filter-input" placeholder="Search for branch...." data-column="3" />
          </td>
          <td>
            <input type="text" class="form-control filter-input" placeholder="Search for level...." data-column="4" />
          </td>
          <td>
            <input type="text" class="form-control filter-input" placeholder="Search for status...." data-column="5" />
          </td>
        </tr>
        <tr>
          <th>FULL NAME</th>
          <th>EMAIL</th>
          <th>AREA</th>
          <th>BRANCH</th>
          <th>LEVEL</th>
          <th>STATUS</th>
        </tr>
      </thead>
    </table>
  </div>
  @role('Super-admin|Admin')
    <input type="button" id="addBtn" class="button" value="New User"> 
  @endrole
    @include('modal.user')
@endsection