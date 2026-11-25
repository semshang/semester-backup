package dao;

import database.DBConnection;
import exception.DuplicateBookException;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;
import model.Book;

public class BookDAO {
    public boolean addBook(Book book) throws SQLException, DuplicateBookException {
        if (findBookById(book.getId()) != null) {
            throw new DuplicateBookException("Book with ID " + book.getId() + " already exists.");
        }
        String sql = "INSERT INTO books(book_id, book_name, author, category, price, quantity, available_quantity) VALUES (?, ?, ?, ?, ?, ?, ?)";
        try (Connection con = DBConnection.getConnection(); PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setString(1, book.getId());
            ps.setString(2, book.getTitle());
            ps.setString(3, book.getAuthor());
            ps.setString(4, book.getCategory());
            ps.setDouble(5, book.getPrice());
            ps.setInt(6, book.getQuantity());
            ps.setInt(7, book.getAvailableQuantity());
            return ps.executeUpdate() == 1;
        }
    }

    public boolean updateBook(Book book) throws SQLException {
        String sql = "UPDATE books SET book_name = ?, author = ?, category = ?, price = ?, quantity = ?, available_quantity = ? WHERE book_id = ?";
        try (Connection con = DBConnection.getConnection(); PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setString(1, book.getTitle());
            ps.setString(2, book.getAuthor());
            ps.setString(3, book.getCategory());
            ps.setDouble(4, book.getPrice());
            ps.setInt(5, book.getQuantity());
            ps.setInt(6, book.getAvailableQuantity());
            ps.setString(7, book.getId());
            return ps.executeUpdate() == 1;
        }
    }

    public boolean deleteBook(String bookId) throws SQLException {
        String sql = "DELETE FROM books WHERE book_id = ?";
        try (Connection con = DBConnection.getConnection(); PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setString(1, bookId);
            return ps.executeUpdate() == 1;
        }
    }

    public Book findBookById(String bookId) throws SQLException {
        String sql = "SELECT book_id, book_name, author, category, price, quantity, available_quantity FROM books WHERE book_id = ?";
        try (Connection con = DBConnection.getConnection(); PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setString(1, bookId);
            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next()) {
                    return mapBook(rs);
                }
                return null;
            }
        }
    }

    public List<Book> searchBooks(String keyword) throws SQLException {
        String sql = "SELECT book_id, book_name, author, category, price, quantity, available_quantity FROM books WHERE book_id LIKE ? OR book_name LIKE ? OR author LIKE ? OR category LIKE ? ORDER BY book_id";
        List<Book> books = new ArrayList<>();
        try (Connection con = DBConnection.getConnection(); PreparedStatement ps = con.prepareStatement(sql)) {
            String searchPattern = "%" + keyword + "%";
            ps.setString(1, searchPattern);
            ps.setString(2, searchPattern);
            ps.setString(3, searchPattern);
            ps.setString(4, searchPattern);
            try (ResultSet rs = ps.executeQuery()) {
                while (rs.next()) {
                    books.add(mapBook(rs));
                }
            }
        }
        return books;
    }

    public List<Book> getAllBooks() throws SQLException {
        String sql = "SELECT book_id, book_name, author, category, price, quantity, available_quantity FROM books ORDER BY book_id";
        List<Book> books = new ArrayList<>();
        try (Connection con = DBConnection.getConnection(); PreparedStatement ps = con.prepareStatement(sql); ResultSet rs = ps.executeQuery()) {
            while (rs.next()) {
                books.add(mapBook(rs));
            }
        }
        return books;
    }

    public boolean updateAvailability(String bookId, int availableQuantity) throws SQLException {
        String sql = "UPDATE books SET available_quantity = ? WHERE book_id = ?";
        try (Connection con = DBConnection.getConnection(); PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setInt(1, availableQuantity);
            ps.setString(2, bookId);
            return ps.executeUpdate() == 1;
        }
    }

    private Book mapBook(ResultSet rs) throws SQLException {
        Book book = new Book();
        book.setId(rs.getString("book_id"));
        book.setTitle(rs.getString("book_name"));
        book.setAuthor(rs.getString("author"));
        book.setCategory(rs.getString("category"));
        book.setPrice(rs.getDouble("price"));
        book.setQuantity(rs.getInt("quantity"));
        book.setAvailableQuantity(rs.getInt("available_quantity"));
        return book;
    }
}
