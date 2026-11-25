package gui;

import dao.LoanDAO;
import exception.BookUnavailableException;
import exception.InvalidInputException;
import java.awt.FlowLayout;
import java.sql.SQLException;
import java.time.LocalDate;
import javax.swing.JButton;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JTextField;
import model.Loan;
import util.DateUtil;
import util.Validator;

public class IssueBookFrame extends JFrame {
    private final JTextField bookIdField = new JTextField(10);
    private final JTextField regField = new JTextField(10);
    private final JTextField nameField = new JTextField(15);
    private final JTextField issueDateField = new JTextField(10);
    private final LoanDAO loanDAO = new LoanDAO();

    public IssueBookFrame() {
        setTitle("Issue Book");
        setSize(500, 250);
        setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        setLocationRelativeTo(null);

        JPanel panel = new JPanel(new FlowLayout(FlowLayout.LEFT));
        panel.add(new JLabel("Book ID"));
        panel.add(bookIdField);
        panel.add(new JLabel("Reg No"));
        panel.add(regField);
        panel.add(new JLabel("Student Name"));
        panel.add(nameField);
        panel.add(new JLabel("Issue Date (yyyy-MM-dd)"));
        panel.add(issueDateField);

        JButton issueButton = new JButton("Issue");
        issueButton.addActionListener(e -> issueBook());
        panel.add(issueButton);

        JButton backButton = new JButton("Back");
        backButton.addActionListener(e -> {
            new DashboardFrame().setVisible(true);
            dispose();
        });
        panel.add(backButton);

        setContentPane(panel);
    }

    private void issueBook() {
        String bookId = bookIdField.getText().trim();
        String regNo = regField.getText().trim();
        String name = nameField.getText().trim();
        String issueDate = issueDateField.getText().trim();

        try {
            Validator.requireNonEmpty(bookId, "Book ID");
            Validator.requireNonEmpty(regNo, "Registration No");
            Validator.requireNonEmpty(name, "Student Name");
            Validator.requireNonEmpty(issueDate, "Issue Date");
            LocalDate issue = DateUtil.parse(issueDate);
            LocalDate dueDate = issue.plusDays(14);

            Loan loan = new Loan();
            loan.setBookId(bookId);
            loan.setRegistrationNo(regNo);
            loan.setStudentName(name);
            loan.setIssueDate(issue);
            loan.setDueDate(dueDate);
            loan.setReturned(false);
            loan.setFine(0);

            loanDAO.issueLoan(loan);
            JOptionPane.showMessageDialog(this, "Book issued successfully. Due date: " + dueDate);
            bookIdField.setText("");
            regField.setText("");
            nameField.setText("");
            issueDateField.setText("");
        } catch (InvalidInputException ex) {
            JOptionPane.showMessageDialog(this, ex.getMessage());
        } catch (BookUnavailableException ex) {
            JOptionPane.showMessageDialog(this, ex.getMessage());
        } catch (SQLException ex) {
            JOptionPane.showMessageDialog(this, "Error issuing book: " + ex.getMessage());
        }
    }
}
