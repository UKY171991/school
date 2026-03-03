<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ApiDocumentationController extends Controller
{
    public function index()
    {
        // Get all API routes
        $apiRoutes = collect(Route::getRoutes())->filter(function ($route) {
            return str_starts_with($route->uri(), 'api/');
        })->map(function ($route) {
            return [
                'method' => implode('|', $route->methods()),
                'uri' => $route->uri(),
                'name' => $route->getName(),
                'action' => $route->getActionName(),
            ];
        })->values();

        // Organize endpoints by category
        $endpoints = [
            'School Information' => [
                [
                    'method' => 'GET',
                    'endpoint' => '/api/public-api/info',
                    'description' => 'Get school information',
                    'parameters' => 'domain (required)',
                    'example' => url('/api/public-api/info?domain=school.developer.space'),
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/public-api/statistics',
                    'description' => 'Get school statistics (students, teachers, attendance, fees)',
                    'parameters' => 'domain (required)',
                    'example' => url('/api/public-api/statistics?domain=school.developer.space'),
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/public-api/branches',
                    'description' => 'Get all branches of the school',
                    'parameters' => 'domain (required)',
                    'example' => url('/api/public-api/branches?domain=school.developer.space'),
                ],
            ],
            'Students' => [
                [
                    'method' => 'GET',
                    'endpoint' => '/api/public-api/students',
                    'description' => 'Get all students (paginated)',
                    'parameters' => 'domain (required), per_page (optional), page (optional)',
                    'example' => url('/api/public-api/students?domain=school.developer.space&per_page=20'),
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/public-api/students/{id}',
                    'description' => 'Get single student by ID or roll number',
                    'parameters' => 'domain (required), id/roll_number (required)',
                    'example' => url('/api/public-api/students/1001?domain=school.developer.space'),
                ],
            ],
            'Teachers' => [
                [
                    'method' => 'GET',
                    'endpoint' => '/api/public-api/teachers',
                    'description' => 'Get all teachers (paginated)',
                    'parameters' => 'domain (required), per_page (optional)',
                    'example' => url('/api/public-api/teachers?domain=school.developer.space'),
                ],
            ],
            'Academic' => [
                [
                    'method' => 'GET',
                    'endpoint' => '/api/public-api/grades',
                    'description' => 'Get all classes/grades with sections',
                    'parameters' => 'domain (required)',
                    'example' => url('/api/public-api/grades?domain=school.developer.space'),
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/public-api/sections',
                    'description' => 'Get all sections',
                    'parameters' => 'domain (required)',
                    'example' => url('/api/public-api/sections?domain=school.developer.space'),
                ],
                [
                    'method' => 'GET',
                    'endpoint' => '/api/public-api/exams',
                    'description' => 'Get all exams (paginated)',
                    'parameters' => 'domain (required), per_page (optional)',
                    'example' => url('/api/public-api/exams?domain=school.developer.space'),
                ],
            ],
            'Attendance' => [
                [
                    'method' => 'GET',
                    'endpoint' => '/api/public-api/attendance',
                    'description' => 'Get attendance records',
                    'parameters' => 'domain (required), date (optional), student_id (optional), per_page (optional)',
                    'example' => url('/api/public-api/attendance?domain=school.developer.space&date=2026-02-27'),
                ],
            ],
            'Fees' => [
                [
                    'method' => 'GET',
                    'endpoint' => '/api/public-api/fee-payments',
                    'description' => 'Get fee payment records',
                    'parameters' => 'domain (required), student_id (optional), status (optional), per_page (optional)',
                    'example' => url('/api/public-api/fee-payments?domain=school.developer.space&status=paid'),
                ],
            ],
        ];

        return view('api-documentation', compact('endpoints', 'apiRoutes'));
    }
}
