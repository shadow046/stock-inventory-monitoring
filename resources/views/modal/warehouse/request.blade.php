<div id="requestModal" class="modal fade" >
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title w-100 text-center" id="head">STOCK REQUEST</h6>
                @if (auth()->user()->hasAnyrole('Viewer'))
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                @endif
                @if (!auth()->user()->hasAnyrole('Viewer'))
                    <button class="close cancel" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                @endif
                <button hidden class="closes" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Date requested:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="date" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Status:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="status" placeholder="1-001" disabled>
                        </div>
                    </div>
                </div>
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Branch name:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="branch" value="{{ auth()->user()->branch->branch }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Requested by:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="name" value="{{ auth()->user()->name }}" disabled>
                        </div>
                    </div>
                </div>
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Area:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="area" value="{{ auth()->user()->area->area }}" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row ticketno">
                        <label class="col-md-5 col-form-label text-md-right">Ticket no.:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="tickets" disabled>
                        </div>
                    </div>
                </div>
                <div class="row no-margin" id="clientrows" style="display:none">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Client Name:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="clients" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Customer Name:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="customers" disabled>
                        </div>
                    </div>
                </div>
                <div class="row no-margin">
                    <div class="col-md-6 form-group row">
                        <label class="col-md-5 col-form-label text-md-right">Request type:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="requesttypes" value="" disabled>
                        </div>
                    </div>
                    <div class="col-md-6 form-group row sched">
                        <label class="col-md-5 col-form-label text-md-right">Schedule on:</label>
                        <div class="col-md-7">
                            <input type="text" style="color: black" class="form-control form-control-sm " id="sched" value="" disabled>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <h5 class="modal-title w-100 text-center">REQUEST DETAILS</h5>
            </div>
            <div class="modal-body">
                <table class="table display requestDetails" style="width:100%;font-size:80%">
                    <thead class="thead-dark">
                        <th>Item Code</th>
                        <th>Description</th>
                        <th>Qty - UOM</th>
                        @if(!auth()->user()->hasrole('Viewer'))
                        <th>Stock Available</th>
                        @endif
                    </thead>
                </table>
                <br>
                <table class="table schedDetails" style="width:100%;font-size:80%">
                    <thead class="thead-dark">
                        <th>Item Code</th>
                        <th>Description</th>
                        <th>Qty - UOM</th>
                        <th>Serial</th>
                    </thead>
                </table>
                <br>
                <p class="notes" style="color:red"><b>NOTE: </b><span class="notes" style="color:black" id="notes"></span></p>
            </div>
            @if(auth()->user()->hasAnyrole('Administrator', 'Encoder'))
            <div class="modal-footer">
                <input type="button" class="btn btn-primary mr-auto" id="prcBtn" class="button" value="Proceed" disabled>
                <input type="button" id="printBtn" class="btn btn-primary mr-auto" value="PRINT">
                <input type="button" id="unresolveBtn" class="btn btn-primary mr-auto" value="UNRESOLVE" hidden>
                <input type="button" class="btn btn-primary" data-dismiss="modal" value="CLOSE">
            </div>
            @endif
        </div>
    </div>
</div>