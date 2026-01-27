<?php
/**
 * Jobs API Endpoint
 * Handles job-related operations
 */

header('Content-Type: application/json; charset=utf-8');
session_start();
require_once '../config/db.php';

$action = $_GET['action'] ?? '';

// Helper function to send JSON response
function jsonResponse($data, $statusCode = 200)
{
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// Check authentication for protected routes
function requireAuth()
{
    if (!isset($_SESSION['user_id'])) {
        jsonResponse(['success' => false, 'error' => 'Unauthorized'], 401);
    }
    return $_SESSION['user_id'];
}

switch ($action) {
    case 'feed':
        // Get jobs for the feed (filtered by user preferences if available)
        $userId = $_SESSION['user_id'] ?? null;

        try {
            // Get jobs the user hasn't swiped on yet
            if ($userId) {
                $stmt = $pdo->prepare("
                    SELECT j.* FROM jobs j
                    WHERE j.id NOT IN (
                        SELECT m.jobId FROM matches m WHERE m.userId = ?
                    )
                    ORDER BY j.createdAt DESC
                    LIMIT 50
                ");
                $stmt->execute([$userId]);
            } else {
                $stmt = $pdo->query("SELECT * FROM jobs ORDER BY createdAt DESC LIMIT 50");
            }

            $jobs = $stmt->fetchAll();

            // Format jobs for frontend
            $formattedJobs = array_map(function ($job) {
                return [
                    'id' => $job['id'],
                    'title' => $job['title'],
                    'company' => $job['company'],
                    'description' => $job['description'],
                    'location' => $job['location'],
                    'salaryRange' => $job['salaryRange'],
                    'type' => $job['type'],
                    'tags' => $job['tags'] ? json_decode($job['tags']) : [],
                    'image' => $job['image'] ?: 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=500'
                ];
            }, $jobs);

            jsonResponse(['success' => true, 'jobs' => $formattedJobs]);
        } catch (PDOException $e) {
            jsonResponse(['success' => false, 'error' => 'Database error'], 500);
        }
        break;

    case 'list':
        // Get jobs by business
        $businessId = $_GET['business_id'] ?? requireAuth();

        try {
            $stmt = $pdo->prepare("SELECT * FROM jobs WHERE business_id = ? ORDER BY createdAt DESC");
            $stmt->execute([$businessId]);
            $jobs = $stmt->fetchAll();

            jsonResponse(['success' => true, 'jobs' => $jobs]);
        } catch (PDOException $e) {
            jsonResponse(['success' => false, 'error' => 'Database error'], 500);
        }
        break;

    case 'get':
        // Get single job
        $jobId = $_GET['id'] ?? null;
        if (!$jobId) {
            jsonResponse(['success' => false, 'error' => 'Job ID required'], 400);
        }

        try {
            $stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ?");
            $stmt->execute([$jobId]);
            $job = $stmt->fetch();

            if (!$job) {
                jsonResponse(['success' => false, 'error' => 'Job not found'], 404);
            }

            jsonResponse(['success' => true, 'job' => $job]);
        } catch (PDOException $e) {
            jsonResponse(['success' => false, 'error' => 'Database error'], 500);
        }
        break;

    case 'create':
        // Create new job
        $userId = requireAuth();

        // Get POST data
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        $title = trim($input['title'] ?? '');
        $company = trim($input['company'] ?? '');
        $description = trim($input['description'] ?? '');
        $location = trim($input['location'] ?? '');
        $salaryRange = trim($input['salaryRange'] ?? '');
        $type = trim($input['type'] ?? '');
        $tags = $input['tags'] ?? [];
        $image = trim($input['image'] ?? '');

        if (empty($title) || empty($description)) {
            jsonResponse(['success' => false, 'error' => 'Title and description required'], 400);
        }

        try {
            // Get user's company name
            $stmt = $pdo->prepare("SELECT company_name FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();

            $stmt = $pdo->prepare("
                INSERT INTO jobs (title, company, description, location, salaryRange, type, tags, image, business_id, createdAt)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");

            $stmt->execute([
                $title,
                $company ?: ($user['company_name'] ?? 'חברה לא ידועה'),
                $description,
                $location,
                $salaryRange,
                $type,
                json_encode($tags),
                $image,
                $userId
            ]);

            $jobId = $pdo->lastInsertId();

            jsonResponse(['success' => true, 'jobId' => $jobId]);
        } catch (PDOException $e) {
            jsonResponse(['success' => false, 'error' => 'Failed to create job'], 500);
        }
        break;

    case 'update':
        // Update existing job
        $userId = requireAuth();
        $jobId = $_GET['id'] ?? null;

        if (!$jobId) {
            jsonResponse(['success' => false, 'error' => 'Job ID required'], 400);
        }

        // Verify ownership
        $stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ? AND business_id = ?");
        $stmt->execute([$jobId, $userId]);
        $job = $stmt->fetch();

        if (!$job) {
            jsonResponse(['success' => false, 'error' => 'Job not found or unauthorized'], 404);
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        try {
            $stmt = $pdo->prepare("
                UPDATE jobs SET
                    title = COALESCE(?, title),
                    company = COALESCE(?, company),
                    description = COALESCE(?, description),
                    location = COALESCE(?, location),
                    salaryRange = COALESCE(?, salaryRange),
                    type = COALESCE(?, type),
                    tags = COALESCE(?, tags),
                    image = COALESCE(?, image)
                WHERE id = ?
            ");

            $stmt->execute([
                $input['title'] ?? null,
                $input['company'] ?? null,
                $input['description'] ?? null,
                $input['location'] ?? null,
                $input['salaryRange'] ?? null,
                $input['type'] ?? null,
                isset($input['tags']) ? json_encode($input['tags']) : null,
                $input['image'] ?? null,
                $jobId
            ]);

            jsonResponse(['success' => true]);
        } catch (PDOException $e) {
            jsonResponse(['success' => false, 'error' => 'Failed to update job'], 500);
        }
        break;

    case 'delete':
        // Delete job
        $userId = requireAuth();
        $jobId = $_GET['id'] ?? null;

        if (!$jobId) {
            jsonResponse(['success' => false, 'error' => 'Job ID required'], 400);
        }

        try {
            $stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ? AND business_id = ?");
            $stmt->execute([$jobId, $userId]);

            if ($stmt->rowCount() === 0) {
                jsonResponse(['success' => false, 'error' => 'Job not found or unauthorized'], 404);
            }

            jsonResponse(['success' => true]);
        } catch (PDOException $e) {
            jsonResponse(['success' => false, 'error' => 'Failed to delete job'], 500);
        }
        break;

    default:
        jsonResponse(['success' => false, 'error' => 'Invalid action'], 400);
}
