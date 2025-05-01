
<!-- Program Rundowns Section -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body p-4">
        <div class="d-flex align-items-center mb-4">
            <div class="flex-shrink-0">
                <div class="avatar-sm">
                    <div class="avatar-title bg-success-subtle text-success rounded-circle fs-18">
                        <i class="ri-calendar-check-line"></i>
                    </div>
                </div>
            </div>
            <div class="flex-grow-1 ms-3">
                <h3 class="card-title mb-0">Program Rundowns</h3>
                <p class="text-muted mb-0 mt-1">Detailed schedule of activities for this program</p>
            </div>
        </div>        <?php if (isset($rundowns) && !empty($rundowns)): ?>
            <?php 
                // Helper function to calculate duration in a readable format
                function calculateDuration($start, $end) {
                    $diff = $start->diff($end);
                    $hours = $diff->h + ($diff->days * 24);
                    $minutes = $diff->i;
                    
                    if ($hours > 0) {
                        return $hours . 'h ' . ($minutes > 0 ? $minutes . 'm' : '');
                    } else {
                        return $minutes . ' minutes';
                    }
                }
            
                // Sort rundowns by order_number
                usort($rundowns, function($a, $b) {
                    // First sort by date
                    $dateA = new DateTime($a['start_date']);
                    $dateB = new DateTime($b['start_date']);
                    
                    if ($dateA->format('Y-m-d') != $dateB->format('Y-m-d')) {
                        return $dateA <=> $dateB;
                    }
                    
                    // Then by order_number
                    return $a['order_number'] - $b['order_number'];
                });
                
                // Group rundowns by date for row spanning
                $grouped_rundowns = [];
                $current_date = null;
                $activities_count = [];
                
                foreach ($rundowns as $rundown) {
                    $start_date = new DateTime($rundown['start_date']);
                    $date_key = $start_date->format('Y-m-d');
                    
                    if (!isset($grouped_rundowns[$date_key])) {
                        $grouped_rundowns[$date_key] = [];
                        $activities_count[$date_key] = 0;
                    }
                    
                    $grouped_rundowns[$date_key][] = $rundown;
                    $activities_count[$date_key]++;
                }
            ?>
            <div class="table-responsive">                <table class="table table-bordered align-middle table-hover">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 15%">Date</th>
                            <th class="text-center" style="width: 20%">Time Range</th>
                            <th style="width: 25%">Activity</th>
                            <th style="width: 40%">Description</th>
                        </tr>
                    </thead>                    <tbody>
                        <?php 
                        foreach ($grouped_rundowns as $date_key => $date_rundowns): 
                            $date_obj = new DateTime($date_key);
                            $rowspan = count($date_rundowns);
                            $first_row = true;
                            
                            foreach ($date_rundowns as $index => $rundown):
                                // Format the date and time
                                $start_date = new DateTime($rundown['start_date']);
                                $end_date = new DateTime($rundown['end_date']);
                                
                                // Check if start and end dates are on the same day
                                $same_day = $start_date->format('Y-m-d') === $end_date->format('Y-m-d');
                                
                                // Format date display
                                if ($same_day) {
                                    $date_display = $start_date->format('M d, Y');
                                } else {
                                    $date_display = $start_date->format('M d') . ' - ' . $end_date->format('M d, Y');
                                }
                                
                                // Format time display - separate start and end times
                                $start_time = $start_date->format('h:i A');
                                $end_time = $end_date->format('h:i A');
                        ?>                        <tr>
                            <?php if ($first_row): // Only show date in first row of the group ?>
                            <td class="text-center align-middle" rowspan="<?= $rowspan ?>">
                                <div class="d-flex flex-column">
                                    <span class="fw-medium"><?= $date_display ?></span>
                                    <?php if (!$same_day): ?>
                                    <small class="text-muted">(<?= $start_date->diff($end_date)->days + 1 ?> days)</small>
                                    <?php endif; ?>
                                    <div class="mt-2">
                                        <span class="badge bg-success rounded-pill">
                                            <?= $activities_count[$date_key] ?> Activities
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <?php endif; ?>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="text-center">
                                        <span class="badge bg-soft-primary text-primary fs-12 px-3 py-2">
                                            <?= $start_time ?> - <?= $end_time ?>
                                        </span>
                                        <div class="mt-1">
                                            <small class="text-muted fs-12">
                                                Duration: <?= calculateDuration($start_date, $end_date) ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <h5 class="fs-14 mb-0"><?= $rundown['title'] ?></h5>
                                </div>
                            </td>
                            <td>
                                <p class="text-muted mb-0">
                                    <?= !empty($rundown['description']) ? $rundown['description'] : 'No description available' ?>
                                </p>
                            </td>
                        </tr>
                        <?php 
                            $first_row = false;
                            endforeach; 
                        endforeach; 
                        ?>
                    </tbody>
                </table>
            </div>
              <div class="alert alert-info mt-3 mb-0">
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <i class="ri-information-line fs-16 align-middle"></i>
                    </div>
                    <div class="flex-grow-1 ms-2">
                        <p class="mb-0"><strong>Note:</strong> This rundown is an estimation only. The final schedule will be updated closer to the program date. Please check back regularly for the most accurate information.</p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center p-4 border rounded-3 bg-light">                <div class="avatar-md mx-auto mb-3">
                    <div class="avatar-title bg-soft-primary text-primary display-6 rounded-circle">
                        <i class="ri-calendar-line"></i>
                    </div>
                </div>
                <h5>No Rundowns Available</h5>
                <p class="text-muted">The estimated schedule will be published closer to the program date. Please check back later.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<!-- End Program Rundowns Section -->