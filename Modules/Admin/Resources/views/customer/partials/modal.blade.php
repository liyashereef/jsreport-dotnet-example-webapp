<!-- Map Modal Start-->
<div id="mapModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" id="modal-close" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Mark Location</h4>
            </div>
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-md-8">
                            <div id="MapContainer" style="height: 500px; "></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <label>Latitude</label>
                            <input type="text" id="lat" readonly />
                        </div>
                        <div class="col-md-2">
                            <label>Longitude</label>
                            <input type="text" id="long" readonly />
                        </div>
                        <div class="col-md-2 radius" style="display:none;">
                            <label>Radius</label>
                            <input type="number" id="radius" /><br />
                            <input type="hidden" id="rowid" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" id="add-fence" style="display:none;">Add Fence</button>
                {{ Form::submit('Save', array('class'=>'button btn btn-primary','id'=>'latlong_submit'))}}
                <button class="btn btn-primary" id="modal_cancel" data-dismiss="modal" aria-hidden="true">Cancel</button>
            </div>
        </div>
    </div>
</div>
<!-- Map Modal End-->