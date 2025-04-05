<?= $this->include('partials/main') ?>

<head>

    <!-- Title Meta -->
    <?= $this->include('partials/title-meta', [
        'meta_title' => "Program Insights - ",
    ]) ?>

    <!--Swiper slider css-->
    <link href="/assets/libs/swiper/swiper-bundle.min.css" rel="stylesheet" type="text/css" />

    <?= $this->include('partials/head-css') ?>

    <!--datatable css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" />
    <!--datatable responsive css-->
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" />

</head>

<body data-bs-spy="scroll" data-bs-target="#navbar-example">

    <!-- Begin page -->
    <div class="layout-wrapper landing">
        <?= $this->include('landing/common/navbar') ?>

        <!-- start Insights title section -->
        <section class="section position-relative pb-5" id="insights-title" style="background-color: #f8f9fa;">
            <div class="bg-overlay bg-overlay-pattern opacity-50"></div>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="text-center pt-5 mt-5">
                            <h1 class="mb-3 ff-secondary fw-semibold text-capitalize lh-base">Program Insights</h1>
                            <p class="text-muted fs-16">Discover comprehensive analytics and performance statistics highlighting our programs.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- end title section -->
        <!-- start Insights section -->
        <section class="section py-5 position-relative" id="insights">
            <div class="container">

                <h2 class="fw-semibold mt-5 mb-5"><?= $program['name'] ?? 'Program Insights' ?></h2>

                <!-- Key Metrics Row with Counter Cards -->
                <div class="row mb-3">
                    <div class="col-lg-3 col-md-6">
                        <div class="card card-animate border-0 overflow-hidden">
                            <div class="card-body bg-soft-primary">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="fs-15 text-uppercase fw-semibold mb-3">Total Participants</h5>
                                        <h2 class="counter-value mb-0" data-target="<?= $totalParticipants ?? 0 ?>">0</h2>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <div class="avatar-title bg-primary text-white rounded-circle fs-3">
                                            <i class="ri-user-star-line"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <div class="card card-animate border-0 overflow-hidden">
                            <div class="card-body bg-soft-danger">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h5 class="fs-15 text-uppercase fw-semibold mb-3">Total Countries</h5>
                                        <h2 class="counter-value mb-0" data-target="<?= $totalCountries ?? 0 ?>">0</h2>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <div class="avatar-title bg-primary text-white rounded-circle fs-3">
                                            <i class="ri-global-line"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- World Map Visualization -->
                <div class="row mb-3">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Participant Distribution by Country</h4>
                            </div>
                            <div class="card-body">
                                <div id="participant-distribution-map" data-colors='["--vz-light", "--vz-success", "--vz-primary"]' style="height: 450px"></div>
                                <div id="country-details" class="text-center mt-3 p-3 border-top d-none">
                                    <h5 class="country-name mb-2"></h5>
                                    <span class="badge bg-soft-success text-success fs-13 country-participants"></span>
                                </div>
                                <div class="d-flex justify-content-center mt-3">
                                    <div class="legend-info d-flex gap-4">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="rounded-circle d-inline-block" style="width: 10px; height: 10px; background-color: #ff0000;"></span>
                                            <span class="fs-12 text-muted">High participation</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="rounded-circle d-inline-block" style="width: 10px; height: 10px; background-color: #ffa500;"></span>
                                            <span class="fs-12 text-muted">Medium participation</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="rounded-circle d-inline-block" style="width: 10px; height: 10px; background-color: #32cd32;"></span>
                                            <span class="fs-12 text-muted">Low participation</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="bg-light rounded-circle d-inline-block border" style="width: 10px; height: 10px;"></span>
                                            <span class="fs-12 text-muted">No participants</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Participants by Country Data Table -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Participants by Country</h4>
                            </div>
                            <div class="card-body">
                                <table id="participants-country-datatable" class="table table-bordered dt-responsive nowrap table-striped align-middle" style="width:100%">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nationality</th>
                                            <th>Participant Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($countriesData ?? [
                                            [
                                                "nationality" => null,
                                                "participants_count" => "5"
                                            ],
                                            [
                                                "nationality" => "Pakistan",
                                                "participants_count" => "1"
                                            ]
                                        ] as $country): ?>
                                            <tr>
                                                <td><?= $country['nationality'] ?? 'Undefined' ?></td>
                                                <td><?= $country['participants_count'] ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <?= $this->include('landing/common/contact-widget') ?>
            </div>
        </section>
        <!-- end Insights section -->

        <?= $this->include('landing/common/footer') ?>

    </div>
    <!-- end layout wrapper -->

    <?= $this->include('partials/vendor-scripts') ?>

    <!--Swiper slider js-->
    <script src="/assets/libs/swiper/swiper-bundle.min.js"></script>

    <!-- apexcharts -->
    <script src="/assets/libs/apexcharts/apexcharts.min.js"></script>

    <!-- landing init -->
    <script src="/assets/js/pages/landing.init.js"></script>

    <!--datatable js-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

    <script>
        // Counter animation
        document.addEventListener("DOMContentLoaded", function() {
            const counterElements = document.querySelectorAll(".counter-value");

            counterElements.forEach(function(element) {
                const target = parseInt(element.getAttribute("data-target"));
                const hasSuffix = element.querySelector(".counter-value-suffix") !== null;
                const duration = 2000; // 2 seconds
                const step = Math.ceil(target / (duration / 16)); // 16ms per frame (approx. 60fps)
                let current = 0;

                const updateCounter = function() {
                    current += step;
                    if (current >= target) {
                        current = target;
                        clearInterval(interval);
                    }

                    if (hasSuffix) {
                        element.childNodes[0].nodeValue = current;
                    } else {
                        element.textContent = current;
                    }
                };

                const interval = setInterval(updateCounter, 16);
            });

            // Initialize chart
            var options = {
                series: [{
                    name: 'Skill Development',
                    data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
                }, {
                    name: 'Career Growth',
                    data: [35, 41, 36, 26, 45, 48, 52, 53, 41]
                }],
                chart: {
                    type: 'bar',
                    height: 350
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: ['Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
                },
                yaxis: {
                    title: {
                        text: 'Participant Growth (%)'
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + "%"
                        }
                    }
                },
                colors: ['#0ab39c', '#299cdb']
            };

            var chart = new ApexCharts(document.querySelector("#program-impact-chart"), options);
            chart.render();
            
            // DataTable initialization is now only in the script below to avoid duplicates
        });
    </script>
    
    <!-- Vector map-->
    <script src="/assets/libs/jsvectormap/jsvectormap.min.js"></script>
    <script src="/assets/libs/jsvectormap/maps/world-merc.js"></script>

    <script>
        // Parse countries data
        var countriesData = <?= json_encode($countriesData ?? [
                    [
                        "nationality" => null,
                        "participants_count" => "5"
                    ],
                    [
                        "nationality" => "Pakistan",
                        "participants_count" => "1"
                    ]
                ]) ?>;
                
        document.addEventListener("DOMContentLoaded", function() {
            // Transform the data for mapping
            var mapData = {};
            var countryNameMapping = {};
            
            // Create a reverse mapping from ISO codes to country names
            var isoCodeToCountryName = {
                'AF': 'Afghanistan', 'AX': 'Aland Islands', 'AL': 'Albania', 'DZ': 'Algeria', 'AS': 'American Samoa',
                'AD': 'Andorra', 'AO': 'Angola', 'AI': 'Anguilla', 'AQ': 'Antarctica', 'AG': 'Antigua and Barbuda',
                'AR': 'Argentina', 'AM': 'Armenia', 'AW': 'Aruba', 'AU': 'Australia', 'AT': 'Austria',
                'AZ': 'Azerbaijan', 'BS': 'Bahamas', 'BH': 'Bahrain', 'BD': 'Bangladesh', 'BB': 'Barbados',
                'BY': 'Belarus', 'BE': 'Belgium', 'BZ': 'Belize', 'BJ': 'Benin', 'BM': 'Bermuda',
                'BT': 'Bhutan', 'BO': 'Bolivia', 'BQ': 'Bonaire', 'BA': 'Bosnia and Herzegovina', 'BW': 'Botswana',
                'BV': 'Bouvet Island', 'BR': 'Brazil', 'IO': 'British Indian Ocean Territory', 'BN': 'Brunei',
                'BG': 'Bulgaria', 'BF': 'Burkina Faso', 'BI': 'Burundi', 'KH': 'Cambodia', 'CM': 'Cameroon', 
                'CA': 'Canada', 'CV': 'Cape Verde', 'KY': 'Cayman Islands', 'CF': 'Central African Republic', 
                'TD': 'Chad', 'CL': 'Chile', 'CN': 'China', 'CX': 'Christmas Island', 'CC': 'Cocos (Keeling) Islands', 
                'CO': 'Colombia', 'KM': 'Comoros', 'CG': 'Congo', 'CD': 'Congo, DRC', 'CK': 'Cook Islands', 
                'CR': 'Costa Rica', 'CI': 'Ivory Coast', 'HR': 'Croatia', 'CU': 'Cuba', 'CW': 'Curacao', 
                'CY': 'Cyprus', 'CZ': 'Czech Republic', 'DK': 'Denmark', 'DJ': 'Djibouti', 'DM': 'Dominica', 
                'DO': 'Dominican Republic', 'EC': 'Ecuador', 'EG': 'Egypt', 'SV': 'El Salvador', 'GQ': 'Equatorial Guinea', 
                'ER': 'Eritrea', 'EE': 'Estonia', 'ET': 'Ethiopia', 'FK': 'Falkland Islands', 'FO': 'Faroe Islands', 
                'FJ': 'Fiji', 'FI': 'Finland', 'FR': 'France', 'GF': 'French Guiana', 'PF': 'French Polynesia', 
                'TF': 'French Southern Territories', 'GA': 'Gabon', 'GM': 'Gambia', 'GE': 'Georgia', 'DE': 'Germany', 
                'GH': 'Ghana', 'GI': 'Gibraltar', 'GR': 'Greece', 'GL': 'Greenland', 'GD': 'Grenada', 
                'GP': 'Guadeloupe', 'GU': 'Guam', 'GT': 'Guatemala', 'GG': 'Guernsey', 'GN': 'Guinea', 
                'GW': 'Guinea-Bissau', 'GY': 'Guyana', 'HT': 'Haiti', 'HM': 'Heard Island & Mcdonald Islands', 
                'VA': 'Holy See (Vatican City)', 'HN': 'Honduras', 'HK': 'Hong Kong', 'HU': 'Hungary', 'IS': 'Iceland', 
                'IN': 'India', 'ID': 'Indonesia', 'IR': 'Iran', 'IQ': 'Iraq', 'IE': 'Ireland', 'IM': 'Isle Of Man', 
                'IL': 'Israel', 'IT': 'Italy', 'JM': 'Jamaica', 'JP': 'Japan', 'JE': 'Jersey', 'JO': 'Jordan', 
                'KZ': 'Kazakhstan', 'KE': 'Kenya', 'KI': 'Kiribati', 'KR': 'Korea, Republic of', 'KP': 'North Korea', 
                'KW': 'Kuwait', 'KG': 'Kyrgyzstan', 'LA': 'Laos', 'LV': 'Latvia', 'LB': 'Lebanon', 'LS': 'Lesotho', 
                'LR': 'Liberia', 'LY': 'Libya', 'LI': 'Liechtenstein', 'LT': 'Lithuania', 'LU': 'Luxembourg', 
                'MO': 'Macao', 'MK': 'North Macedonia', 'MG': 'Madagascar', 'MW': 'Malawi', 'MY': 'Malaysia', 
                'MV': 'Maldives', 'ML': 'Mali', 'MT': 'Malta', 'MH': 'Marshall Islands', 'MQ': 'Martinique', 
                'MR': 'Mauritania', 'MU': 'Mauritius', 'YT': 'Mayotte', 'MX': 'Mexico', 'FM': 'Micronesia', 
                'MD': 'Moldova', 'MC': 'Monaco', 'MN': 'Mongolia', 'ME': 'Montenegro', 'MS': 'Montserrat', 
                'MA': 'Morocco', 'MZ': 'Mozambique', 'MM': 'Myanmar', 'NA': 'Namibia', 'NR': 'Nauru', 
                'NP': 'Nepal', 'NL': 'Netherlands', 'AN': 'Netherlands Antilles', 'NC': 'New Caledonia', 'NZ': 'New Zealand', 
                'NI': 'Nicaragua', 'NE': 'Niger', 'NG': 'Nigeria', 'NU': 'Niue', 'NF': 'Norfolk Island', 
                'MP': 'Northern Mariana Islands', 'NO': 'Norway', 'OM': 'Oman', 'PK': 'Pakistan', 'PW': 'Palau', 
                'PS': 'Palestine', 'PA': 'Panama', 'PG': 'Papua New Guinea', 'PY': 'Paraguay', 'PE': 'Peru', 
                'PH': 'Philippines', 'PN': 'Pitcairn', 'PL': 'Poland', 'PT': 'Portugal', 'PR': 'Puerto Rico', 
                'QA': 'Qatar', 'RE': 'Reunion', 'RO': 'Romania', 'RU': 'Russia', 'RW': 'Rwanda', 'BL': 'Saint Barthelemy', 
                'SH': 'Saint Helena', 'KN': 'Saint Kitts And Nevis', 'LC': 'Saint Lucia', 'MF': 'Saint Martin', 
                'PM': 'Saint Pierre And Miquelon', 'VC': 'Saint Vincent And Grenadines', 'WS': 'Samoa', 'SM': 'San Marino', 
                'ST': 'Sao Tome And Principe', 'SA': 'Saudi Arabia', 'SN': 'Senegal', 'RS': 'Serbia', 'SC': 'Seychelles', 
                'SL': 'Sierra Leone', 'SG': 'Singapore', 'SK': 'Slovakia', 'SI': 'Slovenia', 'SB': 'Solomon Islands', 
                'SO': 'Somalia', 'ZA': 'South Africa', 'GS': 'South Georgia And Sandwich Isl.', 'SS': 'South Sudan',
                'ES': 'Spain', 'LK': 'Sri Lanka', 'SD': 'Sudan', 'SR': 'Suriname', 'SJ': 'Svalbard And Jan Mayen', 
                'SZ': 'Eswatini', 'SE': 'Sweden', 'CH': 'Switzerland', 'SY': 'Syria', 'TW': 'Taiwan', 'TJ': 'Tajikistan', 
                'TZ': 'Tanzania', 'TH': 'Thailand', 'TL': 'Timor-Leste', 'TG': 'Togo', 'TK': 'Tokelau', 'TO': 'Tonga', 
                'TT': 'Trinidad and Tobago', 'TN': 'Tunisia', 'TR': 'Turkey', 'TM': 'Turkmenistan', 'TC': 'Turks And Caicos Islands', 
                'TV': 'Tuvalu', 'UG': 'Uganda', 'UA': 'Ukraine', 'AE': 'United Arab Emirates', 'GB': 'United Kingdom', 
                'US': 'United States', 'UM': 'United States Outlying Islands', 'UY': 'Uruguay', 'UZ': 'Uzbekistan', 
                'VU': 'Vanuatu', 'VE': 'Venezuela', 'VN': 'Vietnam', 'VG': 'Virgin Islands, British', 'VI': 'Virgin Islands, U.S.', 
                'WF': 'Wallis And Futuna', 'EH': 'Western Sahara', 'YE': 'Yemen', 'ZM': 'Zambia', 'ZW': 'Zimbabwe'
            };
            
            // Transform country data for visualization
            countriesData.forEach(function(item) {
                var countryName = item.nationality ? item.nationality : "Undefined";
                var countryCode = getCountryCode(countryName);
                var count = parseInt(item.participants_count);
                
                if(countryCode && countryName !== "Undefined") {
                    mapData[countryCode] = count;
                    countryNameMapping[countryCode] = countryName;
                }
            });
            
            // Map country names to ISO codes for the map visualization
            function getCountryCode(countryName) {
                // Common country name to ISO code mapping
                var countryCodes = {
                    "Afghanistan": "AF", "Albania": "AL", "Algeria": "DZ", "Angola": "AO",
                    "Argentina": "AR", "Armenia": "AM", "Australia": "AU", "Austria": "AT",
                    "Azerbaijan": "AZ", "Bahamas": "BS", "Bangladesh": "BD", "Belarus": "BY",
                    "Belgium": "BE", "Belize": "BZ", "Benin": "BJ", "Bhutan": "BT",
                    "Bolivia": "BO", "Bosnia and Herzegovina": "BA", "Botswana": "BW", "Brazil": "BR",
                    "Brunei": "BN", "Bulgaria": "BG", "Burkina Faso": "BF", "Burundi": "BI",
                    "Cambodia": "KH", "Cameroon": "CM", "Canada": "CA", "Central African Republic": "CF",
                    "Chad": "TD", "Chile": "CL", "China": "CN", "Colombia": "CO",
                    "Congo": "CG", "Costa Rica": "CR", "Croatia": "HR", "Cuba": "CU",
                    "Cyprus": "CY", "Czech Republic": "CZ", "Denmark": "DK", "Djibouti": "DJ",
                    "Dominican Republic": "DO", "DR Congo": "CD", "Ecuador": "EC", "Egypt": "EG",
                    "El Salvador": "SV", "Equatorial Guinea": "GQ", "Eritrea": "ER", "Estonia": "EE",
                    "Eswatini": "SZ", "Ethiopia": "ET", "Fiji": "FJ", "Finland": "FI",
                    "France": "FR", "Gabon": "GA", "Gambia": "GM", "Georgia": "GE",
                    "Germany": "DE", "Ghana": "GH", "Greece": "GR", "Guatemala": "GT",
                    "Guinea": "GN", "Guinea-Bissau": "GW", "Guyana": "GY", "Haiti": "HT",
                    "Honduras": "HN", "Hungary": "HU", "Iceland": "IS", "India": "IN",
                    "Indonesia": "ID", "Iran": "IR", "Iraq": "IQ", "Ireland": "IE",
                    "Israel": "IL", "Italy": "IT", "Ivory Coast": "CI", "Jamaica": "JM",
                    "Japan": "JP", "Jordan": "JO", "Kazakhstan": "KZ", "Kenya": "KE",
                    "Kuwait": "KW", "Kyrgyzstan": "KG", "Laos": "LA", "Latvia": "LV",
                    "Lebanon": "LB", "Lesotho": "LS", "Liberia": "LR", "Libya": "LY",
                    "Lithuania": "LT", "Luxembourg": "LU", "Madagascar": "MG", "Malawi": "MW",
                    "Malaysia": "MY", "Mali": "ML", "Malta": "MT", "Mauritania": "MR",
                    "Mexico": "MX", "Moldova": "MD", "Mongolia": "MN", "Montenegro": "ME",
                    "Morocco": "MA", "Mozambique": "MZ", "Myanmar": "MM", "Namibia": "NA",
                    "Nepal": "NP", "Netherlands": "NL", "New Zealand": "NZ", "Nicaragua": "NI",
                    "Niger": "NE", "Nigeria": "NG", "North Korea": "KP", "North Macedonia": "MK",
                    "Norway": "NO", "Oman": "OM", "Pakistan": "PK", "Panama": "PA",
                    "Papua New Guinea": "PG", "Paraguay": "PY", "Peru": "PE", "Philippines": "PH",
                    "Poland": "PL", "Portugal": "PT", "Qatar": "QA", "Romania": "RO",
                    "Russia": "RU", "Rwanda": "RW", "Saudi Arabia": "SA", "Senegal": "SN",
                    "Serbia": "RS", "Sierra Leone": "SL", "Slovakia": "SK", "Slovenia": "SI",
                    "Somalia": "SO", "South Africa": "ZA", "South Korea": "KR", "South Sudan": "SS",
                    "Spain": "ES", "Sri Lanka": "LK", "Sudan": "SD", "Suriname": "SR",
                    "Sweden": "SE", "Switzerland": "CH", "Syria": "SY", "Taiwan": "TW",
                    "Tajikistan": "TJ", "Tanzania": "TZ", "Thailand": "TH", "Timor-Leste": "TL",
                    "Togo": "TG", "Trinidad and Tobago": "TT", "Tunisia": "TN", "Turkey": "TR",
                    "Turkmenistan": "TM", "Uganda": "UG", "Ukraine": "UA", "United Arab Emirates": "AE",
                    "United Kingdom": "GB", "United States": "US", "Uruguay": "UY", "Uzbekistan": "UZ",
                    "Venezuela": "VE", "Vietnam": "VN", "Yemen": "YE", "Zambia": "ZM",
                    "Zimbabwe": "ZW"
                };
                
                return countryCodes[countryName];
            }
            
            // Function to get country name from code
            function getCountryName(countryCode) {
                if (countryNameMapping[countryCode]) {
                    return countryNameMapping[countryCode]; // First check our actual data
                } else if (isoCodeToCountryName[countryCode]) {
                    return isoCodeToCountryName[countryCode]; // Then check the ISO code mapping
                }
                return countryCode; // Fall back to the code itself
            }
            
            // Get colors array from the string
            function getChartColorsArray(chartId) {
                if (document.getElementById(chartId) !== null) {
                    var colors = document.getElementById(chartId).getAttribute("data-colors");
                    if (colors) {
                        colors = JSON.parse(colors);
                        return colors.map(function (value) {
                            var newValue = value.replace(" ", "");
                            if (newValue.indexOf(",") === -1) {
                                var color = getComputedStyle(document.documentElement).getPropertyValue(newValue);
                                if (color) return color;
                                else return newValue;
                            } else {
                                var val = value.split(',');
                                if (val.length == 2) {
                                    var rgbaColor = getComputedStyle(document.documentElement).getPropertyValue(val[0]);
                                    rgbaColor = "rgba(" + rgbaColor + "," + val[1] + ")";
                                    return rgbaColor;
                                } else {
                                    return newValue;
                                }
                            }
                        });
                    }
                    return null;
                }
                return null;
            }
            
            // Function to update country details section with animation
            function updateCountryDetails(code) {
                const detailsSection = document.getElementById('country-details');
                const countryNameElement = detailsSection.querySelector('.country-name');
                const participantsElement = detailsSection.querySelector('.country-participants');
                
                const countryName = getCountryName(code);
                const participants = mapData[code] || 0;
                
                // Add fade out effect
                detailsSection.classList.add('opacity-50');
                
                // Update content after brief delay
                setTimeout(() => {
                    countryNameElement.textContent = countryName;
                    participantsElement.textContent = participants + ' participant' + (participants !== 1 ? 's' : '');
                    
                    // Show section and fade in
                    detailsSection.classList.remove('d-none', 'opacity-50');
                }, 300);
                
                // Scroll to details if on mobile
                if (window.innerWidth < 768) {
                    setTimeout(() => {
                        detailsSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }, 350);
                }
            }
            
            // Initialize World Map
            // Define a color spectrum from red (highest) through orange, yellow to green (lowest)
            var mapColors = ["#f8f9fa", "#ff0000", "#ff4500", "#ffa500", "#ffff00", "#32cd32"];
            
            // Find the maximum participant count to create appropriate color ranges
            var maxParticipants = 0;
            for (var code in mapData) {
                if (mapData[code] > maxParticipants) {
                    maxParticipants = mapData[code];
                }
            }
            
            // Create a normalized version of the data where values are percentages of max
            var normalizedData = {};
            for (var code in mapData) {
                // Convert each value to a percentage of the maximum (0-100 range)
                normalizedData[code] = (mapData[code] / maxParticipants) * 100;
            }
            
            // Create color scale function based on the maximum participants
            function getColorScale() {
                // If there's no data or only zero values, just use the default colors
                if (maxParticipants <= 0) {
                    return {
                        scale: [mapColors[0], mapColors[5]],
                        values: mapData
                    };
                }
                
                // Use the normalized data to ensure proportional coloring
                // The country with maximum participants will always get the most intense red
                return {
                    scale: [mapColors[5], mapColors[4], mapColors[3], mapColors[2], mapColors[1]],
                    values: normalizedData
                };
            }
            
            // Custom tooltip CSS style
            const tooltipStyle = document.createElement('style');
            tooltipStyle.textContent = `
                .jvm-tooltip {
                    padding: 8px 12px;
                    background-color: rgba(33, 37, 41, 0.9);
                    border-radius: 4px;
                    font-size: 13px;
                    font-family: var(--vz-font-sans-serif);
                    box-shadow: 0 3px 8px rgba(0,0,0,0.2);
                    pointer-events: none;
                    white-space: nowrap;
                    z-index: 1000;
                }
            `;
            document.head.appendChild(tooltipStyle);
            
            var worldMap = new jsVectorMap({
                map: 'world_merc',
                selector: '#participant-distribution-map',
                zoomOnScroll: true,
                zoomButtons: false,
                regionsSelectable: true,
                regionsSelectableOne: true,
                markersSelectable: false,
                regionStyle: {
                    initial: {
                        stroke: "#9599ad",
                        strokeWidth: 0.25,
                        fill: mapColors[0],
                        fillOpacity: 1,
                    },
                    hover: {
                        fillOpacity: 0.7,
                        cursor: 'pointer'
                    },
                    selected: {
                        fill: "#299cdb",
                        fillOpacity: 1
                    },
                    selectedHover: {
                        fillOpacity: 0.8
                    }
                },
                visualizeData: getColorScale(),
                onRegionClick: function(event, code) {
                    // This handler will be called when a region is clicked
                    updateCountryDetails(code);
                },
                onRegionTooltipShow: function(event, tooltip, code) {
                    const countryName = getCountryName(code);
                    const participants = mapData[code] || 0;
                    tooltip.text(countryName + ': ' + participants + ' participant' + (participants !== 1 ? 's' : ''));
                }
            });
            
            // Show the first country with data by default
            let firstCountryCode = null;
            for (let code in mapData) {
                if (mapData[code] > 0) {
                    firstCountryCode = code;
                    break;
                }
            }
            
            if (firstCountryCode) {
                setTimeout(() => {
                    worldMap.setSelectedRegions([firstCountryCode]);
                    updateCountryDetails(firstCountryCode);
                }, 500);
            } else if (countriesData.length > 0 && countriesData[0].nationality === null) {
                // If we only have undefined nationality data
                const detailsSection = document.getElementById('country-details');
                const countryNameElement = detailsSection.querySelector('.country-name');
                const participantsElement = detailsSection.querySelector('.country-participants');
                
                countryNameElement.textContent = "Undefined Nationality";
                participantsElement.textContent = parseInt(countriesData[0].participants_count) + ' participants';
                
                detailsSection.classList.remove('d-none');
            }
            
            // Initialize DataTable with search
            $('#participants-country-datatable').DataTable({
                responsive: true,
                lengthChange: false,
                pageLength: 7,
                info: false,
                searching: true,
                language: {
                    search: '<i class="ri-search-line"></i>',
                    searchPlaceholder: "Search countries...",
                    paginate: {
                        previous: "<i class='mdi mdi-chevron-left'>",
                        next: "<i class='mdi mdi-chevron-right'>"
                    },
                    emptyTable: "No country data available"
                },
                drawCallback: function() {
                    $('.dataTables_paginate > .pagination').addClass('pagination-rounded');
                    $('.dataTables_filter input').addClass('form-control-sm');
                    $('.dataTables_wrapper .row:first-child').addClass('mb-3');
                }
            });
        });
    </script>
</body>

</html>