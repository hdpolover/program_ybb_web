<div class="tab-pane fade show active" id="steparrow-personal" role="tabpanel" aria-labelledby="steparrow-personal-tab">
    <div>
        <div class="text-center">
            <div class="profile-user position-relative d-inline-block mx-auto mb-4">
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
        <div class="row">
            <div class="col-lg-12">
                <div class="mb-3">
                    <label class="form-label" for="personal-fullname">Full Name</label>
                    <input type="text" class="form-control" id="personal-fullname" placeholder="Enter your full name" required>
                    <div class="invalid-feedback">Please enter your full name</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label" for="personal-birthdate">Birth Date</label>
                    <input type="date" class="form-control" id="personal-birthdate" required>
                    <div class="invalid-feedback">Please enter your birth date</div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label" for="personal-gender">Gender</label>
                    <select class="form-select" id="personal-gender" required>
                        <option value="">Select gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                        <option value="prefer-not">Prefer not to say</option>
                    </select>
                    <div class="invalid-feedback">Please select your gender</div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="mb-3">
                    <label class="form-label" for="personal-nationality">Nationality</label>
                    <div data-input-flag data-option-flag-img-name>
                        <input type="text" class="form-control rounded-end flag-input" readonly placeholder="Select nationality" data-bs-toggle="dropdown" aria-expanded="false" />
                        <div class="dropdown-menu w-100">
                            <div class="p-2 px-3 pt-1 searchlist-input">
                                <input type="text" class="form-control form-control-sm border search-countryList" placeholder="Search country..." />
                            </div>
                            <ul class="list-unstyled dropdown-menu-list mb-0"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label" for="personal-origin-address">Origin Address</label>
                    <textarea class="form-control" id="personal-origin-address" rows="3" placeholder="Enter your origin address" required></textarea>
                    <div class="invalid-feedback">Please enter your origin address</div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label" for="personal-current-address">Current Address</label>
                    <textarea class="form-control" id="personal-current-address" rows="3" placeholder="Enter your current address" required></textarea>
                    <div class="invalid-feedback">Please enter your current address</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="mb-3">
                    <label class="form-label" for="personal-phone">Personal Phone Number</label>
                    <div class="input-group" data-input-flag>
                        <button class="btn btn-light border" type="button" data-bs-toggle="dropdown" aria-expanded="false"><img src="/assets/images/flags/us.svg" alt="flag img" height="20" class="country-flagimg rounded"><span class="ms-2 country-codeno">+ 1</span></button>
                        <input type="text" class="form-control rounded-end flag-input" id="personal-phone" value="" placeholder="Enter phone number" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" required />
                        <div class="dropdown-menu w-100">
                            <div class="p-2 px-3 pt-1 searchlist-input">
                                <input type="text" class="form-control form-control-sm border search-countryList" placeholder="Search country name or code..." />
                            </div>
                            <ul class="list-unstyled dropdown-menu-list mb-0"></ul>
                        </div>
                        <div class="invalid-feedback">Please enter your phone number</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label" for="emergency-phone">Emergency Contact Phone Number</label>
                    <div class="input-group" data-input-flag>
                        <button class="btn btn-light border" type="button" data-bs-toggle="dropdown" aria-expanded="false"><img src="/assets/images/flags/us.svg" alt="flag img" height="20" class="country-flagimg rounded"><span class="ms-2 country-codeno">+ 1</span></button>
                        <input type="text" class="form-control rounded-end flag-input" id="emergency-phone" value="" placeholder="Enter phone number" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" required />
                        <div class="dropdown-menu w-100">
                            <div class="p-2 px-3 pt-1 searchlist-input">
                                <input type="text" class="form-control form-control-sm border search-countryList" placeholder="Search country name or code..." />
                            </div>
                            <ul class="list-unstyled dropdown-menu-list mb-0"></ul>
                        </div>
                        <div class="invalid-feedback">Please enter emergency contact phone number</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label" for="emergency-relationship">Relationship</label>
                    <select class="form-select" id="emergency-relationship" required>
                        <option value="">Select relationship</option>
                        <option value="parent">Parent</option>
                        <option value="spouse">Spouse</option>
                        <option value="sibling">Sibling</option>
                        <option value="relative">Other Relative</option>
                        <option value="friend">Friend</option>
                        <option value="guardian">Legal Guardian</option>
                        <option value="other">Other</option>
                    </select>
                    <div class="invalid-feedback">Please select relationship</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label" for="personal-tshirt">T-Shirt Size</label>
                    <select class="form-select" id="personal-tshirt" required>
                        <option value="">Select T-Shirt size</option>
                        <option value="xs">XS</option>
                        <option value="s">S</option>
                        <option value="m">M</option>
                        <option value="l">L</option>
                        <option value="xl">XL</option>
                        <option value="xxl">XXL</option>
                        <option value="xxxl">XXXL</option>
                    </select>
                    <div class="invalid-feedback">Please select your T-Shirt size</div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="mb-3">
                    <label class="form-label" for="personal-disease">Disease History</label>
                    <textarea class="form-control" id="personal-disease" rows="3" placeholder="Enter your disease history or write 'None' if not applicable" required></textarea>
                    <div class="invalid-feedback">Please provide disease history information</div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex align-items-start gap-3 mt-4">
        <button type="button" class="btn btn-success btn-label right ms-auto nexttab" data-nexttab="steparrow-professional-tab"><i class="ri-arrow-right-line label-icon align-middle fs-16 ms-2"></i>Next Step</button>
    </div>
</div>