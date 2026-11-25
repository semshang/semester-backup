package util;

import exception.InvalidInputException;
import java.time.LocalDate;
import java.time.format.DateTimeFormatter;
import java.time.format.DateTimeParseException;

public class DateUtil {
    public static final DateTimeFormatter FORMATTER = DateTimeFormatter.ofPattern("yyyy-MM-dd");

    public static LocalDate parse(String value) throws InvalidInputException {
        if (value == null || value.trim().isEmpty()) {
            throw new InvalidInputException("Date cannot be empty.");
        }
        try {
            return LocalDate.parse(value, FORMATTER);
        } catch (DateTimeParseException ex) {
            throw new InvalidInputException("Invalid date format. Use yyyy-MM-dd.", ex);
        }
    }

    public static String format(LocalDate date) {
        return date == null ? null : date.format(FORMATTER);
    }
}
