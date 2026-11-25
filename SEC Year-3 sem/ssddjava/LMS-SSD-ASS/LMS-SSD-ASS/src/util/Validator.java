package util;

import exception.InvalidInputException;
import java.util.regex.Pattern;

public class Validator {
    private static final Pattern EMAIL_PATTERN = Pattern.compile("^[A-Za-z0-9+_.-]+@[A-Za-z0-9.-]+$");
    private static final Pattern PHONE_PATTERN = Pattern.compile("^[0-9]{10,15}$");
    private static final Pattern ISBN_PATTERN = Pattern.compile("^[0-9]{10,13}$");

    public static void requireNonEmpty(String value, String fieldName) throws InvalidInputException {
        if (value == null || value.trim().isEmpty()) {
            throw new InvalidInputException(fieldName + " cannot be empty");
        }
    }

    public static double parsePositiveDouble(String value, String fieldName) throws InvalidInputException {
        requireNonEmpty(value, fieldName);
        try {
            double parsed = Double.parseDouble(value);
            if (parsed < 0) {
                throw new InvalidInputException(fieldName + " must be a positive number.");
            }
            return parsed;
        } catch (NumberFormatException ex) {
            throw new InvalidInputException(fieldName + " must be a valid number.", ex);
        }
    }

    public static void requireValidEmail(String email) throws InvalidInputException {
        if (email != null && !email.trim().isEmpty() && !isValidEmail(email)) {
            throw new InvalidInputException("Please enter a valid email address.");
        }
    }

    public static void requireValidPhone(String phone) throws InvalidInputException {
        requireNonEmpty(phone, "Mobile");
        if (!isValidPhone(phone)) {
            throw new InvalidInputException("Please enter a valid phone number.");
        }
    }

    public static boolean isValidEmail(String email) {
        return email != null && EMAIL_PATTERN.matcher(email).matches();
    }

    public static boolean isValidPhone(String phone) {
        return phone != null && PHONE_PATTERN.matcher(phone).matches();
    }

    public static boolean isValidIsbn(String isbn) {
        return isbn != null && ISBN_PATTERN.matcher(isbn).matches();
    }
}
