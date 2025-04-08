/*
Template Name: Velzon - Admin & Dashboard Template
Author: Themesbrand
Website: https://Themesbrand.com/
Contact: Themesbrand@gmail.com
File: flag input Js File
*/
(function () {
    ("use strict");
    var url = "/assets/json/";
    var countryListData = '';
    var getJSON = function (jsonurl, callback) {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", url + jsonurl, true);
        xhr.responseType = "json";
        xhr.onload = function () {
            var status = xhr.status;
            if (status === 200) {
                callback(null, xhr.response);
            } else {
                callback(status, xhr.response);
            }
        };
        xhr.send();
    };
    // get json
    getJSON("country-list.json", function (err, data) {
        if (err !== null) {
            console.log("Something went wrong: " + err);
        } else {
            // Fix flag image paths - add leading slash
            if (Array.isArray(data)) {
                data.forEach(function (country) {
                    if (country.flagImg && !country.flagImg.startsWith('/')) {
                        country.flagImg = '/' + country.flagImg;
                    }
                });
            }

            countryListData = data;
            loadCountryListData(countryListData);
        }
    });
    function loadCountryListData(datas) {
        var mainArray = Array.from(document.querySelectorAll("[data-input-flag]"))
        var flags = '';
        var arr = Array.from(datas);
        for (let index = 0; index < arr.length; index++) {
            flags += '<li class="dropdown-item d-flex">\
            <div class="flex-shrink-0 me-2"><img src="'+ arr[index]['flagImg'] + '" alt="country flag" class="options-flagimg" height="20"></div>\
                <div class="flex-grow-1">\
                <div class="d-flex"><div class="country-name me-1">'+ arr[index]['countryName'] + '</div><span class="countrylist-codeno text-muted">' + arr[index]['countryCode'] + '</span></div>\
            </div>\
            </li>';
        }
        for (let i = 0; i < mainArray.length; i++) {
            mainArray[i].querySelector(".dropdown-menu-list").innerHTML = '';
            mainArray[i].querySelector(".dropdown-menu-list").innerHTML = flags;
            countryListClickEvent(mainArray[i]);
        }
    };
    function countryListClickEvent(item) {
        if (item.querySelector(".country-flagimg")) {
            var countryFlagImg = item.querySelector(".country-flagimg").getAttribute('src');
        }
        Array.from(item.querySelectorAll(".dropdown-menu li")).forEach(function (subitem) {
            var optionFlagImg = subitem.querySelector(".options-flagimg").getAttribute("src");
            subitem.addEventListener("click", function () {
                var optionCodeNo = subitem.querySelector(".countrylist-codeno").innerHTML;
                if (item.querySelector("button")) {
                    item.querySelector("button img").setAttribute("src", optionFlagImg);
                    if (item.querySelector("button span")) {
                        item.querySelector("button span").innerHTML = optionCodeNo;
                    }
                }
            });
            if (countryFlagImg == optionFlagImg) {
                subitem.classList.add("active");
            }
        });
        // data option flag img with name
        Array.from(document.querySelectorAll("[data-option-flag-img-name]")).forEach(function (item) {
            var flagImg = getComputedStyle(item.querySelector(".flag-input")).backgroundImage;
            var countryFlagImg = flagImg.substring(
                flagImg.indexOf("/as") + 1,
                flagImg.lastIndexOf('"')
            );
            Array.from(item.querySelectorAll(".dropdown-menu li")).forEach(function (subitem) {
                var optionFlagImg = subitem.querySelector(".options-flagimg").getAttribute("src");
                subitem.addEventListener("click", function () {
                    var optionCountryName = subitem.querySelector(".country-name").innerHTML;
                    item.querySelector(".flag-input").style.backgroundImage = "url(" + optionFlagImg + ")";
                    item.querySelector(".flag-input").value = optionCountryName;
                });
                if (countryFlagImg == optionFlagImg) {
                    subitem.classList.add("active");
                    item.querySelector(".flag-input").value = subitem.querySelector(".country-name").innerHTML;
                }
            });
        });
        // data option flag img with name
        Array.from(document.querySelectorAll("[data-option-flag-name]")).forEach(function (item) {
            var countryName = item.querySelector(".flag-input").value;
            Array.from(item.querySelectorAll(".dropdown-menu li")).forEach(function (subitem) {
                var optionCountryName = subitem.querySelector(".country-name").innerHTML;
                subitem.addEventListener("click", function () {
                    item.querySelector(".flag-input").value = optionCountryName;
                });
                if (countryName == optionCountryName) {
                    subitem.classList.add("active");
                    item.querySelector(".flag-input").value = subitem.querySelector(".country-name").innerHTML;
                }
            });
        });
    };
    //Search bar
    Array.from(document.querySelectorAll("[data-input-flag]")).forEach(function (item) {
        var searchInput = item.querySelector(".search-countryList");
        if (searchInput) {
            searchInput.addEventListener("keyup", function () {
                var inputVal = searchInput.value.toLowerCase();
                function filterItems(arr, query) {
                    return arr.filter(function (el) {
                        return (el.countryName.toLowerCase().indexOf(query.toLowerCase()) !== -1 || el.countryCode.indexOf(query) !== -1)
                    })
                }
                var filterData = filterItems(countryListData, inputVal);
                setTimeout(function () {
                    item.querySelector(".dropdown-menu-list").innerHTML = '';
                    Array.from(filterData).forEach(function (listData) {
                        item.querySelector(".dropdown-menu-list").innerHTML +=
                            '<li class="dropdown-item d-flex">\
                        <div class="flex-shrink-0 me-2"><img src="'+ listData.flagImg + '" alt="country flag" class="options-flagimg" height="20"></div>\
                        <div class="flex-grow-1">\
                        <div class="d-flex"><div class="country-name me-1">'+ listData.countryName + '</div><span class="countrylist-codeno text-muted">' + listData.countryCode + '</span></div>\
                        </div>\
                        </li>';
                    });
                    countryListClickEvent(item);
                }, 350);
            });
        };
    });
    
    // Helper functions to get nationality and phone numbers
    window.YBBFlagInput = {
        getNationality: function() {
            // Get nationality from flag-input in elements with data-option-flag-img-name attribute
            const nationalityInput = document.querySelector('[data-option-flag-img-name] .flag-input');
            return nationalityInput ? nationalityInput.value : '';
        },

        getNationalityCode: function() {
            // Get country code from flag-input in elements with data-option-flag-img-name attribute
            const nationalityInput = document.querySelector('[data-option-flag-img-name] .flag-input');
            const countryCodeElement = document.querySelector('[data-option-flag-img-name] .countrylist-codeno');
            return countryCodeElement ? countryCodeElement.textContent.trim() : '';
        },

        getNationalityFlag: function() {
            // Get country flag name asset from flag-input in elements with data-option-flag-img-name attribute
            const nationalityInput = document.querySelector('[data-option-flag-img-name] .flag-input');
            const countryFlagElement = document.querySelector('[data-option-flag-img-name] .options-flagimg');
            return countryFlagElement ? countryFlagElement.src : '';
        },
        
        getPhoneNumber: function(inputId) {
            // Get phone number by input ID
            return this.getFullPhoneInput(inputId).full;
        },
        
        getFullPhoneInput: function(inputId) {
            // Get phone data by input ID
            const inputElement = document.getElementById(inputId);
            if (!inputElement) return { code: '', number: '', full: '' };
            
            const container = inputElement.closest('[data-input-flag]');
            if (!container) return { code: '', number: '', full: '' };
            
            const codeElement = container.querySelector('.country-codeno');
            // Keep the full country code including '+'
            const code = codeElement ? codeElement.textContent.trim() : '';
            const number = inputElement.value.trim();
            
            console.log(`YBBFlagInput: Phone data for ${inputId}:`, { code, number, full: code + number });
            
            return {
                code: code,
                number: number,
                full: code + number
            };
        },
        
        // Get all phone inputs on the page
        getAllPhoneInputs: function() {
            const result = {};
            const phoneContainers = document.querySelectorAll('[data-input-flag]');
            
            phoneContainers.forEach(container => {
                const input = container.querySelector('.flag-input');
                if (input && input.id) {
                    const codeElement = container.querySelector('.country-codeno');
                    // Keep the full country code including '+'
                    const code = codeElement ? codeElement.textContent.trim() : '';
                    const number = input.value.trim();
                    
                    result[input.id] = {
                        code: code,
                        number: number,
                        full: code + number
                    };
                }
            });
            
            console.log("YBBFlagInput: All phone inputs:", result);
            return result;
        }
    };
})();