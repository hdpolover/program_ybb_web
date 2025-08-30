<?php

namespace App\Controllers\ambassador;

use App\Controllers\BaseController;

class Performance extends BaseController
{
    public function index()
    {
        $ambassador = session()->get('user');

        if (!$ambassador) {
            return redirect()->to('/ambassadors/sign-in');
        }

        $data = [
            'title' => 'Performance Analytics',
            'ambassador' => $ambassador,
        ];

        return $this->render('ambassador/performance', $data);
    }


    /**
     * Performance Metrics API endpoint
     * GET /ambassadors/dashboard/performance
     */
    public function performance()
    {
        try {
            $ambassador = session()->get('user');
            
            if (!$ambassador) {
                return $this->response->setStatusCode(401)->setJSON([
                    'status' => 'error',
                    'message' => 'Unauthorized access'
                ]);
            }

            // Use the documented working API endpoint
            $apiResponse = $this->makeGetRequest('/ambassador/dashboard/performance', [], true);
            
            if ($apiResponse) {
                log_message('info', 'Successfully retrieved performance data from API');
                // makeGetRequest returns only the 'data' portion, so wrap it in a proper response format
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Performance data retrieved successfully',
                    'data' => $apiResponse
                ]);
            } else {
                log_message('warning', 'Performance API call failed - service unavailable');
                return $this->response->setStatusCode(503)->setJSON([
                    'status' => 'error',
                    'message' => 'Performance data is currently unavailable. Please try again later.'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Performance metrics error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Failed to retrieve performance metrics'
            ]);
        }
    }
}