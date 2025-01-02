<?php
include 'db_connection.php';

$search = $_GET['query'] ?? ''; // Arama sorgusu alınıyor
$query = "SELECT id, title FROM movies WHERE title LIKE ?";
$stmt = $conn->prepare($query);
$search_term = '%' . $search . '%';
$stmt->bind_param('s', $search_term);
$stmt->execute();

$result = $stmt->get_result();
$suggestions = [];

while ($row = $result->fetch_assoc()) {
    $suggestions[] = [
        'id' => $row['id'],
        'title' => $row['title']
    ];
}

echo json_encode($suggestions); // JSON formatında çıktı
$stmt->close();
$conn->close();
?>
