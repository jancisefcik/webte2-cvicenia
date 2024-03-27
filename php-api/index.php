<?php
require_once ('config.php');
require_once ('Book.php');

header("Content-Type: application/json");

$book = new Book($conn);

$method = $_SERVER['REQUEST_METHOD'];
$endpoint = $_SERVER['PATH_INFO'];

switch ($method) {
    case 'GET':
        if($endpoint == '/books') {
            echo json_encode($book->getBooks());
        }
        break;
    case 'POST':
        if($endpoint == '/books') {
            $data = json_decode(file_get_contents('php://input'), true);

            if($book->createBook()) {
                http_response_code(201);
                echo json_encode(array(['message' => "Created successfully"]));
            } else {
                http_response_code(400);
                echo json_encode(array(['message' => "Bad request"]));
            }

        }
        break;
    case 'PUT':
        $data = json_decode(file_get_contents('php://input'), true);

        if(preg_match('/^\/books\/(\d+)$/', $endpoint, $matches)) {
            $id = $matches[1];
            if($book->updateBookById($id)) {
                http_response_code(201);
                echo json_encode(array(['message' => "Updated successfully"]));
            } else {
                http_response_code(400);
                echo json_encode(array(['message' => "Bad request"]));
            }
        }
        break;
    case 'DELETE':
        if(preg_match('/^\/books\/(\d+)$/', $endpoint, $matches)) {
            $id = $matches[1];
            if($book->deleteBook($id)) {
                http_response_code(201);
                echo json_encode(array(['message' => "Deleted successfully"]));
            } else {
                http_response_code(400);
                echo json_encode(array(['message' => "Bad request"]));
            }
        }
        break;
    default:
        echo 'Invalid method';
        break;
}

?>
