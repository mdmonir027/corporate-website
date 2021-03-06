<?php require_once 'inc/header.php' ?>

<div class="row justify-content-between">

    <div> <h3>Add New Sliders</h3> </div>
    <div>
        <a href="sliders.php" class="btn btn-primary ">Manage Slider</a>
    </div>

</div>
<hr>

<div class="row justify-content-center">
    <div class="col-md-8 ">
        <div class="card">
            <div class="card-body">
                <form id="image-form" data-url = 'add-team-member'>
                    <div class="form-group row">
                        <label for="name" class="col-sm-2 col-form-label">Name</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="name" id="name" placeholder="Name">

                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="role" class="col-sm-2 col-form-label">Role</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="role" id="role" placeholder="Role">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="short_desc" class="col-sm-2 col-form-label">Short Desc</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="short_desc" id="short_desc" placeholder="Short Desc">
                        </div>
                    </div>


                    <div class="form-group row">
                        <label for="facebook" class="col-sm-2 col-form-label">Facebook</label>
                        <div class="col-sm-10">
                            <input type="url" class="form-control" name="facebook" id="facebook" placeholder="Facebook">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="twitter" class="col-sm-2 col-form-label">Twitter</label>
                        <div class="col-sm-10">
                            <input type="url" class="form-control" name="twitter" id="twitter" placeholder="Twitter">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="instagram" class="col-sm-2 col-form-label">Instagram</label>
                        <div class="col-sm-10">
                            <input type="url" class="form-control" name="instagram" id="instagram" placeholder="Instagram">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="linkedIn" class="col-sm-2 col-form-label">LinkedIn</label>
                        <div class="col-sm-10">
                            <input type="url" class="form-control" name="linkedIn" id="linkedIn" placeholder="LinkedIn">
                        </div>
                    </div>


                    <div class="form-group row">
                        <label for="image" class="col-sm-2 col-form-label">Image</label>
                        <div class="col-sm-10 d-flex justify-content-between">
                            <div class="">
                                <input type="file" class="form-control-file " style="outline: none" onchange="imagePreview(this , '.image-preview')" name="image" id="image">
                            </div>

                            <img src="https://via.placeholder.com/100" alt="image" class="image-preview">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="image" class="col-sm-2 col-form-label">Status</label>
                        <div class="col-sm-10 mt-2">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" checked name="status" id="active" value="1">
                                <label class="form-check-label" for="active">Active</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="inactive" value="0">
                                <label class="form-check-label" for="inactive">Inactive</label>
                            </div>

                        </div>


                    </div>
                    <div class="form-group text-right">
                        <button type="submit" class="btn btn-primary">Add Member</button>
                    </div>
                </form>
            </div>
        </div>



    </div>
</div>



<?php require_once 'inc/footer.php' ?>

