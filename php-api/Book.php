<?php
class Book
{
    private $conn;
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getBooks()
    {
        $query = "SELECT * FROM books";
        $result = mysqli_query($this->conn, $query);
        $books = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $books[] = $row;
        }

        return $books;
    }

    public function createBook($data)
    {
        if (isset($data['title']) && isset($data['author']) && isset($data['publish_year'])) {
            $title = $data['title'];
            $author = $data['author'];
            $publish_year = $data['publish_year'];

            $query = "INSERT INTO books (title, author, publish_year) VALUES ('$title', '$author', '$publish_year')";
            return mysqli_query($this->conn, $query);
        } else {
            return false;
        }
    }

    public function updateBookById($id, $data)
    {

        if (isset($data['title']) && isset($data['author']) && isset($data['publish_year'])) {
            $title = $data['title'];
            $author = $data['author'];
            $publish_year = $data['publish_year'];

            $query = "UPDATE books SET title = '$title', author = '$author', publish_year = '$publish_year' WHERE id = $id";
            return mysqli_query($this->conn, $query);
        } else {
            return false;
        }
    }

    public function deleteBook($id)
    {
        $query = "DELETE FROM books WHERE id = $id";
        return $result = mysqli_query($this->conn, $query);
    }
}
