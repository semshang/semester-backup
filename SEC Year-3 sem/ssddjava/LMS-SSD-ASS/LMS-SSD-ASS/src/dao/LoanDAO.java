package dao;

import database.DBConnection;
import exception.BookUnavailableException;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.time.LocalDate;
import java.util.ArrayList;
import java.util.List;
import model.Book;
import model.Loan;
import model.TransactionRecord;

public class LoanDAO {
    private final BookDAO bookDAO = new BookDAO();

    public boolean issueLoan(Loan loan) throws SQLException, BookUnavailableException {
        Book book = bookDAO.findBookById(loan.getBookId());
        if (book == null) {
            throw new BookUnavailableException("Book not found: " + loan.getBookId());
        }
        if (book.getAvailableQuantity() <= 0) {
            throw new BookUnavailableException("Book is not available for issue.");
        }

        String sql = "INSERT INTO loans(book_id, registration_no, student_name, issue_date, due_date, returned, fine) VALUES (?, ?, ?, ?, ?, ?, ?)";
        try (Connection con = DBConnection.getConnection(); PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setString(1, loan.getBookId());
            ps.setString(2, loan.getRegistrationNo());
            ps.setString(3, loan.getStudentName());
            ps.setString(4, loan.getIssueDate().toString());
            ps.setString(5, loan.getDueDate().toString());
            ps.setBoolean(6, loan.isReturned());
            ps.setDouble(7, loan.getFine());
            boolean inserted = ps.executeUpdate() == 1;
            if (inserted) {
                bookDAO.updateAvailability(book.getId(), book.getAvailableQuantity() - 1);
            }
            return inserted;
        }
    }

    public boolean returnLoan(int loanId, LocalDate returnDate, double fine) throws SQLException {
        Loan loan = findLoanById(loanId);
        if (loan == null || loan.isReturned()) {
            return false;
        }

        String sql = "UPDATE loans SET return_date = ?, returned = TRUE, fine = ? WHERE loan_id = ?";
        try (Connection con = DBConnection.getConnection(); PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setString(1, returnDate.toString());
            ps.setDouble(2, fine);
            ps.setInt(3, loanId);
            boolean updated = ps.executeUpdate() == 1;
            if (updated) {
                Book book = bookDAO.findBookById(loan.getBookId());
                if (book != null) {
                    bookDAO.updateAvailability(book.getId(), book.getAvailableQuantity() + 1);
                }
            }
            return updated;
        }
    }

    public Loan findLoanById(int loanId) throws SQLException {
        String sql = "SELECT loan_id, book_id, registration_no, student_name, issue_date, due_date, return_date, returned, fine FROM loans WHERE loan_id = ?";
        try (Connection con = DBConnection.getConnection(); PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setInt(1, loanId);
            try (ResultSet rs = ps.executeQuery()) {
                if (rs.next()) {
                    return mapLoan(rs);
                }
                return null;
            }
        }
    }

    public List<Loan> getAllLoans() throws SQLException {
        String sql = "SELECT loan_id, book_id, registration_no, student_name, issue_date, due_date, return_date, returned, fine FROM loans ORDER BY loan_id";
        List<Loan> loans = new ArrayList<>();
        try (Connection con = DBConnection.getConnection(); PreparedStatement ps = con.prepareStatement(sql); ResultSet rs = ps.executeQuery()) {
            while (rs.next()) {
                loans.add(mapLoan(rs));
            }
        }
        return loans;
    }

    public List<Loan> getActiveLoans() throws SQLException {
        String sql = "SELECT loan_id, book_id, registration_no, student_name, issue_date, due_date, return_date, returned, fine FROM loans WHERE returned = FALSE ORDER BY loan_id";
        List<Loan> loans = new ArrayList<>();
        try (Connection con = DBConnection.getConnection(); PreparedStatement ps = con.prepareStatement(sql); ResultSet rs = ps.executeQuery()) {
            while (rs.next()) {
                loans.add(mapLoan(rs));
            }
        }
        return loans;
    }

    public List<TransactionRecord> getLoanRecordsByBookId(String bookId) throws SQLException {
        String sql = "SELECT b.book_id, b.book_name, b.author, b.category, b.price, l.registration_no, l.student_name, l.issue_date, l.return_date, l.returned "
                + "FROM books b JOIN loans l ON b.book_id = l.book_id WHERE b.book_id = ? ORDER BY l.loan_id";
        return executeTransactionRecordQuery(sql, bookId);
    }

    public List<TransactionRecord> searchLoanRecordsByBookName(String bookName) throws SQLException {
        String sql = "SELECT b.book_id, b.book_name, b.author, b.category, b.price, l.registration_no, l.student_name, l.issue_date, l.return_date, l.returned "
                + "FROM books b JOIN loans l ON b.book_id = l.book_id WHERE b.book_name LIKE ? ORDER BY l.loan_id";
        return executeTransactionRecordQuery(sql, "%" + bookName + "%");
    }

