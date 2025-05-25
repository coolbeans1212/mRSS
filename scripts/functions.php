<?php
function binarySearchArray($array, $needle) {
    $low = 0;
    $high = count($array) - 1;
    while ($low <= $high) {
        $mid = (int)(($low + $high) / 2);
        if ($array[$mid] === $needle) {
            return $mid;
        } elseif ($array[$mid] < $needle) {
            $low = $mid + 1;
        } else {
            $high = $mid - 1;
        }
    }
    return -1; // Not found
}

function rateLimit($userdb, $clientId, $limit = 10, $timeFrame = 60) { // Will return true if request is allowed, false if rate limit exceeded and add the client to the database if it does not exist
    $currentTime = time();

    // Check if client exists
    $query = "SELECT request_count, last_request_time FROM rate_limits WHERE client_id = ?";
    $stmt = $userdb->prepare($query);
    $stmt->bind_param('s', $clientId);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if ($data) {
        // If time frame has passed, reset count
        if ($currentTime - $data['last_request_time'] > $timeFrame) {
            $updateQuery = "UPDATE rate_limits SET request_count = 1, last_request_time = ? WHERE client_id = ?";
            $updateStmt = $userdb->prepare($updateQuery);
            $updateStmt->bind_param('is', $currentTime, $clientId);
            $updateStmt->execute();
            return true;
        } else {
            if ($data['request_count'] >= $limit) {
                return false; // Rate limit exceeded
            }
            // Increment count
            $updateQuery = "UPDATE rate_limits SET request_count = request_count + 1, last_request_time = ? WHERE client_id = ?";
            $updateStmt = $userdb->prepare($updateQuery);
            $updateStmt->bind_param('is', $currentTime, $clientId);
            $updateStmt->execute();
            return true;
        }
    } else {
        // Insert new client
        $insertQuery = "INSERT INTO rate_limits (client_id, request_count, last_request_time) VALUES (?, 1, ?)";
        $insertStmt = $userdb->prepare($insertQuery);
        $insertStmt->bind_param('si', $clientId, $currentTime);
        $insertStmt->execute();
        return true;
    }
}