package gui;

import dao.BookDAO;
import exception.DuplicateBookException;
import exception.InvalidInputException;
import java.awt.BorderLayout;
import java.awt.FlowLayout;
import java.sql.SQLException;
import java.util.List;
import javax.swing.JButton;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JScrollPane;
import javax.swing.JTable;
import javax.swing.JTextField;
import javax.swing.table.DefaultTableModel;
import model.Book;
import util.Validator;

public class BookManagerFrame extends JFrame {
    private final BookDAO bookDAO = new BookDAO();
    private final DefaultTableModel tableModel = new DefaultTableModel(new Object[]{"Book ID", "Title", "Author", "Category", "Price", "Total", "Available"}, 0);
    private final JTable table = new JTable(tableModel);
    private final JTextField bookIdField = new JTextField(10);
    private final JTextField titleField = new JTextField(15);
    private final JTextField authorField = new JTextField(15);
    private final JTextField categoryField = new JTextField(10);
    private final JTextField priceField = new JTextField(10);
    private final JTextField searchField = new JTextField(15);

    public BookManagerFrame() {
        setTitle("Manage Books");
        setSize(840, 420);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);

        JPanel formPanel = new JPanel(new FlowLayout(FlowLayout.LEFT));
        formPanel.add(new JLabel("Book ID"));
        formPanel.add(bookIdField);
        formPanel.add(new JLabel("Title"));
        formPanel.add(titleField);
        formPanel.add(new JLabel("Author"));
        formPanel.add(authorField);
        formPanel.add(new JLabel("Category"));
        formPanel.add(categoryField);
        formPanel.add(new JLabel("Price"));
        formPanel.add(priceField);
        JButton saveButton = new JButton("Add Book");
        saveButton.addActionListener(e -> saveBook());
        formPanel.add(saveButton);
        JButton backButton = new JButton("Back");
        backButton.addActionListener(e -> {
            new DashboardFrame().setVisible(true);
            dispose();
        });
        formPanel.add(backButton);

        JPanel searchPanel = new JPanel(new FlowLayout(FlowLayout.LEFT));
        searchPanel.add(new JLabel("Search"));
        searchPanel.add(searchField);
        JButton searchButton = new JButton("Search");
        searchButton.addActionListener(e -> searchBooks());
        searchPanel.add(searchButton);

        add(formPanel, BorderLayout.NORTH);
        add(searchPanel, BorderLayout.CENTER);
        add(new JScrollPane(table), BorderLayout.SOUTH);

        searchBooks();
    }

    private void saveBook() {
        String bookId = bookIdField.getText().trim();
        String title = titleField.getText().trim();
        String author = authorField.getText().trim();
        String category = categoryField.getText().trim();
        String priceText = priceField.getText().trim();

        try {
            Validator.requireNonEmpty(bookId, "Book ID");
            Validator.requireNonEmpty(title, "Title");
            Validator.requireNonEmpty(author, "Author");
            Validator.requireNonEmpty(category, "Category");
            double price = Validator.parsePositiveDouble(priceText, "Price");

            Book book = new Book(bookId, title, author, category, price, 1, 1);
            bookDAO.addBook(book);
            JOptionPane.showMessageDialog(this, "Book added successfully");
            clearFields();
            searchBooks();
        } catch (InvalidInputException ex) {
            JOptionPane.showMessageDialog(this, ex.getMessage());
        } catch (DuplicateBookException ex) {
            JOptionPane.showMessageDialog(this, ex.getMessage());
        } catch (SQLException ex) {
            JOptionPane.showMessageDialog(this, "Error adding book: " + ex.getMessage());
        }
    }

    private void searchBooks() {
        tableModel.setRowCount(0);
        try {
            String keyword = searchField.getText().trim();
            List<Book> books = keyword.isEmpty() ? bookDAO.getAllBooks() : bookDAO.searchBooks(keyword);
            for (Book book : books) {
                tableModel.addRow(new Object[]{book.getId(), book.getTitle(), book.getAuthor(), book.getCategory(), book.getPrice(), book.getQuantity(), book.getAvailableQuantity()});
            }
        } catch (SQLException ex) {
            JOptionPane.showMessageDialog(this, "Error loading books: " + ex.getMessage());
        }
    }

    private void clearFields() {
        bookIdField.setText("");
        titleField.setText("");
        authorField.setText("");
        categoryField.setText("");
        priceField.setText("");
    }
}
