<div id="sendModal" class="modal fade" >
    <div class="modal-dialog modal-dialog-centered modal-full modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center">STOCK REQUEST FORM</h6>
                <button class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body mod" style="max-height:250px;overflow-y: auto;">
                <form id="sendForm">
                    {{ csrf_field() }}
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label for="bname" class="col-md-5 col-form-label text-md-right">Date requested:</label>
                        <div class="col-md-7">
                            <input type="text" class="form-control form-control-sm " name="date" id="sdate" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label for="reqno" class="col-md-4 col-form-label text-md-right">Request no.:</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control form-control-sm " name="reqno" id="sreqno" placeholder="1-001" disabled>
                        </div>
                    </div>
                </div>
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label for="branch" class="col-md-5 col-form-label text-md-right">Branch name:</label>
                        <div class="col-md-7">
                            <input type="text" class="form-control form-control-sm " name="branch" id="sbranch" value="{{ Auth::user()->branch->name }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label for="name" class="col-md-4 col-form-label text-md-right">Requested by:</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control form-control-sm " name="name" id="sname" value="{{ Auth::user()->name }}" disabled>
                        </div>
                    </div>
                    
                </div>
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Date schedule:</label>
                        <div class="col-md-7">
                            <input type="date" class="form-control form-control-sm" name="datesched" id="datesched">
                        </div>
                    </div>
                </div>
                <div>
                    <h5 class="modal-title w-100 text-center">REQUEST DETAILS</h5>
                </div>
                <table class="table sendDetails" style="height: 150px;">
                    <thead class="thead-dark">
                        <th>Item Code</th>
                        <th>Description</th>
                        <th>Qty</th>
                        <th>Purpose</th>
                    </thead>
                </table>
            </div>
           
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center">ENTER ITEM HERE</h6>
            </div>
            <div class="modal-body" >
                <div class="row no-margin" id="row1">
                    <div class="col-md-2 form-group">
                        <select id="category1" class="form-control category" row_count="1">
                            <option selected disabled>select category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <select id="item1" class="form-control item" row_count="1">
                            <option selected disabled>select item code</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <select id="desc1" class="form-control desc" row_count="1">
                            <option selected disabled>select description</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <select id="serial1" class="form-control serial" row_count="">
                            <option selected disabled>select serial</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <input type="number" class="form-control" name="stock1" id="stock1" placeholder="Stock" style="width: 6em" disabled>
                    </div>
                    <div class="col-md-1 form-group">
                        <input type="button" class="add_item btn btn-xs btn-primary" btn_id="1" value="Add Item">
                    </div>
                </div>
                <div class="row no-margin" id="row2">
                    <div class="col-md-2 form-group">
                        <select id="category2" class="form-control category" row_count="2">
                            <option selected disabled>select category</option>
                            @foreach ($categories as $category )
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <select id="item2" class="form-control item" row_count="2">
                            <option selected disabled>select item code</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <select id="desc2" class="form-control desc" row_count="2">
                            <option selected disabled>select description</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <select id="serial2" class="form-control serial" row_count="">
                            <option selected disabled>select serial</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <input type="number" class="form-control" name="stock2" id="stock2" placeholder="Stock" style="width: 6em" disabled>
                    </div>
                    <div class="col-md-1 form-group">
                        <input type="button" class="add_item btn btn-xs btn-primary" btn_id="2" value="Add Item">
                    </div>
                </div>
                <div class="row no-margin" id="row3">
                    <div class="col-md-2 form-group">
                        <select id="category3" class="form-control category" row_count="3">
                            <option selected disabled>select category</option>
                            @foreach ($categories as $category )
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <select id="item3" class="form-control item" row_count="3">
                            <option selected disabled>select item code</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <select id="desc3" class="form-control desc" row_count="3">
                            <option selected disabled>select description</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <select id="serial3" class="form-control serial" row_count="">
                            <option selected disabled>select serial</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <input type="number" class="form-control" name="stock3" id="stock3" placeholder="Stock" style="width: 6em" disabled>
                    </div>
                    <div class="col-md-1 form-group">
                        <input type="button" class="add_item btn btn-xs btn-primary" btn_id="3" value="Add Item">
                    </div>
                </div>
                <div class="row no-margin" id="row4">
                    <div class="col-md-2 form-group">
                        <select id="category4" class="form-control category" row_count="4">
                            <option selected disabled>select category</option>
                            @foreach ($categories as $category )
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <select id="item4" class="form-control item" row_count="4">
                            <option selected disabled>select item code</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <select id="desc4" class="form-control desc" row_count="4">
                            <option selected disabled>select description</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <select id="serial4" class="form-control serial" row_count="">
                            <option selected disabled>select serial</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <input type="number" class="form-control" name="stock4" id="stock4" placeholder="Stock" style="width: 6em" disabled>
                    </div>
                    <div class="col-md-1 form-group">
                        <input type="button" class="add_item btn btn-xs btn-primary" btn_id="4" value="Add Item">
                    </div>
                </div>
                <div class="row no-margin" id="row5">
                    <div class="col-md-2 form-group">
                        <select id="category5" class="form-control category" row_count="5">
                            <option selected disabled>select category</option>
                            @foreach ($categories as $category )
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <select id="item5" class="form-control item" row_count="5">
                            <option selected disabled>select item code</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <select id="desc5" class="form-control desc" row_count="5">
                            <option selected disabled>select description</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <select id="serial5" class="form-control serial" row_count="">
                            <option selected disabled>select serial</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <input type="number" class="form-control" name="stock5" id="stock5" placeholder="Stock" style="width: 6em" disabled>
                    </div>
                    <div class="col-md-1 form-group">
                        <input type="button" class="add_item btn btn-xs btn-primary" btn_id="5" value="Add Item">
                    </div>
                </div>
                <div class="row no-margin" id="row6">
                    <div class="col-md-2 form-group">
                        <select id="category6" class="form-control category" row_count="6">
                            <option selected disabled>select category</option>
                            @foreach ($categories as $category )
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <select id="item6" class="form-control item" row_count="6">
                            <option selected disabled>select item code</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <select id="desc6" class="form-control desc" row_count="6">
                            <option selected disabled>select description</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <select id="serial6" class="form-control serial" row_count="">
                            <option selected disabled>select serial</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <input type="number" class="form-control" name="stock6" id="stock6" placeholder="Stock" style="width: 6em" disabled>
                    </div>
                    <div class="col-md-1 form-group">
                        <input type="button" class="add_item btn btn-xs btn-primary" btn_id="6" value="Add Item">
                    </div>
                </div>
                <div class="row no-margin" id="row7">
                    <div class="col-md-2 form-group">
                        <select id="category7" class="form-control category" row_count="7">
                            <option selected disabled>select category</option>
                            @foreach ($categories as $category )
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <select id="item7" class="form-control item" row_count="7">
                            <option selected disabled>select item code</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <select id="desc7" class="form-control desc" row_count="7">
                            <option selected disabled>select description</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <select id="serial7" class="form-control serial" row_count="">
                            <option selected disabled>select serial</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <input type="number" class="form-control" name="stock7" id="stock7" placeholder="Stock" style="width: 6em" disabled>
                    </div>
                    <div class="col-md-1 form-group">
                        <input type="button" class="add_item btn btn-xs btn-primary" btn_id="7" value="Add Item">
                    </div>
                </div>
                <div class="row no-margin" id="row8">
                    <div class="col-md-2 form-group">
                        <select id="category8" class="form-control category" row_count="8">
                            <option selected disabled>select category</option>
                            @foreach ($categories as $category )
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <select id="item8" class="form-control item" row_count="8">
                            <option selected disabled>select item code</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <select id="desc8" class="form-control desc" row_count="8">
                            <option selected disabled>select description</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <select id="serial8" class="form-control serial" row_count="">
                            <option selected disabled>select serial</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <input type="number" class="form-control" name="stock8" id="stock8" placeholder="Stock" style="width: 6em" disabled>
                    </div>
                    <div class="col-md-1 form-group">
                        <input type="button" class="add_item btn btn-xs btn-primary" btn_id="8" value="Add Item">
                    </div>
                </div>
                <div class="row no-margin" id="row9">
                    <div class="col-md-2 form-group">
                        <select id="category9" class="form-control category" row_count="9">
                            <option selected disabled>select category</option>
                            @foreach ($categories as $category )
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <select id="item9" class="form-control item" row_count="9">
                            <option selected disabled>select item code</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <select id="desc9" class="form-control desc" row_count="9">
                            <option selected disabled>select description</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <select id="serial9" class="form-control serial" row_count="">
                            <option selected disabled>select serial</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <input type="number" class="form-control" name="stock9" id="stock9" placeholder="Stock" style="width: 6em" disabled>
                    </div>
                    <div class="col-md-1 form-group">
                        <input type="button" class="add_item btn btn-xs btn-primary" btn_id="9" value="Add Item">
                    </div>
                </div>
                <div class="row no-margin" id="row10">
                    <div class="col-md-2 form-group">
                        <select id="category10" class="form-control category" row_count="10">
                            <option selected disabled>select category</option>
                            @foreach ($categories as $category )
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <select id="item10" class="form-control item" row_count="10">
                            <option selected disabled>select item code</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <select id="desc10" class="form-control desc" row_count="10">
                            <option selected disabled>select description</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <select id="serial10" class="form-control serial" row_count="">
                            <option selected disabled>select serial</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <input type="number" class="form-control" name="stock10" id="stock10" placeholder="Stock" style="width: 6em" disabled>
                    </div>
                    <div class="col-md-1 form-group">
                        <input type="button" class="add_item btn btn-xs btn-primary" btn_id="10" value="Add Item">
                    </div>
                </div>
            </div><hr>
            <div class="modal-footer">
            <input type="button" class="btn btn-primary" data-dismiss="modal" value="Cancel">
            <input type="button" class="btn btn-primary sub_Btn" id="sub_Btn" class="button" value="Submit">
            </form>
                
            </div>
        </div>
    </div>
</div>