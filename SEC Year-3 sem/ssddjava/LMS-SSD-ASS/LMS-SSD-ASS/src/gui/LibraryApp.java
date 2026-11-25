package gui;

import java.sql.SQLException;
import javax.swing.JOptionPane;
import javax.swing.SwingUtilities;
import database.DBConnection;

public class LibraryApp {
    public static void main(String[] args) {
        try {
            System.out.println("[DEBUG] Starting LibraryApp...");
            System.out.println("[DEBUG] Initializing database...");
            
            // Initialize database on main thread first
            DBConnection.initializeDatabase();
            System.out.println("[DEBUG] Database initialized successfully");
            
            // Then show GUI on EDT
            SwingUtilities.invokeLater(() -> {
                try {
                    System.out.println("[DEBUG] Opening LoginFrame...");
                    LoginFrame frame = new LoginFrame();
                    frame.setVisible(true);
                    System.out.println("[DEBUG] LoginFrame displayed");
                } catch (Exception ex) {
                    System.err.println("[ERROR] Failed to open LoginFrame: " + ex.getMessage());
                    ex.printStackTrace();
                    JOptionPane.showMessageDialog(null, "Failed to open application:\n" + ex.getMessage(), "Error", JOptionPane.ERROR_MESSAGE);
                }
            });
        } catch (SQLException ex) {
            System.err.println("[ERROR] Database initialization failed: " + ex.getMessage());
            ex.printStackTrace();
            JOptionPane.showMessageDialog(null, "Database connection failed:\n" + ex.getMessage() + "\n\nMake sure MySQL is running!", "Database Error", JOptionPane.ERROR_MESSAGE);
            System.exit(1);
        } catch (Exception ex) {
            System.err.println("[ERROR] Unexpected error: " + ex.getMessage());
            ex.printStackTrace();
            JOptionPane.showMessageDialog(null, "Unexpected error:\n" + ex.getMessage(), "Error", JOptionPane.ERROR_MESSAGE);
            System.exit(1);
        }
    }
}
