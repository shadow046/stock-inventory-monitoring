@extends('layouts.app')

@section('content')

  <div class="table">
    <table class="table requestTable" id="requestTable">
      <thead class="thead-dark">
        <tr>
          <th>
            DATE
          </th>
          <th>
            REQUEST NO.
          </th>
          <th>
            REQUESTED BY
          </th>
          @if(Auth::user()->hasrole('Administrator'))
            <th>
              BRANCH NAME
            </th>
            <th>
              AREA
            </th>
          @endif
          <th>
            STATUS
          </th>
        </tr>
      </thead>
    </table>
  </div>
  @role('Head')
  <input type="button" id="reqBtn" class="btn btn-primary" value="REQUEST STOCKS">
  <input type="button" id="remBtn" class="btn btn-primary" value="DELETE PENDING REQUEST"> 
  @endrole
@endsection