    public List<TransactionRecord> searchLoanRecordsByCategory(String category) throws SQLException {
        String sql = "SELECT b.book_id, b.book_name, b.author, b.category, b.price, l.registration_no, l.student_name, l.issue_date, l.return_date, l.returned "
                + "FROM books b JOIN loans l ON b.book_id = l.book_id WHERE b.category LIKE ? ORDER BY l.loan_id";
        return executeTransactionRecordQuery(sql, "%" + category + "%");
    }

    public List<TransactionRecord> getLoanRecordsByReturnedStatus(boolean returned) throws SQLException {
        String sql = "SELECT b.book_id, b.book_name, b.author, b.category, b.price, l.registration_no, l.student_name, l.issue_date, l.return_date, l.returned "
                + "FROM books b JOIN loans l ON b.book_id = l.book_id WHERE l.returned = ? ORDER BY l.loan_id";
        List<TransactionRecord> records = new ArrayList<>();
        try (Connection con = DBConnection.getConnection(); PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setBoolean(1, returned);
            try (ResultSet rs = ps.executeQuery()) {
                while (rs.next()) {
                    records.add(mapTransactionRecord(rs));
                }
            }
        }
        return records;
    }

    public Loan findActiveLoanByBookId(String bookId) throws SQLException {
        String sql = "SELECT loan_id, book_id, registration_no, student_name, issue_date, due_date, return_date, returned, fine FROM loans WHERE book_id = ? AND returned = FALSE ORDER BY loan_id LIMIT 1";
        try (Connection con = DBConnection.getConnection(); PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setString(1, bookId);
            try (ResultSet rs = ps.executeQuery()) {
                return rs.next() ? mapLoan(rs) : null;
            }
        }
    }

    public Loan findActiveLoanByBookIdAndRegistrationNo(String bookId, String registrationNo) throws SQLException {
        String sql = "SELECT loan_id, book_id, registration_no, student_name, issue_date, due_date, return_date, returned, fine FROM loans WHERE book_id = ? AND registration_no = ? AND returned = FALSE ORDER BY loan_id LIMIT 1";
        try (Connection con = DBConnection.getConnection(); PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setString(1, bookId);
            ps.setString(2, registrationNo);
            try (ResultSet rs = ps.executeQuery()) {
                return rs.next() ? mapLoan(rs) : null;
            }
        }
    }

    public boolean returnLoanByBookIdAndRegistrationNo(String bookId, String registrationNo, java.time.LocalDate returnDate, double fine) throws SQLException {
        Loan loan = findActiveLoanByBookIdAndRegistrationNo(bookId, registrationNo);
        if (loan == null) {
            return false;
        }
        return returnLoan(loan.getLoanId(), returnDate, fine);
    }

    private List<TransactionRecord> executeTransactionRecordQuery(String sql, String parameter) throws SQLException {
        List<TransactionRecord> records = new ArrayList<>();
        try (Connection con = DBConnection.getConnection(); PreparedStatement ps = con.prepareStatement(sql)) {
            ps.setString(1, parameter);
            try (ResultSet rs = ps.executeQuery()) {
                while (rs.next()) {
                    records.add(mapTransactionRecord(rs));
                }
            }
        }
        return records;
    }

    private TransactionRecord mapTransactionRecord(ResultSet rs) throws SQLException {
        TransactionRecord record = new TransactionRecord();
        record.setBookId(rs.getString("book_id"));
        record.setBookName(rs.getString("book_name"));
        record.setAuthor(rs.getString("author"));
        record.setCategory(rs.getString("category"));
        record.setPrice(rs.getDouble("price"));
        record.setRegistrationNo(rs.getString("registration_no"));
        record.setStudentName(rs.getString("student_name"));
        java.sql.Date issueDate = rs.getDate("issue_date");
        record.setIssueDate(issueDate != null ? issueDate.toString() : null);
        java.sql.Date returnDate = rs.getDate("return_date");
        record.setReturnDate(returnDate != null ? returnDate.toString() : null);
        record.setReturned(rs.getBoolean("returned"));
        return record;
    }

    private Loan mapLoan(ResultSet rs) throws SQLException {
        Loan loan = new Loan();
        loan.setLoanId(rs.getInt("loan_id"));
        loan.setBookId(rs.getString("book_id"));
        loan.setRegistrationNo(rs.getString("registration_no"));
        loan.setStudentName(rs.getString("student_name"));
        loan.setIssueDate(LocalDate.parse(rs.getString("issue_date")));
        loan.setDueDate(LocalDate.parse(rs.getString("due_date")));
        String returnDate = rs.getString("return_date");
        if (returnDate != null) {
            loan.setReturnDate(LocalDate.parse(returnDate));
        }
        loan.setReturned(rs.getBoolean("returned"));
        loan.setFine(rs.getDouble("fine"));
        return loan;
    }
}
