<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3>General Settings</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="#" method="post">
                                @csrf
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="general-date">Date</label>
                                        <input type="text" class="form-control" id="general-date" placeholder="Date" name="date">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="quotations">Quotations done by the user</label>
                                        <select class="form-control" id="quotations" placeholder="Select yes or no" name="quotations">
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="sales-inv">Sales Invoice done by the user </label>
                                        <select class="form-control" id="sales-inv" placeholder="Select yes or no" name="sales_inv">
                                            <option value="yes">Yes</option>
                                            <option value="no">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="resp">Client Response</label>
                                        <input type="text" class="form-control" id="resp" name="clients_res">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="action">Action</label>
                                        <input type="text" class="form-control" id="action" name="action">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="user">User</label>
                                        <input type="text" class="form-control" id="user" name="user">
                                    </div>
                                </div>
                                <input type="submit" class="btn btn-primary" name="submit">
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3>General Settings Value</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            
                            
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<script>
    var date = new Date();
    var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());

    $('#general-date').datepicker({
        format: 'mm/dd/yyyy',
    });

    $('#general-date').datepicker('setDate', today);
</script>
