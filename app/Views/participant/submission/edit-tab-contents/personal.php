<div class="tab-pane active" id="personal-details" role="tabpanel">
    <form action="javascript:void(0);">
        <div class="row">
            <div class="text-center">
                <div class="profile-user position-relative d-inline-block mx-auto  mb-4">
                    <img src="/assets/images/users/avatar-1.jpg" class="rounded-circle avatar-xxl img-thumbnail user-profile-image" alt="user-profile-image">
                    <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                        <input id="profile-img-file-input" type="file" class="profile-img-file-input">
                        <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                            <span class="avatar-title rounded-circle bg-light text-body">
                                <i class="ri-camera-fill"></i>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
            <!--end profile-user-->
            <div class="col-lg-12">
                <div class="mb-3">
                    <label for="fullnameInput" class="form-label">Full Name (This will be used in your Certificates. Make sure spelling is correct)</label>
                    <input type="text" class="form-control" id="fullnameInput" placeholder="Enter your full name" value="Dave">
                </div>
            </div>
            <!--end col-->
            <!--end col-->
            <div class="col-lg-6">
                <div class="mb-3">
                    <label for="phonenumberInput" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="phonenumberInput" placeholder="Enter your phone number" value="+(1) 987 6543">
                </div>
            </div>
            <!--end col-->
            <div class="col-lg-6">
                <div class="mb-3">
                    <label for="emailInput" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="emailInput" placeholder="Enter your email" value="daveadame@velzon.com">
                </div>
            </div>
            <!--end col-->
            <div class="col-lg-12">
                <div class="mb-3">
                    <label for="JoiningdatInput" class="form-label">Joining Date</label>
                    <input type="text" class="form-control" data-provider="flatpickr" id="JoiningdatInput" data-date-format="d M, Y" data-deafult-date="24 Nov, 2021" placeholder="Select date" />
                </div>
            </div>
            <!--end col-->
            <div class="col-lg-12">
                <div class="mb-3">
                    <label for="skillsInput" class="form-label">Skills</label>
                    <select class="form-control" name="skillsInput" data-choices data-choices-text-unique-true multiple id="skillsInput">
                        <option value="illustrator">Illustrator</option>
                        <option value="photoshop">Photoshop</option>
                        <option value="css">CSS</option>
                        <option value="html">HTML</option>
                        <option value="javascript" selected>Javascript</option>
                        <option value="python">Python</option>
                        <option value="php">PHP</option>
                    </select>
                </div>
            </div>
            <!--end col-->
            <div class="col-lg-6">
                <div class="mb-3">
                    <label for="designationInput" class="form-label">Designation</label>
                    <input type="text" class="form-control" id="designationInput" placeholder="Designation" value="Lead Designer / Developer">
                </div>
            </div>
            <!--end col-->
            <div class="col-lg-6">
                <div class="mb-3">
                    <label for="websiteInput1" class="form-label">Website</label>
                    <input type="text" class="form-control" id="websiteInput1" placeholder="www.example.com" value="www.velzon.com" />
                </div>
            </div>
            <!--end col-->
            <div class="col-lg-4">
                <div class="mb-3">
                    <label for="cityInput" class="form-label">City</label>
                    <input type="text" class="form-control" id="cityInput" placeholder="City" value="California" />
                </div>
            </div>
            <!--end col-->
            <div class="col-lg-4">
                <div class="mb-3">
                    <label for="countryInput" class="form-label">Country</label>
                    <input type="text" class="form-control" id="countryInput" placeholder="Country" value="United States" />
                </div>
            </div>
            <!--end col-->
            <div class="col-lg-4">
                <div class="mb-3">
                    <label for="zipcodeInput" class="form-label">Zip Code</label>
                    <input type="text" class="form-control" minlength="5" maxlength="6" id="zipcodeInput" placeholder="Enter zipcode" value="90011">
                </div>
            </div>
            <!--end col-->
            <div class="col-lg-12">
                <div class="mb-3 pb-2">
                    <label for="exampleFormControlTextarea" class="form-label">Description</label>
                    <textarea class="form-control" id="exampleFormControlTextarea" placeholder="Enter your description" rows="3">Hi I'm Anna Adame,It will be as simple as Occidental; in fact, it will be Occidental. To an English person, it will seem like simplified English, as a skeptical Cambridge friend of mine told me what Occidental is European languages are members of the same family.</textarea>
                </div>
            </div>
            <!--end col-->
            <div class="col-lg-12">
                <div class="hstack gap-2 justify-content-end">
                    <button type="submit" class="btn btn-primary">Updates</button>
                    <button type="button" class="btn btn-soft-success">Cancel</button>
                </div>
            </div>
            <!--end col-->
        </div>
        <!--end row-->
    </form>
</div>
<!--end tab-pane-->